<?php

	class configurationActions extends TBGAction
	{
		const ACCESS_READ = 1;
		const ACCESS_FULL = 2;

		/**
		 * Pre-execute function
		 * 
		 * @param TBGRequest 	$request
		 * @param string		$action
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			// forward 403 if you're not allowed here
			$this->forward403unless(TBGContext::getUser()->canAccessConfigurationPage());
			
			$this->access_level = $this->getAccessLevel($request->getParameter('section'), 'core');
			
			$this->getResponse()->setPage('config');
			$this->getResponse()->setProjectMenuStripHidden();
			
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
			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_SETTINGS] = array('route' => 'configure_settings', 'description' => $i18n->__('Settings'), 'icon' => 'general', 'details' => $i18n->__('Every setting in the bug genie can be adjusted in this section.'));
			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_PERMISSIONS] = array('route' => 'configure_permissions', 'description' => $i18n->__('Permissions'), 'icon' => 'permissions', 'details' => $i18n->__('Configure permissions in this section'));
			$general_config_sections[TBGSettings::CONFIGURATION_SECTION_UPLOADS] = array('route' => 'configure_files', 'description' => $i18n->__('Uploads &amp; attachments'), 'icon' => 'files', 'details' => $i18n->__('All settings related to file uploads are controlled from this section.'));
			if (TBGContext::getUser()->getScope()->getID() == 1)
			{
				//$general_config_sections[TBGSettings::CONFIGURATION_SECTION_SCOPES] = array('route' => 'configure_scopes', 'description' => $i18n->__('Scopes'), 'icon' => 'scopes', 'details' => $i18n->__('Scopes are self-contained Bug Genie environments. Configure them here.'));
				//$data_config_sections[TBGSettings::CONFIGURATION_SECTION_IMPORT] = array('route' => 'configure_import', 'description' => $i18n->__('Import data'), 'icon' => 'import', 'details' => $i18n->__('Upgrading from an older version? Import your data from here.'));
			}
			
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_PROJECTS] = array('route' => 'configure_projects', 'description' => $i18n->__('Projects'), 'icon' => 'projects', 'details' => $i18n->__('Set up all projects in this configuration section.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUETYPES] = array('icon' => 'issuetypes', 'description' => $i18n->__('Issue types'), 'route' => 'configure_issuetypes', 'details' => $i18n->__('Manage issue types and configure issue fields for each issue type here'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUEFIELDS] = array('icon' => 'resolutiontypes', 'description' => $i18n->__('Issue fields'), 'route' => 'configure_issuefields', 'details' => $i18n->__('Status types, resolution types, categories, custom fields, etc. are configurable from this section.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_WORKFLOW] = array('icon' => 'workflow', 'description' => $i18n->__('Workflow'), 'route' => 'configure_workflow', 'details' => $i18n->__('Set up and edit workflow configuration from this section'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_USERS] = array('route' => 'configure_users', 'description' => $i18n->__('Users, teams &amp; groups'), 'icon' => 'users', 'details' => $i18n->__('Manage users, user groups and user teams from this section.'));
			$module_config_sections[TBGSettings::CONFIGURATION_SECTION_MODULES][] = array('route' => 'configure_modules', 'description' => $i18n->__('Module settings'), 'icon' => 'modules', 'details' => $i18n->__('Manage Bug Genie extensions from this section. New modules are installed from here.'), 'module' => 'core');
			foreach (TBGContext::getModules() as $module)
			{
				if ($module->hasAccess() && $module->hasConfigSettings())
				{
					$module_config_sections[TBGSettings::CONFIGURATION_SECTION_MODULES][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => $module->getConfigTitle(), 'icon' => $module->getName(), 'details' => $module->getConfigDescription(), 'module' => $module->getName());
				}
			}
			$this->general_config_sections = $general_config_sections; 
			$this->data_config_sections = $data_config_sections;
			$this->module_config_sections = $module_config_sections;
		}
		
		/**
		 * Configure general and server settings
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runSettings(TBGRequest $request)
		{
			if (TBGContext::getRequest()->isMethod(TBGRequest::POST))
			{
				$this->forward403unless($this->access_level == self::ACCESS_FULL);
				$settings = array('theme_name', 'user_themes', 'onlinestate', 'offlinestate', 'awaystate', 'singleprojecttracker',
									'requirelogin', 'allowreg', 'defaultgroup', 'returnfromlogin', 'returnfromlogout', 'permissive',
									'limit_registration', 'showprojectsoverview', 'showprojectsoverview', 'cleancomments',
									'b2_name', 'b2_tagline', 'url_subdir', 'local_path', 'charset', 'language', 'server_timezone',
									'highlight_default_lang', 'highlight_default_interval', 'highlight_default_numbering', 'icon_header',
									'icon_fav', 'icon_header_url', 'icon_fav_url', 'previewcommentimages');
				
				foreach ($settings as $setting)
				{
					if (TBGContext::getRequest()->getParameter($setting) !== null)
					{
						if ($setting == 'b2_name' || $setting == 'b2_tagline')
						{
							TBGSettings::saveSetting($setting, TBGContext::getRequest()->getParameter($setting, null, false));
						}
						else
						{
							TBGSettings::saveSetting($setting, TBGContext::getRequest()->getParameter($setting));
						}
					}
				}
				return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('All settings saved')));
			}
			
			$this->themes = TBGContext::getThemes();
			$this->languages = TBGI18n::getLanguages();
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
			$this->issue_types = TBGIssuetype::getAll();
			$this->icons = TBGIssuetype::getIcons();
		}

		/**
		 * Get issue type options for a specific issue type
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuetypesGetOptions(TBGRequest $request)
		{
			return $this->renderComponent('issuetypeoptions', array('id' => $request->getParameter('id')));
		}

		/**
		 * Perform an action on an issue type
		 * 
		 * @param TBGRequest $request 
		 */
		public function runConfigureIssuetypesAction(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == self::ACCESS_FULL);
			switch ($request->getParameter('mode'))
			{
				case 'add':
					if ($request->getParameter('name'))
					{
						$issuetype = TBGIssuetype::createNew($request->getParameter('name'), $request->getParameter('icon'));
						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Issue type created'), 'content' => $this->getComponentHTML('issuetype', array('type' => $issuetype))));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name for the issue type')));
					break;
				case 'update':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request->getParameter('id'))) instanceof TBGIssuetype)
					{
						if ($request->getParameter('name'))
						{
							$issuetype->setDescription($request->getParameter('description'));
							$issuetype->setName($request->getParameter('name'));
							$issuetype->setIcon($request->getParameter('icon'));
							$issuetype->setIsReportable($request->getParameter('reportable'));
							$issuetype->setRedirectAfterReporting($request->getParameter('redirect_after_reporting'));
							$issuetype->save();
							return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The issue type was updated'), 'description' => $issuetype->getDescription(), 'name' => $issuetype->getName(), 'reportable' => $issuetype->isReportable()));
						}
						else
						{
							return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name for the issue type')));
						}
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid issue type')));
					break;
				case 'updatechoices':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request->getParameter('id'))) instanceof TBGIssuetype)
					{
						$issuetype->clearAvailableFields();
						foreach ($request->getParameter('field', array()) as $key => $details)
						{
							$issuetype->setFieldAvailable($key, $details);
						}
						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Avilable choices updated')));
					}
					else
					{
						return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid issue type')));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Not implemented yet')));
					break;
				case 'delete':
					break;
				default:
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid action for this issue type')));
			}
		}

		/**
		 * Get issue fields list for a specific field type
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuefieldsGetOptions(TBGRequest $request)
		{
			return $this->renderComponent('issuefields', array('type' => $request->getParameter('type'), 'access_level' => $this->access_level));
		}

		/**
		 * Add or delete an issue field option
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuefieldsAction(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$this->forward403unless($this->access_level == self::ACCESS_FULL);
			$types = TBGDatatype::getTypes();

			switch ($request->getParameter('mode'))
			{
				case 'add':
					if ($request->getParameter('name'))
					{
						if (array_key_exists($request->getParameter('type'), $types))
						{
							$item = call_user_func(array($types[$request->getParameter('type')], 'createNew'), $request->getParameter('name'), $request->getParameter('itemdata'));
						}
						else
						{
							$customtype = TBGCustomDatatype::getByKey($request->getParameter('type'));
							$item = $customtype->createNewOption($request->getParameter('name'), $request->getParameter('value'), $request->getParameter('itemdata'));
						}
						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The option was added'), 'content' => $this->getTemplateHTML('issuefield', array('item' => $item, 'access_level' => $this->access_level, 'type' => $request->getParameter('type')))));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name')));
				case 'edit':
					if ($request->getParameter('name'))
					{
						if (array_key_exists($request->getParameter('type'), $types))
						{
							$item = call_user_func(array('TBGFactory', $types[$request->getParameter('type')].'Lab'), $request->getParameter('id'));
						}
						else
						{
							$customtype = TBGCustomDatatype::getByKey($request->getParameter('type'));
							$item = TBGContext::factory()->TBGCustomDatatypeOption($request->getParameter('id'));
						}
						if ($item instanceof TBGDatatypeBase && $item->getItemtype() == $item->getType())
						{
							$item->setName($request->getParameter('name'));
							$item->setItemdata($request->getParameter('itemdata'));
							if (!$item->isBuiltin())
							{
								$item->setValue($request->getParameter('value'));
							}
							$item->save();
							return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The option was updated')));
						}
						else
						{
							return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid id')));
						}
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name')));
				case 'delete':
					if ($request->hasParameter('id'))
					{
						if (array_key_exists($request->getParameter('type'), $types))
						{
							call_user_func(array($types[$request->getParameter('type')], 'delete'), (int) $request->getParameter('id'));
							return $this->renderJSON(array('failed' => false, 'title' => $i18n->__('The option was deleted')));
						}
						else
						{
							TBGCustomDatatypeOption::delete($request->getParameter('id'));
							return $this->renderJSON(array('failed' => false, 'title' => $i18n->__('The option was deleted')));
						}
					}
					return $this->renderJSON(array('failed' => true, 'error' => $i18n->__('Invalid id or type')));
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
			switch ($request->getParameter('mode'))
			{
				case 'add':
					if ($request->getParameter('name') != '')
					{
						if (TBGCustomDatatype::isNameValid($request->getParameter('name')))
						{
							$customtype = TBGCustomDatatype::createNew($request->getParameter('name'), $request->getParameter('field_type'));
							return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The custom field was added'), 'content' => $this->getComponentHTML('issuefields_customtype', array('type_key' => $customtype->getKey(), 'type' => $customtype))));
						}
						return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You need to provide a unique custom field name (key already exists)')));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name')));
					break;
				case 'update':
					if ($request->getParameter('name') != '')
					{
						$customtype = TBGCustomDatatype::getByKey($request->getParameter('type'));
						if ($customtype instanceof TBGCustomDatatype)
						{
							$customtype->setDescription($request->getParameter('description'));
							$customtype->setInstructions($request->getParameter('instructions'));
							$customtype->setName($request->getParameter('name'));
							$customtype->save();
							return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The custom field was updated'), 'description' => $customtype->getDescription(), 'instructions' => $customtype->getInstructions(), 'name' => $customtype->getName()));
						}
						return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You need to provide a custom field key that already exists')));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name')));
					break;
				case 'delete':
					$customtype = TBGCustomDatatype::getByKey($request->getParameter('type'));
					if ($customtype instanceof TBGCustomDatatype)
					{
						$id = $customtype->getID();
						unset($customtype);
						TBGCustomDatatype::delete($id);
						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The custom field was deleted')));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You need to provide a custom type key that already exists')));
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
		}

		/**
		 * Find users and show selection box
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runFindAssignee(TBGRequest $request)
		{
			$this->forward403unless($request->isMethod(TBGRequest::POST));

			$this->message = false;
			
			if ($request->getParameter('find_by'))
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
				$this->users = TBGUser::findUsers($request->getParameter('find_by'), 10);
				$this->teams = TBGTeam::findTeams($request->getParameter('find_by'));
				$this->customers = TBGCustomer::findCustomers($request->getParameter('find_by'));
			}
			else
			{
				$this->message = true;
			}
		}
		
		/**
		 * Adds a user, team or a customer to a project
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAssignToProject(TBGRequest $request)
		{
			$this->forward403unless($request->isMethod(TBGRequest::POST));
									
			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
				}
				catch (Exception $e) {}
				
				$this->forward403unless($this->theProject instanceof TBGProject);
				
				$assignee_type = $request->getParameter('assignee_type');
				$assignee_id = $request->getParameter('assignee_id');
				
				try
				{
					switch ($assignee_type)
					{
						case 'user':
							$assignee = TBGContext::factory()->TBGUser($assignee_id);
							break;
						case 'team':
							$assignee = TBGContext::factory()->TBGTeam($assignee_id);
							break;
						case 'customer':
							$assignee = TBGContext::factory()->TBGCustomer($assignee_id);
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
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("You don't have access to save project settings")));
			
		}

		/**
		 * Configure project editions and components
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjectEditionsAndComponents(TBGRequest $request)
		{
			try
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof TBGProject);
		}

		/**
		 * Configure project data types
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjectOther(TBGRequest $request)
		{
			try
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof TBGProject);
		}

		/**
		 * Configure project data types
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjectMilestones(TBGRequest $request)
		{
			try
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof TBGProject);
			$this->milestones = $this->theProject->getAllMilestones();
		}
		
		/**
		 * Updates visible issue types
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjectUpdateOther(TBGRequest $request)
		{
			try
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('This project does not exist')));
			}
			
			$this->forward403unless($this->theProject instanceof TBGProject && $request->hasParameter('frontpage_summary'));

			try
			{
				if ($this->access_level == self::ACCESS_FULL)
				{
					switch ($request->getParameter('frontpage_summary'))
					{
						case 'issuelist':
						case 'issuetypes':
							$this->theProject->setFrontpageSummaryType($request->getParameter('frontpage_summary'));
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
					return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Your changes has been saved'), 'message' => ''));
				}
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("You don't have access to save project settings")));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('An error occured'), 'message' => $e->getMessage()));
			}
		}

		/**
		 * Configure project builds
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjectDevelopers(TBGRequest $request)
		{
			try
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof TBGProject);
		}
		
		/**
		 * Configure project leaders
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runSetItemLead(TBGRequest $request)
		{
			try
			{
				switch ($request->getParameter('item_type'))
				{
					case 'project':
						$item = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
						break;
					case 'edition':
						$item = TBGContext::factory()->TBGEdition($request->getParameter('edition_id'));
						break;
				}
			}
			catch (Exception $e) {}
			
			$this->forward403unless($item instanceof TBGOwnableItem);
			
			if ($request->hasParameter('value'))
			{
				$this->forward403unless($this->access_level == self::ACCESS_FULL);
				if ($request->hasParameter('identifiable_type'))
				{
					if (in_array($request->getParameter('identifiable_type'), array(TBGIdentifiableClass::TYPE_USER, TBGIdentifiableClass::TYPE_TEAM)))
					{
						switch ($request->getParameter('identifiable_type'))
						{
							case TBGIdentifiableClass::TYPE_USER:
								$identified = TBGContext::factory()->TBGUser($request->getParameter('value'));
								break;
							case TBGIdentifiableClass::TYPE_TEAM:
								$identified = TBGContext::factory()->TBGTeam($request->getParameter('value'));
								break;
						}
						if ($identified instanceof TBGIdentifiableClass)
						{
							if ($request->getParameter('field') == 'owned_by') $item->setOwner($identified);
							elseif ($request->getParameter('field') == 'qa_by') $item->setQaResponsible($identified);
							elseif ($request->getParameter('field') == 'lead_by') $item->setLeader($identified);
							$item->save();
						}
					}
					else
					{
						if ($request->getParameter('field') == 'owned_by') $item->unsetOwner();
						elseif ($request->getParameter('field') == 'qa_by') $item->unsetQaResponsible();
						elseif ($request->getParameter('field') == 'lead_by') $item->unsetLeader();
						$item->save();
					}
				}
				if ($request->getParameter('field') == 'owned_by')
					return $this->renderJSON(array('field' => (($item->hasOwner()) ? array('id' => $item->getOwnerID(), 'name' => (($item->getOwnerType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $item->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $item->getOwner())))) : array('id' => 0))));
				elseif ($request->getParameter('field') == 'lead_by')
					return $this->renderJSON(array('field' => (($item->hasLeader()) ? array('id' => $item->getLeaderID(), 'name' => (($item->getLeaderType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $item->getLeader())) : $this->getComponentHTML('main/teamdropdown', array('team' => $item->getLeader())))) : array('id' => 0))));
				elseif ($request->getParameter('field') == 'qa_by')
					return $this->renderJSON(array('field' => (($item->hasQaResponsible()) ? array('id' => $item->getQaResponsibleID(), 'name' => (($item->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $item->getQaResponsible())) : $this->getComponentHTML('main/teamdropdown', array('team' => $item->getQaResponsible())))) : array('id' => 0))));
			}
		}
		
		/**
		 * Configure project settings
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureProjectSettings(TBGRequest $request)
		{
			try
			{
				$this->project = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
			}
			catch (Exception $e) {}
			
			if (!$this->project instanceof TBGProject) return $this->return404(TBGContext::getI18n()->__("This project doesn't exist"));
			
			if ($request->isAjaxCall())
			{
				if ($this->access_level == self::ACCESS_FULL)
				{
					if ($request->hasParameter('release_month') && $request->hasParameter('release_day') && $request->hasParameter('release_year'))
					{
						$release_date = mktime(0, 0, 1, $request->getParameter('release_month'), $request->getParameter('release_day'), $request->getParameter('release_year'));
						$this->project->setReleaseDate($release_date);
					}

					if ($request->hasParameter('project_name'))
						$this->project->setName($request->getParameter('project_name'));
					
					if ($request->hasParameter('use_prefix'))
						$this->project->setUsePrefix((bool) $request->getParameter('use_prefix'));
					
					if ($request->hasParameter('use_prefix') && $this->project->doesUsePrefix())
					{
						if (!$this->project->setPrefix($request->getParameter('prefix')))
							return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("Project prefixes may only contain letters and numbers")));
					}
					
					if ($request->hasParameter('use_scrum'))
						$this->project->setUsesScrum((bool) $request->getParameter('use_scrum'));
					
					if ($request->hasParameter('description'))
						$this->project->setDescription($request->getParameter('description', null, false));
					
					if ($request->hasParameter('homepage'))
						$this->project->setHomepage($request->getParameter('homepage'));
					
					if ($request->hasParameter('doc_url'))
						$this->project->setDocumentationURL($request->getParameter('doc_url'));
					
					if ($request->hasParameter('defaultstatus'))
						$this->project->setDefaultStatus($request->getParameter('defaultstatus'));
					
					if ($request->hasParameter('planned_release'))
						$this->project->setPlannedReleased($request->getParameter('planned_release'));
					
					if ($request->hasParameter('released'))
						$this->project->setReleased((int) $request->getParameter('released'));
					
					if ($request->hasParameter('locked'))
						$this->project->setLocked((bool) $request->getParameter('locked'));
					
					if ($request->hasParameter('enable_builds'))
						$this->project->setBuildsEnabled((bool) $request->getParameter('enable_builds'));
					
					if ($request->hasParameter('enable_editions'))
						$this->project->setEditionsEnabled((bool) $request->getParameter('enable_editions'));
					
					if ($request->hasParameter('enable_components'))
						$this->project->setComponentsEnabled((bool) $request->getParameter('enable_components'));
					
					if ($request->hasParameter('allow_changing_without_working'))
						$this->project->setChangeIssuesWithoutWorkingOnThem((bool) $request->getParameter('allow_changing_without_working'));
					
					$this->project->save();
					return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Your changes has been saved'), 'message' => ''));
				}
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("You don't have access to save settings")));
			}
		}
		
		/**
		 * Configure a project edition with builds and settings
		 * 
		 * @param TBGRequest $request The request object
		 *
		
		{
			try
			{
				$this->theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
				$this->theEdition = TBGContext::factory()->TBGEdition($request->getParameter('edition_id'));
			}
			catch (Exception $e) {}
			
			$this->forward403unless($this->theProject instanceof TBGProject && $this->theEdition instanceof TBGEdition);
			
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
				return $this->renderJSON(array('failed' => false));
			}
			
		}*/
		
		/**
		 * Add a project (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddProject(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				if ($p_name = $request->getParameter('p_name'))
				{
					$aProject = TBGProject::createNew($p_name);
					if ($aProject instanceof TBGProject)
					{
						return $this->renderJSON(array('title' => $i18n->__('The project has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'content' => $this->getTemplateHTML('projectbox', array('project' => $aProject, 'access_level' => $this->access_level))));
					}
					else
					{
						return $this->renderJSON(array('failed' => true, "error" => $i18n->__('A project with the same key already exists')));
					}
				}

				return $this->renderJSON(array('failed' => true, "error" => $i18n->__('Please specify a valid project name')));
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add projects")));
		}
		
		/**
		 * Add an edition (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddEdition(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$p_id = $request->getParameter('project_id');
					if ($project = TBGContext::factory()->TBGProject($p_id))
					{
						if (TBGContext::getUser()->canManageProjectReleases($project))
						{
							if ($e_name = $request->getParameter('e_name'))
							{
								$project = TBGContext::factory()->TBGProject($p_id);
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
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add projects")));
		}

		/**
		 * Perform actions on a build (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runBuildAction(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					if ($b_id = $request->getParameter('build_id'))
					{
						if (TBGContext::getUser()->hasPermission('b2buildaccess', $b_id))
						{
							$build = TBGContext::factory()->TBGBuild($b_id);
							switch ($request->getParameter('build_action'))
							{
								case 'markdefault':
									$build->setDefault();
									$this->show_mode = 'all';
									break;
								case 'delete':
									$build->delete();
									return $this->renderJSON(array('deleted' => true, 'message' => $i18n->__('The release was deleted')));
									break;
								case 'addtoopen':
									$build->addToOpenParentIssues((int) $request->getParameter('status'), (int) $request->getParameter('category'), (int) $request->getParameter('issuetype'));
									return $this->renderJSON(array('failed' => false, 'title' => $i18n->__('The selected build has been added to open issues based on your selections'), 'message' => ''));
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
										throw new Exception($i18n->__('The build / release needs to have a name'));
									}
									$this->show_mode = 'one';
									break;
							}
						}
						else
						{
							throw new Exception($i18n->__('You do not have access to this build / release'));
						}
					}
					else
					{
						throw new Exception($i18n->__('You need to specify a build / release'));
					}
				}
				catch (Exception $e)
				{
					return $this->renderJSON(array('failed' => true, "error" => $i18n->__('Could not update the build / release').", ".$e->getMessage()));
				}

				$this->build = $build;
			}
			else
			{
				return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add editions")));
			}
		}
		
		/**
		 * Add a build (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddBuild(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$p_id = $request->getParameter('project_id');
					if ($project = TBGContext::factory()->TBGProject($p_id))
					{
						if (TBGContext::getUser()->canManageProjectReleases($project))
						{
							if ($b_name = $request->getParameter('build_name'))
							{
								if (($e_id = $request->getParameter('edition_id')) && $edition = TBGContext::factory()->TBGEdition($e_id))
								{
									$build = TBGBuild::createNew($b_name, null, $e_id, $request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
								}
								else
								{
									$build = TBGBuild::createNew($b_name, $p_id, null, $request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
								}
								return $this->renderJSON(array('title' => $i18n->__('The release has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'html' => $this->getTemplateHTML('buildbox', array('build' => $build, 'access_level' => $this->access_level))));
							}
							else
							{
								throw new Exception($i18n->__('You need to specify a name for the new release'));
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
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add releases")));
		}
		
		/**
		 * Add a component (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddComponent(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$p_id = $request->getParameter('project_id');
					if ($project = TBGContext::factory()->TBGProject($p_id))
					{
						if (TBGContext::getUser()->canManageProjectReleases($project))
						{
							if ($c_name = $request->getParameter('c_name'))
							{
								$project = TBGContext::factory()->TBGProject($p_id);
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
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add releases")));
		}

		/**
		 * Add a milestone (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddMilestone(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$p_id = $request->getParameter('project_id');
					if ($project = TBGContext::factory()->TBGProject($p_id))
					{
						if (TBGContext::getUser()->canManageProjectReleases($project))
						{
							if (($m_name = $request->getParameter('name')) && trim($m_name) != '')
							{
								$theProject = TBGContext::factory()->TBGProject($p_id);
								$theMilestone = $theProject->addMilestone($m_name, $request->getParameter('milestone_type', 1));
								return $this->renderJSON(array('title' => $i18n->__('The milestone has been added'), 'message' => $i18n->__('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'content' => $this->getTemplateHTML('milestonebox', array('milestone' => $theMilestone))));
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
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add milestones")));
		}
		
		/**
		 * Perform actions on a build (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runMilestoneAction(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					if ($m_id = $request->getParameter('milestone_id'))
					{
						$theMilestone = TBGContext::factory()->TBGMilestone($m_id);
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
												$scheduled_date = mktime(23, 59, 59, TBGContext::getRequest()->getParameter('sch_month'), TBGContext::getRequest()->getParameter('sch_day'), TBGContext::getRequest()->getParameter('sch_year'));
												$theMilestone->setScheduledDate($scheduled_date);
											}
										}
										if ($theMilestone->isStarting())
										{
											if ($request->hasParameter('starting_month') && $request->hasParameter('starting_day') && $request->hasParameter('starting_year'))
											{
												$starting_date = mktime(0, 0, 1, TBGContext::getRequest()->getParameter('starting_month'), TBGContext::getRequest()->getParameter('starting_day'), TBGContext::getRequest()->getParameter('starting_year'));
												$theMilestone->setStartingDate($starting_date);
											}
										}
										$theMilestone->save();
										return $this->renderTemplate('milestonebox', array('milestone' => $theMilestone));
									}
									else
									{
										throw new Exception(TBGContext::getI18n()->__('The milestone needs to have a name'));
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
							throw new Exception(TBGContext::getI18n()->__('You do not have access to this milestone'));
						}
					}
					else
					{
						throw new Exception(TBGContext::getI18n()->__('You need to specify a milestone'));
					}
				}
				catch (Exception $e)
				{
					return $this->renderJSON(array('failed' => true, "error" => TBGContext::getI18n()->__('Could not update the milestone').", ".$e->getMessage()));
				}
				return $this->renderJSON(array('done' => true));
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify milestones")));
		}
		
		/**
		 * Add or remove a component to/from an edition (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runEditEditionComponent(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$theEdition   = TBGContext::factory()->TBGEdition($request->getParameter('edition_id'));
					if ($request->getParameter('mode') == 'add')
					{
						$theEdition->addComponent($request->getParameter('component_id'));
					}
					elseif ($request->getParameter('mode') == 'remove')
					{
						$theEdition->removeComponent($request->getParameter('component_id'));
					}
					return $this->renderJSON(array('failed' => false));
				}
				catch (Exception $e)
				{
					return $this->renderJSON(array('failed' => true, "error" => $i18n->__('The component could not be added to this edition').", ".$e->getMessage()));
				}
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify components")));
			
		}

		/**
		 * Edit a component
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runEditComponent(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$theComponent = TBGContext::factory()->TBGComponent($request->getParameter('component_id'));
					if ($request->getParameter('mode') == 'update')
					{
						$theComponent->setName($request->getParameter('c_name', ''));
						return $this->renderJSON(array('failed' => false, 'newname' => $theComponent->getName()));
					}
					elseif ($request->getParameter('mode') == 'delete')
					{
						$theComponent->delete();
					}
				}
				catch (Exception $e)
				{
					return $this->renderJSON(array('failed' => true, "error" => TBGContext::getI18n()->__('Could not edit this component').", ".$e->getMessage()));
				}
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify components")));
		}
		
		/**
		 * Delete a project
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runDeleteProject(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				try
				{
					$theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
					$theProject->delete();
					$theProject->save();
					return $this->renderJSON(array('failed' => false, 'title' => $i18n->__('The project was deleted')));
				}
				catch (Exception $e)
				{
					return $this->renderJSON(array('failed' => true, 'error' => $i18n->__('An error occured') . ': ' . $e->getMessage()));
				}
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to remove projects")));
		}

		/**
		 * Perform an action on a module
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runModuleAction(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == self::ACCESS_FULL);
			
			try
			{
				if ($request->getParameter('mode') == 'install' && file_exists(TBGContext::getIncludePath() . 'modules/' . $request->getParameter('module_key') . '/module'))
				{
					if (TBGModule::installModule($request->getParameter('module_key')))
					{
						TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The module "%module_name%" was installed successfully', array('%module_name%' => $request->getParameter('module_key'))));
					}
					else
					{
						TBGContext::setMessage('module_error', TBGContext::getI18n()->__('There was an error install the module %module_name%', array('%module_name%' => $request->getParameter('module_key'))));
					}
				}
				else
				{
					$module = TBGContext::getModule($request->getParameter('module_key'));
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
							TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The module "%module_name%" was uninstalled successfully', array('%module_name%' => $module->getName())));
							break;
					}
				}
			}
			catch (Exception $e)
			{
				TBGLogging::log('Trying to run action ' . $request->getParameter('mode') . ' on module ' . $request->getParameter('module_key') . ' which is an invalid module', 'main', TBGLogging::LEVEL_FATAL);
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('This module (%module_name%) does not exist', array('%module_name%' => $request->getParameter('module_key'))));
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

			if ($this->access_level == self::ACCESS_FULL)
			{
				return $this->renderJSON(array('failed' => false, 'content' => $this->getComponentHTML('configuration/permissionsblock', array('base_id' => $request->getParameter('base_id'), 'permissions_list' => $request->getParameter('permissions_list'), 'mode' => $request->getParameter('mode'), 'target_id' => $request->getParameter('target_id'), 'user_id' => $request->getParameter('user_id'), 'module' => $request->getParameter('target_module'), 'access_level' => $this->access_level))));
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify components")));
		}

		public function runSetPermission(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == self::ACCESS_FULL)
			{
				$uid = 0;
				$gid = 0;
				$tid = 0;
				switch ($request->getParameter('target_type'))
				{
					case 'user':
						$uid = $request->getParameter('item_id');
						break;
					case 'group':
						$gid = $request->getParameter('item_id');
						break;
					case 'team':
						$tid = $request->getParameter('item_id');
						break;
				}

				switch ($request->getParameter('mode'))
				{
					case 'allowed':
						TBGContext::setPermission($request->getParameter('key'), $request->getParameter('target_id'), $request->getParameter('target_module'), $uid, $gid, $tid, true);
						break;
					case 'denied':
						TBGContext::setPermission($request->getParameter('key'), $request->getParameter('target_id'), $request->getParameter('target_module'), $uid, $gid, $tid, false);
						break;
					case 'unset':
						TBGContext::removePermission($request->getParameter('key'), $request->getParameter('target_id'), $request->getParameter('target_module'), $uid, $gid, $tid);
						break;
				}
				return $this->renderJSON(array('failed' => false, 'content' => $this->getComponentHTML('configuration/permissionsinfoitem', array('key' => $request->getParameter('key'), 'target_id' => $request->getParameter('target_id'), 'type' => $request->getParameter('target_type'), 'mode' => $request->getParameter('template_mode'), 'item_id' => $request->getParameter('item_id'), 'module' => $request->getParameter('target_module'), 'access_level' => $this->access_level))));
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify components")));
		}
		
		/**
		 * Configure a module
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runConfigureModule(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == self::ACCESS_FULL);
			
			try
			{
				$module = TBGContext::getModule($request->getParameter('config_module'));
				if (!$module->hasConfigSettings())
				{
					throw new Exception('module not configurable');
				}
				else
				{
					if ($request->isMethod(TBGRequest::POST) && $this->access_level == self::ACCESS_FULL)
					{
						try
						{
							$module->postConfigSettings($request);
							TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Settings saved successfully'));
						}
						catch (Exception $e)
						{
							TBGContext::setMessage('module_error', $e->getMessage());
						}
						$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => $request->getParameter('config_module'))));
					}
					$this->module = $module;
				}
			}
			catch (Exception $e)
			{
				TBGLogging::log('Trying to configure module ' . $request->getParameter('config_module') . " which isn't configurable", 'main', TBGLogging::LEVEL_FATAL);
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The module "%module_name%" is not configurable', array('%module_name%' => $request->getParameter('config_module'))));
				$this->forward(TBGContext::getRouting()->generate('configure_modules'));
			}
			$this->module_message = TBGContext::getMessageAndClear('module_message');
			$this->module_error = TBGContext::getMessageAndClear('module_error');
			$this->module_error_details = TBGContext::getMessageAndClear('module_error_details');
		}

		public function runConfigurePermissions(TBGRequest $request)
		{
			$this->forward403unless($this->access_level == self::ACCESS_FULL);
		}

		public function runConfigureUploads(TBGRequest $request)
		{
			if ($request->isMethod(TBGRequest::POST))
			{
				$this->forward403unless($this->access_level == self::ACCESS_FULL);
				if ($request->getParameter('upload_storage') == 'files' && (bool) $request->getParameter('enable_uploads'))
				{
					if (!is_writable($request->getParameter('upload_localpath')))
					{
						return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("The upload path isn't writable")));
					}
				}
				$settings = array('enable_uploads', 'upload_restriction_mode', 'upload_extensions_list', 'upload_max_file_size', 'upload_storage', 'upload_localpath');

				foreach ($settings as $setting)
				{
					if (TBGContext::getRequest()->hasParameter($setting))
					{
						TBGSettings::saveSetting($setting, TBGContext::getRequest()->getParameter($setting));
					}
				}
				return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('All settings saved')));
			}
		}
		
		public function runConfigureUsers(TBGRequest $request)
		{
			$this->groups = TBGGroup::getAll();
			$this->teams = TBGTeam::getAll();
		}

		public function runDeleteGroup(TBGRequest $request)
		{
			try
			{
				try
				{
					$group = TBGContext::factory()->TBGGroup($request->getParameter('group_id'));
				}
				catch (Exception $e) { }
				if (!$group instanceof TBGGroup)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete this group"));
				}
				$group->delete();
				return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The group was deleted')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runAddGroup(TBGRequest $request)
		{
			try
			{
				$mode = $request->getParameter('mode');
				$request_param = ($mode == 'clone') ? 'new_group_name' : 'group_name';
				if ($group_name = $request->getParameter($request_param))
				{
					if ($mode == 'clone')
					{
						try
						{
							$old_group = TBGContext::factory()->TBGGroup($request->getParameter('group_id'));
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
					$group = TBGGroup::createNew($group_name);
					if ($mode == 'clone')
					{
						if ($request->getParameter('clone_permissions'))
						{
							TBGPermissionsTable::getTable()->cloneGroupPermissions($old_group->getID(), $group->getID());
						}
						$message = TBGContext::getI18n()->__('The group was cloned');
					}
					else
					{
						$message = TBGContext::getI18n()->__('The group was added');
					}
					return $this->renderJSON(array('failed' => false, 'message' => $message, 'content' => $this->getTemplateHTML('configuration/groupbox', array('group' => $group))));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a group name'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runGetGroupMembers(TBGRequest $request)
		{
			try
			{
				$group = TBGContext::factory()->TBGGroup((int) $request->getParameter('group_id'));
				$users = $group->getMembers();
				return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('configuration/groupuserlist', array('users' => $users))));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runDeleteTeam(TBGRequest $request)
		{
			try
			{
				try
				{
					$team = TBGContext::factory()->TBGTeam($request->getParameter('team_id'));
				}
				catch (Exception $e) { }
				if (!$team instanceof TBGTeam)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot delete this team"));
				}
				$team->delete();
				return $this->renderJSON(array('success' => true, 'message' => TBGContext::getI18n()->__('The team was deleted')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runAddTeam(TBGRequest $request)
		{
			try
			{
				$mode = $request->getParameter('mode');
				$request_param = ($mode == 'clone') ? 'new_team_name' : 'team_name';
				if ($team_name = $request->getParameter($request_param))
				{
					if ($mode == 'clone')
					{
						try
						{
							$old_team = TBGContext::factory()->TBGTeam($request->getParameter('team_id'));
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
					$team = TBGTeam::createNew($team_name);
					if ($mode == 'clone')
					{
						if ($request->getParameter('clone_permissions'))
						{
							TBGPermissionsTable::getTable()->cloneTeamPermissions($old_team->getID(), $team->getID());
						}
						if ($request->getParameter('clone_memberships'))
						{
							TBGTeamMembersTable::getTable()->cloneTeamMemberships($old_team->getID(), $team->getID());
						}
						$message = TBGContext::getI18n()->__('The team was cloned');
					}
					else
					{
						$message = TBGContext::getI18n()->__('The team was added');
					}
					return $this->renderJSON(array('failed' => false, 'message' => $message, 'content' => $this->getTemplateHTML('configuration/teambox', array('team' => $team))));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a team name'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runGetTeamMembers(TBGRequest $request)
		{
			try
			{
				$team = TBGContext::factory()->TBGTeam((int) $request->getParameter('team_id'));
				$users = $team->getMembers();
				return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('configuration/teamuserlist', array('users' => $users))));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runFindUsers(TBGRequest $request)
		{
			$this->too_short = false;
			$findstring = $request->getParameter('findstring');
			if (strlen($findstring) >= 1)
			{
				list ($this->users, $this->total_results) = TBGUsersTable::getTable()->findInConfig($findstring);
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
				if ($username = $request->getParameter('username'))
				{
					$user = TBGUser::createNew($username, $username, $username, TBGContext::getScope()->getID());
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
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runUpdateUser(TBGRequest $request)
		{
			$user = TBGContext::factory()->TBGUser($request->getParameter('user_id'));
			if ($user instanceof TBGUser)
			{
				$user->setRealname($request->getParameter('realname'));
				$return_options = array();
				if ($group = TBGContext::factory()->TBGGroup($request->getParameter('group')))
				{
					if ($user->getGroupID() != $group->getID())
					{
						$groups = array($user->getGroupID(), $group->getID());
						$return_options['update_groups'] = array('ids' => array(), 'membercounts' => array());
					}
					$user->setGroup($group);
				}
				$existing_teams = array_keys($user->getTeams());
				$new_teams = array();
				$user->clearTeams();
				foreach ($request->getParameter('teams', array()) as $team_id => $team)
				{
					if ($team = TBGContext::factory()->TBGTeam($team_id))
					{
						$new_teams[] = $team_id;
						$user->addToTeam($team);
					}
				}
				$testuser = TBGUser::getByUsername($request->getParameter('username'));
				if (!$testuser instanceof TBGUser || $testuser->getID() == $user->getID())
				{
					$user->setUsername($request->getParameter('username'));
				}
				else
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('This username is already taken')));
				}
				$user->setBuddyname($request->getParameter('nickname'));
				$user->setActivated((bool) $request->getParameter('activated'));
				$user->setEmail($request->getParameter('email'));
				$user->setEnabled((bool) $request->getParameter('enabled'));
				$user->save();
				if (isset($groups))
				{
					foreach ($groups as $group_id)
					{
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
				$return_options['failed'] = false;
				$return_options['content'] = $this->getTemplateHTML('configuration/finduser_row', array('user' => $user));
				$return_options['message'] = TBGContext::getI18n()->__('User updated!');
				return $this->renderJSON($return_options);
			}
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('This user could not be updated')));
		}

		public function runGetPermissionsConfigurator(TBGRequest $request)
		{
			return $this->renderComponent('configuration/permissionsconfigurator', array('access_level' => $this->access_level, 'user_id' => $request->getParameter('user_id', 0), 'base_id' => $request->getParameter('base_id', 0)));
		}

		public function runConfigureProjectEdition(TBGRequest $request)
		{
			try
			{
				if ($edition_id = $request->getParameter('edition_id'))
				{
					$edition = TBGContext::factory()->TBGEdition($edition_id);
					if ($request->isMethod(TBGRequest::POST))
					{
						if ($request->hasParameter('release_month') && $request->hasParameter('release_day') && $request->hasParameter('release_year'))
						{
							$release_date = mktime(0, 0, 1, $request->getParameter('release_month'), $request->getParameter('release_day'), $request->getParameter('release_year'));
							$edition->setReleaseDate($release_date);
						}

						$edition->setName($request->getParameter('edition_name'));
						$edition->setDescription($request->getParameter('description', null, false));
						$edition->setDocumentationURL($request->getParameter('doc_url'));
						$edition->setPlannedReleased($request->getParameter('planned_release'));
						$edition->setReleased((int) $request->getParameter('released'));
						$edition->setLocked((bool) $request->getParameter('locked'));
						$edition->save();
						return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('Edition details saved')));
					}
					else
					{
						switch ($request->getParameter('mode'))
						{
							case 'releases':
							case 'components':
								$this->selected_section = $request->getParameter('mode');
								break;
							default:
								$this->selected_section = 'general';
						}
						$content = $this->getComponentHTML('configuration/projectedition', array('edition' => $edition, 'access_level' => $this->access_level, 'selected_section' => $this->selected_section));
						return $this->renderJSON(array('failed' => false, 'content' => $content));
					}
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Invalid edition id'));
				}
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runConfigureProject(TBGRequest $request)
		{
			try
			{
				if ($project_id = $request->getParameter('project_id'))
				{
					$project = TBGContext::factory()->TBGProject($project_id);
					$content = $this->getComponentHTML('configuration/projectconfig', array('project' => $project, 'access_level' => $this->access_level, 'section' => 'hierarchy'));
					return $this->renderJSON(array('failed' => false, 'content' => $content));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Invalid project id'));
				}
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runConfigureWorkflowSchemes(TBGRequest $request)
		{
			$this->schemes = TBGWorkflowScheme::getAll();
		}

		public function runConfigureWorkflows(TBGRequest $request)
		{
			$this->workflows = TBGWorkflow::getAll();
		}

		public function runConfigureWorkflowScheme(TBGRequest $request)
		{
			$this->workflow_scheme = null;
			try
			{
				$this->workflow_scheme = TBGContext::factory()->TBGWorkflowScheme($request->getParameter('scheme_id'));
				$this->issuetypes = TBGIssuetype::getAll();
			}
			catch (Exception $e)
			{
				$this->error = TBGContext::getI18n()->__('This workflow scheme does not exist');
			}
		}

		public function runConfigureWorkflowSteps(TBGRequest $request)
		{
			$this->workflow = null;
			try
			{
				$this->workflow = TBGContext::factory()->TBGWorkflow($request->getParameter('workflow_id'));
			}
			catch (Exception $e)
			{
				$this->error = TBGContext::getI18n()->__('This workflow does not exist');
			}
		}

		public function runConfigureWorkflowStep(TBGRequest $request)
		{
			$this->workflow = null;
			$this->step = null;
			try
			{
				$this->workflow = TBGContext::factory()->TBGWorkflow($request->getParameter('workflow_id'));
				$this->step = TBGContext::factory()->TBGWorkflowStep($request->getParameter('step_id'));
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
				$this->workflow = TBGContext::factory()->TBGWorkflow($request->getParameter('workflow_id'));
				if ($request->hasParameter('transition_id'))
				{
					$this->transition = TBGContext::factory()->TBGWorkflowTransition($request->getParameter('transition_id'));
				}
				elseif ($request->isMethod(TBGRequest::POST) && $request->hasParameter('step_id'))
				{
					if ($step->isCore() || $workflow->isCore())
					{
						throw new InvalidArgumentException("The default workflow cannot be edited");
					}
					$step = TBGContext::factory()->TBGWorkflowStep($request->getParameter('step_id'));
					if ($request->getParameter('add_transition_type') == 'existing' && $request->hasParameter('existing_transition_id'))
					{
						$transition = TBGContext::factory()->TBGWorkflowTransition($request->getParameter('existing_transition_id'));
					}
					else
					{
						if ($request->getParameter('transition_name') && $request->getParameter('outgoing_step_id') && $request->hasParameter('template'))
						{
							$transition = TBGWorkflowTransition::createNew($this->workflow->getID(), $request->getParameter('transition_name'), $request->getParameter('transition_description'), $request->getParameter('outgoing_step_id'), $request->getParameter('template'));
						}
						else
						{
							throw new InvalidArgumentException(TBGContext::getI18n()->__('Please fill in all required fields'));
						}
					}
					$step->addOutgoingTransition($transition);
					$this->forward(TBGContext::getRouting()->generate('configure_workflow_transition', array('workflow_id' => $this->workflow->getID(), 'transition_id' => $transition->getID())));
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
				//throw $e;
				$this->error = TBGContext::getI18n()->__('This workflow / transition does not exist');
			}
		}

		public function getAccessLevel($section, $module)
		{
			return (TBGContext::getUser()->canSaveConfiguration($section, $module)) ? self::ACCESS_FULL : self::ACCESS_READ;
		}

	}
