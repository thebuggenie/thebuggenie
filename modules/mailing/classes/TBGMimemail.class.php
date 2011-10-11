<?php

	/*
	 *  from original coding by Ninebirds (unknown origin)
	 */

	class TBGMimemail
	{
		protected $charset = "utf-8";
		protected $default_message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><title>The Bug Genie email</title><body>Empty message...</body></html>';

		protected $from = array();
		protected $to = array();
		protected $cc = array();
		protected $bcc = array();

		protected $replacements = array();

		protected $sep1 = null;
		protected $sep2 = null;

		protected $headers = array();

		protected $subject = '';
		protected $subject_translated = null;
		protected $template = null;
		protected $template_parameters = array();
		protected $language = null;
		protected $message_html = null;
		protected $message_html_decoration_before = null;
		protected $message_html_decoration_before_replaced = null;
		protected $message_html_decoration_after = null;
		protected $message_html_decoration_after_replaced = null;
		protected $message_html_replaced = null;
		protected $message_plain = null;
		protected $message_plain_replaced = null;
		protected $attachments = array();
		
		public static function createNewFromTemplate($subject, $template, $parameters = array(), $language = null, $recipients = array(), $charset = 'utf-8')
		{
			try
			{
				return new self($subject, $template, $parameters, $language, null, null, $recipients, $charset);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public static function createNewFromMessage($subject, $message_plain, $message_html = null, $recipients = array(), $charset = 'utf-8')
		{
			return new self($subject, null, null, null, $message_plain, $message_html, $recipients, $charset);
		}

		protected function __construct($subject, $template, $parameters = array(), $language = null, $message_plain = null, $message_html = null, $recipients = array(), $charset = 'utf-8')
		{
			/* Prepare two separators. $sep1 for the html/text message part. $sep2 for the attachment part. */
			for ($len = 10, $sep1 = ""; mb_strlen($sep1) < $len; $sep1 .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122))));
			for ($len = 10, $sep2 = ""; mb_strlen($sep2) < $len; $sep2 .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122))));
			$this->sep1 = "_1_" . bin2hex($sep1);
			$this->sep2 = "_2_" . bin2hex($sep2);
			
			$this->subject = $subject;
			if ($template !== null)
			{
				$this->template = $template;
				$this->template_parameters = $parameters;
				if ($language !== null)
				{
					$this->language = $language;
				}
			}
			elseif ($message_plain !== null)
			{
				$this->message_plain = $message_plain;
				$this->message_plain_replaced = $message_plain;
				if ($this->message_html !== null)
				{
					$this->message_html = $message_plain;
					$this->message_html_replaced = $message_plain;
				}
			}

			$recipients = (array) $recipients;
			foreach ($recipients as $recipient)
			{
				if (is_array($recipient))
				{
					if (array_key_exists('name', $recipient))
					{
						$this->addTo($recipient['name'], $recipient['address']);
					}
					elseif (count($recipient) == 2)
					{
						$this->addTo($recipient[1], $recipient[0]);
					}
					else
					{
						$this->addTo($recipient[0]);
					}
				}
				else
				{
					$this->addTo($recipient);
				}
			}
			$this->charset = $charset;
			$this->headers['X-Mailer'] = "PHP/" . phpversion();
			$this->headers['Subject'] = $subject;
			$this->headers['Date'] = date('r');
			$this->headers['MIME-Version'] = "1.0";
			$this->headers['Message-ID'] = "<{$this->sep1}@{$_SERVER['SERVER_NAME']}>";
		}

		public function setLanguage($language)
		{
			$this->language = $language;
			if ($this->template !== null)
			{
				$this->message_html = null;
				$this->message_html_replaced = null;
				$this->message_plain = null;
				$this->message_plain_replaced = null;
			}
		}

		public function addHeader($header, $value)
		{
			$this->headers[$header] = $value;
		}

		public function getHeader($header)
		{
			return (array_key_exists($header, $this->headers)) ? $this->headers[$header] : null;
		}

		public function getHeaders()
		{
			if (count($this->attachments) > 0)
			{
				$this->headers['Content-Type'] = "multipart/mixed;\r\n\tboundary=\"----=_AttaPart_{$this->sep2}\"\r\n\r\nThis is a multi-part message in MIME format.\r\n\r\n------=_AttaPart_{$this->sep2}\r\n";
			}
			elseif ($this->isMultipart())
			{
				$this->headers['Content-Type'] = "multipart/alternative;\r\n\tboundary=\"----=_MessPart_{$this->sep1}\"\r\n\r\nThis is a multi-part message in MIME format.\r\n\r\n";
			}
			elseif ($this->getMessageHTML(false))
			{
				$this->headers['Content-Type'] = "text/html; charset=\"{$this->charset}\"";
			}
			else
			{
				$this->headers['Content-Type'] = "text/plain; charset=\"{$this->charset}\"";
			}
			return $this->headers;
		}

		public function getHeadersAsString($include_subject = true)
		{
			$headers = $this->getHeaders();
			$header = '';
			foreach ($headers as $key => $val)
			{
				if (!$include_subject && mb_strtolower($key) == 'subject') continue;
				$header .= "{$key}: {$val}\r\n";
			}
			return $header;
		}

		public function setSubject($subject)
		{
			$this->subject = $subject;
		}

		protected function _translateSubject()
		{
			if ($this->language !== null)
			{
				try
				{
					$current_language = TBGContext::getI18n()->getCurrentLanguage();
					TBGContext::getI18n()->setLanguage($this->language);
					$this->subject_translated = TBGContext::getI18n()->__($this->subject);
					TBGContext::getI18n()->setLanguage($current_language);
				}
				catch (Exception $e)
				{
					TBGContext::getI18n()->setLanguage($current_language);
					throw $e;
				}
			}
			else
			{
				$this->subject_translated = $this->subject;
			}
		}

		public function getSubject()
		{
			$this->_translateSubject();
			return html_entity_decode($this->subject_translated);
		}
		
		public function setMessagePlain($message)
		{
			$this->message_plain = $message;
			$this->message_plain_replaced = null;
		}

		protected function _replaceMessageValues()
		{
			if ($this->message_plain_replaced === null)
			{
				if (count($this->replacements))
				{
					$this->message_plain_replaced = str_replace(array_keys($this->replacements), array_values($this->replacements), $this->message_plain);
				}
				else
				{
					$this->message_plain_replaced = $this->message_plain;
				}
			}
			if ($this->message_html_replaced === null)
			{
				if (count($this->replacements))
				{
					$this->message_html_replaced = str_replace(array_keys($this->replacements), array_values($this->replacements), $this->message_html);
				}
				else
				{
					$this->message_html_replaced = $this->message_html;
				}
			}
			if ($this->message_html_decoration_before_replaced === null)
			{
				if (count($this->replacements))
				{
					$this->message_html_decoration_before_replaced = str_replace(array_keys($this->replacements), array_values($this->replacements), $this->message_html_decoration_before);
					$this->message_html_decoration_after_replaced = str_replace(array_keys($this->replacements), array_values($this->replacements), $this->message_html_decoration_after);
				}
				else
				{
					$this->message_html_decoration_before_replaced = $this->message_html_decoration_before;
					$this->message_html_decoration_after_replaced = $this->message_html_decoration_after;
				}
			}
		}

		protected function _prepareMessages()
		{
			if ($this->template !== null)
			{
				if ($this->message_plain === null && $this->message_html === null)
				{
					if ($this->language !== null)
					{
						try
						{
							$current_language = TBGContext::getI18n()->getCurrentLanguage();
							TBGContext::getI18n()->setLanguage($this->language);
							$this->message_html = TBGAction::returnTemplateHTML("mailing/{$this->template}.html", $this->template_parameters);
							if ($this->message_html == '')
							{
								$this->message_html = null;
							}
							$this->message_plain = TBGAction::returnTemplateHTML("mailing/{$this->template}.text", $this->template_parameters);
							if ($this->message_plain == '')
							{
								$this->message_plain = null;
							}
							TBGContext::getI18n()->setLanguage($current_language);
						}
						catch (Exception $e)
						{
							TBGContext::getI18n()->setLanguage($current_language);
							throw $e;
						}
					}
					else
					{
						$this->message_html = TBGAction::returnTemplateHTML("mailing/{$this->template}.html", $this->template_parameters);
						$this->message_plain = TBGAction::returnTemplateHTML("mailing/{$this->template}.text", $this->template_parameters);
					}
				}
			}
		}

		public function getMessagePlain($replaced = true)
		{
			$this->_prepareMessages();
			if ($replaced)
			{
				$this->_replaceMessageValues();
			}
			return ($replaced) ? $this->message_plain_replaced : $this->message_plain;
		}

		public function setMessageHTML($message)
		{
			$this->message_html = $message;
			$this->message_html_replaced = null;
		}

		protected function _getMessageHTMLDecorated($replaced = true)
		{
			if ($this->message_html !== null)
			{
				if ($replaced)
				{
					return $this->message_html_decoration_before_replaced . $this->message_html_replaced . $this->message_html_decoration_after_replaced;
				}
				else
				{
					return $this->message_html_decoration_before . $this->message_html . $this->message_html_decoration_after;
				}
			}
			else
			{
				return null;
			}
		}

		public function getMessageHTML($replaced = true)
		{
			$this->_prepareMessages();
			if ($replaced)
			{
				$this->_replaceMessageValues();
			}
			return ($replaced) ? $this->_getMessageHTMLDecorated() : $this->_getMessageHTMLDecorated(false);
		}

		public function decorateMessageHTML($before, $after)
		{
			$this->message_html_decoration_before = $before;
			$this->message_html_decoration_before_replaced = null;
			$this->message_html_decoration_after = $after;
			$this->message_html_decoration_after_replaced = null;
		}

		public function clearReplacementValues()
		{
			$this->replacements = array();
			$this->message_plain_replaced = null;
			$this->message_html_replaced = null;
			$this->message_html_decoration_after_replaced = null;
			$this->message_html_decoration_before_replaced = null;
		}

		public function addReplacementValues($replacements)
		{
			foreach ($replacements as $pattern => $replacement)
			{
				$this->replacements[$pattern] = $replacement;
			}
			
			$this->message_plain_replaced = null;
			$this->message_html_replaced = null;
			$this->message_html_decoration_after_replaced = null;
			$this->message_html_decoration_before_replaced = null;
		}

		public function isMultipart()
		{
			return ($this->getMessagePlain(false) != '' && $this->getMessageHTML(false) != '');
		}

		public function getBody($base64_encoded = false)
		{
			$body = array();
			$multipart = $this->isMultipart();

			if ($this->getMessagePlain(false) != '')
			{
				if ($multipart)
				{
					$body[] = "------=_MessPart_{$this->sep1}\r\n";
					$body[] = "Content-Type: text/plain;\r\n";
					$body[] = "\tcharset=\"".$this->charset."\"\r\n";
					$body[] = "Content-Transfer-Encoding: " . (($base64_encoded) ? 'base64' : '7bit') . "\r\n";
					$body[] = "\r\n";
				}
				$body[] = ($base64_encoded) ? chunk_split(base64_encode($this->getMessagePlain())) : $this->getMessagePlain();
				$body[] = "\r\n";
				$body[] = "\r\n";
			}
			if ($this->getMessageHTML(false) != '')
			{
				if ($multipart)
				{
					$body[] = "------=_MessPart_{$this->sep1}\r\n";
					$body[] = "Content-Type: text/html;\r\n";
					$body[] = "\tcharset=\"".$this->charset."\"\r\n";
					$body[] = "Content-Transfer-Encoding: " . (($base64_encoded) ? 'base64' : '7bit') . "\r\n";
					$body[] = "\r\n";
				}
				$body[] = ($base64_encoded) ? chunk_split(base64_encode($this->getMessageHTML())) : $this->getMessageHTML();
				$body[] = "\r\n";
				$body[] = "\r\n";
				if ($multipart)
				{
					$body[] = "------=_MessPart_{$this->sep1}--\r\n";
					$body[] = "\r\n";
				}
			}
			if (count($this->attachments) > 0)
			{
				foreach ($this->attachments as $attachment)
				{
					$body[] = "------=_AttaPart_{$this->sep2}\r\n";
					$body[] = "Content-Type: ";
					if ($attachment['type'] == '')
					{
						$body[] = "application/octet-stream;\r\n";
					}
					else
					{
						$body[] = "{$attachment['type']};\r\n";
					}
					$body[] = "\tname=\"".basename($attachment['filename'])."\"\r\n";
					$body[] = "Content-Transfer-Encoding: " . (($base64_encoded) ? 'base64' : '7bit') . " \r\n";
					$body[] = "Content-Disposition: attachment;\r\n";
					$body[] = "\tfilename=\"".basename($attachment['filename'])."\"\r\n";
					$body[] = "\r\n";
					$body[] = ($base64_encoded) ? chunk_split(base64_encode($attachment['content'], $attachment['size'])) : $attachment['content'];
					$body[] = "\r\n";
				}
				$body[] = "------=_AttaPart_{$this->sep2}--\r\n";
				$body[] = "\r\n";
			}
			
			return $body;
		}

		public function getBodyAsString()
		{
			return join('', $this->getBody());
		}

		public function setFrom($address, $name = null)
		{
			if ($name === null) $name = $address;

			$this->headers['From'] = "{$name} <{$address}>";
			$this->headers['Return-Path'] = "<{$address}>";
			$this->from = array('name' => $name, 'address' => $address);
		}

		public function getFrom()
		{
			return $this->from;
		}

		public function getSender()
		{
			return $this->from;
		}

		public function getFromName()
		{
			return (array_key_exists('name', $this->from)) ? $this->from['name'] : $this->getFromAddress();
		}

		public function getSenderName()
		{
			return $this->getFromName();
		}

		public function hasFromAddress()
		{
			if (count($this->from) > 0 && array_key_exists('address', $this->from))
			{
				return true;
			}

			return false;
		}

		public function hasSender()
		{
			return $this->hasFromAddress();
		}

		public function getFromAddress()
		{
			if ($this->hasFromAddress())
			{
				return $this->from['address'];
			}
			
			return null;
		}

		public function getSenderAddress()
		{
			return $this->getFromAddress();
		}

		public function clearTo()
		{
			$this->to = array();
			unset($this->headers['To']);
		}

		public function clearRecipients()
		{
			$this->clearTo();
		}

		public function addTo($address, $name = null)
		{
			if ($name === null)
			{
				$name = $address;
			}
			$this->to[] = array('name' => $name, 'address' => $address);
			$to_header = array();
			foreach ($this->to as $recipient)
			{
				$to_header[] = "\"{$recipient['name']}\" <{$recipient['address']}>";
			}
			$this->addHeader('To', join(', ', $to_header));
		}

		public function getTo()
		{
			return $this->to;
		}

		public function getRecipients()
		{
			return $this->getTo();
		}

		public function getRecipientAddresses()
		{
			$recipients = array();
			foreach ($this->to as $recipient)
			{
				$recipients[] = $recipient['address'];
			}
			return $recipients;
		}

		public function getRecipientAddressesAsString()
		{
			return join(', ', $this->getRecipientAddresses());
		}

		public function getRecipientsAsString()
		{
			return $this->getHeader('To');
		}

		public function getNumberOfRecipients()
		{
			return count($this->to);
		}

		public function hasRecipients()
		{
			return (bool) count($this->to);
		}

		public function addCC($name, $address)
		{
			$this->cc[] = array('name' => $name, 'address' => $address);
			$cc_header = array();
			foreach ($this->cc as $recipient)
			{
				$cc_header[] = "\"{$recipient['name']}\" <{$recipient['address']}>";
			}
			$this->addHeader('Cc', join(', ', $cc_header));
		}

		public function getCC()
		{
			return $this->cc;
		}

		public function addBCC($name, $address)
		{
			$this->bcc[] = array('name' => $name, 'address' => $address);
			$bcc_header = array();
			foreach ($this->bcc as $recipient)
			{
				$bcc_header[] = "\"{$recipient['name']}\" <{$recipient['address']}>";
			}
			$this->addHeader('Bcc', join(', ', $bcc_header));
		}

		public function getBCC()
		{
			return $this->bcc;
		}
		
		public function addAttachment($type, $filename)
		{
			$attachment = array('type' => $type, 'filename' => $filename);
			$this->attachments[] = $attachment;
		}

		public function getAttachments()
		{
			return $this->attachments;
		}

		public function hasAttachments()
		{
			return (bool) count($this->attachments);
		}

		public function getNumberOfAttachments()
		{
			return count($this->attachments);
		}
		
		public function setCharset($charset)
		{
			$this->charset = $charset;
		}

		public function getCharset()
		{
			return $this->charset;
		}

	}