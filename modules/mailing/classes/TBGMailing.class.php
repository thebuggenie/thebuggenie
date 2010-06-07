<?php

	class TBGMailing extends TBGModule
	{

		protected $mailer = null;
		
		public function __construct($m_id, $res = null)
		{
			$i18n = TBGContext::getI18n();
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName($i18n->__('Email communication'));
			$this->setMenuTitle('');
			$this->setConfigTitle($i18n->__('Email communication'));
			$this->setDescription($i18n->__('Enables in- and outgoing email functionality'));
			$this->setConfigDescription($i18n->__('Set up in- and outgoing email communication from this section'));
			$this->setHasAccountSettings();
			$this->setAccountSettingsName($i18n->__('Notifications'));
			$this->setAccountSettingsLogo('notification_settings.png');
			$this->setHasConfigSettings();
			$this->addAvailableListener('core', 'user_registration', 'listen_registerUser', $i18n->__('Email when user registers'));
			$this->addAvailableListener('core', 'password_reset', 'listen_forgottenPassword', $i18n->__('Email to reset password'));
			$this->addAvailableListener('core', 'viewissue_top', 'listen_issueTop', $i18n->__('Email when user registers'));
			$this->addAvailableListener('core', 'login_middle', 'listen_loginMiddle', $i18n->__('Email to reset password'));
			$this->addAvailableListener('core', 'password_reset', 'listen_passwordReset', $i18n->__('Email when password is reset'));
			$this->addAvailableListener('core', 'TBGIssue::save', 'listen_issueSave', $i18n->__('Email when an issue is updated'));
			$this->addAvailableListener('core', 'TBGIssue::createNew', 'listen_issueCreate', $i18n->__('Email on new issues'));
			$this->addAvailableListener('core', 'TBGComment::createNew', 'listen_TBGComment_createNew', $i18n->__('Email when comments are posted'));

			// No, I didn't forget the parameters, but what else would you call
			// it when it's about retrieving a forgotten password?
			$this->addRoute('forgot', '/forgot', 'forgot');
			$this->addRoute('mailing_test_email', '/mailing/test', 'testEmail');
		}

		public function initialize()
		{
		}
		
		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			
			$module = parent::_install('mailing', 'TBGMailing', '1.0', true, false, false, $scope);
								  
			$module->enableListenerSaved('core', 'user_registration', $scope);
			$module->enableListenerSaved('core', 'login_middle', $scope);
			$module->enableListenerSaved('core', 'password_reset', $scope);
			$module->enableListenerSaved('core', 'TBGIssue::save', $scope);
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

			if ($scope == TBGContext::getScope()->getID())
			{
				TBGMailQueueTable::getTable()->create();
			}

			return true;
		}
		
		public function uninstall()
		{
			$this->_uninstall();
		}

		public function postConfigSettings(TBGRequest $request)
		{
			$settings = array('smtp_host', 'smtp_port', 'smtp_user', 'timeout', 'mail_type', 'enable_outgoing_notifications',
								'smtp_pwd', 'headcharset', 'from_name', 'from_addr', 'ehlo', 'use_queue');
			foreach ($settings as $setting)
			{
				if ($request->getParameter($setting) !== null)
				{
					$this->saveSetting($setting, $request->getParameter($setting));
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

		public function listen_loginMiddle(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				TBGActionComponent::includeComponent('mailing/forgotPasswordBlock');
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
		
		public function sendforgottenPasswordEmail($user)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$subject = TBGContext::getI18n()->__('Forgot your password?');
				$message = $this->createNewTBGMimemailFromTemplate($subject, 'forgottenpassword');
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
				throw TBGContext::getI18n()->__('The email module is not configured for outgoing emails');
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
				$to_users = (array) $to_users;
				foreach ($to_users as $user)
				{
					if ($user->getID() != TBGContext::getUser()->getUID() || $this->getSetting('notify_issue_change_own', $user->getID()))
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
						$issue = TBGFactory::TBGIssueLab($comment->getTargetID());
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
				if ($this->mailer->getType() == TBGMailer::MAIL_TYPE_PHP)
				{
					$this->mailer->setServer($this->getSmtpHost());
					$this->mailer->setPort($this->getSmtpPort());
					$this->mailer->setUsername($this->getSmtpUsername());
					$this->mailer->setPassword($this->getSmtpPassword());
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
				$mail = TBGMimemail::createNewFromTemplate($subject, $template, $parameters, $language, $recipients, $charset);
				$this->_setInitialMailValues($mail);

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
