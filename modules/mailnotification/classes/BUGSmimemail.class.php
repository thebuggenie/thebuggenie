<?php

	/*
	 *  from original coding by Ninebirds (unknown origin)
	 */

	class BUGSmimemail
	{
		protected $debug = false;
		protected $charset = "iso-8859-1";
		protected $default_message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><title>BUGS - The Bug Genie mail</title><body>Empty...</body></html>';

		protected $server = '';
		protected $port = 25;
		protected $timeout = 30;
		
		protected $username = '';
		protected $userpass = '';
		protected $ehlo = false;
		
		protected $from = array();
		protected $to = array();
		protected $cc = array();
		protected $bcc = array();
		
		protected $subject = '';
		protected $message = '';
		protected $attachments = array();
		
		public function __construct($server, $port, $username = null, $userpass = null)
		{
			$this->server = $server;
			$this->port = $port;
			if ($timeout = BUGScontext::getModule('mailnotification')->getSetting('timeout'))
			{
				$this->timeout = $timeout;
			}
			
			if (BUGScontext::getModule('mailnotification')->getSetting('ehlo') == 1)
			{
				$this->ehlo = true;
			}
			else
			{
				$this->ehlo = false;
			}

			if ($username)
			{
				$this->username = $username;
			}
			
			if ($userpass)
			{
				$this->userpass = $userpass;
			}
		}

		public function setSubject($subject)
		{
			$this->subject = $subject;
		}
		
		public function setMessage($message)
		{
			$this->message = $message;
		}
		
		public function setDebug($val)
		{
			$this->debug = $val;
		}
		
		public function setFrom($name, $addr)
		{
			$from_arr = array('name' => $name, 'addr' => $addr);
			$this->from = $from_arr;
		}
		
		public function addTo($name, $addr)
		{
			$to_arr = array('name' => $name, 'addr' => $addr);
			$this->to[] = $to_arr;
		}

		public function addCC($name, $addr)
		{
			$cc_arr = array('name' => $name, 'addr' => $addr);
			$this->cc[] = $cc_arr;
		}

		public function addBCC($name, $addr)
		{
			$bcc_arr = array('name' => $name, 'addr' => $addr);
			$this->bcc[] = $bcc_arr;
		}
		
		public function addAttachment($type, $filename)
		{
			$attachment = array('type' => $type, 'filename' => $filename);
			$this->attachments[] = $attachment;
		}
		
		public function setEhlo()
		{
			$this->ehlo = true;
		}

		public function setHelo()
		{
			$this->ehlo = false;
		}
		
		public function setCharset($charset)
		{
			$this->charset = $charset;
		}
		
		public function sendMail()
		{
			if (count($this->to) == 0)
			{
				throw new Exception('You need to add at least one recipient');
			}
			
			if (count($this->from) == 0)
			{
				throw new Exception('You need to add a sender name and address');
			}
			
			try
			{
				$retval = $this->_mail();
			}
			catch (Exception $e)
			{
				throw $e;
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
					throw new Exception('Timed out during server conversation');
				}
			}
			flush();
			ob_flush();
			return $ret;
		}

		private function _mail()
		{
			/* Prepare two separators. $sep1 for the html/text message part. $sep2 for the attachment part. */
			for ($len = 10, $sep1 = ""; strlen($sep1) < $len; $sep1 .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122))));
			for ($len = 10, $sep2 = ""; strlen($sep2) < $len; $sep2 .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122))));
			$sep1 = "_1_" . bin2hex($sep1); // this format not used by spam tool ?
			$sep2 = "_2_" . bin2hex($sep2);

			/* Open a socket connection to the mail server SMTP port (25) and read the welcome message. */
			$fp = fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
			if (!$fp)
			{
				if ($this->debug)
				{
					echo("No server? $errno $errstr<br>");
				}
				throw new Exception('Could not open connection to server ' . $this->server . ' on port ' . $this->port);
			}
			$this->_read_buffer($fp, 'open');

			/* Standard "ehlo" message. */
			if ($this->ehlo) 
			{
				fputs($fp, "ehlo {$_SERVER['SERVER_NAME']}\r\n");
				$this->_read_buffer($fp, 'ehlo');
			}
			else /* MS Exchange "helo" message. */
			{
				fputs($fp, "helo {$_SERVER['SERVER_NAME']}\r\n");
				$this->_read_buffer($fp, 'helo');
			}

			/* Auth login: (Note that Username and Userpass must be Base64 encoded string.) */
			if ($this->username != '')
			{
				fputs($fp, "AUTH LOGIN\r\n");
				$rv = fgets($fp, 4096);
				if ($this->debug)
				{
					echo(base64_decode(substr($rv,4)) . $this->username . ' ' . $rv . '<br>');
				}
				fputs($fp,base64_encode($this->username) . "\r\n");
				$rv = fgets($fp, 4096);
				if ($this->debug)
				{
					echo(base64_decode(substr($rv,4)) . $this->userpass . ' ' . $rv . '<br>');
				}
				fputs($fp,base64_encode($this->userpass) . "\r\n");
				$rv = $this->_read_buffer($fp, 'user/pass');

				if (preg_match("/^500/i",$rv))
				{
					if ($this->debug)
					{
						echo 'Not ready to authenticate. ('.$rv.') Try changing server type';
					}
					throw new Exception('Not ready to authenticate. ('.$rv.') Try changing server type');
				}
				
				if (!preg_match("/^235/i",$rv))
				{ /* OK Authenticated */
					if ($this->debug)
					{
						echo('Username / password not accepted on server<br>');
					}
					fclose($fp);
					throw new Exception('Username / password not accepted on server: ' . $rv);
				}
			}

			/* "mail from" message and read the return. Assume everything is OK. */
			fputs($fp, "mail from: <{$this->from['addr']}>\r\n");
			$rv = $this->_read_buffer($fp, 'mail_from');

			/* "rcpt to" message and read the return. Each name in the $to, $cc, and $bcc array requires one "rcpt to". We will also
			   take this opportunity to prepare the To, Cc, and Bcc field of the message body. */
			$_to = '';
			foreach ($this->to as $to)
			{
				fputs($fp, "rcpt to: <{$to['addr']}>\r\n");
				$rv = $this->_read_buffer($fp, 'to');
				$_to .= "\"{$to['name']}\" <{$to['addr']}>, ";
			}

			if (preg_match("/^550/i",$rv))
			{
				if ($this->debug)
				{
					echo "You are not allowed to send emails through this server.";
				}
				throw new Exception("You are not allowed to send emails through this server. \nThe error was: ".$rv);
			}
			
			$_cc = '';
			foreach ($this->cc as $cc)
			{
				fputs($fp, "rcpt to: <{$cc['addr']}>\r\n");
				$this->_read_buffer($fp, 'cc');
				$_cc .= "\"{$cc['name']}\" <{$cc['addr']}>, ";
			}
			$_bcc = '';
			foreach ($this->bcc as $bcc)
			{
				fputs($fp, "rcpt to: <{$bcc['addr']}>\r\n");
				$this->_read_buffer($fp, 'bcc');
				$_bcc .= "\"{$bcc['name']}\" <{$bcc['addr']}>, ";
			}

			/* "data" message and the message body follows. */
			fputs($fp, "data\r\n");
			$this->_read_buffer($fp, 'data');

			/* Standard message parts. */
			fputs($fp, "Return-Path: <{$this->from['addr']}>\r\n");
			fputs($fp, "Message-ID: <$sep1@{$_SERVER['SERVER_NAME']}>\r\n");
			//fputs($fp, "From: =?".$this->charset."?Q?{$from['name']}?=\r\n <{$from['addr']}>\r\n");
			fputs($fp, "From: \"{$this->from['name']}\" <{$this->from['addr']}>\r\n");
			
			if ($_to != "")
			{
				fputs($fp, "To: ".rtrim($_to, ", ")."\r\n");
			}
			
			if ($_cc != "")
			{
				fputs($fp, "Cc: ".rtrim($_cc, ", ")."\r\n");
			}
			
			if ($_bcc != "")
			{
				fputs($fp, "Bcc: ".rtrim($_bcc, ", ")."\r\n");
			}
			
			fputs($fp, "Subject: $this->subject\r\n");
			$date = date("r");
			fputs($fp, "Date: $date\r\n");
			fputs($fp, "MIME-Version: 1.0\r\n");

			/* If we have attachments, then we need a Content-Type of "multipart/mixed". For the html/text message part, the
			   Content-Type will always be "multipart/alternative". */
			/* For simplicity, we will always encode the information for transfer by base64 encoding. */

			if (count($this->attachments) > 0)
			{
				fputs($fp, "Content-Type: multipart/mixed;\r\n");
				fputs($fp, "\tboundary=\"----=_AttaPart_$sep2\"\r\n");
				fputs($fp, "\r\n");
				fputs($fp, "This is a multi-part message in MIME format.\r\n");
				fputs($fp, "\r\n");
				fputs($fp, "------=_AttaPart_$sep2\r\n");
			}
			fputs($fp, "Content-Type: multipart/alternative;\r\n");
			fputs($fp, "\tboundary=\"----=_MessPart_$sep1\"\r\n");
			fputs($fp, "\r\n");
			if (count($this->attachments) == 0)
			{
				fputs($fp, "This is a multi-part message in MIME format.\r\n");
			}
			fputs($fp, "\r\n");
			fputs($fp, "------=_MessPart_$sep1\r\n");
			fputs($fp, "Content-Type: text/plain;\r\n");
			fputs($fp, "\tcharset=\"".$this->charset."\"\r\n");
			fputs($fp, "Content-Transfer-Encoding: base64\r\n");
			fputs($fp, "\r\n");
			fputs($fp, chunk_split(base64_encode(strip_tags($this->message))));
			fputs($fp, "\r\n");
			fputs($fp, "------=_MessPart_$sep1\r\n");
			fputs($fp, "Content-Type: text/html;\r\n");
			fputs($fp, "\tcharset=\"".$this->charset."\"\r\n");
			fputs($fp, "Content-Transfer-Encoding: base64\r\n");
			fputs($fp, "\r\n");
			fputs($fp, chunk_split(base64_encode($this->message)));
			fputs($fp, "\r\n");
			fputs($fp, "------=_MessPart_$sep1--\r\n");
			fputs($fp, "\r\n");
			if (count($this->attachments) > 0)
			{
				foreach ($this->attachments as $attachment)
				{
					fputs($fp, "------=_AttaPart_$sep2\r\n");
					fputs($fp, "Content-Type: ");
					if ($attachment['type'] == '')
					{
						fputs($fp,"application/octet-stream;\r\n");
					}
					else
					{
						fputs($fp,"{$attachment['type']};\r\n");
					}
					fputs($fp, "\tname=\"".basename($attachment['filename'])."\"\r\n");
					fputs($fp, "Content-Transfer-Encoding: base64 \r\n");
					fputs($fp, "Content-Disposition: attachment;\r\n");
					fputs($fp, "\tfilename=\"".basename($attachment['filename'])."\"\r\n");
					fputs($fp, "\r\n");
					fputs($fp, chunk_split(base64_encode(fread(fopen($attachment['filename'], "rb"), filesize($attachment['filename'])))));
					fputs($fp, "\r\n");
				}
				fputs($fp, "------=_AttaPart_$sep2--\r\n");
				fputs($fp, "\r\n");
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
				throw new Exception("Did not receive a confirmation message from the mail server.. \nHowever, we received: ".$rv);
			}
			
			
		}
	}

?>