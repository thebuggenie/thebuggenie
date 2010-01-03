<?php

	class configurationActionComponents extends BUGSactioncomponent
	{

		public function componentConfigLeftmenu()
		{
			$config_sections = array();
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_SETTINGS] = array('route' => 'configure_settings', 'description' => __('Settings'), 'icon' => 'general', 'module' => 'core');
			if (BUGScontext::getUser()->getScope()->getID() == 1)
			{
				//$config_sections[BUGSsettings::CONFIGURATION_SECTION_SCOPES] = array('route' => 'configure_scopes', 'description' => __('Scopes'), 'icon' => 'scopes', 'module' => 'core');
			}
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_PERMISSIONS] = array('route' => 'configure_permissions', 'description' => __('Permissions'), 'icon' => 'permissions', 'module' => 'core');
			//$config_sections[BUGSsettings::CONFIGURATION_SECTION_IMPORT] = array('route' => 'configure_import', 'description' => __('Import data'), 'icon' => 'import', 'module' => 'core');
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_UPLOADS] = array('route' => 'configure_files', 'description' => __('Uploads &amp; attachments'), 'icon' => 'files', 'module' => 'core');
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_PROJECTS] = array('route' => 'configure_projects', 'description' => __('Projects'), 'icon' => 'projects', 'module' => 'core');
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_ISSUETYPES] = array('icon' => 'issuetypes', 'description' => __('Issue types'), 'route' => 'configure_issuetypes', 'module' => 'core');
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_ISSUEFIELDS] = array('icon' => 'resolutiontypes', 'description' => __('Issue fields'), 'route' => 'configure_issuefields', 'module' => 'core');
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_USERS] = array('route' => 'configure_users', 'description' => __('Users, teams &amp; groups'), 'icon' => 'users', 'module' => 'core');
			$config_sections[BUGSsettings::CONFIGURATION_SECTION_MODULES][] = array('route' => 'configure_modules', 'description' => __('Modules'), 'icon' => 'modules', 'module' => 'core');
			foreach (BUGScontext::getModules() as $module)
			{
				if ($module->hasAccess() && $module->isVisibleInConfig())
				{
					$config_sections[BUGSsettings::CONFIGURATION_SECTION_MODULES][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => $module->getConfigTitle(), 'icon' => $module->getName(), 'module' => $module->getName());
				}
			}
			$this->config_sections = $config_sections;
			if ($this->selected_section == BUGSsettings::CONFIGURATION_SECTION_MODULES)
			{
				if (BUGScontext::getRouting()->getCurrentRouteName() == 'configure_modules')
				{
					$this->selected_subsection = 'core';
				}
				else
				{
					$this->selected_subsection = BUGScontext::getRequest()->getParameter('config_module');
				}
			}

		}

		public function componentIssueFields()
		{
			$this->items = array();
			$this->showitems = true;
			$this->iscustom = false;
			$types = BUGSdatatype::getTypes();

			if (array_key_exists($this->type, $types))
			{
				$this->items = call_user_func(array($types[$this->type], 'getAll'));
			}
			else
			{
				$customtype = BUGScustomdatatype::getByKey($this->type);
				$this->showitems = $customtype->hasCustomOptions();
				$this->iscustom = true;
				if ($this->showitems)
				{
					$this->items = $customtype->getOptions();
				}
				$this->customtype = $customtype;
			}
		}

		public function componentIssueTypeOptions()
		{
			$this->issuetype = BUGSfactory::BUGSissuetypeLab($this->id);
			$this->builtinfields = BUGSdatatype::getAvailableFields(true);
			$this->customtypes = BUGScustomdatatype::getAll();
			$this->visiblefields = $this->issuetype->getVisibleFields();
		}

		public function componentIssueType()
		{
			$this->icons = BUGSissuetype::getIcons();
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
				$permissions = BUGScontext::getAvailablePermissions();
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

	}