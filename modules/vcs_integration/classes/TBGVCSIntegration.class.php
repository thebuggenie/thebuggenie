<?php
	/**
	 * Module class, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * Module class, vcs_integration
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 * 
	 * @Table(name="TBGModulesTable")
	 */
	class TBGVCSIntegration extends TBGModule 
	{
		const MODE_DISABLED = 0;
		const MODE_ISSUECOMMITS = 1;
		
		const ACCESS_DIRECT = 0;
		const ACCESS_HTTP = 1;
		
		protected $_longname = 'VCS Integration';
		
		protected $_description = 'Allows details from source code checkins to be displayed in The Bug Genie. Configure in each project\'s settings.';
		
		protected $_module_config_title = 'VCS Integration';
		
		protected $_module_config_description = 'Configure repository settings for source code integration';
		
		protected $_has_config_settings = false;
		
		protected $_module_version = '2.0';

		/**
		 * Return an instance of this module
		 *
		 * @return TBGVCSIntegration
		 */
		public static function getModule()
		{
			return TBGContext::getModule('vcs_integration');
		}

		protected function _initialize()
		{
		}
		
		protected function _install($scope)
		{
		}
		
		protected function _addListeners()
		{
			TBGEvent::listen('core', 'project_sidebar_links_statistics', array($this, 'listen_sidebar_links'));
			TBGEvent::listen('core', 'breadcrumb_project_links', array($this, 'listen_breadcrumb_links'));
			TBGEvent::listen('core', 'viewissue_tabs', array($this, 'listen_viewissue_tab'));
			TBGEvent::listen('core', 'viewissue_tab_panes_back', array($this, 'listen_viewissue_panel'));
			TBGEvent::listen('core', 'config_project_tabs', array($this, 'listen_projectconfig_tab'));
			TBGEvent::listen('core', 'config_project_panes', array($this, 'listen_projectconfig_panel'));
			TBGEvent::listen('core', 'project_header_buttons', array($this, 'listen_projectheader'));
		}

		protected function _addRoutes()
		{
			$this->addRoute('vcs_commitspage', '/:project_key/commits', 'projectCommits');
			$this->addRoute('normalcheckin', '/vcs_integration/report/:project_key/', 'addCommit');
			$this->addRoute('githubcheckin', '/vcs_integration/report/:project_key/github/', 'addCommitGithub');
			$this->addRoute('gitoriouscheckin', '/vcs_integration/report/:project_key/gitorious/', 'addCommitGitorious');
			$this->addRoute('configure_vcs_settings', '/configure/project/:project_id/vcs', 'configureProjectSettings', array('config_module' => 'core', 'section' => TBGSettings::CONFIGURATION_SECTION_PROJECTS));
		}

		protected function _uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				TBGVCSIntegrationCommitsTable::getTable()->drop();
				TBGVCSIntegrationFilesTable::getTable()->drop();
				TBGVCSIntegrationIssueLinksTable::getTable()->drop();
				
				try
				{
					\b2db\Core::getTable('TBGVCSIntegrationTable')->drop();
				}
				catch (Exception $e) { }
			}
			parent::_uninstall();
		}
		
		protected function _upgrade()
		{
			switch ($this->_version)
			{
				case "1.0":
					// Upgrade tables
					\b2db\Core::getTable('TBGVCSIntegrationCommitsTable')->create();
					\b2db\Core::getTable('TBGVCSIntegrationFilesTable')->create();
					\b2db\Core::getTable('TBGVCSIntegrationIssueLinksTable')->create();
					
					// Migrate data from old table to new tables
					$crit = new \b2db\Criteria();
					$crit->addOrderBy(TBGVCSIntegrationTable::DATE, \b2db\Criteria::SORT_DESC);
					$results = TBGVCSIntegrationTable::getTable()->doSelect($crit);
					
					if (is_object($results) && count($results) > 0)
					{
						$commits = array();

						while ($results->next())
						{
							$rev = $results->get(TBGVCSIntegrationTable::NEW_REV);
							if (array_key_exists($rev, $commits))
							{
								// Add a new file or issue to the commit data
								$commits[$rev]['files'][$results->get(TBGVCSIntegrationTable::FILE_NAME)] = array('file_name' => $results->get(TBGVCSIntegrationTable::FILE_NAME), 'action' => $results->get(TBGVCSIntegrationTable::ACTION));
								$commits[$rev]['issues'][$results->get(TBGVCSIntegrationTable::ISSUE_NO)] = $results->get(TBGVCSIntegrationTable::ISSUE_NO);
							}
							else
							{
								// All issues will be of the same project, so use one issue
								$issue = TBGContext::factory()->TBGIssue($results->get(TBGVCSIntegrationTable::ISSUE_NO));
								// Add details of a new commit
								$commits[$rev] = array('commit' => array(), 'files' => array(), 'issues' => array());
								
								$commits[$rev]['commit'] = array('new_rev' => $rev, 'old_rev' => $results->get(TBGVCSIntegrationTable::OLD_REV), 'author' => $results->get(TBGVCSIntegrationTable::AUTHOR), 'date' => $results->get(TBGVCSIntegrationTable::DATE), 'log' => $results->get(TBGVCSIntegrationTable::LOG), 'scope' => $results->get(TBGVCSIntegrationTable::SCOPE), 'project' => $issue->getProject());
								$commits[$rev]['files'][$results->get(TBGVCSIntegrationTable::FILE_NAME)] = array('file_name' => $results->get(TBGVCSIntegrationTable::FILE_NAME), 'action' => $results->get(TBGVCSIntegrationTable::ACTION));
								$commits[$rev]['issues'][$results->get(TBGVCSIntegrationTable::ISSUE_NO)] = $results->get(TBGVCSIntegrationTable::ISSUE_NO);
							}
						}
						
						foreach ($commits as $commit)
						{
							$files = array();
							$issues = array();

							$scope = TBGContext::factory()->TBGScope($commit['commit']['scope']);
							
							try
							{
								$author = TBGContext::factory()->TBGUser($commit['commit']['author']);
							}
							catch (Exception $e)
							{
								$author = TBGContext::factory()->TBGUser(TBGSettings::getDefaultUserID());
							}

							// Add the commit
							$inst = new TBGVCSIntegrationCommit();
							$inst->setAuthor($author);
							$inst->setDate($commit['commit']['date']);
							$inst->setLog($commit['commit']['log']);
							$inst->setPreviousRevision($commit['commit']['old_rev']);
							$inst->setRevision($commit['commit']['new_rev']);
							$inst->setProject($commit['commit']['project']);
							$inst->setScope($scope);
							$inst->save();
							
							// Process issue list, remove duplicates
							$issues = $commit['issues'];
							$files = $commit['files'];

							$commit = $inst;
							
							foreach ($files as $file)
							{
								// Add affected files
								$inst = new TBGVCSIntegrationFile();
								$inst->setCommit($commit);
								$inst->setFile($file['file_name']);
								$inst->setAction($file['action']);
								$inst->setScope($scope);
								$inst->save();
							}
							
							foreach ($issues as $issue)
							{
								// Add affected issues
								$issue = TBGContext::factory()->TBGIssue($issue);
								$inst = new TBGVCSIntegrationIssueLink();
								$inst->setIssue($issue);
								$inst->setCommit($commit);
								$inst->setScope($scope);
								$inst->save();
							}
						}
					}
					
					// Drop old table
					TBGVCSIntegrationTable::getTable()->drop();
					
					// Migrate settings to new format
					$access_method = $this->getSetting('use_web_interface');
					$passkey = $this->getSetting('vcs_passkey');
					
					foreach (TBGProject::getAll() as $project)
					{
						$projectId = $project->getID();
						$web_path = $this->getSetting('web_path_' . $projectId);
						$web_repo = $this->getSetting('web_repo_' . $projectId);
						
						// Check if enabled
						if ($web_path == ''): continue; endif;
							
						switch ($this->getSetting('web_type_' . $projectId))
						{
							case 'viewvc':
								$base_url = $web_path . '/' . '?root=' . $web_repo;
								$link_rev = '&amp;view=rev&amp;revision=%revno%';
								$link_file = '&amp;view=log';
								$link_diff = '&amp;r1=%revno%&amp;r2=%oldrev%';
								$link_view = '&amp;revision=%revno%&amp;view=markup';
								break;
							case 'viewvc_repo':
								$base_url = $web_path;
								$link_rev = '/?view=rev&amp;revision=%revno%';
								$link_file = '/%file%?view=log';
								$link_diff = '/%file%?r1=%revno%&amp;r2=%oldrev%';
								$link_view = '/%file%?revision=%revno%&amp;view=markup';
								break;
							case 'websvn':
								$base_url = $web_path;
								$link_rev = '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=%revno%';
								$link_file = '/log.php?repname=' . $web_repo . '&amp;path=/$%file%';
								$link_diff = '/comp.php?repname=' . $web_repo . '&amp;compare[]=/%file%@%revno%&amp;compare[]=/%file%@%oldrev%';
								$link_view = '/filedetails.php?repname=' . $web_repo . '&path=/%file%&amp;rev=%revno%';
								break;
							case 'websvn_mv':
								$base_url = $web_path;
								$link_rev = '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=%revno%';
								$link_file = '/%file%?repname=' . $web_repo;
								$link_diff = '/%file%?repname=' . $web_repo . '&amp;compare[]=/%file%@%revno%&amp;compare[]=/%file%@%oldrev%';
								$link_view = '/%file%?repname=' . $web_repo . '&amp;rev=%revno%';
								break;
							case 'loggerhead':
								$base_url = $web_path . '/' . $web_repo;
								$link_rev = '/revision/%revno%';
								$link_file = '/changes';
								$link_diff = '/revision/%revno%?compare_revid=%oldrev%';
								$link_view = '/annotate/head:/%file%';
								break;
							case 'gitweb':
								$base_url = $web_path . '/' . '?p=' . $web_repo;
								$link_rev = ';a=commitdiff;h=%revno%';
								$link_file = ';a=history;f=%file%;hb=HEAD';
								$link_diff = ';a=blobdiff;f=%file%;hb=%revno%;hpb=%oldrev%';
								$link_view = ';a=blob;f=%file%;hb=%revno%';
								break;
							case 'cgit':
								$base_url = $web_path . '/' . $web_repo;
								$link_rev = '/commit/?id=%revno%';
								$link_file = '/log';
								$link_diff = '/diff/%file%?id=%revno%?id2=%oldrev%';
								$link_view = '/tree/%file%?id=%revno%';
								break;
							case 'hgweb':
								$base_url = $web_path . '/' . $web_repo;
								$link_rev = '/rev/%revno%';
								$link_file = '/log/tip/%file%';
								$link_diff = '/diff/%revno%/%file%';
								$link_view = '/file/%revno%/%file%';
								break;
							case 'github':
								$base_url = 'http://github.com/' . $web_repo;
								$link_rev = '/commit/%revno%';
								$link_file = '/commits/master/%file%';
								$link_diff = '/commit/%revno%';
								$link_view = '/blob/%revno%/%file%';
								break;
							case 'gitorious':
								$base_url = $web_path . '/' . $web_repo;
								$link_rev = '/commit/%revno%';
								$link_file = '/blobs/history/master/%file%';
								$link_diff = '/commit/%revno%';
								$link_view = '/blobs/%revno%/%file%';
								break;
						}
						$this->saveSetting('browser_url_'.$projectId, $base_url);
						$this->saveSetting('log_url_'.$projectId, $link_file);
						$this->saveSetting('blob_url_'.$projectId, $link_diff);
						$this->saveSetting('diff_url_'.$projectId, $link_view);
						$this->saveSetting('commit_url_'.$projectId, $link_rev);
						
						// Access method
						$this->saveSetting('access_method_'.$projectId, $access_method);
						if ($access_method == self::ACCESS_HTTP)
						{
							$this->saveSetting('access_passkey_'.$projectId, $passkey);
						}
						
						// Enable VCS Integration
						$this->saveSetting('vcs_mode_'.$projectId, self::MODE_ISSUECOMMITS);
						
						// Remove old settings
						$this->deleteSetting('web_type_' . $projectId);
						$this->deleteSetting('web_path_' . $projectId);
						$this->deleteSetting('web_repo_' . $projectId);
					}
					
					// Remove old settings
					$this->deleteSetting('use_web_interface');
					$this->deleteSetting('vcs_passkey');
					
					// Upgrade module version
					$this->_version = $this->_module_version;
					$this->save();
					break;
			}
		}

		public function getRoute()
		{
			return TBGContext::getRouting()->generate('vcs_integration');
		}

		public function hasProjectAwareRoute()
		{
			return false;
		}
		
		public function listen_sidebar_links(TBGEvent $event)
		{
			if (TBGContext::isProjectContext())
			{
				TBGActionComponent::includeTemplate('vcs_integration/menustriplinks', array('project' => TBGContext::getCurrentProject(), 'module' => $this, 'submenu' => $event->getParameter('submenu')));
			}
		}
		
		public function listen_breadcrumb_links(TBGEvent $event)
		{
			$event->addToReturnList(array('url' => TBGContext::getRouting()->generate('vcs_commitspage', array('project_key' => TBGContext::getCurrentProject()->getKey())), 'title' => TBGContext::getI18n()->__('Commits')));
		}
		
		public function listen_projectheader(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('vcs_integration/projectheaderbutton');
		}
		
		public function listen_projectconfig_tab(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('vcs_integration/projectconfig_tab', array('selected_tab' => $event->getParameter('selected_tab')));
		}
		
		public function listen_projectconfig_panel(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('vcs_integration/projectconfig_panel', array('selected_tab' => $event->getParameter('selected_tab'), 'access_level' => $event->getParameter('access_level'), 'project' => $event->getParameter('project')));
		}
		
		public function listen_viewissue_tab(TBGEvent $event)
		{
			if (TBGContext::getModule('vcs_integration')->getSetting('vcs_mode_' . TBGContext::getCurrentProject()->getID()) == TBGVCSIntegration::MODE_DISABLED): return; endif;
				
			$count = count(TBGVCSIntegrationIssueLink::getCommitsByIssue($event->getSubject()));
			TBGActionComponent::includeTemplate('vcs_integration/viewissue_tab', array('count' => $count));
		}
		
		public function listen_viewissue_panel(TBGEvent $event)
		{
			if (TBGContext::getModule('vcs_integration')->getSetting('vcs_mode_' . TBGContext::getCurrentProject()->getID()) == TBGVCSIntegration::MODE_DISABLED): return; endif;

			$links = TBGVCSIntegrationIssueLink::getCommitsByIssue($event->getSubject());
			
			if (count($links) == 0 || !is_array($links))
			{
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_top', array('items' => false));
			}
			else
			{
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_top', array('items' => true));
				
				/* Now produce each box */
				foreach ($links as $link)
				{	
					include_template('vcs_integration/commitbox', array("projectId" => $event->getSubject()->getProject()->getID(), "commit" => $link->getCommit()));
				}
				
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_bottom');
			}
		}
		
		public static function processCommit(TBGProject $project, $commit_msg, $old_rev, $new_rev, $date = null, $changed, $author, $branch = null)
		{
			$output = '';
			TBGContext::setCurrentProject($project);
			
			if ($project->isArchived()): return; endif;
			
			try
			{
				TBGContext::getI18n();
			}
			catch (Exception $e)
			{
				TBGContext::reinitializeI18n(null);
			}
			
			// Is VCS Integration enabled?
			if (TBGSettings::get('vcs_mode_'.$project->getID(), 'vcs_integration') == TBGVCSIntegration::MODE_DISABLED)
			{
				$output .= '[VCS '.$project->getKey().'] This project does not use VCS Integration' . "\n";
				return $output;
			}
			
			$fixes_grep = TBGTextParser::getIssueRegex();

			// Build list of affected issues
			$temp = array();
			$issues = array();
			
			if (preg_match_all($fixes_grep, $commit_msg, $temp))
			{	
				$temp = array_unique($temp[2]);
				foreach ($temp as $issue_no)
				{
					$issue = TBGIssue::getIssueFromLink($issue_no);
					if ($issue instanceof TBGIssue): $issues[] = $issue; endif;
				}
			}

			// If no issues exist, we may not be able to continue
			if (count($issues) == 0)
			{
				$output .= '[VCS '.$project->getKey().'] This project only accepts commits which affect issues' . "\n";
				return $output;
			}
			
			// Build list of affected files
			$file_lines = preg_split('/[\n\r]+/', $changed);
			$files = array();
			
			foreach ($file_lines as $aline)
			{
				$action = mb_substr($aline, 0, 1);
			
				if ($action == "A" || $action == "U" || $action == "D" || $action == "M")
				{
					$theline = trim(mb_substr($aline, 1));
					$files[] = array($action, $theline);
				}
			}
			
			// Find author of commit, fallback is guest
			$uid = 0;
			
			/*
			 * Some VCSes use a different format of storing the committer's name. Systems like bzr, git and hg use the format
			 * Joe Bloggs <me@example.com>, instead of a classic username. Therefore a user will be found via 4 queries:
			 * a) First we extract the email if there is one, and find a user with that email
			 * b) If one is not found - or if no email was specified, then instead test against the real name (using the name part if there was an email)
			 * c) the username or full name is checked against the friendly name field
			 * d) and if we still havent found one, then we check against the username
			 * e) and if we STILL havent found one, we use the guest user
			 */
			 
			if (preg_match("/(?<=<)(.*)(?=>)/", $author, $matches))
			{
				$email = $matches[0];

				// a)
				$crit = new \b2db\Criteria();
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
				$crit = new \b2db\Criteria();
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
				$crit = new \b2db\Criteria();
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
				$crit = new \b2db\Criteria();
				$crit->setFromTable(TBGUsersTable::getTable());
				$crit->addSelectionColumn(TBGUsersTable::ID);
				$crit->addWhere(TBGUsersTable::UNAME, $author);
				$row = TBGUsersTable::getTable()->doSelectOne($crit);
				
				if ($row != null)
				{
					$uid = $row->get(TBGUsersTable::ID);
				}
			}
			
			// e)
			
			if ($uid == 0)
			{
				$uid = TBGSettings::getDefaultUserID();
			}
			
			$user = TBGContext::factory()->TBGUser($uid);
			
			$output .= '[VCS '.$project->getKey().'] Commit to be logged by user ' . $user->getName() . "\n";

			if ($date == null):
				$date = time();
			endif;
			
			// Create the commit data
			$commit = new TBGVCSIntegrationCommit();
			$commit->setAuthor($user);
			$commit->setDate($date);
			$commit->setLog($commit_msg);
			$commit->setPreviousRevision($old_rev);
			$commit->setRevision($new_rev);
			$commit->setProject($project);
			
			if ($branch !== null)
			{
				$data = 'branch:'.$branch;
				$commit->setMiscData($data);
			}
			
			$commit->save();
			
			$output .= '[VCS '.$project->getKey().'] Commit logged with revision ' . $commit->getRevision() . "\n";
			
			// Create issue links
			foreach ($issues as $issue)
			{
				$inst = new TBGVCSIntegrationIssueLink();
				$inst->setIssue($issue);
				$inst->setCommit($commit);
				$inst->save();
				
				$issue->addSystemComment(TBGContext::getI18n()->__('Issue updated from code repository'), TBGContext::getI18n()->__('This issue has been updated with the latest changes from the code repository.<source>%commit_msg%</source>', array('%commit_msg%' => $commit_msg)), $uid);
				$output .= '[VCS '.$project->getKey().'] Updated issue ' . $issue->getFormattedIssueNo() . "\n";
			}
			
			// Create file links
			foreach ($files as $afile)
			{
				// index 0 is action, index 1 is file
				$inst = new TBGVCSIntegrationFile();
				$inst->setAction($afile[0]);
				$inst->setFile($afile[1]);
				$inst->setCommit($commit);
				$inst->save();
				
				$output .= '[VCS '.$project->getKey().'] Added with action '.$afile[0].' file ' . $afile[1] . "\n";
			}

			TBGEvent::createNew('vcs_integration', 'new_commit')->trigger(array('commit' => $commit));

			return $output;
		}
	}
