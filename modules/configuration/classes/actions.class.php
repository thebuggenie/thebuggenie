<?php

	class configurationActions extends BUGSaction
	{
		const ACCESS_READ = 1;
		const ACCESS_FULL = 2;
		
		/**
		 * Pre-execute function
		 * 
		 * @param BUGSrequest 	$request
		 * @param string		$action
		 */
		public function preExecute(BUGSrequest $request, $action)
		{
			// forward 403 if you're not allowed here
			$this->forward403unless(BUGScontext::getUser()->hasPermission("b2saveconfig", 0, 'core', true) ||
									BUGScontext::getUser()->hasPermission("b2viewconfig", 0, 'core', true));
			
			$this->access_level = $this->getAccessLevel($request->getParameter('section'), 'core');
			
			$this->getResponse()->setPage('config');
			$this->getResponse()->setProjectMenuStripHidden();
			
		}
		
		/**
		 * Configuration main page
		 * 
		 * @param BUGSrequest $request
		 */
		public function runIndex(BUGSrequest $request)
		{
			$general_config_sections = array();
			$data_config_sections = array();
			$module_config_sections = array();
			$general_config_sections[12] = array('route' => 'configure_settings', 'description' => __('Settings'), 'icon' => 'general', 'details' => __('Every setting in the bug genie can be adjusted in this section.'));
			$general_config_sections[3] = array('route' => 'configure_files', 'description' => __('Uploads &amp; attachments'), 'icon' => 'files', 'details' => __('All settings related to file uploads are controlled from this section.'));
			$general_config_sections[5] = array('route' => 'configure_permissions', 'description' => __('Permissions'), 'icon' => 'permissions', 'details' => __('Configure permissions in this section'));
			if (BUGScontext::getUser()->getScope()->getID() == 1)
			{
				$general_config_sections[14] = array('route' => 'configure_scopes', 'description' => __('Scopes'), 'icon' => 'scopes', 'details' => __('Scopes are self-contained Bug Genie environments. Configure them here.'));
				$data_config_sections[16] = array('route' => 'configure_import', 'description' => __('Import data'), 'icon' => 'import', 'details' => __('Upgrading from an older version? Import your data from here.'));
			}
			
			$data_config_sections[10] = array('route' => 'configure_projects', 'description' => __('Projects'), 'icon' => 'projects', 'details' => __('Set up all projects in this configuration section.'));
			$data_config_sections[4] = array('icon' => 'resolutiontypes', 'description' => __('Issue fields'), 'route' => 'configure_issuefields', 'details' => __('Status types, resolution types, categories, custom fields, etc. are configurable from this section.'));
			$data_config_sections[2] = array('route' => 'configure_users', 'description' => __('Users, teams &amp; groups'), 'icon' => 'users', 'details' => __('Manage users, user groups and user teams from this section.'));
			$module_config_sections[15][] = array('route' => 'configure_modules', 'description' => __('Module settings'), 'icon' => 'modules', 'details' => __('Manage Bug Genie extensions from this section. New modules are installed from here.'), 'module' => 'core');
			foreach (BUGScontext::getModules() as $module)
			{
				if ($module->hasAccess() && $module->isVisibleInConfig())
				{
					$module_config_sections[15][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => $module->getConfigTitle(), 'icon' => $module->getName(), 'details' => $module->getConfigDescription(), 'module' => $module->getName());
				}
			}
			$this->general_config_sections = $general_config_sections; 
			$this->data_config_sections = $data_config_sections;
			$this->module_config_sections = $module_config_sections;
		}
		
		/**
		 * Configure general and server settings
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runSettings(BUGSrequest $request)
		{
			if (BUGScontext::getRequest()->isMethod(BUGSrequest::POST))
			{
				$settings = array('theme_name', 'user_themes', 'onlinestate', 'offlinestate', 'awaystate', 'singleprojecttracker',
									'requirelogin', 'allowreg', 'defaultgroup', 'returnfromlogin', 'returnfromlogout', 'permissive',
									'showloginbox', 'limit_registration', 'showprojectsoverview', 'showprojectsoverview', 
									'cleancomments', 'b2_name', 'b2_tagline', 'url_subdir', 'local_path', 'server_timezone');
				
				foreach ($settings as $setting)
				{
					if (BUGScontext::getRequest()->getParameter($setting) !== null)
					{
						if ($setting == 'b2_name' || $setting == 'b2_tagline')
						{
							BUGSsettings::saveSetting($setting, BUGScontext::getRequest()->getParameter($setting, null, false));
						}
						else
						{
							BUGSsettings::saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
						}
					}
				}
			}
			
			$this->themes = BUGScontext::getThemes();
			$this->languages = BUGSi18n::getLanguages();
			
		}

		/**
		 * Configure projects
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjects(BUGSrequest $request)
		{
			$this->allProjects = BUGSproject::getAll();
		}
		
		/**
		 * Configure issue fields
		 *
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureIssuefields(BUGSrequest $request)
		{
			$i18n = BUGScontext::getI18n();
			$builtin_types = array();
			$builtin_types['status'] = $i18n->__('Status types');
			$builtin_types['resolution'] = $i18n->__('Resolution types');
			$builtin_types['priority'] = $i18n->__('Priority levels');
			$builtin_types['severity'] = $i18n->__('Severity levels');
			$builtin_types['category'] = $i18n->__('Categories');
			$builtin_types['reproducability'] = $i18n->__('Reproducability grades');

			$this->builtin_types = $builtin_types;
		}

		/**
		 * Get issue fields list for a specific issue type
		 *
		 * @param BUGSrequest $request
		 */
		public function runConfigureIssuefieldsGetOptions(BUGSrequest $request)
		{
			return $this->renderComponent('issuefields_builtin', array('type' => $request->getParameter('type')));
		}

		/**
		 * Add or delete an issue field option
		 *
		 * @param BUGSrequest $request
		 */
		public function runConfigureIssuefieldsAction(BUGSrequest $request)
		{
			$i18n = BUGScontext::getI18n();
			$types = BUGSdatatype::getTypes();

			if (array_key_exists($request->getParameter('type'), $types))
			{
				switch ($request->getParameter('mode'))
				{
					case 'add':
						if ($request->getParameter('name'))
						{
							$item = call_user_func(array($types[$request->getParameter('type')], 'createNew'), $request->getParameter('name'), $request->getParameter('itemdata'));
							return $this->renderJSON(array('failed' => false, 'title' => BUGScontext::getI18n()->__('The option was added'), 'content' => $this->getTemplateHTML('issuefield_builtin', array('item' => $item, 'type' => $request->getParameter('type')))));
						}
						return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please provide a valid name')));
					case 'delete':
						if ($request->hasParameter('id'))
						{
							call_user_func(array($types[$request->getParameter('type')], 'delete'), (int) $request->getParameter('id'));
							return $this->renderJSON(array('failed' => false, 'title' => $i18n->__('The option was deleted')));
						}
						return $this->renderJSON(array('failed' => true, 'error' => $i18n->__('Invalid id or type')));
						break;
				}
			}
		}

		/**
		 * Configure modules
		 *
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureModules(BUGSrequest $request)
		{
			$this->module_message = BUGScontext::getMessageAndClear('module_message');
			$this->module_error = BUGScontext::getMessageAndClear('module_error');
			$this->modules = BUGScontext::getModules();
			$this->uninstalled_modules = BUGScontext::getUninstalledModules();
		}

		/**
		 * Find users and show selection box
		 * 
		 * @param BUGSrequest $request The request object
		 */		
		public function runFindAssignee(BUGSrequest $request)
		{
			$this->forward403unless($request->isMethod(BUGSrequest::POST));

			$this->message = false;
			
			if ($request->getParameter('find_by'))
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
				$this->users = BUGSuser::findUsers($request->getParameter('find_by'), 10);
				$this->teams = BUGSteam::findTeams($request->getParameter('find_by'));
				$this->customers = BUGScustomer::findCustomers($request->getParameter('find_by'));
			}
			else
			{
				$this->message = true;
			}
		}
		
		/**
		 * Adds a user, team or a customer to a project
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runAssignToProject(BUGSrequest $request)
		{
			$this->forward403unless($request->isMethod(BUGSrequest::POST));
									
			if ($this->access_level = self::ACCESS_FULL)
			{
				try
				{
					$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
				}
				catch (Exception $e) {}
				
				$this->forward403unless($this->theProject instanceof BUGSproject);
				
				$assignee_type = $request->getParameter('assignee_type');
				$assignee_id = $request->getParameter('assignee_id');
				
				try
				{
					switch ($assignee_type)
					{
						case 'user':
							$assignee = BUGSfactory::userLab($assignee_id);
							break;
						case 'team':
							$assignee = BUGSfactory::teamLab($assignee_id);
							break;
						case 'customer':
							$assignee = BUGSfactory::customerLab($assignee_id);
							break;
						default:
							$this->forward403();
							break;
					}
				}
				catch (Exception $e)
				{
					$this->forward403();
				}
				
				$assignee_role = $request->getParameter('role');
				$target_info = explode('_', $request->getParameter('target'));
				$this->forward403unless(count($target_info) == 2);
				
				switch ($target_info[0])
				{
					case 'project':
						$this->theProject->addAssignee($assignee, $assignee_role);
						break;
					case 'edition':
						foreach ($this->theProject->getEditions() as $e_id => $edition)
						{
							if ($e_id = $target_info[1])
							{
								$edition->addAssignee($assignee, $assignee_role);
								break;
							}
						}
						break;
					case 'component':
						foreach ($this->theProject->getComponents() as $c_id => $component)
						{
							if ($c_id = $target_info[1])
							{
								$component->addAssignee($assignee, $assignee_role);
								break;
							}
						}
						break;
						break;
					default:
						$this->forward403();
						break;
				}
				
				return $this->renderTemplate('projects_assignees', array('project' => $this->theProject));
			}
			
		}

		/**
		 * Configure project editions and components
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectEditionsAndComponents(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof BUGSproject);
		}

		/**
		 * Configure project data types
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectOther(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof BUGSproject);
		}

		/**
		 * Configure project data types
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectMilestones(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof BUGSproject);
			$this->milestones = $this->theProject->getAllMilestones();
		}
		
		/**
		 * Updates visible issue types
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectUpdateOther(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('saved' => false, 'error' => BUGScontext::getI18n()->__('This project does not exist')));
			}
			
			$this->forward403unless($this->theProject instanceof BUGSproject && $request->hasParameter('milestones_or_issuetypes'));

			try
			{
				switch ($request->getParameter('milestones_or_issuetypes'))
				{
					case 'issuetypes':
						$this->theProject->setFrontpageSummaryType('issuetypes');
						$this->theProject->save();
						$this->theProject->clearVisibleIssuetypes();
						foreach ($request->getParameter('showissuetype', array()) as $issuetype_id)
						{
							$this->theProject->addVisibleIssuetype($issuetype_id);
						}
						break;
					case 'milestones':
						$this->theProject->setFrontpageSummaryType('milestones');
						$this->theProject->save();
						$this->theProject->clearVisibleMilestones();
						foreach ($request->getParameter('showmilestone', array()) as $milestone_id)
						{
							$this->theProject->addVisibleMilestone($milestone_id);
						}
						break;
					case '':
						$this->theProject->setFrontpageSummaryType('');
						$this->theProject->save();
						break;
				}
				return $this->renderJSON(array('saved' => true, 'title' => BUGScontext::getI18n()->__('Your changes has been saved'), 'message' => ''));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('saved' => false, 'error' => BUGScontext::getI18n()->__('An error occured'), 'message' => $e->getMessage()));
			}
		}

		/**
		 * Configure project builds
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectDevelopers(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof BUGSproject);
		}
		
		/**
		 * Configure project leaders
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runSetProjectLead(BUGSrequest $request)
		{
			try
			{
				$project = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($project instanceof BUGSproject);
			
			if ($request->hasParameter('value'))
			{
				BUGSlogging::log('has value');
				if ($request->hasParameter('identifiable_type'))
				{
					BUGSlogging::log('has type');
					if (in_array($request->getParameter('identifiable_type'), array(BUGSidentifiableclass::TYPE_USER, BUGSidentifiableclass::TYPE_TEAM)))
					{
						switch ($request->getParameter('identifiable_type'))
						{
							case BUGSidentifiableclass::TYPE_USER:
								BUGSlogging::log('is user');
								$identified = BUGSfactory::userLab($request->getParameter('value'));
								break;
							case BUGSidentifiableclass::TYPE_TEAM:
								BUGSlogging::log('is team');
								$identified = BUGSfactory::teamLab($request->getParameter('value'));
								break;
						}
						BUGSlogging::log($request->getParameter('field'));
						if ($identified instanceof BUGSidentifiableclass)
						{
							BUGSlogging::log('is identifiable');
							if ($request->getParameter('field') == 'owned_by') $project->setOwner($identified);
							elseif ($request->getParameter('field') == 'qa_by') $project->setQA($identified);
							elseif ($request->getParameter('field') == 'lead_by') $project->setLeadBy($identified);
							$project->save();
						}
					}
					else
					{
						if ($request->getParameter('field') == 'owned_by') $project->unsetOwner();
						elseif ($request->getParameter('field') == 'qa_by') $project->unsetQA();
						elseif ($request->getParameter('field') == 'lead_by') $project->unsetLeadBy();
						$project->save();
					}
				}
				if ($request->getParameter('field') == 'owned_by')
					return $this->renderJSON(array('field' => (($project->hasOwner()) ? array('id' => $project->getOwnerID(), 'name' => (($project->getOwnerType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $project->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $project->getOwner())))) : array('id' => 0))));
				elseif ($request->getParameter('field') == 'lead_by')
					return $this->renderJSON(array('field' => (($project->hasLeader()) ? array('id' => $project->getLeaderID(), 'name' => (($project->getLeaderType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $project->getLeader())) : $this->getComponentHTML('main/teamdropdown', array('team' => $project->getLeader())))) : array('id' => 0))));
				elseif ($request->getParameter('field') == 'qa_by')
					return $this->renderJSON(array('field' => (($project->hasQA()) ? array('id' => $project->getQAID(), 'name' => (($project->getQAType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $project->getQA())) : $this->getComponentHTML('main/teamdropdown', array('team' => $project->getQA())))) : array('id' => 0))));
			}
		}
		
		/**
		 * Configure project settings
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectSettings(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			if (!$this->theProject instanceof BUGSproject) return $this->return404(BUGScontext::getI18n()->__("This project doesn't exist"));
			
			$this->statustypes = BUGSstatus::getAll();
			if ($request->isAjaxCall())
			{
				if ($request->hasParameter('release_month') && $request->hasParameter('release_day') && $request->hasParameter('release_year'))
				{
					$release_date = mktime(0, 0, 1, $request->getParameter('release_month'), $request->getParameter('release_day'), $request->getParameter('release_year'));
					$this->theProject->setReleaseDate($release_date);
				}

				$this->theProject->setName($request->getParameter('project_name'));
				$this->theProject->setUsePrefix((bool) $request->getParameter('use_prefix'));
				$this->theProject->setPrefix($request->getParameter('prefix'));
				$this->theProject->setDescription($request->getParameter('description', null, false));
				$this->theProject->setHomepage($request->getParameter('homepage'));
				$this->theProject->setDocumentationURL($request->getParameter('doc_url'));
				$this->theProject->setDefaultStatus($request->getParameter('defaultstatus'));
				$this->theProject->setPlannedReleased($request->getParameter('planned_release'));
				$this->theProject->setTasksEnabled((bool) $request->getParameter('enable_tasks'));
				$this->theProject->setReleased((int) $request->getParameter('released'));
				$this->theProject->setVotesEnabled((bool) $request->getParameter('votes'));
				$this->theProject->setTimeUnit((int) $request->getParameter('time_unit'));
				$this->theProject->setHoursPerDay($request->getParameter('hrs_pr_day'));
				$this->theProject->setLocked((bool) $request->getParameter('locked'));
				$this->theProject->setBuildsEnabled((bool) $request->getParameter('enable_builds'));
				$this->theProject->setEditionsEnabled((bool) $request->getParameter('enable_editions'));
				$this->theProject->setComponentsEnabled((bool) $request->getParameter('enable_components'));
				$this->theProject->setChangeIssuesWithoutWorkingOnThem((bool) $request->getParameter('allow_changing_without_working'));
				$this->theProject->save();
				return $this->renderJSON(array('saved' => true, 'title' => BUGScontext::getI18n()->__('Your changes has been saved'), 'message' => ''));
			}
		}
		
		/**
		 * Configure a project edition with builds and settings
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureProjectEdition(BUGSrequest $request)
		{
			try
			{
				$this->theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
				$this->theEdition = BUGSfactory::editionLab($request->getParameter('edition_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof BUGSproject && $this->theEdition instanceof BUGSedition);
			
			if ($request->isAjaxCall())
			{
				if ($request->hasParameter('release_month') && $request->hasParameter('release_day') && $request->hasParameter('release_year'))
				{
					$release_date = mktime(0, 0, 1, $request->getParameter('release_month'), $request->getParameter('release_day'), $request->getParameter('release_year'));
					$this->theEdition->setReleaseDate($release_date);
				}

				$this->theEdition->setName($request->getParameter('edition_name'));
				$this->theEdition->setDescription($request->getParameter('description', null, false));
				$this->theEdition->setDocumentationURL($request->getParameter('doc_url'));
				$this->theEdition->setPlannedReleased($request->getParameter('planned_release'));
				$this->theEdition->setReleased((int) $request->getParameter('released'));
				$this->theEdition->setLocked((bool) $request->getParameter('locked'));
				$this->theEdition->save();
				return $this->renderJSON(array('saved' => true));
			}
			
			switch ($request->getParameter('mode'))
			{
				case 'releases':
				case 'components':
					$this->selected_section = $request->getParameter('mode');
					break;
				default:
					$this->selected_section = 'general';
			}
		}
		
		/**
		 * Add a project (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runAddProject(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			$i18n = BUGScontext::getI18n();

			if ($p_name = $request->getParameter('p_name'))
			{
				$aProject = BUGSproject::createNew($p_name);
				return $this->renderJSON(array('title' => $i18n->__('The project has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'html' => $this->getTemplateHTML('projectbox', array('project' => $aProject))));
			}
			else
			{
				return $this->renderJSON(array('failed' => true, "error" => $i18n->__('The project could not be added')."', '".$i18n->__('Please specify a project name')));
			}
		}
		
		/**
		 * Add an edition (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runAddEdition(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			$i18n = BUGScontext::getI18n();

			try
			{
				if ($p_id = $request->getParameter('project_id'))
				{
					if (BUGScontext::getUser()->hasPermission('b2projectaccess', $p_id))
					{
						if ($e_name = $request->getParameter('e_name'))
						{
							$project = BUGSfactory::projectLab($p_id);
							$edition = $project->addEdition($e_name);
							return $this->renderJSON(array('title' => $i18n->__('The edition has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'html' => $this->getTemplateHTML('editionbox', array('edition' => $edition))));
						}
						else
						{
							throw new Exception($i18n->__('You need to specify a name for the new edition'));
						}
					}
					else
					{
						throw new Exception($i18n->__('You do not have access to this project'));
					}
				}
				else
				{
					throw new Exception($i18n->__('You need to specify a project id'));
				}
			}
			catch (Exception $e) 
			{
				return $this->renderJSON(array('failed' => true, "error" => $i18n->__('The edition could not be added').", ".$e->getMessage()));
			}
		}

		/**
		 * Perform actions on a build (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runBuildAction(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);

			try
			{
				if ($b_id = $request->getParameter('build_id'))
				{
					if (BUGScontext::getUser()->hasPermission('b2buildaccess', $b_id))
					{
						$build = BUGSfactory::buildLab($b_id);
						switch ($request->getParameter('build_action'))
						{
							case 'markdefault':
								$build->setDefault();
								$this->show_mode = 'all';
								break;
							case 'delete':
								$build->delete();
								return $this->renderJSON(array('deleted' => true));
								break;
							case 'addtoopen':
								$build->addToOpenParentIssues((int) $request->getParameter('status'), (int) $request->getParameter('category'), (int) $request->getParameter('issuetype'));
								return $this->renderJSON(array('saved' => true, 'title' => BUGScontext::getI18n()->__('The selected build has been added to open issues based on your selections'), 'message' => ''));
								break;
							case 'release':
								$build->setReleased(true);
								$build->setReleaseDate();
								$build->save();
								$this->show_mode = 'one';
								break;
							case 'retract':
								$build->setReleased(false);
								$build->setReleaseDate(0);
								$build->save();
								$this->show_mode = 'one';
								break;
							case 'lock':
								$build->setLocked(true);
								$build->save();
								$this->show_mode = 'one';
								break;
							case 'unlock':
								$build->setLocked(false);
								$build->save();
								$this->show_mode = 'one';
								break;
							case 'update':
								if ($b_name = $request->getParameter('build_name'))
								{
									$build->setName($b_name);
									$build->setVersionMajor($request->getParameter('ver_mj'));
									$build->setVersionMinor($request->getParameter('ver_mn'));
									$build->setVersionRevision($request->getParameter('ver_rev'));
									if ($request->hasParameter('release_month') && $request->hasParameter('release_day') && $request->hasParameter('release_year'))
									{
										$release_date = mktime(0, 0, 1, $request->getParameter('release_month'), $request->getParameter('release_day'), $request->getParameter('release_year'));
										$build->setReleaseDate($release_date);
									}
									$build->save();
								}
								else
								{
									throw new Exception(BUGScontext::getI18n()->__('The build / release needs to have a name'));
								}
								$this->show_mode = 'one';
								break;
						}
					}
					else
					{
						throw new Exception(BUGScontext::getI18n()->__('You do not have access to this build / release'));
					}
				}
				else
				{
					throw new Exception(BUGScontext::getI18n()->__('You need to specify a build / release'));
				}
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, "error" => BUGScontext::getI18n()->__('Could not update the build / release').", ".$e->getMessage()));
			}
			
			$this->build = $build;
		}
		
		/**
		 * Add a build (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runAddBuild(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			$i18n = BUGScontext::getI18n();

			try
			{
				if ($p_id = $request->getParameter('project_id'))
				{
					if (BUGScontext::getUser()->hasPermission('b2projectaccess', $p_id))
					{
						if ($b_name = $request->getParameter('build_name'))
						{
							if ($e_id = $request->getParameter('edition_id'))
							{
								if (BUGScontext::getUser()->hasPermission('b2editionaccess', $e_id))
								{
									$build = BUGSbuild::createNew($b_name, null, $e_id, $request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
								}
								else
								{
									throw new Exception($i18n->__('You do not have access to this edition'));
								}
							}
							else
							{
								$build = BUGSbuild::createNew($b_name, $p_id, null, $request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
							}
							return $this->renderJSON(array('title' => $i18n->__('The build has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'html' => $this->getTemplateHTML('buildbox', array('build' => $build, 'access_level' => $this->access_level))));
						}
						else
						{
							throw new Exception($i18n->__('You need to specify a name for the new build'));
						}
					}
					else
					{
						throw new Exception($i18n->__('You do not have access to this project'));
					}
				}
				else
				{
					throw new Exception($i18n->__('You need to specify a project id'));
				}
			}
			catch (Exception $e) 
			{
				return $this->renderJSON(array('failed' => true, "error" => $i18n->__('The build could not be added').", ".$e->getMessage()));
			}
		}
		
		/**
		 * Add a component (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runAddComponent(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			$i18n = BUGScontext::getI18n();

			try
			{
				if ($p_id = $request->getParameter('project_id'))
				{
					if (BUGScontext::getUser()->hasPermission('b2projectaccess', $p_id))
					{
						if ($c_name = $request->getParameter('c_name'))
						{
							$project = BUGSfactory::projectLab($p_id);
							$component = $project->addComponent($c_name);
							return $this->renderJSON(array('title' => $i18n->__('The component has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'html' => $this->getTemplateHTML('componentbox', array('component' => $component))));
						}
						else
						{
							throw new Exception($i18n->__('You need to specify a name for the new component'));
						}
					}
					else
					{
						throw new Exception($i18n->__('You do not have access to this project'));
					}
				}
				else
				{
					throw new Exception($i18n->__('You need to specify a project id'));
				}
			}
			catch (Exception $e) 
			{
				return $this->renderJSON(array('failed' => true, "error" => $i18n->__('The component could not be added').", ".$e->getMessage()));
			}
		}

		/**
		 * Add a component (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runAddMilestone(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			$i18n = BUGScontext::getI18n();

			try
			{
				if ($p_id = $request->getParameter('project_id'))
				{
					if (BUGScontext::getUser()->hasPermission('b2projectaccess', $p_id))
					{
						if (($m_name = $request->getParameter('name')) && trim($m_name) != '')
						{
							$theProject = BUGSfactory::projectLab($p_id);
							$theMilestone = $theProject->addMilestone($m_name, $request->getParameter('milestone_type', 1));
							return $this->renderJSON(array('title' => $i18n->__('The milestone has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'html' => $this->getTemplateHTML('milestonebox', array('milestone' => $theMilestone))));
						}
						else
						{
							throw new Exception($i18n->__('You need to specify a name for the new milestone'));
						}
					}
					else
					{
						throw new Exception($i18n->__('You do not have access to this project'));
					}
				}
				else
				{
					throw new Exception($i18n->__('You need to specify a project id'));
				}
			}
			catch (Exception $e) 
			{
				return $this->renderJSON(array('failed' => true, "error" => $i18n->__('The milestone could not be added').", ".$e->getMessage()));
			}
		}
		
		/**
		 * Perform actions on a build (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runMilestoneAction(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);

			try
			{
				if ($m_id = $request->getParameter('milestone_id'))
				{
					$theMilestone = BUGSfactory::milestoneLab($m_id);
					if ($theMilestone->hasAccess())
					{
						switch ($request->getParameter('milestone_action'))
						{
							case 'update':
								if (($m_name = $request->getParameter('name')) && trim($m_name) != '')
								{
									$theMilestone->setName($m_name);
									$theMilestone->setScheduled((bool) $request->getParameter('is_scheduled'));
									$theMilestone->setStarting((bool) $request->getParameter('is_starting'));
									$theMilestone->setDescription($request->getParameter('description', null, false));
									$theMilestone->setType($request->getParameter('milestone_type', 1));
									if ($theMilestone->isScheduled())
									{
										if ($request->hasParameter('sch_month') && $request->hasParameter('sch_day') && $request->hasParameter('sch_year'))
										{
											$scheduled_date = mktime(0, 0, 0, BUGScontext::getRequest()->getParameter('sch_month'), BUGScontext::getRequest()->getParameter('sch_day'), BUGScontext::getRequest()->getParameter('sch_year'));
											$theMilestone->setScheduledDate($scheduled_date);
										}
									}
									if ($theMilestone->isStarting())
									{
										if ($request->hasParameter('starting_month') && $request->hasParameter('starting_day') && $request->hasParameter('starting_year'))
										{
											$starting_date = mktime(0, 0, 0, BUGScontext::getRequest()->getParameter('starting_month'), BUGScontext::getRequest()->getParameter('starting_day'), BUGScontext::getRequest()->getParameter('starting_year'));
											$theMilestone->setStartingDate($starting_date);
										}
									}
									$theMilestone->save();
									return $this->renderTemplate('milestonebox', array('milestone' => $theMilestone));
								}
								else
								{
									throw new Exception(BUGScontext::getI18n()->__('The milestone needs to have a name'));
								}
								break;
							case 'delete':
								$theMilestone->delete();
								return $this->renderJSON(array('deleted' => true));
								break;
						}
					}
					else
					{
						throw new Exception(BUGScontext::getI18n()->__('You do not have access to this milestone'));
					}
				}
				else
				{
					throw new Exception(BUGScontext::getI18n()->__('You need to specify a milestone'));
				}
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, "error" => BUGScontext::getI18n()->__('Could not update the milestone').", ".$e->getMessage()));
			}
			return $this->renderJSON(array('done' => true));
		}
						
		
		/**
		 * Add or remove a component to/from an edition (AJAX call)
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runEditEditionComponent(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			
			try
			{
				$theEdition   = BUGSfactory::editionLab($request->getParameter('edition_id'));
				if ($request->getParameter('mode') == 'add')
				{
					$theEdition->addComponent($request->getParameter('component_id'));
				}
				elseif ($request->getParameter('mode') == 'remove')
				{
					$theEdition->removeComponent($request->getParameter('component_id'));
				}
				return $this->renderJSON(array('saved' => true));
			}
			catch (Exception $e) 
			{
				return $this->renderJSON(array('failed' => true, "error" => BUGScontext::getI18n()->__('The component could not be added to this edition').", ".$e->getMessage()));
			}
			
		}

		/**
		 * Edit a component
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runEditComponent(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			
			try
			{
				$theComponent = BUGSfactory::componentLab($request->getParameter('component_id'));
				if ($request->getParameter('mode') == 'update')
				{
					$theComponent->setName($request->getParameter('c_name', ''));
					return $this->renderJSON(array('saved' => true, 'newname' => $theComponent->getName()));
				}
				elseif ($request->getParameter('mode') == 'delete')
				{
					$theComponent->delete();
				}
			}
			catch (Exception $e) 
			{
				return $this->renderJSON(array('failed' => true, "error" => BUGScontext::getI18n()->__('Could not edit this component').", ".$e->getMessage()));
			}
		}
		
		/**
		 * Delete a project
		 * 
		 * @param BUGSrequest $request The request object
		 */
		public function runDeleteProject(BUGSrequest $request)
		{
			$this->forward403unless($this->access_level = self::ACCESS_FULL);
			
			try
			{
				$theProject = BUGSfactory::projectLab($request->getParameter('project_id'));
				$theProject->delete();
				$theProject->save();
				return $this->renderJSON(array('deleted' => true));
			}
			catch (Exception $e) 
			{
				BUGScontext::loadLibrary('ui');
				return $this->renderJSON(array('deleted' => false));
			}
		}

		/**
		 * Perform an action on a module
		 *
		 * @param BUGSrequest $request The request object
		 */
		public function runModuleAction(BUGSrequest $request)
		{
			try
			{
				if ($request->getParameter('mode') == 'install' && file_exists(BUGScontext::getIncludePath() . 'modules/' . $request->getParameter('module_key') . '/module'))
				{
					if (BUGSmodule::installModule($request->getParameter('module_key')))
					{
						BUGScontext::setMessage('module_message', BUGScontext::getI18n()->__('The module "%module_name%" was installed successfully', array('%module_name%' => $request->getParameter('module_key'))));
					}
					else
					{
						BUGScontext::setMessage('module_error', BUGScontext::getI18n()->__('There was an error install the module %module_name%', array('%module_name%' => $request->getParameter('module_key'))));
					}
				}
				else
				{
					$module = BUGScontext::getModule($request->getParameter('module_key'));
					switch ($request->getParameter('mode'))
					{
						case 'disable':
							$module->disable();
							break;
						case 'enable':
							$module->enable();
							break;
						case 'uninstall':
							$module->uninstall();
							BUGScontext::setMessage('module_message', BUGScontext::getI18n()->__('The module "%module_name%" was uninstalled successfully', array('%module_name%' => $module->getName())));
							break;
					}
				}
			}
			catch (Exception $e)
			{
				BUGSlogging::log('Trying to run action ' . $request->getParameter('mode') . ' on module ' . $request->getParameter('module_key') . ' which is an invalid module', 'main', BUGSlogging::LEVEL_FATAL);
				BUGScontext::setMessage('module_error', BUGScontext::getI18n()->__('This module (%module_name%) does not exist', array('%module_name%' => $request->getParameter('module_key'))));
			}
			$this->forward(BUGScontext::getRouting()->generate('configure_modules'));
		}
		
		/**
		 * Configure a module
		 *
		 * @param BUGSrequest $request The request object
		 */
		public function runConfigureModule(BUGSrequest $request)
		{
			try
			{
				$module = BUGScontext::getModule($request->getParameter('config_module'));
				if (!$module->hasConfigSettings())
				{
					throw new Exception('module not configurable');
				}
				else
				{
					if ($request->isMethod(BUGSrequest::POST) && $this->access_level == self::ACCESS_FULL)
					{
						try
						{
							$module->postConfigSettings();
							BUGScontext::setMessage('module_message', BUGScontext::getI18n()->__('Settings saved successfully'));
						}
						catch (Exception $e)
						{
							BUGScontext::setMessage('module_error', $e->getMessage());
						}
						$this->forward(BUGScontext::getRouting()->generate('configure_module', array('config_module' => $request->getParameter('config_module'))));
					}
					$this->module = $module;
				}
			}
			catch (Exception $e)
			{
				BUGSlogging::log('Trying to configure module ' . $request->getParameter('config_module') . " which isn't configurable", 'main', BUGSlogging::LEVEL_FATAL);
				BUGScontext::setMessage('module_error', BUGScontext::getI18n()->__('The module "%module_name%" is not configurable', array('%module_name%' => $request->getParameter('config_module'))));
				$this->forward(BUGScontext::getRouting()->generate('configure_modules'));
			}
			$this->module_message = BUGScontext::getMessageAndClear('module_message');
			$this->module_error = BUGScontext::getMessageAndClear('module_error');
			$this->module_error_details = BUGScontext::getMessageAndClear('module_error_details');
		}

		public function getAccessLevel($section, $module)
		{
			return (BUGScontext::getUser()->hasPermission("b2saveconfig", $section, $module, true)) ? self::ACCESS_FULL : self::ACCESS_READ;
		}

		
	}

?>
