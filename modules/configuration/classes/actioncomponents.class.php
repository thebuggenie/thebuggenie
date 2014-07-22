<?php

	class configurationActionComponents extends TBGActionComponent
	{

		public function componentGeneral()
		{
			$files = scandir(THEBUGGENIE_CORE_PATH . 'lib' . DS . 'easybook' . DS . 'geshi' . DS);
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
			$this->onlinestate = TBGSettings::getOnlineState();
			$this->awaystate = TBGSettings::getAwayState();
			$this->offlinestate = TBGSettings::getOfflineState();
		}

		public function componentAppearance()
		{
			$this->themes = TBGContext::getThemes();
			$this->icons = TBGContext::getIconSets();
		}

		public function componentReglang()
		{
			$this->languages = TBGI18n::getLanguages();
			$this->timezones = tbg_get_timezones();
		}
		
		public function componentOffline()
		{
			
		}

		public function componentLeftmenu()
		{
			$config_sections = TBGSettings::getConfigSections(TBGContext::getI18n());
			$breadcrumblinks = array();
			foreach ($config_sections as $key => $sections)
			{
				foreach ($sections as $section)
				{
					if ($key == TBGSettings::CONFIGURATION_SECTION_MODULES)
					{
						$url = (is_array($section['route'])) ? make_url($section['route'][0], $section['route'][1]) : make_url($section['route']);
						$breadcrumblinks[] = array('url' => $url, 'title' => $section['description']);
					}
					else
					{
						$breadcrumblinks[] = array('url' => make_url($section['route']), 'title' => $section['description']);
					}
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

		public function componentIssueFieldPermissions()
		{
		}

		public function componentPermissionsPopup()
		{
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
			$this->team_id = (isset($this->team_id)) ? $this->team_id : 0;
			$this->mode = ($this->user_id) ? 'user' : 'team';
		}

		public function componentWorkflowtransitionaction()
		{
			$available_assignees_users = array();
			foreach (TBGContext::getUser()->getTeams() as $team)
			{
				foreach ($team->getMembers() as $user)
				{
					$available_assignees_users[$user->getID()] = $user;
				}
			}
			foreach (TBGContext::getUser()->getFriends() as $user)
			{
				$available_assignees_users[$user->getID()] = $user;
			}
			$this->available_assignees_teams = TBGTeam::getAll();
			$this->available_assignees_users = $available_assignees_users;
		}

		public function componentUserscopes()
		{
			$this->scopes = TBGScope::getAll();
		}
		
		public function componentSiteicons()
		{
		}
	}
