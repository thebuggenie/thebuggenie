<?php

	class configurationActions extends TBGAction
	{

		const CSV_TYPE_ISSUES = 'issues';
		const CSV_TYPE_CLIENTS = 'clients';
		const CSV_TYPE_PROJECTS = 'projects';

		const CSV_PROJECT_NAME             = 'name';
		const CSV_PROJECT_PREFIX           = 'prefix';
		const CSV_PROJECT_SCRUM            = 'scrum';
		const CSV_PROJECT_OWNER            = 'owner';
		const CSV_PROJECT_OWNER_TYPE       = 'owner_type';
		const CSV_PROJECT_LEAD             = 'lead';
		const CSV_PROJECT_LEAD_TYPE        = 'lead_type';
		const CSV_PROJECT_QA               = 'qa';
		const CSV_PROJECT_QA_TYPE          = 'qa_type';
		const CSV_PROJECT_DESCR            = 'descr';
		const CSV_PROJECT_DOC_URL          = 'doc_url';
		const CSV_PROJECT_WIKI_URL         = 'wiki_url';
		const CSV_PROJECT_FREELANCE        = 'freelance';
		const CSV_PROJECT_EN_BUILDS        = 'en_builds';
		const CSV_PROJECT_EN_COMPS         = 'en_comps';
		const CSV_PROJECT_EN_EDITIONS      = 'en_editions';
		const CSV_PROJECT_WORKFLOW_ID      = 'workflow_id';
		const CSV_PROJECT_CLIENT           = 'client';
		const CSV_PROJECT_SHOW_SUMMARY     = 'show_summary';
		const CSV_PROJECT_SUMMARY_TYPE     = 'summary_type';
		const CSV_PROJECT_ISSUETYPE_SCHEME = 'issuetype_scheme';
		const CSV_PROJECT_ALLOW_REPORTING  = 'allow_reporting';
		const CSV_PROJECT_AUTOASSIGN       = 'autoassign';

		const CSV_CLIENT_NAME      = 'name';
		const CSV_CLIENT_EMAIL     = 'email';
		const CSV_CLIENT_TELEPHONE = 'telephone';
		const CSV_CLIENT_FAX       = 'fax';
		const CSV_CLIENT_WEBSITE   = 'website';

		const CSV_ISSUE_TITLE           = 'title';
		const CSV_ISSUE_PROJECT         = 'project';
		const CSV_ISSUE_DESCR           = 'descr';
		const CSV_ISSUE_REPRO           = 'repro';
		const CSV_ISSUE_STATE           = 'state';
		const CSV_ISSUE_STATUS          = 'status';
		const CSV_ISSUE_POSTED_BY       = 'posted_by';
		const CSV_ISSUE_OWNER           = 'owner';
		const CSV_ISSUE_OWNER_TYPE      = 'owner_type';
		const CSV_ISSUE_ASSIGNED        = 'assigned';
		const CSV_ISSUE_ASSIGNED_TYPE   = 'assigned_type';
		const CSV_ISSUE_RESOLUTION      = 'resolution';
		const CSV_ISSUE_ISSUE_TYPE      = 'issue_type';
		const CSV_ISSUE_PRIORITY        = 'priority';
		const CSV_ISSUE_CATEGORY        = 'category';
		const CSV_ISSUE_SEVERITY        = 'severity';
		const CSV_ISSUE_REPRODUCIBILITY = 'reproducability';
		const CSV_ISSUE_VOTES           = 'votes';
		const CSV_ISSUE_PERCENTAGE      = 'percentage';
		const CSV_ISSUE_BLOCKING        = 'blocking';
		const CSV_ISSUE_MILESTONE       = 'milestone';
		
		const CSV_IDENTIFIER_TYPE_USER  = 1;
		const CSV_IDENTIFIER_TYPE_TEAM  = 2;

		/**
		 * Pre-execute function
		 * 
		 * @param TBGRequest 	$request
		 * @param string		$action
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			if (!$request->hasParameter('section')) return;

			// forward 403 if you're not allowed here
			if ($request->isAjaxCall() == false) // for avoiding empty error when an user disables himself its own permissions
			{
				$this->forward403unless(TBGContext::getUser()->canAccessConfigurationPage());
			}
			
			$this->access_level = $this->getAccessLevel($request['section'], 'core');
			
			if (!$request->isAjaxCall())
			{
				$this->getResponse()->setPage('config');
				TBGContext::loadLibrary('ui');
				$this->getResponse()->addBreadcrumb(TBGContext::getI18n()->__('Configure The Bug Genie'), TBGContext::getRouting()->generate('configure'), $this->getResponse()->getPredefinedBreadcrumbLinks('main_links'));
			}
			
		}
		
		/**
		 * Configuration main page
		 * 
		 * @param TBGRequest $request
		 */
		public function runIndex(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$general_config_sections = array();
			$data_config_sections = array();
			$module_config_sections = array();
			
			if (TBGContext::getScope()->getID() == 1)
				$general_config_sections[TBGSettings::CONFIGURATION_SECTION_SCOPES] = array('route' => 'configure_scopes', 'description' => $i18n->__('Scopes'), 'icon' => 'scopes', 'details' => $i18n->__('Scopes are self-contained Bug Genie environments. Configure them here.'));

			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_SETTINGS] = array('route' => 'configure_settings', 'description' => $i18n->__('Settings'), 'icon' => 'general', 'details' => $i18n->__('Every setting in the bug genie can be adjusted in this section.'));
			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_PERMISSIONS] = array('route' => 'configure_permissions', 'description' => $i18n->__('Permissions'), 'icon' => 'permissions', 'details' => $i18n->__('Configure permissions in this section'));
			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_ROLES] = array('route' => 'configure_roles', 'description' => $i18n->__('Roles'), 'icon' => 'roles', 'details' => $i18n->__('Configure roles (permission templates) in this section'));
			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_AUTHENTICATION] = array('route' => 'configure_authentication', 'description' => $i18n->__('Authentication'), 'icon' => 'authentication', 'details' => $i18n->__('Configure the authentication method in this section'));
			
			if (TBGContext::getScope()->isUploadsEnabled())
				$general_config_sections[TBGSettings::CONFIGURATION_SECTION_UPLOADS] = array('route' => 'configure_files', 'description' => $i18n->__('Uploads &amp; attachments'), 'icon' => 'files', 'details' => $i18n->__('All settings related to file uploads are controlled from this section.'));

			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_IMPORT] = array('route' => 'configure_import', 'description' => $i18n->__('Import data'), 'icon' => 'import', 'details' => $i18n->__('Import data from CSV files and other sources.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_PROJECTS] = array('route' => 'configure_projects', 'description' => $i18n->__('Projects'), 'icon' => 'projects', 'details' => $i18n->__('Set up all projects in this configuration section.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUETYPES] = array('icon' => 'issuetypes', 'description' => $i18n->__('Issue types'), 'route' => 'configure_issuetypes', 'details' => $i18n->__('Manage issue types and configure issue fields for each issue type here'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUEFIELDS] = array('icon' => 'resolutiontypes', 'description' => $i18n->__('Issue fields'), 'route' => 'configure_issuefields', 'details' => $i18n->__('Status types, resolution types, categories, custom fields, etc. are configurable from this section.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_WORKFLOW] = array('icon' => 'workflow', 'description' => $i18n->__('Workflow'), 'route' => 'configure_workflow', 'details' => $i18n->__('Set up and edit workflow configuration from this section'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_USERS] = array('route' => 'configure_users', 'description' => $i18n->__('Users, teams, clients &amp; groups'), 'icon' => 'users', 'details' => $i18n->__('Manage users, user groups, clients and user teams from this section.'));
			$module_config_sections[TBGSettings::CONFIGURATION_SECTION_MODULES][] = array('route' => 'configure_modules', 'description' => $i18n->__('Module settings'), 'icon' => 'modules', 'details' => $i18n->__('Manage Bug Genie extensions from this section. New modules are installed from here.'), 'module' => 'core');
			foreach (TBGContext::getModules() as $module)
			{
				if ($module->hasConfigSettings() && $module->isEnabled())
					$module_config_sections[TBGSettings::CONFIGURATION_SECTION_MODULES][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => TBGContext::geti18n()->__($module->getConfigTitle()), 'icon' => $module->getName(), 'details' => TBGContext::geti18n()->__($module->getConfigDescription()), 'module' => $module->getName());
			}
			$this->general_config_sections = $general_config_sections; 
			$this->data_config_sections = $data_config_sections;
			$this->module_config_sections = $module_config_sections;
			$this->outdated_modules = TBGContext::getOutdatedModules();
		}
		
		/**
		 * check for updates
		 * 
		 * @param TBGRequest $request
		 */
		public function runCheckUpdates(TBGRequest $request)
		{
			$data = json_decode(file_get_contents('http://www.thebuggenie.com/updatecheck.php'));
			if (!is_object($data))
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Failed to check for updates'), 'message' => TBGContext::getI18n()->__('The response from The Bug Genie website was invalid')));
			}
			
			$outofdate = false;
			
			// major
			if ($data->maj > TBGSettings::getMajorVer())
			{
				$outofdate = true;
			}
			elseif ($data->min > TBGSettings::getMinorVer() && ($data->maj == TBGSettings::getMajorVer()))
			{
				$outofdate = true;
			}
			elseif ($data->rev > TBGSettings::getRevision() && ($data->maj == TBGSettings::getMajorVer()) && ($data->min == TBGSettings::getMinorVer()))
			{
				$outofdate = true;
			}
			
			if (!$outofdate)
			{
				return $this->renderJSON(array('uptodate' => true, 'title' => TBGContext::getI18n()->__('The Bug Genie is up to date'), 'message' => TBGContext::getI18n()->__('The latest version is %ver%', array('%ver%' => $data->nicever))));
			}
			else
			{
				return $this->renderJSON(array('uptodate' => false, 'title' => TBGContext::getI18n()->__('The Bug Genie is out of date'), 'message' => TBGContext::getI18n()->__('The latest version is %ver%. Update now from www.thebuggenie.com.', array('%ver%' => $data->nicever))));
			}
		}
		
		/**
		 * Configuration import page
		 * 
		 * @param TBGRequest $request
		 */
		public function runImport(TBGRequest $request)
		{
			if ($request->isPost())
			{
				if ($request['import_sample_data'])
				{
					ini_set('memory_limit','64M');
					$users = array();
					
					$user1 = new TBGUser();
					$user1->setUsername('john');
					$user1->setPassword('john');
					$user1->setBuddyname('John');
					$user1->setRealname('John');
					$user1->setActivated();
					$user1->setEnabled();
					$user1->save();
					$users[] = $user1;
					
					$user2 = new TBGUser();
					$user2->setUsername('jane');
					$user2->setPassword('jane');
					$user2->setBuddyname('Jane');
					$user2->setRealname('Jane');
					$user2->setActivated();
					$user2->setEnabled();
					$user2->save();
					$users[] = $user2;
					
					$user3 = new TBGUser();
					$user3->setUsername('jackdaniels');
					$user3->setPassword('jackdaniels');
					$user3->setBuddyname('Jack');
					$user3->setRealname('Jack Daniels');
					$user3->setActivated();
					$user3->setEnabled();
					$user3->save();
					$users[] = $user3;
					
					$project1 = new TBGProject();
					$project1->setName('Sample project 1');
					$project1->setOwner($users[rand(0, 2)]);
					$project1->setLeader($users[rand(0, 2)]);
					$project1->setQaResponsible($users[rand(0, 2)]);
					$project1->setDescription('This is a sample project that is awesome. Try it out!');
					$project1->setHomepage('http://www.google.com');
					$project1->save();
					
					$project2 = new TBGProject();
					$project2->setName('Sample project 2');
					$project2->setOwner($users[rand(0, 2)]);
					$project2->setLeader($users[rand(0, 2)]);
					$project2->setQaResponsible($users[rand(0, 2)]);
					$project2->setDescription('This is the second sample project. Not as awesome as the first one, but still worth a try!');
					$project2->setHomepage('http://www.bing.com');
					$project2->save();

					foreach (array($project1, $project2) as $project)
					{
						for ($cc = 1; $cc <= 5; $cc++)
						{
							$milestone = new TBGMilestone();
							$milestone->setName("Milestone {$cc}");
							$milestone->setProject($project);
							$milestone->setType(TBGMilestone::TYPE_REGULAR);
							if ((bool) rand(0,1))
							{
								$milestone->setScheduledDate(NOW + (100000 * (20 * $cc)));
							}
							$milestone->save();
						}
					}
					
					$p1_milestones = $project1->getMilestones();
					$p2_milestones = $project2->getMilestones();
					
					$issues = array();
					$priorities = TBGPriority::getAll();
					$categories = TBGCategory::getAll();
					$severities = TBGSeverity::getAll();
					$statuses = TBGStatus::getAll();
					$reproducabilities = TBGReproducability::getAll();
					$lorem_ipsum = TBGArticlesTable::getTable()->getArticleByName('LoremIpsum');
					$lorem_ipsum = PublishFactory::article($lorem_ipsum->get(TBGArticlesTable::ID), $lorem_ipsum);
					$lorem_words = explode(' ', $lorem_ipsum->getContent());
					
					foreach (array('bugreport', 'featurerequest', 'enhancement', 'idea') as $issuetype)
					{
						$issuetype = TBGIssuetype::getIssuetypeByKeyish($issuetype);
						for ($cc = 1; $cc <= 10; $cc++)
						{
							$issue1 = new TBGIssue();
							$issue1->setProject($project1);
							$issue1->setPostedBy($users[rand(0, 2)]);
							$issue1->setPosted(NOW - (86400 * rand(1, 30)));
							$title_string = '';
							$description_string = '';
							$rand_length = rand(4, 15);
							$ucnext = true;
							for ($ll = 1; $ll <= $rand_length; $ll++)
							{
								$word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
								$word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
								$title_string .= $word;
								$ucnext = false;
								if ($ll == $rand_length || rand(1, 15) == 5) 
								{
									$title_string .= '.';
									$ucnext = true;
								}
								$title_string .= ' ';
							}
							$rand_length = rand(40, 500);
							$ucnext = true;
							for ($ll = 1; $ll <= $rand_length; $ll++)
							{
								$word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
								$word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
								$description_string .= $word;
								$ucnext = false;
								if ($ll == $rand_length || rand(1, 15) == 5) 
								{
									$description_string .= '.';
									$ucnext = true;
									$description_string .= ($ll != $rand_length && rand(1, 15) == 8) ? "\n\n" : ' ';
								}
								else
								{
									$description_string .= ' ';
								}
							}
							$issue1->setTitle(ucfirst($title_string));
							$issue1->setDescription($description_string);
							$issue1->setIssuetype($issuetype);
							$issue1->setMilestone($p1_milestones[array_rand($p1_milestones)]);
							$issue1->setPriority($priorities[array_rand($priorities)]);
							$issue1->setCategory($categories[array_rand($categories)]);
							$issue1->setSeverity($severities[array_rand($severities)]);
							$issue1->setReproducability($reproducabilities[array_rand($reproducabilities)]);
							$issue1->setPercentCompleted(rand(0, 100));
							$issue1->save();
							$issue1->setStatus($statuses[array_rand($statuses)]);
							if (rand(0, 1)) $issue1->setAssignee($users[array_rand($users)]);
							$issue1->save();
							$issues[] = $issue1;

							$issue2 = new TBGIssue();
							$issue2->setProject($project2);
							$issue2->setPostedBy($users[rand(0, 2)]);
							$issue2->setPosted(NOW - (86400 * rand(1, 30)));
							$title_string = '';
							$description_string = '';
							$rand_length = rand(4, 15);
							$ucnext = true;
							for ($ll = 1; $ll <= $rand_length; $ll++)
							{
								$word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
								$word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
								$title_string .= $word;
								$ucnext = false;
								if ($ll == $rand_length || rand(1, 15) == 5) 
								{
									$title_string .= '.';
									$ucnext = true;
								}
								$title_string .= ' ';
							}
							$rand_length = rand(40, 500);
							$ucnext = true;
							for ($ll = 1; $ll <= $rand_length; $ll++)
							{
								$word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
								$word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
								$description_string .= $word;
								$ucnext = false;
								if ($ll == $rand_length || rand(1, 15) == 5) 
								{
									$description_string .= '.';
									$ucnext = true;
									$description_string .= ($ll != $rand_length && rand(1, 15) == 8) ? "\n\n" : ' ';
								}
								else
								{
									$description_string .= ' ';
								}
							}
							$issue2->setTitle(ucfirst($title_string));
							$issue2->setDescription($description_string);
							$issue2->setIssuetype($issuetype);
							$issue2->setMilestone($p2_milestones[array_rand($p2_milestones)]);
							$issue2->setPriority($priorities[array_rand($priorities)]);
							$issue2->setCategory($categories[array_rand($categories)]);
							$issue2->setSeverity($severities[array_rand($severities)]);
							$issue2->setReproducability($reproducabilities[array_rand($reproducabilities)]);
							$issue2->setPercentCompleted(rand(0, 100));
							if (rand(0, 1)) $issue1->setAssignee($users[array_rand($users)]);
							$issue2->save();
							$issue2->setStatus($statuses[array_rand($statuses)]);
							$issue2->save();
							$issues[] = $issue2;
						}
					}
					
					$rand_issues_to_close = rand(8, 40);
					$resolutions = TBGResolution::getAll();
					
					for ($cc = 1; $cc <= $rand_issues_to_close; $cc++)
					{
						$issue = array_slice($issues, array_rand($issues), 1);
						$issue = $issue[0];
						$issue->setResolution($resolutions[array_rand($resolutions)]);
						$issue->close();
						$issue->save();
					}
					
					$this->imported_data = true;
					$roles = TBGRole::getAll();

					foreach (array($project1, $project2) as $project)
					{
						foreach ($users as $user)
						{
							$project->addAssignee($user, $roles[array_rand($roles)]);
						}
					}
				}
			}

			$project1 = TBGProject::getByKey('sampleproject1');
			$project2 = TBGProject::getByKey('sampleproject2');
			$this->canimport = (!$project1 instanceof TBGProject && !$project2 instanceof TBGProject);
		}
		
		/**
		 * Configure general and server settings
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runSettings(TBGRequest $request)
		{
			if (TBGContext::getRequest()->isPost())
			{
				$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
				$settings = array(TBGSettings::SETTING_THEME_NAME, TBGSettings::SETTING_ALLOW_USER_THEMES, TBGSettings::SETTING_ONLINESTATE, TBGSettings::SETTING_ENABLE_GRAVATARS,
								TBGSettings::SETTING_OFFLINESTATE, TBGSettings::SETTING_AWAYSTATE, TBGSettings::SETTING_AWAYSTATE, TBGSettings::SETTING_IS_SINGLE_PROJECT_TRACKER,
								TBGSettings::SETTING_REQUIRE_LOGIN, TBGSettings::SETTING_ALLOW_REGISTRATION, TBGSettings::SETTING_ALLOW_OPENID, TBGSettings::SETTING_USER_GROUP,
								TBGSettings::SETTING_RETURN_FROM_LOGIN, TBGSettings::SETTING_RETURN_FROM_LOGOUT, TBGSettings::SETTING_IS_PERMISSIVE_MODE,
								TBGSettings::SETTING_REGISTRATION_DOMAIN_WHITELIST, TBGSettings::SETTING_SHOW_PROJECTS_OVERVIEW, TBGSettings::SETTING_KEEP_COMMENT_TRAIL_CLEAN,
								TBGSettings::SETTING_TBG_NAME, TBGSettings::SETTING_TBG_NAME_HTML, TBGSettings::SETTING_DEFAULT_CHARSET, TBGSettings::SETTING_DEFAULT_LANGUAGE,
								TBGSettings::SETTING_SERVER_TIMEZONE, TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_LANGUAGE, TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL,
								TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_NUMBERING, TBGSettings::SETTING_PREVIEW_COMMENT_IMAGES, TBGSettings::SETTING_HEADER_LINK,
								TBGSettings::SETTING_MAINTENANCE_MESSAGE, TBGSettings::SETTING_MAINTENANCE_MODE, TBGSettings::SETTING_ICONSET);
				
				foreach ($settings as $setting)
				{
					if (TBGContext::getRequest()->getParameter($setting) !== null)
					{
						$value = TBGContext::getRequest()->getParameter($setting);
						switch ($setting)
						{
							case TBGSettings::SETTING_TBG_NAME:
								$value = TBGContext::getRequest()->getParameter($setting, null, false);
								break;
							case  TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL:
								if (!is_numeric($value) || $value < 1)
								{
									$this->getResponse()->setHttpStatus(400);
									return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid setting for highlighting interval')));
								}
								break;
							case TBGSettings::SETTING_DEFAULT_CHARSET:
								TBGContext::loadLibrary('common');
								if ($value && !tbg_check_syntax($value, "CHARSET"))
								{
										$this->getResponse()->setHttpStatus(400);
										return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid setting for charset')));
								}
								break;
						}
						TBGSettings::saveSetting($setting, $value);
					}
				}
				return $this->renderJSON(array('title' => TBGContext::getI18n()->__('All settings saved')));
			}
		}

		/**
		 * Configure projects
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjects(TBGRequest $request)
		{
			$this->allProjects = TBGProject::getAll();
		}
		
		/**
		 * Configure issue fields
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureIssuefields(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$builtin_types = array();
			$builtin_types['status'] = array('description' => $i18n->__('Status types'), 'key' => 'status');
			$builtin_types['resolution'] = array('description' => $i18n->__('Resolution types'), 'key' => 'resolution');
			$builtin_types['priority'] = array('description' => $i18n->__('Priority levels'), 'key' => 'priority');
			$builtin_types['severity'] = array('description' => $i18n->__('Severity levels'), 'key' => 'severity');
			$builtin_types['category'] = array('description' => $i18n->__('Categories'), 'key' => 'category');
			$builtin_types['reproducability'] = array('description' => $i18n->__('Reproducability'), 'key' => 'reproducability');

			$this->builtin_types = $builtin_types;
			$this->custom_types = TBGCustomDatatype::getAll();
		}

		/**
		 * Configure issue fields
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureIssuetypes(TBGRequest $request)
		{
			$this->mode = $request->getParameter('mode', 'issuetypes');
			if ($this->mode == 'issuetypes' || $this->mode == 'scheme')
			{
				$this->issue_types = TBGIssuetype::getAll();
				$this->icons = TBGIssuetype::getIcons();
			}
			elseif ($this->mode == 'schemes')
			{
				$this->issue_type_schemes = TBGIssuetypeScheme::getAll();
			}
			if ($request->hasParameter('scheme_id'))
			{
				$this->scheme = TBGContext::factory()->TBGIssuetypeScheme((int) $request['scheme_id']);
				if ($this->mode == 'copy_scheme')
				{
					if ($new_name = $request['new_name'])
					{
						$new_scheme = new TBGIssuetypeScheme();
						$new_scheme->setName($new_name);
						$new_scheme->save();
						foreach ($this->scheme->getIssuetypes() as $issuetype)
						{
							$new_scheme->setIssuetypeEnabled($issuetype);
							$new_scheme->setIssuetypeRedirectedAfterReporting($issuetype, $this->scheme->isIssuetypeRedirectedAfterReporting($issuetype));
							$new_scheme->setIssuetypeReportable($issuetype, $this->scheme->isIssuetypeReportable($issuetype));
						}
						TBGIssueFieldsTable::getTable()->copyBySchemeIDs($this->scheme->getID(), $new_scheme->getID());
						return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/issuetypescheme', array('scheme' => $new_scheme))));
					}
					else
					{
						$this->error = TBGContext::getI18n()->__('Please enter a valid name');
					}
				}
				elseif ($this->mode == 'delete_scheme')
				{
					$this->scheme->delete();
					return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The issuetype scheme was deleted')));
				}
			}
		}

		/**
		 * Get issue type options for a specific issue type
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuetypesGetOptionsForScheme(TBGRequest $request)
		{
			return $this->renderComponent('issuetypeschemeoptions', array('id' => $request['id'], 'scheme_id' => $request['scheme_id']));
		}

		/**
		 * Perform an action on an issue type
		 * 
		 * @param TBGRequest $request 
		 */
		public function runConfigureIssuetypesAction(TBGRequest $request)
		{
			if ($request->hasParameter('scheme_id'))
			{
				$this->scheme = TBGContext::factory()->TBGIssuetypeScheme((int) $request['scheme_id']);
			}
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
			switch ($request['mode'])
			{
				case 'add':
					if ($request['name'])
					{
						$issuetype = new TBGIssuetype();
						$issuetype->setName($request['name']);
						$issuetype->setIcon($request['icon']);
						$issuetype->save();
						return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Issue type created'), 'content' => $this->getComponentHTML('issuetype', array('type' => $issuetype))));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid name for the issue type')));
					break;
				case 'update':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request['id'])) instanceof TBGIssuetype)
					{
						if ($this->scheme instanceof TBGIssuetypeScheme)
						{
							$this->scheme->setIssuetypeRedirectedAfterReporting($issuetype, $request['redirect_after_reporting']);
							$this->scheme->setIssuetypeReportable($issuetype, $request['reportable']);
							return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The issue type details were updated'), 'description' => $issuetype->getDescription(), 'name' => $issuetype->getName()));
						}
						elseif ($request['name'])
						{
							$issuetype->setDescription($request['description']);
							$issuetype->setName($request['name']);
							$issuetype->setIcon($request['icon']);
							$issuetype->save();
							return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The issue type was updated'), 'description' => $issuetype->getDescription(), 'name' => $issuetype->getName()));
						}
						else
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid name for the issue type')));
						}
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid issue type')));
					break;
				case 'updatechoices':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request['id'])) instanceof TBGIssuetype)
					{
						$this->scheme->clearAvailableFieldsForIssuetype($issuetype);
						foreach ($request->getParameter('field', array()) as $key => $details)
						{
							$this->scheme->setFieldAvailableForIssuetype($issuetype, $key, $details);
						}
						return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Available choices updated')));
					}
					else
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid issue type')));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Not implemented yet')));
					break;
				case 'delete':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request['id'])) instanceof TBGIssuetype)
					{
						$issuetype->delete();
						return $this->renderJSON(array('message' => TBGContext::getI18n()->__('Issue type deleted')));
					}
					else
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid issue type')));
					}
					break;
				case 'toggletype':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request['id'])) instanceof TBGIssuetype)
					{
						if ($this->scheme instanceof TBGIssuetypeScheme)
						{
							$this->scheme->setIssuetypeEnabled($issuetype, ($request['state'] == 'enable'));
							return $this->renderJSON(array('issuetype_id' => $issuetype->getID()));
						}
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid action for this issue type / scheme')));
					break;
				default:
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid action for this issue type')));
			}
		}

		/**
		 * Get issue fields list for a specific field type
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuefieldsGetOptions(TBGRequest $request)
		{
			return $this->renderComponent('issuefields', array('type' => $request['type'], 'access_level' => $this->access_level));
		}

		/**
		 * Add or delete an issue field option
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuefieldsAction(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
			$types = TBGDatatype::getTypes();

			switch ($request['mode'])
			{
				case 'saveorder':
					$itemtype = $request['type'];
					if (array_key_exists($itemtype, $types))
					{
						TBGListTypesTable::getTable()->saveOptionOrder($request[$itemtype.'_list'], $itemtype);
					}
					else
					{
						$customtype = TBGCustomDatatype::getByKey($request['type']);
						TBGCustomFieldOptionsTable::getTable()->saveOptionOrder($request[$itemtype.'_list'], $customtype->getID());
					}
					return $this->renderJSON('ok');
					break;
				case 'add':
					if ($request['name'])
					{
						if (array_key_exists($request['type'], $types))
						{
							$type_name = $types[$request['type']];
							$item = new $type_name();
							$item->setName($request['name']);
							$item->setItemdata($request['itemdata']);
							$item->save();
						}
						else
						{
							$customtype = TBGCustomDatatype::getByKey($request['type']);
							$item = $customtype->createNewOption($request['name'], $request['value'], $request['itemdata']);
						}
						return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The option was added'), 'content' => $this->getTemplateHTML('issuefield', array('item' => $item, 'access_level' => $this->access_level, 'type' => $request['type']))));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid name')));
				case 'edit':
					if ($request['name'])
					{
						if (array_key_exists($request['type'], $types))
						{
							$labname = $types[$request['type']];
							$item = TBGContext::factory()->$labname($request['id']);
						}
						else
						{
							$customtype = TBGCustomDatatype::getByKey($request['type']);
							$item = TBGContext::factory()->TBGCustomDatatypeOption($request['id']);
						}
						if ($item instanceof TBGDatatypeBase)
						{
							$item->setName($request['name']);
							$item->setItemdata($request['itemdata']);
							if (!$item->isBuiltin())
							{
								$item->setValue($request['value']);
							}
							$item->save();
							return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The option was updated')));
						}
						else
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid id')));
						}
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid name')));
				case 'delete':
					if ($request->hasParameter('id'))
					{
						if (array_key_exists($request['type'], $types))
						{
							$classname = 'TBG'.ucfirst($request['type']);
							$item = TBGContext::factory()->$classname($request['id'])->delete();
							return $this->renderJSON(array('title' => $i18n->__('The option was deleted')));
						}
						else
						{
							\b2db\Core::getTable('TBGCustomFieldOptionsTable')->doDeleteById($request['id']);
							return $this->renderJSON(array('title' => $i18n->__('The option was deleted')));
						}
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $i18n->__('Invalid id or type')));
					break;
			}
		}

		/**
		 * Add or delete a custom type
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuefieldsCustomTypeAction(TBGRequest $request)
		{
			switch ($request['mode'])
			{
				case 'add':
					if ($request['name'] != '')
					{
						try
						{
							$customtype = new TBGCustomDatatype();
							$customtype->setName($request['name']);
							$customtype->setItemdata($request['label']);
							$customtype->setDescription($request['label']);
							$customtype->setType($request['field_type']);
							$customtype->save();
							return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The custom field was added'), 'content' => $this->getComponentHTML('issuefields_customtype', array('type_key' => $customtype->getKey(), 'type' => $customtype))));
						}
						catch (Exception $e)
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => $e->getMessage() /*TBGContext::getI18n()->__('You need to provide a unique custom field name (key already exists)')*/));
						}
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid name')));
					break;
				case 'update':
					if ($request['name'] != '')
					{
						$customtype = TBGCustomDatatype::getByKey($request['type']);
						if ($customtype instanceof TBGCustomDatatype)
						{
							$customtype->setDescription($request['description']);
							$customtype->setInstructions($request['instructions']);
							$customtype->setName($request['name']);
							$customtype->save();
							return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The custom field was updated'), 'description' => $customtype->getDescription(), 'instructions' => $customtype->getInstructions(), 'name' => $customtype->getName()));
						}
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You need to provide a custom field key that already exists')));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('Please provide a valid name')));
					break;
				case 'delete':
					$customtype = TBGCustomDatatype::getByKey($request['type']);
					if ($customtype instanceof TBGCustomDatatype)
					{
						$customtype->delete();
						return $this->renderJSON(array('title' => TBGContext::getI18n()->__('The custom field was deleted')));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('You need to provide a custom field key that already exists')));
					break;
			}
		}

		/**
		 * Configure modules
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureModules(TBGRequest $request)
		{
			$this->module_message = TBGContext::getMessageAndClear('module_message');
			$this->module_error = TBGContext::getMessageAndClear('module_error');
			$this->modules = TBGContext::getModules();
			$this->uninstalled_modules = TBGContext::getUninstalledModules();
			$this->outdated_modules = TBGContext::getOutdatedModules();
		}

		/**
		 * Add a project (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddProject(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if (!TBGContext::getScope()->hasProjectsAvailable())
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array("error" => $i18n->__("There are no more projects available in this instance")));
			}
			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				if (($p_name = $request['p_name']) && trim($p_name) != '')
				{
					try
					{
						$project = new TBGProject();
						$project->setName($p_name);
						$project->save();
						return $this->renderJSON(array('message' => $i18n->__('The project has been added'), 'content' => $this->getTemplateHTML('projectbox', array('project' => $project, 'access_level' => $this->access_level)), 'total_count' => TBGProject::getProjectsCount(), 'more_available' => TBGContext::getScope()->hasProjectsAvailable()));
					}
					catch (InvalidArgumentException $e)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array("error" => $i18n->__('A project with the same key already exists')));
					}
					catch (Exception $e)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array("error" => $i18n->__('An error occurred: '. $e->getMessage())));
					}
				}
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array("error" => $i18n->__('Please specify a valid project name')));
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array("error" => $i18n->__("You don't have access to add projects")));
		}
		
		/**
		 * Get edit form for user
		 */
		public function runGetUserEditForm(TBGRequest $request)
		{
			return $this->renderJSON(array("content" => $this->getTemplateHtml('finduser_row_editable', array('user' => TBGContext::factory()->TBGUser($request['user_id'])))));
		}	
			
		/**
		 * Delete a project
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runDeleteProject(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				try
				{
					$theProject = TBGContext::factory()->TBGProject($request['project_id']);
					$theProject->setDeleted();
					$theProject->save();
					return $this->renderJSON(array('title' => $i18n->__('The project was deleted'), 'total_count' => TBGProject::getProjectsCount(), 'more_available' => TBGContext::getScope()->hasProjectsAvailable()));
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $i18n->__('An error occured') . ': ' . $e->getMessage()));
				}
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array("error" => $i18n->__("You don't have access to remove projects")));
		}
		
		/**
		 * Handle archive functiions
		 * 
		 * @param bool $archived Status
		 * @param TBGRequest $request The request object
		 */
		protected function _setArchived($archived, TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				try
				{
					$theProject = TBGContext::factory()->TBGProject($request['project_id']);
					$theProject->setArchived($archived);
					$theProject->save();
					
					$projectbox = $this->getTemplateHtml('projectbox', array('project' => $theProject, 'access_level' => $this->access_level));
					return $this->renderJSON(array('message' => $i18n->__('Project successfully updated'), 'box' => $projectbox));
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $i18n->__('An error occured') . ': ' . $e->getMessage()));
				}
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array("error" => $i18n->__("You don't have access to archive projects")));
		}
		
		/**
		 * Archive
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runArchiveProject(TBGRequest $request)
		{
			return $this->_setArchived(true, $request);
		}
		
		/**
		 * Unarchive
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runUnarchiveProject(TBGRequest $request)
		{
			// Don't unarchive if we will have too many projects
			if (!TBGContext::getScope()->hasProjectsAvailable())
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array("error" => $i18n->__("There are no more projects available in this instance")));
			}
			
			return $this->_setArchived(false, $request);
		}

		/**
		 * Perform an action on a module
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runModuleAction(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
			
			try
			{
				if ($request['mode'] == 'install' && file_exists(THEBUGGENIE_MODULES_PATH . $request['module_key'] . DS . 'module'))
				{
					if (TBGModule::installModule($request['module_key']))
					{
						TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The module "%module_name%" was installed successfully', array('%module_name%' => $request['module_key'])));
					}
					else
					{
						TBGContext::setMessage('module_error', TBGContext::getI18n()->__('There was an error install the module %module_name%', array('%module_name%' => $request['module_key'])));
					}
				}
				else if ($request['mode'] == 'upload')
				{
					$archive = $request->getUploadedFile('archive');	
					if ($archive == null || $archive['error'] != UPLOAD_ERR_OK || !preg_match('/application\/(x-)?zip/i', $archive['type']))
					{
						TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Invalid or empty archive uploaded'));
					}
					else
					{
						$module_name = TBGModule::uploadModule($archive);
						TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The module "%module_name%" was uploaded successfully', array('%module_name%' => $module_name)));
					}
				}				
				else
				{
					$module = TBGContext::getModule($request['module_key']);
					if (!$module->isCore())
						switch ($request['mode'])
						{
							case 'disable':
								if ($module->getType() !== TBGModule::MODULE_AUTH):
									$module->disable();
								endif;
								break;
							case 'enable':
								if ($module->getType() !== TBGModule::MODULE_AUTH):
									$module->enable();
								endif;
								break;
							case 'uninstall':
								$module->uninstall();
								TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The module "%module_name%" was uninstalled successfully', array('%module_name%' => $module->getName())));
								break;
							case 'update':
								try
								{
									$module->upgrade();
									TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The module "%module_name%" was successfully upgraded and can now be used again', array('%module_name%' => $module->getName())));
								}
								catch (Exception $e)
								{ throw $e;
									TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The module "%module_name%" was not successfully upgraded', array('%module_name%' => $module->getName())));	
								}
								break;
						}
				}
			}
			catch (Exception $e)
			{ throw $e;
				TBGLogging::log('Trying to run action ' . $request['mode'] . ' on module ' . $request['module_key'] . ' made an exception: ' . $e->getMessage(), TBGLogging::LEVEL_FATAL);
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('This module (%module_name%) does not exist', array('%module_name%' => $request['module_key'])));
			}
			$this->forward(TBGContext::getRouting()->generate('configure_modules'));
		}

		/**
		 * Get permissions info for a single permission key
		 *
		 * @param TBGRequest $request
		 */
		public function runGetPermissionsInfo(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				return $this->renderJSON(array('content' => $this->getComponentHTML('configuration/permissionsblock', array('base_id' => $request['base_id'], 'permissions_list' => $request['permissions_list'], 'mode' => $request['mode'], 'target_id' => $request['target_id'], 'user_id' => $request['user_id'], 'module' => $request['target_module'], 'access_level' => $this->access_level))));
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array("error" => $i18n->__("You don't have access to modify permissions")));
		}

		public function runSetPermission(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				$uid = 0;
				$gid = 0;
				$tid = 0;
				switch ($request['target_type'])
				{
					case 'user':
						$uid = $request['item_id'];
						break;
					case 'group':
						$gid = $request['item_id'];
						break;
					case 'team':
						$tid = $request['item_id'];
						break;
				}

				switch ($request['mode'])
				{
					case 'allowed':
						TBGContext::setPermission($request['key'], $request['target_id'], $request['target_module'], $uid, $gid, $tid, true);
						break;
					case 'denied':
						TBGContext::setPermission($request['key'], $request['target_id'], $request['target_module'], $uid, $gid, $tid, false);
						break;
					case 'unset':
						TBGContext::removePermission($request['key'], $request['target_id'], $request['target_module'], $uid, $gid, $tid);
						break;
				}
				return $this->renderJSON(array('content' => $this->getComponentHTML('configuration/permissionsinfoitem', array('key' => $request['key'], 'target_id' => $request['target_id'], 'type' => $request['target_type'], 'mode' => $request['template_mode'], 'item_id' => $request['item_id'], 'module' => $request['target_module'], 'access_level' => $this->access_level))));
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array("error" => $i18n->__("You don't have access to modify permissions")));
		}
		
		/**
		 * Configure a module
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureModule(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
			
			try
			{
				$module = TBGContext::getModule($request['config_module']);
				if (!$module->isEnabled())
				{
					throw new Exception('disabled');
				}
				elseif (!$module->hasConfigSettings())
				{
					throw new Exception('module not configurable');
				}
				else
				{
					if ($request->isPost() && $this->access_level == TBGSettings::ACCESS_FULL)
					{
						try
						{
							$module->postConfigSettings($request);
							if (!TBGContext::hasMessage('module_message'))
							{
								TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Settings saved successfully'));
							}
						}
						catch (Exception $e)
						{
							TBGContext::setMessage('module_error', $e->getMessage());
						}
						$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => $request['config_module'])));
					}
					$this->module = $module;
				}
			}
			catch (Exception $e)
			{
				TBGLogging::log('Trying to configure module ' . $request['config_module'] . " which isn't configurable", 'main', TBGLogging::LEVEL_FATAL);
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The module "%module_name%" is not configurable', array('%module_name%' => $request['config_module'])));
				$this->forward(TBGContext::getRouting()->generate('configure_modules'));
			}
			$this->module_message = TBGContext::getMessageAndClear('module_message');
			$this->module_error = TBGContext::getMessageAndClear('module_error');
			$this->module_error_details = TBGContext::getMessageAndClear('module_error_details');
		}

		public function runConfigurePermissions(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
		}

		public function runConfigureUploads(TBGRequest $request)
		{
			$this->uploads_enabled = TBGContext::getScope()->isUploadsEnabled();
			if ($this->uploads_enabled && $request->isPost())
			{
				$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
				if ($request['enable_uploads'])
				{
					if ($request['upload_storage'] == 'files' && (bool) $request['enable_uploads'])
					{
						if(!is_dir($request['upload_localpath']))
						{
							mkdir($request['upload_localpath'], 0744, true);
						}
						if (!is_writable($request['upload_localpath']))
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__("The upload path isn't writable")));
						}
					}

					if (!is_numeric($request['upload_max_file_size']))
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__("The maximum file size must be a number")));
					}

					$settings = array('upload_restriction_mode', 'upload_extensions_list', 'upload_max_file_size', 'upload_storage', 'upload_localpath');

					foreach ($settings as $setting)
					{
						if (TBGContext::getRequest()->hasParameter($setting))
						{
							TBGSettings::saveSetting($setting, TBGContext::getRequest()->getParameter($setting));
						}
					}
				}

				TBGSettings::saveSetting('enable_uploads', TBGContext::getRequest()->getParameter('enable_uploads'));

				return $this->renderJSON(array('title' => TBGContext::getI18n()->__('All settings saved')));
			}
		}
		
		public function runConfigureAuthentication(TBGRequest $request)
		{
			$modules = array();
			$allmods = TBGContext::getModules();
			foreach ($allmods as $mod)
			{
				if ($mod->getType() == TBGModule::MODULE_AUTH)
				{
					$modules[] = $mod;
				}
			}
			$this->modules = $modules;
		}
		
		public function runSaveAuthentication(TBGRequest $request)
		{
			if (TBGContext::getRequest()->isPost())
			{
				$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
				$settings = array(TBGSettings::SETTING_AUTH_BACKEND, 'register_message', 'forgot_message', 'changepw_message', 'changedetails_message');
				
				foreach ($settings as $setting)
				{
					if (TBGContext::getRequest()->getParameter($setting) !== null)
					{
						$value = TBGContext::getRequest()->getParameter($setting);
						TBGSettings::saveSetting($setting, $value);
					}
				}
			}
		}
		
		public function runConfigureUsers(TBGRequest $request)
		{
			$this->groups = TBGGroup::getAll();
			$this->teams = TBGTeam::getAll();
			$this->clients = TBGClient::getall();
			$this->finduser = $request['finduser'];
		}

		public function runDeleteGroup(TBGRequest $request)
		{
			try
			{
				if (in_array($request['group_id'], TBGSettings::getDefaultGroupIDs()))
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete the default groups"));
				}
				
				try
				{
					$group = TBGContext::factory()->TBGGroup($request['group_id']);
				}
				catch (Exception $e) { }
				if (!$group instanceof TBGGroup)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete this group"));
				}
				if ($group->isDefaultUserGroup())
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete the group for the default user"));
				}
				$group->delete();
				return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The group was deleted')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runAddGroup(TBGRequest $request)
		{
			try
			{
				$mode = $request['mode'];
				if ($group_name = $request['group_name'])
				{
					if ($mode == 'clone')
					{
						try
						{
							$old_group = TBGContext::factory()->TBGGroup($request['group_id']);
						}
						catch (Exception $e) { }
						if (!$old_group instanceof TBGGroup)
						{
							throw new Exception(TBGContext::getI18n()->__("You cannot clone this group"));
						}
					}
					if (TBGGroup::doesGroupNameExist(trim($group_name)))
					{
						throw new Exception(TBGContext::getI18n()->__("Please enter a group name that doesn't already exist"));
					}
					$group = new TBGGroup();
					$group->setName($group_name);
					$group->save();
					if ($mode == 'clone')
					{
						if ($request['clone_permissions'])
						{
							TBGPermissionsTable::getTable()->cloneGroupPermissions($old_group->getID(), $group->getID());
						}
						$message = TBGContext::getI18n()->__('The group was cloned');
					}
					else
					{
						$message = TBGContext::getI18n()->__('The group was added');
					}
					return $this->renderJSON(array('message' => $message, 'content' => $this->getTemplateHTML('configuration/groupbox', array('group' => $group))));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a group name'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runGetGroupMembers(TBGRequest $request)
		{
			try
			{
				$group = TBGContext::factory()->TBGGroup((int) $request['group_id']);
				$users = $group->getMembers();
				return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/groupuserlist', array('users' => $users))));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}
		
		public function runDeleteUser(TBGRequest $request)
		{
			try
			{
				try
				{
					$return_options = array();
					$user = TBGContext::factory()->TBGUser($request['user_id']);
					if ($user->getGroup() instanceof TBGGroup)
					{
						$return_options['update_groups'] = array('ids' => array(), 'membercounts' => array());
						$group_id = $user->getGroup()->getID();
						$return_options['update_groups']['ids'][] = $group_id;
						$return_options['update_groups']['membercounts'][$group_id] = $user->getGroup()->getNumberOfMembers();
					}
					if (count($user->getTeams()))
					{
						$return_options['update_teams'] = array('ids' => array(), 'membercounts' => array());
						foreach ($user->getTeams() as $team)
						{
							$team_id = $team->getID();
							$return_options['update_teams']['ids'][] = $team_id;
							$return_options['update_teams']['membercounts'][$team_id] = $team->getNumberOfMembers();
						}
					}
					if (in_array($user->getID(), array(1, TBGSettings::getDefaultUserID())))
					{
						throw new Exception(TBGContext::getI18n()->__("You cannot delete this system user"));
					}
				}
				catch (Exception $e) { }
				if (!$user instanceof TBGUser)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete this user"));
				}
				if (TBGContext::getScope()->isDefault())
				{
					$user->markAsDeleted();
					$user->save();
					$return_options['message'] = TBGContext::getI18n()->__('The user was deleted');
				}
				else
				{
					$user->removeScope(TBGContext::getScope()->getID());
					$return_options['message'] = TBGContext::getI18n()->__('The user has been removed from this scope');
				}
				$return_options['total_count'] = TBGUser::getUsersCount();
				$return_options['more_available'] = TBGContext::getScope()->hasUsersAvailable();
				
				return $this->renderJSON($return_options);
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runDeleteTeam(TBGRequest $request)
		{
			try
			{
				try
				{
					$team = TBGContext::factory()->TBGTeam($request['team_id']);
				}
				catch (Exception $e) { }
				if (!$team instanceof TBGTeam)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete this team"));
				}
				$team->delete();
				return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The team was deleted'), 'total_count' => TBGTeam::countAll(), 'more_available' => TBGContext::getScope()->hasTeamsAvailable()));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runAddTeam(TBGRequest $request)
		{
			try
			{
				$mode = $request['mode'];
				if ($team_name = $request['team_name'])
				{
					if ($mode == 'clone')
					{
						try
						{
							$old_team = TBGContext::factory()->TBGTeam($request['team_id']);
						}
						catch (Exception $e) { }
						if (!$old_team instanceof TBGTeam)
						{
							throw new Exception(TBGContext::getI18n()->__("You cannot clone this team"));
						}
					}
					if (TBGTeam::doesTeamNameExist(trim($team_name)))
					{
						throw new Exception(TBGContext::getI18n()->__("Please enter a team name that doesn't already exist"));
					}
					$team = new TBGTeam();
					$team->setName($team_name);
					$team->save();
					if ($mode == 'clone')
					{
						if ($request['clone_permissions'])
						{
							TBGPermissionsTable::getTable()->cloneTeamPermissions($old_team->getID(), $team->getID());
						}
						if ($request['clone_memberships'])
						{
							TBGTeamMembersTable::getTable()->cloneTeamMemberships($old_team->getID(), $team->getID());
						}
						$message = TBGContext::getI18n()->__('The team was cloned');
					}
					else
					{
						$message = TBGContext::getI18n()->__('The team was added');
					}
					return $this->renderJSON(array('message' => $message, 'content' => $this->getTemplateHTML('configuration/teambox', array('team' => $team)), 'total_count' => TBGTeam::countAll(), 'more_available' => TBGContext::getScope()->hasTeamsAvailable()));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a team name'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runGetTeamMembers(TBGRequest $request)
		{
			try
			{
				$team = TBGContext::factory()->TBGTeam((int) $request['team_id']);
				$users = $team->getMembers();
				return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/teamuserlist', array('users' => $users))));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runFindUsers(TBGRequest $request)
		{
			$this->too_short = false;
			$findstring = $request['findstring'];
			if (mb_strlen($findstring) >= 1)
			{
				$this->users = TBGUsersTable::getTable()->findInConfig($findstring);
				$this->total_results = count($this->users);
			}
			else
			{
				$this->too_short = true;
			}
			switch ($findstring)
			{
				case 'unactivated':
					$this->findstring = TBGContext::getI18n()->__('Unactivated users');
					break;
				case 'newusers':
					$this->findstring = TBGContext::getI18n()->__('New users');
					break;
				case 'all':
					$this->findstring = TBGContext::getI18n()->__('All users');
					break;
				default:
					$this->findstring = $findstring;
			}
			
		}

		public function runAddUser(TBGRequest $request)
		{
			try
			{
				if (!TBGContext::getScope()->hasUsersAvailable())
				{
					throw new Exception(TBGContext::getI18n()->__('This instance of The Bug Genie cannot add more users'));
				}
				
				if ($username = $request['username'])
				{
					if (!TBGUser::isUsernameAvailable($username))
					{
						if ($request->getParameter('mode') == 'import')
						{
							$user = TBGUser::getByUsername($username);
							$user->addScope(TBGContext::getScope());
							return $this->renderJSON(array('imported' => true, 'message' => $this->getI18n()->__('The user was successfully added to this scope (pending user confirmation)')));
						}
						elseif (TBGContext::getScope()->isDefault())
						{
							throw new Exception(TBGContext::getI18n()->__('This username already exists'));
						}
						else
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('allow_import' => true));
						}
					}
					$user = new TBGUser();
					$user->setUsername($username);
					$user->setRealname($username);
					$user->setBuddyname($username);
					$user->setEnabled();
					$user->setActivated();
					$user->setPassword(TBGUser::hashPassword(TBGUser::createPassword()));
					$user->setJoined();
					$user->save();
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a username'));
				}
				$this->getResponse()->setTemplate('configuration/findusers');
				$this->too_short = false;
				$this->created_user = true;
				$this->users = array($user);
				$this->total_results = 1;
				$this->title = TBGContext::getI18n()->__('User %username% created', array('%username%' => $username));
				$this->total_count = TBGUser::getUsersCount();
				$this->more_available = TBGContext::getScope()->hasUsersAvailable();
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runUpdateUser(TBGRequest $request)
		{
			try
			{
				$user = TBGContext::factory()->TBGUser($request['user_id']);
				if ($user instanceof TBGUser)
				{
					if (!$user->isConfirmedMemberOfScope(TBGContext::getScope()))
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This user is not a confirmed member of this scope')));
					}
					if (!empty($request['username'])) {
						$testuser = TBGUser::getByUsername($request['username']);
						if (!$testuser instanceof TBGUser || $testuser->getID() == $user->getID())
						{
							$user->setUsername($request['username']);
						}
						else
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This username is already taken')));
						}
					}
					$password_changed = false;
					if ($request['password_action'] == 'change' && $request['new_password_1'] && $request['new_password_2'])
					{
						if ($request['new_password_1'] == $request['new_password_2'])
						{
							$user->setPassword($request['new_password_1']);
							$password_changed = true;
						}
						else
						{
							$this->getResponse()->setHttpStatus(400);
							return $this->renderJSON(array( 'error' => TBGContext::getI18n()->__('Please enter the new password twice')));
						}
					}
					elseif ($request['password_action'] == 'random')
					{
						$random_password = TBGUser::createPassword();
						$user->setPassword($random_password);
						$password_changed = true;
					}
					if (isset($request['realname'])) {
						$user->setRealname($request['realname']);
					}
					$return_options = array();
					try
					{
						if ($group = TBGContext::factory()->TBGGroup($request['group']))
						{
							if ($user->getGroupID() != $group->getID())
							{
								$groups = array($user->getGroupID(), $group->getID());
								$return_options['update_groups'] = array('ids' => array(), 'membercounts' => array());
							}
							$user->setGroup($group);
						}
					}
					catch (Exception $e)
					{
						throw new Exception(TBGContext::getI18n()->__('Invalid user group'));
					}
					
					$existing_teams = array_keys($user->getTeams());
					$new_teams = array();
					$user->clearTeams();
					try
					{
						foreach ($request->getParameter('teams', array()) as $team_id => $team)
						{
							if ($team = TBGContext::factory()->TBGTeam($team_id))
							{
								$new_teams[] = $team_id;
								$user->addToTeam($team);
							}
						}
					}
					catch (Exception $e)
					{
						throw new Exception(TBGContext::getI18n()->__('One or more teams were invalid'));
					}
					
					try
					{
						$user->clearClients();
						foreach ($request->getParameter('clients', array()) as $client_id => $client)
						{
							if ($client = TBGContext::factory()->TBGClient($client_id))
							{
								$new_clients[] = $client_id;
								$user->addToClient($client);
							}
						}
					}
					catch (Exception $e)
					{
						throw new Exception(TBGContext::getI18n()->__('One or more clients were invalid'));
					}
					if (isset($request['nickname'])) {
						$user->setBuddyname($request['nickname']);
					}
					if (isset($request['email'])) {
						$user->setEmail($request['email']);
					}
					if (isset($request['homepage'])) {
						$user->setHomepage($request['homepage']);
					}
					if (TBGContext::getScope()->isDefault())
					{
						$user->setActivated((bool) $request['activated']);
						$user->setEnabled((bool) $request['enabled']);
					}
					$user->save();
					if (isset($groups))
					{
						foreach ($groups as $group_id)
						{
							if (!$group_id) continue;
							$return_options['update_groups']['ids'][] = $group_id;
							$return_options['update_groups']['membercounts'][$group_id] = TBGContext::factory()->TBGGroup($group_id)->getNumberOfMembers();
						}
					}
					if ($new_teams != $existing_teams)
					{
						$new_team_ids = array_diff($new_teams, $existing_teams);
						$existing_team_ids = array_diff($existing_teams, $new_teams);
						$teams_to_update = array_merge($new_team_ids, $existing_team_ids);
						$return_options['update_teams'] = array('ids' => array(), 'membercounts' => array());
						foreach ($teams_to_update as $team_id)
						{
							$return_options['update_teams']['ids'][] = $team_id;
							$return_options['update_teams']['membercounts'][$team_id] = TBGContext::factory()->TBGTeam($team_id)->getNumberOfMembers();
						}
					}
					$template_options = array('user' => $user);
					if (isset($random_password))
					{
						$template_options['random_password'] = $random_password;
					}
					$return_options['content'] = $this->getTemplateHTML('configuration/finduser_row', $template_options);
					$return_options['title'] = TBGContext::getI18n()->__('User updated!');
					if ($password_changed)
					{
						$return_options['message'] = TBGContext::getI18n()->__('The password was changed');
					}
					return $this->renderJSON($return_options);
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This user could not be updated: %message%', array('%message%' => $e->getMessage()))));
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This user could not be updated')));
		}

		public function runUpdateUserScopes(TBGRequest $request)
		{
			try
			{
				if (!TBGContext::getScope()->isDefault()) throw new Exception('This operation is not allowed');

				$user = TBGContext::factory()->TBGUser($request['user_id']);
				if ($user instanceof TBGUser)
				{
					$return_options = array('message' => $this->getI18n()->__("The user's scope access was successfully updated"));
					$scopes = $request->getParameter('scopes', array());
					if (count($scopes) && !(count($scopes) == 1 && array_key_exists(TBGSettings::getDefaultScopeID(), $scopes)))
					{
						foreach ($user->getScopes() as $scope_id => $scope)
						{
							if (!$scope->isDefault() && !array_key_exists($scope_id, $scopes))
							{
								$user->removeScope($scope_id);
							}
						}
						foreach ($scopes as $scope_id => $scope)
						{
							try
							{
								$scope = new TBGScope((int) $scope_id);
								if ($user->isMemberOfScope($scope)) continue;

								$user->addScope($scope);
							}
							catch (Exception $e) {}
						}
					}
					return $this->renderJSON($return_options);
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This user could not be updated: %message%', array('%message%' => $e->getMessage()))));
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This user could not be updated')));
		}

		public function runGetPermissionsConfigurator(TBGRequest $request)
		{
			return $this->renderComponent('configuration/permissionsconfigurator', array('access_level' => $this->access_level, 'user_id' => $request->getParameter('user_id', 0), 'base_id' => $request->getParameter('base_id', 0)));
		}

		public function runConfigureWorkflowSchemes(TBGRequest $request)
		{
			$this->schemes = TBGWorkflowScheme::getAll();
		}

		public function runConfigureWorkflows(TBGRequest $request)
		{
			$this->workflows = TBGWorkflow::getAll();
			if ($request->isPost())
			{
				try
				{
					$workflow_name = $request['workflow_name'];
					$workflow = new TBGWorkflow();
					$workflow->setName($workflow_name);
					$workflow->save();
					$step = new TBGWorkflowStep();
					$step->setName(TBGContext::getI18n()->__('New'));
					$step->setWorkflow($workflow);
					$step->save();
					$this->forward(TBGContext::getRouting()->generate('configure_workflow'));
				}
				catch (Exception $e)
				{
					$this->error = $e->getMessage();
				}
			}
		}

		public function runConfigureWorkflowScheme(TBGRequest $request)
		{
			$this->workflow_scheme = null;
			$this->mode = $request->getParameter('mode', 'list');
			try
			{
				$this->workflow_scheme = TBGContext::factory()->TBGWorkflowScheme($request['scheme_id']);
				$this->issuetypes = TBGIssuetype::getAll();
				if (TBGContext::getScope()->isCustomWorkflowsEnabled() && $this->mode == 'copy_scheme')
				{
					if ($new_name = $request['new_name'])
					{
						$new_scheme = new TBGWorkflowScheme();
						$new_scheme->setName($new_name);
						$new_scheme->save();
						foreach ($this->issuetypes as $issuetype)
						{
							if ($this->workflow_scheme->hasWorkflowAssociatedWithIssuetype($issuetype))
							{
								$new_scheme->associateIssuetypeWithWorkflow($issuetype, $this->workflow_scheme->getWorkflowForIssuetype($issuetype));
							}
						}
						return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/workflowscheme', array('scheme' => $new_scheme))));
					}
					else
					{
						$this->error = TBGContext::getI18n()->__('Please enter a valid name');
					}
				}
				elseif (TBGContext::getScope()->isCustomWorkflowsEnabled() && $this->mode == 'delete_scheme')
				{
					$this->workflow_scheme->delete();
					return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The workflow scheme was deleted')));
				}
				elseif (TBGContext::getScope()->isCustomWorkflowsEnabled() && $request->isPost())
				{
					foreach ($request->getParameter('workflow_id', array()) as $issuetype_id => $workflow_id)
					{
						$issuetype = TBGContext::factory()->TBGIssuetype($issuetype_id);
						if ($workflow_id)
						{
							$workflow = TBGContext::factory()->TBGWorkflow($workflow_id);
							$this->workflow_scheme->associateIssuetypeWithWorkflow($issuetype, $workflow);
						}
						else
						{
							$this->workflow_scheme->unassociateIssuetype($issuetype);
						}
					}
					return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('Workflow associations were updated')));
				}
			}
			catch (Exception $e)
			{
				if ($request->getRequestedFormat() == 'json')
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('success' => false, 'message' => TBGContext::getI18n()->__('An error occured'), 'error' => $e->getMessage()));
				}
				else
				{
					$this->error = TBGContext::getI18n()->__('This workflow scheme does not exist');
				}
			}
		}

		public function runConfigureWorkflowSteps(TBGRequest $request)
		{
			$this->workflow = null;
			$this->mode = $request->getParameter('mode', 'list');
			try
			{
				$this->workflow = TBGContext::factory()->TBGWorkflow($request['workflow_id']);
				if ($this->mode == 'copy_workflow')
				{
					if ($new_name = $request['new_name'])
					{
						$new_workflow = $this->workflow->copy($new_name);
						return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/workflow', array('workflow' => $new_workflow)), 'total_count' => TBGWorkflow::getCustomWorkflowsCount(), 'more_available' => TBGContext::getScope()->hasCustomWorkflowsAvailable()));
					}
					else
					{
						$this->error = TBGContext::getI18n()->__('Please enter a valid name');
					}
				}
				elseif ($this->mode == 'delete_workflow')
				{
					$this->workflow->delete();
					return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The workflow was deleted'), 'total_count' => TBGWorkflow::getCustomWorkflowsCount(), 'more_available' => TBGContext::getScope()->hasCustomWorkflowsAvailable()));
				}
			}
			catch (Exception $e)
			{
				if ($request->getRequestedFormat() == 'json')
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('success' => false, 'message' => TBGContext::getI18n()->__('An error occured'), 'error' => $e->getMessage()));
				}
				else
				{
					$this->error = TBGContext::getI18n()->__('This workflow does not exist');
				}
			}
		}

		public function runConfigureWorkflowStep(TBGRequest $request)
		{
			$this->workflow = null;
			$this->step = null;
			try
			{
				$this->workflow = TBGContext::factory()->TBGWorkflow($request['workflow_id']);
				if ($request['mode'] == 'edit' && !$request->hasParameter('step_id'))
				{
					$this->step = new TBGWorkflowStep();
					$this->step->setWorkflow($this->workflow);
				}
				else
				{
					$this->step = TBGContext::factory()->TBGWorkflowStep($request['step_id']);
				}
				if ($request->isPost() && $request['mode'] == 'delete_outgoing_transitions')
				{
					$this->step->deleteOutgoingTransitions();
					$this->forward(TBGContext::getRouting()->generate('configure_workflow_steps', array('workflow_id' => $this->workflow->getID())));
				}
				if ($request->isPost() && $request['mode'] == 'delete' && !$this->step->hasIncomingTransitions())
				{
					$this->step->deleteOutgoingTransitions();
					$this->step->delete();
					$this->forward(TBGContext::getRouting()->generate('configure_workflow_steps', array('workflow_id' => $this->workflow->getID())));
				}
				elseif ($request->isPost() && ($request->hasParameter('edit') || $request['mode'] == 'edit'))
				{
					$this->step->setName($request['name']);
					$this->step->setDescription($request['description']);
					$this->step->setLinkedStatusID($request['status_id']);
					$this->step->setIsEditable((bool) $request['is_editable']);
					$this->step->setIsClosed((bool) ($request['state'] == TBGIssue::STATE_CLOSED));
					$this->step->save();
					$this->forward(TBGContext::getRouting()->generate('configure_workflow_step', array('workflow_id' => $this->workflow->getID(), 'step_id' => $this->step->getID())));
				}
			}
			catch (Exception $e)
			{
				$this->error = TBGContext::getI18n()->__('This workflow / step does not exist');
			}
		}

		public function runConfigureWorkflowTransition(TBGRequest $request)
		{
			$this->workflow = null;
			$this->transition = null;
			
			try
			{
				$this->workflow = TBGContext::factory()->TBGWorkflow($request['workflow_id']);
				if ($request->hasParameter('transition_id'))
				{
					$mode = $request['mode'];
					$this->transition = TBGContext::factory()->TBGWorkflowTransition($request['transition_id']);
					if ($request->isPost())
					{
						if ($mode == 'delete')
						{
							$this->transition->deleteTransition($request['direction']);
							return $this->renderJSON('ok');
						}
						elseif ($mode == 'delete_action')
						{
							$this->action = TBGContext::factory()->TBGWorkflowTransitionAction($request['action_id']);
							$this->action->delete();
							return $this->renderJSON(array('message' => TBGContext::getI18n()->__('The action has been deleted')));
						}
						elseif ($mode == 'new_action')
						{
							$action = new TBGWorkflowTransitionAction();
							$action->setActionType($request['action_type']);
							$action->setTransition($this->transition);
							$action->setWorkflow($this->workflow);
							$action->setTargetValue('');
							$action->save();
							return $this->renderJSON(array('content' => $this->getComponentHTML('configuration/workflowtransitionaction', array('action' => $action))));
						}
						elseif ($mode == 'update_action')
						{
							$this->action = TBGContext::factory()->TBGWorkflowTransitionAction($request['action_id']);
							$this->action->setTargetValue($request['target_value']);
							$this->action->save();
							$text = $request['target_value'];
							switch ($this->action->getActionType())
							{
								case TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
									$text = ($this->action->getTargetValue()) ? TBGContext::factory()->TBGUser((int) $this->action->getTargetValue())->getName() : TBGContext::getI18n()->__('User specified during transition');
									break;
								case TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION:
									$text = ($this->action->getTargetValue()) ? TBGContext::factory()->TBGResolution((int) $this->action->getTargetValue())->getName() : TBGContext::getI18n()->__('Resolution specified by user');
									break;
								case TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
									$text = ($this->action->getTargetValue()) ? TBGContext::factory()->TBGReproducability((int) $this->action->getTargetValue())->getName() : TBGContext::getI18n()->__('Reproducability specified by user');
									break;
								case TBGWorkflowTransitionAction::ACTION_SET_STATUS:
									$text = ($this->action->getTargetValue()) ? TBGContext::factory()->TBGStatus((int) $this->action->getTargetValue())->getName() : TBGContext::getI18n()->__('Status specified by user');
									break;
								case TBGWorkflowTransitionAction::ACTION_SET_MILESTONE:
									$text = ($this->action->getTargetValue()) ? TBGContext::factory()->TBGMilestone((int) $this->action->getTargetValue())->getName() : TBGContext::getI18n()->__('Milestone specified by user');
									break;
								case TBGWorkflowTransitionAction::ACTION_SET_PRIORITY:
									$text = ($this->action->getTargetValue()) ? TBGContext::factory()->TBGPriority((int) $this->action->getTargetValue())->getName() : TBGContext::getI18n()->__('Priority specified by user');
									break;
							}
							return $this->renderJSON(array('content' => $text));
						}
						elseif ($mode == 'delete_validation_rule')
						{
							$this->rule = TBGContext::factory()->TBGWorkflowTransitionValidationRule($request['rule_id']);
							$this->rule->delete();
							return $this->renderJSON(array('message' => TBGContext::getI18n()->__('The validation rule has been deleted')));
						}
						elseif ($mode == 'new_validation_rule')
						{
							$rule = new TBGWorkflowTransitionValidationRule();
							if ($request['postorpre'] == 'post')
							{
								$exists = (bool) ($this->transition->hasPostValidationRule($request['rule']));
								if (!$exists) $rule->setPost();
							}
							elseif ($request['postorpre'] == 'pre')
							{
								$exists = (bool) ($this->transition->hasPreValidationRule($request['rule']));
								if (!$exists) $rule->setPre();
							}
							if ($exists)
							{
								$this->getResponse()->setHttpStatus(400);
								return $this->renderJSON(array('message' => TBGContext::getI18n()->__('This validation rule already exist')));
							}
							$rule->setRule($request['rule']);
							$rule->setRuleValue('');
							$rule->setTransition($this->transition);
							$rule->setWorkflow($this->workflow);
							$rule->save();
							
							return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/workflowtransitionvalidationrule', array('rule' => $rule))));
						}
						elseif ($mode == 'update_validation_rule')
						{
							$this->rule = TBGContext::factory()->TBGWorkflowTransitionValidationRule($request['rule_id']);
							$text = null;
							switch ($this->rule->getRule())
							{
								case TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES:
									$this->rule->setRuleValue($request['rule_value']);
									$text = ($this->rule->getRuleValue()) ? $this->rule->getRuleValue() : TBGContext::getI18n()->__('Unlimited');
									break;
								case TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID:
								case TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID:
								case TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID:
								case TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID:
									$this->rule->setRuleValue(join(',', $request['rule_value']));
									$text = ($this->rule->getRuleValue()) ? $this->rule->getRuleValueAsJoinedString() : TBGContext::getI18n()->__('Any valid value');
									break;
								//case TBGWorkflowTransitionValidationRule::RULE_:
								//	$text = ($this->rule->getRuleValue()) ? $this->rule->getRuleValue() : TBGContext::getI18n()->__('Unlimited');
								//	break;
							}
							$this->rule->save();
							return $this->renderJSON(array('content' => $text));
						}
						elseif ($request['transition_name'] && $request['outgoing_step_id'] && $request->hasParameter('template'))
						{
							$this->transition->setName($request['transition_name']);
							$this->transition->setDescription($request['transition_description']);
							if ($request['template'])
							{
								$this->transition->setTemplate($request['template']);
							}
							else
							{
								$this->transition->setTemplate(null);
							}
							try
							{
								$step = TBGContext::factory()->TBGWorkflowStep($request['outgoing_step_id']);
							}
							catch (Exception $e) {}
							$this->transition->setOutgoingStep($step);
							$this->transition->save();
							$transition = $this->transition;
							$redirect_transition = true;
						}
					}
				}
				elseif ($request->isPost() && $request->hasParameter('step_id'))
				{
					$step = TBGContext::factory()->TBGWorkflowStep($request['step_id']);
					/*if ($step->isCore() || $workflow->isCore())
					{
						throw new InvalidArgumentException("The default workflow cannot be edited");
					}*/
					if ($request['add_transition_type'] == 'existing' && $request->hasParameter('existing_transition_id'))
					{
						$transition = TBGContext::factory()->TBGWorkflowTransition($request['existing_transition_id']);
						$redirect_transition = false;
					}
					else
					{
						if ($request['transition_name'] && $request['outgoing_step_id'] && $request->hasParameter('template'))
						{
							if (($outgoing_step = TBGContext::factory()->TBGWorkflowStep((int) $request['outgoing_step_id'])) && $step instanceof TBGWorkflowStep)
							{
								if (array_key_exists($request['template'], TBGWorkflowTransition::getTemplates()))
								{
									$transition = new TBGWorkflowTransition();
									$transition->setWorkflow($this->workflow);
									$transition->setName($request['transition_name']);
									$transition->setDescription($request['transition_description']);
									$transition->setOutgoingStep($outgoing_step);
									$transition->setTemplate($request['template']);
									$transition->save();
									$step->addOutgoingTransition($transition);
									$redirect_transition = true;
								}
								else
								{
									throw new InvalidArgumentException(TBGContext::getI18n()->__('Please select a valid template'));
								}
							}
							else
							{
								throw new InvalidArgumentException(TBGContext::getI18n()->__('Please select a valid outgoing step'));
							}
						}
						else
						{
							throw new InvalidArgumentException(TBGContext::getI18n()->__('Please fill in all required fields'));
						}
					}
					$step->addOutgoingTransition($transition);
				}
				else
				{
					throw new InvalidArgumentException('Invalid action');
				}
			}
			catch (InvalidArgumentException $e)
			{
				//throw $e;
				$this->error = $e->getMessage();
			}
			catch (Exception $e)
			{
				throw $e;
				$this->error = TBGContext::getI18n()->__('This workflow / transition does not exist');
			}
			if (isset($redirect_transition) && $redirect_transition)
			{
				$this->forward(TBGContext::getRouting()->generate('configure_workflow_transition', array('workflow_id' => $this->workflow->getID(), 'transition_id' => $transition->getID())));
			}
			elseif (isset($redirect_transition))
			{
				$this->forward(TBGContext::getRouting()->generate('configure_workflow_steps', array('workflow_id' => $this->workflow->getID())));
			}
		}

		public function getAccessLevel($section, $module)
		{
			return (TBGContext::getUser()->canSaveConfiguration($section, $module)) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
		}
		
		public function runAddClient(TBGRequest $request)
		{
			try
			{
				$mode = $request['mode'];
				if ($client_name = $request['client_name'])
				{
					if (TBGClient::doesClientNameExist(trim($request['client_name'])))
					{
						throw new Exception(TBGContext::getI18n()->__("Please enter a client name that doesn't already exist"));
					}
					$client = new TBGClient();
					$client->setName($request['client_name']);
					$client->save();

					$message = TBGContext::getI18n()->__('The client was added');
					return $this->renderJSON(array('message' => $message, 'content' => $this->getTemplateHTML('configuration/clientbox', array('client' => $client))));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a client name'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		public function runDeleteClient(TBGRequest $request)
		{
			try
			{
				try
				{
					$client = TBGContext::factory()->TBGClient($request['client_id']);
				}
				catch (Exception $e) { }
				if (!$client instanceof TBGClient)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete this client"));
				}
				
				if (TBGProject::getAllByClientID($client->getID()) !== null)
				{
					foreach (TBGProject::getAllByClientID($client->getID()) as $project)
					{
						$project->setClient(null);
						$project->save();
					}
				}
				
				$client->delete();
				return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The client was deleted')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}
		
		public function runGetClientMembers(TBGRequest $request)
		{
			try
			{
				$client = TBGContext::factory()->TBGClient((int) $request['client_id']);
				$users = $client->getMembers();
				return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/clientuserlist', array('users' => $users))));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}
		
		public function runEditClient(TBGRequest $request)
		{
			try
			{
				try
				{
					$client = TBGContext::factory()->TBGClient($request['client_id']);
				}
				catch (Exception $e) { }
				if (!$client instanceof TBGClient)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot edit this client"));
				}
				
				if (TBGClient::doesClientNameExist(trim($request['client_name'])) && strtolower($request['client_name']) != strtolower($client->getName()))
				{
					throw new Exception(TBGContext::getI18n()->__("Please enter a client name that doesn't already exist"));
				}
				
				$client->setName($request['client_name']);
				$client->setEmail($request['client_email']);
				$client->setWebsite($request['client_website']);
				$client->setTelephone($request['client_telephone']);
				$client->setFax($request['client_fax']);
				$client->save();
				return $this->renderJSON(array('success' => true, 'content' => $this->getTemplateHTML('configuration/clientbox', array('client' => $client)), 'message' => TBGContext::getI18n()->__('The client was saved')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}
		
		public function runImportCSV(TBGRequest $request)
		{
			$content = $this->getTemplateHTML('configuration/importcsv', array('type' => $request['type']));
			return $this->renderJSON(array('content' => $content));
		}
		
		public function runGetIDsForImportCSV(TBGRequest $request)
		{
			$content = $this->getTemplateHTML('configuration/import_ids');
			return $this->renderJSON(array('content' => $content));
		}
		
		public function runDoImportCSV(TBGRequest $request)
		{
			try
			{
				if ($request['csv_data'] == '')
				{
					throw new Exception(TBGContext::getI18n()->__('No data supplied to import'));
				}

				$csv = str_replace("\r\n", "\n", $request['csv_data']);
				$csv = html_entity_decode($csv);

				$headerrow = null;
				$data = array();
				$errors = array();

				// Parse CSV
				$handle = fopen("php://memory", 'r+');
				fputs($handle, $csv);
				rewind($handle);
				$i = 0;
				while (($row = fgetcsv($handle, 1000)) !== false)
				{
					if (!$headerrow) {
						$headerrow = $row;
					} else {
						if (count($headerrow) == count($row)) {
							$data[] = array_combine($headerrow, $row);
						} else {
							$errors[] = TBGContext::getI18n()->__('Row %row% does not have the same number of elements as the header row', array('%row%' => $i));
						}
					}
					$i++;
				}
				fclose($handle);

				if (empty($data))
				{
					throw new Exception(TBGContext::getI18n()->__('Insufficient data to import'));
				}

				// Verify required columns are present based on type
				$requiredcols = array(
					self::CSV_TYPE_CLIENTS  => array(self::CSV_CLIENT_NAME),
					self::CSV_TYPE_PROJECTS => array(self::CSV_PROJECT_NAME),
					self::CSV_TYPE_ISSUES   => array(self::CSV_ISSUE_TITLE, self::CSV_ISSUE_PROJECT, self::CSV_ISSUE_ISSUE_TYPE),
				);

				if (!isset($requiredcols[$request['type']]))
				{
					throw new Exception('Sorry, this type is unimplemented');
				}

				foreach ($requiredcols[$request['type']] as $col) {
					if (!in_array($col, $headerrow))
					{
						$errors[] = TBGContext::getI18n()->__('Required column \'%col%\' not found in header row', array('%col%' => $col));
					}
				}
				
				// Check if rows are long enough and fields are not empty
				for ($i = 0; $i != count($data); $i++)
				{
					$activerow = $data[$i];

					// Check if fields are empty
					foreach ($activerow as $col => $val)
					{
						if (strlen($val) == 0)
						{
							$errors[] = TBGContext::getI18n()->__('Row %row% column %col% has no value', array('%col%' => $col, '%row%' => $i+1));
						}
					}
				}

				if (count($errors) == 0)
				{
					// Check if fields are valid
					switch ($request['type'])
					{
						case self::CSV_TYPE_PROJECTS:
							for ($i = 0; $i != count($data); $i++)
							{
								$activerow = $data[$i];

								// Check if project exists
								$key = str_replace(' ', '', $activerow[self::CSV_PROJECT_NAME]);
								$key = mb_strtolower($key);
								
								$tmp = TBGProject::getByKey($key);
								
								if ($tmp !== null)
								{
									$errors[] = TBGContext::getI18n()->__('Row %row%: A project with this name already exists', array('%row%' => $i+1));
								}
								
								// First off are booleans
								$boolitems = array(self::CSV_PROJECT_SCRUM, self::CSV_PROJECT_ALLOW_REPORTING, self::CSV_PROJECT_AUTOASSIGN, self::CSV_PROJECT_FREELANCE,
									self::CSV_PROJECT_EN_BUILDS, self::CSV_PROJECT_EN_COMPS, self::CSV_PROJECT_EN_EDITIONS, self::CSV_PROJECT_SHOW_SUMMARY);
								
								foreach ($boolitems as $boolitem)
								{
									if (array_key_exists($boolitem, $activerow) && isset($activerow[$boolitem]) && $activerow[$boolitem] != 1 && $activerow[$boolitem] != 0)
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be 1/0)', array('%col%' => $boolitem, '%row%' => $i+1));
									}
								}
								
								// Now identifiables
								$identifiableitems = array(
									array(self::CSV_PROJECT_QA, self::CSV_PROJECT_QA_TYPE),
									array(self::CSV_PROJECT_LEAD, self::CSV_PROJECT_LEAD_TYPE),
									array(self::CSV_PROJECT_OWNER, self::CSV_PROJECT_OWNER_TYPE)
								);
								
								foreach ($identifiableitems as $identifiableitem)
								{

									if ((!array_key_exists($identifiableitem[1], $activerow) && array_key_exists($identifiableitem[0], $activerow)) || (array_key_exists($identifiableitem[1], $activerow) && !array_key_exists($identifiableitem[0], $activerow)))
									{
											$errors[] = TBGContext::getI18n()->__('Row %row%: Both the type and item ID must be supplied for owner/lead/qa fields', array('%row%' => $i+1));
											continue;
									}
									
									if (array_key_exists($identifiableitem[1], $activerow) && isset($activerow[$identifiableitem[1]]) !== null && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_USER && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_TEAM)
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be 1 for a user or 2 for a team)', array('%col%' => $identifiableitem[1], '%row%' => $i+1));
									}
									
									if (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && !is_numeric($activerow[$identifiableitem[0]]))
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => $identifiableitem[0], '%row%' => $i+1));
									}
									elseif (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && is_numeric($activerow[$identifiableitem[0]]))
									{
										// check if they exist
										switch ($activerow[$identifiableitem[1]])
										{
											case self::CSV_IDENTIFIER_TYPE_USER:
												try
												{
													TBGContext::factory()->TBGUser($activerow[$identifiableitem[0]]);
												}
												catch (Exception $e)
												{
													$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: user does not exist', array('%col%' => $identifiableitem[0], '%row%' => $i+1));
												}
												break;
											case self::CSV_IDENTIFIER_TYPE_TEAM:
												try
												{
													TBGContext::factory()->TBGTeam($activerow[$identifiableitem[0]]);
												}
												catch (Exception $e)
												{
													$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: team does not exist', array('%col%' => $identifiableitem[0], '%row%' => $i+1));
												}
												break;
										}
									}
								}
								
								// Now check client exists
								if (array_key_exists(self::CSV_PROJECT_CLIENT, $activerow) && isset($activerow[self::CSV_PROJECT_CLIENT]))
								{
									if (!is_numeric($activerow[self::CSV_PROJECT_CLIENT]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_PROJECT_CLIENT, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGClient($activerow[self::CSV_PROJECT_CLIENT]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: client does not exist', array('%col%' => self::CSV_PROJECT_CLIENT, '%row%' => $i+1));
										}
									}
								}
								
								// Now check if workflow exists
								if (array_key_exists(self::CSV_PROJECT_WORKFLOW_ID, $activerow) && isset($activerow[self::CSV_PROJECT_WORKFLOW_ID]))
								{
									if (!is_numeric($activerow[self::CSV_PROJECT_WORKFLOW_ID]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_PROJECT_WORKFLOW_ID, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGWorkflowScheme($activerow[self::CSV_PROJECT_WORKFLOW_ID]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: workflow scheme does not exist', array('%col%' => self::CSV_PROJECT_WORKFLOW_ID, '%row%' => $i+1));
										}
									}
								}
								
								// Now check if issuetype scheme
								if (array_key_exists(self::CSV_PROJECT_ISSUETYPE_SCHEME, $activerow) && isset($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]))
								{
									if (!is_numeric($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_PROJECT_ISSUETYPE_SCHEME, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGIssuetypeScheme($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: issuetype scheme does not exist', array('%col%' => self::CSV_PROJECT_ISSUETYPE_SCHEME, '%row%' => $i+1));
										}
									}
								}
								
								// Finally check if the summary type is valid. At this point, your error list has probably become so big it has eaten up all your available RAM...
								if (array_key_exists(self::CSV_PROJECT_SUMMARY_TYPE, $activerow) && isset($activerow[self::CSV_PROJECT_SUMMARY_TYPE]))
								{
									if ($activerow[self::CSV_PROJECT_SUMMARY_TYPE] != 'issuetypes' && $activerow[self::CSV_PROJECT_SHOW_SUMMARY] != 'milestones')
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be \'issuetypes\' or \'milestones\')', array('%col%' => self::CSV_PROJECT_SUMMARY_TYPE, '%row%' => $i+1));
									}
								}
							}
							break;
						case self::CSV_TYPE_ISSUES:
							for ($i = 0; $i != count($data); $i++)
							{
								$activerow = $data[$i];

								// Check if project exists
								try
								{
									$prjtmp = TBGContext::factory()->TBGProject($activerow[self::CSV_ISSUE_PROJECT]);
								}
								catch (Exception $e)
								{
									$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: Project does not exist', array('%col%' => self::CSV_ISSUE_PROJECT, '%row%' => $i+1));
									break;
								}
								
								// First off are booleans
								$boolitems = array(self::CSV_ISSUE_STATE, self::CSV_ISSUE_BLOCKING);
								
								foreach ($boolitems as $boolitem)
								{
									if (array_key_exists($boolitem, $activerow) && isset($activerow[$boolitem]) && $activerow[$boolitem] != 1 && $activerow[$boolitem] != 0)
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be 1/0)', array('%col%' => $boolitem, '%row%' => $i+1));
									}
								}
								
								// Now numerics
								$numericitems = array(self::CSV_ISSUE_VOTES, self::CSV_ISSUE_PERCENTAGE);
								
								foreach ($numericitems as $numericitem)
								{
									if (array_key_exists($numericitem, $activerow) && isset($activerow[$numericitem]) && !(is_numeric($activerow[$numericitem])))
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => $numericitem, '%row%' => $i+1));
									}
								}
								
								// Percentage must be 0-100
								if (array_key_exists(self::CSV_ISSUE_PERCENTAGE, $activerow) && isset($activerow[self::CSV_ISSUE_PERCENTAGE]) && (($activerow[self::CSV_ISSUE_PERCENTAGE] < 0) || ($activerow[self::CSV_ISSUE_PERCENTAGE] > 100)))
								{
									$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: Percentage must be from 0 to 100 inclusive', array('%col%' => self::CSV_ISSUE_PERCENTAGE, '%row%' => $i+1));
								}
									
								// Now identifiables
								$identifiableitems = array(
									array(self::CSV_ISSUE_OWNER, self::CSV_ISSUE_OWNER_TYPE),
									array(self::CSV_ISSUE_ASSIGNED, self::CSV_ISSUE_ASSIGNED_TYPE)
								);
								
								foreach ($identifiableitems as $identifiableitem)
								{
									if ((!array_key_exists($identifiableitem[1], $activerow) && array_key_exists($identifiableitem[0], $activerow)) || (array_key_exists($identifiableitem[1], $activerow) && !array_key_exists($identifiableitem[0], $activerow)))
									{
											$errors[] = TBGContext::getI18n()->__('Row %row%: Both the type and item ID must be supplied for owner/lead/qa fields', array('%row%' => $i+1));
											continue;
									}
									
									if (array_key_exists($identifiableitem[1], $activerow) && isset($activerow[$identifiableitem[1]]) && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_USER && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_TEAM)
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be 1 for a user or 2 for a team)', array('%col%' => $identifiableitem[1], '%row%' => $i+1));
									}
									
									if (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && !is_numeric($activerow[$identifiableitem[0]]))
									{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => $identifiableitem[0], '%row%' => $i+1));
									}
									elseif (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && is_numeric($activerow[$identifiableitem[0]]))
									{
										// check if they exist
										switch ($activerow[$identifiableitem[1]])
										{
											case self::CSV_IDENTIFIER_TYPE_USER:
												try
												{
													TBGContext::factory()->TBGUser($activerow[$identifiableitem[0]]);
												}
												catch (Exception $e)
												{
													$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: user does not exist', array('%col%' => $identifiableitem[0], '%row%' => $i+1));
												}
												break;
											case self::CSV_IDENTIFIER_TYPE_TEAM:
												try
												{
													TBGContext::factory()->TBGTeam($activerow[$identifiableitem[0]]);
												}
												catch (Exception $e)
												{
													$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: team does not exist', array('%col%' => $identifiableitem[0], '%row%' => $i+1));
												}
												break;
										}
									}
								}
								
								// Now check user exists for postedby
								if (array_key_exists(self::CSV_ISSUE_POSTED_BY, $activerow) && isset($activerow[self::CSV_ISSUE_POSTED_BY]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_POSTED_BY]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_POSTED_BY, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGUser($activerow[self::CSV_ISSUE_POSTED_BY]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: user does not exist', array('%col%' => self::CSV_ISSUE_POSTED_BY, '%row%' => $i+1));
										}
									}
								}
								
								// Now check milestone exists and is valid
								if (array_key_exists(self::CSV_ISSUE_MILESTONE, $activerow) && isset($activerow[self::CSV_ISSUE_MILESTONE]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_MILESTONE]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_MILESTONE, '%row%' => $i+1));
									}
									else
									{
										try
										{
											$milestonetmp = TBGContext::factory()->TBGMilestone($activerow[self::CSV_ISSUE_MILESTONE]);
											if ($milestonetmp->getProject()->getID() != $activerow[self::CSV_ISSUE_PROJECT])
											{
												$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: milestone does not apply to the specified project', array('%col%' => self::CSV_ISSUE_MILESTONE, '%row%' => $i+1));
											}
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: milestone does not exist', array('%col%' => self::CSV_ISSUE_MILESTONE, '%row%' => $i+1));
										}
									}
								}
								
								// status
								if (array_key_exists(self::CSV_ISSUE_STATUS, $activerow) && isset($activerow[self::CSV_ISSUE_STATUS]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_STATUS]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_STATUS, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGStatus($activerow[self::CSV_ISSUE_STATUS]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: status does not exist', array('%col%' => self::CSV_ISSUE_STATUS, '%row%' => $i+1));
										}
									}
								}
								
								// resolution
								if (array_key_exists(self::CSV_ISSUE_RESOLUTION, $activerow) && isset($activerow[self::CSV_ISSUE_RESOLUTION]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_RESOLUTION]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_RESOLUTION, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGResolution($activerow[self::CSV_ISSUE_RESOLUTION]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: resolution does not exist', array('%col%' => self::CSV_ISSUE_RESOLUTION, '%row%' => $i+1));
										}
									}
								}
								
								// priority
								if (array_key_exists(self::CSV_ISSUE_PRIORITY, $activerow) && isset($activerow[self::CSV_ISSUE_PRIORITY]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_PRIORITY]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_PRIORITY, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGPriority($activerow[self::CSV_ISSUE_PRIORITY]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: priority does not exist', array('%col%' => self::CSV_ISSUE_PRIORITY, '%row%' => $i+1));
										}
									}
								}
								
								// category
								if (array_key_exists(self::CSV_ISSUE_CATEGORY, $activerow) && isset($activerow[self::CSV_ISSUE_CATEGORY]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_CATEGORY]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_CATEGORY, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGCategory($activerow[self::CSV_ISSUE_CATEGORY]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: category does not exist', array('%col%' => self::CSV_ISSUE_CATEGORY, '%row%' => $i+1));
										}
									}
								}
								
								// severity
								if (array_key_exists(self::CSV_ISSUE_SEVERITY, $activerow) && isset($activerow[self::CSV_ISSUE_SEVERITY]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_SEVERITY]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_SEVERITY, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGSeverity($activerow[self::CSV_ISSUE_SEVERITY]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: severity does not exist', array('%col%' => self::CSV_ISSUE_SEVERITY, '%row%' => $i+1));
										}
									}
								}
								
								// reproducability
								if (array_key_exists(self::CSV_ISSUE_REPRODUCIBILITY, $activerow) && isset($activerow[self::CSV_ISSUE_REPRODUCIBILITY]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_REPRODUCIBILITY]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_REPRODUCIBILITY, '%row%' => $i+1));
									}
									else
									{
										try
										{
											TBGContext::factory()->TBGReproducability($activerow[self::CSV_ISSUE_REPRODUCIBILITY]);
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: reproducability does not exist', array('%col%' => self::CSV_ISSUE_REPRODUCIBILITY, '%row%' => $i+1));
										}
									}
								}
								
								// type
								if (array_key_exists(self::CSV_ISSUE_ISSUE_TYPE, $activerow) && isset($activerow[self::CSV_ISSUE_ISSUE_TYPE]))
								{
									if (!is_numeric($activerow[self::CSV_ISSUE_ISSUE_TYPE]))
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => self::CSV_ISSUE_ISSUE_TYPE, '%row%' => $i+1));
									}
									else
									{
										try
										{
											$typetmp = TBGContext::factory()->TBGIssuetype($activerow[self::CSV_ISSUE_ISSUE_TYPE]);
											if (!($prjtmp->getIssuetypeScheme()->isSchemeAssociatedWithIssuetype($typetmp)))
												$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: this project does not support issues of this type (%type%)', array('%type%' => $typetmp->getName(), '%col%' => self::CSV_ISSUE_ISSUE_TYPE, '%row%' => $i+1));
										}
										catch (Exception $e)
										{
											$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: issue type does not exist', array('%col%' => self::CSV_ISSUE_ISSUE_TYPE, '%row%' => $i+1));
										}
									}
								}
							}
							break;
					}
				}
					
				// Handle errors
				if (count($errors) != 0)
				{
					$errordiv = '<ul>';
					foreach ($errors as $error)
					{
						$errordiv .= '<li>'.$error.'</li>';
					}
					$errordiv .= '</ul>';
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('errordetail' => $errordiv, 'error' => TBGContext::getI18n()->__('Errors occured while importing, see the error list in the import screen for further details')));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('errordetail' => $e->getMessage(), 'error' => $e->getMessage()));
			}
				
			if ($request['csv_dry_run'])
			{
				return $this->renderJSON(array('message' => TBGContext::getI18n()->__('Dry-run successful, you can now uncheck the dry-run box and import your data.')));
			}
			else
			{
				switch ($request['type'])
				{
					case self::CSV_TYPE_CLIENTS:
						for ($i = 0; $i != count($data); $i++)
						{
							try
							{
								$activerow = $data[$i];

								$client = new TBGClient();
								$client->setName($activerow[self::CSV_CLIENT_NAME]);

								if (isset($activerow[self::CSV_CLIENT_EMAIL]))
									$client->setEmail($activerow[self::CSV_CLIENT_EMAIL]);
									
								if (isset($activerow[self::CSV_CLIENT_WEBSITE]))
									$client->setWebsite($activerow[self::CSV_CLIENT_WEBSITE]);
									
								if (isset($activerow[self::CSV_CLIENT_FAX]))
									$client->setFax($activerow[self::CSV_CLIENT_FAX]);
								
								if (isset($activerow[self::CSV_CLIENT_TELEPHONE]))
									$client->setTelephone($activerow[self::CSV_CLIENT_TELEPHONE]);
									
								$client->save();
							}
							catch (Exception $e)
							{
									$errors[] = TBGContext::getI18n()->__('Row %row% failed: %err%', array('%row%' => $i+1, '%err%' => $e->getMessage()));
							}
						}
						break;
					case self::CSV_TYPE_PROJECTS:
						for ($i = 0; $i != count($data); $i++)
						{
							try
							{
								$activerow = $data[$i];

								$project = new TBGProject();
								$project->setName($activerow[self::CSV_PROJECT_NAME]);

								$project->save();

								if (isset($activerow[self::CSV_PROJECT_PREFIX]))
								{
									$project->setPrefix($activerow[self::CSV_PROJECT_PREFIX]);
									$project->setUsePrefix(true);
								}
									
								if (isset($activerow[self::CSV_PROJECT_SCRUM]))
								{
									if ($activerow[self::CSV_PROJECT_SCRUM] == '1')
										$project->setUsesScrum(true);
								}
								
								if (isset($activerow[self::CSV_PROJECT_OWNER]) && isset($activerow[self::CSV_PROJECT_OWNER_TYPE]))
								{
									switch ($activerow[self::CSV_PROJECT_OWNER_TYPE])
									{
										case self::CSV_IDENTIFIER_TYPE_USER:
											$user = new TBGUser($activerow[self::CSV_PROJECT_OWNER]);
											$project->setOwner($user);
											break;
										case self::CSV_IDENTIFIER_TYPE_TEAM:
											$team = new TBGTeam($activerow[self::CSV_PROJECT_OWNER]);
											$project->setOwner($team);
											break;
									}
								}
								
								if (isset($activerow[self::CSV_PROJECT_LEAD]) && isset($activerow[self::CSV_PROJECT_LEAD_TYPE]))
								{
									switch ($activerow[self::CSV_PROJECT_LEAD_TYPE])
									{
										case self::CSV_IDENTIFIER_TYPE_USER:
											$user = new TBGUser($activerow[self::CSV_PROJECT_LEAD]);
											$project->setLeader($user);
											break;
										case self::CSV_IDENTIFIER_TYPE_TEAM:
											$team = new TBGTeam($activerow[self::CSV_PROJECT_LEAD]);
											$project->setLeader($team);
											break;
									}
								}
								
								if (isset($activerow[self::CSV_PROJECT_QA]) && isset($activerow[self::CSV_PROJECT_QA_TYPE]))
								{
									switch ($activerow[self::CSV_PROJECT_QA_TYPE])
									{
										case self::CSV_IDENTIFIER_TYPE_USER:
											$user = new TBGUser($activerow[self::CSV_PROJECT_QA]);
											$project->setQaResponsible($user);
											break;
										case self::CSV_IDENTIFIER_TYPE_TEAM:
											$team = new TBGTeam($activerow[self::CSV_PROJECT_QA]);
											$project->setQaResponsible($team);
											break;
									}
								}
								
								if (isset($activerow[self::CSV_PROJECT_DESCR]))
									$project->setDescription($activerow[self::CSV_PROJECT_DESCR]);
									
								if (isset($activerow[self::CSV_PROJECT_DOC_URL]))
									$project->setDocumentationUrl($activerow[self::CSV_PROJECT_DOC_URL]);

								if (isset($activerow[self::CSV_PROJECT_WIKI_URL]))
									$project->setWikiUrl($activerow[self::CSV_PROJECT_WIKI_URL]);
									
								if (isset($activerow[self::CSV_PROJECT_FREELANCE]))
								{
									if ($activerow[self::CSV_PROJECT_FREELANCE] == '1')
										$project->setChangeIssuesWithoutWorkingOnThem(true);
								}
								
								if (isset($activerow[self::CSV_PROJECT_EN_BUILDS]))
								{
									if ($activerow[self::CSV_PROJECT_EN_BUILDS] == '1')
										$project->setBuildsEnabled(true);
								}
								
								if (isset($activerow[self::CSV_PROJECT_EN_COMPS]))
								{
									if ($activerow[self::CSV_PROJECT_EN_COMPS] == '1')
										$project->setComponentsEnabled(true);
								}
								
								if (isset($activerow[self::CSV_PROJECT_EN_EDITIONS]))
								{
									if ($activerow[self::CSV_PROJECT_EN_EDITIONS] == '1')
										$project->setEditionsEnabled(true);
								}
																
								if (isset($activerow[self::CSV_PROJECT_CLIENT]))
									$project->setClient(TBGContext::factory()->TBGClient($activerow[self::CSV_PROJECT_CLIENT]));
								
								if (isset($activerow[self::CSV_PROJECT_SHOW_SUMMARY]))
								{
									if ($activerow[self::CSV_PROJECT_SHOW_SUMMARY] == '1')
										$project->setFrontpageSummaryVisibility(true);
								}
								
								if (isset($activerow[self::CSV_PROJECT_SUMMARY_TYPE]))
									$project->setFrontpageSummaryType($activerow[self::CSV_PROJECT_SUMMARY_TYPE]);
									
								if (isset($activerow[self::CSV_PROJECT_ALLOW_REPORTING]))
									$project->setLocked($activerow[self::CSV_PROJECT_ALLOW_REPORTING]);
							
								if (isset($activerow[self::CSV_PROJECT_AUTOASSIGN]))
									$project->setAutoassign($activerow[self::CSV_PROJECT_AUTOASSIGN]);
								
								if (isset($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]))
									$project->setIssuetypeScheme(TBGContext::factory()->TBGIssuetypeScheme($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]));
								
								if (isset($activerow[self::CSV_PROJECT_WORKFLOW_ID]));
									$project->setWorkflowScheme(TBGContext::factory()->TBGWorkflowScheme($activerow[self::CSV_PROJECT_WORKFLOW_ID]));
								
								$project->save();
							}
							catch (Exception $e)
							{
								$errors[] = TBGContext::getI18n()->__('Row %row% failed: %err%', array('%row%' => $i+1, '%err%' => $e->getMessage()));
							}
						}
						break;
					case self::CSV_TYPE_ISSUES:
						for ($i = 0; $i != count($data); $i++)
						{
							try
							{
								$activerow = $data[$i];

								$issue = new TBGIssue();
								$issue->setTitle($activerow[self::CSV_ISSUE_TITLE]);
								$issue->setProject($activerow[self::CSV_ISSUE_PROJECT]);
								$issue->setIssuetype($activerow[self::CSV_ISSUE_ISSUE_TYPE]);
								
								$issue->save();
								
								if (isset($activerow[self::CSV_ISSUE_DESCR]))
									$issue->setDescription($activerow[self::CSV_ISSUE_DESCR]);
									
								if (isset($activerow[self::CSV_ISSUE_REPRO]))
									$issue->setReproductionSteps($activerow[self::CSV_ISSUE_REPRO]);
								
								if (isset($activerow[self::CSV_ISSUE_STATE]))
									$issue->setState($activerow[self::CSV_ISSUE_STATE]);
								
								if (isset($activerow[self::CSV_ISSUE_STATUS]))
									$issue->setStatus($activerow[self::CSV_ISSUE_STATUS]);
								
								if (isset($activerow[self::CSV_ISSUE_POSTED_BY]))
									$issue->setPostedBy(TBGContext::factory()->TBGUser($activerow[self::CSV_ISSUE_POSTED_BY]));
								
								if (isset($activerow[self::CSV_ISSUE_OWNER]) && isset($activerow[self::CSV_ISSUE_OWNER_TYPE]))
								{
									switch ($activerow[self::CSV_ISSUE_OWNER_TYPE])
									{
										case self::CSV_IDENTIFIER_TYPE_USER:
											$user = new TBGUser($activerow[self::CSV_ISSUE_OWNER]);
											$issue->setOwner($user);
											break;
										case self::CSV_IDENTIFIER_TYPE_TEAM:
											$team = new TBGTeam($activerow[self::CSV_ISSUE_OWNER]);
											$issue->setOwner($team);
											break;
									}
								}
								
								if (isset($activerow[self::CSV_ISSUE_ASSIGNED]) && isset($activerow[self::CSV_ISSUE_ASSIGNED_TYPE]))
								{
									switch ($activerow[self::CSV_ISSUE_ASSIGNED_TYPE])
									{
										case self::CSV_IDENTIFIER_TYPE_USER:
											$user = new TBGUser($activerow[self::CSV_ISSUE_ASSIGNED]);
											$issue->setAssignee($user);
											break;
										case self::CSV_IDENTIFIER_TYPE_TEAM:
											$team = new TBGTeam($activerow[self::CSV_ISSUE_ASSIGNED]);
											$issue->setAssignee($team);
											break;
									}
								}
								
								if (isset($activerow[self::CSV_ISSUE_RESOLUTION]))
									$issue->setResolution($activerow[self::CSV_ISSUE_RESOLUTION]);
									
								if (isset($activerow[self::CSV_ISSUE_PRIORITY]))
									$issue->setPriority($activerow[self::CSV_ISSUE_PRIORITY]);
								
								if (isset($activerow[self::CSV_ISSUE_CATEGORY]))
									$issue->setCategory($activerow[self::CSV_ISSUE_CATEGORY]);
								
								if (isset($activerow[self::CSV_ISSUE_BLOCKING]))
									$issue->setBlocking($activerow[self::CSV_ISSUE_BLOCKING]);
									
								if (isset($activerow[self::CSV_ISSUE_SEVERITY]))
									$issue->setSeverity($activerow[self::CSV_ISSUE_SEVERITY]);
									
								if (isset($activerow[self::CSV_ISSUE_REPRODUCIBILITY]))
									$issue->setReproducability($activerow[self::CSV_ISSUE_REPRODUCIBILITY]);
									
								if (isset($activerow[self::CSV_ISSUE_VOTES]))
									$issue->setVotes($activerow[self::CSV_ISSUE_VOTES]);
								
								if (isset($activerow[self::CSV_ISSUE_PERCENTAGE]))
									$issue->setPercentCompleted($activerow[self::CSV_ISSUE_PERCENTAGE]);
								
								if (isset($activerow[self::CSV_ISSUE_MILESTONE]))
									$issue->setMilestone($activerow[self::CSV_ISSUE_MILESTONE]);
								
								$issue->save();
							}
							catch (Exception $e)
							{
								$errors[] = TBGContext::getI18n()->__('Row %row% failed: %err%', array('%row%' => $i+1, '%err%' => $e->getMessage()));
							}
						}
						break;
				}
				
				// Handle errors
				if (count($errors) != 0)
				{
					$errordiv = '<ul>';
					foreach ($errors as $error)
					{
						$errordiv .= '<li>'.$error.'</li>';
					}
					$errordiv .= '</ul>';
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('errordetail' => $errordiv, 'error' => TBGContext::getI18n()->__('Errors occured while importing, see the error list in the import screen for further details')));
				}
				else
				{
					return $this->renderJSON(array('message' => TBGContext::getI18n()->__('Successfully imported %num% rows!', array('%num%' => count($data)))));
				}
			}
		}
		
		public function runScopes(TBGRequest $request)
		{
			if ($request->isPost())
			{
				$hostname = $request['hostname'];
				$hostname = str_replace(array('http://', 'https://'), array('', ''), $hostname);
				
				$scopename = $request['name'];
				if (!$hostname || TBGScopesTable::getTable()->getByHostname($hostname) instanceof TBGScope)
				{
					$this->scope_hostname_error = true;
				}
				elseif (!$scopename)
				{
					$this->scope_name_error = true;
				}
				else
				{
					$scope = new TBGScope();
					$scope->addHostname($hostname);
					$scope->setName($scopename);
					$scope->setEnabled();
					$scope->save();
					$this->forward(TBGContext::getRouting()->generate('configure_scopes'));
				}
			}
			$this->scope_deleted = TBGContext::getMessageAndClear('scope_deleted');
			$this->scopes = TBGScope::getAll();
		}

		public function runScope(TBGRequest $request)
		{
			$this->scope = new TBGScope($request['id']);
			$modules = TBGModulesTable::getTable()->getModulesForScope($this->scope->getID());
			$this->modules = $modules;
			$this->scope_save_error = TBGContext::getMessageAndClear('scope_save_error');
			$this->scope_saved = TBGContext::getMessageAndClear('scope_saved');

			if ($request->isPost())
			{
				try
				{
					if ($request['scope_action'] == 'delete')
					{
						if (!$this->scope->isDefault())
						{
							$this->scope->delete();
							TBGContext::setMessage('scope_deleted', true);
							$this->forward(make_url('configure_scopes'));
						}
						else
						{
							$this->scope_save_error = TBGContext::getI18n()->__('You cannot delete the default scope');
						}
					}
					else
					{
						if (!$request['name'])
						{
							throw new Exception(TBGContext::getI18n()->__('Please specify a scope name'));
						}
						$this->scope->setName($request['name']);
						$this->scope->setDescription($request['description']);
						$this->scope->setCustomWorkflowsEnabled((bool) $request['custom_workflows_enabled']);
						$this->scope->setMaxWorkflowsLimit((int) $request['workflow_limit']);
						$this->scope->setUploadsEnabled((bool) $request['file_uploads_enabled']);
						$this->scope->setMaxUploadLimit((int) $request['upload_limit']);
						$this->scope->setMaxProjects((int) $request['project_limit']);
						$this->scope->setMaxUsers((int) $request['user_limit']);
						$this->scope->setMaxTeams((int) $request['team_limit']);
						$this->scope->save();

						$enabled_modules = $request['module_enabled'];
						$prev_scope = TBGContext::getScope();
						foreach ($enabled_modules as $module => $enabled)
						{
							if (!TBGContext::getModule($module)->isCore() && !$enabled && array_key_exists($module, $modules))
							{
								$module = TBGModulesTable::getTable()->getModuleForScope($module, $this->scope->getID());
								$module->uninstall($this->scope->getID());
							}
							elseif (!TBGContext::getModule($module)->isCore() && $enabled && !array_key_exists($module, $modules))
							{
								TBGContext::setScope($this->scope);
								TBGModule::installModule($module);
								TBGContext::setScope($prev_scope);
							}
						}
						TBGContext::setMessage('scope_saved', true);
						$this->forward(make_url('configure_scope', array('id' => $this->scope->getID())));
					}
				}
				catch (Exception $e)
				{
					TBGContext::setMessage('scope_save_error', $e->getMessage());
				}
			}
		}

		public function runConfigureRole(TBGRequest $request)
		{
			try
			{
				$role = new TBGRole($request['role_id']);
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid role')));
			}
			if ($role->isSystemRole())
			{
				$access_level = $this->getAccessLevel($request['section'], 'core');
			}
			else
			{
				$access_level = ($this->getUser()->canManageProject($role->getProject())) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
			}

			switch ($request['mode'])
			{
				case 'list_permissions':
					return $this->renderTemplate('configuration/rolepermissionslist', array('role' => $role));
					break;
				case 'edit':
					if (!$access_level == TBGSettings::ACCESS_FULL)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => $this->getI18n()->__('You do not have access to edit these permissions')));
					}
					if ($request->isPost())
					{
						$role->setName($request['name']);
						$role->save();
						$permissions = array();
						foreach ($request['permissions'] as $permission)
						{
							list ($module, $target_id, $permission_key) = explode(',', $permission);
							$p = new TBGRolePermission();
							$p->setRole($role);
							$p->setModule($module);
							$p->setPermission($permission_key);
							if ($target_id) $p->setTargetID($target_id);

							$permissions[] = $p;
						}
						$role->setPermissions($permissions);
						return $this->renderJSON(array('message' => $this->getI18n()->__('Permissions updated'), 'permissions_count' => count($request['permissions']), 'role_name' => $role->getName()));
					}
					return $this->renderTemplate('configuration/rolepermissionsedit', array('role' => $role));
				case 'delete':
					if (!$access_level == TBGSettings::ACCESS_FULL || !$request->isPost())
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => $this->getI18n()->__('This role cannot be removed')));
					}
					$role->delete();
					return $this->renderJSON(array('message' => $this->getI18n()->__('Role deleted')));
			}
		}

		public function runConfigureRoles(TBGRequest $request)
		{
			if ($request->isPost())
			{
				if (trim($request['role_name']) == '')
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => $this->getI18n()->__('You have to specify a name for this role')));
				}
				$role = new TBGRole();
				$role->setName($request['role_name']);
				$role->save();
				return $this->renderJSON(array('content' => $this->getTemplateHTML('configuration/role', array('role' => $role))));
			}
			$this->roles = TBGRole::getAll();
		}

		public function runSiteIcons(TBGRequest $request)
		{
			if ($this->getAccessLevel($request['section'], 'core') == TBGSettings::ACCESS_FULL)
			{
				if ($request->isPost())
				{
					switch ($request['small_icon_action'])
					{
						case 'upload_file':
							$file = $request->handleUpload('small_icon');
							TBGSettings::saveSetting(TBGSettings::SETTING_FAVICON_TYPE, TBGSettings::APPEARANCE_FAVICON_CUSTOM);
							TBGSettings::saveSetting(TBGSettings::SETTING_FAVICON_ID, $file->getID());
							break;
						case 'clear_file':
							TBGSettings::saveSetting(TBGSettings::SETTING_FAVICON_TYPE, TBGSettings::APPEARANCE_FAVICON_THEME);
							break;
					}
					switch ($request['large_icon_action'])
					{
						case 'upload_file':
							$file = $request->handleUpload('large_icon');
							TBGSettings::saveSetting(TBGSettings::SETTING_HEADER_ICON_TYPE, TBGSettings::APPEARANCE_HEADER_CUSTOM);
							TBGSettings::saveSetting(TBGSettings::SETTING_HEADER_ICON_ID, $file->getID());
							break;
						case 'clear_file':
							TBGSettings::saveSetting(TBGSettings::SETTING_HEADER_ICON_TYPE, TBGSettings::APPEARANCE_HEADER_THEME);
							break;
					}
				}
				$route = TBGContext::getRouting()->generate('configure_settings');
				if ($request->isAjaxCall())
				{
					return $this->renderJSON(array('forward' => $route));
				}
				else
				{
					$this->forward($route);
				}
			}
			return $this->forward403($this->getI18n()->__("You don't have access to perform this action"));
		}
	}
