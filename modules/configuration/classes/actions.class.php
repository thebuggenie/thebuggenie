<?php

	class configurationActions extends TBGAction
	{
		
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
			TBGContext::loadLibrary('ui');
			$this->getResponse()->addBreadcrumb(link_tag(make_url('configure'), TBGContext::getI18n()->__('Configure')));
			
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
			}
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_IMPORT] = array('route' => 'configure_import', 'description' => $i18n->__('Import data'), 'icon' => 'import', 'details' => $i18n->__('Import data from CSV files and other sources.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_PROJECTS] = array('route' => 'configure_projects', 'description' => $i18n->__('Projects'), 'icon' => 'projects', 'details' => $i18n->__('Set up all projects in this configuration section.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUETYPES] = array('icon' => 'issuetypes', 'description' => $i18n->__('Issue types'), 'route' => 'configure_issuetypes', 'details' => $i18n->__('Manage issue types and configure issue fields for each issue type here'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUEFIELDS] = array('icon' => 'resolutiontypes', 'description' => $i18n->__('Issue fields'), 'route' => 'configure_issuefields', 'details' => $i18n->__('Status types, resolution types, categories, custom fields, etc. are configurable from this section.'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_WORKFLOW] = array('icon' => 'workflow', 'description' => $i18n->__('Workflow'), 'route' => 'configure_workflow', 'details' => $i18n->__('Set up and edit workflow configuration from this section'));
			$data_config_sections[TBGSettings::CONFIGURATION_SECTION_USERS] = array('route' => 'configure_users', 'description' => $i18n->__('Users, teams, clients &amp; groups'), 'icon' => 'users', 'details' => $i18n->__('Manage users, user groups, clients and user teams from this section.'));
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
		 * Configuration import page
		 * 
		 * @param TBGRequest $request
		 */
		public function runImport(TBGRequest $request)
		{
			if ($request->isMethod(TBGRequest::POST))
			{
				if ($request->getParameter('import_sample_data'))
				{
					$project = new TBGProject();
					$project->setName('Sample project 1');
					$project->setDescription('This is a sample project that is awesome. Try it out!');
					$project->setHomepage('http://www.google.com');
					$project->save();
					
					$project = new TBGProject();
					$project->setName('Sample project 2');
					$project->setDescription('This is the second sample project. Not as awesome as the first one, but still worth a try!');
					$project->setHomepage('http://www.bing.com');
					$project->save();
					
					$this->imported_data = true;
				}
			}
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
				$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
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
							$value = TBGContext::getRequest()->getParameter($setting);
							if ($setting == 'highlight_default_interval')
							{
								if (!is_numeric($value) || $value < 1)
								{
									$this->getResponse()->setHttpStatus(400);
									return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provde a valid setting for highlighting interval')));
								}
							}
							TBGSettings::saveSetting($setting, $value);
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
				$this->scheme = TBGContext::factory()->TBGIssuetypeScheme((int) $request->getParameter('scheme_id'));
				if ($this->mode == 'copy_scheme')
				{
					if ($new_name = $request->getParameter('new_name'))
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
			}
		}

		/**
		 * Get issue type options for a specific issue type
		 *
		 * @param TBGRequest $request
		 */
		public function runConfigureIssuetypesGetOptionsForScheme(TBGRequest $request)
		{
			return $this->renderComponent('issuetypeschemeoptions', array('id' => $request->getParameter('id'), 'scheme_id' => $request->getParameter('scheme_id')));
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
				$this->scheme = TBGContext::factory()->TBGIssuetypeScheme((int) $request->getParameter('scheme_id'));
			}
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
			switch ($request->getParameter('mode'))
			{
				case 'add':
					if ($request->getParameter('name'))
					{
						$issuetype = new TBGIssuetype();
						$issuetype->setName($request->getParameter('name'));
						$issuetype->setIcon($request->getParameter('icon'));
						$issuetype->save();
						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Issue type created'), 'content' => $this->getComponentHTML('issuetype', array('type' => $issuetype))));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid name for the issue type')));
					break;
				case 'update':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request->getParameter('id'))) instanceof TBGIssuetype)
					{
						if ($this->scheme instanceof TBGIssuetypeScheme)
						{
							$this->scheme->setIssuetypeRedirectedAfterReporting($issuetype, $request->getParameter('redirect_after_reporting'));
							$this->scheme->setIssuetypeReportable($issuetype, $request->getParameter('reportable'));
							return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The issue type details were updated'), 'description' => $issuetype->getDescription(), 'name' => $issuetype->getName()));
						}
						elseif ($request->getParameter('name'))
						{
							$issuetype->setDescription($request->getParameter('description'));
							$issuetype->setName($request->getParameter('name'));
							$issuetype->setIcon($request->getParameter('icon'));
							$issuetype->save();
							return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The issue type was updated'), 'description' => $issuetype->getDescription(), 'name' => $issuetype->getName()));
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
						$this->scheme->clearAvailableFieldsForIssuetype($issuetype);
						foreach ($request->getParameter('field', array()) as $key => $details)
						{
							$this->scheme->setFieldAvailableForIssuetype($issuetype, $key, $details);
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
				case 'toggletype':
					if (($issuetype = TBGContext::factory()->TBGIssuetype($request->getParameter('id'))) instanceof TBGIssuetype)
					{
						if ($this->scheme instanceof TBGIssuetypeScheme)
						{
							$this->scheme->setIssuetypeEnabled($issuetype, ($request->getParameter('state') == 'enable'));
							return $this->renderJSON(array('failed' => false, 'issuetype_id' => $issuetype->getID()));
						}
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please provide a valid action for this issue type / scheme')));
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
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
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
							$labname = $types[$request->getParameter('type')];
							$item = TBGContext::factory()->$labname($request->getParameter('id'));
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
							B2DB::getTable('TBGCustomFieldOptionsTable')->doDeleteById($request->getParameter('id'));
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
							$customtype = new TBGCustomDatatype();
							$customtype->setName($request->getParameter('name'));
							$customtype->setType($request->getParameter('field_type'));
							$customtype->save();
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
						$customtype->delete();
						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('The custom field was deleted')));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You need to provide a custom field key that already exists')));
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
									
			if ($this->access_level == TBGSettings::ACCESS_FULL)
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
				if ($this->access_level == TBGSettings::ACCESS_FULL)
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
					case 'component':
						$item = TBGContext::factory()->TBGComponent($request->getParameter('component_id'));
						break;
				}
			}
			catch (Exception $e) {}
			
			$this->forward403unless($item instanceof TBGOwnableItem);
			
			if ($request->hasParameter('value'))
			{
				$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
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
			
			if ($request->isAjaxCall() && $request->isMethod(TBGRequest::POST))
			{
				if ($this->access_level == TBGSettings::ACCESS_FULL)
				{
					if ($request->hasParameter('release_month') && $request->hasParameter('release_day') && $request->hasParameter('release_year'))
					{
						$release_date = mktime(0, 0, 1, $request->getParameter('release_month'), $request->getParameter('release_day'), $request->getParameter('release_year'));
						$this->project->setReleaseDate($release_date);
					}

					$old_key = $this->project->getKey();

					if ($request->hasParameter('project_name'))
						$this->project->setName($request->getParameter('project_name'));

					$message = ($old_key != $this->project->getKey()) ? TBGContext::getI18n()->__('%IMPORTANT%: The project key has changed. Remember to replace the current url with the new project key', array('%IMPORTANT%' => '<b>'.TBGContext::getI18n()->__('IMPORTANT').'</b>')) : '';
					
					if ($request->hasParameter('use_prefix'))
						$this->project->setUsePrefix((bool) $request->getParameter('use_prefix'));
					
					if ($request->hasParameter('use_prefix') && $this->project->doesUsePrefix())
					{
						if (!$this->project->setPrefix($request->getParameter('prefix')))
							return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("Project prefixes may only contain letters and numbers")));
					}
					
					if ($request->hasParameter('client'))
					{
						if ($request->getParameter('client') == 0)
						{
							$this->project->setClient(null);
						}
						else
						{
							$this->project->setClient(TBGContext::factory()->TBGClient($request->getParameter('client')));
						}
					}
					
					if ($request->hasParameter('workflow_scheme'))
					{
						try
						{
							$workflow_scheme = TBGContext::factory()->TBGWorkflowScheme($request->getParameter('workflow_scheme'));
							$this->project->setWorkflowScheme($workflow_scheme);
						}
						catch (Exception $e) {}
					}
					
					if ($request->hasParameter('issuetype_scheme'))
					{
						try
						{
							$issuetype_scheme = TBGContext::factory()->TBGIssuetypeScheme($request->getParameter('issuetype_scheme'));
							$this->project->setIssuetypeScheme($issuetype_scheme);
						}
						catch (Exception $e) {}
					}
					
					if ($request->hasParameter('use_scrum'))
						$this->project->setUsesScrum((bool) $request->getParameter('use_scrum'));
					
					if ($request->hasParameter('description'))
						$this->project->setDescription($request->getParameter('description', null, false));
					
					if ($request->hasParameter('homepage'))
						$this->project->setHomepage($request->getParameter('homepage'));
					
					if ($request->hasParameter('doc_url'))
						$this->project->setDocumentationURL($request->getParameter('doc_url'));
					
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
					$project_description = new TBGTextParser($this->project->getDescription());
					$project_description = $project_description->getParsedText();
					return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Your changes has been saved'), 'message' => $message, 'project_key' => $this->project->getKey(), 'project_description' => $project_description));
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				if ($p_name = $request->getParameter('p_name'))
				{
					try
					{
						$project = new TBGProject();
						$project->setName($p_name);
						$project->save();
						return $this->renderJSON(array('title' => $i18n->__('The project has been added'), 'content' => $this->getTemplateHTML('projectbox', array('project' => $project, 'access_level' => $this->access_level))));
					}
					catch (InvalidArgumentException $e)
					{
						return $this->renderJSON(array('failed' => true, "error" => $i18n->__('A project with the same key already exists')));
					}
					catch (Exception $e)
					{
						return $this->renderJSON(array('failed' => true, "error" => $i18n->__('An error occurred: '. $e->getMessage())));
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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
								return $this->renderJSON(array('title' => $i18n->__('The edition has been added'), 'html' => $this->getTemplateHTML('editionbox', array('edition' => $edition))));
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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
								$build = new TBGBuild();
								$build->setName($b_name);
								$build->setVersion($request->getParameter('ver_mj', 0), $request->getParameter('ver_mn', 0), $request->getParameter('ver_rev', 0));
								if (($e_id = $request->getParameter('edition_id')) && $edition = TBGContext::factory()->TBGEdition($e_id))
								{
									$build->setEdition($edition);
								}
								else
								{
									$build->setProject($project);
								}
								$build->save();
								return $this->renderJSON(array('title' => $i18n->__('The release has been added'), 'html' => $this->getTemplateHTML('buildbox', array('build' => $build, 'access_level' => $this->access_level))));
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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
								return $this->renderJSON(array('title' => $i18n->__('The component has been added'), 'html' => $this->getTemplateHTML('componentbox', array('component' => $component))));
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
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to add components")));
		}

		/**
		 * Add a milestone (AJAX call)
		 * 
		 * @param TBGRequest $request The request object
		 */
		public function runAddMilestone(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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
								return $this->renderJSON(array('title' => $i18n->__('The milestone has been added'), 'content' => $this->getTemplateHTML('milestonebox', array('milestone' => $theMilestone))));
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				try
				{
					$theProject = TBGContext::factory()->TBGProject($request->getParameter('project_id'));
					$theProject->setDeleted();
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
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
			
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

			if ($this->access_level == TBGSettings::ACCESS_FULL)
			{
				return $this->renderJSON(array('failed' => false, 'content' => $this->getComponentHTML('configuration/permissionsblock', array('base_id' => $request->getParameter('base_id'), 'permissions_list' => $request->getParameter('permissions_list'), 'mode' => $request->getParameter('mode'), 'target_id' => $request->getParameter('target_id'), 'user_id' => $request->getParameter('user_id'), 'module' => $request->getParameter('target_module'), 'access_level' => $this->access_level))));
			}
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify permissions")));
		}

		public function runSetPermission(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();

			if ($this->access_level == TBGSettings::ACCESS_FULL)
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
			return $this->renderJSON(array('failed' => true, "error" => $i18n->__("You don't have access to modify permissions")));
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
				$module = TBGContext::getModule($request->getParameter('config_module'));
				if (!$module->hasConfigSettings())
				{
					throw new Exception('module not configurable');
				}
				else
				{
					if ($request->isMethod(TBGRequest::POST) && $this->access_level == TBGSettings::ACCESS_FULL)
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
			$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
		}

		public function runConfigureUploads(TBGRequest $request)
		{
			if ($request->isMethod(TBGRequest::POST))
			{
				$this->forward403unless($this->access_level == TBGSettings::ACCESS_FULL);
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
			$this->clients = TBGClient::getall();
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
					$group = new TBGGroup();
					$group->setName($group_name);
					$group->save();
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
					$team = new TBGTeam();
					$team->setName($team_name);
					$team->save();
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
					$user = new TBGUser();
					$user->setUsername($username);
					$user->setRealname($username);
					$user->setBuddyname($username);
					$user->setEnabled();
					$user->setActivated();
					$user->setPassword(TBGUser::hashPassword(TBGUser::createPassword()));
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
				$testuser = TBGUser::getByUsername($request->getParameter('username'));
				if (!$testuser instanceof TBGUser || $testuser->getID() == $user->getID())
				{
					$user->setUsername($request->getParameter('username'));
				}
				else
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('This username is already taken')));
				}
				$password_changed = false;
				if ($request->getParameter('password_action') == 'change' && $request->getParameter('new_password_1') && $request->getParameter('new_password_2'))
				{
					if ($request->getParameter('new_password_1') == $request->getParameter('new_password_2'))
					{
						$user->setPassword($request->getParameter('new_password_1'));
						$password_changed = true;
					}
					else
					{
						return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please enter the new password twice')));
					}
				}
				elseif ($request->getParameter('password_action') == 'random')
				{
					$random_password = TBGUser::createPassword();
					$user->setPassword($random_password);
					$password_changed = true;
				}
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
				$user->clearClients();
				foreach ($request->getParameter('clients', array()) as $client_id => $client)
				{
					if ($client = TBGContext::factory()->TBGClient($client_id))
					{
						$new_clients[] = $client_id;
						$user->addToClient($client);
					}
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
				$return_options['failed'] = false;
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
				if ($request->getParameter('mode') == 'edit' && !$request->hasParameter('step_id'))
				{
					$this->step = new TBGWorkflowStep();
					$this->step->setWorkflow($this->workflow);
				}
				else
				{
					$this->step = TBGContext::factory()->TBGWorkflowStep($request->getParameter('step_id'));
				}
				if ($request->isMethod(TBGRequest::POST) && $request->getParameter('mode') == 'delete_outgoing_transitions')
				{
					$this->step->deleteOutgoingTransitions();
					$this->forward(TBGContext::getRouting()->generate('configure_workflow_steps', array('workflow_id' => $this->workflow->getID())));
				}
				if ($request->isMethod(TBGRequest::POST) && $request->getParameter('mode') == 'delete' && !$this->step->hasIncomingTransitions())
				{
					$this->step->deleteOutgoingTransitions();
					$this->step->delete();
					$this->forward(TBGContext::getRouting()->generate('configure_workflow_steps', array('workflow_id' => $this->workflow->getID())));
				}
				elseif ($request->isMethod(TBGRequest::POST) && ($request->hasParameter('edit') || $request->getParameter('mode') == 'edit'))
				{
					$this->step->setName($request->getParameter('name'));
					$this->step->setDescription($request->getParameter('description'));
					$this->step->setLinkedStatusID($request->getParameter('status_id'));
					$this->step->setIsEditable((bool) $request->getParameter('is_editable'));
					$this->step->setIsClosed((bool) ($request->getParameter('state') == TBGIssue::STATE_CLOSED));
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
				$this->workflow = TBGContext::factory()->TBGWorkflow($request->getParameter('workflow_id'));
				if ($request->hasParameter('transition_id'))
				{
					$this->transition = TBGContext::factory()->TBGWorkflowTransition($request->getParameter('transition_id'));
					if ($request->isMethod(TBGRequest::POST))
					{
						if ($request->getParameter('mode') == 'delete')
						{
							$this->transition->deleteTransition($request->getParameter('direction'));
							return $this->renderJSON(array('failed' => false));
						}
						elseif ($request->getParameter('transition_name') && $request->getParameter('outgoing_step_id') && $request->hasParameter('template'))
						{
							$this->transition->setName($request->getParameter('transition_name'));
							$this->transition->setDescription($request->getParameter('transition_description'));
							if ($request->getParameter('template'))
							{
								$this->transition->setTemplate($request->getParameter('template'));
							}
							else
							{
								$this->transition->setTemplate(null);
							}
							try
							{
								$step = TBGContext::factory()->TBGWorkflowStep($request->getParameter('outgoing_step_id'));
							}
							catch (Exception $e) {}
							$this->transition->setOutgoingStep($step);
							$this->transition->save();
							$transition = $this->transition;
							$redirect_transition = true;
						}
					}
				}
				elseif ($request->isMethod(TBGRequest::POST) && $request->hasParameter('step_id'))
				{
					$step = TBGContext::factory()->TBGWorkflowStep($request->getParameter('step_id'));
					/*if ($step->isCore() || $workflow->isCore())
					{
						throw new InvalidArgumentException("The default workflow cannot be edited");
					}*/
					if ($request->getParameter('add_transition_type') == 'existing' && $request->hasParameter('existing_transition_id'))
					{
						$transition = TBGContext::factory()->TBGWorkflowTransition($request->getParameter('existing_transition_id'));
						$redirect_transition = false;
					}
					else
					{
						if ($request->getParameter('transition_name') && $request->getParameter('outgoing_step_id') && $request->hasParameter('template'))
						{
							if (($outgoing_step = TBGContext::factory()->TBGWorkflowStep((int) $request->getParameter('outgoing_step_id'))) && $step instanceof TBGWorkflowStep)
							{
								if (array_key_exists($request->getParameter('template'), TBGWorkflowTransition::getTemplates()))
								{
									$transition = new TBGWorkflowTransition();
									$transition->setWorkflow($this->workflow);
									$transition->setName($request->getParameter('transition_name'));
									$transition->setDescription($request->getParameter('transition_description'));
									$transition->setOutgoingStep($outgoing_step);
									$transition->setTemplate($request->getParameter('template'));
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
				//throw $e;
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
				$mode = $request->getParameter('mode');
				if ($client_name = $request->getParameter('client_name'))
				{
					if (TBGClient::doesClientNameExist(trim($request->getParameter('client_name'))))
					{
						throw new Exception(TBGContext::getI18n()->__("Please enter a client name that doesn't already exist"));
					}
					$client = new TBGClient();
					$client->setName($request->getParameter('client_name'));
					$client->save();

					$message = TBGContext::getI18n()->__('The client was added');
					return $this->renderJSON(array('failed' => false, 'message' => $message, 'content' => $this->getTemplateHTML('configuration/clientbox', array('client' => $client))));
				}
				else
				{
					throw new Exception(TBGContext::getI18n()->__('Please enter a client name'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		public function runDeleteClient(TBGRequest $request)
		{
			try
			{
				try
				{
					$client = TBGContext::factory()->TBGClient($request->getParameter('client_id'));
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
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}
		
		public function runGetClientMembers(TBGRequest $request)
		{
			try
			{
				$client = TBGContext::factory()->TBGClient((int) $request->getParameter('client_id'));
				$users = $client->getMembers();
				return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('configuration/clientuserlist', array('users' => $users))));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}
		
		public function runEditClient(TBGRequest $request)
		{
			try
			{
				try
				{
					$client = TBGContext::factory()->TBGClient($request->getParameter('client_id'));
				}
				catch (Exception $e) { }
				if (!$client instanceof TBGClient)
				{
					throw new Exception(TBGContext::getI18n()->__("You cannot edit this client"));
				}
				
				if (TBGClient::doesClientNameExist(trim($request->getParameter('client_name'))) && $request->getParameter('client_name') != $client->getName())
				{
					throw new Exception(TBGContext::getI18n()->__("Please enter a client name that doesn't already exist"));
				}
				
				$client->setName($request->getParameter('client_name'));
				$client->setEmail($request->getParameter('client_email'));
				$client->setWebsite($request->getParameter('client_website'));
				$client->setTelephone($request->getParameter('client_telephone'));
				$client->setFax($request->getParameter('client_fax'));
				$client->save();
				return $this->renderJSON(array('success' => true, 'content' => $this->getTemplateHTML('configuration/clientbox', array('client' => $client)), 'message' => TBGContext::getI18n()->__('The client was saved')));
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}
		
		public function runImportCSV(TBGRequest $request)
		{
			$content = $this->getTemplateHTML('configuration/importcsv', array('type' => $request->getParameter('type')));
			return $this->renderJSON(array('failed' => false, 'content' => $content));
		}
		
		public function runDoImportCSV(TBGRequest $request)
		{
			try
			{
				if ($request->getParameter('csv_data') == '')
				{
					throw new Exception(TBGContext::getI18n()->__('No data supplied to import'));
				}
				
				// Split data into individual lines
				$data = str_replace("\r\n", "\n", $request->getParameter('csv_data'));
				$data = explode("\n", $data);
				if (count($data) <= 1)
				{
					throw new Exception(TBGContext::getI18n()->__('Insufficient data to import'));
				}
				$headerrow = $data[0];
				$headerrow = html_entity_decode($headerrow, ENT_QUOTES);
				$headerrow = explode(',', $headerrow);
				$headerrow2 = array();
				for ($i = 0; $i != count($headerrow); $i++)
				{
					$headerrow2[$i] = trim($headerrow[$i], '"');
				}
				
				$errors = array();
				
				// inspect for correct rows
				switch ($request->getParameter('type'))
				{
					case 'clients':
						$namecol = null;
						$emailcol = null;
						$telephonecol = null;
						$faxcol = null;
						$websitecol = null;
						
						for ($i = 0; $i != count($headerrow2); $i++)
						{
							if ($headerrow2[$i] == 'name'):
								$namecol = $i;
							elseif ($headerrow2[$i] == 'email'):
								$emailcol = $i;
							elseif ($headerrow2[$i] == 'telephone'):
								$telephonecol = $i;
							elseif ($headerrow2[$i] == 'fax'):
								$faxcol = $i;
							elseif ($headerrow2[$i] == 'website'):
								$websitecol = $i;
							endif;
						}
						
						$rowlength = count($headerrow2);
						
						if ($namecol === null)
						{
							$errors[] = TBGContext::getI18n()->__('Required column \'%col%\' not found in header row', array('%col%' => 'name'));
						}
						
						break;
					case 'projects':
						$namecol = null;
						$prefix = null;
						$scrum = null;
						$owner = null;
						$owner_type = null;
						$lead = null;
						$lead_type = null;
						$qa = null;
						$qa_type = null;
						$descr = null;
						$doc_url = null;
						$freelance = null;
						$en_builds = null;
						$en_comps = null;
						$en_editions = null;
						$workflow_id = null;
						$client = null;
						$show_summary = null;
						$summary_type = null;
						
						for ($i = 0; $i != count($headerrow2); $i++)
						{
							if ($headerrow2[$i] == 'name'):
								$namecol = $i;
							elseif ($headerrow2[$i] == 'prefix'):
								$prefix = $i;
							elseif ($headerrow2[$i] == 'scrum'):
								$scrum = $i;
							elseif ($headerrow2[$i] == 'owner'):
								$owner = $i;
							elseif ($headerrow2[$i] == 'owner_type'):
								$owner_type = $i;
							elseif ($headerrow2[$i] == 'lead'):
								$lead = $i;
							elseif ($headerrow2[$i] == 'lead_type'):
								$lead_type = $i;
							elseif ($headerrow2[$i] == 'qa'):
								$qa = $i;
							elseif ($headerrow2[$i] == 'qa_type'):
								$qa_type = $i;
							elseif ($headerrow2[$i] == 'descr'):
								$descr = $i;
							elseif ($headerrow2[$i] == 'doc_url'):
								$doc_url = $i;
							elseif ($headerrow2[$i] == 'freelance'):
								$freelance = $i;
							elseif ($headerrow2[$i] == 'en_builds'):
								$en_builds = $i;
							elseif ($headerrow2[$i] == 'en_comps'):
								$en_comps = $i;
							elseif ($headerrow2[$i] == 'en_editions'):
								$en_editions = $i;
							elseif ($headerrow2[$i] == 'workflow_id'):
								$workflow_id = $i;
							elseif ($headerrow2[$i] == 'client'):
								$client = $i;
							elseif ($headerrow2[$i] == 'show_summary'):
								$show_summary = $i;
							elseif ($headerrow2[$i] == 'summary_type'):
								$summary_type = $i;
							endif;
						}
						
						$rowlength = count($headerrow2);
						
						if ($namecol === null)
						{
							$errors[] = TBGContext::getI18n()->__('Required column \'%col%\' not found in header row', array('%col%' => 'name'));
						}
						
						break;
					default:
						throw new Exception('Sorry, this type is unimplemented');
						break;
				}
				
				// Check if rows are long enough
				for ($i = 1; $i != count($data); $i++)
				{
					$activerow = $data[$i];
					$activerow = html_entity_decode($activerow, ENT_QUOTES);
					$activerow = explode(',', $activerow);
					
					if (count($activerow) != $rowlength)
					{
						$errors[] = TBGContext::getI18n()->__('Row %row% does not have the same number of elements as the header row', array('%row%' => $i+1));
					}
				}
				
				reset($data);
				
				// Check if fields are empty
				for ($i = 1; $i != count($data); $i++)
				{
					$activerow = $data[$i];
					$activerow = html_entity_decode($activerow, ENT_QUOTES);
					$activerow = explode(',', $activerow);
					
					for ($j = 0; $j != count($activerow); $j++)
					{
						if ($activerow[$j] == '' || $activerow[$j] == '""')
						{
							$errors[] = TBGContext::getI18n()->__('Row %row% column %col% has no value', array('%col%' => $j+1, '%row%' => $i+1));
						}
					}
				}
				
				// Check if fields are valid
				switch ($request->getParameter('type'))
				{
					case 'projects':
						for ($i = 1; $i != count($data); $i++)
						{
							$activerow = $data[$i];
							$activerow = html_entity_decode($activerow, ENT_QUOTES);
							$activerow = explode(',', $activerow);
							
							// First off are booleans
							$boolitems = array($scrum, $freelance, $en_builds, $en_comps, $en_editions, $show_summary);
							
							foreach ($boolitems as $boolitem)
							{
								if ($boolitem !== null && trim($activerow[$boolitem], '"') != 0 && trim($activerow[$boolitem], '"') != 1)
								{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be 1/0)', array('%col%' => $boolitem+1, '%row%' => $i+1));
								}
							}
							
							// Now identifiables
							$identifiableitems = array(array($qa, $qa_type), array($lead, $lead_type), array($owner, $owner_type));
							
							foreach ($identifiableitems as $identifiableitem)
							{
								if (($identifiableitem[0] === null || $identifiableitem[1] === null) && !($identifiableitem[0] === null && $identifiableitem[1] === null))
								{
										$errors[] = TBGContext::getI18n()->__('Row %row%: Both the type and item ID must be supplied for owner/lead/qa fields', array('%row%' => $i+1));
										continue;
								}
								
								if ($identifiableitem[1] !== null && trim($activerow[$identifiableitem[1]], '"') != 1 && trim($activerow[$identifiableitem[1]], '"') != 2)
								{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be 1 for a user or 2 for a team)', array('%col%' => $identifiableitem[1]+1, '%row%' => $i+1));
								}
								
								if ($identifiableitem[0] !== null && !(is_numeric(trim($activerow[$identifiableitem[0]], '"'))))
								{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => $identifiableitem[0]+1, '%row%' => $i+1));
								}
								elseif ($identifiableitem[0] !== null && (is_numeric(trim($activerow[$identifiableitem[0]], '"'))))
								{
									// check if they exist
									switch (trim($activerow[$identifiableitem[1]], '"'))
									{
										case 1:
											try
											{
												TBGContext::factory()->TBGUser(trim($activerow[$identifiableitem[0]], '"'));
											}
											catch (Exception $e)
											{
												$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: user does not exist', array('%col%' => $identifiableitem[0]+1, '%row%' => $i+1));
											}
											break;
										case 2:
											try
											{
												TBGContext::factory()->TBGTeam(trim($activerow[$identifiableitem[0]], '"'));
											}
											catch (Exception $e)
											{
												$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: team does not exist', array('%col%' => $identifiableitem[0]+1, '%row%' => $i+1));
											}
											break;
									}
								}
							}
							
							// Now check client exists
							if ($client !== null)
							{
								if (!is_numeric(trim($activerow[$client], '"')))
								{
									$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => $client+1, '%row%' => $i+1));
								}
								else
								{
									try
									{
										TBGContext::factory()->TBGClient(trim($activerow[$client], '"'));
									}
									catch (Exception $e)
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: client does not exist', array('%col%' => $client+1, '%row%' => $i+1));
									}
								}
							}
							
							// Now check if workflow exists
							if ($workflow_id !== null)
							{
								if (!is_numeric(trim($activerow[$workflow_id], '"')))
								{
									$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be a number)', array('%col%' => $workflow_id+1, '%row%' => $i+1));
								}
								else
								{
									try
									{
										TBGContext::factory()->TBGWorkflowScheme(trim($activerow[$workflow_id], '"'));
									}
									catch (Exception $e)
									{
										$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: workflow scheme does not exist', array('%col%' => $workflow_id+1, '%row%' => $i+1));
									}
								}
							}
							
							// Finally check if the summary type is valid. At this point, your error list has probably become so big it has eaten up all your available RAM...
							if ($summary_type !== null)
							{
								if (trim($activerow[$summary_type], '"') != 'issuetypes' && trim($activerow[$summary_type], '"') != 'milestones')
								{
									$errors[] = TBGContext::getI18n()->__('Row %row% column %col%: invalid value (must be \'issuetypes\' or \'milestones\')', array('%col%' => $summary_type+1, '%row%' => $i+1));
								}
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
					return $this->renderJSON(array('failed' => true, 'errordetail' => $errordiv, 'error' => TBGContext::getI18n()->__('Errors occured while importing, see the error list in the import screen for further details')));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('failed' => true, 'errordetail' => $e->getMessage(), 'error' => $e->getMessage()));
			}
			
			if ($request->getParameter('csv_dry_run'))
			{
				return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('Dry-run successful, you can now uncheck the dry-run box and import your data.')));
			}
			else
			{
				//return $this->renderJSON(array('failed' => false, 'message' => __('Import successfully completed!')));
				return $this->renderJSON(array('failed' => false, 'message' => 'non-dry unimplemented but otherwise okay'));
			}
		}
	}
