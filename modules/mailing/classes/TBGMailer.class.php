<?php

	/**
	 * Mailer class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mailing
	 */

	/**
	 * Mailer class
	 *
	 * @package thebuggenie
	 * @subpackage mailing
	 */
	class TBGMailer
	{
		const MAIL_TYPE_PHP = 1;
		const MAIL_TYPE_B2M = 2;

		protected $debug = false;

		protected $type = null;
		protected $server = '';
		protected $port = 25;
		protected $timeout = 30;

		protected $username = '';
		protected $password = '';
		protected $no_dash_f = false;
		protected $ehlo = false;

		public function __construct($type)
		{
			$this->type = $type;
		}

		public function setServer($server)
		{
			$this->server = $server;
		}
		
		public function setNoDashF($val = true)
		{
			$this->no_dash_f = (bool) $val;
		}

		public function getServer()
		{
			return $this->server;
		}

		public function setPort($port = 25)
		{
			$this->port = $port;
		}

		public function setTimeout($timeout)
		{
			$this->timeout = $timeout;
		}

		public function setUsername($username)
		{
			$this->username = $username;
		}

		public function setPassword($password)
		{
			$this->password = $password;
		}

		public function getType()
		{
			return $this->type;
		}

		public function setDebug($val)
		{
			$this->debug = $val;
		}

		public function setEhlo()
		{
			$this->ehlo = true;
		}

		public function setHelo()
		{
			$this->ehlo = false;
		}

		public function send(TBGMimemail $email)
		{
			try
			{
				TBGContext::getI18n();
			}
			catch (Exception $e)
			{
				TBGContext::reinitializeI18n(null);
			}
			
			if (!$email->hasRecipients())
			{
				throw new Exception(TBGContext::getI18n()->__('You need to add at least one recipient'));
			}

			if (!$email->hasSender())
			{
				throw new Exception(TBGContext::getI18n()->__('You need to add a sender name and address'));
			}

			try
			{
				if ($this->type == self::MAIL_TYPE_PHP)
				{
					$retval = $this->_mail($email);
				}
				else
				{
					$retval = $this->_mail2($email);
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
			return $retval;
		}

		protected function _mail(TBGMimemail $email)
		{
			$boundary = md5(date('U'));

			if (!$this->no_dash_f)
			{
				$retval = mb_send_mail($email->getRecipientAddressesAsString(), $email->getSubject(), $email->getBodyAsString(), $email->getHeadersAsString(false), '-f'.$email->getFromAddress());
			}
			else
			{
				$retval = mb_send_mail($email->getRecipientAddressesAsString(), $email->getSubject(), $email->getBodyAsString(), $email->getHeadersAsString(false));
			}
			if ($retval)
			{
				TBGLogging::log("Sending email to {$email->getRecipients()} accepted for delivery OK");
			}
			else
			{
				TBGLogging::log("Sending email to {$email->getRecipients()} not accepted for delivery", TBGLogging::LEVEL_NOTICE);
			}
			
			return $retval;
		}

		private function _read_buffer($fp, $where)
		{
			$rv = fgets($fp, 4096);
			if ($this->debug)
			{
				echo("$where: $rv<br>");
			}
			$ret = $rv;
			$status = stream_get_meta_data($fp);
			while ($status['unread_bytes']>0)
			{
				$rv = fgets($fp, 4096);
				if ($this->debug)
				{
					echo("$where: $rv<br>");
				}
				$ret.= $rv;

				$status = stream_get_meta_data($fp);

				if ($status['timed_out'] == true)
				{
					throw new Exception(TBGContext::getI18n()->__('Timed out during server conversation'));
				}
			}
			return $ret;
		}

		protected function _mail2(TBGMimemail $email)
		{
			if (TBGContext::isCLI())
			{
				$server = php_uname('n');
			}
			else
			{
				$server = $_SERVER['SERVER_NAME'];
			}

			/* Open a socket connection to the mail server SMTP port (25) and read the welcome message. */
			$fp = fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
			if (!$fp)
			{
				if ($this->debug)
				{
					echo("No server? $errno $errstr<br>");
				}
				throw new Exception(TBGContext::getI18n()->__('Could not open connection to server %server on port %port%', array('%server%' => $this->server, '%port%' => $this->port)));
			}
			$this->_read_buffer($fp, 'open');

			/* Standard "ehlo" message. */
			if ($this->ehlo)
			{
				fputs($fp, "ehlo {$server}\r\n");
				$this->_read_buffer($fp, 'ehlo');
			}
			else /* MS Exchange "helo" message. */
			{
				fputs($fp, "helo {$server}\r\n");
				$this->_read_buffer($fp, 'helo');
			}

			/* Auth login: (Note that Username and password must be Base64 encoded string.) */
			if ($this->username != '')
			{
				fputs($fp, "AUTH LOGIN\r\n");
				$rv = fgets($fp, 4096);
				if ($this->debug)
				{
					echo(base64_decode(mb_substr($rv,4)) . $this->username . ' ' . $rv . '<br>');
				}
				fputs($fp,base64_encode($this->username) . "\r\n");
				$rv = fgets($fp, 4096);
				if ($this->debug)
				{
					echo(base64_decode(mb_substr($rv,4)) . $this->password . ' ' . $rv . '<br>');
				}
				fputs($fp,base64_encode($this->password) . "\r\n");
				$rv = $this->_read_buffer($fp, 'user/pass');

				if (preg_match("/^500/i",$rv))
				{
					if ($this->debug)
					{
						echo 'Not ready to authenticate. ('.$rv.') Try changing server type';
					}
					throw new Exception(TBGContext::getI18n()->__('Not ready to authenticate. (%rv%) Try changing server type', array('%rv%' => $rv)));
				}

				if (!preg_match("/^235/i",$rv))
				{ /* OK Authenticated */
					if ($this->debug)
					{
						echo('Username / password not accepted on server<br>');
					}
					fclose($fp);
					throw new Exception(TBGContext::getI18n()->__('Username / password not accepted on server: %rv%', array('%rv%' => $rv)));
				}
			}

			// "mail from" message and read the return. Assume everything is OK.
			fputs($fp, "mail from: <{$email->getFromaddress()}>\r\n");
			$rv = $this->_read_buffer($fp, 'mail_from');

			// "rcpt to" message and read the return. Each name in the $to, $cc, and $bcc array requires one "rcpt to"
			foreach ($email->getRecipients() as $recipient)
			{
				fputs($fp, "rcpt to: <{$recipient['address']}>\r\n");
				$rv = $this->_read_buffer($fp, 'to');
			}

			if (preg_match("/^550/i",$rv))
			{
				if ($this->debug)
				{
					echo "You are not allowed to send emails through this server.";
				}
				throw new Exception(TBGContext::getI18n()->__("You are not allowed to send emails through this server. \nThe error was: %rv%", array('%rv%' => $rv)));
			}

			foreach ($email->getCC() as $cc)
			{
				fputs($fp, "rcpt to: <{$cc['address']}>\r\n");
				$this->_read_buffer($fp, 'cc');
			}
			foreach ($email->getBCC() as $bcc)
			{
				fputs($fp, "rcpt to: <{$bcc['address']}>\r\n");
				$this->_read_buffer($fp, 'bcc');
			}

			/* "data" message and the message body follows. */
			fputs($fp, "data\r\n");
			$this->_read_buffer($fp, 'data');

			/* Standard message parts. */
			fputs($fp, $email->getHeadersAsString());

			foreach ($email->getBody(true) as $body_line)
			{
				fputs($fp, $body_line);
			}

			/* "quit" message and done. */
			fputs($fp, ".\r\n");
			fputs($fp, "quit\r\n");
			$rv = $this->_read_buffer($fp, 'quit');
			fclose($fp);

			/* status 250 if Message accepted for delivery */
			if (preg_match("/^250/i",$rv))
			{
				return true;
			}
			else
			{
				if ($this->debug)
				{
					echo "Did not receive a confirmation message from the mail server.";
				}
				throw new Exception(TBGContext::getI18n()->__("Did not receive a confirmation message from the mail server.. \nHowever, we received: %rv%", array('%rv%' => $rv)));
			}

		}
	}
