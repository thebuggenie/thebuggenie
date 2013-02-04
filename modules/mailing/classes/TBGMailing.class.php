<?php

	/**
	 * @Table(name="TBGModulesTable")
	 */
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

		/**
		 * Notify the user when an issue he commented on is updated
		 */
		const NOTIFY_ISSUE_COMMENTED_ON = 'notify_issue_commented_on';

		const MAIL_ENCODING_BASE64 = 3;
		const MAIL_ENCODING_QUOTED = 4;
		const MAIL_ENCODING_UTF7 = 0;

		protected $_longname = 'Email communication';

		protected $_description = 'Enables in- and outgoing email functionality';

		protected $_module_config_title = 'Email communication';

		protected $_module_config_description = 'Set up in- and outgoing email communication from this section';

		protected $_account_settings_name = 'Notification settings';

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
			TBGEvent::listen('core', 'TBGUser::_postSave', array($this, 'listen_registerUser'));
			TBGEvent::listen('core', 'password_reset', array($this, 'listen_forgottenPassword'));
			TBGEvent::listen('core', 'login_form_pane', array($this, 'listen_loginPane'));
			TBGEvent::listen('core', 'login_form_tab', array($this, 'listen_loginTab'));
			TBGEvent::listen('core', 'TBGUser::addScope', array($this, 'listen_addScope'));
			TBGEvent::listen('core', 'TBGIssue::createNew', array($this, 'listen_issueCreate'));
			TBGEvent::listen('core', 'TBGUser::_postSave', array($this, 'listen_createUser'));
			TBGEvent::listen('core', 'TBGIssue::save', array($this, 'listen_issueSave'));
			TBGEvent::listen('core', 'TBGComment::createNew', array($this, 'listen_TBGComment_createNew'));
			TBGEvent::listen('core', 'header_begins', array($this, 'listen_headerBegins'));
			TBGEvent::listen('core', 'viewissue', array($this, 'listen_viewissue'));
			TBGEvent::listen('core', 'user_dropdown_anon', array($this, 'listen_userDropdownAnon'));
			TBGEvent::listen('core', 'config_project_tabs', array($this, 'listen_projectconfig_tab'));
			TBGEvent::listen('core', 'config_project_panes', array($this, 'listen_projectconfig_panel'));
			TBGEvent::listen('core', 'get_backdrop_partial', array($this, 'listen_get_backdrop_partial'));
		}

		protected function _addRoutes()
		{
			$this->addRoute('forgot', '/forgot', 'forgot');
			$this->addRoute('mailing_test_email', '/mailing/test', 'testEmail');
			$this->addRoute('mailing_save_incoming_account', '/mailing/:project_key/incoming_account/*', 'saveIncomingAccount');
			$this->addRoute('mailing_check_account', '/mailing/incoming_account/:account_id/check', 'checkIncomingAccount');
			$this->addRoute('mailing_delete_account', '/mailing/incoming_account/:account_id/delete', 'deleteIncomingAccount');
		}

		protected function _install($scope)
		{
			$this->saveSetting('smtp_host', '', 0, $scope);
			$this->saveSetting('smtp_port', 25, 0, $scope);
			$this->saveSetting('smtp_user', '', 0, $scope);
			$this->saveSetting('smtp_pwd', '', 0, $scope);
			$this->saveSetting('headcharset', TBGContext::getI18n()->getLangCharset(), 0, $scope);
			$this->saveSetting('from_name', 'The Bug Genie Automailer', 0, $scope);
			$this->saveSetting('from_addr', '', 0, $scope);
			$this->saveSetting('ehlo', 1, 0, $scope);
		}

		protected function _uninstall()
		{
			parent::_uninstall();
		}

		public function postConfigSettings(TBGRequest $request)
		{
			TBGContext::loadLibrary('common');
			$settings = array('smtp_host', 'smtp_port', 'smtp_user', 'timeout', 'mail_type', 'enable_outgoing_notifications', 'cli_mailing_url',
								'smtp_pwd', 'headcharset', 'from_name', 'from_addr', 'ehlo', 'use_queue', 'no_dash_f', 'activation_needed');
			foreach ($settings as $setting)
			{
				if ($request->getParameter($setting) !== null || $setting == 'no_dash_f' || $setting == 'activation_needed')
				{
					$value = $request->getParameter($setting);
					switch($setting)
					{
						case 'smtp_host':
							if ($request['mail_type'] == TBGMailer::MAIL_TYPE_CUSTOM && !tbg_check_syntax($value, "MAILSERVER"))
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
							if ($request['mail_type'] == TBGMailer::MAIL_TYPE_CUSTOM && !is_numeric($value) || $value < 0)
							{
								throw new Exception(TBGContext::getI18n()->__('Please provide a valid setting for SMTP server timeout'));
							}
							break;
						case 'smtp_port':
							if ($request['mail_type'] == TBGMailer::MAIL_TYPE_CUSTOM && !is_numeric($value) || $value < 1)
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
						case 'activation_needed':
							$value = (int) $request->getParameter($setting, 0);
							break;
						case 'cli_mailing_url':
							$value = $request->getParameter($setting);
							if (substr($value, -1) == '/')
							{
								$value = substr($value, 0, strlen($value) - 1);
							}
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
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, self::NOTIFY_ISSUE_COMMENTED_ON);

			foreach ($settings as $setting)
				$this->saveSetting($setting, 1, $uid);
		}

		public function generateURL($route, $parameters = array())
		{
			$url = TBGContext::getRouting()->generate($route, $parameters);
			return $this->getMailingUrl() . $url;
		}

		public function getEmailTemplates($template, $parameters = array())
		{
			if (!array_key_exists('module', $parameters)) $parameters['module'] = $this;
			$message_plain = TBGAction::returnTemplateHTML("mailing/{$template}.text", $parameters);
			$html = TBGAction::returnTemplateHTML("mailing/{$template}.html", $parameters);
			$styles = file_get_contents(THEBUGGENIE_MODULES_PATH . 'mailing' . DS . 'fixtures' . DS . TBGSettings::getThemeName() . '.css');
			$message_html = <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
		<head>
			<meta http-equiv=Content-Type content="text/html; charset=utf-8">
			<style type="text/css">
				$styles
			</style>
		</head>
		<body>
			$html
		</body>
	</html>
EOT;
			return array($message_plain, $message_html);
		}

		/**
		 * Gets a configured Swift_Message object
		 *
		 * @param Swift_Message $subject
		 */
		public function getSwiftMessage($subject, $message_plain, $message_html)
		{
			require_once THEBUGGENIE_CORE_PATH . 'lib' . DS . 'swift' . DS . 'lib' . DS . 'swift_required.php';
			$message = Swift_Message::newInstance();
			$message->setSubject($subject);
			$message->setFrom(array($this->getEmailFromAddress() => $this->getEmailFromName()));
			$message->setBody($message_plain);
			$message->addPart($message_html, 'text/html');
			return $message;
		}

		public function getUsersAndLanguages($users)
		{
			$langs = array();
			foreach ($users as $user)
			{
				if ($user instanceof TBGUser)
				{
					$user_language = $user->getLanguage();
					if (!array_key_exists($user_language, $langs)) $langs[$user_language] = array();
					$langs[$user_language][] = $user;
				}
			}

			return $langs;
		}

		public function getTranslatedMessages($subject, $template, $parameters, $users)
		{
			if (!is_array($parameters)) $parameters = array();
			$langs = $this->getUsersAndLanguages($users);
			$messages = array();
			$parameters['module'] = $this;

			foreach ($langs as $language => $users)
			{
				$current_language = TBGContext::getI18n()->getCurrentLanguage();
				try
				{
					TBGContext::getI18n()->setLanguage($language);
					$body_parts = $this->getEmailTemplates($template, $parameters);
					$subject = TBGContext::getI18n()->__($subject);
					$message = $this->getSwiftMessage($subject, $body_parts[0], $body_parts[1]);
					foreach ($users as $user)
					{
						$message->addTo($user->getEmail(), $user->getBuddyname());
					}
					$messages[] = $message;
					TBGContext::getI18n()->setLanguage($current_language);
				}
				catch (Exception $e)
				{
					TBGContext::getI18n()->setLanguage($current_language);
					throw $e;
				}
			}

			return $messages;
		}

		public function listen_registerUser(TBGEvent $event)
		{
			if ($this->isActivationNeeded() && $this->isOutgoingNotificationsEnabled())
			{
				$user = $event->getSubject();
				$password = TBGUser::createPassword(8);
				$user->setPassword($password);
				$user->save();

				if ($user->getEmail())
				{
	//				The following line is included for the i18n parser to pick up the translatable string:
	//				__('User account registered with The Bug Genie');
					$subject = 'User account registered with The Bug Genie';
					$link_to_activate = $this->generateURL('activate', array('user' => str_replace('.', '%2E', $user->getUsername()), 'key' => $user->generateActivationKey()));
					$parameters = compact('user', 'password', 'link_to_activate');
					$messages = $this->getTranslatedMessages($subject, 'registeruser', $parameters, array($user));

					foreach ($messages as $message)
					{
						$this->sendMail($message);
					}
				}
				$event->setProcessed();
			}
		}

		public function listen_addScope(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
//				The following line is included for the i18n parser to pick up the translatable string:
//				__('Your account in The Bug Genie has been added to a new scope');
				$subject = 'Your account in The Bug Genie has been added to a new scope';
				$user = $event->getSubject();
				$scope = $event->getParameter('scope');
				$parameters = compact('user', 'scope');
				$messages = $this->getTranslatedMessages($subject, 'addtoscope', $parameters, array($user));

				foreach ($messages as $message)
				{
					$this->sendMail($message);
				}
				$event->setProcessed();
			}
		}

		public function listen_forgottenPassword(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
//				The following line is included for the i18n parser to pick up the translatable string:
//				__('Password reset');
				$subject = 'Password reset';
				$user = $event->getSubject();
				$parameters = array('user' => $user, 'password' => $event->getParameter('password'));
				$messages = $this->getTranslatedMessages($subject, 'passwordreset', $parameters, array($user));

				foreach ($messages as $message)
				{
					$this->sendMail($message);
				}
			}
		}

		public function sendforgottenPasswordEmail($user)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
