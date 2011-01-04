<?php
	/**
	 * Module class, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * Module class, vcs_integration
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegration extends TBGModule 
	{
		
		protected $_module_version = '1.0';

		/**
		 * Return an instance of this module
		 *
		 * @return TBGVCSIntegration
		 */
		public static function getModule()
		{
			return TBGContext::getModule('vcs_integration');
		}

		protected function _initialize(TBGI18n $i18n)
		{
			$this->setLongName($i18n->__('VCS Integration'));
			$this->setConfigTitle($i18n->__('VCS Integration'));
			$this->setDescription($i18n->__('Allows details from source code checkins to be displayed in The Bug Genie'));
			$this->setConfigDescription($i18n->__('Configure repository settings for source code integration'));
			$this->setHasConfigSettings();
		}
		
		protected function _install($scope)
		{
			$this->enableListenerSaved('core', 'project_sidebar_links_timeline');
			$this->enableListenerSaved('core', 'viewissue_tabs');
			$this->enableListenerSaved('core', 'viewissue_tab_panes_back');
			//TBGPublish::getModule()->loadArticles('VCSIntegration', true);
		}
		
		protected function _addAvailableListeners()
		{
			$this->addAvailableListener('core', 'project_sidebar_links_timeline', 'listen_SidebarLinks', 'Project sidebar links');
			$this->addAvailableListener('core', 'viewissue_tabs', 'listen_viewissue_tab', 'Tab to view commit details when viewing issues');
			$this->addAvailableListener('core', 'viewissue_tab_panes_back', 'listen_viewissue_panel', 'Commit details shown when viewing issues');
		}

		protected function _addAvailableRoutes()
		{
			$this->addRoute('normalcheckin', '/vcs_integration/report/:project/', 'addCommit');
			$this->addRoute('githubcheckin', '/vcs_integration/report/:project/github/', 'addCommitGithub');
		}

		protected function _uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				TBGVCSIntegrationTable::getTable()->drop();
			}
			parent::_uninstall();
		}

		public function getRoute()
		{
			return TBGContext::getRouting()->generate('vcs_integration');
		}

		public function hasProjectAwareRoute()
		{
			return false;
		}

		public function postConfigSettings(TBGRequest $request)
		{
			$settings = array('use_web_interface', 'vcs_passkey');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
			}
			
			foreach (TBGProject::getAll() as $aProduct)
			{
				if ($request->hasParameter('web_path_' . $aProduct->getID()))
				{
					$this->saveSetting('web_path_' . $aProduct->getID(), $request->getParameter('web_path_' . $aProduct->getID()));
				}
				if ($request->hasParameter('web_type_' . $aProduct->getID()))
				{
					$this->saveSetting('web_type_' . $aProduct->getID(), $request->getParameter('web_type_' . $aProduct->getID()));
				}
				if ($request->hasParameter('web_repo_' . $aProduct->getID()))
				{
					$this->saveSetting('web_repo_' . $aProduct->getID(), $request->getParameter('web_repo_' . $aProduct->getID()));
				}
			}
		}
		
		public function isUsingHTTPMethod()
		{
			if ($this->getSetting('use_web_interface') == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function listen_SidebarLinks(TBGEvent $event)
		{
			if (TBGContext::isProjectContext())
			{
				TBGActionComponent::includeTemplate('vcs_integration/menustriplinks', array('project' => TBGContext::getCurrentProject(), 'module' => $this));
			}
		}
		
		public function listen_viewissue_tab(TBGEvent $event)
		{
			$web_path = $this->getSetting('web_path_' . $event->getSubject()->getProject()->getID());
			$web_repo = $this->getSetting('web_repo_' . $event->getSubject()->getProject()->getID());

			if (empty($web_repo) || empty($web_path))
			{
				return;
			}

			$count = TBGVCSIntegrationTable::getTable()->getNumberOfCommitsByIssue($event->getSubject()->getId());
			TBGActionComponent::includeTemplate('vcs_integration/viewissue_tab', array('count' => $count));
		}
		
		public function listen_viewissue_panel(TBGEvent $event)
		{
			$web_path = $this->getSetting('web_path_' . $event->getSubject()->getProject()->getID());
			$web_repo = $this->getSetting('web_repo_' . $event->getSubject()->getProject()->getID());

			if (empty($web_repo) || empty($web_path))
			{
				return;
			}

			$data = TBGVCSIntegrationTable::getTable()->getCommitsByIssue($event->getSubject()->getId());
			
			if (!is_array($data))
			{
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_top', array('items' => false));
			}
			else
			{
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_top', array('items' => true));
				
				/* Now produce each box */
				foreach ($data as $revno => $entry)
				{
					$revision = $revno;
					/* Build correct URLs */
					switch ($this->getSetting('web_type_' . $event->getSubject()->getProject()->getID()))
					{
						case 'viewvc':
							$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $revision;
							break;
						case 'viewvc_repo':
							$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $revision;
							break;
						case 'websvn':
							$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $revision;
							break;
						case 'websvn_mv':
							$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $revision;
							break;
						case 'loggerhead':
							$link_rev = $web_path . '/' . $web_repo . '/revision/' . $revision;
							break;
						case 'gitweb':
							$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $revision;
							break;
						case 'cgit':
							$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $revision;
							break;
						case 'hgweb':
							$link_rev = $web_path . '/' . $web_repo . '/rev/' . $revision;
							break;
						case 'github':
							$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $revision;
							break;
					}
					
					/* Now we have everything, render the template */
					include_template('vcs_integration/commitbox', array("projectId" => $event->getSubject()->getProject()->getID(), "id" => $entry[0][0], "revision" => $revision, "author" => $entry[0][1], "date" => $entry[0][2], "log" => $entry[0][3], "files" => $entry[1]));
				}
				
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_bottom');
			}
		}
		
		public function addNewCommit($project, $commit_msg, $old_rev, $new_rev, $date = null, $changed, $author)
		{
			/* Find issues to update */
			$fixes_grep = "#((bug|issue|ticket|fix|fixes|fixed|fixing|applies to|closes|references|ref|addresses|re|see|according to|also see)\s\#?(([A-Z0-9]+\-)?\d+))#ie";
			
			$output = '';
			
			$f_issues = array();
			
			try
			{
				$project = new TBGProject($project);
			}
			catch (Exception $e)
			{
				return TBGContext::getI18n()->__('Error: Invalid project ID');
			}
			
			if (preg_match_all($fixes_grep, $commit_msg, $f_issues))
			{
				// Github
				if (is_array($changed))
				{
					$entries = $changed;
					$changed = '';
					// Now handle changed files
					foreach ($entries[0] as $file)
					{
						$changed .= 'M'.$file."\n";
					}
					
					// Now handle new files
					foreach ($entries[1] as $file)
					{
						$changed .= 'A'.$file."\n";
					}
					
					// Now handle deleted files
					foreach ($entries[2] as $file)
					{
						$changed .= 'D'.$file."\n";
					}
				}
				
				$f_issues = array_unique($f_issues[3]);

				$file_lines = preg_split('/[\n\r]+/', $changed);
				$files = array();

				foreach ($file_lines as $aline)
				{
					$action = substr($aline, 0, 1);

					if ($action == "A" || $action == "U" || $action == "D" || $action == "M")
					{
						$theline = trim(substr($aline, 1));
						$files[] = array($action, $theline);
					}
				}
				
				foreach ($f_issues as $issue_no)
				{
					TBGContext::setCurrentProject($project);
					$theIssue = TBGIssue::getIssueFromLink($issue_no, true);

					if ($theIssue instanceof TBGIssue)
					{
						$uid = 0;
						
						/*
						 * Some VCSes use a different format of storing the committer's name. Systems like bzr, git and hg use the format
						 * Joe Bloggs <me@example.com>, instead of a classic username. Therefore a user will be found via 4 queries:
						 * a) First we extract the email if there is one, and find a user with that email
						 * b) If one is not found - or if no email was specified, then instead test against the real name (using the name part if there was an email)
						 * c) the username or full name is checked against the friendly name field
						 * d) and if we still havent found one, then we check against the username
						 * e) and if we STILL havent found one, we just say the user is id 0 (unknown user).
						 */
						 
						if (preg_match("/(?<=<)(.*)(?=>)/", $author, $matches))
						{
							$email = $matches[0];
							
							// a)
							$crit = new B2DBCriteria();
							$crit->setFromTable(TBGUsersTable::getTable());
							$crit->addSelectionColumn(TBGUsersTable::ID);
							$crit->addWhere(TBGUsersTable::EMAIL, $email);
							$row = TBGUsersTable::getTable()->doSelectOne($crit);
							
							if ($row != null)
							{
								$uid = $row->get(TBGUsersTable::ID);
							}
							else
							{
								// Not found by email
								preg_match("/(?<=^)(.*)(?= <)/", $author, $matches);
								$author = $matches[0];
							}
						}

						// b)
						
						if ($uid == 0)
						{
							$crit = new B2DBCriteria();
							$crit->setFromTable(TBGUsersTable::getTable());
							$crit->addSelectionColumn(TBGUsersTable::ID);
							$crit->addWhere(TBGUsersTable::REALNAME, $author);
							$row = TBGUsersTable::getTable()->doSelectOne($crit);
							
							if ($row != null)
							{
								$uid = $row->get(TBGUsersTable::ID);
							}
						}
						
						// c)
						
						if ($uid == 0)
						{
							$crit = new B2DBCriteria();
							$crit->setFromTable(TBGUsersTable::getTable());
							$crit->addSelectionColumn(TBGUsersTable::ID);
							$crit->addWhere(TBGUsersTable::BUDDYNAME, $author);
							$row = TBGUsersTable::getTable()->doSelectOne($crit);
							
							if ($row != null)
							{
								$uid = $row->get(TBGUsersTable::ID);
							}
						}
						
						// d)
						
						if ($uid == 0)
						{
							$crit = new B2DBCriteria();
							$crit->setFromTable(TBGUsersTable::getTable());
							$crit->addSelectionColumn(TBGUsersTable::ID);
							$crit->addWhere(TBGUsersTable::UNAME, $author);
							$row = TBGUsersTable::getTable()->doSelectOne($crit);
							
							if ($row != null)
							{
								$uid = $row->get(TBGUsersTable::ID);
							}
						}
						
						$theIssue->addSystemComment(TBGContext::getI18n()->__('Issue updated from code repository'), TBGContext::getI18n()->__('This issue has been updated with the latest changes from the code repository.<source>%commit_msg%</source>', array('commit_msg' => $commit_msg)), $uid);

						foreach ($files as $afile)
						{
							if ($date == null)
							{
								$date = time();
							}
						
							TBGVCSIntegrationTable::addEntry($theIssue->getID(), $afile[0], $commit_msg, $afile[1], $new_rev, $old_rev, $uid, $date);
						}
						$output .= 'Updated ' . $theIssue->getFormattedIssueNo() . "\n";
					}
					else
					{
						$output .= 'Can\'t find ' . $issue_no . ' so not updating that one.' . "\n";
					}
				}
			}
			return $output;
		}
	}
