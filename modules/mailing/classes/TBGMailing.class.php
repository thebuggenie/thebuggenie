<?php

	class TBGMailing extends TBGModule
	{

		protected $_module_version = '1.0';

		protected $mailer = null;

		/**
		 * Get an instance of this module
		 * 
		 * @return TBGMailing
		 */
		public static function getModule()
		{
			return TBGContext::getModule('mailing');
		}
		
		protected function _initialize(TBGI18n $i18n)
		{
			$this->setLongName($i18n->__('Email communication'));
			$this->setConfigTitle($i18n->__('Email communication'));
			$this->setDescription($i18n->__('Enables in- and outgoing email functionality'));
			$this->setConfigDescription($i18n->__('Set up in- and outgoing email communication from this section'));
			$this->setHasAccountSettings();
			$this->setAccountSettingsName($i18n->__('Notifications'));
			$this->setAccountSettingsLogo('notification_settings.png');
			$this->setHasConfigSettings();
		}

		protected function _addAvailableListeners()
		{
			$i18n = TBGContext::getI18n();
			$this->addAvailableListener('core', 'user_registration', 'listen_registerUser', $i18n->__('Email when user registers'));
			$this->addAvailableListener('core', 'password_reset', 'listen_forgottenPassword', $i18n->__('Email to reset password'));
			$this->addAvailableListener('core', 'viewissue_top', 'listen_issueTop', $i18n->__('Email when user registers'));
			$this->addAvailableListener('core', 'login_form_pane', 'listen_loginPane', $i18n->__('Email to reset password'));
			$this->addAvailableListener('core', 'login_form_tab', 'listen_loginTab', $i18n->__('Email to reset password'));
			$this->addAvailableListener('core', 'password_reset', 'listen_passwordReset', $i18n->__('Email when password is reset'));
			$this->addAvailableListener('core', 'TBGIssue::save', 'listen_issueSave', $i18n->__('Email when an issue is updated'));
			$this->addAvailableListener('core', 'TBGIssue::createNew', 'listen_issueCreate', $i18n->__('Email on new issues'));
			$this->addAvailableListener('core', 'TBGComment::createNew', 'listen_TBGComment_createNew', $i18n->__('Email when comments are posted'));
			$this->addAvailableListener('core', 'header_begins', 'listen_headerBegins', $i18n->__('Javascript Mailing'));
		}

		protected function _addAvailableRoutes()
		{
			$this->addRoute('forgot', '/forgot', 'forgot');
			$this->addRoute('reset', '/reset/:user/:reset_hash', 'resetPassword', array('continue' => true));
			$this->addRoute('mailing_test_email', '/mailing/test', 'testEmail');
		}
		
		protected function _install($scope)
		{
			$this->enableListenerSaved('core', 'user_registration', $scope);
			$this->enableListenerSaved('core', 'login_form_tab', $scope);
			$this->enableListenerSaved('core', 'login_form_pane', $scope);
			$this->enableListenerSaved('core', 'password_reset', $scope);
			$this->enableListenerSaved('core', 'TBGIssue::save', $scope);
			$this->enableListenerSaved('core', 'TBGIssue::createNew', $scope);
			$this->enableListenerSaved('core', 'TBGComment::createNew', $scope);
			$this->enableListenerSaved('core', 'header_begins', $scope);
			$this->saveSetting('smtp_host', '');
			$this->saveSetting('smtp_port', 25);
			$this->saveSetting('smtp_user', '');
			$this->saveSetting('smtp_pwd', '');
			$this->saveSetting('headcharset', TBGContext::getI18n()->getLangCharset());
			$this->saveSetting('from_name', 'The Bug Genie Automailer');
			$this->saveSetting('from_addr', '');
			$this->saveSetting('ehlo', 1);
		}
		
		protected function _uninstall()
		{
			parent::_uninstall();
		}

		public function postConfigSettings(TBGRequest $request)
		{
			TBGContext::loadLibrary('common');
			$settings = array('smtp_host', 'smtp_port', 'smtp_user', 'timeout', 'mail_type', 'enable_outgoing_notifications',
								'smtp_pwd', 'headcharset', 'from_name', 'from_addr', 'ehlo', 'use_queue', 'no_dash_f');
			foreach ($settings as $setting)
			{
				if ($request->getParameter($setting) !== null)
				{
					$value = $request->getParameter($setting);
					$dns_regex = '(\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b|(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\]))';
					$mail_regex = '(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@';
					switch($setting)
					{
						case 'smtp_host':
							if ($request->getParameter('mail_type') == TBGMailer::MAIL_TYPE_B2M && !tbg_check_syntax($value, "MAILSERVER"))
							{
								throw new Exception(TBGContext::getI18n()->__('Please provide a valid setting for SMTP server address'));
							}
							break;
						case 'from_addr':
							if (!tbg_check_syntax($value, "EMAIL"))
							{						
								throw new Exception(TBGContext::getI18n()->__('Please provide a valid setting for email "from"-address'));
							}
							break;
						case 'timeout':
							if ($request->getParameter('mail_type') == TBGMailer::MAIL_TYPE_B2M && !is_numeric($value) || $value < 0)
							{
								throw new Exception(TBGContext::getI18n()->__('Please provide a valid setting for SMTP server timeout'));
							}
							break;
						case 'smtp_port':
							if ($request->getParameter('mail_type') == TBGMailer::MAIL_TYPE_B2M && !is_numeric($value) || $value < 1)
							{
								throw new Exception(TBGContext::getI18n()->__('Please provide a valid setting for SMTP server port'));
							}							
							break;							
						case 'headcharset':
							// list of supported character sets based on PHP doc : http://www.php.net/manual/en/function.htmlentities.php
							if (!tbg_check_syntax($value, "CHARSET"))
							{
									throw new Exception(TBGContext::getI18n()->__('Please provide a valid setting for email header charset'));
							}							
							break;	
						case 'no_dash_f':
							$value = (int) $request->getParameter($setting, 0);
							break;
					}
					$this->saveSetting($setting, $value);
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

		public function listen_accountSettingsList(TBGEvent $event)
		{
			include_template('mailing/accountsettingslist');
		}
		
		public function listen_registerUser(TBGEvent $event)
		{
			$user = $event->getSubject();
			$password = $event->getParameter('password');
			if ($this->isOutgoingNotificationsEnabled())
			{
				$subject = TBGContext::getI18n()->__('User account registered with The Bug Genie');
				$message = $this->createNewTBGMimemailFromTemplate($subject, 'registeruser', array('user' => $user, 'password' => $password), null, array($user->getBuddyname(), $user->getEmail()));

				try
				{
					$this->sendMail($message);
					$event->setProcessed();
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
		}

		public function listen_loginPane(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				TBGActionComponent::includeComponent('mailing/forgotPasswordPane', $event->getParameters());
			}
		}

		public function listen_loginTab(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				TBGActionComponent::includeComponent('mailing/forgotPasswordTab', $event->getParameters());
			}
		}			
		
		public function listen_passwordReset(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$subject = TBGContext::getI18n()->__('Password reset');
				$message = $this->createNewTBGMimemailFromTemplate($subject, 'passwordreset', array('password' => $event->getParameter('password')));
				$this->_sendToUsers($event->getSubject(), $message);
			}
		}
		
		public function listen_headerBegins(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled() && TBGContext::getUser()->isGuest())
			{			
				TBGContext::getResponse()->addJavascript('forgot.js');
			}
		}
		
		public function sendforgottenPasswordEmail($user)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$subject = TBGContext::getI18n()->__('Forgot your password?');
				$message = $this->createNewTBGMimemailFromTemplate($subject, 'forgottenpassword', array('user' => $user));
				$this->_sendToUsers($user, $message);
			}
		}
		
		public function sendTestEmail($email_address)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				try
				{
					$subject = TBGContext::getI18n()->__('Test email');
					$message = $this->createNewTBGMimemailFromTemplate($subject, 'testemail', array(), null, array($email_address));
					return $this->sendMail($message);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				throw new Exception(TBGContext::getI18n()->__('The email module is not configured for outgoing emails'));
			}
		}

		public function listen_issueCreate(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$issue = $event->getSubject();
				if ($issue instanceof TBGIssue)
				{
					$to_users = $issue->getRelatedUsers();
					$subject = TBGContext::getI18n()->__('[%project_name%] %issue_type% %issue_no% - "%issue_title%" created', array('%project_name%' => $issue->getProject()->getKey(), '%issue_type%' => TBGContext::getI18n()->__($issue->getIssueType()->getName()), '%issue_no%' => $issue->getFormattedIssueNo(true), '%issue_title%' => $issue->getTitle()));
					$message = $this->createNewTBGMimemailFromTemplate($subject, 'issuecreate', array('issue' => $issue));
					$this->_sendToUsers($to_users, $message);
				}
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
		
		protected function _sendToUsers($to_users, TBGMimemail $message)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				if (!is_array($to_users))
				{
					$to_users = array($to_users);
				}
				foreach ($to_users as $user)
				{
					if ($user->getID() != TBGContext::getUser()->getID() || $this->getSetting('notify_issue_change_own', $user->getID()))
					{
						if ($user instanceof TBGUser && $user->isEnabled() && $user->isActivated() && !$user->isDeleted() && !$user->isGuest())
						{
							$message->setLanguage($user->getLanguage());
							$message->clearRecipients();
							$message->addReplacementValues(array('%user_buddyname%' => $user->getBuddyname(), '%user_username%' => $user->getUsername()));
							$message->addTo($user->getEmail(), $user->getBuddyname());

							try
							{
								$this->sendMail($message);
							}
							catch (Exception $e)
							{
								$this->log('There was an error when trying to send email to ' . join('/', $message->getRecipients()) . ":\n" . $e->getMessage(), TBGLogging::LEVEL_NOTICE);
							}
						}
					}
				}
			}
		}
		
		public function listen_TBGComment_createNew(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$comment = $event->getSubject();
				if ($comment instanceof TBGComment && $comment->getTargetType() == 1)
				{
					try
					{
						$issue = TBGContext::factory()->TBGIssue($comment->getTargetID());
						$title = $comment->getTitle();
						$content = $comment->getContent();
						$to_users = $issue->getRelatedUsers();
						$subject = TBGContext::getI18n()->__('[%project_name%] %issue_type% %issue_no% - "%issue_title%" updated', array('%project_name%' => $issue->getProject()->getKey(), '%issue_type%' => TBGContext::getI18n()->__($issue->getIssueType()->getName()), '%issue_no%' => $issue->getFormattedIssueNo(true), '%issue_title%' => $issue->getTitle()));
						$message = $this->createNewTBGMimemailFromTemplate($subject, 'issuecomment', array('issue' => $issue));
						$this->_sendToUsers($to_users, $message);
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
			}
		}
		
		public function listen_issueTop(TBGEvent $event)
		{
			$issue = $event->getSubject();
			TBGSettings::deleteSetting('notified_issue_'.$issue->getId(), 'mailing', '', 0, TBGContext::getUser()->getId());
		}
		
		public function listen_issueSave(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled() && $event->getParameter('notify'))
			{
				$issue = $event->getSubject();

				if ($issue instanceof TBGIssue)
				{
					$to_users = $issue->getRelatedUsers();
					foreach ($to_users as &$a_user)
					{
						if (is_array($a_user) && isset($a_user['id'])) $a_user = $a_user['id'];
						if ($this->_mustNotifyUserForIssue($issue->getID(), $a_user))
						{
							if ($this->getSetting('hold_email_on_issue_update', $a_user) == 1)
							{
								$this->saveSetting('notified_issue_'.$issue->getID(), 1, $a_user);
							}
						}
						else
						{
							unset($to_users[$cc]);
						}
					}

					$subject = TBGContext::getI18n()->__('[%project_name%] %issue_type% %issue_no% - "%issue_title%" updated', array('%project_name%' => $issue->getProject()->getKey(), '%issue_type%' => TBGContext::getI18n()->__($issue->getIssueType()->getName()), '%issue_no%' => $issue->getFormattedIssueNo(true), '%issue_title%' => $issue->getTitle()));
					$message = $this->createNewTBGMimemailFromTemplate($subject, 'issueupdate', array('issue' => $issue, 'comment_lines' => $event->getParameter('comment_lines'), 'updated_by' => $event->getParameter('updated_by')));
					$this->_sendToUsers($to_users, $message);
				}
			}
		}

		public function getMailerType()
		{
			return $this->getSetting('mail_type');
		}

		public function getSmtpHost()
		{
			return $this->getSetting('smtp_host');
		}

		public function getSmtpPort()
		{
			return $this->getSetting('smtp_port');
		}

		public function getSmtpUsername()
		{
			return $this->getSetting('smtp_user');
		}

		public function getSmtpPassword()
		{
			return $this->getSetting('smtp_pwd');
		}

		/**
		 * Retrieve the instantiated and configured mailer object
		 *
		 * @return TBGMailer
		 */
		public function getMailer()
		{
			if ($this->mailer === null)
			{
				$this->mailer = new TBGMailer($this->getMailerType());
				if ($this->mailer->getType() == TBGMailer::MAIL_TYPE_B2M)
				{
					$this->mailer->setServer($this->getSmtpHost());
					$this->mailer->setPort($this->getSmtpPort());
					$this->mailer->setUsername($this->getSmtpUsername());
					$this->mailer->setPassword($this->getSmtpPassword());
				}
				else
				{
					$this->mailer->setNoDashF((bool) $this->getSetting('no_dash_f'));
				}
			}

			return $this->mailer;
		}

		protected function _setInitialMailValues(TBGMimemail $mail)
		{
			$from_name = $this->getEmailFromName();
			$from_email = $this->getEmailFromAddress();
			if (!$from_email)
			{
				throw new Exception('The email module does not have a "from" address');
			}
			$mail->setFrom($this->getEmailFromAddress(), $this->getEmailFromName());
			$pre_html_message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><head><meta http-equiv=Content-Type content="text/html; charset=' . $mail->getCharset() . '"><title>' . TBGSettings::getTBGname() . '</title></head><body>';
			$post_html_message = '</body></html>';
			$mail->decorateMessageHTML($pre_html_message, $post_html_message);
			$mail->addReplacementValues(array('%thebuggenie_url%' => TBGContext::getRouting()->generate('home', array(), false)));
		}
		
		protected function _setAdditionalMailValues(TBGMimemail $mail, array $parameters)
		{
			$mail->addReplacementValues(array('%password%' => isset($parameters['password']) ? $parameters['password'] : ''));
			$mail->addReplacementValues(array('%link_to_reset_password%' => isset($parameters['user']) ? TBGContext::getRouting()->generate('reset', array('user' => $parameters['user']->getUsername(), 'reset_hash' => $parameters['user']->getHashPassword(), 'id' => $parameters['user']->getHashPassword()), false) : '' ));
			$mail->addReplacementValues(array('%link_to_activate%' => isset($parameters['user']) ? TBGContext::getRouting()->generate('activate', array('user' => $parameters['user']->getUsername(), 'key' => $parameters['user']->getHashPassword()), false) : ''));
		}

		/**
		 * Create a new TBGMimemail and return it
		 *
		 * @param string $subject
		 * @param string $template
		 * @param array $parameters
		 * @param string $language
		 * @param array $recipients
		 * @param string $charset
		 *
		 * @return TBGMimemail
		 */
		public function createNewTBGMimemailFromTemplate($subject, $template, $parameters = array(), $language = null, $recipients = array(), $charset = 'utf-8')
		{
			try
			{
				//if (file_exists(TBGContext::getRouting()->getCurrentRouteModule() . $module . DIRECTORY_SEPARATOR . $templatefile))
				//if($basepath . $module . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . $templatefile)
				$mail = TBGMimemail::createNewFromTemplate($subject, $template, $parameters, $language, $recipients, $charset);
				$this->_setInitialMailValues($mail);
				$this->_setAdditionalMailValues($mail, $parameters);

				return $mail;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		/**
		 * Create a new TBGMimemail and return it
		 *
		 * @param string $subject
		 * @param string $message_plain
		 * @param string $message_html
		 * @param array $recipients
		 * @param string $charset
		 *
		 * @return TBGMimemail
		 */
		public function createNewTBGMimemailFromMessage($subject, $message_plain, $message_html = null, $recipients = array(), $charset = 'utf-8')
		{
			try
			{
				$mail = TBGMimemail::createNewFromMessage($subject, $message_plain, $message_html, $recipients, $charset);
				$this->_setInitialMailValues($mail);

				return $mail;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function sendMail(TBGMimemail $mail, $debug = false)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				if ($this->usesEmailQueue())
				{
					TBGMailQueueTable::getTable()->addMailToQueue($mail);
					return true;
				}
				else
				{
					$mailer = $this->getMailer();
					$retval = $mailer->send($mail);
				}

				return $retval;
			}
		}

		public function postAccountSettings(TBGRequest $request)
		{
			$settings = array('notify_add_friend', 'notify_issue_change', 'notify_issue_change_own', 'notify_issue_comment');
			$uid = TBGContext::getUser()->getID();
			foreach ($settings as $setting)
			{
				$this->saveSetting($setting, (int) $request->getParameter($setting, 0), $uid);
			}
			return true;
		}

		public function isOutgoingNotificationsEnabled()
		{
			return (bool) $this->getSetting('enable_outgoing_notifications');
		}

		public function usesEmailQueue()
		{
			return (bool) $this->getSetting('use_queue');
		}

		public function setOutgoingNotificationsEnabled($enabled = true)
		{
			$this->saveSetting('enable_outgoing_notifications', $enabled);
		}

	}
