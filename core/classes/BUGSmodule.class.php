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
	abstract class BUGSmodule extends BUGSidentifiableclass implements BUGSidentifiable 
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
		
		static protected $_permissions = array();
		
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
				$row = B2DB::getTable('B2tModules')->doSelectById($m_id);
			}
			$this->_itemid = $m_id;
			$this->_name = $row->get(B2tModules::MODULE_NAME);
			$this->_classname = $row->get(B2tModules::CLASSNAME);
			$this->_enabled = (bool) $row->get(B2tModules::ENABLED);
			$this->_shortname = $row->get(B2tModules::MODULE_NAME);
			$this->_showinconfig = (bool) $row->get(B2tModules::SHOW_IN_CONFIG);
			$this->_showinmenu = (bool) $row->get(B2tModules::SHOW_IN_MENU);
			$this->_showinusermenu = (bool) $row->get(B2tModules::SHOW_IN_USERMENU);
			$this->_version = $row->get(B2tModules::VERSION);
		}
		
		public function log($message, $level = 1)
		{
			BUGSlogging::log($message, $this->getName(), $level);
		}
		
		public function disable()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tModules::ENABLED, 0);
			B2DB::getTable('B2tModules')->doUpdateById($crit, $this->getID());
			$this->_enabled = false;
		}

		public function enable()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tModules::ENABLED, 1);
			B2DB::getTable('B2tModules')->doUpdateById($crit, $this->getID());
			$this->_enabled = true;
		}
		
		public function showInMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tModules::SHOW_IN_MENU, 1);
			B2DB::getTable('B2tModules')->doUpdateById($crit, $this->getID());
			$this->_showinmenu = true;
		}
		
		public function hideFromMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tModules::SHOW_IN_MENU, 0);
			B2DB::getTable('B2tModules')->doUpdateById($crit, $this->getID());
			$this->_showinmenu = false;
		}
		
		public function showInUserMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tModules::SHOW_IN_USERMENU, 1);
			B2DB::getTable('B2tModules')->doUpdateById($crit, $this->getID());
			$this->_showinusermenu = true;
		}

		public function hideFromUserMenu()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tModules::SHOW_IN_USERMENU, 0);
			B2DB::getTable('B2tModules')->doUpdateById($crit, $this->getID());
			$this->_showinusermenu = false;
		}
		
		protected function _uninstall()
		{
			$scope = BUGScontext::getScope()->getID();
			B2DB::getTable('B2tModules')->doDeleteById($this->getID());
			B2DB::getTable('B2tEnabledModuleListeners')->removeAllModuleListeners($this->getName(), $scope);
			BUGSsettings::deleteModuleSettings($module_name);
			BUGScontext::deleteModulePermissions($this->getName());
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
		 * @return BUGSmodule
		 */
		static protected function _install($identifier, $classname, $version, $show_in_config, $show_in_menu, $show_in_usermenu, $scope)
		{
  			if (!BUGScontext::getScope() instanceof BUGSscope) throw new Exception('No scope??');

			BUGSlogging::log('installing module' . $identifier);
			$module_id = B2DB::getTable('B2tModules')->installModule($identifier, $classname, $version, $show_in_config, $show_in_menu, $show_in_usermenu, $scope);
  			
			if (class_exists($classname))
			{
				$module = new $classname($module_id);
				if ($scope == BUGScontext::getScope()->getID())
				{
					BUGScontext::addModule($module, $identifier);
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
				B2DB::getTable('B2tEnabledModuleListeners')->removePermanentListener($module, $identifier, $this->getName());
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
		
		public function addAvailablePermission($permission_name, $description, $target = 0, $levels = 2)
		{
			$this->_availablepermissions[] = array('permission_name' => $permission_name, 'description' => $description, 'target' => $target, 'levels' => $levels);
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
		
		static public function cacheAllAccessPermissions()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tModulePermissions::SCOPE, BUGScontext::getScope()->getID());
			
			$resultset = B2DB::getTable('B2tModulePermissions')->doSelect($crit);
			while ($row = $resultset->getNextRow())
			{
				self::cacheAccessPermission($row->get(B2tModulePermissions::MODULE_NAME), $row->get(B2tModulePermissions::UID), $row->get(B2tModulePermissions::GID), $row->get(B2tModulePermissions::TID), 0, (bool) $row->get(B2tModulePermissions::ALLOWED));
			}
		}

		static public function cacheAccessPermission($module_name, $uid, $gid, $tid, $all, $allowed)
		{
			self::$_permissions[$module_name][] = array('uid' => $uid, 'gid' => $gid, 'tid' => $tid, 'all' => $all, 'allowed' => $allowed); 
		}

		public function setPermission($uid, $gid, $tid, $allowed, $scope = null)
		{
			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;
			B2DB::getTable('B2tModulePermissions')->deleteByModuleAndUIDandGIDandTIDandScope($this->getName(), $uid, $gid, $tid, $scope);
			B2DB::getTable('B2tModulePermissions')->setPermissionByModuleAndUIDandGIDandTIDandScope($this->getName(), $uid, $gid, $tid, $allowed, $scope);
			if ($scope == BUGScontext::getScope()->getID())
			{
				self::cacheAccessPermission($this->getName(), $uid, $gid, $tid, 0, $allowed);
			}
		}
		
		static public function rebuildAccessPermissionCache()
		{
			self::$_permissions = array();
			self::cacheAllAccessPermissions();
		}
		
		static public function getAccessPermissionList()
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
			if ($debug) BUGSlogging::log($this->_name);
			if ($debug)
			{
				foreach ($permissions[$this->getName()] as $aPerm)
				{
					BUGSlogging::log($aPerm);
				}
			}
			if ($all == null)
			{
				$uid = ($uid === null) ? BUGScontext::getUser()->getUID() : $uid;
				$tid = ($tid === null) ? BUGScontext::getUser()->getTeams() : $tid;
				if (!BUGScontext::getUser()->getGroup() instanceof BUGSgroup) return false;
				$gid = ($gid === null) ? BUGScontext::getUser()->getGroup()->getID() : $gid;
				
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
			if ($res = B2DB::getTable('B2tEnabledModuleListeners')->getAll($module_names))
			{
				while ($row = $res->getNextRow())
				{
					$module = BUGScontext::getModule($row->get(B2tEnabledModuleListeners::MODULE_NAME));
					if ($module->hasAccess() && $module->isEnabled())
					{
						$module->enableListener($row->get(B2tEnabledModuleListeners::MODULE), $row->get(B2tEnabledModuleListeners::IDENTIFIER));
					}
				}
			}
		}
		
		public function enableListener($module, $identifier, $scope = null)
		{
			if (array_key_exists($module . '_' . $identifier, $this->_listeners) && !$this->_listeners[$module . '_' . $identifier]['enabled'])
			{
				if ($scope === null || $scope == BUGScontext::getScope()->getID())
				{
					$listener = &$this->_listeners[$module . '_' . $identifier];
					BUGScontext::listenToTrigger($module, $identifier, array($this, $listener['callback_function']));
					$listener['enabled'] = true;
				}
				B2DB::getTable('B2tEnabledModuleListeners')->savePermanentListener($module, $identifier, $this->getName(), $scope);
			}
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
			$this->_module_config_description;
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
			return BUGSsettings::get($setting, $this->getName(), BUGScontext::getScope()->getID(), $uid);
		}
		
		public function saveSetting($setting, $value, $uid = 0, $scope = null)
		{
			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;
			return BUGSsettings::saveSetting($setting, $value, $this->getName(), $scope, $uid);
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
					BUGScontext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3], $route[4]);
				}
				else
				{
					BUGScontext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3]);
				}
				$this->log('done (adding route ' . $route[0] . ')');
			}
		}
		
		public function activate()
		{
			if ($this->_enabled == false || $this->hasAccess() == false)
			{
				bugs_showError('B2 Engine error - Not permitted', "You do not have access to this module. <br>You may have tried to access a link that is no longer in use.<br><br>If you think this is an error, please contact the administrator of this BUGS 2 instance.", true);
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
		
		static public function getAllModulePermissions($module, $uid, $tid, $gid)
		{
	
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tModulePermissions::MODULE_NAME, $module);
			//$sql = "select b2mp.allowed from bugs2_modulepermissions b2mp where b2mp.module_name = '$module'";
			switch (true)
			{
				case ($uid != 0):
					//$sql .= " and uid = $uid";
					$crit->addWhere(B2tModulePermissions::UID, $uid);
				case ($tid != 0):
					//$sql .= " and tid = $tid";
					$crit->addWhere(B2tModulePermissions::TID, $tid);
				case ($gid != 0):
					//$sql .= " and gid = $gid";
					$crit->addWhere(B2tModulePermissions::GID, $gid);
			}
			if (($uid + $tid + $gid) == 0)
			{
				//$sql .= " and uid = $uid and tid = $tid and gid = $gid";
				$crit->addWhere(B2tModulePermissions::UID, $uid);
				$crit->addWhere(B2tModulePermissions::TID, $tid);
				$crit->addWhere(B2tModulePermissions::GID, $gid);
			}
			
			//$sql .= " AND b2mp.scope = " . BUGScontext::getScope()->getID();
			$crit->addWhere(B2tModulePermissions::SCOPE, BUGScontext::getScope()->getID());
	
			//$res = b2db_sql_query($sql, B2DB::getDBlink());
	
			#print $sql;
	
			$permissions = array();
			$res = B2DB::getTable('B2tModulePermissions')->doSelect($crit);
	
			while ($row = $res->getNextRow())
			{
				$permissions[] = array('allowed' => $row->get(B2tModulePermissions::ALLOWED));
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
		
	}