//				The following line is included for the i18n parser to pick up the translatable string:
//				__('Forgot your password?');
				$subject = 'Forgot your password?';
				$parameters = compact('user');
				$messages = $this->getTranslatedMessages($subject, 'forgottenpassword', $parameters, array($user));

				foreach ($messages as $message)
				{
					$this->sendMail($message);
				}
			}
		}

		public function sendTestEmail($email_address)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$subject = TBGContext::getI18n()->__('Test email');
				$body_parts = $this->getEmailTemplates('testemail');
				$message = $this->getSwiftMessage($subject, $body_parts[0], $body_parts[1]);
				$message->addTo($email_address);
				return $this->sendMail($message);
			}
			else
			{
				throw new Exception(TBGContext::getI18n()->__('The email module is not configured for outgoing emails'));
			}
		}

		protected function _getIssueRelatedUsers(TBGIssue $issue)
		{
			$uids = array();
			$cu = TBGContext::getUser()->getID();
			$ns = $this->getSetting(self::NOTIFY_ISSUE_UPDATED_SELF, $cu);

			// Add all users who's marked this issue as interesting
			$uids = TBGUserIssuesTable::getTable()->getUserIDsByIssueID($issue->getID());

			// Add all users from the team owning the issue if valid
			// or add the owning user if a user owns the issue
			if ($issue->getOwner() instanceof TBGTeam)
			{
				foreach ($issue->getOwner()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getOwner() instanceof TBGUser)
			{
				if (!($issue->getOwner()->getID() == $cu && !$ns))
					$uids[$issue->getOwner()->getID()] = $issue->getOwner()->getID();
			}

			// Add the poster
			if ($this->getSetting(self::NOTIFY_ISSUE_POSTED_UPDATED, $issue->getPostedByID()))
			{
				if (!($issue->getPostedByID() == $cu && !$ns))
					$uids[$issue->getPostedByID()] = $issue->getPostedByID();
			}

			// Add any users who created a comment
			$cmts = $issue->getComments();
			foreach ($cmts as $cmt)
			{
				$pbid = $cmt->getPostedByID();
				if ($pbid && $this->getSetting(self::NOTIFY_ISSUE_COMMENTED_ON, $pbid))
					$uids[$pbid] = $pbid;
			}

			// Add all users from the team assigned to the issue if valid
			// or add the assigned user if a user is assigned to the issue
			if ($issue->getAssignee() instanceof TBGTeam)
			{
				// Get team member IDs
				foreach ($issue->getAssignee()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getAssignee() instanceof TBGUser)
			{
				if (!($issue->getAssignee()->getID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, $issue->getAssignee()->getID())))
					$uids[$issue->getAssignee()->getID()] = $issue->getAssignee()->getID();
			}

			// Add all users in the team who leads the project, if valid
			// or add the user who leads the project, if valid
			if ($issue->getProject()->getLeader() instanceof TBGTeam)
			{
				foreach ($issue->getProject()->getLeader()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getProject()->getLeader() instanceof TBGUser)
			{
				$lid = $issue->getProject()->getLeader()->getID();
				if (!($lid == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $lid)))
					$uids[$lid] = $lid;
			}

			// Same for QA
			if ($issue->getProject()->getQaResponsible() instanceof TBGTeam)
			{
				foreach ($issue->getProject()->getQaResponsible()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getProject()->getQaResponsible() instanceof TBGUser)
			{
				$qaid = $issue->getProject()->getQaResponsible()->getID();
				if (!($qaid == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $qaid)))
					$uids[$qaid] = $qaid;
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
				if ($edition_list['edition']->getLeader() instanceof TBGTeam)
				{
					foreach ($edition_list['edition']->getLeader()->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				elseif ($edition_list['edition']->getLeader() instanceof TBGUser)
				{
					if (!($edition_list['edition']->getLeaderID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $edition_list['edition']->getLeaderID())))
						$uids[$edition_list['edition']->getLeaderID()] = $edition_list['edition']->getLeaderID();
				}

				if ($edition_list['edition']->getQaResponsible() instanceof TBGTeam)
				{
					foreach ($edition_list['edition']->getQaResponsible()->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				elseif ($edition_list['edition']->getQaResponsible() instanceof TBGUser)
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

		public function listen_issueCreate(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$issue = $event->getSubject();
				if ($issue instanceof TBGIssue)
				{
					$subject = '['.$issue->getProject()->getKey().'] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, TBGContext::getI18n()->getCharset());
					$parameters = compact('issue');
					$to_users = $this->_getIssueRelatedUsers($issue);
					$messages = $this->getTranslatedMessages($subject, 'issuecreate', $parameters, $to_users);

					foreach ($messages as $message)
					{
						$this->sendMail($message);
					}
				}
			}
		}

		public function listen_TBGComment_createNew(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$comment = $event->getSubject();
				if ($comment instanceof TBGComment && $comment->getTargetType() == TBGComment::TYPE_ISSUE)
				{
					$issue = $event->getParameter('issue');
					$subject = 'Re: ['.$issue->getProject()->getKey().'] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, TBGContext::getI18n()->getCharset());
					$parameters = compact('issue', 'comment');
					$to_users = $this->_getIssueRelatedUsers($issue);
					$messages = $this->getTranslatedMessages($subject, 'issuecomment', $parameters, $to_users);

					foreach ($messages as $message)
					{
						$this->sendMail($message);
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
					$subject = 'Re: ['.$issue->getProject()->getKey().'] ' . $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . ' - ' . html_entity_decode($issue->getTitle(), ENT_COMPAT, TBGContext::getI18n()->getCharset());
					$parameters = array('issue' => $issue, 'comment' => $event->getParameter('comment'), 'log_items' => $event->getParameter('log_items'), 'updated_by' => $event->getParameter('updated_by'));
					$to_users = $this->_getIssueRelatedUsers($issue);
					$messages = $this->getTranslatedMessages($subject, 'issueupdate', $parameters, $to_users);

					foreach ($messages as $message)
					{
						$this->sendMail($message);
					}
					//$message = $this->createNewTBGMimemailFromTemplate($subject, 'issueupdate', array('issue' => $issue, 'comment_lines' => $event->getParameter('comment_lines'), 'updated_by' => $event->getParameter('updated_by')));
				}
			}
		}

		public function listen_viewissue(TBGEvent $event)
		{
			$uid = TBGContext::getUser()->getID();
			if ($this->getSetting(self::NOTIFY_ISSUE_ONCE,$uid))
			{
				$this->deleteSetting(self::NOTIFY_ISSUE_ONCE . '_' . $event->getSubject()->getID(), $uid);
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

		public function listen_headerBegins(TBGEvent $event)
		{

		}

		public function listen_userDropdownAnon(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				TBGActionComponent::includeTemplate('mailing/userDropdownAnon', $event->getParameters());
			}
		}

		public function listen_projectconfig_tab(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('mailing/projectconfig_tab', array('selected_tab' => $event->getParameter('selected_tab')));
		}

		public function listen_get_backdrop_partial(TBGEvent $event)
		{
			if ($event->getSubject() == 'mailing_editincomingemailaccount')
			{
				$account = new TBGIncomingEmailAccount(TBGContext::getRequest()->getParameter('account_id'));
				$event->addToReturnList($account, 'account');
				$event->setReturnValue('mailing/editincomingemailaccount');
				$event->setProcessed();
			}
		}

		public function listen_projectconfig_panel(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('mailing/projectconfig_panel', array('selected_tab' => $event->getParameter('selected_tab'), 'access_level' => $event->getParameter('access_level'), 'project' => $event->getParameter('project')));
		}

		public function getMailingUrl($clean = false)
		{
			$url = $this->getSetting('cli_mailing_url');
			if ($clean)
			{
				$url = parse_url($url);
				return $url['host'];
			}
			return $url;
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
		 * @return Swift_Mailer
		 */
		public function getMailer()
		{
			if ($this->mailer === null)
			{
				if ($this->getMailerType() == TBGMailer::MAIL_TYPE_CUSTOM)
				{
					$transport = Swift_SmtpTransport::newInstance($this->getSmtpHost(), $this->getSmtpPort());
					$transport->setUsername($this->getSmtpUsername());
					$transport->setPassword($this->getSmtpPassword());
				}
				else
				{
					$transport = Swift_MailTransport::newInstance();
				}
				$mailer = Swift_Mailer::newInstance($transport);
				$this->mailer = $mailer;
			}

			return $this->mailer;
		}

		protected function _setInitialMailValues(TBGMimemail $mail)
		{
			if (!$this->getEmailFromAddress())
			{
				throw new Exception('The email module does not have a "from" address');
			}
			$mail->setFrom($this->getEmailFromAddress(), $this->getEmailFromName());
			$pre_html_message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><head><meta http-equiv=Content-Type content="text/html; charset=' . $mail->getCharset() . '"><title>' . TBGSettings::getTBGname() . '</title></head><body>';
			$post_html_message = '</body></html>';
			$mail->decorateMessageHTML($pre_html_message, $post_html_message);
			$mail->addReplacementValues(array('%thebuggenie_url%' => $this->getMailingUrl() . TBGContext::getRouting()->generate('home')));
		}

		protected function _setAdditionalMailValues(TBGMimemail $mail, array $parameters)
		{
			$mail->addReplacementValues(array('%link_to_reset_password%' => isset($parameters['user']) ? $this->getMailingUrl() . TBGContext::getRouting()->generate('reset_password', array('user' => str_replace('.', '%2E', $parameters['user']->getUsername()), 'reset_hash' => $parameters['user']->getActivationKey())) : '' ));
			$mail->addReplacementValues(array('%link_to_activate%' => isset($parameters['user']) ? $this->getMailingUrl() . TBGContext::getRouting()->generate('activate', array('user' => str_replace('.', '%2E', $parameters['user']->getUsername()), 'key' => $parameters['user']->getActivationKey())) : ''));
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

		public function mail(Swift_Message $message)
		{
			require_once THEBUGGENIE_CORE_PATH . 'lib' . DS . 'swift' . DS . 'lib' . DS . 'swift_required.php';
			$mailer = $this->getMailer();
			return $mailer->send($message);
		}

		public function sendMail(Swift_Message $email, $debug = false)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				if ($this->usesEmailQueue())
				{
					TBGMailQueueTable::getTable()->addMailToQueue($email);
					return true;
				}
				else
				{
					$retval = $this->mail($email);
				}

				return $retval;
			}
		}

		public function postAccountSettings(TBGRequest $request)
		{
			$uid = TBGContext::getUser()->getID();
			switch ($request['notification_settings_preset'])
			{
				case 'silent':
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_POSTED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_ONCE, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_ASSIGNED_UPDATED, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_UPDATED_SELF, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_PROJECT_ASSIGNED, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_COMMENTED_ON, false, $uid);
					break;
				case 'recommended':
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_POSTED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_ONCE, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_ASSIGNED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_UPDATED_SELF, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_PROJECT_ASSIGNED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_COMMENTED_ON, true, $uid);
					break;
				case 'verbose':
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_POSTED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_ONCE, false, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_ASSIGNED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_UPDATED_SELF, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_PROJECT_ASSIGNED, true, $uid);
					$this->saveSetting(TBGMailing::NOTIFY_ISSUE_COMMENTED_ON, true, $uid);
					break;
				default:
					$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, self::NOTIFY_ISSUE_UPDATED_SELF, self::NOTIFY_ISSUE_COMMENTED_ON);
					foreach ($settings as $setting)
					{
						$this->saveSetting($setting, (int) $request->getParameter($setting, 0), $uid);
					}
			}
			return true;
		}

		public function isOutgoingNotificationsEnabled()
		{
			return (bool) $this->getSetting('enable_outgoing_notifications');
		}

		public function isActivationNeeded()
		{
			return (bool) $this->getSetting('activation_needed');
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
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, self::NOTIFY_ISSUE_COMMENTED_ON);
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

		function getMailMimeType($structure)
		{
			$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
			if ($structure->subtype)
			{
				$type = $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
			}
			else
			{
				$type = "TEXT/PLAIN";
			}
			return $type;
		}

		function getMailPart($stream, $msg_number, $mime_type, $structure, $part_number = false)
		{
			if ($mime_type == $this->getMailMimeType($structure))
			{
				if (!$part_number)
				{
					$part_number = "1";
				}
				$text = imap_fetchbody($stream, $msg_number, $part_number);
				if ($structure->encoding == self::MAIL_ENCODING_BASE64)
				{
					$ret_val = imap_base64($text);
				}
				elseif ($structure->encoding == self::MAIL_ENCODING_QUOTED)
				{
					$ret_val = imap_qprint($text);
				}
				else
				{
					$ret_val = $text;
				}

				return $ret_val;
			}

			if ($structure->type == 1) /* multipart */
			{
				while (list($index, $sub_structure) = each($structure->parts))
				{
					if ($part_number)
					{
						$prefix = $part_number . '.';
					}
					$data = $this->getMailPart($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
					if ($data)
					{
						return $data;
					}
				} // END OF WHILE
			} // END OF MULTIPART
			return false;
		}

		function getMailAttachments($structure, $connection, $message_number)
		{
			$attachments = array();
			if (isset($structure->parts) && count($structure->parts))
			{
				for ($i = 0; $i < count($structure->parts); $i++)
				{
					$attachments[$i] = array(
						'is_attachment' => false,
						'filename' => '',
						'name' => '',
						'mimetype' => '',
						'attachment' => '');

					if ($structure->parts[$i]->ifdparameters)
					{
						foreach ($structure->parts[$i]->dparameters as $object)
						{
							if (strtolower($object->attribute) == 'filename')
							{
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['filename'] = $object->value;
							}
						}
					}

					if ($structure->parts[$i]->ifparameters)
					{
						foreach ($structure->parts[$i]->parameters as $object)
						{
							if (strtolower($object->attribute) == 'name')
							{
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['name'] = $object->value;
							}
						}
					}

					if ($attachments[$i]['is_attachment'])
					{
						$attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i + 1);
						if ($structure->parts[$i]->encoding == 3)
						{ // 3 = BASE64
							$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
						}
						elseif ($structure->parts[$i]->encoding == 4)
						{ // 4 = QUOTED-PRINTABLE
							$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
						}
						$attachments[$i]['mimetype'] = $structure->parts[$i]->type."/".$structure->parts[$i]->subtype;
					}
					else
					{
						unset($attachments[$i]);
					}
				} // for($i = 0; $i < count($structure->parts); $i++)
			} // if(isset($structure->parts) && count($structure->parts))

			return $attachments;
		}

		public function getIncomingEmailAccounts()
		{
			return TBGIncomingEmailAccount::getAll();
		}

		public function getIncomingEmailAccountsForProject(TBGProject $project)
		{
			return TBGIncomingEmailAccount::getAllByProjectID($project->getID());
		}

		public function processIncomingEmails($limit = 25)
		{
			foreach ($this->getIncomingEmailAccounts() as $account)
			{
				$this->processIncomingEmailAccount($account, $limit);
			}
		}

		public function getEmailAdressFromSenderString($from)
		{
			$tokens = explode(" ", $from);
			foreach ($tokens as $email)
			{
				$email = str_replace(array("<", ">"), array("", ""), $email);
				if (filter_var($email, FILTER_VALIDATE_EMAIL))
					return $email;
			}
		}

		public function getOrCreateUserFromEmailString($email_string)
		{
			$email = $this->getEmailAdressFromSenderString($email_string);
			if (!$user = TBGUser::findUser($email))
			{
				$name = $email;

				if (($q_pos = strpos($email_string, "<")) !== false)
				{
					$name = trim(substr($email_string, 0, $q_pos - 1));
				}

				$user = new TBGUser();

				try
				{
					$user->setBuddyname($name);
					$user->setEmail($email);
					$user->setUsername($email);
					$user->setValidated();
					$user->setActivated();
					$user->setEnabled();
					$user->save();
				}
				catch (Exception $e)
				{
					return null;
				}
			}

			return $user;
		}

		public function processIncomingEmailCommand($content, TBGIssue $issue, TBGUser $user)
		{
			if (!$issue->isWorkflowTransitionsAvailable()) return false;

			$lines = preg_split("/(\r?\n)/", $content);
			$first_line = array_shift($lines);
			$commands = explode(" ", trim($first_line));
			$command = array_shift($commands);
			foreach ($issue->getAvailableWorkflowTransitions() as $transition)
			{
				if (strpos(str_replace(array(' ', '/'), array('', ''), mb_strtolower($transition->getName())), str_replace(array(' ', '/'), array('', ''), mb_strtolower($command))) !== false)
				{
					foreach ($commands as $single_command)
					{
						if (mb_strpos($single_command, '='))
						{
							list($key, $val) = explode('=', $single_command);
							switch ($key)
							{
								case 'resolution':
									if (($resolution = TBGResolution::getResolutionByKeyish($val)) instanceof TBGResolution)
									{
										TBGContext::getRequest()->setParameter('resolution_id', $resolution->getID());
									}
									break;
								case 'status':
									if (($status = TBGStatus::getStatusByKeyish($val)) instanceof TBGStatus)
									{
										TBGContext::getRequest()->setParameter('status_id', $status->getID());
									}
									break;
							}
						}
					}
					TBGContext::getRequest()->setParameter('comment_body', join("\n", $lines));
					return $transition->transitionIssueToOutgoingStepFromRequest($issue, TBGContext::getRequest());
				}
			}
		}

		public function processIncomingEmailAccount(TBGIncomingEmailAccount $account, $limit = 25)
		{
			$count = 0;
			if ($emails = $account->getUnprocessedEmails())
			{
				try
				{
					$current_user = TBGContext::getUser();
					foreach ($emails as $email)
					{
						$user = $this->getOrCreateUserFromEmailString($email->from);

						if ($user instanceof TBGUser)
						{
							if (TBGContext::getUser()->getID() != $user->getID()) TBGContext::switchUserContext($user);

							$message = $account->getMessage($email);
							$data = ($message->getBodyPlain()) ? $message->getBodyPlain() : strip_tags($message->getBodyHTML());
							if ($data)
							{
								if (mb_detect_encoding($data, 'UTF-8', true) === false) $data = utf8_encode($data);
								$new_data = '';
								foreach (explode("\n", $data) as $line)
								{
									$line = trim($line);
									if ($line)
									{
										$line = preg_replace('/^(_{2,}|-{2,})$/', "<hr>", $line);
										$new_data .= $line . "\n";
									}
									else
									{
										$new_data .= "\n";
									}
								}
								$data = nl2br($new_data, false);
							}

							$matches = array();
							preg_match(TBGTextParser::getIssueRegex(), mb_decode_mimeheader($email->subject), $matches);

							$issue = ($matches) ? TBGIssue::getIssueFromLink($matches[0], $account->getProject()) : null;

							if ($issue instanceof TBGIssue)
							{
								$text = preg_replace('#(^\w.+:\n)?(^>.*(\n|$))+#mi', "", $data);
								$text = trim($text);
								if (!$this->processIncomingEmailCommand($text, $issue, $user) && $user->canPostComments())
								{
									$comment = new TBGComment();
									$comment->setContent($text);
									$comment->setPostedBy($user);
									$comment->setTargetID($issue->getID());
									$comment->setTargetType(TBGComment::TYPE_ISSUE);
									$comment->save();
								}
							}
							else
							{
								if ($user->canReportIssues($account->getProject()))
								{
									$issue = new TBGIssue();
									$issue->setProject($account->getProject());
									$issue->setTitle(mb_decode_mimeheader($email->subject));
									$issue->setDescription($data);
									$issue->setPostedBy($user);
									$issue->setIssuetype($account->getIssuetype());
									$issue->save();
								}
							}

							if ($issue instanceof TBGIssue && $message->hasAttachments())
							{
								foreach ($message->getAttachments() as $attachment_no => $attachment)
								{
									echo 'saving attachment '.$attachment_no;
									$name = $attachment['filename'];
									$new_filename = TBGContext::getUser()->getID() . '_' . NOW . '_' . basename($name);
									if (TBGSettings::getUploadStorage() == 'files')
									{
										$files_dir = TBGSettings::getUploadsLocalpath();
										$filename = $files_dir.$new_filename;
									}
									else
									{
										$filename = $name;
									}
									TBGLogging::log('Creating issue attachment '.$filename.' from attachment '.$attachment_no);
									echo 'Creating issue attachment '.$filename.' from attachment '.$attachment_no;
									$content_type = $attachment['type'].'/'.$attachment['subtype'];
									$file = new TBGFile();
									$file->setRealFilename($new_filename);
									$file->setOriginalFilename(basename($name));
									$file->setContentType($content_type);
									$file->setDescription($name);
									$file->setUploadedBy(TBGContext::getUser());
									if (TBGSettings::getUploadStorage() == 'database')
									{
										$file->setContent($attachment['data']);
									}
									else
									{
										TBGLogging::log('Saving file '.$new_filename.' with content from attachment '.$attachment_no);
										file_put_contents($new_filename, $attachment['data']);
									}
									$file->save();
									$issue->attachFile($file);
								}
							}

							$count++;
						}
					}
				}
				catch (Exception $e)
				{
				}
				if (TBGContext::getUser()->getID() != $current_user->getID()) TBGContext::switchUserContext($current_user);
			}
			$account->setTimeLastFetched(time());
			$account->setNumberOfEmailsLastFetched($count);
			$account->save();
			return $count;
		}

	}
