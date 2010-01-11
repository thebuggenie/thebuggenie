<?php

	class BUGSmailnotification extends TBGModule 
	{

		const MAIL_TYPE_PHP = 1;
		const MAIL_TYPE_B2M = 2;
		
		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(TBGContext::getI18n()->__('Mail notification'));
			$this->setMenuTitle(TBGContext::getI18n()->__('Mail notification'));
			$this->setConfigTitle(TBGContext::getI18n()->__('Mail notification'));
			$this->setDescription(TBGContext::getI18n()->__('Enables email notification functionality'));
			$this->setConfigDescription(TBGContext::getI18n()->__('Set up email- and user notifications from this section'));
			$this->setHasAccountSettings();
			$this->setHasConfigSettings();
			$this->addAvailableListener('core', 'user_registration', 'listen_registerUser', TBGContext::getI18n()->__('Email when user registers'));
			$this->addAvailableListener('core', 'password_reset', 'listen_forgottenPassword', TBGContext::getI18n()->__('Email to reset password'));
			$this->addAvailableListener('core', 'viewissue_top', 'listen_issueTop', TBGContext::getI18n()->__('Email when user registers'));
			$this->addAvailableListener('core', 'login_middle', 'listen_loginMiddle', TBGContext::getI18n()->__('Email to reset password'));
			$this->addAvailableListener('core', 'password_reset', 'listen_passwordReset', TBGContext::getI18n()->__('Email when password is reset'));
			$this->addAvailableListener('core', 'TBGIssue::update', 'listen_issueUpdate', TBGContext::getI18n()->__('Email on issue update'));
			$this->addAvailableListener('core', 'TBGIssue::createNew', 'listen_issueCreate', TBGContext::getI18n()->__('Email on new issues'));
			$this->addAvailableListener('core', 'TBGComment::createNew', 'listen_TBGComment_createNew', TBGContext::getI18n()->__('Email when comments are posted'));

			// No, I didn't forget the parameters, but what else would you call
			// it when it's about retrieving a forgotten password?
			$this->addRoute('forgot', '/forgot', 'forgot');
			$this->addRoute('mailnotification_test_email', '/mailnotification/test', 'testEmail');
		}

		public function initialize()
		{
		}
		
		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			
			$module = parent::_install('mailnotification', 'BUGSmailnotification', '1.0', true, false, false, $scope);
								  
			$module->enableListenerSaved('core', 'user_registration', $scope);
			$module->enableListenerSaved('core', 'login_middle', $scope);
			$module->enableListenerSaved('core', 'password_reset', $scope);
			$module->enableListenerSaved('core', 'TBGIssue::update', $scope);
			$module->enableListenerSaved('core', 'TBGIssue::createNew', $scope);
			$module->enableListenerSaved('core', 'TBGComment::createNew', $scope);
			$module->saveSetting('smtp_host', '');
			$module->saveSetting('smtp_port', 25);
			$module->saveSetting('smtp_user', '');
			$module->saveSetting('smtp_pwd', '');
			$module->saveSetting('headcharset', 'utf-8');
			$module->saveSetting('from_name', 'The Bug Genie Automailer');
			$module->saveSetting('from_addr', '');
			$module->saveSetting('ehlo', 1);

			return true;
		}
		
		public function uninstall()
		{
			$this->_uninstall();
		}

		public function postConfigSettings()
		{
			$settings = array('smtp_host', 'smtp_port', 'smtp_user', 'timeout', 'mail_type',
								'smtp_pwd', 'headcharset', 'from_name', 'from_addr', 'ehlo',
								'returnfromlogin', 'returnfromlogout', 'showloginbox', 'limit_registration',
								'showprojectsoverview', 'showprojectsoverview', 'cleancomments');
			foreach ($settings as $setting)
			{
				if (TBGContext::getRequest()->getParameter($setting) !== null)
				{
					$this->saveSetting($setting, TBGContext::getRequest()->getParameter($setting));
				}
			}
		}

		public function getEmailFromAddress()
		{
			return $this->getSetting('from_addr');
		}
					
		public function getEmailFromName()
		{
			return $this->getSetting('from_name');
		}

		public function listen_accountSettingsList()
		{
			include_template('mailnotification/accountsettingslist');
		}
		
		public function listen_registerUser($vars)
		{
			$user = array_shift($vars);
			$password = array_shift($vars);
			$subject = TBGContext::getI18n()->__('User account registered with The Bug Genie');
			$html_message = TBGAction::returnTemplateHTML('mailnotification/registeruser.html', array('user' => $user, 'password' => $password));
			$plain_message = TBGAction::returnTemplateHTML('mailnotification/registeruser.text', array('user' => $user, 'password' => $password));
	
			try
			{
				$this->sendMail($user->getBuddyname(), $user->getEmail(), $subject, $html_message, $plain_message);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function listen_loginMiddle()
		{
			TBGActionComponent::includeComponent('mailnotification/forgotPasswordBlock');
		}
		
		public function listen_passwordReset($vars)
		{
			$to_users = array(array('id' => $vars[0]->getID()));
			$new_pwd = $vars[1];
			$subject = TBGContext::getI18n()->__('Password reset');
			$message = TBGAction::returnTemplateHTML('mailnotification/passwordreset', array('password' => $new_pwd));
			$this->_sendToUsers($to_users, $subject, $message);
		}
		
		public function sendforgottenPasswordEmail($user)
		{
			$to_users = array($user);
			$subject = TBGContext::getI18n()->__('Forgot your password?');
			$html_message = TBGAction::returnTemplateHTML('mailnotification/forgottenpassword.html');
			$plain_message = TBGAction::returnTemplateHTML('mailnotification/forgottenpassword.text');
			$this->_sendToUsers($to_users, $subject, $html_message, $plain_message);
		}
		
		public function sendTestEmail($email_address)
		{
			$subject = TBGContext::getI18n()->__('Test email');
			$html_message = TBGAction::returnTemplateHTML('mailnotification/testemail.html');
			$plain_message = TBGAction::returnTemplateHTML('mailnotification/testemail.text');
			return $this->sendMail($email_address, $email_address, $subject, $html_message, $plain_message);
		}

		public function listen_issueCreate(TBGIssue $issue)
		{
			if ($issue instanceof TBGIssue)
			{
				$to_users = $issue->getRelatedUsers();
				$subject = TBGContext::getI18n()->__('New issue reported: %issue_no% - %issue_title%', array('%issue_no%' => $issue->getFormattedIssueNo(false), '%issue_title%' => $issue->getTitle()));
				$html_message = TBGAction::returnTemplateHTML('mailnotification/issuecreate.html', array('issue' => $issue));
				$plain_message = TBGAction::returnTemplateHTML('mailnotification/issuecreate.text', array('issue' => $issue));
				$this->_sendToUsers($to_users, $subject, $html_message, $plain_message);
			}
		}
		
		protected function _mustNotifyUserForIssue($issue_id, $user_id)
		{
			$dont_want_forced_notifications = $this->getSetting('hold_email_on_issue_update', $user_id);
			if (!$dont_want_forced_notifications)
			{
				return true;
			}
			else
			{
				return !(bool) $this->getSetting('notified_issue_'.$issue_id, $user_id);
			}
		}
		
		protected function _sendToUsers($to_users, $subject, $orig_message_html, $orig_message_plain = null)
		{
			foreach ($to_users as $user)
			{
				if ($user->getID() != TBGContext::getUser()->getUID())
				{
					if ($user->isEnabled() && $user->isActivated() && !$user->isDeleted() && !$user->isGuest())
					{
						$patterns = array('%user_buddyname%', '%user_username%');
						$replacements = array($user->getBuddyname(), $user->getUsername());
						$html_message = str_replace($patterns, $replacements, $orig_message_html);
						if ($orig_message_plain !== null)
						{
							$text_message = str_replace($patterns, $replacements, $orig_message_plain);
						}
						else
						{
							$text_message = null;
						}
						
						try
						{
							$this->sendMail($user->getBuddyname(), $user->getEmail(), $subject, $html_message, $text_message);
						}
						catch (Exception $e) 
						{
							$this->log('There was an error when trying to send email to ' . $to . ":\n" . $e->getMessage(), TBGLogging::LEVEL_NOTICE);
						}
					}
				}
			}
		}
		
		public function listen_TBGComment_createNew($comment)
		{
			if ($comment instanceof TBGComment && $comment->getTargetType() == 1)
			{
				try
				{
					$theIssue = TBGFactory::TBGIssueLab($comment->getTargetID());
					$title = $comment->getTitle();
					$content = $comment->getContent();
					$this->listen_issueUpdate(array($theIssue, $title, $content, null, null));
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
		}
		
		public function listen_issueTop($theIssue)
		{
			TBGSettings::deleteSetting('notified_issue_'.$theIssue->getId(), 'mailnotification', '', 0, TBGContext::getUser()->getId());
		}
		
		public function listen_issueUpdate($vars)
		{
			$theIssue = array_shift($vars);
			$title = array_shift($vars);
			$content = array_shift($vars);
			$uid = array_shift($vars);
			$system = array_shift($vars);
			$to_users = array();
	
			if ($theIssue instanceof TBGIssue)
			{
				$to_users = $theIssue->getRelatedUIDs();
				$cc = 0;
				foreach ($to_users as &$a_user)
				{
					if (is_array($a_user) && isset($a_user['id'])) $a_user = $a_user['id'];
					if ($this->_mustNotifyUserForIssue($theIssue->getID(), $a_user))
					{
						if ($this->getSetting('hold_email_on_issue_update', $a_user) == 1)
						{
							$this->saveSetting('notified_issue_'.$theIssue->getID(), 1, $a_user);
						}
					}
					else
					{
						unset($to_users[$cc]);
					}
					$cc++;
				}
				
				$subject = TBGContext::getI18n()->__('Issue ' . $theIssue->getFormattedIssueNo(false) . ' - ' . $theIssue->getTitle() . ' updated');
				$message = 'Hi, %user_buddyname%!<br>You are receiving this update because you are subscribing for updates.<br>This email is an update for issue ' . $theIssue->getFormattedIssueNo(false) . ' - ' . $theIssue->getTitle();
				$message .= '<br><br><b>' . $title . '</b>';
				$message .= '<br>' . bugs_BBDecode($content);
				$message .= '<br><br>You can open the issue by clicking the following link:<br><a href="%link_to_issue%' . $theIssue->getFormattedIssueNo(true) . '">%link_to_issue%' . $theIssue->getFormattedIssueNo(true) . '</a>';				
	
				$message = "<div style=\"font-family: \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-size: 11px; color: #646464;\">".$message."</div>";
				$this->_sendToUsers($to_users, $subject, $message);
			}
		}
		
		public function sendMail($to_name, $to_email, $subject, $message_html, $message_plain = null, $cc = '', $bcc = '', $attachments = array(), $debug = false)
		{

			$pre_html_message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html>';
			$pre_html_message .= '<head><meta http-equiv=Content-Type content="text/html; charset=utf-8"><title>The Bug Genie automailer</title></head><body>';
			$post_html_message = '</body></html>';
			$message_html = str_replace('%thebuggenie_url%', TBGContext::getRouting()->generate('home', array(), false), $message_html);
			$message_plain = str_replace('%thebuggenie_url%', TBGContext::getRouting()->generate('home', array(), false), $message_plain);

			$from_name = $this->getEmailFromName();
			$from_email = $this->getEmailFromAddress();
			if (!$from_name && $from_email)
			{
				return false;
			}
			if ($this->getSetting('mail_type') != self::MAIL_TYPE_B2M)
			{
				$boundary = md5(date('U'));

				$to = "$to_email";
				$headers = "From: {$from_name} <{$from_email}>\r\n";
				$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: multipart/alternative; boundary={$boundary}\r\n";
				$headers .= "Content-Transfer-Encoding: 7bit\r\n";

				if ($message_plain === null)
				{
					$message_plain = strip_tags(str_replace(array('<br>', '<br />'), array("\n", "\n"), $message_plain));
				}

				$message = "Multipart Message coming up\r\n\r\n";
				$message .= "--{$boundary}\r\n";
				$message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
				$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
				$message .= $message_plain . "\r\n\r\n";
				$message .= "--{$boundary}\r\n";
				$message .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
				$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
				$message .= $pre_html_message . $message_html . $post_html_message . "\r\n";
				$message .= "--{$boundary}--";

				$retval = mail($to, $subject, $message, $headers, '-f'.$from_email);
				if ($retval)
				{
					$this->log("Sending email to {$to} accepted for delivery OK");
				}
				else
				{
					$this->log("Sending email to {$to} not accepted for delivery", TBGLogging::LEVEL_NOTICE);
				}

			}
			else
			{
				$smtp_host = $this->getSetting('smtp_host');
				$smtp_port = $this->getSetting('smtp_port');
				$smtp_user = $this->getSetting('smtp_user');
				$smtp_pwd = $this->getSetting('smtp_pwd');

				$headcharset = $this->getSetting('headcharset');

				$name = $this->getSetting('from_name');
				$addr = $this->getSetting('from_addr');

				if ($smtp_host == '' || $smtp_port == '' || $name == '' || $addr == '')
				{
					$e_msg = 'Please configure the mail notification module before trying to send an email';
					if ($smtp_host == '') $e_msg .= "\nMissing SMTP hostname";
					if ($smtp_port == '') $e_msg .= "\nMissing SMTP port (usually 25)";
					if ($name == '') $e_msg .= "\nMissing email \"From\"-name";
					if ($addr == '') $e_msg .= "\nMissing email \"From\"-address";
					throw new Exception($e_msg);
				}

				$buf = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html>';
				$buf .= '<head><meta http-equiv=Content-Type content="text/html; charset='.$headcharset.'"><title>BUGS 2 Automailer</title></head><body>';

				if ($options & 1)
				{
					$buf .= '<table cellpadding=0 cellspacing=0 width="100%" style="background-color: #381499; color: #FFF;table-layout: fixed;">';
					$buf .= '<tr>';
					$buf .= '<td style="width: 70px; height: 65px;" align="center" valign="middle">';
					$buf .= '<img src="' . TBGSettings::get('url_host') . TBGSettings::get('url_subdir') . 'themes/' . TBGSettings::getThemeName() . '/logo_48.png">';
					$buf .= '</td>';
					$buf .= '<td align="left" valign="middle" style="width: 300px;"><div style="font: 20px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-weight: bold; color: #FFF;">' . TBGSettings::get('b2_name') . '</div><div style="font: 11px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; color: #FFF;">' . TBGSettings::get('b2_tagline') . '</div></td>';
					$buf .= '<td style="width: auto;">&nbsp;</td>';
					$buf .= '</tr>';
					$buf .= '</table><br>';
				}

				$buf .= $message;

				if ($options & 2)
				{
					$buf .= '<br><table cellpadding=0 cellspacing=0 style="table-layout: auto; background-color: #FFF; width: 100%; color: #999; border-top: 1px solid #DDD;" align="center">';
					$buf .= '<tr>';
					$buf .= '<td style="width: 30px; text-align: right; padding: 5px;">';
					$buf .= '<img src="' . TBGSettings::get('url_host') . TBGSettings::get('url_subdir') . 'themes/' . TBGSettings::getThemeName() . '/footer_logo.png">';
					$bug .= '</td>';
					$buf .= '<td style="width: auto;"><div style="padding: 8px 0px 0px 0px; font: 11px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif;"><a style="color: #00A400; text-decoration: none;" href="http://www.thebuggenie.net/" target="_blank">BUGS - The Bug Genie</a>, Copyright 2002 &copy; 09 <a style="color: #00A400; text-decoration: none;" href="http://www.zegeniestudios.net" target="_blank">zegenie Studios</a></div><div style="padding: 0px 0px 0px 0px; font: 10px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif;">Released under the MPL 1.1 only. Read the license at <a style="color: #00A400; text-decoration: none;" href="http://www.opensource.org/licenses/mozilla1.1.php" target="_blank">www.opensource.org</a>. Resistance is futile.</div>';
					$buf .= '</td>';
					$buf .= '</tr>';
					$buf .= '</table>';
				}

				$buf .= '</body></html>';

				$br_codes = array("<br>","<br>","<br>","<br>");
				$buf = str_replace($br_codes,"<br>\r\n",$buf);

				$mail = new BUGSmimemail($smtp_host, $smtp_port, $smtp_user, $smtp_pwd); // $name, $addr, $to_name, $to_addr, $subject, $buf);
				$mail->setDebug($debug);

				$mail->setFrom($name, $addr);
				$mail->addTo($to_name, $to_addr);
				$mail->setSubject($subject);
				$mail->setMessage($buf);

				if ($cc != '')
				{
					$mail->addCC($cc);
				}

				if ($bcc != '')
				{
					$mail->addBCC($bcc);
				}

				if (count($attachments) > 0)
				{
					foreach ($attachments as $attachment)
					{
						$mail->addAttachment($attachment['type'], $attachment['filename']);
					}
				}

				$mail->setCharset($headcharset);
				try
				{
					$retval = $mail->sendMail();
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return $retval;
		}
		
	}

?>