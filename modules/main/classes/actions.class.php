<?php 

	/**
	 * actions for the main module
	 */
	class mainActions extends TBGAction
	{

		/**
		 * The currently selected project in actions where there is one
		 *
		 * @access protected
		 * @property TBGProject $selected_project
		 */

		public function preExecute(TBGRequest $request, $action)
		{
			try
			{
				if ($project_key = $request['project_key'])
					$this->selected_project = TBGProject::getByKey($project_key);
				elseif ($project_id = (int) $request['project_id'])
					$this->selected_project = TBGProjectsTable::getTable()->selectById($project_id);
				
				TBGContext::setCurrentProject($this->selected_project);
			}
			catch (Exception $e) {}
		}
		
		protected function _getIssueFromRequest(TBGRequest $request)
		{
			if ($issue_no = TBGContext::getRequest()->getParameter('issue_no'))
			{
				$issue = TBGIssue::getIssueFromLink($issue_no);
				if ($issue instanceof TBGIssue)
				{
					if (!$this->selected_project instanceof TBGProject || $issue->getProjectID() != $this->selected_project->getID())
					{
						$issue = null;
					}
				}
				else
				{
					TBGLogging::log("Issue no [$issue_no] not a valid issue no", 'main', TBGLogging::LEVEL_WARNING_RISK);
				}
			}
			TBGLogging::log('done (Loading issue)');
			//$this->getResponse()->setPage('viewissue');
			if ($issue instanceof TBGIssue && (!$issue->hasAccess() || $issue->isDeleted()))
				$issue = null;

			return $issue;
		}

		/**
		 * Go to the next/previous open issue
		 *
		 * @param TBGRequest $request
		 */
		public function runNavigateIssue(TBGRequest $request)
		{
			$issue = $this->_getIssueFromRequest($request);
			
			if (!$issue instanceof TBGIssue)
			{
				$this->getResponse()->setTemplate('viewissue');
				return;
			}
		
			do
			{
				if ($request['direction'] == 'next')
				{
					$found_issue = TBGIssuesTable::getTable()->getNextIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
				}
				else
				{
					$found_issue = TBGIssuesTable::getTable()->getPreviousIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
				}
				if (is_null($found_issue)) break;
			}
			while ($found_issue instanceof TBGIssue && !$found_issue->hasAccess());
			
			if ($found_issue instanceof TBGIssue)
			{
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $found_issue->getProject()->getKey(), 'issue_no' => $found_issue->getFormattedIssueNo())));
			}
			else
			{
				TBGContext::setMessage('issue_message', $this->getI18n()->__('There are no more issues in that direction.'));
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
		}

		/**
		 * View an issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runViewIssue(TBGRequest $request)
		{
			TBGLogging::log('Loading issue');
			
			$issue = $this->_getIssueFromRequest($request);

			if ($issue instanceof TBGIssue)
			{
				if (!array_key_exists('viewissue_list', $_SESSION))
				{
					$_SESSION['viewissue_list'] = array();
				}
				
				$k = array_search($issue->getID(), $_SESSION['viewissue_list']);
				if ($k !== false) unset($_SESSION['viewissue_list'][$k]);
				
				array_push($_SESSION['viewissue_list'], $issue->getID());
				
				if (count($_SESSION['viewissue_list']) > 10)
					array_shift($_SESSION['viewissue_list']);

				$this->editions = ($issue->getProject()->isEditionsEnabled()) ? $issue->getEditions() : array();
				$this->components = ($issue->getProject()->isComponentsEnabled()) ? $components = $issue->getComponents() : array();
				$this->builds = ($issue->getProject()->isBuildsEnabled()) ? $builds = $issue->getBuilds(): array();
				$this->affected_count = count($this->editions) + count($this->components) + count($this->builds);
				$this->getUser()->markNotificationsRead('issue', $issue->getID());

				TBGEvent::createNew('core', 'viewissue', $issue)->trigger();
			}

			$message = TBGContext::getMessageAndClear('issue_saved');
			$uploaded = TBGContext::getMessageAndClear('issue_file_uploaded');
			
			if ($request->isPost() && $issue instanceof TBGIssue && $request->hasParameter('issue_action'))
			{
				if ($request['issue_action'] == 'save')
				{
					if (!$issue->hasMergeErrors())
					{
						try
						{
							$issue->getWorkflow()->moveIssueToMatchingWorkflowStep($issue);
							$issue->save();
							TBGContext::setMessage('issue_saved', true);
							$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
						}
						catch (TBGWorkflowException $e)
						{
							$this->error = $e->getMessage();
							$this->workflow_error = true;
						}
						catch (Exception $e)
						{
							$this->error = $e->getMessage();
						}
					}
					else
					{
						$this->issue_unsaved = true;
					}
				}
			}
			elseif (TBGContext::hasMessage('issue_deleted'))
			{
				$this->issue_deleted = TBGContext::getMessageAndClear('issue_deleted');
			}
			elseif ($message == true)
			{
				$this->issue_saved = true;
			}
			elseif ($uploaded == true)
			{
				$this->issue_file_uploaded = true;
			}
			elseif (TBGContext::hasMessage('issue_error'))
			{
				$this->error = TBGContext::getMessageAndClear('issue_error');
			}
			elseif (TBGContext::hasMessage('issue_message'))
			{
				$this->issue_message = TBGContext::getMessageAndClear('issue_message');
			}
			
			$this->issue = $issue;
			$event = TBGEvent::createNew('core', 'viewissue', $issue)->trigger();
			$this->listenViewIssuePostError($event);
		}
		
		public function runMoveIssue(TBGRequest $request) 
		{
			$issue = null;
			$project = null;
			$multi = (bool) $request->getParameter('multi', false);
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e) { }
			}
			if ($project_id = $request['project_id'])
			{
				try
				{
					$project = TBGContext::factory()->TBGProject($project_id);
				}
				catch (Exception $e) { }
			}
			
			if (!$issue instanceof TBGIssue)
			{
				if ($multi)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $this->getI18n()->__('Cannot find the issue specified')));
				}
				return $this->return404(TBGContext::getI18n()->__('Cannot find the issue specified'));
			}

			if (!$project instanceof TBGProject)
			{
				if ($multi)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $this->getI18n()->__('Cannot find the project specified')));
				}
				return $this->return404(TBGContext::getI18n()->__('Cannot find the project specified'));
			}

			if ($issue->getProject()->getID() != $project->getID())
			{
				$issue->setProject($project);
				$issue->clearUserWorkingOnIssue();
				$issue->clearAssignee();
				$issue->clearOwner();
				$issue->setPercentCompleted(0);
				$issue->setMilestone(null);
				$issue->setIssueNumber(TBGIssuesTable::getTable()->getNextIssueNumberForProductID($project->getID()));
				$step = $issue->getProject()->getWorkflowScheme()->getWorkflowForIssuetype($issue->getIssueType())->getFirstStep();
				$step->applyToIssue($issue);
				$issue->save();
				if ($multi)
				{
					return $this->renderJSON(array('content' => $this->getTemplateHTML('issuemoved', compact('issue', 'project'))));
				}
				TBGContext::setMessage('issue_message', TBGContext::getI18n()->__('The issue was moved'));
			}
			else
			{
				if ($multi)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $this->getI18n()->__('The issue was not moved, since the project is the same')));
				}
				TBGContext::setMessage('issue_error', TBGContext::getI18n()->__('The issue was not moved, since the project is the same'));
			}
			
			return $this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $project->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Frontpage
		 *  
		 * @param TBGRequest $request
		 */
		public function runIndex(TBGRequest $request)
		{
			if (TBGSettings::isSingleProjectTracker())
			{
				if (($projects = TBGProject::getAllRootProjects(false)) && $project = array_shift($projects))
				{
					$this->forward(TBGContext::getRouting()->generate('project_dashboard', array('project_key' => $project->getKey())));
				}
			}
			$this->forward403unless($this->getUser()->hasPageAccess('home'));
			$this->links = TBGContext::getMainLinks();
			$this->show_project_list = TBGSettings::isFrontpageProjectListVisible();
			$this->show_project_config_link = $this->getUser()->canAccessConfigurationPage(TBGSettings::CONFIGURATION_SECTION_PROJECTS);
			if ($this->show_project_list || $this->show_project_config_link)
			{
				$projects = TBGProject::getAllRootProjects(false);
				foreach ($projects as $k => $project)
				{
					if (!$project->hasAccess()) unset($projects[$k]);
				}
				$this->projects = $projects;
				$this->project_count = count($this->projects);
			}
		}
		
		public function runUserdata(TBGRequest $request)
		{
			if ($this->getUser()->isGuest()) {
				return $this->renderJSON(array());
			} else {
				$data = array();
				if ($request->isPost())
				{
					switch ($request['say'])
					{
						case 'notificationstatus':
							$notification = TBGNotificationsTable::getTable()->selectById($request['notification_id']);
							if ($notification instanceof TBGNotification)
							{
								$notification->setIsRead(!$notification->isRead());
								$notification->save();
								$data['notification_id'] = $notification->getID();
								$data['is_read'] = (int) $notification->isRead();
							}
							break;
						case 'notificationsread':
							$this->getUser()->markAllNotificationsRead();
							$data['all'] = 'read';
							break;
					}
				}
				else
				{
					switch ($request['say']) 
					{
						case 'get_mentionables':
							switch ($request['target_type'])
							{
								case 'issue':
									$target = TBGIssuesTable::getTable()->selectById($request['target_id']);
									break;
								case 'article':
									$target = TBGArticlesTable::getTable()->selectById($request['target_id']);
									break;
								case 'project':
									$target = TBGProjectsTable::getTable()->selectById($request['target_id']);
									break;
							}
							$mentionables = array();
							if (isset($target) && $target instanceof TBGMentionableProvider)
							{
								foreach ($target->getMentionableUsers() as $user)
								{
									if ($user->isOpenIdLocked()) continue;
									$mentionables[$user->getID()] = array('username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL());
								}
							}
							foreach ($this->getUser()->getFriends() as $user)
							{
								if ($user->isOpenIdLocked()) continue;
								$mentionables[$user->getID()] = array('username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL());
							}
							foreach ($this->getUser()->getTeams() as $team)
							{
								foreach ($team->getMembers() as $user)
								{
									if ($user->isOpenIdLocked()) continue;
									$mentionables[$user->getID()] = array('username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL());
								}
							}
							foreach ($this->getUser()->getClients() as $client)
							{
								foreach ($client->getMembers() as $user)
								{
									if ($user->isOpenIdLocked()) continue;
									$mentionables[$user->getID()] = array('username' => $user->getUsername(), 'name' => $user->getName(), 'image' => $user->getAvatarURL());
								}
							}
							$data['mentionables'] = array_values($mentionables);
							break;
						default:
							$data['unread_notifications'] = $this->getUser()->getNumberOfUnreadNotifications();
					}
				}

				return $this->renderJSON($data);
			}
		}

		/**
		 * Developer dashboard
		 *  
		 * @param TBGRequest $request
		 */
		public function runDashboard(TBGRequest $request)
		{
			$this->forward403unless(!$this->getUser()->isThisGuest() && $this->getUser()->hasPageAccess('dashboard'));
			if (TBGSettings::isSingleProjectTracker())
			{
				if (($projects = TBGProject::getAll()) && $project = array_shift($projects))
				{
					TBGContext::setCurrentProject($project);
				}
			}
			if ($request->isPost() && $request['setup_default_dashboard'])
			{
				TBGDashboardViewsTable::getTable()->setDefaultViews($this->getUser()->getID(), TBGDashboardViewsTable::TYPE_USER);
				$this->forward($this->getRouting()->generate('dashboard'));
			}
			$this->views = TBGDashboardView::getUserViews($this->getUser()->getID());
		}
		
		/**
		 * Save dashboard configuration (AJAX call)
		 *  
		 * @param TBGRequest $request
		 */
		public function runDashboardSave(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$login_referer = (array_key_exists('HTTP_REFERER', $_SERVER) && isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
			$this->login_referer = htmlentities($login_referer, ENT_COMPAT, TBGContext::getI18n()->getCharset());
			$this->options = $request->getParameters();
			try
			{
				if (TBGContext::getRequest()->isAjaxCall() || TBGContext::getRequest()->getRequestedFormat() == 'json')
				{
					if ($request->getMethod() == TBGRequest::POST)
					{
						if ($request->hasParameter('id'))
						{
							$views = array();
							foreach(explode(';', $request['id']) as $view)
							{
								array_push($views, array('type' => strrev(mb_strstr(strrev($view), '_', true)), 'id' => mb_strstr($view, '_', true)));
							}
							array_pop($views);
							TBGDashboardView::setViews($request['tid'], $request['target_type'], $views);
							return $this->renderJSON(array('message' => $i18n->__('Dashboard configuration saved')));
						}
						else
						{
							throw new Exception($i18n->__('An internal error has occured'));
						}
					}
					else 
					{
						throw new Exception($i18n->__('An internal error has occured'));
					}
				}
				else 
				{
					throw new Exception($i18n->__('An internal error has occured'));
				}				
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $i18n->__($e->getMessage()), 'referer' => htmlentities($request['tbg3_referer'], ENT_COMPAT, TBGContext::getI18n()->getCharset())));
			}
		}
		
		/**
		 * Client Dashboard
		 *  
		 * @param TBGRequest $request
		 */
		public function runClientDashboard(TBGRequest $request)
		{
			$this->client = null;
			try
			{
				$this->client = TBGContext::factory()->TBGClient($request['client_id']);
				$projects = TBGProject::getAllByClientID($this->client->getID());
				
				$final_projects = array();
				
				foreach ($projects as $project)
				{
					if (!$project->isArchived()): $final_projects[] = $project; endif;
				}
				
				$this->projects = $final_projects;
				
				$this->forward403Unless($this->client->hasAccess());
			}
			catch (Exception $e)
			{
				return $this->return404(TBGContext::getI18n()->__('This client does not exist'));
				TBGLogging::log($e->getMessage(), 'core', TBGLogging::LEVEL_WARNING);
			}
		}
		
		/**
		 * Team Dashboard
		 *  
		 * @param TBGRequest $request
		 */
		public function runTeamDashboard(TBGRequest $request)
		{
			try
			{
				$this->team = TBGContext::factory()->TBGTeam($request['team_id']);
				$this->forward403Unless($this->team->hasAccess());
				
				$projects = array();
				foreach (TBGProject::getAllByOwner($this->team) as $project) {
					$projects[$project->getID()] = $project;
				}
				foreach (TBGProject::getAllByLeader($this->team) as $project) {
					$projects[$project->getID()] = $project;
				}
				foreach (TBGProject::getAllByQaResponsible($this->team) as $project) {
					$projects[$project->getID()] = $project;
				}
				foreach ($this->team->getAssociatedProjects() as $project_id => $project) {
					$projects[$project_id] = $project;
				}

				$final_projects = array();
				
				foreach ($projects as $project)
				{
					if (!$project->isArchived()): $final_projects[] = $project; endif;
				}
				
				$this->projects = $final_projects;
				
				$this->users = $this->team->getMembers();
			}
			catch (Exception $e)
			{
				return $this->return404(TBGContext::getI18n()->__('This team does not exist'));
				TBGLogging::log($e->getMessage(), 'core', TBGLogging::LEVEL_WARNING);
			}
		}
				
		/**
		 * About page
		 *  
		 * @param TBGRequest $request
		 */
		public function runAbout(TBGRequest $request)
		{
			$this->forward403unless($this->getUser()->hasPageAccess('about'));
		}
		
		/**
		 * 404 not found page
		 * 
		 * @param TBGRequest $request
		 */
		public function runNotFound(TBGRequest $request)
		{
			$this->getResponse()->setHttpStatus(404);
			$message = null;
		}
		
		/**
		 * Logs the user out
		 * 
		 * @param TBGRequest $request
		 */
		public function runLogout(TBGRequest $request)
		{
			if ($this->getUser() instanceof TBGUser)
			{
				TBGLogging::log('Setting user logout state');
				$this->getUser()->setOffline();
			}
			TBGContext::logout();
			if ($request->isAjaxCall())
			{
				return $this->renderJSON(array('status' => 'logout ok', 'url' => TBGContext::getRouting()->generate(TBGSettings::getLogoutReturnRoute())));
			}
			$this->forward(TBGContext::getRouting()->generate(TBGSettings::getLogoutReturnRoute()));
		}
		
		/**
		 * Static login page
		 * @param TBGRequest $request
		 */
		public function runLogin(TBGRequest $request)
		{
			//if (!$this->getUser()->isGuest()) return $this->forward(TBGContext::getRouting()->generate('home'));
			$this->section = $request->getParameter('section', 'login');
		}
		
		/**
		 * Static elevated login page
		 * @param TBGRequest $request
		 */
		public function runDoElevatedLogin(TBGRequest $request)
		{
			if ($this->getUser()->hasPassword($request['tbg3_elevated_password']))
			{
				$expiration = time() + (60 * $request->getParameter('tbg3_elevation_duration', 30));
				TBGContext::getResponse()->setCookie('tbg3_elevated_password', $this->getUser()->getPassword(), $expiration);
				return $this->renderJSON(array('elevated' => true));
			}
			else
			{
				return $this->renderJSON(array('elevated' => false, 'error' => $this->getI18n()->__('Incorrect password')));
			}
		}
		
		/**
		 * Static elevated login page
		 * @param TBGRequest $request
		 */
		public function runElevatedLogin(TBGRequest $request)
		{
			if ($this->getUser()->isGuest()) return $this->forward(TBGContext::getRouting()->generate('login_page'));
		}

		public function runDisableTutorial(TBGRequest $request)
		{
			if (strlen(trim($request['key'])))
				$this->getUser()->disableTutorial($request['key']);

			return $this->renderJSON(array('disabled' => $request['key']));
		}

		public function runSwitchUser(TBGRequest $request)
		{
			if (!$this->getUser()->canAccessConfigurationPage(TBGSettings::CONFIGURATION_SECTION_USERS) && !$request->hasCookie('tbg3_original_username'))
				return $this->forward403();

			$response = $this->getResponse();
			if ($request['user_id'])
			{
				$user = new TBGUser($request['user_id']);
				$response->setCookie('tbg3_original_username', $request->getCookie('tbg3_username'));
				$response->setCookie('tbg3_original_password', $request->getCookie('tbg3_password'));
				TBGContext::getResponse()->setCookie('tbg3_password', $user->getPassword());
				TBGContext::getResponse()->setCookie('tbg3_username', $user->getUsername());
			}
			else
			{
				$response->setCookie('tbg3_username', $request->getCookie('tbg3_original_username'));
				$response->setCookie('tbg3_password', $request->getCookie('tbg3_original_password'));
				TBGContext::getResponse()->deleteCookie('tbg3_original_password');
				TBGContext::getResponse()->deleteCookie('tbg3_original_username');
			}
			$this->forward($this->getRouting()->generate('home'));
		}

		protected function checkScopeMembership(TBGUser $user)
		{
			if (!TBGContext::getScope()->isDefault() && !$user->isGuest() && !$user->isConfirmedMemberOfScope(TBGContext::getScope()))
			{
				$route = self::getRouting()->generate('add_scope');
				if (TBGContext::getRequest()->isAjaxCall())
				{
					return $this->renderJSON(array('forward' => $route));
				}
				else
				{
					$this->getResponse()->headerRedirect($route);
				}
			}
		}

		/**
		 * Do login (AJAX call)
		 *  
		 * @param TBGRequest $request
		 */
		public function runDoLogin(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$options = $request->getParameters();
			$forward_url = TBGContext::getRouting()->generate('home');

			if ($request->hasParameter('persona') && $request['persona'] == 'true') 
			{
				$url = 'https://verifier.login.persona.org/verify';
				$assert = filter_input(
					INPUT_POST,
					'assertion',
					FILTER_UNSAFE_RAW,
					FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
				);
				//Use the $_POST superglobal array for PHP < 5.2 and write your own filter 
				$params = 'assertion=' . urlencode($assert) . '&audience=' .
						   urlencode(TBGContext::getURLhost().':80');
				$ch = curl_init();
				$options = array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => TRUE,
					CURLOPT_POST => 2,
					CURLOPT_POSTFIELDS => $params
				);
				curl_setopt_array($ch, $options);
				$result = curl_exec($ch);
				curl_close($ch);
				$details = json_decode($result);
				$user = null;
				if ($details->status == 'okay')
				{
					$user = TBGUser::getByEmail($details->email);
					if ($user instanceof TBGUser)
					{
						TBGContext::getResponse()->setCookie('tbg3_password', $user->getPassword());
						TBGContext::getResponse()->setCookie('tbg3_username', $user->getUsername());
						TBGContext::getResponse()->setCookie('tbg3_persona_session', true);
						return $this->renderJSON(array('status' => 'login ok', 'redirect' => in_array($request['referrer_route'], array('home', 'login'))));
					}
				}
				
				if (!$user instanceof TBGUser)
				{
					$this->getResponse()->setHttpStatus(401);
					$this->renderJSON(array('message' => $this->getI18n()->__('Invalid login')));
				}
				return;
			}
			
			if (TBGSettings::isOpenIDavailable())
				$openid = new LightOpenID(TBGContext::getRouting()->generate('login_page', array(), false));

			if (TBGSettings::isOpenIDavailable() && !$openid->mode && $request->isPost() && $request->hasParameter('openid_identifier')) 
			{
				$openid->identity = $request->getRawParameter('openid_identifier');
				$openid->required = array('contact/email');
				$openid->optional = array('namePerson/first', 'namePerson/friendly');
				return $this->forward($openid->authUrl());
			}
			elseif (TBGSettings::isOpenIDavailable() && $openid->mode == 'cancel') 
			{
				$this->error = TBGContext::getI18n()->__("OpenID authentication cancelled");
			} 
			elseif (TBGSettings::isOpenIDavailable() && $openid->mode)
			{
				try
				{
					if ($openid->validate())
					{
						if ($this->getUser()->isAuthenticated() && !$this->getUser()->isGuest())
						{
							if (TBGOpenIdAccountsTable::getTable()->getUserIDfromIdentity($openid->identity))
							{
								TBGContext::setMessage('openid_used', true);
								throw new Exception('OpenID already in use');
							}
							$user = $this->getUser();
						}
						else
						{
							$user = TBGUser::getByOpenID($openid->identity);
						}
						if ($user instanceof TBGUser)
						{
							$attributes = $openid->getAttributes();
							$email = (array_key_exists('contact/email', $attributes)) ? $attributes['contact/email'] : null;
							if (!$user->getEmail())
							{
								if (array_key_exists('contact/email', $attributes)) $user->setEmail($attributes['contact/email']);
								if (array_key_exists('namePerson/first', $attributes)) $user->setRealname($attributes['namePerson/first']);
								if (array_key_exists('namePerson/friendly', $attributes)) $user->setBuddyname($attributes['namePerson/friendly']);

								if (!$user->getNickname() || $user->isOpenIdLocked()) $user->setBuddyname($user->getEmail());
								if (!$user->getRealname()) $user->setRealname($user->getBuddyname());

								$user->save();
							}
							if (!$user->hasOpenIDIdentity($openid->identity))
							{
								TBGOpenIdAccountsTable::getTable()->addIdentity($openid->identity, $email, $user->getID());
							}
							TBGContext::getResponse()->setCookie('tbg3_password', $user->getPassword());
							TBGContext::getResponse()->setCookie('tbg3_username', $user->getUsername());
							if ($this->checkScopeMembership($user)) return true;

							return $this->forward(TBGContext::getRouting()->generate(TBGSettings::get('returnfromlogin')));
						}
						else
						{
							$this->error = TBGContext::getI18n()->__("Didn't recognize this OpenID. Please log in using your username and password, associate it with your user account in your account settings and try again.");
						}
					}
					else
					{
						$this->error = TBGContext::getI18n()->__("Could not validate against the OpenID provider");
					}
				}
				catch (Exception $e)
				{
					$this->error = TBGContext::getI18n()->__("Could not validate against the OpenID provider: %message", array('%message' => htmlentities($e->getMessage(), ENT_COMPAT, TBGContext::getI18n()->getCharset())));
				}
			}
			elseif ($request->getMethod() == TBGRequest::POST)
			{
				try
				{
					if ($request->hasParameter('tbg3_username') && $request->hasParameter('tbg3_password') && $request['tbg3_username'] != '' && $request['tbg3_password'] != '')
					{
						$user = TBGUser::loginCheck($request, $this);

						TBGContext::setUser($user);
						if ($this->checkScopeMembership($user)) return true;
						if ($request->hasParameter('return_to')) 
						{
							$forward_url = $request['return_to'];
						}
						else
						{
							if (TBGSettings::get('returnfromlogin') == 'referer')
							{
								$forward_url = $request->getParameter('tbg3_referer', TBGContext::getRouting()->generate('dashboard'));
							}
							else
							{
								$forward_url = TBGContext::getRouting()->generate(TBGSettings::get('returnfromlogin'));
							}
						}
						$forward_url = htmlentities($forward_url, ENT_COMPAT, TBGContext::getI18n()->getCharset());
					}
					else
					{
						throw new Exception('Please enter a username and password');
					}
				}
				catch (Exception $e)
				{
					if ($request->isAjaxCall())
					{
						$this->getResponse()->setHttpStatus(401);
						TBGLogging::log($e->getMessage(), 'openid', TBGLogging::LEVEL_WARNING_RISK);
						return $this->renderJSON(array("error" => $i18n->__("Invalid login details")));
					}
					else
					{
						$this->forward403($e->getMessage());
					}
				}
			}
			else
			{
				if ($request->isAjaxCall())
				{
					$this->getResponse()->setHttpStatus(401);
					return $this->renderJSON(array("error" => $i18n->__('Please enter a username and password')));
				}
				else
				{
					$this->forward403($i18n->__('Please enter a username and password'));
				}
			}

			if ($this->checkScopeMembership($user)) return true;
			if ($request->isAjaxCall())
			{
				return $this->renderJSON(array('forward' => $forward_url));
			}
			else
			{
				$this->forward($this->getRouting()->generate('account'));
			}
		}

		/**
		 * Registration logic
		 *  
		 * @param TBGRequest $request
		 */
		public function runRegisterCheckUsernameAvailability(TBGRequest $request)
		{
			$username = mb_strtolower(trim($request['fieldusername']));
			$available = ($username != '') ? TBGUsersTable::getTable()->isUsernameAvailable($username) : false;
			
			return $this->renderJSON(array('available' => (bool) $available));
		}

		/**
		 * Registration logic
		 *  
		 * @param TBGRequest $request
		 */
		public function runRegister(TBGRequest $request)
		{
			TBGContext::loadLibrary('common');
			$i18n = TBGContext::getI18n();
			
			try
			{
				$username = mb_strtolower(trim($request['fieldusername']));
				$buddyname = $request['buddyname'];
				$email = mb_strtolower(trim($request['email_address']));
				$confirmemail = mb_strtolower(trim($request['email_confirm']));
				$security = $request['verification_no'];
				$realname = $request['realname'];
				
				$available = TBGUsersTable::getTable()->isUsernameAvailable($username);
				
				$fields = array();
				
				if (!$available)
				{
					throw new Exception($i18n->__('This username is in use'));
				}
				
				if (!empty($buddyname) && !empty($email) && !empty($confirmemail) && !empty($security))
				{
					if ($email != $confirmemail)
					{
						array_push($fields, 'email_address', 'email_confirm');
						throw new Exception($i18n->__('The email address must be valid, and must be typed twice.'));
					}

					if ($security != $_SESSION['activation_number'])
					{
						array_push($fields, 'verification_no');
						throw new Exception($i18n->__('To prevent automatic sign-ups, enter the verification number shown below.'));
					}

					$email_ok = false;
					$valid_domain = false;

					if (tbg_check_syntax($email, "EMAIL"))
					{
						$email_ok = true;
					}
					
					if ($email_ok && TBGSettings::get('limit_registration') != '')
					{

						$allowed_domains = preg_replace('/[[:space:]]*,[[:space:]]*/' ,'|', TBGSettings::get('limit_registration'));					
						if (preg_match('/@(' . $allowed_domains . ')$/i', $email) == false)
						{							
							array_push($fields, 'email_address', 'email_confirm');					
							throw new Exception($i18n->__('Email adresses from this domain can not be used.'));
						}
						/*if (count($allowed_domains) > 0)
						{
							foreach ($allowed_domains as $allowed_domain)
							{
								$allowed_domain = '@' . trim($allowed_domain);
								if (mb_strpos($email, $allowed_domain) !== false ) //mb_strpos checks if $to
								{
									$valid_domain = true;
									break;
								}
							}
							
						}
						else
						{
							$valid_domain = true;
						}*/
					}
					/*if ($valid_domain == false)
					{
						array_push($fields, 'email_address', 'email_confirm');					
						throw new Exception($i18n->__('Email adresses from this domain can not be used.'));
					}*/
					
					if($email_ok == false)
					{
						array_push($fields, 'email_address', 'email_confirm');
						throw new Exception($i18n->__('The email address must be valid, and must be typed twice.'));
					}
					
					if ($security != $_SESSION['activation_number'])
					{
						array_push($fields, 'verification_no');
						throw new Exception($i18n->__('To prevent automatic sign-ups, enter the verification number shown below.'));
					}					

					$password = TBGUser::createPassword();
					$user = new TBGUser();
					$user->setUsername($username);
					$user->setRealname($realname);
					$user->setBuddyname($buddyname);
					$user->setGroup(TBGSettings::getDefaultGroup());
					$user->setEnabled();
					$user->setPassword($password);
					$user->setEmail($email);
					$user->setJoined();
					$user->save();

					if ($user->isActivated())
					{
						TBGContext::setMessage('auto_password', $password);
						return $this->renderJSON(array('loginmessage' => $i18n->__('After pressing %continue, you need to set your password.', array('%continue' => $i18n->__('Continue'))), 'one_time_password' => $password, 'activated' => true));
					}
					return $this->renderJSON(array('loginmessage' => $i18n->__('The account has now been registered - check your email inbox for the activation email. Please be patient - this email can take up to two hours to arrive.'), 'activated' => false));
				}
				else
				{
					array_push($fields, 'email_address', 'email_confirm', 'buddyname', 'verification_no');
					throw new Exception($i18n->__('You need to fill out all fields correctly.'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $i18n->__($e->getMessage()), 'fields' => $fields));
			}
		}

		/**
		 * Activate newly registered account
		 *  
		 * @param TBGRequest $request
		 */
		public function runActivate(TBGRequest $request)
		{
			$this->getResponse()->setPage('login');
			
			$user = TBGUsersTable::getTable()->getByUsername(str_replace('%2E', '.', $request['user']));
			if ($user instanceof TBGUser)
			{
				if ($user->getActivationKey() != $request['key'])
				{
					 TBGContext::setMessage('login_message_err', TBGContext::getI18n()->__('This activation link is not valid'));
				}
				else
				{
					$user->setValidated(true);
					$user->save();
					TBGContext::setMessage('login_message', TBGContext::getI18n()->__('Your account has been activated! You can now log in with the username %user and the password in your activation email.', array('%user' => $user->getUsername())));
				}
			}
			else
			{
				TBGContext::setMessage('login_message_err', TBGContext::getI18n()->__('This activation link is not valid'));
			}
			$this->forward(TBGContext::getRouting()->generate('login_page'));
		}

		/**
		 * "My account" page
		 *  
		 * @param TBGRequest $request
		 */
		public function runMyAccount(TBGRequest $request)
		{
			$this->forward403unless($this->getUser()->hasPageAccess('account'));
			$notificationsettings = array();
			$i18n = $this->getI18n();
			$notificationsettings[TBGSettings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ISSUES] = $i18n->__('Automatically subscribe to issues I get involved in');
			$notificationsettings[TBGSettings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ARTICLES] = $i18n->__('Automatically subscribe to article I get involved in');
			$notificationsettings[TBGSettings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS] = $i18n->__('Automatically subscribe to new issues that are created in my project(s)');
			$notificationsettings[TBGSettings::SETTINGS_USER_SUBSCRIBE_NEW_ARTICLES_MY_PROJECTS] = $i18n->__('Automatically subscribe to new articles that are created in my project(s)');
			$this->notificationsettings = $notificationsettings;
			$this->has_autopassword = TBGContext::hasMessage('auto_password');
			if ($this->has_autopassword)
			{
				$this->autopassword = TBGContext::getMessage('auto_password');
			}
			
			if ($request->isPost() && $request->hasParameter('mode'))
			{
				switch ($request['mode'])
				{
					case 'information':
						if (!$request['buddyname'] || !$request['email'])
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please fill out all the required fields')));
						}
						$this->getUser()->setBuddyname($request['buddyname']);
						$this->getUser()->setRealname($request['realname']);
						$this->getUser()->setHomepage($request['homepage']);
						$this->getUser()->setEmailPrivate((bool) $request['email_private']);
						$this->getUser()->setUsesGravatar((bool) $request['use_gravatar']);
						$this->getUser()->setTimezone($request->getRawParameter('timezone'));
						$this->getUser()->setLanguage($request['profile_language']);

						if ($this->getUser()->getEmail() != $request['email'])
						{
							if (TBGEvent::createNew('core', 'changeEmail', $this->getUser(), array('email' => $request['email']))->triggerUntilProcessed()->isProcessed() == false)
							{
								$this->getUser()->setEmail($request['email']);
							}
						}

						$this->getUser()->save();

						return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Profile information saved')));
						break;
					case 'settings':
						$this->getUser()->setPreferredWikiSyntax($request['syntax_articles']);
						$this->getUser()->setPreferredIssuesSyntax($request['syntax_issues']);
						$this->getUser()->setPreferredCommentsSyntax($request['syntax_comments']);
						$this->getUser()->setKeyboardNavigationEnabled($request['enable_keyboard_navigation']);
						foreach ($notificationsettings as $setting => $description)
						{
							if ($request->hasParameter('core_'.$setting))
							{
								$this->getUser()->setNotificationSetting($setting, true)->save();
							}
							else
							{
								$this->getUser()->setNotificationSetting($setting, false)->save();
							}
						}
						TBGEvent::createNew('core', 'mainActions::myAccount::saveNotificationSettings')->trigger(compact('request'));
						$this->getUser()->save();

						return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Profile settings saved')));
						break;
					case 'module':
						foreach (TBGContext::getModules() as $module_name => $module)
						{
							if ($request['target_module'] == $module_name && $module->hasAccountSettings())
							{
								if ($module->postAccountSettings($request))
								{
									return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Settings saved')));
								}
								else
								{
									$this->getResponse()->setHttpStatus(400);
									return $this->renderJSON(array('error' => TBGContext::getI18n()->__('An error occured')));
								}
							}
						}
						break;
				}
			}
			$this->rnd_no = rand();
			$this->languages = TBGI18n::getLanguages();
			$this->timezones = TBGI18n::getTimezones();
			$this->error = TBGContext::getMessageAndClear('error');
			$this->username_chosen = TBGContext::getMessageAndClear('username_chosen');
			$this->openid_used = TBGContext::getMessageAndClear('openid_used');
			$this->rsskey_generated = TBGContext::getMessageAndClear('rsskey_generated');
			
			$this->selected_tab = 'profile';
			if ($this->rsskey_generated) $this->selected_tab = 'security';
		}

		/**
		 * Change password ajax action
		 *
		 * @param TBGRequest $request
		 */
		public function runAccountRegenerateRssKey(TBGRequest $request)
		{
			$this->getUser()->regenerateRssKey();
			TBGContext::setMessage('rsskey_generated', true);
			return $this->forward($this->getRouting()->generate('account'));
		}

		/**
		 * Change password ajax action
		 *
		 * @param TBGRequest $request
		 */
		public function runAccountRemovePassword(TBGRequest $request)
		{
			$passwords = $this->getUser()->getApplicationPasswords();
			foreach ($passwords as $password)
			{
				if ($password->getID() == $request['id'])
				{
					$password->delete();
					return $this->renderJSON(array('message' => $this->getI18n()->__('The application password has been deleted')));
				}
			}
			
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => $this->getI18n()->__('Cannot delete this application-specific password')));
		}

		/**
		 * Change password ajax action
		 *
		 * @param TBGRequest $request
		 */
		public function runAccountAddPassword(TBGRequest $request)
		{
			$this->forward403unless($this->getUser()->hasPageAccess('account'));
			if (trim($request['name']))
			{
				$password = new TBGApplicationPassword();
				$password->setUser($this->getUser());
				$password->setName(trim($request['name']));
				$visible_password = strtolower(TBGUser::createPassword());
				$password->setPassword($visible_password);
				$password->save();
				$spans = '';
				
				for ($cc = 0; $cc < 4; $cc++)
				{
					$spans .= '<span>'.substr($visible_password, $cc * 4, 4).'</span>';
				}
				
				return $this->renderJSON(array('password' => $spans));
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $this->getI18n()->__('Please enter a valid name')));
			}
		}

		/**
		 * Change password ajax action
		 *
		 * @param TBGRequest $request
		 */
		public function runAccountChangePassword(TBGRequest $request)
		{
			$this->forward403unless($this->getUser()->hasPageAccess('account'));
			if ($request->isPost())
			{
				if ($this->getUser()->canChangePassword() == false)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__("You're not allowed to change your password.")));
				}
				if (!$request->hasParameter('current_password') || !$request['current_password'])
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please enter your current password')));
				}
				if (!$request->hasParameter('new_password_1') || !$request['new_password_1'])
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please enter a new password')));
				}
				if (!$request->hasParameter('new_password_2') || !$request['new_password_2'])
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please enter the new password twice')));
				}
				if (!$this->getUser()->hasPassword($request['current_password']))
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please enter your current password')));
				}
				if ($request['new_password_1'] != $request['new_password_2'])
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please enter the new password twice')));
				}
				$this->getUser()->changePassword($request['new_password_1']);
				$this->getUser()->save();
				TBGContext::clearMessage('auto_password');
				$this->getResponse()->setCookie('tbg3_password', $this->getUser()->getHashPassword());
				return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Your new password has been saved')));
			}
		}

		protected function _clearReportIssueProperties()
		{
			$this->title = null;
			$this->description = null;
			$this->description_syntax = null;
			$this->reproduction_steps = null;
			$this->reproduction_steps_syntax = null;
			$this->selected_category = null;
			$this->selected_status = null;
			$this->selected_reproducability = null;
			$this->selected_resolution = null;
			$this->selected_severity = null;
			$this->selected_priority = null;
			$this->selected_edition = null;
			$this->selected_build = null;
			$this->selected_component = null;
			$this->selected_estimated_time = null;
			$this->selected_spent_time = null;
			$this->selected_percent_complete = null;
			$this->selected_pain_bug_type = null;
			$this->selected_pain_likelihood = null;
			$this->selected_pain_effect = null;
			$selected_customdatatype = array();
			foreach (TBGCustomDatatype::getAll() as $customdatatype)
			{
				$selected_customdatatype[$customdatatype->getKey()] = null;
			}
			$this->selected_customdatatype = $selected_customdatatype;
		}

		protected function _loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction(TBGRequest $request)
		{
			try
			{
				if ($project_key = $request['project_key'])
					$this->selected_project = TBGProject::getByKey($project_key);
				elseif ($project_id = $request['project_id'])
					$this->selected_project = TBGContext::factory()->TBGProject($project_id);
			}
			catch (Exception $e) {}
			
			if ($this->selected_project instanceof TBGProject)
				TBGContext::setCurrentProject($this->selected_project);
			if ($this->selected_project instanceof TBGProject)
				$this->issuetypes = $this->selected_project->getIssuetypeScheme()->getIssuetypes();
			else
				$this->issuetypes = TBGIssuetype::getAll();

			$this->selected_issuetype = null;
			if ($request->hasParameter('issuetype'))
				$this->selected_issuetype = TBGIssuetype::getIssuetypeByKeyish($request['issuetype']);

			if (!$this->selected_issuetype instanceof TBGIssuetype)
			{
				$this->issuetype_id = $request['issuetype_id'];
				if ($this->issuetype_id)
				{
					try
					{
						$this->selected_issuetype = TBGContext::factory()->TBGIssuetype($this->issuetype_id);
					}
					catch (Exception $e) {}
				}
			}
			else
			{
				$this->issuetype_id = $this->selected_issuetype->getID();
			}
		}

		protected function _postIssueValidation(TBGRequest $request, &$errors, &$permission_errors)
		{
			$i18n = TBGContext::getI18n();
			if (!$this->selected_project instanceof TBGProject) $errors['project'] = $i18n->__('You have to select a valid project');
			if (!$this->selected_issuetype instanceof TBGIssuetype) $errors['issuetype'] = $i18n->__('You have to select a valid issue type');
			if (empty($errors))
			{
				$fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);

				$this->title = $request->getRawParameter('title');
				$this->selected_description = $request->getRawParameter('description', null, false);
				$this->selected_description_syntax = $request->getParameter('description_syntax', null, false);
				$this->selected_reproduction_steps = $request->getRawParameter('reproduction_steps', null, false);
				$this->selected_reproduction_steps_syntax = $request->getRawParameter('reproduction_steps_syntax', null, false);

				if ($edition_id = (int) $request['edition_id'])
					$this->selected_edition = TBGContext::factory()->TBGEdition($edition_id);
				if ($build_id = (int) $request['build_id'])
					$this->selected_build = TBGContext::factory()->TBGBuild($build_id);
				if ($component_id = (int) $request['component_id'])
					$this->selected_component = TBGContext::factory()->TBGComponent($component_id);

				if (trim($this->title) == '' || $this->title == $this->default_title)
					$errors['title'] = true;
				if (isset($fields_array['description']) && $fields_array['description']['required'] && trim($this->selected_description) == '')
					$errors['description'] = true;
				if (isset($fields_array['reproduction_steps']) && !$request->isAjaxCall() && $fields_array['reproduction_steps']['required'] && trim($this->selected_reproduction_steps) == '')
					$errors['reproduction_steps'] = true;

				if (isset($fields_array['edition']) && $edition_id && !in_array($edition_id, array_keys($fields_array['edition']['values'])))
					$errors['edition'] = true;

				if (isset($fields_array['build']) && $build_id && !in_array($build_id, array_keys($fields_array['build']['values'])))
					$errors['build'] = true;

				if (isset($fields_array['component']) && $component_id && !in_array($component_id, array_keys($fields_array['component']['values'])))
					$errors['component'] = true;

				if ($category_id = (int) $request['category_id'])
					$this->selected_category = TBGContext::factory()->TBGCategory($category_id);

				if ($status_id = (int) $request['status_id'])
					$this->selected_status = TBGContext::factory()->TBGStatus($status_id);

				if ($reproducability_id = (int) $request['reproducability_id'])
					$this->selected_reproducability = TBGContext::factory()->TBGReproducability($reproducability_id);

				if ($milestone_id = (int) $request['milestone_id'])
					$this->selected_milestone = TBGContext::factory()->TBGMilestone($milestone_id);

				if ($parent_issue_id = (int) $request['parent_issue_id'])
					$this->parent_issue = TBGContext::factory()->TBGIssue($parent_issue_id);

				if ($resolution_id = (int) $request['resolution_id'])
					$this->selected_resolution = TBGContext::factory()->TBGResolution($resolution_id);

				if ($severity_id = (int) $request['severity_id'])
					$this->selected_severity = TBGContext::factory()->TBGSeverity($severity_id);

				if ($priority_id = (int) $request['priority_id'])
					$this->selected_priority = TBGContext::factory()->TBGPriority($priority_id);

				if ($request['estimated_time'])
					$this->selected_estimated_time = $request['estimated_time'];

				if ($request['spent_time'])
					$this->selected_spent_time = $request['spent_time'];

				if (is_numeric($request['percent_complete']))
					$this->selected_percent_complete = (int) $request['percent_complete'];

				if ($pain_bug_type_id = (int) $request['pain_bug_type_id'])
					$this->selected_pain_bug_type = $pain_bug_type_id;

				if ($pain_likelihood_id = (int) $request['pain_likelihood_id'])
					$this->selected_pain_likelihood = $pain_likelihood_id;

				if ($pain_effect_id = (int) $request['pain_effect_id'])
					$this->selected_pain_effect = $pain_effect_id;

				$selected_customdatatype = array();
				foreach (TBGCustomDatatype::getAll() as $customdatatype)
				{
					$customdatatype_id = $customdatatype->getKey() . '_id';
					$customdatatype_value = $customdatatype->getKey() . '_value';
					if ($customdatatype->hasCustomOptions())
					{
						$selected_customdatatype[$customdatatype->getKey()] = null;
						if ($request->hasParameter($customdatatype_id))
						{
							$$customdatatype_id = (int) $request->getParameter($customdatatype_id);
							$selected_customdatatype[$customdatatype->getKey()] = new TBGCustomDatatypeOption($$customdatatype_id);
						}
					}
					else
					{
						$selected_customdatatype[$customdatatype->getKey()] = null;
						switch ($customdatatype->getType())
						{
							case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
							case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
								if ($request->hasParameter($customdatatype_value))
									$selected_customdatatype[$customdatatype->getKey()] = $request->getParameter($customdatatype_value, null, false);

								break;
							default:
								if ($request->hasParameter($customdatatype_value))
									$selected_customdatatype[$customdatatype->getKey()] = $request->getParameter($customdatatype_value);
								elseif ($request->hasParameter($customdatatype_id))
									$selected_customdatatype[$customdatatype->getKey()] = $request->getParameter($customdatatype_id);

								break;
						}
					}
				}
				$this->selected_customdatatype = $selected_customdatatype;

				foreach ($fields_array as $field => $info)
				{
					if ($field == 'user_pain')
					{
						if ($info['required'])
						{
							if (!($this->selected_pain_bug_type != 0 && $this->selected_pain_likelihood != 0 && $this->selected_pain_effect != 0))
							{
								$errors['user_pain'] = true;
							}
						}
					}
					elseif ($info['required'])
					{
						$var_name = "selected_{$field}";
						if ((in_array($field, TBGDatatype::getAvailableFields(true)) && ($this->$var_name === null || $this->$var_name === 0)) || (!in_array($field, TBGDatatype::getAvailableFields(true)) && !in_array($field, array('pain_bug_type', 'pain_likelihood', 'pain_effect')) && (array_key_exists($field, $selected_customdatatype) && $selected_customdatatype[$field] === null)))
						{
							$errors[$field] = true;
						}
					}
					else
					{
						if (in_array($field, TBGDatatype::getAvailableFields(true)))
						{
							if (!$this->selected_project->fieldPermissionCheck($field, true))
							{
								$permission_errors[$field] = true;
							}
						}
						elseif (!$this->selected_project->fieldPermissionCheck($field, true, true))
						{
							$permission_errors[$field] = true;
						}
					}
				}
				$event = new TBGEvent('core', 'mainActions::_postIssueValidation', null, array(), $errors);
				$event->trigger();
				$errors = $event->getReturnList();
			}
			return !(bool) (count($errors) + count($permission_errors));
		}

		protected function _postIssue()
		{
			$fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);
			$issue = new TBGIssue();
			$issue->setTitle($this->title);
			$issue->setIssuetype($this->issuetype_id);
			$issue->setProject($this->selected_project);
			if (isset($fields_array['description'])) $issue->setDescription($this->selected_description);
			if (isset($fields_array['description_syntax'])) $issue->setDescriptionSyntax($this->selected_description_syntax);
			if (isset($fields_array['reproduction_steps'])) $issue->setReproductionSteps($this->selected_reproduction_steps);
			if (isset($fields_array['reproduction_steps_syntax'])) $issue->setReproductionStepsSyntax($this->selected_reproduction_steps_syntax);
			if (isset($fields_array['category']) && $this->selected_category instanceof TBGDatatype) $issue->setCategory($this->selected_category->getID());
			if (isset($fields_array['status']) && $this->selected_status instanceof TBGDatatype) $issue->setStatus($this->selected_status->getID());
			if (isset($fields_array['reproducability']) && $this->selected_reproducability instanceof TBGDatatype) $issue->setReproducability($this->selected_reproducability->getID());
			if (isset($fields_array['resolution']) && $this->selected_resolution instanceof TBGDatatype) $issue->setResolution($this->selected_resolution->getID());
			if (isset($fields_array['severity']) && $this->selected_severity instanceof TBGDatatype) $issue->setSeverity($this->selected_severity->getID());
			if (isset($fields_array['priority']) && $this->selected_priority instanceof TBGDatatype) $issue->setPriority($this->selected_priority->getID());
			if (isset($fields_array['estimated_time'])) $issue->setEstimatedTime($this->selected_estimated_time);
			if (isset($fields_array['spent_time'])) $issue->setSpentTime($this->selected_spent_time);
			if (isset($fields_array['milestone']) || isset($this->selected_milestone)) $issue->setMilestone($this->selected_milestone);
			if (isset($fields_array['percent_complete'])) $issue->setPercentCompleted($this->selected_percent_complete);
			if (isset($fields_array['pain_bug_type'])) $issue->setPainBugType($this->selected_pain_bug_type);
			if (isset($fields_array['pain_likelihood'])) $issue->setPainLikelihood($this->selected_pain_likelihood);
			if (isset($fields_array['pain_effect'])) $issue->setPainEffect($this->selected_pain_effect);
			foreach (TBGCustomDatatype::getAll() as $customdatatype)
			{
				if (!isset($fields_array[$customdatatype->getKey()])) continue;
				if ($customdatatype->hasCustomOptions())
				{
					if (isset($fields_array[$customdatatype->getKey()]) && $this->selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption)
					{
						$selected_option = $this->selected_customdatatype[$customdatatype->getKey()];
						$issue->setCustomField($customdatatype->getKey(), $selected_option->getID());
					}
				}
				else
				{
					$issue->setCustomField($customdatatype->getKey(), $this->selected_customdatatype[$customdatatype->getKey()]);
				}
			}

			// FIXME: If we set the issue assignee during report issue, this needs to be set INSTEAD of this
			if ($this->selected_project->canAutoassign())
			{
				if (isset($fields_array['component']) && $this->selected_component instanceof TBGComponent && $this->selected_component->hasLeader())
				{
					$issue->setAssignee($this->selected_component->getLeader());
				}
				elseif (isset($fields_array['edition']) && $this->selected_edition instanceof TBGEdition && $this->selected_edition->hasLeader())
				{
					$issue->setAssignee($this->selected_edition->getLeader());
				}
				elseif ($this->selected_project->hasLeader())
				{
					$issue->setAssignee($this->selected_project->getLeader());
				}
			}
			
			$issue->save();

			if (isset($this->parent_issue)) $issue->addParentIssue($this->parent_issue);
			if (isset($fields_array['edition']) && $this->selected_edition instanceof TBGEdition) $issue->addAffectedEdition($this->selected_edition);
			if (isset($fields_array['build']) && $this->selected_build instanceof TBGBuild) $issue->addAffectedBuild($this->selected_build);
			if (isset($fields_array['component']) && $this->selected_component instanceof TBGComponent) $issue->addAffectedComponent($this->selected_component);



			return $issue;
		}
		
		/**
		 * "Report issue" page
		 *  
		 * @param TBGRequest $request
		 */
		public function runReportIssue(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$errors = array();
			$permission_errors = array();
			$this->issue = null;
			$this->getResponse()->setPage('reportissue');

			$this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);
			
			$this->forward403unless(TBGContext::getCurrentProject() instanceof TBGProject && TBGContext::getCurrentProject()->hasAccess() && $this->getUser()->canReportIssues(TBGContext::getCurrentProject()));
			
			if ($request->isPost())
			{
				if ($this->_postIssueValidation($request, $errors, $permission_errors))
				{
					try
					{
						$issue = $this->_postIssue();
						if ($request->hasParameter('files') && $request->hasParameter('file_description'))
						{
							$files = $request['files'];
							$file_descriptions = $request['file_description'];
							foreach ($files as $file_id => $nothing)
							{
								$file = TBGFilesTable::getTable()->selectById((int) $file_id);
								$file->setDescription($file_descriptions[$file_id]);
								$file->save();
								TBGIssueFilesTable::getTable()->addByIssueIDandFileID($issue->getID(), $file->getID());
							}
						}
						if ($request['return_format'] == 'planning')
						{
							$this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);
							$options['selected_issuetype'] = $issue->getIssueType();
							$options['selected_project'] = $this->selected_project;
							$options['issuetypes'] = $this->issuetypes;
							$options['issue'] = $issue;
							$options['errors'] = $errors;
							$options['permission_errors'] = $permission_errors;
							if ($request->hasParameter('milestone_id'))
							{
								try
								{
									$options['selected_milestone'] = TBGContext::factory()->TBGMilestone((int) $request['milestone_id']);
								}
								catch (Exception $e) {}
							}
							if ($request->hasParameter('parent_issue_id'))
							{
								try
								{
									$options['parent_issue'] = TBGContext::factory()->TBGIssue((int) $request['parent_issue_id']);
								}
								catch (Exception $e) {}
							}
							if ($request->hasParameter('build_id'))
							{
								try
								{
									$options['selected_build'] = TBGContext::factory()->TBGBuild((int) $request['build_id']);
								}
								catch (Exception $e) {}
							}
							return $this->renderJSON(array('content' => $this->getComponentHTML('main/reportissuecontainer', $options)));
						}
						if ($request->getRequestedFormat() != 'json' && $issue->getProject()->getIssuetypeScheme()->isIssuetypeRedirectedAfterReporting($this->selected_issuetype))
						{
							$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), 303);
						}
						else
						{
							$this->_clearReportIssueProperties();
							$this->issue = $issue;
						}
					}
					catch (Exception $e)
					{
						if ($request['return_format'] == 'planning')
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => $e->getMessage()));
						}
						$errors[] = $e->getMessage();
					}
				}
			}
			if ($request['return_format'] == 'planning')
			{
				$err_msg = array();
				foreach ($errors as $field => $value)
				{
					$err_msg[] = $i18n->__('Please provide a value for the %field_name field', array('%field_name' => $field));
				}
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $i18n->__('An error occured while creating this story: %errors', array('%errors' => '')), 'message' => join('<br>', $err_msg)));
			}
			$this->errors = $errors;
			$this->permission_errors = $permission_errors;
            $this->options = $this->getParameterHolder();
		}
		
		/**
		 * Retrieves the fields which are valid for that product and issue type combination
		 *  
		 * @param TBGRequest $request
		 */
		public function runReportIssueGetFields(TBGRequest $request)
		{
			if (!$this->selected_project instanceof TBGProject)
			{
				return $this->renderText('invalid project');
			}
			
			$fields_array = $this->selected_project->getReportableFieldsArray($request['issuetype_id']);
			$available_fields = TBGDatatypeBase::getAvailableFields();
			$available_fields[] = 'pain_bug_type';
			$available_fields[] = 'pain_likelihood';
			$available_fields[] = 'pain_effect';
			return $this->renderJSON(array('available_fields' => $available_fields, 'fields' => $fields_array));
		}

		/**
		 * Toggle favourite issue (starring)
		 *  
		 * @param TBGRequest $request
		 */
		public function runToggleFavouriteIssue(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
					$user = TBGUsersTable::getTable()->selectById($request['user_id']);
				}
				catch (Exception $e)
				{
					return $this->renderText('fail');
				}
			}
			else
			{
				return $this->renderText('no issue');
			}
			
			if ($user->isIssueStarred($issue_id))
			{
				$retval = !$user->removeStarredIssue($issue_id);
			}
			else
			{
				$retval = $user->addStarredIssue($issue_id);
				if ($user->getID() != $this->getUser()->getID())
				{
					TBGEvent::createNew('core', 'issue_subscribe_user', $issue, compact('user'))->trigger();
				}
			}


			return $this->renderText(json_encode(array('starred' => $retval, 'subscriber' => $this->getTemplateHTML('main/issuesubscriber', array('user' => $user, 'issue' => $issue)))));
		}
		
		public function _setFieldFromRequest(TBGRequest $request)
		{
			
		}

		public function runIssueDeleteTimeSpent(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGIssuesTable::getTable()->selectById($issue_id);
					if ($entry_id = $request['entry_id'])
					{
						$spenttime = TBGIssueSpentTimesTable::getTable()->selectById($entry_id);
					}
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderText('fail');
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderText('no issue');
			}

			$spenttime->delete();
			$spenttime->getIssue()->save();
			$timesum = array_sum($spenttime->getIssue()->getSpentTime());

			return $this->renderJSON(array('deleted' => 'ok', 'issue_id' => $issue_id, 'timesum' => $timesum, 'spenttime' => TBGIssue::getFormattedTime($spenttime->getIssue()->getSpentTime())));
		}

		public function runIssueEditTimeSpent(TBGRequest $request)
		{
			$entry_id = $request['entry_id'];
			$spenttime = ($entry_id) ? TBGIssueSpentTimesTable::getTable()->selectById($entry_id) : new TBGIssueSpentTime();

			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderText('fail');
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderText('no issue');
			}

			if (!$spenttime->getID())
			{
				if ($request['timespent_manual'])
				{
					$times = TBGIssue::convertFancyStringToTime($request['timespent_manual']);
				}
				else
				{
					$times = array('points' => 0, 'hours' => 0, 'days' => 0, 'weeks' => 0, 'months' => 0);
					$times[$request['timespent_specified_type']] = $request['timespent_specified_value'];
				}
				$spenttime->setIssue($issue);
				$spenttime->setUser($this->getUser());
			}
			else
			{
				$times = array('points' => $request['points'],
								'hours' => $request['hours'],
								'days' => $request['days'],
								'weeks' => $request['weeks'],
								'months' => $request['months']);
				$edited_at = $request['edited_at'];
				$spenttime->setEditedAt(mktime(0, 0, 1, $edited_at['month'], $edited_at['day'], $edited_at['year']));
			}
			$times['hours'] *= 100;
			$spenttime->setSpentPoints($times['points']);
			$spenttime->setSpentHours($times['hours']);
			$spenttime->setSpentDays($times['days']);
			$spenttime->setSpentWeeks($times['weeks']);
			$spenttime->setSpentMonths($times['months']);
			$spenttime->setActivityType($request['timespent_activitytype']);
			$spenttime->setComment($request['timespent_comment']);
			$spenttime->save();

			$spenttime->getIssue()->save();

			$timesum = array_sum($spenttime->getIssue()->getSpentTime());

			return $this->renderJSON(array('edited' => 'ok', 'issue_id' => $issue_id, 'timesum' => $timesum, 'spenttime' => TBGIssue::getFormattedTime($spenttime->getIssue()->getSpentTime()), 'timeentries' => $this->getComponentHTML('main/issuespenttimes', array('issue' => $spenttime->getIssue()))));
		}

		/**
		 * Sets an issue field to a specified value
		 * 
		 * @param TBGRequest $request
		 */
		public function runIssueSetField(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGIssuesTable::getTable()->selectById($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderText('fail');
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderText('no issue');
			}

			TBGContext::loadLibrary('common');
			
			if (!$issue instanceof TBGIssue) return false;
			
			switch ($request['field'])
			{
				case 'description':
					if (!$issue->canEditDescription()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));

					$issue->setDescription($request->getRawParameter('value'));
					$issue->setDescriptionSyntax($request->getParameter('value_syntax'));
					return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isDescriptionChanged(), 'field' => array('id' => (int) ($issue->getDescription() != ''), 'name' => $issue->getParsedDescription(array('issue' => $issue))), 'description' => $issue->getParsedDescription(array('issue' => $issue))));
					break;
				case 'reproduction_steps':
					if (!$issue->canEditReproductionSteps()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					
					$issue->setReproductionSteps($request->getRawParameter('value'));
					$issue->setReproductionStepsSyntax($request->getParameter('value_syntax'));
					return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isReproductionStepsChanged(), 'field' => array('id' => (int) ($issue->getReproductionSteps() != ''), 'name' => $issue->getParsedReproductionSteps(array('issue' => $issue))), 'reproduction_steps' => $issue->getParsedReproductionSteps(array('issue' => $issue))));
					break;
				case 'title':
					if (!$issue->canEditTitle()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					
					if ($request['value'] == '')
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You have to provide a title')));
					}
					else
					{
						$issue->setTitle($request->getRawParameter('value'));
						return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isTitleChanged(), 'field' => array('id' => 1, 'name' => strip_tags($issue->getTitle()))));
					}
					break;
				case 'percent_complete':
					if (!$issue->canEditPercentage()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					
					$issue->setPercentCompleted($request['percent']);
					return $this->renderJSON(array('issue_id' => $issue->getID(), 'field' => 'percent_complete', 'changed' => $issue->isPercentCompletedChanged(), 'percent' => $issue->getPercentCompleted()));
					break;
				case 'estimated_time':
					if (!$issue->canEditEstimatedTime()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					if (!$issue->isUpdateable()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('This issue cannot be updated')));
					
					if ($request['estimated_time'])
					{
						$issue->setEstimatedTime($request['estimated_time']);
					}
					elseif ($request->hasParameter('value'))
					{
						$issue->setEstimatedTime($request['value']);
					}
					else
					{
						$issue->setEstimatedMonths($request['months']);
						$issue->setEstimatedWeeks($request['weeks']);
						$issue->setEstimatedDays($request['days']);
						$issue->setEstimatedHours($request['hours']);
						$issue->setEstimatedPoints($request['points']);
					}
					if ($request['do_save'])
					{
						$issue->save();
					}
					return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isEstimatedTimeChanged(), 'field' => (($issue->hasEstimatedTime()) ? array('id' => 1, 'name' => TBGIssue::getFormattedTime($issue->getEstimatedTime())) : array('id' => 0)), 'values' => $issue->getEstimatedTime()));
					break;
				case 'posted_by':
				case 'owned_by':
				case 'assigned_to':
					if ($request['field'] == 'posted_by' && !$issue->canEditPostedBy()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'owned_by' && !$issue->canEditOwner()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'assigned_to' && !$issue->canEditAssignee()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					
					if ($request->hasParameter('value'))
					{
						if ($request->hasParameter('identifiable_type'))
						{
							if (in_array($request['identifiable_type'], array('team', 'user')) && $request['value'] != 0)
							{
								switch ($request['identifiable_type'])
								{
									case 'user':
										$identified = TBGContext::factory()->TBGUser($request['value']);
										break;
									case 'team':
										$identified = TBGContext::factory()->TBGTeam($request['value']);
										break;
								}
								if ($identified instanceof TBGUser || $identified instanceof TBGTeam)
								{
									if ((bool) $request->getParameter('teamup', false))
									{
										$team = new TBGTeam();
										$team->setName($identified->getBuddyname() . ' & ' . $this->getUser()->getBuddyname());
										$team->setOndemand(true);
										$team->save();
										$team->addMember($identified);
										$team->addMember($this->getUser());
										$identified = $team;
									}
									if ($request['field'] == 'owned_by') $issue->setOwner($identified);
									elseif ($request['field'] == 'assigned_to') $issue->setAssignee($identified);
								}
							}
							else
							{
								if ($request['field'] == 'owned_by') $issue->clearOwner();
								elseif ($request['field'] == 'assigned_to') $issue->clearAssignee();
							}
						}
						elseif ($request['field'] == 'posted_by')
						{
							$identified = TBGContext::factory()->TBGUser($request['value']);
							if ($identified instanceof TBGUser)
							{
								$issue->setPostedBy($identified);
							}
						}
						if ($request['field'] == 'posted_by')
							return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isPostedByChanged(), 'field' => array('id' => $issue->getPostedByID(), 'name' => $this->getComponentHTML('main/userdropdown', array('user' => $issue->getPostedBy())))));
						if ($request['field'] == 'owned_by')
							return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isOwnerChanged(), 'field' => (($issue->isOwned()) ? array('id' => $issue->getOwner()->getID(), 'name' => (($issue->getOwner() instanceof TBGUser) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getOwner())))) : array('id' => 0))));
						if ($request['field'] == 'assigned_to')
							return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isAssigneeChanged(), 'field' => (($issue->isAssigned()) ? array('id' => $issue->getAssignee()->getID(), 'name' => (($issue->getAssignee() instanceof TBGUser) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getAssignee())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getAssignee())))) : array('id' => 0))));
					}
					break;
				case 'spent_time':
					if (!$issue->canEditSpentTime()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					
					if ($request['spent_time'] != TBGContext::getI18n()->__('Enter time spent here') && $request['spent_time'])
					{
						$issue->addSpentTime($request['spent_time']);
					}
					elseif ($request->hasParameter('value'))
					{
						$issue->addSpentTime($request['value']);
					}
					else
					{
						$issue->addSpentMonths($request['months']);
						$issue->addSpentWeeks($request['weeks']);
						$issue->addSpentDays($request['days']);
						$issue->addSpentHours($request['hours']);
						$issue->addSpentPoints($request['points']);
					}
					return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>$issue->isSpentTimeChanged(), 'field' => (($issue->hasSpentTime()) ? array('id' => 1, 'name' => TBGIssue::getFormattedTime($issue->getSpentTime())) : array('id' => 0)), 'values' => $issue->getSpentTime()));
					break;
				case 'category':
				case 'resolution':
				case 'severity':
				case 'reproducability':
				case 'priority':
				case 'milestone':
				case 'issuetype':
				case 'status':
				case 'pain_bug_type':
				case 'pain_likelihood':
				case 'pain_effect':
					if ($request['field'] == 'category' && !$issue->canEditCategory()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'resolution' && !$issue->canEditResolution()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'severity' && !$issue->canEditSeverity()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'reproducability' && !$issue->canEditReproducability()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'priority' && !$issue->canEditPriority()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'milestone' && !$issue->canEditMilestone()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'issuetype' && !$issue->canEditIssuetype()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif ($request['field'] == 'status' && !$issue->canEditStatus()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					elseif (in_array($request['field'], array('pain_bug_type', 'pain_likelihood', 'pain_effect')) && !$issue->canEditUserPain()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'error' => TBGContext::getI18n()->__('You do not have permission to perform this action')));
					
					try
					{
						$classname = null;
						$parameter_name = mb_strtolower($request['field']);
						$parameter_id_name = "{$parameter_name}_id";
						$is_pain = in_array($parameter_name, array('pain_bug_type', 'pain_likelihood', 'pain_effect'));
						if ($is_pain)
						{
							switch ($parameter_name)
							{
								case 'pain_bug_type':
									$set_function_name = 'setPainBugType';
									$is_changed_function_name = 'isPainBugTypeChanged';
									$get_pain_type_label_function = 'getPainBugTypeLabel';
									break;
								case 'pain_likelihood':
									$set_function_name = 'setPainLikelihood';
									$is_changed_function_name = 'isPainLikelihoodChanged';
									$get_pain_type_label_function = 'getPainLikelihoodLabel';
									break;
								case 'pain_effect':
									$set_function_name = 'setPainEffect';
									$is_changed_function_name = 'isPainEffectChanged';
									$get_pain_type_label_function = 'getPainEffectLabel';
									break;
							}
						}
						else
						{
							$classname = 'TBG'.ucfirst($parameter_name);
							$lab_function_name = $classname;
							$set_function_name = 'set'.ucfirst($parameter_name);
							$is_changed_function_name = 'is'.ucfirst($parameter_name).'Changed';
						}
						if ($request->hasParameter($parameter_id_name)) //$request['field'] == 'pain_bug_type')
						{
							$parameter_id = $request->getParameter($parameter_id_name);
							if ($parameter_id !== 0)
							{
								$is_valid = ($is_pain) ? in_array($parameter_id, array_keys(TBGIssue::getPainTypesOrLabel($parameter_name))) : ($parameter_id == 0 || (($parameter = TBGContext::factory()->$lab_function_name($parameter_id)) instanceof $classname));
							}
							if ($parameter_id == 0 || ($parameter_id !== 0 && $is_valid))
							{
								if ($classname == 'TBGIssuetype')
								{
									$visible_fields = ($issue->getIssuetype() instanceof TBGIssuetype) ? $issue->getProject()->getVisibleFieldsArray($issue->getIssuetype()->getID()) : array();
								}
								else
								{
									$visible_fields = null;
								}
								$issue->$set_function_name($parameter_id);
								if ($is_pain)
								{
									if (!$issue->$is_changed_function_name()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
									return ($parameter_id == 0) ? $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText())) : $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => $parameter_id, 'name' => $issue->$get_pain_type_label_function()), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
								}
								else
								{
									if (!$issue->$is_changed_function_name()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false));
									
									if (isset($parameter))
									{
										$name = $parameter->getName();
									}
									else
									{
										$name = null;
									}
									
									$field = array('id' => $parameter_id, 'name' => $name);
									if ($classname == 'TBGIssuetype')
									{
										TBGContext::loadLibrary('ui');
										$field['src'] = htmlspecialchars(TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getThemeName() . '/' . $issue->getIssuetype()->getIcon() . '_small.png');
									}
									if ($parameter_id == 0) 
									{
										return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0)));
									}
									else
									{
										$options = array('issue_id' => $issue->getID(), 'changed' =>true, 'visible_fields' => $visible_fields, 'field' => $field);
										if ($request['field'] == 'milestone')
											$options['field']['url'] = $this->getRouting()->generate('project_milestone_details', array('project_key' => $issue->getProject()->getKey(), 'milestone_id' => $issue->getMilestone()->getID()));
										if ($request['field'] == 'status')
											$options['field']['color'] = $issue->getStatus()->getItemdata();

										return $this->renderJSON($options);
									}
								}
							}
						}
					}
					catch (Exception $e)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => $e->getMessage()));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field value specified')));
					break;
				default:
					if ($customdatatype = TBGCustomDatatype::getByKey($request['field']))
					{
						$key = $customdatatype->getKey();
						
						$customdatatypeoption_value = $request->getParameter("{$key}_value");
						if (!$customdatatype->hasCustomOptions())
						{
							switch ($customdatatype->getType())
							{
								case TBGCustomDatatype::EDITIONS_CHOICE:
								case TBGCustomDatatype::COMPONENTS_CHOICE:
								case TBGCustomDatatype::RELEASES_CHOICE:
								case TBGCustomDatatype::STATUS_CHOICE:
								case TBGCustomDatatype::MILESTONE_CHOICE:
								case TBGCustomDatatype::USER_CHOICE:
								case TBGCustomDatatype::TEAM_CHOICE:
									if ($customdatatypeoption_value == '')
									{
										$issue->setCustomField($key, "");
									}
									else
									{
										switch ($customdatatype->getType())
										{
											case TBGCustomDatatype::EDITIONS_CHOICE:
												$temp = TBGEditionsTable::getTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
											case TBGCustomDatatype::COMPONENTS_CHOICE:
												$temp = TBGComponentsTable::getTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
											case TBGCustomDatatype::RELEASES_CHOICE:
												$temp = TBGBuildsTable::getTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
											case TBGCustomDatatype::MILESTONE_CHOICE:
												$temp = TBGMilestonesTable::getTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
											case TBGCustomDatatype::STATUS_CHOICE:
												$temp = TBGStatus::getB2DBTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
											case TBGCustomDatatype::USER_CHOICE:
												$temp = TBGUsersTable::getTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
											case TBGCustomDatatype::TEAM_CHOICE:
												$temp = TBGTeamsTable::getTable()->selectById($request->getRawParameter("{$key}_value"));
												break;
										}
										$finalvalue = $temp->getName();
										$issue->setCustomField($key, $request->getRawParameter("{$key}_value"));
									}

									if ($customdatatype->getType() == TBGCustomDatatype::STATUS_CHOICE && isset($temp) && is_object($temp))
									{
										$finalvalue = '<div class="status_badge" style="background-color: '.$temp->getColor().';"><span>'.$finalvalue.'</span></div>';
									}
									elseif ($customdatatype->getType() == TBGCustomDatatype::USER_CHOICE && isset($temp) && is_object($temp))
									{
										$finalvalue = $this->getComponentHTML('main/userdropdown', array('user' => $temp));
									}
									elseif ($customdatatype->getType() == TBGCustomDatatype::TEAM_CHOICE && isset($temp) && is_object($temp))
									{
										$finalvalue = $this->getComponentHTML('main/teamdropdown', array('team' => $temp));
									}

									$changed_methodname = "isCustomfield{$key}Changed";
									if (!$issue->$changed_methodname()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false));
									return ($customdatatypeoption_value == '') ? $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0))) : $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('value' => $key, 'name' => $finalvalue)));
									break;
								case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
								case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
									if ($customdatatypeoption_value == '')
									{
										$issue->setCustomField($key, "");
									}
									else
									{
										$issue->setCustomField($key, $request->getRawParameter("{$key}_value"));
									}
									$changed_methodname = "isCustomfield{$key}Changed";
									if (!$issue->$changed_methodname()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false));
									return ($customdatatypeoption_value == '') ? $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0))) : $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('value' => $key, 'name' => tbg_parse_text($request->getRawParameter("{$key}_value")))));
									break;
								case TBGCustomDatatype::DATE_PICKER:
									if ($customdatatypeoption_value == '')
									{
										$issue->setCustomField($key, "");
									}
									else
									{
										$issue->setCustomField($key, $request->getParameter("{$key}_value"));
									}
									$changed_methodname = "isCustomfield{$key}Changed";
									if (!$issue->$changed_methodname()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false));
									return ($customdatatypeoption_value == '') ? $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0))) : $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('value' => $key, 'name' => date('Y-m-d', (int) $request->getRawParameter("{$key}_value")))));
									break;
								default:
									if ($customdatatypeoption_value == '')
									{
										$issue->setCustomField($key, "");
									}
									else
									{
										$issue->setCustomField($key, $request->getParameter("{$key}_value"));
									}
									$changed_methodname = "isCustomfield{$key}Changed";
									if (!$issue->$changed_methodname()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false));
									return ($customdatatypeoption_value == '') ? $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0))) : $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' => true, 'field' => array('value' => $key, 'name' => (filter_var($customdatatypeoption_value, FILTER_VALIDATE_URL) !== false) ? "<a href=\"{$customdatatypeoption_value}\">{$customdatatypeoption_value}</a>" : $customdatatypeoption_value)));
									break;
							}
						}
						$customdatatypeoption = ($customdatatypeoption_value) ? TBGCustomDatatypeOption::getB2DBTable()->selectById($customdatatypeoption_value) : null;
						if ($customdatatypeoption instanceof TBGCustomDatatypeOption)
						{
							$issue->setCustomField($key, $customdatatypeoption->getID());
						}
						else
						{
							$issue->setCustomField($key, null);
						}
						$changed_methodname = "isCustomfield{$key}Changed";
						if (!$issue->$changed_methodname()) return $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>false));
						return (!$customdatatypeoption instanceof TBGCustomDatatypeOption) ? $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' =>true, 'field' => array('id' => 0))) : $this->renderJSON(array('issue_id' => $issue->getID(), 'changed' => true, 'field' => array('value' => $customdatatypeoption->getID(), 'name' => $customdatatypeoption->getName())));
					}
					break;
			}
			
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field specified (%field)', array('%field' => $request['field']))));
		}

		/**
		 * Reverts an issue field back to the original value
		 * 
		 * @param TBGRequest $request
		 */
		public function runIssueRevertField(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderText('fail');
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderText('no issue');
			}
			
			$field = null;
			TBGContext::loadLibrary('common');
			switch ($request['field'])
			{
				case 'description':
					$issue->revertDescription();
					$issue->revertDescription_Syntax();
					$field = array('id' => (int) ($issue->getDescription() != ''), 'name' => $issue->getParsedDescription(array('issue' => $issue)), 'form_value' => $issue->getDescription());
					break;
				case 'reproduction_steps':
					$issue->revertReproduction_Steps();
					$issue->revertReproduction_Steps_Syntax();
					$field = array('id' => (int) ($issue->getReproductionSteps() != ''), 'name' => $issue->getParsedReproductionSteps(array('issue' => $issue)), 'form_value' => $issue->getReproductionSteps());
					break;
				case 'title':
					$issue->revertTitle();
					$field = array('id' => 1, 'name' => strip_tags($issue->getTitle()));
					break;
				case 'category':
					$issue->revertCategory();
					$field = ($issue->getCategory() instanceof TBGCategory) ? array('id' => $issue->getCategory()->getID(), 'name' => $issue->getCategory()->getName()) : array('id' => 0);
					break;
				case 'resolution':
					$issue->revertResolution();
					$field = ($issue->getResolution() instanceof TBGResolution) ? array('id' => $issue->getResolution()->getID(), 'name' => $issue->getResolution()->getName()) : array('id' => 0);
					break;
				case 'severity':
					$issue->revertSeverity();
					$field = ($issue->getSeverity() instanceof TBGSeverity) ? array('id' => $issue->getSeverity()->getID(), 'name' => $issue->getSeverity()->getName()) : array('id' => 0);
					break;
				case 'reproducability':
					$issue->revertReproducability();
					$field = ($issue->getReproducability() instanceof TBGReproducability) ? array('id' => $issue->getReproducability()->getID(), 'name' => $issue->getReproducability()->getName()) : array('id' => 0);
					break;
				case 'priority':
					$issue->revertPriority();
					$field = ($issue->getPriority() instanceof TBGPriority) ? array('id' => $issue->getPriority()->getID(), 'name' => $issue->getPriority()->getName()) : array('id' => 0);
					break;
				case 'percent_complete':
					$issue->revertPercentCompleted();
					$field = $issue->getPercentCompleted();
					break;
				case 'status':
					$issue->revertStatus();
					$field = ($issue->getStatus() instanceof TBGStatus) ? array('id' => $issue->getStatus()->getID(), 'name' => $issue->getStatus()->getName(), 'color' => $issue->getStatus()->getColor()) : array('id' => 0);
					break;
				case 'pain_bug_type':
					$issue->revertPainBugType();
					$field = ($issue->hasPainBugType()) ? array('id' => $issue->getPainBugType(), 'name' => $issue->getPainBugTypeLabel(), 'user_pain' => $issue->getUserPain()) : array('id' => 0, 'user_pain' => $issue->getUserPain());
					break;
				case 'pain_likelihood':
					$issue->revertPainLikelihood();
					$field = ($issue->hasPainLikelihood()) ? array('id' => $issue->getPainLikelihood(), 'name' => $issue->getPainLikelihoodLabel(), 'user_pain' => $issue->getUserPain()) : array('id' => 0, 'user_pain' => $issue->getUserPain());
					break;
				case 'pain_effect':
					$issue->revertPainEffect();
					$field = ($issue->hasPainEffect()) ? array('id' => $issue->getPainEffect(), 'name' => $issue->getPainEffectLabel(), 'user_pain' => $issue->getUserPain()) : array('id' => 0, 'user_pain' => $issue->getUserPain());
					break;
				case 'issuetype':
					$issue->revertIssuetype();
					$field = ($issue->getIssuetype() instanceof TBGIssuetype) ? array('id' => $issue->getIssuetype()->getID(), 'name' => $issue->getIssuetype()->getName(), 'src' => htmlspecialchars(TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getThemeName() . '/' . $issue->getIssuetype()->getIcon() . '_small.png')) : array('id' => 0);
					$visible_fields = ($issue->getIssuetype() instanceof TBGIssuetype) ? $issue->getProject()->getVisibleFieldsArray($issue->getIssuetype()->getID()) : array();
					return $this->renderJSON(array('ok' => true, 'issue_id' => $issue->getID(), 'field' => $field, 'visible_fields' => $visible_fields));
					break;
				case 'milestone':
					$issue->revertMilestone();
					$field = ($issue->getMilestone() instanceof TBGMilestone) ? array('id' => $issue->getMilestone()->getID(), 'name' => $issue->getMilestone()->getName()) : array('id' => 0);
					break;
				case 'estimated_time':
					$issue->revertEstimatedTime();
					return $this->renderJSON(array('ok' => true, 'issue_id' => $issue->getID(), 'field' => (($issue->hasEstimatedTime()) ? array('id' => 1, 'name' => TBGIssue::getFormattedTime($issue->getEstimatedTime())) : array('id' => 0)), 'values' => $issue->getEstimatedTime()));
					break;
				case 'spent_time':
					$issue->revertSpentTime();
					return $this->renderJSON(array('ok' => true, 'issue_id' => $issue->getID(), 'field' => (($issue->hasSpentTime()) ? array('id' => 1, 'name' => TBGIssue::getFormattedTime($issue->getSpentTime())) : array('id' => 0)), 'values' => $issue->getSpentTime()));
					break;
				case 'owned_by':
					$issue->revertOwner();
					return $this->renderJSON(array('changed' => $issue->isOwnerChanged(), 'field' => (($issue->isOwned()) ? array('id' => $issue->getOwner()->getID(), 'name' => (($issue->getOwner() instanceof TBGUser) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getOwner())))) : array('id' => 0))));
					break;
				case 'assigned_to':
					$issue->revertAssignee();
					return $this->renderJSON(array('changed' => $issue->isAssigneeChanged(), 'field' => (($issue->isAssigned()) ? array('id' => $issue->getAssignee()->getID(), 'name' => (($issue->getAssignee() instanceof TBGUser) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getAssignee())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getAssignee())))) : array('id' => 0))));
					break;
				case 'posted_by':
					$issue->revertPostedBy();
					return $this->renderJSON(array('changed' => $issue->isPostedByChanged(), 'field' => array('id' => $issue->getPostedByID(), 'name' => $this->getComponentHTML('main/userdropdown', array('user' => $issue->getPostedBy())))));
					break;
				default:
					if ($customdatatype = TBGCustomDatatype::getByKey($request['field']))
					{
						$key = $customdatatype->getKey();
						$revert_methodname = "revertCustomfield{$key}";
						$issue->$revert_methodname();
						
						if ($customdatatype->hasCustomOptions())
						{
							$field = ($issue->getCustomField($key) instanceof TBGCustomDatatypeOption) ? array('value' => $issue->getCustomField($key)->getID(), 'name' => $issue->getCustomField($key)->getName()) : array('id' => 0);
						}
						else
						{
							switch ($customdatatype->getType())
							{
								case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
								case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
									$field = ($issue->getCustomField($key) != '') ? array('value' => $key, 'name' => tbg_parse_text($issue->getCustomField($key))) : array('id' => 0);
									break;
								default:
									$field = ($issue->getCustomField($key) != '') ? array('value' => $key, 'name' => $issue->getCustomField($key)) : array('id' => 0);
									break;
							}							
						}
					}
					break;
			}
			
			if ($field !== null)
			{
				return $this->renderJSON(array('ok' => true, 'issue_id' => $issue->getID(), 'field' => $field));
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field specified (%field)', array('%field' => $request['field']))));
			}
		}
		
		/**
		 * Unlock the issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runUnlockIssue(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
					if (!$issue->canEditIssueDetails()) return $this->forward403();
					$issue->setLocked(false);
					$issue->save();
					TBGPermissionsTable::getTable()->deleteByPermissionTargetIDAndModule('canviewissue', $issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
			}

			return $this->renderJSON(array('message' => $this->getI18n()->__('Issue access policy updated')));
		}
		
		/**
		 * Unlock the issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runLockIssue(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
					if (!$issue->canEditIssueDetails())
					{
						$this->forward403($this->getI18n()->__("You don't have access to update the issue access policy"));
						return;
					}
					$issue->setLocked();
					$issue->save();
					TBGContext::setPermission('canviewissue', $issue->getID(), 'core', 0, 0, 0, false);
					TBGContext::setPermission('canviewissue', $issue->getID(), 'core', $this->getUser()->getID(), 0, 0, true);
					
					$al_users = $request->getParameter('access_list_users', array());
					$al_teams = $request->getParameter('access_list_teams', array());
					$i_al = $issue->getAccessList();
					foreach ($i_al as $k => $item)
					{
						if ($item['target'] instanceof TBGTeam)
						{
							$tid = $item['target']->getID();
							if (array_key_exists($tid, $al_teams))
							{
								unset($i_al[$k]);
							}
							else
							{
								TBGContext::removePermission('canviewissue', $issue->getID(), 'core', 0, 0, $tid);
							}
						}
						elseif ($item['target'] instanceof TBGUser)
						{
							$uid = $item['target']->getID();
							if (array_key_exists($uid, $al_users))
							{
								unset($i_al[$k]);
							}
							elseif ($uid != $this->getUser()->getID())
							{
								TBGContext::removePermission('canviewissue', $issue->getID(), 'core', $uid, 0, 0);
							}
						}
					}
					foreach ($al_users as $uid)
					{
						TBGContext::setPermission('canviewissue', $issue->getID(), 'core', $uid, 0, 0, true);
					}
					foreach ($al_teams as $tid)
					{
						TBGContext::setPermission('canviewissue', $issue->getID(), 'core', 0, 0, $tid, true);
					}
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
			}

			return $this->renderJSON(array('message' => $this->getI18n()->__('Issue access policy updated')));
		}
		
		/**
		 * Mark the issue as not blocking the next release
		 * 
		 * @param TBGRequest $request
		 */
		public function runMarkAsNotBlocker(TBGRequest $request)
		{
			$this->forward403unless($this->getUser()->hasPermission('caneditissue') || $this->getUser()->hasPermission('caneditissuebasic'));

			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
			}

			$issue->setBlocking(false);
			$issue->save();
			
			return $this->renderJSON('not blocking');
		}
		
		/**
		 * Mark the issue as blocking the next release
		 * 
		 * @param TBGRequest $request
		 */
		public function runMarkAsBlocker(TBGRequest $request)
		{
			$this->forward403unless($this->getUser()->hasPermission('caneditissue') || $this->getUser()->hasPermission('caneditissuebasic'));
						
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This issue does not exist')));
			}

			$issue->setBlocking();
			$issue->save();
			
			return $this->renderJSON('blocking');
		}
		
		/**
		 * Delete an issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runDeleteIssue(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
			}

			$this->forward403unless($issue->canDeleteIssue());
			$issue->deleteIssue();
			$issue->save();
			
			TBGContext::setMessage('issue_deleted', true);
			$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Find users and show selection links
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runFindIdentifiable(TBGRequest $request)
		{
			$this->forward403unless($request->isPost());
			$this->users = array();
			
			if ($find_identifiable_by = $request['find_identifiable_by'])
			{
				$this->users = TBGUsersTable::getTable()->getByDetails($find_identifiable_by, 10);
				if ($request['include_teams'])
				{
					$this->teams = TBGTeamsTable::getTable()->quickfind($find_identifiable_by);
				}
				else
				{
					$this->teams = array();
				}
			}
			$teamup_callback = $request['teamup_callback'];
			return $this->renderComponent('identifiableselectorresults', array('users' => $this->users, 'teams' => $this->teams, 'callback' => $request['callback'], 'teamup_callback' => $teamup_callback, 'team_callback' => $request['team_callback']));
		}
		
		/**
		 * Hides an infobox with a specific key
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runHideInfobox(TBGRequest $request)
		{
			TBGSettings::hideInfoBox($request['key']);
			return $this->renderJSON(array('hidden' => true));
		}

		public function runSetToggle(TBGRequest $request)
		{
			TBGSettings::setToggle($request['key'], $request['state']);
			return $this->renderJSON(array('state' => $request['state']));
		}

		public function runGetUploadStatus(TBGRequest $request)
		{
			$id = $request->getParameter('upload_id', 0);

			TBGLogging::log('requesting status for upload with id ' . $id);
			$status = TBGContext::getRequest()->getUploadStatus($id);
			TBGLogging::log('status was: ' . (int) $status['finished']. ', pct: '. (int) $status['percent']);
			if (array_key_exists('file_id', $status) && $request['mode'] == 'issue')
			{
				$file = TBGContext::factory()->TBGFile($status['file_id']);
				$status['content_uploader'] = $this->getComponentHTML('main/attachedfile', array('base_id' => 'uploaded_files', 'mode' => 'issue', 'issue_id' => $request['issue_id'], 'file' => $file));
				$status['content_inline'] = $this->getComponentHTML('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue_id' => $request['issue_id'], 'file' => $file));
				$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
				$status['attachmentcount'] = count($issue->getFiles()) + count($issue->getLinks());
			}
			elseif (array_key_exists('file_id', $status) && $request['mode'] == 'article')
			{
				$file = TBGContext::factory()->TBGFile($status['file_id']);
				$status['content_uploader'] = $this->getComponentHTML('main/attachedfile', array('base_id' => 'article_'.mb_strtolower(urldecode($request['article_name'])).'_files', 'mode' => 'article', 'article_name' => $request['article_name'], 'file' => $file));
				$status['content_inline'] = $this->getComponentHTML('main/attachedfile', array('base_id' => 'article_'.mb_strtolower(urldecode($request['article_name'])).'_files', 'mode' => 'article', 'article_name' => $request['article_name'], 'file' => $file));
				$article = TBGWikiArticle::getByName($request['article_name']);
				$status['attachmentcount'] = count($article->getFiles());
			}
			
			return $this->renderJSON($status);
		}

		public function runUpdateAttachments(TBGRequest $request)
		{
			switch ($request['target']) 
			{
				case 'issue':
					$target = TBGIssuesTable::getTable()->selectById($request['target_id']);
					$base_id = 'viewissue_files';
					$container_id = 'viewissue_uploaded_files';
					$target_identifier = 'issue_id';
					$target_id = $target->getID();
					break;
				case 'article':
					$target = TBGArticlesTable::getTable()->selectById($request['target_id']);
					$base_id = 'article_'.mb_strtolower(urldecode($request['article_name'])).'_files';
					$container_id = 'article_'.$target->getID().'_files';
					$target_identifier = 'article_name';
					$target_id = $request['article_name'];
					break;
			}
			$saved_file_ids = $request['files'];
			$files = array();
			foreach ($request['file_description'] as $file_id => $description)
			{
				$file = TBGFilesTable::getTable()->selectById($file_id);
				$file->setDescription($description);
				$file->save();
				if (in_array($file_id, $saved_file_ids))
				{
					$target->attachFile($file);
				}
				else
				{
					$target->detachFile($file);
				}
				$files[] = $this->getComponentHTML('main/attachedfile', array('base_id' => $base_id, 'mode' => $request['target'], $request['target'] => $target, $target_identifier => $target_id, 'file' => $file));
			}
			$attachmentcount = ($request['target'] == 'issue') ? $target->countFiles() + $target->countLinks() : $target->countFiles();
			
			return $this->renderJSON(array('attached' => 'ok', 'container_id' => $container_id, 'files' => $files, 'attachmentcount' => $attachmentcount));
		}

		public function runUploadFile(TBGRequest $request)
		{
			if (!isset($_SESSION['upload_files']))
			{
				$_SESSION['upload_files'] = array();
			}
			
			$files = array();
			$files_dir = TBGSettings::getUploadsLocalpath();
			
			foreach ($request->getUploadedFiles() as $key => $file)
			{
				$new_filename = TBGContext::getUser()->getID() . '_' . NOW . '_' . basename($file['name']);
				if (TBGSettings::getUploadStorage() == 'files')
				{
					$filename = $files_dir.$new_filename;
				}
				else
				{
					$filename = $file['tmp_name'];
				}
				TBGLogging::log('Moving uploaded file to '.$filename);
				if (TBGSettings::getUploadStorage() == 'files' && !move_uploaded_file($file['tmp_name'], $filename))
				{
					TBGLogging::log('Moving uploaded file failed!');
					throw new Exception(TBGContext::getI18n()->__('An error occured when saving the file'));
				}
				else
				{
					TBGLogging::log('Upload complete and ok, storing upload status and returning filename '.$new_filename);
					$content_type = TBGFile::getMimeType($filename);
					$file_object = new TBGFile();
					$file_object->setRealFilename($new_filename);
					$file_object->setOriginalFilename(basename($file['name']));
					$file_object->setContentType($content_type);
					$file_object->setDescription('');
					$file_object->setUploadedBy(TBGContext::getUser());
					if (TBGSettings::getUploadStorage() == 'database')
					{
						$file_object->setContent(file_get_contents($filename));
					}
					$file_object->save();
					return $this->renderJSON(array('file_id' => $file_object->getID()));
				}
			}
			
			return $this->renderJSON(array('error' => $this->getI18n()->__('An error occurred when uploading the file')));
		}

		public function runUpload(TBGRequest $request)
		{
			$apc_exists = TBGRequest::CanGetUploadStatus();
			if ($apc_exists && !$request['APC_UPLOAD_PROGRESS'])
			{
				$request->setParameter('APC_UPLOAD_PROGRESS', $request['upload_id']);
			}
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);

			$canupload = false;

			if ($request['mode'] == 'issue')
			{
				$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
				$canupload = (bool) ($issue instanceof TBGIssue && $issue->hasAccess() && $issue->canAttachFiles());
			}
			elseif ($request['mode'] == 'article')
			{
				$article = TBGWikiArticle::getByName($request['article_name']);
				$canupload = (bool) ($article instanceof TBGWikiArticle && $article->canEdit());
			}
			else
			{
				$event = TBGEvent::createNew('core', 'upload', $request['mode']);
				$event->triggerUntilProcessed();

				$canupload = ($event->isProcessed()) ? (bool) $event->getReturnValue() : true;
			}
			
			if ($canupload)
			{
				try
				{
					$file = TBGContext::getRequest()->handleUpload('uploader_file');
					if ($file instanceof TBGFile)
					{
						switch ($request['mode'])
						{
							case 'issue':
								if (!$issue instanceof TBGIssue) break;
								$issue->attachFile($file, $request->getRawParameter('comment'), $request['uploader_file_description']);
								$issue->save();
								break;
							case 'article':
								if (!$article instanceof TBGWikiArticle) break;
								$article->attachFile($file);
								break;
						}
						if ($apc_exists)
							return $this->renderText('ok');
					}
					$this->error = TBGContext::getI18n()->__('An unhandled error occured with the upload');
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					$this->error = $e->getMessage();
				}
			}
			else
			{
//				$this->getResponse()->setHttpStatus(401);
				$this->error = TBGContext::getI18n()->__('You are not allowed to attach files here');
			}
			if (!$apc_exists)
			{
				switch ($request['mode'])
				{
					case 'issue':
						if (!$issue instanceof TBGIssue) break;
						$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
						break;
					case 'article':
						if (!$article instanceof TBGWikiArticle) break;
						$this->forward(TBGContext::getRouting()->generate('publish_article_attachments', array('article_name' => $article->getName())));
						break;
				}
			}
			TBGLogging::log('marking upload ' . $request['APC_UPLOAD_PROGRESS'] . ' as completed with error ' . $this->error);
			$request->markUploadAsFinishedWithError($request['APC_UPLOAD_PROGRESS'], $this->error);
			return $this->renderText($request['APC_UPLOAD_PROGRESS'].': '.$this->error);
		}

		public function runDetachFile(TBGrequest $request)
		{
			try
			{
				switch ($request['mode'])
				{
					case 'issue':
						$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
						if ($issue->canRemoveAttachments() && (int) $request->getParameter('file_id', 0))
						{
							\b2db\Core::getTable('TBGIssueFilesTable')->removeByIssueIDAndFileID($issue->getID(), (int) $request['file_id']);
							return $this->renderJSON(array('file_id' => $request['file_id'], 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'message' => TBGContext::getI18n()->__('The attachment has been removed')));
						}
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You can not remove items from this issue')));
						break;
					case 'article':
						$article = TBGWikiArticle::getByName($request['article_name']);
						if ($article instanceof TBGWikiArticle && $article->canEdit() && (int) $request->getParameter('file_id', 0))
						{
							$article->removeFile(TBGContext::factory()->TBGFile((int) $request['file_id']));
							return $this->renderJSON(array('file_id' => $request['file_id'], 'attachmentcount' => count($article->getFiles()), 'message' => TBGContext::getI18n()->__('The attachment has been removed')));
						}
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You can not remove items from this issue')));
						break;
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Invalid mode')));
		}

		public function runGetFile(TBGRequest $request)
		{
			$file = new TBGFile((int) $request['id']);
			if ($file instanceof TBGFile)
			{
				if ($file->hasAccess())
				{
					$this->getResponse()->cleanBuffer();
					$this->getResponse()->clearHeaders();
					$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
					$this->getResponse()->addHeader('Content-disposition: '.(($request['mode'] == 'download') ? 'attachment' : 'inline').'; filename="'.$file->getOriginalFilename().'"');
					$this->getResponse()->setContentType($file->getContentType());
					$this->getResponse()->renderHeaders();
					if (TBGSettings::getUploadStorage() == 'files')
					{
						fpassthru(fopen(TBGSettings::getUploadsLocalpath().$file->getRealFilename(), 'r'));
						exit();
					}
					else
					{
						echo $file->getContent();
						exit();
					}
					return true;
				}
			}
			$this->return404(TBGContext::getI18n()->__('This file does not exist'));
		}

		public function runAttachLinkToIssue(TBGRequest $request)
		{
			$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
			if ($issue instanceof TBGIssue && $issue->canAttachLinks())
			{
				if ($request['link_url'] != '')
				{
					$link_id = $issue->attachLink($request['link_url'], $request['description']);
					return $this->renderJSON(array('message' => TBGContext::getI18n()->__('Link attached!'), 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'content' => $this->getTemplateHTML('main/attachedlink', array('issue' => $issue, 'link_id' => $link_id, 'link' => array('description' => $request['description'], 'url' => $request['link_url'])))));
				}
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You have to provide a link URL, otherwise we have nowhere to link to!')));
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You can not attach links to this issue')));
		}

		public function runRemoveLinkFromIssue(TBGRequest $request)
		{
			$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
			if ($issue instanceof TBGIssue && $issue->canRemoveAttachments())
			{
				if ($request['link_id'] != 0)
				{
					$issue->removeLink($request['link_id']);
					return $this->renderJSON(array('attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'message' => TBGContext::getI18n()->__('Link removed!')));
				}
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You have to provide a valid link id')));
			}
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You can not remove items from this issue')));
		}

		public function runAttachLink(TBGRequest $request)
		{
			$link_id = TBGLinksTable::getTable()->addLink($request['target_type'], $request['target_id'], $request['link_url'], $request->getRawParameter('description'));
			return $this->renderJSON(array('message' => TBGContext::getI18n()->__('Link added!'), 'content' => $this->getTemplateHTML('main/menulink', array('link_id' => $link_id, 'link' => array('target_type' => $request['target_type'], 'target_id' => $request['target_id'], 'description' => $request->getRawParameter('description'), 'url' => $request['link_url'])))));
		}

		public function runRemoveLink(TBGRequest $request)
		{
			if (!$this->getUser()->canEditMainMenu())
			{
				$this->getResponse()->setHttpStatus(403);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You do not have access to removing links')));
			}

			if (!$request['link_id'])
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You have to provide a valid link id')));
			}

			TBGLinksTable::getTable()->removeByTargetTypeTargetIDandLinkID($request['target_type'], $request['target_id'], $request['link_id']);
			TBGContext::clearMenuLinkCache();
			return $this->renderJSON(array('message' => TBGContext::getI18n()->__('Link removed!')));
		}

		public function runSaveMenuOrder(TBGRequest $request)
		{
			$target_type = $request['target_type'];
			$target_id = $request['target_id'];
			TBGLinksTable::getTable()->saveLinkOrder($request[$target_type.'_'.$target_id.'_links']);
			if ($target_type == 'main_menu')
			{
				TBGCache::delete(TBGCache::KEY_MAIN_MENU_LINKS);
				TBGCache::fileDelete(TBGCache::KEY_MAIN_MENU_LINKS);
			}
			return $this->renderJSON('ok');
		}
		
		public function runDeleteComment(TBGRequest $request)
		{
			$comment = TBGContext::factory()->TBGComment($request['comment_id']);
			if ($comment instanceof TBGcomment)
			{							
				if (!$comment->canUserDeleteComment())
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
				}
				else
				{
					unset($comment);
					$comment = TBGContext::factory()->TBGComment((int) $request['comment_id']);
					$comment->delete();
					return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Comment deleted!')));
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Comment ID is invalid')));
			}
		}
		
		public function runUpdateComment(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			$comment = TBGContext::factory()->TBGComment($request['comment_id']);
			if ($comment instanceof TBGcomment)
			{							
				if (!$comment->canUserEditComment())
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
				}
				else
				{
					if ($request['comment_body'] == '')
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('The comment must have some content')));
					}
					
					$comment->setContent($request->getRawParameter('comment_body'));
					
					if ($request['comment_title'] == '')
					{
						$comment->setTitle(TBGContext::getI18n()->__('Untitled comment'));
					}
					else
					{
						$comment->setTitle($request['comment_title']);
					}
					
					$comment->setIsPublic($request['comment_visibility']);
					$comment->setSyntax((int) $request['comment_body_syntax']);
					$comment->setUpdatedBy($this->getUser()->getID());
					$comment->save();

					TBGContext::loadLibrary('common');
					$body = $comment->getParsedContent();
					
					return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Comment edited!'), 'comment_title' => $comment->getTitle(), 'comment_body' => $body));
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Comment ID is invalid')));
			}
		}

		public function listenIssueSaveAddComment(TBGEvent $event)
		{
			$this->comment_lines = $event->getParameter('comment_lines');
			$this->comment = $event->getParameter('comment');
		}

		public function listenViewIssuePostError(TBGEvent $event)
		{
			if (TBGContext::hasMessage('comment_error'))
			{
				$this->comment_error = true;
				$this->error = TBGContext::getMessageAndClear('comment_error');
				$this->comment_error_title = TBGContext::getMessageAndClear('comment_error_title');
				$this->comment_error_body = TBGContext::getMessageAndClear('comment_error_body');
			}
		}
		
		public function runAddComment(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$comment_applies_type = $request['comment_applies_type'];
			try
			{
				if (!$this->getUser()->canPostComments())
				{
					throw new Exception($i18n->__('You are not allowed to do this'));
				}
				if (!trim($request['comment_body']))
				{
					throw new Exception($i18n->__('The comment must have some content'));
				}

				$comment = new TBGComment();
				$comment->setTitle('');
				$comment->setContent($request->getParameter('comment_body', null, false));
				$comment->setPostedBy($this->getUser()->getID());
				$comment->setTargetID($request['comment_applies_id']);
				$comment->setTargetType($request['comment_applies_type']);
				$comment->setReplyToComment($request['reply_to_comment_id']);
				$comment->setModuleName($request['comment_module']);
				$comment->setIsPublic((bool) $request['comment_visibility']);
				$comment->setSyntax($request['comment_body_syntax']);
				$comment->save();

				if ($comment_applies_type == TBGComment::TYPE_ISSUE)
				{
					$issue = TBGIssuesTable::getTable()->selectById((int) $request['comment_applies_id']);
					if (!$request->isAjaxCall() || $request['comment_save_changes'])
					{
						$issue->setSaveComment($comment);
						$issue->save();
					}
					else
					{
						TBGEvent::createNew('core', 'TBGComment::createNew', $comment, compact('issue'))->trigger();
					}
				}
				elseif ($comment_applies_type == TBGComment::TYPE_ARTICLE)
				{
					$article = TBGArticlesTable::getTable()->selectById((int) $request['comment_applies_id']);
					TBGEvent::createNew('core', 'TBGComment::createNew', $comment, compact('article'))->trigger();
				}

				switch ($comment_applies_type)
				{
					case TBGComment::TYPE_ISSUE:
						$comment_html = $this->getTemplateHTML('main/comment', array('comment' => $comment, 'issue' => TBGContext::factory()->TBGIssue($request['comment_applies_id'])));
						break;
					case TBGComment::TYPE_ARTICLE:
						$comment_html = $this->getTemplateHTML('main/comment', array('comment' => $comment));
						break;
					default:
						$comment_html = 'OH NO!';
				}

			}
			catch (Exception $e)
			{
				if ($request->isAjaxCall())
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $e->getMessage()));
				}
				else
				{
					TBGContext::setMessage('comment_error', $e->getMessage());
					TBGContext::setMessage('comment_error_body', $request['comment_body']);
					TBGContext::setMessage('comment_error_title', $request['comment_title']);
					TBGContext::setMessage('comment_error_visibility', $request['comment_visibility']);
				}
			}
			if ($request->isAjaxCall())
				return $this->renderJSON(array('title' => $i18n->__('Comment added!'), 'comment_data' => $comment_html, 'continue_url' => $request['forward_url'], 'commentcount' => TBGComment::countComments($request['comment_applies_id'], $request['comment_applies_type']/*, $request['comment_module']*/)));
			if (isset($comment) && $comment instanceof TBGComment)
				$this->forward($request['forward_url'] . "#comment_{$request['comment_applies_type']}_{$request['comment_applies_id']}_{$comment->getID()}");
			else
				$this->forward($request['forward_url']);
		}

		public function runListProjects(TBGRequest $request)
		{
			$projects = TBGProject::getAll();

			$return_array = array();
			foreach ($projects as $project)
			{
				$return_array[$project->getKey()] = $project->getName();
			}

			$this->projects = $return_array;
		}

		public function runListIssuetypes(TBGRequest $request)
		{
			$issuetypes = TBGIssuetype::getAll();

			$return_array = array();
			foreach ($issuetypes as $issuetype)
			{
				$return_array[$issuetype->getKey()] = $issuetype->getName();
			}

			$this->issuetypes = $return_array;
		}

		public function runListFieldvalues(TBGRequest $request)
		{
			$field_key = $request['field_key'];
			$return_array = array('description' => null, 'type' => null, 'choices' => null);
			if ($field_key == 'title' || in_array($field_key, TBGDatatypeBase::getAvailableFields(true)))
			{
				switch ($field_key)
				{
					case 'title':
						$return_array['description'] = TBGContext::getI18n()->__('Single line text input without formatting');
						$return_array['type'] = 'single_line_input';
						break;
					case 'description':
					case 'reproduction_steps':
						$return_array['description'] = TBGContext::getI18n()->__('Text input with wiki formatting capabilities');
						$return_array['type'] = 'wiki_input';
						break;
					case 'status':
					case 'resolution':
					case 'reproducability':
					case 'priority':
					case 'severity':
					case 'category':
						$return_array['description'] = TBGContext::getI18n()->__('Choose one of the available values');
						$return_array['type'] = 'choice';

						$classname = "TBG".ucfirst($field_key);
						$choices = $classname::getAll();
						foreach ($choices as $choice_key => $choice)
						{
							$return_array['choices'][$choice_key] = $choice->getName();
						}
						break;
					case 'percent_complete':
						$return_array['description'] = TBGContext::getI18n()->__('Value of percentage completed');
						$return_array['type'] = 'choice';
						$return_array['choices'][] = "1-100%";
						break;
					case 'owner':
					case 'assignee':
						$return_array['description'] = TBGContext::getI18n()->__('Select an existing user or <none>');
						$return_array['type'] = 'select_user';
						break;
					case 'estimated_time':
					case 'spent_time':
						$return_array['description'] = TBGContext::getI18n()->__('Enter time, such as points, hours, minutes, etc or <none>');
						$return_array['type'] = 'time';
						break;
					case 'milestone':
						$return_array['description'] = TBGContext::getI18n()->__('Select from available project milestones');
						$return_array['type'] = 'choice';
						if ($this->selected_project instanceof TBGProject)
						{
							$milestones = $this->selected_project->getAvailableMilestones();
							foreach ($milestones as $milestone)
							{
								$return_array['choices'][$milestone->getID()] = $milestone->getName();
							}
						}
						break;
				}
			}
			else
			{

			}

			$this->field_info = $return_array;
		}

		public function runGetBackdropPartial(TBGRequest $request)
		{
			if (!$request->isAjaxCall())
			{
				return $this->return404($this->getI18n()->__('You need to enable javascript for The Bug Genie to work properly'));
			}
			try
			{
				$template_name = null;
				if ($request->hasParameter('issue_id'))
				{
					$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
					$options = array('issue' => $issue);
				}
				else
				{
					$options = array();
				}
				switch ($request['key'])
				{
					case 'usercard':
						$template_name = 'main/usercard';
						if ($user_id = $request['user_id'])
						{
							$user = TBGContext::factory()->TBGUser($user_id);
							$options['user'] = $user;
						}
						break;
					case 'login':
						$template_name = 'main/loginpopup';
						$options = $request->getParameters();
						$options['content'] = $this->getComponentHTML('login', array('section' => $request->getParameter('section', 'login')));
						$options['mandatory'] = false;
						break;
					case 'uploader':
						$template_name = 'main/uploader';
						$options = $request->getParameters();
						$options['uploader'] = ($request['uploader'] == 'dynamic') ? 'dynamic' : 'standard';
						break;
					case 'openid':
						$template_name = 'main/openid';
						break;
					case 'notifications':
						$template_name = 'main/notifications';
						break;
					case 'workflow_transition':
						$transition = TBGContext::factory()->TBGWorkflowTransition($request['transition_id']);
						$template_name = $transition->getTemplate();
						$options['transition'] = $transition;
						$options['issues'] = array();
						foreach ($request['issue_ids'] as $issue_id)
						{
							$options['issues'][$issue_id] = new TBGIssue($issue_id);
						}
						$options['project'] = $this->selected_project;
						break;
					case 'reportissue':
						$template_name = 'main/reportissuecontainer';
						$this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);
						$options['selected_project'] = $this->selected_project;
						$options['selected_issuetype'] = $this->selected_issuetype;
						if ($request->hasParameter('milestone_id'))
						{
							try
							{
								$options['selected_milestone'] = TBGContext::factory()->TBGMilestone((int) $request['milestone_id']);
							}
							catch (Exception $e) {}
						}
						if ($request->hasParameter('parent_issue_id'))
						{
							try
							{
								$options['parent_issue'] = TBGContext::factory()->TBGIssue((int) $request['parent_issue_id']);
							}
							catch (Exception $e) {}
						}
						if ($request->hasParameter('build_id'))
						{
							try
							{
								$options['selected_build'] = TBGContext::factory()->TBGBuild((int) $request['build_id']);
							}
							catch (Exception $e) {}
						}
						$options['issuetypes'] = $this->issuetypes;
						$options['errors'] = array();
						break;
					case 'move_issue':
						$template_name = 'main/moveissue';
						$options['multi'] = (bool) $request->getParameter('multi', false);
						break;
					case 'issue_permissions':
						$template_name = 'main/issuepermissions';
						break;
					case 'issue_subscribers':
						$template_name = 'main/issuesubscribers';
						break;
					case 'issue_spenttimes':
						$template_name = 'main/issuespenttimes';
						$options['initial_view'] = $request->getParameter('initial_view', 'list');
						break;
					case 'issue_spenttime':
						$template_name = 'main/issuespenttime';
						$options['entry_id'] = $request->getParameter('entry_id');
						break;
					case 'relate_issue':
						$template_name = 'main/relateissue';
						break;
					case 'milestone':
						$template_name = 'project/milestone';
						$options['project'] = TBGContext::factory()->TBGProject($request['project_id']);
						if ($request->hasParameter('milestone_id'))
							$options['milestone'] = TBGContext::factory()->TBGMilestone($request['milestone_id']);
						break;
					case 'project_build':
						$template_name = 'project/build';
						$options['project'] = TBGContext::factory()->TBGProject($request['project_id']);
						if ($request->hasParameter('build_id'))
							$options['build'] = TBGContext::factory()->TBGBuild($request['build_id']);
						break;
					case 'project_icons':
						$template_name = 'project/projecticons';
						$options['project'] = TBGContext::factory()->TBGProject($request['project_id']);
						break;
					case 'project_workflow':
						$template_name = 'project/projectworkflow';
						$options['project'] = TBGContext::factory()->TBGProject($request['project_id']);
						break;
					case 'permissions':
						$options['key'] = $request['permission_key'];
						if ($details = TBGContext::getPermissionDetails($options['key']))
						{
							$template_name = 'configuration/permissionspopup';
							$options['mode'] = $request['mode'];
							$options['module'] = $request['target_module'];
							$options['target_id'] = $request['target_id'];
							$options['item_name'] = $details['description'];
							$options['access_level'] = $request['access_level'];
						}
						break;
					case 'issuefield_permissions':
						$options['item_key'] = $request['item_key'];
						if ($details = TBGContext::getPermissionDetails($options['item_key']))
						{
							$template_name = 'configuration/issuefieldpermissions';
							$options['item_name'] = $details['description'];
							$options['item_id'] = $request['item_id'];
							$options['access_level'] = $request['access_level'];
						}
						else
						{
							die('fu');
						}
						break;
					case 'site_icons':
						$template_name = 'configuration/siteicons';
						break;
					case 'project_config':
						$template_name = 'project/projectconfig_container';
						$project = TBGContext::factory()->TBGProject($request['project_id']);
						$options['project'] = $project;
						$options['section'] = $request->getParameter('section', 'info');
						if ($request->hasParameter('edition_id'))
						{
							$edition = TBGContext::factory()->TBGEdition($request['edition_id']);
							$options['edition'] = $edition;
							$options['selected_section'] = $request->getParameter('section', 'general');
						}
						break;
					case 'issue_add_item':
						$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
						$template_name = 'main/issueadditem';
						break;
					case 'client_users':
						$options['client'] = TBGContext::factory()->TBGClient($request['client_id']);
						$template_name = 'main/clientusers';
						break;
					case 'dashboard_config':
						$template_name = 'main/dashboardconfig';
						$options['tid'] = $request['tid'];
						$options['target_type'] = $request['target_type'];
						$options['previous_route'] = $request['previous_route'];
						$options['mandatory'] = true;
						break;
					case 'archived_projects':
						$template_name = 'main/archivedprojects';
						$options['mandatory'] = true;
						break;
					case 'team_archived_projects':
						$template_name = 'main/archivedprojects';
						$options['target'] = 'team';
						$options['id'] = $request['tid'];
						$options['mandatory'] = true;
						break;
					case 'client_archived_projects':
						$template_name = 'main/archivedprojects';
						$options['target'] = 'client';
						$options['id'] = $request['cid'];
						$options['mandatory'] = true;
						break;
					case 'project_archived_projects':
						$template_name = 'main/archivedprojects';
						$options['target'] = 'project';
						$options['id'] = $request['pid'];
						$options['mandatory'] = true;
						break;
					case 'bulk_workflow':
						$template_name = 'search/bulkworkflow';
						$options['issue_ids'] = $request['issue_ids'];
						break;
					case 'confirm_username':
						$template_name = 'main/confirmusername';
						$options['username'] = $request['username'];
						break;
					case 'userscopes':
						if (!TBGContext::getScope()->isDefault()) throw new Exception($this->getI18n()->__('This is not allowed outside the default scope'));

						$template_name = 'configuration/userscopes';
						$options['user'] = new TBGUser((int) $request['user_id']);
						break;
					default:
						$event = new TBGEvent('core', 'get_backdrop_partial', $request['key']);
						$event->triggerUntilProcessed();
						$options = $event->getReturnList();
						$template_name = $event->getReturnValue();
				}
				if ($template_name !== null)
				{
					return $this->renderJSON(array('content' => $this->getComponentHTML($template_name, $options)));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->cleanBuffer();
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('An error occured: %error_message', array('%error_message' => $e->getMessage()))));
			}
			$this->getResponse()->cleanBuffer();
			$this->getResponse()->setHttpStatus(400);
			$error = (TBGContext::isDebugMode()) ? TBGContext::getI18n()->__('Invalid template or parameter') : $this->getI18n()->__('Could not show the requested popup');
			return $this->renderJSON(array('error' => $error));
		}

		public function runFindIssue(TBGRequest $request)
		{
			$status = 200;
			$message = null;
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$status = 400;
					$message = TBGContext::getI18n()->__('Could not find this issue');
				}
			}
			elseif ($request->hasParameter('issue_id'))
			{
				$status = 400;
				$message = TBGContext::getI18n()->__('Please provide an issue number');
			}

			$searchfor = $request['searchfor'];

			if (mb_strlen(trim($searchfor)) < 3 && !is_numeric($searchfor) && mb_substr($searchfor, 0, 1) != '#')
			{
//				$status = 400;
//				$message = TBGContext::getI18n()->__('Please enter something to search for (3 characters or more) %searchfor', array('searchfor' => $searchfor));
				$issues = array();
				$count = 0;
			}
			else
			{
				$this->getResponse()->setHttpStatus($status);
				if ($status == 400)
				{
					return $this->renderJSON(array('error' => $message));
				}

				list ($issues, $count) = TBGIssue::findIssuesByText($searchfor, $this->selected_project);
			}
			$options = array('project' => $this->selected_project, 'issues' => $issues, 'count' => $count);
			if (isset($issue)) $options['issue'] = $issue;
			
			return $this->renderJSON(array('content' => $this->getComponentHTML('main/find'.$request['type'].'issues', $options)));
		}
		
		public function runFindDuplicateIssue(TBGRequest $request)
		{
			$status = 200;
			$message = null;
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$status = 400;
					$message = TBGContext::getI18n()->__('Could not find this issue');
				}
			}
			else
			{
				$status = 400;
				$message = TBGContext::getI18n()->__('Please provide an issue number');
			}

			$searchfor = $request['searchfor'];

			if (mb_strlen(trim($searchfor)) < 3 && !is_numeric($searchfor))
			{
				$status = 400;
				$message = TBGContext::getI18n()->__('Please enter something to search for (3 characters or more) %searchfor', array('searchfor' => $searchfor));
			}

			$this->getResponse()->setHttpStatus($status);
			if ($status == 400)
			{
				return $this->renderJSON(array('error' => $message));
			}

			list ($issues, $count) = TBGIssue::findIssuesByText($searchfor, $this->selected_project);
			return $this->renderJSON(array('content' => $this->getComponentHTML('main/findduplicateissues', array('issue' => $issue, 'issues' => $issues, 'count' => $count))));
		}

		public function runRemoveRelatedIssue(TBGRequest $request)
		{
			try
			{
				try
				{
					$issue_id = (int) $request['issue_id'];
					$related_issue_id = (int) $request['related_issue_id'];
					$issue = null;
					$related_issue = null;
					if ($issue_id && $related_issue_id)
					{
						$issue = TBGContext::factory()->TBGIssue($issue_id);
						$related_issue = TBGContext::factory()->TBGIssue($related_issue_id);
					}
					if (!$issue instanceof TBGIssue || !$related_issue instanceof TBGIssue)
					{
						throw new Exception('');
					}
					$issue->removeDependantIssue($related_issue->getID());
				}
				catch (Exception $e)
				{
					throw new Exception($this->getI18n()->__('Please provide a valid issue number and a valid related issue number'));
				}
				return $this->renderJSON(array('message' => $this->getI18n()->__('The issues are no longer related')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
			
		}

		public function runRelateIssues(TBGRequest $request)
		{
			$status = 200;
			$message = null;
			
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e)
				{
					$status = 400;
					$message = TBGContext::getI18n()->__('Could not find this issue');
				}
			}
			else
			{
				$status = 400;
				$message = TBGContext::getI18n()->__('Please provide an issue number');
			}

			if (!$issue->canAddRelatedIssues())
			{
				$status = 400;
				$message = TBGContext::getI18n()->__('You are not allowed to relate issues');
			}
			
			$this->getResponse()->setHttpStatus($status);
			if ($status == 400)
			{
				return $this->renderJSON(array('error' => $message));
			}

			$related_issues = $request->getParameter('relate_issues', array());

			$cc = 0;
			$message = TBGContext::getI18n()->__('Unknown error');
			if (count($related_issues))
			{
				$mode = $request['relate_action'];
				$content = '';
				foreach ($related_issues as $issue_id)
				{
					try
					{
						$related_issue = TBGContext::factory()->TBGIssue((int) $issue_id);
						if ($mode == 'relate_children')
						{
							$issue->addChildIssue($related_issue);
						}
						else
						{
							$issue->addParentIssue($related_issue);
						}
						$cc++;
						$content .= $this->getTemplateHTML('main/relatedissue', array('issue' => $related_issue, 'related_issue' => $issue));
					}
					catch (Exception $e)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('An error occured when relating issues: %error', array('%error' => $e->getMessage()))));
					}
				}
			}
			else
			{
				$message = TBGContext::getI18n()->__('Please select at least one issue');
			}

			if ($cc > 0)
			{
				return $this->renderJSON(array('content' => $content, 'message' => TBGContext::getI18n()->__('The related issue was added')));
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('An error occured when relating issues: %error', array('%error' => $message))));
			}
		}

		public function runRelatedIssues(TBGRequest $request)
		{
			if ($issue_id = $request['issue_id'])
			{
				try
				{
					$this->issue = TBGContext::factory()->TBGIssue($issue_id);
				}
				catch (Exception $e) { }
			}
		}

		public function runVoteForIssue(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
			$vote_direction = $request['vote'];
			if ($issue instanceof TBGIssue && !$issue->hasUserVoted($this->getUser()->getID(), ($vote_direction == 'up')))
			{
				$issue->vote(($vote_direction == 'up'));
				return $this->renderJSON(array('content' => $issue->getVotes(), 'message' => $i18n->__('Vote added')));
			}

		}

		public function runToggleFriend(TBGRequest $request)
		{
			try
			{
				$friend_user = TBGContext::factory()->TBGUser($request['user_id']);
				$mode = $request['mode'];
				if ($mode == 'add')
				{
					if ($friend_user instanceof TBGUser && $friend_user->isDeleted())
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This user has been deleted')));
					}
					$this->getUser()->addFriend($friend_user);
				}
				else
				{
					$this->getUser()->removeFriend($friend_user);
				}
				return $this->renderJSON(array('mode' => $mode));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Could not add or remove friend')));
			}
		}

		public function runSetState(TBGRequest $request)
		{
			try
			{
				$state = TBGContext::factory()->TBGUserstate($request['state_id']);
				$this->getUser()->setState($state);
				$this->getUser()->save();
				return $this->renderJSON(array('userstate' => $this->getI18n()->__($state->getName())));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $this->getI18n()->__('An error occured while trying to update your status')));
			}
		}
		
		public function runToggleAffectedConfirmed(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			try
			{
				$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
				$itemtype = $request['affected_type'];

				if (!(($itemtype == 'build' && $issue->canEditAffectedBuilds()) || ($itemtype == 'component' && $issue->canEditAffectedComponents()) || ($itemtype == 'edition' && $issue->canEditAffectedEditions())))
				{
					throw new Exception($this->getI18n()->__('You are not allowed to do this'));
				}

				$affected_id = $request['affected_id'];
				
				switch ($itemtype)
				{
					case 'edition':
						if (!$issue->getProject()->isEditionsEnabled())
						{
							throw new Exception($this->getI18n()->__('Editions are disabled'));
						}
						
						$editions = $issue->getEditions();
						if (!array_key_exists($affected_id, $editions))
						{
							throw new Exception($this->getI18n()->__('This edition is not affected by this issue'));
						}
						$edition = $editions[$affected_id];
						
						if ($edition['confirmed'] == true)
						{
							$issue->confirmAffectedEdition($edition['edition'], false);
							
							$message = TBGContext::getI18n()->__('Edition <b>%edition</b> is now unconfirmed for this issue', array('%edition' => $edition['edition']->getName()));
							$alt = TBGContext::getI18n()->__('No');
							$src = image_url('action_cancel_small.png');
						}
						else
						{
							$issue->confirmAffectedEdition($edition['edition']);
							
							$message = TBGContext::getI18n()->__('Edition <b>%edition</b> is now confirmed for this issue', array('%edition' => $edition['edition']->getName()));
							$alt = TBGContext::getI18n()->__('Yes');
							$src = image_url('action_ok_small.png');
						}
						
						break;
					case 'component':
						if (!$issue->getProject()->isComponentsEnabled())
						{
							throw new Exception($this->getI18n()->__('Components are disabled'));
						}
						
						$components = $issue->getComponents();
						if (!array_key_exists($affected_id, $components))
						{
							throw new Exception($this->getI18n()->__('This component is not affected by this issue'));
						}
						$component = $components[$affected_id];
						
						if ($component['confirmed'] == true)
						{
							$issue->confirmAffectedComponent($component['component'], false);
							
							$message = TBGContext::getI18n()->__('Component <b>%component</b> is now unconfirmed for this issue', array('%component' => $component['component']->getName()));
							$alt = TBGContext::getI18n()->__('No');
							$src = image_url('action_cancel_small.png');
						}
						else
						{
							$issue->confirmAffectedComponent($component['component']);
							
							$message = TBGContext::getI18n()->__('Component <b>%component</b> is now confirmed for this issue', array('%component' => $component['component']->getName()));
							$alt = TBGContext::getI18n()->__('Yes');
							$src = image_url('action_ok_small.png');
						}
						
						break;
					case 'build':
						if (!$issue->getProject()->isBuildsEnabled())
						{
							throw new Exception($this->getI18n()->__('Releases are disabled'));
						}
						
						$builds = $issue->getBuilds();
						if (!array_key_exists($affected_id, $builds))
						{
							throw new Exception($this->getI18n()->__('This release is not affected by this issue'));
						}
						$build = $builds[$affected_id];
						
						if ($build['confirmed'] == true)
						{
							$issue->confirmAffectedBuild($build['build'], false);
							
							$message = TBGContext::getI18n()->__('Release <b>%build</b> is now unconfirmed for this issue', array('%build' => $build['build']->getName()));
							$alt = TBGContext::getI18n()->__('No');
							$src = image_url('action_cancel_small.png');
						}
						else
						{
							$issue->confirmAffectedBuild($build['build']);
							
							$message = TBGContext::getI18n()->__('Release <b>%build</b> is now confirmed for this issue', array('%build' => $build['build']->getName()));
							$alt = TBGContext::getI18n()->__('Yes');
							$src = image_url('action_ok_small.png');
						}
						
						break;
					default:
						throw new Exception('Internal error');
						break;
				}
				
				return $this->renderJSON(array('message' => $message, 'alt' => $alt, 'src' => $src));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}
		
		public function runRemoveAffected(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			try
			{
				$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
				
				if (!$issue->canEditIssue())
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
				}
				
				switch ($request['affected_type'])
				{
					case 'edition':
						if (!$issue->getProject()->isEditionsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Editions are disabled')));
						}
						
						$editions = $issue->getEditions();
						$edition = $editions[$request['affected_id']];
						
						$issue->removeAffectedEdition($edition['edition']);
						
						$message = TBGContext::getI18n()->__('Edition <b>%edition</b> is no longer affected by this issue', array('%edition' => $edition['edition']->getName()));
												
						break;
					case 'component':
						if (!$issue->getProject()->isComponentsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Components are disabled')));
						}
						
						$components = $issue->getComponents();
						$component = $components[$request['affected_id']];
						
						$issue->removeAffectedComponent($component['component']);
						
						$message = TBGContext::getI18n()->__('Component <b>%component</b> is no longer affected by this issue', array('%component' => $component['component']->getName()));
												
						break;
					case 'build':
						if (!$issue->getProject()->isBuildsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Releases are disabled')));
						}
						
						$builds = $issue->getBuilds();
						$build = $builds[$request['affected_id']];
						
						$issue->removeAffectedBuild($build['build']);
						
						$message = TBGContext::getI18n()->__('Release <b>%build</b> is no longer affected by this issue', array('%build' => $build['build']->getName()));
											
						break;
					default:
						throw new Exception('Internal error');
						break;
				}
				
				$editions = array();
				$components = array();
				$builds = array();
				
				if($issue->getProject()->isEditionsEnabled())
				{
					$editions = $issue->getEditions();
				}
				
				if($issue->getProject()->isComponentsEnabled())
				{
					$components = $issue->getComponents();
				}

				if($issue->getProject()->isBuildsEnabled())
				{
					$builds = $issue->getBuilds();
				}
				
				$count = count($editions) + count($components) + count($builds) - 1;
				
				return $this->renderJSON(array('message' => $message, 'itemcount' => $count));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('An internal error has occured')));
			}
		}
		
		public function runStatusAffected(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			try
			{
				$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
				$status = TBGContext::factory()->TBGStatus($request['status_id']);
				if (!$issue->canEditIssue())
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
				}
				
				switch ($request['affected_type'])
				{
					case 'edition':
						if (!$issue->getProject()->isEditionsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Editions are disabled')));
						}
						$editions = $issue->getEditions();
						$edition = $editions[$request['affected_id']];

						$issue->setAffectedEditionStatus($edition['edition'], $status);
						
						$message = TBGContext::getI18n()->__('Edition <b>%edition</b> is now %status', array('%edition' => $edition['edition']->getName(), '%status' => $status->getName()));
												
						break;
					case 'component':
						if (!$issue->getProject()->isComponentsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Components are disabled')));
						}
						$components = $issue->getComponents();
						$component = $components[$request['affected_id']];
						
						$issue->setAffectedcomponentStatus($component['component'], $status);
						
						$message = TBGContext::getI18n()->__('Component <b>%component</b> is now %status', array('%component' => $component['component']->getName(), '%status' => $status->getName()));
												
						break;
					case 'build':
						if (!$issue->getProject()->isBuildsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Releases are disabled')));
						}
						$builds = $issue->getBuilds();
						$build = $builds[$request['affected_id']];

						$issue->setAffectedbuildStatus($build['build'], $status);
						
						$message = TBGContext::getI18n()->__('Release <b>%build</b> is now %status', array('%build' => $build['build']->getName(), '%status' => $status->getName()));
												
						break;
					default:
						throw new Exception('Internal error');
						break;
				}
				
				return $this->renderJSON(array('message' => $message, 'colour' => $status->getColor(), 'name' => $status->getName()));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('An internal error has occured')));
			}
		}	
			
		public function runAddAffected(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			try
			{
				$issue = TBGContext::factory()->TBGIssue($request['issue_id']);
				$statuses = TBGStatus::getAll();

				switch ($request['item_type'])
				{
					case 'edition':
						if (!$issue->getProject()->isEditionsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Editions are disabled')));
						}
						elseif (!$issue->canEditAffectedEditions())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
						}

						
						$edition = TBGContext::factory()->TBGEdition($request['which_item_edition']);
						
						if (TBGIssueAffectsEditionTable::getTable()->getByIssueIDandEditionID($issue->getID(), $edition->getID()))
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('%item is already affected by this issue', array('%item' => $edition->getName()))));
						}
						
						$edition = $issue->addAffectedEdition($edition);

						$item = $edition;
						$itemtype = 'edition';
						$itemtypename = TBGContext::getI18n()->__('Edition');
						$content = get_template_html('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
						
						$message = TBGContext::getI18n()->__('Edition <b>%edition</b> is now affected by this issue', array('%edition' => $edition['edition']->getName()));
												
						break;
					case 'component':
						if (!$issue->getProject()->isComponentsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Components are disabled')));
						}
						elseif (!$issue->canEditAffectedComponents())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
						}
						
						$component = TBGContext::factory()->TBGComponent($request['which_item_component']);
						
						if (TBGIssueAffectsComponentTable::getTable()->getByIssueIDandComponentID($issue->getID(), $component->getID()))
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('%item is already affected by this issue', array('%item' => $component->getName()))));
						}
						
						$component = $issue->addAffectedComponent($component);
						
						$item = $component;
						$itemtype = 'component';
						$itemtypename = TBGContext::getI18n()->__('Component');
						$content = get_template_html('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
						
						$message = TBGContext::getI18n()->__('Component <b>%component</b> is now affected by this issue', array('%component' => $component['component']->getName()));
												
						break;
					case 'build':
						if (!$issue->getProject()->isBuildsEnabled())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Releases are disabled')));
						}
						elseif (!$issue->canEditAffectedBuilds())
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You are not allowed to do this')));
						}
						
						$build = TBGContext::factory()->TBGBuild($request['which_item_build']);

						
						if (TBGIssueAffectsBuildTable::getTable()->getByIssueIDandBuildID($issue->getID(), $build->getID()))
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('%item is already affected by this issue', array('%item' => $build->getName()))));
						}
												
						$build = $issue->addAffectedBuild($build);
						
						$item = $build;
						$itemtype = 'build';
						$itemtypename = TBGContext::getI18n()->__('Release');
						$content = get_template_html('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
												
						$message = TBGContext::getI18n()->__('Release <b>%build</b> is now affected by this issue', array('%build' => $build['build']->getName()));
												
						break;
					default:
						throw new Exception('Internal error');
						break;
				}
				
				$editions = array();
				$components = array();
				$builds = array();
				
				if($issue->getProject()->isEditionsEnabled())
				{
					$editions = $issue->getEditions();
				}
				
				if($issue->getProject()->isComponentsEnabled())
				{
					$components = $issue->getComponents();
				}

				if($issue->getProject()->isBuildsEnabled())
				{
					$builds = $issue->getBuilds();
				}
				
				$count = count($editions) + count($components) + count($builds);
				
				return $this->renderJSON(array('content' => $content, 'message' => $message, 'itemcount' => $count));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}
		
		/**
		 * Reset user password
		 * 
		 * @param TBGRequest $request The request object
		 * 
		 */
		public function runReset(TBGRequest $request)
		{			
			$i18n = TBGContext::getI18n();
			
			try
			{
				if ($request->hasParameter('user') && $request->hasParameter('reset_hash'))
				{
					$user = TBGUser::getByUsername(str_replace('%2E', '.', $request['user']));
					if ($user instanceof TBGUser)
					{
						if ($request['reset_hash'] == $user->getActivationKey())
						{
							$this->error = false;
							if ($request->isPost())
							{
								$p1 = trim($request['password_1']);
								$p2 = trim($request['password_2']);
								
								if ($p1 && $p2 && $p1 == $p2)
								{
									$user->setPassword($p1);
									$user->regenerateActivationKey();
									$user->save();
									TBGContext::setMessage('login_message', $i18n->__('Your password has been reset. Please log in.'));
									TBGContext::setMessage('login_referer', $this->getRouting()->generate('home'));
									return $this->forward(TBGContext::getRouting()->generate('login_page'));
								}
								else
								{
									$this->error = true;
								}
							}
							else
							{
								$user->regenerateActivationKey();
							}
							$this->user = $user;
						}
						else
						{
							throw new Exception('Your password recovery token is either invalid or has expired');
						}
					}
					else
					{
						throw new Exception('User is invalid or does not exist');	
					}
				}
				else
				{
					throw new Exception('An internal error has occured');
				}
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('login_message_err', $i18n->__($e->getMessage()));
				return $this->forward(TBGContext::getRouting()->generate('login_page'));
			}
		}
		
		/**
		 * Generate captcha picture
		 * 
		 * @param TBGRequest $request The request object
		 * @global array $_SESSION['activation_number'] The session captcha activation number
		 */			
		public function runCaptcha(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			
			if (!function_exists('imagecreatetruecolor'))
			{
				return $this->return404();
			}
			
			$this->getResponse()->setContentType('image/png');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			$chain = str_split($_SESSION['activation_number'],1);
			$size = getimagesize(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'iconsets' . DS . TBGSettings::getIconsetName() . DS . 'numbers/0.png');
			$captcha = imagecreatetruecolor($size[0]*sizeof($chain), $size[1]);
			foreach ($chain as $n => $number)
			{
				$pic = imagecreatefrompng(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'iconsets' . DS . TBGSettings::getIconsetName() . DS . 'numbers/' . $number . '.png');
				imagecopymerge($captcha, $pic, $size[0]*$n, 0, 0, 0, imagesx($pic), imagesy($pic), 100);
				imagedestroy($pic);
			}
			imagepng($captcha);
			imagedestroy($captcha);
			
			return true;
		}
		
		public function runIssueGetTempFieldValue(TBGRequest $request)
		{
			switch ($request['field'])
			{
				case 'assigned_to':
					if ($request['identifiable_type'] == 'user')
					{
						$identifiable = TBGContext::factory()->TBGUser($request['value']);
						$content = $this->getComponentHTML('main/userdropdown', array('user' => $identifiable));
					}
					elseif ($request['identifiable_type'] == 'team')
					{
						$identifiable = TBGContext::factory()->TBGTeam($request['value']);
						$content = $this->getComponentHTML('main/teamdropdown', array('team' => $identifiable));
					}

					return $this->renderJSON(array('content' => $content));
					break;
			}
		}
		
		public function runServe(TBGRequest $request)
		{
			if(!TBGContext::isMinifyEnabled())
			{
				$itemarray = array($request['g'] => explode(',', base64_decode($request['files'])));
				
				if (array_key_exists('js', $itemarray))
				{
					header('Content-type: text/javascript');
					foreach($itemarray['js'] as $file)
					{
						$ext = substr($file, -2);
						if($ext == 'js' && file_exists($file) && strpos(realpath($file), THEBUGGENIE_PATH) !== false)
						{
							echo file_get_contents($file);
						}
					}
				}
				else
				{
					header('Content-type: text/css');
					foreach($itemarray['css'] as $file)
					{
						$ext = substr($file, -3);
                        if($ext == 'css' && file_exists($file) && strpos(realpath($file), THEBUGGENIE_PATH) !== false)
						{
							echo file_get_contents($file);
						}
					}
				}
				exit();
			}
			
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			define('MINIFY_MIN_DIR', dirname(__FILE__).'/../../../core/min');

			// load config
			require MINIFY_MIN_DIR . '/config.php';
			
			// setup include path
			set_include_path($min_libPath . PATH_SEPARATOR . get_include_path());
			
			require 'Minify.php';
			
			Minify::$uploaderHoursBehind = $min_uploaderHoursBehind;
			Minify::setCache(
			    isset($min_cachePath) ? $min_cachePath : ''
			    ,$min_cacheFileLocking
			);
			
			if ($min_documentRoot) {
			    $_SERVER['DOCUMENT_ROOT'] = $min_documentRoot;
			} elseif (0 === mb_stripos(PHP_OS, 'win')) {
			    Minify::setDocRoot(); // IIS may need help
			}
			
			$min_serveOptions['minifierOptions']['text/css']['symlinks'] = $min_symlinks;
			
			if ($min_allowDebugFlag && isset($_GET['debug'])) {
			    $min_serveOptions['debug'] = true;
			}
			
			if ($min_errorLogger) {
			    require_once 'Minify/Logger.php';
			    if (true === $min_errorLogger) {
			        require_once 'FirePHP.php';
			        Minify_Logger::setLogger(FirePHP::getInstance(true));
			    } else {
			        Minify_Logger::setLogger($min_errorLogger);
			    }
			}
			
			// check for URI versioning
			if (preg_match('/&\\d/', $_SERVER['QUERY_STRING'])) {
			    $min_serveOptions['maxAge'] = 31536000;
			}

			$itemarray = array($request['g'] => explode(',', base64_decode($request['files'])));
			$min_serveOptions['minApp']['groups'] = $itemarray;
			
			ob_end_clean();

			$data = Minify::serve('MinApp', $min_serveOptions);
			header_remove('Pragma');

			foreach ($data['headers'] as $name => $val)
			{
				header($name . ': ' . $val);
			}
			
			header('HTTP/1.1 '.$data['statusCode']);

			if ($data['statusCode'] != 304)
			{
				echo $data['content'];
			}

			exit();
		}
		
		public function runAccountCheckUsername(TBGRequest $request)
		{
			if ($request['desired_username'] && TBGUser::isUsernameAvailable($request['desired_username']))
			{
				return $this->renderJSON(array('available' => true, 'url' => TBGContext::getRouting()->generate('get_partial_for_backdrop', array('key' => 'confirm_username', 'username' => $request['desired_username']))));
			}
			else
			{
				return $this->renderJSON(array('available' => false));
			}
		}
		
		public function runAccountPickUsername(TBGRequest $request)
		{
			if (TBGUser::isUsernameAvailable($request['selected_username']))
			{
				$user = $this->getUser();
				$user->setUsername($request['selected_username']);
				$user->setOpenIdLocked(false);
				$user->setPassword(TBGUser::createPassword());
				$user->save();
				
				$this->getResponse()->setCookie('tbg3_username', $user->getUsername());
				$this->getResponse()->setCookie('tbg3_password', $user->getPassword());
				
				TBGContext::setMessage('username_chosen', true);
				$this->forward($this->getRouting()->generate('account'));
			}
			
			TBGContext::setMessage('error', $this->getI18n()->__('Could not pick the username "%username"', array('%username' => $request['selected_username'])));
			$this->forward($this->getRouting()->generate('account'));
		}

		public function runDashboardView(TBGRequest $request)
		{
			$view = new TBGDashboardView($request['view_id']);
			if ($view->getTargetType() == TBGDashboardView::TYPE_PROJECT)
			{
				TBGContext::setCurrentProject(new TBGProject($view->getProjectID()));
			}
			return $this->renderJSON(array('content' => $this->returnComponentHTML($view->getTemplate(), array('view' => $view))));
		}

		public function runRemoveOpenIDIdentity(TBGRequest $request)
		{
			$identity = TBGOpenIdAccountsTable::getTable()->getIdentityFromID($request['openid']);
			if ($identity && $this->getUser()->hasOpenIDIdentity($identity))
			{
				TBGOpenIdAccountsTable::getTable()->doDeleteById($request['openid']);
				return $this->renderJSON(array('message' => $this->getI18n()->__('The OpenID identity has been removed from this user account')));
			}

			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => $this->getI18n()->__('Could not remove this OpenID account')));
		}

		public function runGetTempIdentifiable(TBGRequest $request)
		{
			if ($request['i_type'] == 'user')
				return $this->renderComponent('main/userdropdown', array('user' => $request['i_id']));
			else
				return $this->renderComponent('main/teamdropdown', array('team' => $request['i_id']));

		}

		public function runDebug(TBGRequest $request)
		{
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			$this->tbg_summary = TBGContext::getDebugData($request['debug_id']);
		}

		public function runGetACLFormEntry(TBGRequest $request)
		{
			switch ($request['identifiable_type'])
			{
				case 'user':
					$target = TBGContext::factory()->TBGUser((int) $request['identifiable_value']);
					break;
				case 'team':
					$target = TBGContext::factory()->TBGTeam((int) $request['identifiable_value']);
					break;
			}
			return $this->renderJSON(array('content' => $this->getTemplateHTML('main/issueaclformentry', array('target' => $target))));
		}

		public function runRemoveScope(TBGRequest $request)
		{
			$this->getUser()->removeScope((int) $request['scope_id']);
			return $this->renderJSON('ok');
		}

		public function runConfirmScope(TBGRequest $request)
		{
			$this->getUser()->confirmScope((int) $request['scope_id']);
			return $this->renderJSON('ok');
		}

		public function runAddScope(TBGRequest $request)
		{
			if ($request->isPost())
			{
				$scope = TBGContext::getScope();
				$this->getUser()->addScope($scope, false);
				$this->getUser()->confirmScope($scope->getID());
				$route = (TBGSettings::getLoginReturnRoute() != 'referer') ? TBGSettings::getLoginReturnRoute() : 'home';
				$this->forward(TBGContext::getRouting()->generate($route));
			}
		}

		public function runIssueLog(TBGRequest $request)
		{
			try
			{
				$this->issue = TBGIssuesTable::getTable()->getIssueById((int) $request['issue_id']);
				$this->log_items = $this->issue->getLogEntries();
				if ($this->issue->isDeleted() || !$this->issue->hasAccess()) $this->issue = null;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

}
