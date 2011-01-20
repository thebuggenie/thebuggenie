<?php

	/**
	 * Module class, extended by all thebuggenie modules
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	abstract class TBGModule extends TBGIdentifiableClass 
	{

		static protected $_b2dbtablename = 'TBGModulesTable';
		protected $_classname = '';
		protected $_description = '';
		protected $_enabled = false;
		protected $_longname = '';
		protected $_showinconfig = false;
		protected $_shortname = '';
		protected $_module_config_title = '';
		protected $_module_config_description = '';
		protected $_version = '';
		protected $_availablepermissions = array();
		protected $_listeners = array();
		protected $_settings = array();
		protected $_routes = array();
		protected $_has_account_settings = false;
		protected $_account_settings_name = null;
		protected $_account_settings_logo = null;
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
			$module_basepath = TBGContext::getIncludePath() . "modules/{$module_name}";
			$module_classpath = "{$module_basepath}/classes";
			TBGContext::addClasspath($module_classpath);
			
			$scope = TBGContext::getScope()->getID();
			$module_details = file_get_contents($module_basepath . '/class');

			if (strpos($module_details, '|') === false)
			{
				throw new Exception("Need to have module details in the form of ModuleName|version in the {$module_basepath}/class file");
			}

			$details = explode('|', $module_details);
			list($classname, $version) = $details;

  			if (!TBGContext::getScope() instanceof TBGScope) throw new Exception('No scope??');

			TBGLogging::log('installing module ' . $module_name);
			$module_id = B2DB::getTable('TBGModulesTable')->installModule($module_name, $classname, $version, $scope);

			if (!class_exists($classname))
			{
				throw new Exception('Can not load new instance of type ' . $classname . ', is not loaded');
			}

			$module = new $classname($module_id);
			$module->install($scope);

			return $module;
		}

		protected function _addAvailablePermissions() { }

		protected function _addAvailableListeners() { }

		protected function _addAvailableRoutes() { }

		abstract protected function _initialize(TBGI18n $i18n);

		protected function _install($scope) { }

		protected function _uninstall() { }

		/**
		 * Class constructor
		 */
		final public function _construct(B2DBRow $row, $foreign_key = null)
		{
			if ($this->_version != $row->get(TBGModulesTable::VERSION))
			{
				throw new Exception('This module must be upgraded to the latest version');
			}
		}

		protected function _loadFixtures($scope) { }

		final public function install($scope)
		{
			try
			{
				$this->_install($scope);
				$b2db_classpath = TBGContext::getIncludePath() . 'modules/' . $this->_name . '/classes/B2DB';

				if ($scope == TBGContext::getScope()->getID() && is_dir($b2db_classpath))
				{
					TBGContext::addClasspath($b2db_classpath);
					$b2db_classpath_handle = opendir($b2db_classpath);
					while ($table_class_file = readdir($b2db_classpath_handle))
					{
						if (($tablename = substr($table_class_file, 0, strpos($table_class_file, '.'))) != '')
						{
							B2DB::getTable($tablename)->create();
						}
					}
				}
				$this->_loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function log($message, $level = 1)
		{
			TBGLogging::log($message, $this->getName(), $level);
		}
		
		public static function disableModule($module_id)
		{
			TBGModulesTable::getTable()->disableModuleByID($module_id);
		}

		public static function removeModule($module_id)
		{
			TBGModulesTable::getTable()->removeModuleByID($module_id);
		}

		public final function isCore()
		{
			return in_array($this->_name, array('publish'));
		}

		public function disable()
		{
			self::disableModule($this->getID());
			$this->_enabled = false;
		}

		public function enable()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGModulesTable::ENABLED, 1);
			B2DB::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_enabled = true;
		}
		
		final public function uninstall()
		{
			if ($this->isCore())
			{
				throw new Exception('Cannot uninstall core modules');
			}
			$this->_uninstall();
			$scope = TBGContext::getScope()->getID();
			B2DB::getTable('TBGModulesTable')->doDeleteById($this->getID());
			B2DB::getTable('TBGEnabledModuleListenersTable')->removeAllModuleListeners($this->getName(), $scope);
			TBGSettings::deleteModuleSettings($this->getName());
			TBGContext::deleteModulePermissions($this->getName());
		}
		
		public function getClassname()
		{
			return $this->_classname;
		}
		
		public function disableListener($module, $identifier, $scope = null)
		{
			if (array_key_exists($module . '_' . $identifier, $this->_listeners))
			{
				if ($scope === null || $scope == TBGContext::getScope()->getID())
				{
					$this->_listeners[$module . '_' . $identifier]['enabled'] = false;
				}
			}
		}

		public function disableListenerSaved($module, $identifier, $scope = null)
		{
			$this->disableListener($module, $identifier, $scope);
			B2DB::getTable('TBGEnabledModuleListenersTable')->removePermanentListener($module, $identifier, $this->getName(), $scope);
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
			return $this->_id;
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

		public function addAvailablePermission($permission_name, $description, $target = 0)
		{
			$this->_availablepermissions[$permission_name] = array('description' => TBGContext::getI18n()->__($description), 'target_id' => $target);
		}
		
		public function getAvailablePermissions()
		{
			return $this->_availablepermissions;
		}

		public function getAvailableCommandLineCommands()
		{
			return array();
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

			if ($resultset)
			{
				while ($row = $resultset->getNextRow())
				{
					self::cacheAccessPermission($row->get(TBGModulePermissionsTable::MODULE_NAME), $row->get(TBGModulePermissionsTable::UID), $row->get(TBGModulePermissionsTable::GID), $row->get(TBGModulePermissionsTable::TID), 0, (bool) $row->get(TBGModulePermissionsTable::ALLOWED));
				}
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
			if (TBGContext::isCLI()) return true;

			return true;

			// TODO: module access permissions have not been implemented yet
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
				$uid = ($uid === null) ? TBGContext::getUser()->getID() : $uid;
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
					TBGEvent::listen($module, $identifier, array($this, $listener['callback_function']));
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
			return $this->_version;
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
		 * Returns whether the module is enabled
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

		public function addRoute($key, $url, $function, $params = array(), $csrf_enabled = false, $module_name = null)
		{
			$module_name = ($module_name !== null) ? $module_name : $this->getName();
			$this->_routes[] = array($key, $url, $module_name, $function, $params, $csrf_enabled);
		}

		final public function initialize()
		{
			$this->_initialize(TBGContext::getI18n());
			if ($this->isEnabled())
			{
				$this->_addAvailablePermissions();
				$this->_addAvailableListeners();
				$this->_addAvailableRoutes();
				$this->_loadRoutes();
			}
		}

		final protected function _loadRoutes()
		{
			foreach ($this->_routes as $route)
			{
				$this->log('adding route ' . $route[0]);
				call_user_func_array(array(TBGContext::getRouting(), 'addRoute'), $route);
				$this->log('done (adding route ' . $route[0] . ')');
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

		public function setAccountSettingsName($name)
		{
			$this->_account_settings_name = $name;
		}

		public function getAccountSettingsName()
		{
			return $this->_account_settings_name;
		}

		public function setAccountSettingsLogo($logo)
		{
			$this->_account_settings_logo = $logo;
		}

		public function getAccountSettingsLogo()
		{
			return $this->_account_settings_logo;
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

		public function postConfigSettings(TBGRequest $request)
		{

		}

		public function postAccountSettings(TBGRequest $request)
		{

		}

	}
