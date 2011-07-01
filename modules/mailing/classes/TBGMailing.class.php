<?php

	class TBGMailing extends TBGModule
	{

		/**
		 * Notify the user when an issue I posted gets updated
		 */
		const NOTIFY_ISSUE_POSTED_UPDATED = 'notify_issue_posted_updated';
		
		/**
		 * Only notify me once per issue
		 */
		const NOTIFY_ISSUE_ONCE = 'notify_issue_once';
		
		/**
		 * Notify the user when an issue I'm assigned to gets updated
		 */
		const NOTIFY_ISSUE_ASSIGNED_UPDATED = 'notify_issue_assigned_updated';
		
		/**
		 * Notify the user when he updates an issue
		 */
		const NOTIFY_ISSUE_UPDATED_SELF = 'notify_issue_updated_self';
		
		/**
		 * Notify the user when an issue assigned to one of my teams is updated
		 */
		const NOTIFY_ISSUE_TEAMASSIGNED_UPDATED = 'notify_issue_teamassigned_updated';
		
		/**
		 * Notify the user when an issue related to one of my team assigned projects is updated
		 */
		const NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED = 'notify_issue_related_project_teamassigned';
		
		/**
		 * Notify the user when an issue related to one of my assigned projects is updated
		 */
		const NOTIFY_ISSUE_PROJECT_ASSIGNED = 'notify_issue_project_vip';
		
		protected $_longname = 'Email communication';
		
		protected $_description = 'Enables in- and outgoing email functionality';
		
		protected $_module_config_title = 'Email communication';
		
		protected $_module_config_description = 'Set up in- and outgoing email communication from this section';
		
		protected $_account_settings_name = 'Notifications';
		
		protected $_account_settings_logo = 'notification_settings.png';
		
		protected $_has_account_settings = true;

		protected $_has_config_settings = true;
		
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
		
		protected function _initialize()
		{
		}

		protected function _addListeners()
		{
			TBGEvent::listen('core', 'user_registration', array($this, 'listen_registerUser'));
			TBGEvent::listen('core', 'password_reset', array($this, 'listen_forgottenPassword'));
			TBGEvent::listen('core', 'login_form_pane', array($this, 'listen_loginPane'));
			TBGEvent::listen('core', 'login_form_tab', array($this, 'listen_loginTab'));
			//TBGEvent::listen('core', 'TBGIssue::save', array($this, 'listen_issueSave'));
			TBGEvent::listen('core', 'TBGIssue::createNew', array($this, 'listen_issueCreate'));
			TBGEvent::listen('core', 'TBGUser::createNew', array($this, 'listen_createUser'));
			TBGEvent::listen('core', 'TBGComment::createNew', array($this, 'listen_TBGComment_createNew'));
			TBGEvent::listen('core', 'header_begins', array($this, 'listen_headerBegins'));
			TBGEvent::listen('core', 'viewissue', array($this, 'listen_viewissue'));
		}

		protected function _addRoutes()
		{
			$this->addRoute('forgot', '/forgot', 'forgot');
			$this->addRoute('reset', '/reset/:user/:reset_hash', 'resetPassword', array('continue' => true));
			$this->addRoute('mailing_test_email', '/mailing/test', 'testEmail');
		}
		
		protected function _install($scope)
		{
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
				if ($request->getParameter($setting) !== null || $setting = 'no_dash_f')
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

		public function listen_createUser(TBGEvent $event)
		{
			$uid = $event->getSubject()->getID();
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED);

			foreach ($settings as $setting)
				$this->saveSetting($setting, 1, $uid);
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
		
		public function listen_forgottenPassword(TBGEvent $event)
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

		protected function _getIssueRelatedUsers(TBGIssue $issue)
		{
			$uids = array();
			$ns = $this->getSetting(self::NOTIFY_ISSUE_UPDATED_SELF);
			$cu = TBGContext::getUser()->getID();
	
			// Add all users who's marked this issue as interesting
			$uids = TBGUserIssuesTable::getTable()->getUserIDsByIssueID($issue->getID());
	
			// Add all users from the team owning the issue if valid
			// or add the owning user if a user owns the issue
			if ($issue->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				foreach ($issue->getOwner()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getOwnerType() == TBGIdentifiableClass::TYPE_USER)
			{
				if (!($issue->getOwnerID() == $cu && !$ns))
					$uids[$issue->getOwnerID()] = $issue->getOwnerID();
			}

			// Add the poster
			if ($this->getSetting(self::NOTIFY_ISSUE_POSTED_UPDATED, $issue->getPostedByID()))
			{
				if (!($issue->getPostedByID() == $cu && !$ns))
					$uids[$issue->getPostedByID()] = $issue->getPostedByID();
			}

			// Add all users from the team assigned to the issue if valid
			// or add the assigned user if a user is assigned to the issue
			if ($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				// Get team member IDs
				foreach ($issue->getAssignee()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER)
			{
				if (!($issue->getAssigneeID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, $issue->getAssigneeID())))
					$uids[$issue->getAssigneeID()] = $issue->getAssigneeID();
			}
			
			// Add all users in the team who leads the project, if valid
			// or add the user who leads the project, if valid
			if ($issue->getProject()->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				foreach ($issue->getProject()->getLeader()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getProject()->getLeaderType() == TBGIdentifiableClass::TYPE_USER)
			{
				if (!($issue->getProject()->getLeaderID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $issue->getProject()->getLeaderID())))
					$uids[$issue->getProject()->getLeaderID()] = $issue->getProject()->getLeaderID();
			}
	
			// Same for QA
			if ($issue->getProject()->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				foreach ($issue->getProject()->getQaResponsible()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getProject()->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER)
			{
				if (!($issue->getProject()->getQaResponsibleID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $issue->getProject()->getQaResponsibleID())))
					$uids[$issue->getProject()->getQaResponsibleID()] = $issue->getProject()->getQaResponsibleID();
			}
			
			foreach ($issue->getProject()->getAssignedTeams() as $team_id => $assignments)
			{
				foreach (TBGContext::factory()->TBGTeam($team_id)->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			foreach ($issue->getProject()->getAssignedUsers() as $user_id => $assignments)
			{
				$member = TBGContext::factory()->TBGUser($user_id);
				if (!($member->getID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $member->getID())))
					$uids[$member->getID()] = $member->getID();
			}
			
			// Add all users relevant for all affected editions
			foreach ($issue->getEditions() as $edition_list)
			{
				if ($edition_list['edition']->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM)
				{
					foreach ($edition_list['edition']->getLeader()->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				elseif ($edition_list['edition']->getLeaderType() == TBGIdentifiableClass::TYPE_USER)
				{
					if (!($edition_list['edition']->getLeaderID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $edition_list['edition']->getLeaderID())))
						$uids[$edition_list['edition']->getLeaderID()] = $edition_list['edition']->getLeaderID();
				}
				
				if ($edition_list['edition']->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM)
				{
					foreach ($edition_list['edition']->getQaResponsible()->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				elseif ($edition_list['edition']->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER)
				{
					if (!($edition_list['edition']->getQaResponsibleID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $edition_list['edition']->getQaResponsibleID())))
						$uids[$edition_list['edition']->getQaResponsibleID()] = $edition_list['edition']->getQaResponsibleID();
				}
				foreach ($edition_list['edition']->getAssignedTeams() as $team_id => $assignments)
				{
					foreach (TBGContext::factory()->TBGTeam($team_id)->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				foreach ($edition_list['edition']->getAssignedUsers() as $user_id => $assignments)
				{
					$member = TBGContext::factory()->TBGUser($user_id);
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			
			// Add all users relevant for all affected components
			foreach ($issue->getComponents() as $component_list)
			{
				foreach ($component_list['component']->getAssignedTeams() as $team_id => $assignments)
				{
					foreach (TBGContext::factory()->TBGTeam($team_id)->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				foreach ($component_list['component']->getAssignedUsers() as $user_id => $assignments)
				{
					$member = TBGContext::factory()->TBGUser($user_id);
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			
			foreach ($uids as $uid => $val)
			{
				if ($this->getSetting(self::NOTIFY_ISSUE_ONCE, $uid))
				{
					if ($this->getSetting(self::NOTIFY_ISSUE_ONCE . '_' . $issue->getID(), $uid))
					{
						unset($uids[$uid]);
						continue;
					}
					else
					{
						$this->saveSetting(self::NOTIFY_ISSUE_ONCE . '_' . $issue->getID(), 1, $uid);
					}
				}
				$uids[$uid] = TBGContext::factory()->TBGUser($uid);
			}
			
			return $uids;
		}
		
		public function listen_viewissue(TBGEvent $event)
		{
			if ($this->getSetting(self::NOTIFY_ISSUE_ONCE))
			{
				$this->deleteSetting(self::NOTIFY_ISSUE_ONCE . '_' . $event->getSubject()->getID(), $uid);
			}
		}
		
		public function listen_issueCreate(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$issue = $event->getSubject();
				if ($issue instanceof TBGIssue)
				{
					$to_users = $this->_getIssueRelatedUsers($issue);
					$subject = TBGContext::getI18n()->__('[%project_name%] %issue_type% %issue_no% - "%issue_title%" created', array('%project_name%' => $issue->getProject()->getKey(), '%issue_type%' => TBGContext::getI18n()->__($issue->getIssueType()->getName()), '%issue_no%' => $issue->getFormattedIssueNo(true), '%issue_title%' => $issue->getTitle()));
					$message = $this->createNewTBGMimemailFromTemplate($subject, 'issuecreate', array('issue' => $issue));
					$this->_sendToUsers($to_users, $message);
				}
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
					if ($user instanceof TBGUser && $user->isEnabled() && $user->isActivated() && !$user->isDeleted() && !$user->isGuest() && $user->getEmail())
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
							$this->log("There was an error when trying to send email to some recipients:\n" . $e->getMessage(), TBGLogging::LEVEL_NOTICE);
						}
					}
				}
			}
		}
		
		public function listen_TBGComment_createNew(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$comment = $event->getParameter('comment');
				if ($comment instanceof TBGComment && $comment->getTargetType() == TBGComment::TYPE_ISSUE)
				{
					try
					{
						$issue = $event->getSubject();
						$title = $comment->getTitle();
						$content = $comment->getContent();
						$to_users = $this->_getIssueRelatedUsers($issue);
						
						$subject = TBGContext::getI18n()->__('[%project_name%] %issue_type% %issue_no% - "%issue_title%" updated', array('%project_name%' => $issue->getProject()->getKey(), '%issue_type%' => TBGContext::getI18n()->__($issue->getIssueType()->getName()), '%issue_no%' => $issue->getFormattedIssueNo(true), '%issue_title%' => $issue->getTitle()));
						$message = $this->createNewTBGMimemailFromTemplate($subject, 'issueupdate', array('issue' => $issue, 'comment' => $content, 'updated_by' => $comment->getPostedBy()));
//						var_dump($message);
						$this->_sendToUsers($to_users, $message);
//						$subject = TBGContext::getI18n()->__('[%project_name%] %issue_type% %issue_no% - Comment added by %comment_user%', array('%project_name%' => $issue->getProject()->getKey(), '%issue_type%' => TBGContext::getI18n()->__($issue->getIssueType()->getName()), '%issue_no%' => $issue->getFormattedIssueNo(true), '%comment_user%' => $comment->getPostedBy()->getName()));
//						$message = $this->createNewTBGMimemailFromTemplate($subject, 'issuecomment', array('issue' => $issue, 'comment' => $comment));
//						$this->_sendToUsers($to_users, $message);
					}
					catch (Exception $e)
					{
//						var_dump('fu');
//						var_dump($e);die();
						throw $e;
					}
				}
			}
		}
		
		public function listen_issueSave(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$issue = $event->getSubject();

				if ($issue instanceof TBGIssue)
				{
					$to_users = $this->_getIssueRelatedUsers($issue);

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

		public function getEhlo()
		{
			return $this->getSetting('ehlo');
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
					$this->getEhlo() ? $this->mailer->setEhlo() : $this->mailer->setHelo(); 
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
			$mail->addReplacementValues(array('%link_to_reset_password%' => isset($parameters['user']) ? TBGContext::getRouting()->generate('reset', array('user' => str_replace('.', '%2E', $parameters['user']->getUsername()), 'reset_hash' => $parameters['user']->getHashPassword(), 'id' => $parameters['user']->getHashPassword()), false) : '' ));
			$mail->addReplacementValues(array('%link_to_activate%' => isset($parameters['user']) ? TBGContext::getRouting()->generate('activate', array('user' => str_replace('.', '%2E', $parameters['user']->getUsername()), 'key' => $parameters['user']->getHashPassword()), false) : ''));
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
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, self::NOTIFY_ISSUE_UPDATED_SELF);
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
		
		protected function addDefaultSettingsToAllUsers()
		{
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED);
			foreach (TBGUsersTable::getTable()->getAllUserIDs() as $uid)
			{
				foreach ($settings as $setting)
				{
					$this->saveSetting($setting, 1, $uid);
				}
			}
		}

		public function upgradeFrom3dot0()
		{
			$this->addDefaultSettingsToAllUsers();
		}
		
	}
