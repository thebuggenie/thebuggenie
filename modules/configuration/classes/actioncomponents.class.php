<?php

	class configurationActionComponents extends TBGActionComponent
	{

		public function componentGeneral()
		{
			$files = scandir(THEBUGGENIE_CORE_PATH . 'geshi' . DS . 'geshi' . DS);
			$geshi_languages = array();
			foreach ($files as $file)
			{
				if (mb_strstr($file, '.php') === false) continue;
				$lang = str_replace('.php', '', $file);
				$geshi_languages[$lang] = $lang;
			}
			$this->geshi_languages = $geshi_languages;
		}

		public function componentUser()
		{
			$this->userstates = TBGUserstate::getAll();
		}

		public function componentAppearance()
		{
			$this->themes = TBGContext::getThemes();
			$this->icons = TBGContext::getIconSets();
		}

		public function componentReglang()
		{
			$this->languages = TBGI18n::getLanguages();
		}
		
		public function componentOffline()
		{
			
		}

		public function componentLeftmenu()
		{
			$i18n = TBGContext::getI18n();
			$config_sections = array();

			if (TBGContext::getUser()->getScope()->getID() == 1)
				$config_sections[TBGSettings::CONFIGURATION_SECTION_SCOPES] = array('route' => 'configure_scopes', 'description' => $i18n->__('Scopes'), 'icon' => 'scopes', 'module' => 'core');

			$config_sections[TBGSettings::CONFIGURATION_SECTION_SETTINGS] = array('route' => 'configure_settings', 'description' => $i18n->__('Settings'), 'icon' => 'general', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_PERMISSIONS] = array('route' => 'configure_permissions', 'description' => $i18n->__('Permissions'), 'icon' => 'permissions', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_AUTHENTICATION] = array('route' => 'configure_authentication', 'description' => $i18n->__('Authentication'), 'icon' => 'authentication', 'module' => 'core');

			if (TBGContext::getScope()->isUploadsEnabled())
				$config_sections[TBGSettings::CONFIGURATION_SECTION_UPLOADS] = array('route' => 'configure_files', 'description' => $i18n->__('Uploads &amp; attachments'), 'icon' => 'files', 'module' => 'core');
			
			$config_sections[TBGSettings::CONFIGURATION_SECTION_IMPORT] = array('route' => 'configure_import', 'description' => $i18n->__('Import data'), 'icon' => 'import', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_PROJECTS] = array('route' => 'configure_projects', 'description' => $i18n->__('Projects'), 'icon' => 'projects', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUETYPES] = array('icon' => 'issuetypes', 'description' => $i18n->__('Issue types'), 'route' => 'configure_issuetypes', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_ISSUEFIELDS] = array('icon' => 'resolutiontypes', 'description' => $i18n->__('Issue fields'), 'route' => 'configure_issuefields', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_WORKFLOW] = array('icon' => 'workflow', 'description' => $i18n->__('Workflow'), 'route' => 'configure_workflow', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_USERS] = array('route' => 'configure_users', 'description' => $i18n->__('Users, teams, clients &amp; groups'), 'icon' => 'users', 'module' => 'core');
			$config_sections[TBGSettings::CONFIGURATION_SECTION_MODULES][] = array('route' => 'configure_modules', 'description' => $i18n->__('Modules'), 'icon' => 'modules', 'module' => 'core');
			foreach (TBGContext::getModules() as $module)
			{
				if ($module->hasConfigSettings() && $module->isEnabled())
				{
					$config_sections[TBGSettings::CONFIGURATION_SECTION_MODULES][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => TBGContext::geti18n()->__($module->getConfigTitle()), 'icon' => $module->getName(), 'module' => $module->getName());
				}
			}
			$breadcrumblinks = array();
			foreach ($config_sections as $section)
			{
				if (is_array($section) && !array_key_exists('route', $section))
				{
					foreach ($section as $subsection)
					{
						$url = (is_array($subsection['route'])) ? make_url($subsection['route'][0], $subsection['route'][1]) : make_url($subsection['route']);
						$breadcrumblinks[] = array('url' => $url, 'title' => $subsection['description']);
					}
				}
				else
				{
					$breadcrumblinks[] = array('url' => make_url($section['route']), 'title' => $section['description']);
				}
			}
			$this->breadcrumblinks = $breadcrumblinks;

			$this->config_sections = $config_sections;
			if ($this->selected_section == TBGSettings::CONFIGURATION_SECTION_MODULES)
			{
				if (TBGContext::getRouting()->getCurrentRouteName() == 'configure_modules')
				{
					$this->selected_subsection = 'core';
				}
				else
				{
					$this->selected_subsection = TBGContext::getRequest()->getParameter('config_module');
				}
			}

		}

		public function componentIssueFields()
		{
			$this->items = array();
			$this->showitems = true;
			$this->iscustom = false;
			$types = TBGDatatype::getTypes();

			if (array_key_exists($this->type, $types))
			{
				$this->items = call_user_func(array($types[$this->type], 'getAll'));
			}
			else
			{
				$customtype = TBGCustomDatatype::getByKey($this->type);
				$this->showitems = $customtype->hasCustomOptions();
				$this->iscustom = true;
				if ($this->showitems)
				{
					$this->items = $customtype->getOptions();
				}
				$this->customtype = $customtype;
			}
		}

		public function componentIssueTypeSchemeOptions()
		{
			$this->issuetype = TBGContext::factory()->TBGIssuetype($this->id);
			$this->scheme = TBGContext::factory()->TBGIssuetypeScheme($this->scheme_id);
			$this->builtinfields = TBGDatatype::getAvailableFields(true);
			$this->customtypes = TBGCustomDatatype::getAll();
			$this->visiblefields = $this->scheme->getVisibleFieldsForIssuetype($this->issuetype);
		}

		public function componentIssueType()
		{
			$this->icons = TBGIssuetype::getIcons();
		}
		
		public function componentIssuetypescheme()
		{
			
		}

		public function componentIssueFields_CustomType()
		{
			
		}

		public function componentPermissionsinfo()
		{
			switch ($this->mode)
			{
				case 'datatype':
					
					break;
			}
		}

		public function componentPermissionsinfoitem()
		{
			
		}
		
		protected function _getPermissionListFromKey($key, $permissions = null)
		{
			if ($permissions === null)
			{
				$permissions = TBGContext::getAvailablePermissions();
			}
			foreach ($permissions as $pkey => $permission)
			{
				if ($pkey == $key)
				{
					return (array_key_exists('details', $permission)) ? $permission['details'] : array();
				}
				elseif (array_key_exists('details', $permission) && count($permission['details']) > 0 && ($plist = $this->_getPermissionListFromKey($key, $permission['details'])))
				{
					return $plist;
				}
			}
			return array();
		}
		
		public function componentPermissionsblock()
		{
			if (!is_array($this->permissions_list))
			{
				$this->permissions_list = $this->_getPermissionListFromKey($this->permissions_list);
			}
		}

		public function componentPermissionsConfigurator()
		{
			$this->base_id = (isset($this->base_id)) ? $this->base_id : 0;
			$this->user_id = (isset($this->user_id)) ? $this->user_id : 0;
		}

		public function componentProjectConfig_Container()
		{
			$this->access_level = (TBGContext::getUser()->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_PROJECTS)) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
			$this->section = isset($this->section) ? $this->section : 'info';
		}

		public function componentProjectConfig()
		{
			$this->access_level = (TBGContext::getUser()->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_PROJECTS)) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
			$this->statustypes = TBGStatus::getAll();
			$this->selected_tab = isset($this->section) ? $this->section : 'info';
		}
		
		public function componentProjectInfo()
		{
			$this->valid_subproject_targets = TBGProject::getValidSubprojects($this->project);
		}

		public function componentProjectSettings()
		{
			$this->statustypes = TBGStatus::getAll();
		}
		
		public function componentProjectMilestones()
		{
			$this->milestones = $this->project->getAllMilestones();
		}

		public function componentProjectEdition()
		{
			$this->access_level = (TBGContext::getUser()->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_PROJECTS)) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
		}
		
		public function componentWorkflowtransitionaction()
		{
			$available_assignees = array();
			foreach (TBGContext::getUser()->getTeams() as $team)
			{
				foreach ($team->getMembers() as $user)
				{
					$available_assignees[$user->getID()] = $user;
				}
			}
			foreach (TBGContext::getUser()->getFriends() as $user)
			{
				$available_assignees[$user->getID()] = $user;
			}
			$this->available_assignees = $available_assignees;
		}
		
		public function componentBuildbox()
		{
			$this->access_level = (TBGContext::getUser()->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_PROJECTS)) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
		}
		
		public function componentBuild()
		{
			if (!isset($this->build))
			{
				$this->build = new TBGBuild();
				$this->build->setProject(TBGContext::getCurrentProject());
				$this->build->setName(TBGContext::getI18n()->__('%project_name% version 0.0.0', array('%project_name%' => $this->project->getName())));
				if (TBGContext::getRequest()->getParameter('edition_id') && $edition = TBGContext::factory()->TBGEdition(TBGContext::getRequest()->getParameter('edition_id')))
				{
					$this->build->setEdition($edition);
				}
			}
		}
		
		public function componentProjecticons()
		{
			
		}
		
		public function componentProjectworkflow()
		{
			
		}

	}