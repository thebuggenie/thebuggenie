<?php

	/**
	 * Module class, extended by all thebuggenie modules
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Module class, extended by all thebuggenie modules
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	abstract class TBGModule extends TBGIdentifiableClass implements TBGIdentifiable 
	{
		
		protected $_classname = '';
		protected $_description = '';
		protected $_enabled = false;
		protected $_longname = '';
		protected $_showinconfig = false;
		protected $_showinmenu = false;
		protected $_showinusermenu = false;
		protected $_version = '';
		protected $_shortname = '';
		protected $_module_menu_title = '';
		protected $_module_config_title = '';
		protected $_module_config_description = '';
		protected $_module_version = '';
		protected $_availablepermissions = array();
		protected $_listeners = array();
		protected $_settings = array();
		protected $_routes = array();
		protected $_has_account_settings = false;
		protected $_has_config_settings = false;
		
		static protected $_permissions = array();

		/**
		 * Installs a module
		 * 
		 * @param string $module_name the module key
		 * @return boolean Whether the install succeeded or not
		 */
		public static function installModule($module_name)
		{
			TBGContext::addClasspath(TBGContext::getIncludePath() . 'modules/' . $module_name . '/classes');
			if (is_dir(TBGContext::getIncludePath() . 'modules/' . $module_name . '/classes/B2DB'))
			{
				TBGContext::addClasspath(TBGContext::getIncludePath() . 'modules/' . $module_name . '/classes/B2DB');
			}
			$classname = file_get_contents(TBGContext::getIncludePath() . 'modules/' . $module_name . '/class');

			return (call_user_func(array($classname, 'install')));
		}
		
		/**
		 * Class constructor
		 *
		 * @param integer $m_id
		 * @param B2DBRow $row
		 */
		public function __construct($m_id, $row = null)
		{
			if ($row === null)
			{
				$row = B2DB::getTable('TBGModulesTable')->doSelectById($m_id);
			}
			$this->_itemid = $m_id;
			$this->_name = $row->get(TBGModulesTable::MODULE_NAME);
			$this->_classname = $row->get(TBGModulesTable::CLASSNAME);
			$this->_enabled = (bool) $row->get(TBGModulesTable::ENABLED);
			$this->_shortname = $row->get(TBGModulesTable::MODULE_NAME);
			$this->_showinconfig = (bool) $row->get(TBGModulesTable::SHOW_IN_CONFIG);
			$this->_showinmenu = (bool) $row->get(TBGModulesTable::SHOW_IN_MENU);
			$this->_showinusermenu = (bool) $row->get(TBGModulesTable::SHOW_IN_USERMENU);
			$this->_version = $row->get(TBGModulesTable::VERSION);
		}
		
		public function log($message, $level = 1)
		{
			TBGLogging::log($message, $this->getName(), $level);
		}
		
		public function disable()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::ENABLED, 0);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_enabled = false;
		}

		public function enable()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::ENABLED, 1);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_enabled = true;
		}
		
		public function showInMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::SHOW_IN_MENU, 1);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_showinmenu = true;
		}
		
		public function hideFromMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::SHOW_IN_MENU, 0);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_showinmenu = false;
		}
		
		public function showInUserMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::SHOW_IN_USERMENU, 1);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_showinusermenu = true;
		}

		public function hideFromUserMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::SHOW_IN_USERMENU, 0);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_showinusermenu = false;
		}

		abstract public function uninstall();
		
		protected function _uninstall()
		{
			$scope = TBGContext::getScope()->getID();
			B2DB::getTable('TBGModulesTable')->doDeleteById($this->getID());
			B2DB::getTable('TBGEnabledModuleListenersTable')->removeAllModuleListeners($this->getName(), $scope);
			TBGSettings::deleteModuleSettings($this->getName());
			TBGContext::deleteModulePermissions($this->getName());
		}
		
		/**
		 * Installs the module, adds it to the list of loaded modules and returns it
		 *
		 * @param string $identifier
		 * @param string $longname
		 * @param string $description
		 * @param string $classname
		 * @param bool   $show_in_config
		 * @param bool   $show_in_menu
		 * @param bool   $show_in_usermenu
		 * @param string $version
		 * @param bool   $enabled
		 * @param int	$scope
		 * 
		 * @return TBGModule
		 */
		static protected function _install($identifier, $classname, $version, $show_in_config, $show_in_menu, $show_in_usermenu, $scope)
		{
  			if (!TBGContext::getScope() instanceof TBGScope) throw new Exception('No scope??');

			TBGLogging::log('installing module' . $identifier);
			$module_id = B2DB::getTable('TBGModulesTable')->installModule($identifier, $classname, $version, $show_in_config, $show_in_menu, $show_in_usermenu, $scope);
  			
			if (class_exists($classname))
			{
				$module = new $classname($module_id);
				if ($scope == TBGContext::getScope()->getID())
				{
					TBGContext::addModule($module, $identifier);
				}
				$module->setPermission(0, 0, 0, true, $scope);
			}
			else
			{
				throw new Exception('Can not load new instance of type ' . $classname . ', is not loaded');
			}

			return $module;
		}
		
		public function getClassname()
		{
			return $this->_classname;
		}
		
		public function disableListener($module, $identifier, $scope)
		{
			if (array_key_exists($module . '_' . $identifier, $this->_listeners))
			{
				$this->_listeners[$module . '_' . $identifier]['enabled'] = false;
				B2DB::getTable('TBGEnabledModuleListenersTable')->removePermanentListener($module, $identifier, $this->getName());
			}
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		public function __call($func, $args)
		{
			throw new Exception('Trying to call function ' . $func . '() in module ' . $this->_shortname . ', but the function does not exist');
		}
		
		public function getID()
		{
			return $this->_itemid;
		}
		
		public function getName()
		{
			return $this->_name;
		}

		public function setLongName($name)
		{
			$this->_longname = $name;
		}

		public function getLongName()
		{
			return $this->_longname;
		}

		public function setMenuTitle($title)
		{
			$this->_module_menu_title = $title;
		}
		
		public function getMenuTitle()
		{
			return $this->_module_menu_title;
		}
		
		public function addAvailablePermission($permission_name, $description, $target = 0)
		{
			$this->_availablepermissions[$permission_name] = array('description' => $description, 'target_id' => $target);
		}
		
		public function getAvailablePermissions()
		{
			return $this->_availablepermissions;
		}
		
		public function addAvailableListener($module, $identifier, $callback_function, $description)
		{
			$this->_listeners[$module . '_' . $identifier] = array('callback_function' => $callback_function, 'description' => $description, 'enabled' => false);
		}
		
		public function getAvailableListeners()
		{
			return $this->_listeners;
		}
		
		public static function cacheAllAccessPermissions()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGModulePermissionsTable::SCOPE, TBGContext::getScope()->getID());
			
			$resultset = B2DB::getTable('TBGModulePermissionsTable')->doSelect($crit);
			while ($row = $resultset->getNextRow())
			{
				self::cacheAccessPermission($row->get(TBGModulePermissionsTable::MODULE_NAME), $row->get(TBGModulePermissionsTable::UID), $row->get(TBGModulePermissionsTable::GID), $row->get(TBGModulePermissionsTable::TID), 0, (bool) $row->get(TBGModulePermissionsTable::ALLOWED));
			}
		}

		public static function cacheAccessPermission($module_name, $uid, $gid, $tid, $all, $allowed)
		{
			self::$_permissions[$module_name][] = array('uid' => $uid, 'gid' => $gid, 'tid' => $tid, 'all' => $all, 'allowed' => $allowed); 
		}

		public function setPermission($uid, $gid, $tid, $allowed, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			B2DB::getTable('TBGModulePermissionsTable')->deleteByModuleAndUIDandGIDandTIDandScope($this->getName(), $uid, $gid, $tid, $scope);
			B2DB::getTable('TBGModulePermissionsTable')->setPermissionByModuleAndUIDandGIDandTIDandScope($this->getName(), $uid, $gid, $tid, $allowed, $scope);
			if ($scope == TBGContext::getScope()->getID())
			{
				self::cacheAccessPermission($this->getName(), $uid, $gid, $tid, 0, $allowed);
			}
		}
		
		public static function rebuildAccessPermissionCache()
		{
			self::$_permissions = array();
			self::cacheAllAccessPermissions();
		}
		
		public static function getAccessPermissionList()
		{
			if (self::$_permissions === null)
			{
				self::rebuildAccessPermissionCache();
			}
			return self::$_permissions;
		}
		
		public function hasAccess($uid = null, $gid = null, $tid = null, $all = null, $debug = false)
		{
			$permissions = self::getAccessPermissionList();
			if (!array_key_exists($this->getName(), $permissions))
			{
				throw new Exception('This modules access permission has not been cached. Something is wrong.');
			}
			$debug = false;
			if ($debug) TBGLogging::log($this->_name);
			if ($debug)
			{
				foreach ($permissions[$this->getName()] as $aPerm)
				{
					TBGLogging::log($aPerm);
				}
			}
			if ($all == null)
			{
				$uid = ($uid === null) ? TBGContext::getUser()->getUID() : $uid;
				$tid = ($tid === null) ? TBGContext::getUser()->getTeams() : $tid;
				if (!TBGContext::getUser()->getGroup() instanceof TBGGroup) return false;
				$gid = ($gid === null) ? TBGContext::getUser()->getGroup()->getID() : $gid;
				
				foreach ($permissions[$this->getName()] as $aPerm)
				{
					if ($aPerm['uid'] == $uid && $uid != 0)
					{
						if ($debug) echo 'returning from uid';
						return $aPerm['allowed'];
					}
				}
				
				foreach ($permissions[$this->getName()] as $aPerm)
				{
					if ($aPerm['tid'] == $tid && $tid != 0)
					{
						if ($debug) echo 'returning from uid';
						return $aPerm['allowed'];
					}
				}
				
				foreach ($permissions[$this->getName()] as $aPerm)
				{
					if ($aPerm['gid'] == $gid && $gid != 0)
					{
						if ($debug) echo 'returning from uid';
						return $aPerm['allowed'];
					}
				}
				
			}
			
			foreach ($permissions[$this->getName()] as $aPerm)
			{
				if (($aPerm['uid'] + $aPerm['gid'] + $aPerm['tid']) == 0)
				{
					return $aPerm['allowed'];
				}
			}
			
			return false;
		}
				
		static function loadModuleListeners($module_names)
		{
			if ($res = B2DB::getTable('TBGEnabledModuleListenersTable')->getAll($module_names))
			{
				while ($row = $res->getNextRow())
				{
					$module = TBGContext::getModule($row->get(TBGEnabledModuleListenersTable::MODULE_NAME));
					if ($module->hasAccess() && $module->isEnabled())
					{
						$module->enableListener($row->get(TBGEnabledModuleListenersTable::MODULE), $row->get(TBGEnabledModuleListenersTable::IDENTIFIER));
					}
				}
			}
		}
		
		public function enableListener($module, $identifier, $scope = null)
		{
			if (array_key_exists($module . '_' . $identifier, $this->_listeners) && !$this->_listeners[$module . '_' . $identifier]['enabled'])
			{
				if ($scope === null || $scope == TBGContext::getScope()->getID())
				{
					$listener = &$this->_listeners[$module . '_' . $identifier];
					TBGContext::listenToTrigger($module, $identifier, array($this, $listener['callback_function']));
					$listener['enabled'] = true;
				}
			}
		}

		public function enableListenerSaved($module, $identifier, $scope = null)
		{
			$this->enableListener($module, $identifier, $scope);
			B2DB::getTable('TBGEnabledModuleListenersTable')->savePermanentListener($module, $identifier, $this->getName(), $scope);
		}

		public function setConfigTitle($title)
		{
			$this->_module_config_title = $title;
		}

		public function getConfigTitle()
		{
			return $this->_module_config_title;
		}

		public function setConfigDescription($description)
		{
			$this->_module_config_description = $description;
		}
		
		public function getConfigDescription()
		{
			return $this->_module_config_description;
		}
		
		public function getVersion()
		{
			return $this->_module_version;
		}
		
		/**
		 * Shortcut for the global settings function
		 * 
		 * @param string  $setting the name of the setting
		 * @param integer $uid     the uid for the user to check
		 * 
		 * @return mixed
		 */
		public function getSetting($setting, $uid = 0)
		{
			return TBGSettings::get($setting, $this->getName(), TBGContext::getScope()->getID(), $uid);
		}
		
		public function saveSetting($setting, $value, $uid = 0, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			return TBGSettings::saveSetting($setting, $value, $this->getName(), $scope, $uid);
		}
		
		/**
		 * Returns whether the module is visible in the menu
		 *
		 * @return boolean
		 */
		public function isVisibleInMenu()
		{
			return $this->_showinmenu;
		}
		
		/**
		 * Returns whether the module is visible in configuration
		 *
		 * @return boolean
		 */
		public function isVisibleInConfig()
		{
			return $this->_showinconfig;
		}
		
		/**
		 * Returns whether the module is visible in the user menu
		 *
		 * @return boolean
		 */
		public function isVisibleInUsermenu()
		{
			return $this->_showinusermenu;
		}
		
		/**
		 * Returns whether the module is visible in the menu
		 *
		 * @return boolean
		 */
		public function isEnabled()
		{
			return $this->_enabled;
		}
		
		public function isListening($module, $identifier)
		{
			if ($this->_enabled && array_key_exists($module . '_' . $identifier, $this->_listeners))
			{
				return $this->_listeners[$module . '_' . $identifier]['enabled'];
			}
			return false;
		}

		abstract public function initialize();

		public function addRoute($key, $url, $function, $params = array())
		{
			$this->_routes[] = array($key, $url, $this->getName(), $function, $params);
		}

		public function loadRoutes()
		{
			foreach ($this->_routes as $route)
			{
				$this->log('adding route ' . $route[0]);
				if (isset($route[4]) && !empty($route[4]))
				{
					TBGContext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3], $route[4]);
				}
				else
				{
					TBGContext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3]);
				}
				$this->log('done (adding route ' . $route[0] . ')');
			}
		}
		
		public function activate()
		{
			if ($this->_enabled == false || $this->hasAccess() == false)
			{
				tbg_showError('B2 Engine error - Not permitted', "You do not have access to this module. <br>You may have tried to access a link that is no longer in use.<br><br>If you think this is an error, please contact the administrator of this BUGS 2 instance.", true);
				exit();				
			}
		}
		
		public function setDescription($description)
		{
			$this->_description = $description;
		}

		public function getDescription()
		{
			return $this->_description;
		}
		
		public static function getAllModulePermissions($module, $uid, $tid, $gid)
		{
	
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGModulePermissionsTable::MODULE_NAME, $module);
			//$sql = "select b2mp.allowed from tbg_2_modulepermissions b2mp where b2mp.module_name = '$module'";
			switch (true)
			{
				case ($uid != 0):
					//$sql .= " and uid = $uid";
					$crit->addWhere(TBGModulePermissionsTable::UID, $uid);
				case ($tid != 0):
					//$sql .= " and tid = $tid";
					$crit->addWhere(TBGModulePermissionsTable::TID, $tid);
				case ($gid != 0):
					//$sql .= " and gid = $gid";
					$crit->addWhere(TBGModulePermissionsTable::GID, $gid);
			}
			if (($uid + $tid + $gid) == 0)
			{
				//$sql .= " and uid = $uid and tid = $tid and gid = $gid";
				$crit->addWhere(TBGModulePermissionsTable::UID, $uid);
				$crit->addWhere(TBGModulePermissionsTable::TID, $tid);
				$crit->addWhere(TBGModulePermissionsTable::GID, $gid);
			}
			
			//$sql .= " AND b2mp.scope = " . TBGContext::getScope()->getID();
			$crit->addWhere(TBGModulePermissionsTable::SCOPE, TBGContext::getScope()->getID());
	
			//$res = b2db_sql_query($sql, B2DB::getDBlink());
	
			#print $sql;
	
			$permissions = array();
			$res = B2DB::getTable('TBGModulePermissionsTable')->doSelect($crit);
	
			while ($row = $res->getNextRow())
			{
				$permissions[] = array('allowed' => $row->get(TBGModulePermissionsTable::ALLOWED));
			}
	
			return $permissions;
		}
	
		public function loadHelpTitle($topic)
		{
			return $topic;
		}
		
		public function getRoute()
		{
			return 'login';
		}

		public function setHasAccountSettings($val = true)
		{
			$this->_has_account_settings = (bool) $val;
		}

		public function hasAccountSettings()
		{
			return $this->_has_account_settings;
		}

		public function setHasConfigSettings($val = true)
		{
			$this->_has_config_settings = (bool) $val;
		}

		public function hasConfigSettings()
		{
			return $this->_has_config_settings;
		}

		public function hasProjectAwareRoute()
		{
			return false;
		}

		public function getTabKey()
		{
			return $this->getName();
		}

	}
