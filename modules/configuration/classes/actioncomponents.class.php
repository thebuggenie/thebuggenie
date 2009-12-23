<?php

	class configurationActionComponents extends BUGSactioncomponent
	{

		public function componentConfigLeftmenu()
		{
			$config_sections = array();
			$config_sections[12] = array('route' => 'configure_settings', 'description' => __('Settings'), 'icon' => 'general', 'module' => 'core');
			if (BUGScontext::getUser()->getScope()->getID() == 1)
			{
				//$config_sections[14] = array('route' => 'configure_scopes', 'description' => __('Scopes'), 'icon' => 'scopes', 'module' => 'core');
			}
			$config_sections[5] = array('route' => 'configure_permissions', 'description' => __('Permissions'), 'icon' => 'permissions', 'module' => 'core');
			//$config_sections[16] = array('route' => 'configure_import', 'description' => __('Import data'), 'icon' => 'import', 'module' => 'core');
			$config_sections[3] = array('route' => 'configure_files', 'description' => __('Uploads &amp; attachments'), 'icon' => 'files', 'module' => 'core');
			$config_sections[10] = array('route' => 'configure_projects', 'description' => __('Projects'), 'icon' => 'projects', 'module' => 'core');
			#$config_sections[9] = array('route' => 'configure_milestones', 'description' => __('Milestones'), 'icon' => 'builds');
			$config_sections[6] = array('icon' => 'issuetypes', 'description' => __('Issue types'), 'route' => 'configure_issuetypes', 'module' => 'core');
			$config_sections[4] = array('icon' => 'resolutiontypes', 'description' => __('Issue fields'), 'route' => 'configure_issuefields', 'module' => 'core');
			/*$config_sections[4][] = array('icon' => 'issuetypes', 'description' => __('Issue types'), 'route' => 'configure_issue_types');
			$config_sections[4][] = array('icon' => 'resolutiontypes', 'description' => __('Resolution types'), 'route' => 'configure_resolution_types');
			$config_sections[4][] = array('icon' => 'priorities', 'description' => __('Priority levels'), 'route' => 'configure_priority_levels');
			$config_sections[4][] = array('icon' => 'categories', 'description' => __('Categories'), 'route' => 'configure_categories');
			$config_sections[4][] = array('icon' => 'repro', 'description' => __('Reproduction levels'), 'route' => 'configure_reproduction_levels');
			$config_sections[4][] = array('icon' => 'statustypes', 'description' => __('Status types'), 'route' => 'configure_status_types');
			$config_sections[4][] = array('icon' => 'severities', 'description' => __('Severity levels'), 'route' => 'configure_severity_levels');
			$config_sections[4][] = array('icon' => 'users', 'description' => __('User states'), 'route' => 'configure_user_states');*/
			$config_sections[2] = array('route' => 'configure_users', 'description' => __('Users, teams &amp; groups'), 'icon' => 'users', 'module' => 'core');
			#$config_sections[1] = array('route' => 'configure_teams_groups', 'description' => __('Teams &amp; groups'), 'icon' => 'projects');
			$config_sections[15][] = array('route' => 'configure_modules', 'description' => __('Modules'), 'icon' => 'modules', 'module' => 'core');
			foreach (BUGScontext::getModules() as $module)
			{
				if ($module->hasAccess() && $module->isVisibleInConfig())
				{
					$config_sections[15][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => $module->getConfigTitle(), 'icon' => $module->getName(), 'module' => $module->getName());
				}
			}
			$this->config_sections = $config_sections;
			if ($this->selected_section == 15)
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

		public function componentIssueTypes()
		{
			$this->issuetype = BUGSfactory::BUGSissuetypeLab($this->id);
			//$this->items = $this->issuetype->ge

		}

		public function componentIssueFields_CustomType()
		{
			
		}

	}