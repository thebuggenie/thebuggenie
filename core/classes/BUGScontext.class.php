<?php

	/**
	 * The core class of the B2 engine
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * The core class of the B2 engine
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	final class BUGScontext
	{
		static protected $_B2DBObject = null;
		
		/**
		 * The current user
		 *
		 * @var BUGSuser
		 */
		static protected $_user = null;
		
		/**
		 * List of modules 
		 * 
		 * @var array
		 */
		static protected $_modules = array();
		
		/**
		 * List of module permissions
		 * 
		 * @var array
		 */
		static protected $_modulepermissions = array();
		
		/**
		 * List of permissions
		 *  
		 * @var array
		 */
		static protected $_permissions = array();
		
		/**
		 * List of available permissions
		 * 
		 * @var array
		 */
		static protected $_available_permissions = null;
		
		/**
		 * The include path
		 * 
		 * @var string
		 */
		static protected $_includepath = null;
		
		/**
		 * The path to thebuggenie relative from url server root
		 * 
		 * @var string
		 */
		static protected $_tbgpath = null;
		
		/**
		 * Stripped version of the $_tbgpath
		 * 
		 * @see $_tbgpath
		 * 
		 * @var string
		 */
		static protected $_stripped_tbgpath = null;
		
		/**
		 * Whether we're in installmode or not
		 * 
		 * @var boolean
		 */
		static protected $_installmode = false;
		
		/**
		 * The i18n object
		 *
		 * @var BUGSi18n
		 */
		static protected $_i18n = null;
		
		/**
		 * The request object
		 * 
		 * @var BUGSrequest
		 */
		static protected $_request = null;
		
		/**
		 * The current scope object
		 *
		 * @var BUGSscope
		 */
		static protected $_scope = null;
		
		/**
		 * Used to determine when the b2 engine started loading
		 * 
		 * @var integer
		 */
		static protected $_loadstart = null;
		
		/**
		 * Used for timing purposes
		 * 
		 * @var integer
		 */
		static protected $_loadend = null;
		
		/**
		 * List of classpaths
		 * 
		 * @var array
		 */
		static protected $_classpaths = array();
		
		/**
		 * List of registered trigger listeners
		 * 
		 * @var string
		 */
		static protected $_registeredlisteners = array();
		
		/**
		 * List of loaded libraries
		 * 
		 * @var string
		 */
		static protected $_libs = array();
		
		/**
		 * The routing object
		 * 
		 * @var BUGSrouting
		 */
		static protected $_routing = null;
		
		/**
		 * Returns the Database object
		 *
		 * @return B2DBObject
		 */
		public static function getB2DBObject()
		{
			return B2DBObject;
		}
		
		/**
		 * Returns whether or not we're in install mode
		 * 
		 * @return boolean
		 */
		public static function isInstallmode()
		{
			return self::$_installmode;
		}
		
		/**
		 * Add a path to the list of searched paths in the autoloader
		 * Class files must contain one class with the same name as the class
		 * in the form of Classname.class.php
		 * 
		 * @param string $classpath The path where the class files are
		 * 
		 * @return null
		 */
		public static function addClasspath($classpath)
		{
			if ($classpath[strlen($classpath) - 1] != DIRECTORY_SEPARATOR)
			{
				BUGSlogging::log('Invalid classpath, appending directory separator', 'main', BUGSlogging::LEVEL_WARNING);
				$classpath .= DIRECTORY_SEPARATOR;
			}
			if (file_exists($classpath . 'generics.class.php') && !isset(self::$_classpaths[$classpath . 'generics.class.php']))
			{
				require_once $classpath . 'generics.class.php';
			}
			if (file_exists($classpath . 'actions.class.php'))
			{
				require_once $classpath . 'actions.class.php';
			}
			if (file_exists($classpath . 'actioncomponents.class.php'))
			{
				require_once $classpath . 'actioncomponents.class.php';
			}
			if ($dir_handle = opendir($classpath))
			{
				while (false !== ($file = readdir($dir_handle)))
				{
					if ($offset = strpos($file, '.class.php'))
					{
						self::$_classpaths[substr($file, 0, $offset)] = $classpath . $file;
					}
				}
			}
			else
			{
				throw new Exception($classpath . ' is not a valid directory');
			}
		}
		
		/**
		 * Returns the classpaths that has been registered to the autoloader
		 *
		 * @return array
		 */
		public static function getClasspaths()
		{
			return self::$_classpaths;
		}
		
		/**
		 * Returns the routing object
		 * 
		 * @return BUGSrouting
		 */
		public static function getRouting()
		{
			if (!self::$_routing)
			{
				self::$_routing = new BUGSrouting();
			}
			return self::$_routing;
		}
		
		/**
		 * Returns the include path (used for including files)
		 * 
		 * @return string
		 */
		public static function getIncludePath()
		{
			return self::$_includepath;
		}
		
		/**
		 * Get when we last loaded the engine
		 * 
		 * @return integer
		 */
		public static function getLastLoadedAt()
		{
			return $_SESSION['b2lastreloadtime'];
		}
		
		/**
		 * Set when we last loaded the engine
		 */
		public static function setLoadedAt()
		{
			$_SESSION['b2lastreloadtime'] = $_SERVER["REQUEST_TIME"];
		}
		
		/**
		 * Set the include path to a specific path
		 * 
		 * @param string $path the path to change to
		 */
		public static function setIncludePath($path = null)
		{
			if ($path !== null)
			{
				self::$_includepath = $path;
			}
			else
			{
				self::$_includepath = BUGSsettings::get('local_path', 'core');
			}
		}

		/**
		 * Get the subdirectory part of the url
		 * 
		 * @return string
		 */
		public static function getTBGPath()
		{
			if (self::$_tbgpath === null)
			{
				self::_setTBGPath();
			}
			return self::$_tbgpath;
		}
		
		/**
		 * Get the subdirectory part of the url, stripped
		 * 
		 * @return string
		 */
		public static function getStrippedTBGPath()
		{
			if (self::$_stripped_tbgpath === null)
			{
				self::$_stripped_tbgpath = substr(self::getTBGPath(), 0, strlen(self::getTBGPath()) - 1);
			}
			return self::$_stripped_tbgpath;
		}

		/**
		 * Set the subdirectory part of the url, from the url
		 */
		protected static function _setTBGPath()
		{
			self::$_tbgpath = dirname($_SERVER['PHP_SELF']);
			if (self::$_tbgpath[strlen(self::$_tbgpath) - 1] != '/') self::$_tbgpath .= '/';
		}
		
		/**
		 * Set that we've started loading
		 * 
		 * @param integer $when
		 */
		public static function setLoadStart($when)
		{
			self::$_loadstart = $when;
		}
		
		/**
		 * Manually ping the loader
		 */
		public static function ping()
		{
			$endtime = microtime();
			$endtime = explode(' ', $endtime);
			$endtime = $endtime[1] + $endtime[0];
			self::$_loadend = $endtime;
		}

		/**
		 * Get the time from when we started loading
		 * 
		 * @param integer $precision
		 * @return integer
		 */
		public static function getLoadtime($precision = 5)
		{
			self::ping();
			return round((self::$_loadend - self::$_loadstart), $precision);
			//return round(round((self::$_loadend - self::$_loadstart), $precision) * 1000, 0);
		}
		
		/**
		 * Initialize the context
		 * 
		 * @return null
		 */
		static public function initialize()
		{
			try
			{
				BUGSlogging::log('Loading request');
				self::$_request = new BUGSrequest();
				BUGSlogging::log('...done');
				if (!is_readable(BUGS2_INCLUDE_PATH . 'installed') && !isset($argc))
				{
					self::$_installmode = true;
					return true;
				}
				elseif (!class_exists('B2DB'))
				{
					throw new Exception('The Bug Genie seems installed, but B2DB isn\'t configured. This usually indicates an error with the installation. Try removing the file ' . BUGS2_INCLUDE_PATH . 'installed and try again.');
				}
				BUGSlogging::log("Setting current scope");
				self::setScope();
				BUGSlogging::log("...done");
				
				BUGSlogging::log("Loading settings");
				BUGSsettings::loadSettings();
				BUGSlogging::log("...done");
			
				$load_modules = true;
				BUGSlogging::log('Loading user');
				try
				{
					BUGSlogging::log('is this logout?');
					if (self::getRequest()->getParameter('logout'))
					{
						BUGSlogging::log('yes');
						self::logout();
					}
					else
					{
						BUGSlogging::log('no');
						BUGSlogging::log('sets up user object');
						self::loadUser();
						BUGSlogging::log('loaded');
						BUGSlogging::log('caches permissions');
						self::cacheAllPermissions();
						BUGSlogging::log('...cached');
					}
				}
				catch (Exception $e)
				{
					BUGSlogging::log("Something happened while setting up user: ". $e->getMessage(), 'main', BUGSlogging::LEVEL_WARNING);
					if (self::getRouting()->getCurrentRouteModule() != 'main' || self::getRouting()->getCurrentRouteAction() != 'login')
					{
						BUGSaction::forward(self::getRouting()->generate('login'));
					}
					else
					{
						self::$_user = new BUGSuser();
						$load_modules = false;
					}
				}
				BUGSlogging::log('...done');
				BUGSlogging::log('Loading i18n strings');
				if (!$cached_i18n = BUGScache::get('i18n_'.BUGSsettings::get('language')))
				{
					BUGSlogging::log('Loading strings from file');
					self::$_i18n = new BUGSi18n(BUGSsettings::get('language'));
					self::$_i18n->initialize();
					BUGScache::add('i18n_'.BUGSsettings::get('language'), self::$_i18n);
				}
				else
				{
					BUGSlogging::log('Using cached i18n strings');
					self::$_i18n = $cached_i18n;
				}
				BUGSlogging::log('...done');
				BUGSlogging::log('Loading modules');
				if ($load_modules)
				{
					self::loadModules();
				}
				BUGSlogging::log('...done');
				
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns the request object
		 * 
		 * @return BUGSrequest
		 */
		public static function getRequest()
		{
			return self::$_request;
		}
		
		/**
		 * Reinitialize the i18n object, used only when changing the language in the middle of something
		 * 
		 * @param string $language The language code to change to
		 */
		static public function reinitializeI18n($language = null) 
		{
			if (!$language)
			{
				self::$_i18n = new BUGSi18n(BUGSsettings::get('language'));
			}
			else
			{
				self::$_i18n = new BUGSi18n($language);
			}
		}
		
		/**
		 * Get the i18n object
		 *
		 * @return BUGSi18n
		 */
		static public function getI18n()
		{
			if (!self::$_i18n instanceof BUGSi18n)
			{
				self::reinitializeI18n('en_US');
			}
			return self::$_i18n;
		}
		
		/**
		 * Get available themes
		 * 
		 * @return array
		 */
		static public function getThemes()
		{
			$theme_path = BUGSsettings::get('local_path') . 'themes/';
			$theme_path_handle = opendir($theme_path);
			$themes = array();
			while ($theme = readdir($theme_path_handle))
			{
				if (strstr($theme, '.') == '') 
				{ 
					$themes[] = $theme; 
				}
			}
			return $themes;
		}
		
		/**
		 * Load the user object into the user property
		 * 
		 * @return BUGSuser
		 */
		public static function loadUser()
		{
			try
			{
				self::$_user = BUGSuser::loginCheck();
			}
			catch (Exception $e)
			{
				throw $e;
			}
			return self::$_user;
		}
		
		/**
		 * Returns the user object
		 *
		 * @return BUGSuser
		 */
		public static function getUser()
		{
			return self::$_user;
		}
		
		/**
		 * Loads and initializes all modules
		 */
		public static function loadModules()
		{
			self::$_modules = array();
			$modules = array();
			
			if ($module_paths = BUGScache::get('module_paths'))
			{
				BUGSlogging::log('using cached modules');
				foreach ($module_paths as $moduleClassPath)
				{
					try
					{
						self::addClasspath($moduleClassPath);
					}
					catch (Exception $e) { } // ignore "dir not exists" errors
				}
				$modules = BUGScache::get('modules');
				foreach ($modules as $module)
				{
					self::$_modules[$module] = unserialize(BUGScache::get("module_{$module}"));
				}
				BUGSlogging::log('done');
			}
			else
			{
				BUGSlogging::log('getting modules from database');
				$module_paths = array();
				
				if ($res = B2DB::getTable('B2tModules')->getAll())
				{
					while ($moduleRow = $res->getNextRow())
					{
						$module_name = $moduleRow->get(B2tModules::MODULE_NAME);
						$modules[$module_name] = $moduleRow;
						$moduleClassPath = self::getIncludePath() . "modules/{$module_name}/classes/";
						try
						{
							self::addClasspath($moduleClassPath);
							$module_paths[] = $moduleClassPath;
							self::addClasspath($moduleClassPath . 'B2DB/');
							$module_paths[] = $moduleClassPath . 'B2DB/';
						}
						catch (Exception $e) { } // ignore "dir not exists" errors
					}
				}
				BUGSlogging::log('done (getting modules from database)');
				BUGScache::add('module_paths', $module_paths);
				BUGScache::add('modules', array_keys($modules));
				BUGSlogging::log('setting up module objects');
				foreach ($modules as $module_name => $moduleRow)
				{
					$classname = ($moduleRow->get(B2tModules::CLASSNAME) != '') ? $moduleRow->get(B2tModules::CLASSNAME) : 'BUGSmodule';
					if (class_exists($classname))
					{
						if ($classname != 'BUGSmodule')
						{
							self::getI18n()->loadModuleStrings($module_name);
							self::$_modules[$module_name] = new $classname($moduleRow->get(B2tModules::ID), $moduleRow);
							BUGScache::add("module_{$module_name}", serialize(self::$_modules[$module_name]));
						}
						else
						{
							throw new Exception('Cannot load module "' . $module_name . '" as class BUGSmodule - modules should extend the BUGSmodule class with their own class.');
						}
					}
					else
					{
						throw new Exception('Cannot load module "' . $module_name . '" as class "' . $classname . '", the class is not defined in the classpaths.');
					}
				}
				BUGSlogging::log('done (setting up module objects)');
			
				BUGSlogging::log('caching module access permissions');
				BUGSmodule::cacheAllAccessPermissions();
				BUGSlogging::log('done (caching module access permissions)');
			}
			
			BUGSlogging::log('initializing modules');
			if (!empty(self::$_modules))
			{
				BUGSmodule::populateSections(array_keys(self::$_modules));
				foreach (self::$_modules as $module_name => $module)
				{
					if ($module->isEnabled())
					{
						BUGSlogging::log("initializing {$module_name}");
						$module->initialize();
						BUGSlogging::log("done (initializing {$module_name})");
					}
				}
				BUGSlogging::log('done (initializing modules)');
			}
			else
			{
				BUGSlogging::log('no modules found');
			}
		}
		
		/**
		 * Adds a module to the module list
		 *
		 * @param BUGSmodule $module
		 */
		public static function addModule($module, $module_name)
		{
			self::$_modules[$module_name] = $module;
		}
		
		/**
		 * Returns an array of modules
		 *
		 * @return array
		 */
		public static function getModules()
		{
			return self::$_modules;
		}
		
		/**
		 * Returns a specified module
		 *
		 * @param string $module_name
		 * 
		 * @return BUGSmodule
		 */
		public static function getModule($module_name)
		{
			if (!self::isModuleLoaded($module_name))
			{
				throw new Exception('This module is not loaded');
			}
			else
			{
				return self::$_modules[$module_name];	
			}
		}
		
		/**
		 * Whether or not a module is loaded
		 *
		 * @param string $module_name
		 * 
		 * @return boolean
		 */
		public static function isModuleLoaded($module_name)
		{
			if (isset(self::$_modules[$module_name]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Register a listener for a spesified trigger
		 * 
		 * @param string $module The module for which the trigger is active
		 * @param string $identifier The trigger identifier
		 * @param string $callback_function Which function to call
		 */
		public static function listenToTrigger($module, $identifier, $callback_function)
		{
			self::$_registeredlisteners[$module][$identifier][] = $callback_function;
		}

		/**
		 * Invoke a trigger
		 * 
		 * @param string $module The module for which the trigger is active
		 * @param string $identifier The trigger identifier
		 * @param array $params Parameters to pass to the registered listeners
		 * 
		 * @return unknown_type
		 */
		public static function trigger($module, $identifier, $params = array())
		{
			BUGSlogging::log("Triggering $module - $identifier");
			if (isset(self::$_registeredlisteners[$module][$identifier]))
			{
				foreach (self::$_registeredlisteners[$module][$identifier] as $trigger)
				{
					try
					{
						call_user_func($trigger, $params);
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
			}
			BUGSlogging::log("done (Triggering $module - $identifier)");
		}
		
		/**
		 * Whether or not there are any listeners to a specific trigger
		 * 
		 * @param string $module The module for which the trigger is active
		 * @param string $identifier The trigger identifier
		 * 
		 * @return boolean
		 */
		public static function isHookedInto($module, $identifier)
		{
			if (isset(self::$_registeredlisteners[$module]) && isset(self::$_registeredlisteners[$module][$identifier]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		//TODO: reimplement
		/**
		 * Return all permissions available
		 * @param string $type
		 * @param $uid
		 * @param $tid
		 * @param $gid
		 * @param $target_id
		 * @param $all
		 * @return unknown_type
		 */
		public static function getAllPermissions($type, $uid, $tid, $gid, $target_id = null, $all = false)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tPermissions::SCOPE, self::getScope()->getID());
			$crit->addWhere(B2tPermissions::PERMISSION_TYPE, $type);
			//$sql = "select b2pt.permission_type as p_type, b2pt.target_id, b2pt.allowed from bugs2_permissions b2pt where b2pt.permission_type = '$type'";
			if (($uid + $tid + $gid) == 0 && !$all)
			{
				$crit->addWhere(B2tPermissions::UID, $uid);
				$crit->addWhere(B2tPermissions::TID, $tid);
				$crit->addWhere(B2tPermissions::GID, $gid);
				//$sql .= " and uid = $uid and tid = $tid and gid = $gid";
			}
			else
			{
				switch (true)
				{
					case ($uid != 0):
						$crit->addWhere(B2tPermissions::UID, $uid);
						//$sql .= " and uid = $uid";
					case ($tid != 0):
						$crit->addWhere(B2tPermissions::TID, $tid);
						//$sql .= " and tid = $tid";
					case ($gid != 0):
						$crit->addWhere(B2tPermissions::GID, $gid);
						//$sql .= " and gid = $gid";
				}
			}
			if ($target_id != null)
			{
				$crit->addWhere(B2tPermissions::TARGET_ID, $target_id);
				//$sql .= " and b2pt.target_id = $target_id";
			}
	
			//$res = b2db_sql_query($sql, B2DB::getDBlink());
			$res = B2DB::getTable('B2tPermissions')->doSelect($crit);
			//echo $res->printSQL();
	
			#print $sql;
	
			$permissions = array();
	
			while ($row = $res->getNextRow())
			{
				$permissions[] = array('p_type' => $row->get(B2tPermissions::PERMISSION_TYPE), 'target_id' => $row->get(B2tPermissions::TARGET_ID), 'allowed' => $row->get(B2tPermissions::ALLOWED), 'uid' => $row->get(B2tPermissions::UID), 'gid' => $row->get(B2tPermissions::GID), 'tid' => $row->get(B2tPermissions::TID), 'id' => $row->get(B2tPermissions::ID));
			}
	
			return $permissions;
		}
		
		/**
		 * Cache all permissions
		 */
		public static function cacheAllPermissions()
		{
			self::$_permissions = array();
			
			BUGSlogging::log('starting to cache access permissions');
			if ($res = B2DB::getTable('B2tPermissions')->getAll())
			{
				while ($row = $res->getNextRow())
				{
					self::$_permissions[] = array('module_name' => $row->get(B2tPermissions::MODULE), 'permission_type' => $row->get(B2tPermissions::PERMISSION_TYPE), 'target_id' => $row->get(B2tPermissions::TARGET_ID), 'uid' => $row->get(B2tPermissions::UID), 'gid' => $row->get(B2tPermissions::GID), 'tid' => $row->get(B2tPermissions::TID), 'allowed' => ($row->get(B2tPermissions::ALLOWED) == 0) ? false : true);
				}
			}
			BUGSlogging::log('done (starting to cache access permissions)');
		}

		/**
		 * Cache a permission
		 * 
		 * @param array $perm_cache
		 */
		public static function cachePermission($perm_cache)
		{
			self::$_permissions[] = $perm_cache; 
		}

		/**
		 * Remove a saved permission
		 * 
		 * @param string $permission_type The permission type 
		 * @param mixed $target_id The target id
		 * @param string $module The name of the module for which the permission is valid
		 * @param integer $uid The user id for which the permission is valid, 0 for none
		 * @param integer $gid The group id for which the permission is valid, 0 for none
		 * @param integer $tid The team id for which the permission is valid, 0 for none
		 * @param boolean $recache Whether to recache after clearing this permission
		 * @param integer $scope A specified scope if not the default
		 */
		public static function removePermission($permission_type, $target_id, $module, $uid, $gid, $tid, $recache = true, $scope = null)
		{
			if ($scope === null) $scope = self::getScope()->getID();
			
			B2DB::getTable('B2tPermissions')->removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope);
			
			if ($recache) self::cacheAllPermissions();
		}

		/**
		 * Save a permission setting
		 * 
		 * @param string $permission_type The permission type 
		 * @param mixed $target_id The target id
		 * @param string $module The name of the module for which the permission is valid
		 * @param integer $uid The user id for which the permission is valid, 0 for none
		 * @param integer $gid The group id for which the permission is valid, 0 for none
		 * @param integer $tid The team id for which the permission is valid, 0 for none
		 * @param boolean allowed Allowed or not
		 * @param integer $scope[optional] A specified scope if not the default
		 */
		public static function setPermission($permission_type, $target_id, $module, $uid, $gid, $tid, $allowed, $scope = null)
		{
			if ($scope === null) $scope = self::getScope()->getID();
			
			self::removePermission($permission_type, $target_id, $module, $uid, $gid, $tid, false, $scope);
			B2DB::getTable('B2tPermissions')->setPermission($uid, $gid, $tid, $allowed, $module, $permission_type, $target_id, $scope);
			
			self::cacheAllPermissions();
		}
	
		/**
		 * Check to see if a specified user/group/team has access
		 * 
		 * @param string $permission_type The permission type 
		 * @param mixed $target_id The target id
		 * @param string $module_name The name of the module for which the permission is valid
		 * @param integer $uid The user id for which the permission is valid, 0 for none
		 * @param integer $gid The group id for which the permission is valid, 0 for none
		 * @param integer $tid The team id for which the permission is valid, 0 for none
		 * @param $all
		 * @return unknown_type
		 */
		public static function checkPermission($permission_type, $target_id = 0, $module_name = 'core', $uid = null, $gid = null, $tid = null, $all = null)
		{
			$debug = false;
			if ($all === null || $all == 0)
			{
				$uid = ($uid === null) ? self::getUser()->getUID() : $uid;
				$tid = ($tid === null) ? self::getUser()->getTeams() : $tid;
				if (self::getUser()->getGroup() instanceof BUGSgroup)
				{
					$gid = ($gid === null) ? self::getUser()->getGroup()->getID() : $gid;
				}
				
				echo ($debug) ? 'checking ' . $permission_type . ', target: ' . $target_id : '';
				foreach (self::$_permissions as $aPerm)
				{
					if ($aPerm['permission_type'] == $permission_type && $aPerm['target_id'] == $target_id && $aPerm['module_name'] == $module_name)
					{
						echo ($debug) ? '<br>' : '';
						echo ($debug) ? 'hit: ' . $permission_type . ', u:' . $uid . '?' . $aPerm['uid'] . ', g:' . $gid . '?' . $aPerm['gid'] . ', t:' . $tid . '?' . $aPerm['tid'] : '';
						if ($aPerm['uid'] == $uid && $uid != 0)
						{
							echo ($debug) ? 'returnfromuid: ' . var_dump($aPerm['allowed']) : '';
							echo ($debug) ? '<br>' : '';
							return $aPerm['allowed'];
						}
					}
				}
				
				foreach (self::$_permissions as $aPerm)
				{
					if ($aPerm['permission_type'] == $permission_type && $aPerm['target_id'] == $target_id && $aPerm['module_name'] == $module_name)
					{
						echo ($debug) ? '<br>' : '';
						echo ($debug) ? 'hit: ' . $permission_type . ', u:' . $uid . '?' . $aPerm['uid'] . ', g:' . $gid . '?' . $aPerm['gid'] . ', t:' . $tid . '?' . $aPerm['tid'] : '';
						if (((is_array($tid) && in_array($aPerm['tid'], $tid)) || (is_array($tid) == false && $aPerm['tid'] == $tid)) && $tid != 0)
						{
							echo ($debug) ? 'returnfromtid: ' . var_dump($aPerm['allowed']) : '';
							echo ($debug) ? '<br>' : '';
							return $aPerm['allowed'];
						}
					}
				}
	
				foreach (self::$_permissions as $aPerm)
				{
					if ($aPerm['permission_type'] == $permission_type && $aPerm['target_id'] == $target_id && $aPerm['module_name'] == $module_name)
					{
						echo ($debug) ? '<br>' : '';
						echo ($debug) ? 'hit: ' . $permission_type . ', u:' . $uid . '?' . $aPerm['uid'] . ', g:' . $gid . '?' . $aPerm['gid'] . ', t:' . $tid . '?' . $aPerm['tid'] : '';
						if ($aPerm['gid'] == $gid && $gid != 0)
						{
							echo ($debug) ? 'returnfromgid: ' . var_dump($aPerm['allowed']) : '';
							echo ($debug) ? '<br>' : '';
							return $aPerm['allowed'];
						}
					}
				}
			}
			
			foreach (self::$_permissions as $aPerm)
			{
				if ($aPerm['permission_type'] == $permission_type && $aPerm['target_id'] == $target_id && $aPerm['module_name'] == $module_name && ($aPerm['uid'] + $aPerm['gid'] + $aPerm['tid']) == 0)
				{
					echo ($debug) ? '<br>' : '';
					echo ($debug) ? 'checking ' . $permission_type . ', target: ' . $target_id : '';
					echo ($debug) ? '<br>' : '';
					echo ($debug) ? 'returnfromfail: ' . $aPerm['allowed'] : '';
					echo ($debug) ? '<br>' : '';
					return $aPerm['allowed'];
				}
			}
			echo ($debug) ? 'checking ' . $permission_type . ', target: ' . $target_id : '';
			echo ($debug) ? '<br>' : '';
			echo ($debug) ? 'returnfromnothing: ' . false : '';
			return false;
		}
		
		protected static function _cacheAvailablePermissions()
		{
			if (self::$_available_permissions === null)
			{
				self::$_available_permissions = array();
				$res = B2DB::getTable('B2tPermissionsList')->getAll();
				while ($row = $res->getNextRow())
				{
					self::$_available_permissions[$row->get(B2tPermissionsList::APPLIES_TO)][] = array('permission_name' => $row->get(B2tPermissionsList::PERMISSION_NAME), 'description' => $row->get(B2tPermissionsList::DESCRIPTION), 'levels' => $row->get(B2tPermissionsList::LEVELS));
				}
			}			
		}
		
		/**
		 * Returns all permissions available for a specific identifier
		 *  
		 * @param string $applies_to The identifier
		 * 
		 * @return array
		 */
		public static function getAvailablePermissions($applies_to)
		{
			self::_cacheAvailablePermissions();
			if (array_key_exists($applies_to, self::$_available_permissions))
			{
				return self::$_available_permissions[$applies_to];
			}
			else
			{
				return array();
			}
		}
		
		/**
		 * Log out the current user (does not work when auth method is set to http)
		 */
		public static function logout()
		{
			setcookie('b2_username', '', $_SERVER["REQUEST_TIME"] - 36000);
			setcookie('b2_password', '', $_SERVER["REQUEST_TIME"] - 36000);
			setcookie("THEBUGGENIE", '', $_SERVER["REQUEST_TIME"] - 36000);
			session_regenerate_id(true);
			session_destroy();
		}

		/**
		 * Find and set the current scope
		 * 
		 * @param integer $scope Specify a scope to set for this request
		 */
		public static function setScope($scope = null)
		{
			if ($scope !== null)
			{
				BUGSlogging::log("\t\tSetting scope from function parameter");
				$theScope = BUGSfactory::scopeLab((int) $scope);
				//$_SESSION['b2_scope'] = (int) $scope;
				self::$_scope = $theScope;
				BUGSlogging::log("\t\t...done");
				return true;
			}
	
			try
			{
				$hostprefix = (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
				BUGSlogging::log("\t\tChecking if scope can be set from hostname (".$hostprefix.$_SERVER['HTTP_HOST'].")");
				$row = B2DB::getTable('B2tScopes')->getByHostname($hostprefix . $_SERVER['HTTP_HOST']);
				if ($row instanceof B2DBRow)
				{
					BUGSlogging::log("\t\tIt could");
					BUGSlogging::log("\t\tSetting scope from hostname");
					$theScope = BUGSfactory::scopeLab($row->get(B2tScopes::ID), $row);
					//$_SESSION['b2_scope'] = $theScope->getID();
					self::$_scope = $theScope;
					BUGSlogging::log("\t\t...done");
					return true;
				}
				BUGSlogging::log("\t\tIt couldn't");
			}
			catch (Exception $e) { }
	
			/*if (isset($_REQUEST['scope']))
			{
				BUGSlogging::log("\t\tSetting scope from request parameter");
				try
				{
					$theScope = BUGSfactory::scopeLab($_REQUEST['scope']);
					$_SESSION['b2_scope'] = $theScope->getID();
					self::$_scope = $theScope;
					BUGSlogging::log("\t\t...done");
					return true;
				}
				catch (Exception $e) { die($e); }
			}
			
			if (isset($_REQUEST['issue_no']))
			{
				BUGSlogging::log("\t\tSetting scope from requested issue");
				try
				{
					$theIssue = new BUGSissue(BUGSissue::getIssueIDfromLink($_REQUEST['issue_no']));
					$_SESSION['b2_scope'] = $theIssue->getScope()->getID();
					self::$_scope = $theIssue->getScope();
					BUGSlogging::log("\t\t...done");
					return true;
				}
				catch (Exception $e) 
				{ 
					BUGSlogging::log("\t\tCouldn't find a valid issue");
				}
			}*/
	
			/*if (isset($_SESSION['b2_scope']) && $_SESSION['b2_scope'] != "")
			{
				BUGSlogging::log("\t\tSetting scope from session");
				try
				{
					$theScope = BUGSfactory::scopeLab($_SESSION['b2_scope']);
					$_SESSION['b2_scope'] = $theScope->getID();
					self::$_scope = $theScope;
					BUGSlogging::log("\t\t...done");
					return true;
				}
				catch (Exception $e) 
				{ 
					BUGSlogging::log("\t\tOops, couldn't set scope from session");
				}
			}*/

			BUGSlogging::log("\t\tMust ... get ... default ... scope");
			try
			{
				BUGSlogging::log("\t\tSetting scope to default scope");
				$theScope = BUGSsettings::getDefaultScope();
				if (!$theScope instanceof BUGSscope)
				{
					throw new Exception('');
				}
				$_SESSION['b2_scope'] = $theScope->getID();
				self::$_scope = $theScope;
				BUGSlogging::log("\t\t...done");
				return true;
			}
			catch (Exception $e)
			{
				BUGSlogging::log("\t\tCouldn't find a default scope");
				echo $e->getMessage();
				throw new Exception('Could not load default scope. This is usually because the fixtures has not been added correctly.');
			}
		}

		/**
		 * Returns current scope
		 *
		 * @return BUGSscope
		 */
		public static function getScope()
		{
			return self::$_scope;
		}
		
		/**
		 * Set a message to be retrieved in the next request
		 * 
		 * @param string $message The message
		 */
		public static function setMessage($message)
		{
			$_SESSION['tbg_message'] = $message;
		}

		/**
		 * Whether or not there is a message in the next request
		 * 
		 * @return boolean
		 */
		public static function hasMessage()
		{
			return array_key_exists('tbg_message', $_SESSION);
		}
		
		/**
		 * Retrieve the message
		 * 
		 * @return string
		 */
		public static function getMessage()
		{
			return (self::hasMessage()) ? $_SESSION['tbg_message'] : false;
		}
		
		/**
		 * Clear the message
		 */
		public static function clearMessage()
		{
			unset($_SESSION['tbg_message']);
		}

		/**
		 * Retrieve the message and clear it
		 * 
		 * @return string
		 */
		public static function getMessageAndClear()
		{
			$message = self::getMessage();
			self::clearMessage();
			return $message;
		}

		/**
		 * Loads a library
		 * 
		 * @param string $lib_name The name of the library
		 */
		public static function loadLibrary($lib_name)
		{
			if (!array_key_exists($lib_name, self::$_libs))
			{
				$lib_file_name = "{$lib_name}.inc.php";
				if (file_exists(self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/' . $lib_file_name))
				{
					require self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/' . $lib_file_name;
					self::$_libs[$lib_name] = self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/' . $lib_file_name;
				}
				elseif (file_exists(self::getIncludePath() . "core/lib/" . $lib_file_name))
				{
					require self::getIncludePath() . "core/lib/" . $lib_file_name;
					self::$_libs[$lib_name] = self::getIncludePath() . "core/lib/" . $lib_file_name;
				}
				else
				{
					BUGSlogging::log("The \"{$lib_name}\" library does not exist in either " . self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/ or ' . self::getIncludePath() . "core/lib/", 'core', BUGSlogging::LEVEL_FATAL);
					throw new BUGSLibraryNotFoundException("The \"{$lib_name}\" library does not exist in either " . self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/ or ' . self::getIncludePath() . "core/lib/");
				}
			}
		}
		
		/**
		 * Performs an action
		 * 
		 * @param string $action Name of the action
		 * @param string $method Name of the action method to run
		 */
		public static function performAction($action, $method)
		{
			$templatePath = self::getIncludePath() . 'modules/' . $action . '/templates/';
			$actionClassName = $action.'Actions';
			$actionToRunName = 'run' . ucfirst($method);
			$preActionToRunName = 'pre' . ucfirst($method);
			$responseObject = new BUGSresponse();
			$actionObject = new $actionClassName($responseObject);
			$responseObject->setTemplate(strtolower($method) . '.php');
			$responseObject->setDecoration(BUGSresponse::DECORATE_BOTH, array('header' => self::getIncludePath() . 'core/templates/header.inc.php', 'footer' => self::getIncludePath() . 'core/templates/footer.inc.php'));
			if (method_exists($actionObject, $actionToRunName))
			{
				BUGSlogging::log('Running main pre-execute action');
				$actionObject->preExecute(self::getRequest(), $method);
				if (method_exists($actionObject, $preActionToRunName))
				{
					BUGSlogging::log('Running custom pre-execute action');
					$actionObject->$preActionToRunName(self::getRequest(), $method);
				}
				BUGSlogging::log('Running route action '.$actionToRunName.'()');
				ob_start();
				ob_implicit_flush(0);
				if ($action_retval = $actionObject->$actionToRunName(self::getRequest()))
				{
					$content = ob_get_clean();
					BUGSlogging::log('...done');
				}
				else
				{
					BUGSlogging::log('...done');
					BUGSlogging::log('Displaying template');
					if (strpos($responseObject->getTemplate(), '/'))
					{
						$newPath = explode('/', $responseObject->getTemplate());
						$templateName = self::getIncludePath() . 'modules/' . $newPath[0] . '/templates/' . $newPath[1];
					}
					else
					{
						$templateName = $templatePath . $responseObject->getTemplate();
					}
					if (!file_exists($templateName))
					{
						BUGSlogging::log('The template file for the ' . $method . ' action ("'.$responseObject->getTemplate().'") does not exist', 'core', BUGSlogging::LEVEL_FATAL);
						throw new BUGSTemplateNotFoundException('The template file for the ' . $method . ' action ("'.$responseObject->getTemplate().'") does not exist');
					}
					BUGSlogging::log('configuring variables');
					foreach ($actionObject->getParameterHolder() as $key => $val)
					{
						$$key = $val;
					}
					/**
					 * @global BUGSrequest The request object
					 */
					$bugs_request = self::getRequest();
					
					/**
					 * @global BUGSuser The user object
					 */
					$bugs_user = self::getUser();
					
					/**
					 * @global BUGSaction The action object
					 */
					$bugs_action = $actionObject;

					/**
					 * @global BUGSresponse The action object
					 */
					$bugs_response = $responseObject;
					
					self::loadLibrary('ui');
					BUGSlogging::log('buffering output');
					require $templateName;
					$content = ob_get_clean();
					BUGSlogging::log('...completed');
				}
				$responseObject->renderHeaders();
				ob_start();
				ob_implicit_flush(0);
				if (!self::getRequest()->isAjaxCall() && $responseObject->doDecorateHeader())
				{
					BUGSlogging::log('decorating with header');
					require $responseObject->getHeaderDecoration(); 
					$decoration_header = ob_get_clean();
				}
				ob_start();
				ob_implicit_flush(0);
				$load_time = self::getLoadtime();
				if (class_exists('B2DB'))
				{
					$run_summary = self::getI18n()->__('Page load time: %load_time%, with %num_queries% queries. Scope ID: %scope_id%.', array('%load_time%' => ($load_time >= 1) ? __('%num_seconds% seconds', array('%num_seconds%' => round($load_time, 2))) : __('%num_milliseconds% ms', array('%num_milliseconds%' => round($load_time * 1000, 1))), '%num_queries%' => B2DB::getSQLHits(), '%scope_id%' => (self::getScope() instanceof BUGSscope) ? self::getScope()->getID() : __('unknown')));
				}
				else
				{
					$run_summary = self::getI18n()->__('Page load time: %load_time%.', array('%load_time%' => ($load_time >= 1) ? __('%num_seconds% seconds', array('%num_seconds%' => round($load_time, 2))) : __('%num_milliseconds% ms', array('%num_milliseconds%' => round($load_time * 1000, 1)))));
				}
				if (!self::getRequest()->isAjaxCall() && $responseObject->doDecorateFooter())
				{
					BUGSlogging::log('decorating with footer');
					require $responseObject->getFooterDecoration(); 
					$decoration_footer = ob_get_clean();
				}
				BUGSlogging::log('...done');
				BUGSlogging::log('rendering content');
				if (isset($decoration_header))
				{
					echo $decoration_header;
				}
				echo $content;
				if (isset($decoration_footer))
				{
					echo $decoration_footer;
				}
				BUGSlogging::log('...done (rendering content)');
				
				return true;
			}
			else
			{
				BUGSlogging::log("Cannot find the method {$actionToRunName}() in class {$actionClassName}.", 'core', BUGSlogging::LEVEL_FATAL);
				throw new BUGSActionNotFoundException("Cannot find the method {$actionToRunName}() in class {$actionClassName}. Make sure the method exists.");
			}
		}
		
		/**
		 * Returns all the links on the frontpage
		 * 
		 * @return array
		 */
		public static function getMainLinks()
		{
			if (!$links = BUGScache::get('core_main_links'))
			{
				$links = B2DB::getTable('B2tLinks')->getMainLinks();
				BUGScache::add('core_main_links', $links);
			}
			return $links;
		}
		
		/**
		 * Launches the MVC framework
		 */
		public static function go()
		{
			BUGSlogging::log('Dispatching');
			try
			{
				if ($route = self::getRouting()->getRouteFromUrl(self::getRequest()->getParameter('url')))
				{
					if (self::isInstallmode())
					{
						$route = array('module' => 'installation', 'action' => 'installIntro');
					} 
					if (is_dir(self::getIncludePath() . 'modules/' . $route['module']))
					{
						require self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actions.class.php';
						if (file_exists(self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actioncomponents.class.php'))
						{
							require self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actioncomponents.class.php';
						}
						if (self::performAction($route['module'], $route['action']))
						{
							return true;
						}
					}
					else
					{
						throw new Exception('Cannot load the ' . $route['module'] . ' module');
						return;
					}
				}
				else
				{
					//header("HTTP/1.0 404 Not Found", true, 404);
					//bugs_msgbox(true, 'Can\'t find the page you\'re looking for.', '404');
					require self::getIncludePath() . 'modules/main/classes/actions.class.php';
					self::performAction('main', 'notFound');
				}
			}
			catch (BUGSTemplateNotFoundException $e)
			{
				header("HTTP/1.0 404 Not Found", true, 404);
				tbg_exception('Template file does not exist for current action', $e);
				exit();
			}
			catch (BUGSActionNotFoundException $e)
			{
				header("HTTP/1.0 404 Not Found", true, 404);
				tbg_exception('Module action does not exist', $e);
				exit();				
			}
			catch (Exception $e)
			{
				header("HTTP/1.0 404 Not Found", true, 404);
				tbg_exception('An error occured', $e);
				exit();
			}
			B2DB::closeDBLink();
		
			BUGScontext::setLoadedAt();
		}
		
	}
	