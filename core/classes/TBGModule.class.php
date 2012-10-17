<?php

	/**
	 * Module class, extended by all thebuggenie modules
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Module class, extended by all thebuggenie modules
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGModulesTable")
	 */
	abstract class TBGModule extends TBGIdentifiableScopedClass
	{

		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * @var string
		 * @Column(type="string", length=100)
		 */
		protected $_classname = '';

		/**
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_enabled = false;

		/**
		 * @var string
		 * @Column(type="string", length=10)
		 */
		protected $_version = '';

		protected $_longname = '';
		protected $_shortname = '';
		protected $_showinconfig = false;
		protected $_module_config_title = '';
		protected $_module_config_description = '';
		protected $_description = '';
		protected $_availablepermissions = array();
		protected $_settings = array();
		protected $_routes = array();

		protected $_has_account_settings = false;
		protected $_account_settings_name = null;
		protected $_account_settings_logo = null;
		protected $_has_config_settings = false;
		
		protected static $_permissions = array();
		
		const MODULE_NORMAL = 1;
		const MODULE_AUTH = 2;
		const MODULE_GRAPH = 3;

		/**
		 * Installs a module
		 * 
		 * @param string $module_name the module key
		 * @return boolean Whether the install succeeded or not
		 */
		public static function installModule($module_name, $scope = null)
		{
			$module_basepath = THEBUGGENIE_MODULES_PATH . $module_name;
			$module_classpath = $module_basepath . DS . "classes";

			if ($scope === null || $scope->getID() == TBGContext::getScope()->getID())
				TBGContext::addAutoloaderClassPath($module_classpath);
			
			$scope_id = ($scope) ? $scope->getID() : TBGContext::getScope()->getID();
			$module_details = file_get_contents($module_basepath . DS . 'class');

			if (mb_strpos($module_details, '|') === false)
				throw new Exception("Need to have module details in the form of ModuleName|version in the {$module_basepath}/class file");

			$details = explode('|', $module_details);
			list($classname, $version) = $details;

  			if (!TBGContext::getScope() instanceof TBGScope) throw new Exception('No scope??');

			if ($scope_id != TBGContext::getScope()->getID())
			{
				$prev_scope = TBGContext::getScope();
				TBGContext::setScope($scope);
			}
			TBGLogging::log('installing module ' . $module_name);
			try
			{
				$module_id = TBGModulesTable::getTable()->installModule($module_name, $classname, $version, $scope_id);

				if (!class_exists($classname))
				{
					throw new Exception('Can not load new instance of type ' . $classname . ', is not loaded');
				}

				$module = new $classname($module_id);
				$module->install($scope_id);
			}
			catch (\Exception $e)
			{
				if (isset($prev_scope))
				{
					TBGContext::setScope($prev_scope);
					TBGContext::clearPermissionsCache();
				}
				throw $e;
			}
			if (isset($prev_scope))
			{
				TBGContext::setScope($prev_scope);
				TBGContext::clearPermissionsCache();
			}

			return $module;
		}
		
		/**
		 * Upload a new module from ZIP archive
		 * 
		 * @param file $module_archive the module archive file (.zip)
		 * @return string the module name uploaded
		 */		
		public static function uploadModule($module_archive, $scope = null)
		{
			$zip = new ZipArchive();
			if ($zip->open($module_archive['tmp_name']) === false) {
				throw new Exception('Can not open module archive ' . $module_archive['name']);
			}
			else
			{
				$module_name = preg_replace('/(\w*)\.zip$/i', '$1', $module_archive['name']);
				$module_info = $zip->getFromName('module');
				$module_details = explode('|',$zip->getFromName('class'));
				list($module_classname, $module_version) = $module_details;
				$module_basepath = THEBUGGENIE_MODULES_PATH . $module_name;
				
				if (($module_info & $module_details) === false)
				{
					throw new Exception('Invalid module archive ' . $module_archive['name']);
				}
				
				$modules = TBGContext::getModules();
				foreach($modules as $module)
				{
					if ($module->getName() == $module_name || $module->getClassname() == $module_classname)
					{
						throw new Exception('Conflict with the module ' . $module->getLongName() . ' that is already installed with version ' . $module->getVersion());
					}
				}
				
				if (is_dir($module_basepath) === false)
				{
					if (mkdir($module_basepath) === false)
					{
						TBGLogging::log('Try to upload module archive ' . $module_archive['name'] . ': unable to create module directory ' . $module_basepath); 
						throw new Exception('Unable to create module directory ' . $module_basepath);
					}
					if ($zip->extractTo($module_basepath) === false)
					{
						TBGLogging::log('Try to upload module archive ' . $module_archive['name'] . ': unable to extract archive into ' . $module_basepath);
						throw new Exception('Unable to extract module into ' . $module_basepath);
					}
				}
				return $module_name;
			}
			return null;
		}		

		protected function _addAvailablePermissions() { }

		protected function _addListeners() { }

		protected function _addRoutes() { }

		abstract protected function _initialize();

		protected function _install($scope) { }

		protected function _uninstall() { }

		protected function _upgrade() { }

		/**
		 * Class constructor
		 */
		final public function _construct(\b2db\Row $row, $foreign_key = null)
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
				TBGContext::clearRoutingCache();
				TBGContext::clearPermissionsCache();
				$this->_install($scope);
				$b2db_classpath = THEBUGGENIE_MODULES_PATH . $this->_name . DS . 'classes' . DS . 'B2DB';

				if (TBGContext::getScope()->isDefault() && is_dir($b2db_classpath))
				{
					TBGContext::addAutoloaderClassPath($b2db_classpath);
					$b2db_classpath_handle = opendir($b2db_classpath);
					while ($table_class_file = readdir($b2db_classpath_handle))
					{
						if (($tablename = mb_substr($table_class_file, 0, mb_strpos($table_class_file, '.'))) != '')
						{
							\b2db\Core::getTable($tablename)->create();
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
			$crit = new \b2db\Criteria();
			$crit->addUpdate(TBGModulesTable::ENABLED, 1);
			\b2db\Core::getTable('TBGModulesTable')->doUpdateById($crit, $this->getID());
			$this->_enabled = true;
		}
		
		final public function upgrade()
		{
			TBGContext::clearRoutingCache();
			TBGContext::clearPermissionsCache();
			$this->_upgrade();
		}

		final public function uninstall($scope = null)
		{
			if ($this->isCore())
			{
				throw new Exception('Cannot uninstall core modules');
			}
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$this->_uninstall($scope);
			\b2db\Core::getTable('TBGModulesTable')->doDeleteById($this->getID());
			TBGSettings::deleteModuleSettings($this->getName(), $scope);
			TBGContext::deleteModulePermissions($this->getName(), $scope);
			TBGContext::clearRoutingCache();
			TBGContext::clearPermissionsCache();
		}
		
		public function getClassname()
		{
			return $this->_classname;
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		public function __call($func, $args)
		{
			throw new Exception('Trying to call function ' . $func . '() in module ' . $this->_shortname . ', but the function does not exist');
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
		
		public function setPermission($uid, $gid, $tid, $allowed, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			\b2db\Core::getTable('TBGModulePermissionsTable')->deleteByModuleAndUIDandGIDandTIDandScope($this->getName(), $uid, $gid, $tid, $scope);
			\b2db\Core::getTable('TBGModulePermissionsTable')->setPermissionByModuleAndUIDandGIDandTIDandScope($this->getName(), $uid, $gid, $tid, $allowed, $scope);
			if ($scope == TBGContext::getScope()->getID())
			{
				self::cacheAccessPermission($this->getName(), $uid, $gid, $tid, 0, $allowed);
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
		
		public function getType()
		{
			return self::MODULE_NORMAL;
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
		
		public function deleteSetting($setting, $uid = null, $scope = null)
		{
			return TBGSettings::deleteSetting($setting, $this->getName(), $scope, $uid);
		}
		
		/**
		 * Returns whether the module is enabled
		 *
		 * @return boolean
		 */
		public function isEnabled()
		{
			/* Outdated modules can not be used */
			if ($this->isOutdated())
			{
				return false;
			}
			return $this->_enabled;
		}
		
		/**
		 * Returns whether the module is out of date
		 * 
		 * @return boolean
		 */
		public function isOutdated()
		{
			if ($this->_version != $this->_module_version)
			{
				return true;
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
			$this->_initialize();
			if ($this->isEnabled())
			{
				$this->_addAvailablePermissions();
				$this->_addListeners();
				if (!TBGCache::has(TBGCache::KEY_POSTMODULES_ROUTES_CACHE, false))
				{
					$this->_addRoutes();
					$this->_loadRoutes();
				}
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
	
			$crit = new \b2db\Criteria();
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
	
			//$res = b2db_sql_query($sql, \b2db\Core::getDBlink());
	
			#print $sql;
	
			$permissions = array();
			$res = \b2db\Core::getTable('TBGModulePermissionsTable')->doSelect($crit);
	
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
			return TBGContext::geti18n()->__($this->_account_settings_name);
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
			/* If the module is outdated, we may not access its settings */
			if ($this->isOutdated()): return false; endif;
			
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

		/**
		 * Return the items name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

	}
