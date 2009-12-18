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
		static protected $_modules = null;
		
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
		 * The response object
		 * 
		 * @var BUGSresponse
		 */
		static protected $_response = null;
		
		/**
		 * The current scope object
		 *
		 * @var BUGSscope
		 */
		static protected $_scope = null;
		
		/**
		 * The currently selected project, if any
		 * 
		 * @var BUGSproject
		 */
		static protected $_selected_project = null;
		
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
			if (!file_exists($classpath)) throw new Exception("Cannot add {$classpath} to classpaths, since it doesn't exist");
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
			$endtime = explode(' ', microtime());
			self::$_loadend = $endtime[1] + $endtime[0];
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
		}
		
		/**
		 * Initialize the context
		 * 
		 * @return null
		 */
		public static function initialize()
		{
			try
			{
				BUGSlogging::log('Loading request');
				self::$_request = new BUGSrequest();
				self::$_response = new BUGSresponse();
				BUGSlogging::log('...done');
				if (!is_readable(THEBUGGENIE_PATH . 'installed') && !isset($argc))
				{
					self::$_installmode = true;
				}
				elseif (!class_exists('B2DB'))
				{
					throw new Exception('The Bug Genie seems installed, but B2DB isn\'t configured. This usually indicates an error with the installation. Try removing the file ' . THEBUGGENIE_PATH . 'installed and try again.');
				}
				
				BUGSlogging::log('Loading first batch of routes', 'routing');
				if (!($routes_1 = BUGScache::get('routes_1')))
				{
					BUGSlogging::log('generating routes', 'routing');
					require THEBUGGENIE_PATH . 'core/load_routes.inc.php';
					BUGScache::add('routes_1', self::getRouting()->getRoutes());
				}
				else
				{
					BUGSlogging::log('loading routes from cache', 'routing');
					self::getRouting()->setRoutes($routes_1);
				}
				BUGSlogging::log('...done', 'routing');
				if (self::$_installmode) return true;

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
						self::getResponse()->headerRedirect(self::getRouting()->generate('login'));
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
					BUGSlogging::log(BUGSsettings::get('language'));
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
				if ($load_modules)
				{
					BUGSlogging::log('Loading modules');
					self::loadModules();
					BUGSlogging::log('...done');
				}
				else
				{
					BUGSlogging::log('Not loading modules');
				}

				BUGSlogging::log('Loading last batch of routes', 'routing');
				if (!($routes = BUGScache::get('routes_2')))
				{
					BUGSlogging::log('generating routes', 'routing');
					require THEBUGGENIE_PATH . 'core/load_routes_postmodules.inc.php';
					BUGScache::add('routes_2', self::getRouting()->getRoutes());
				}
				else
				{
					BUGSlogging::log('loading routes from cache', 'routing');
					self::getRouting()->setRoutes($routes);
				}
				BUGSlogging::log('...done', 'routing');

				BUGSlogging::log('...done initializing');
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
		 * Returns the response object
		 * 
		 * @return BUGSresponse
		 */
		public static function getResponse()
		{
			return self::$_response;
		}
		
		/**
		 * Reinitialize the i18n object, used only when changing the language in the middle of something
		 * 
		 * @param string $language The language code to change to
		 */
		public static function reinitializeI18n($language = null) 
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
		public static function getI18n()
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
		public static function getThemes()
		{
			$theme_path_handle = opendir(self::getIncludePath() . 'thebuggenie/themes/');
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
			if (self::$_modules === null)
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
								if (file_exists($moduleClassPath . 'B2DB'))
								{
									self::addClasspath($moduleClassPath . 'B2DB/');
									$module_paths[] = $moduleClassPath . 'B2DB/';
								}
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
						$classname = $moduleRow->get(B2tModules::CLASSNAME);
						if ($classname != '' && $classname != 'BUGSmodule')
						{
							if (class_exists($classname))
							{
								self::getI18n()->loadModuleStrings($module_name);
								self::$_modules[$module_name] = new $classname($moduleRow->get(B2tModules::ID), $moduleRow);
								BUGScache::add("module_{$module_name}", serialize(self::$_modules[$module_name]));
							}
							else
							{
								throw new Exception('Cannot load module "' . $module_name . '" as class "' . $classname . '", the class is not defined in the classpaths.');
							}
						}
						else
						{
							throw new Exception('Cannot load module "' . $module_name . '" as class BUGSmodule - modules should extend the BUGSmodule class with their own class.');
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
					BUGSmodule::loadModuleListeners(array_keys(self::$_modules));
					foreach (self::$_modules as $module_name => $module)
					{
						if ($module->isEnabled())
						{
							BUGSlogging::log("initializing {$module_name}");
							$module->initialize();
							BUGSlogging::log("done (initializing {$module_name})");
							BUGSlogging::log("loading module routes for {$module_name}", 'routing');
							$module->loadRoutes();
							BUGSlogging::log("done (loading module routes)", 'routing');
						}
					}
					BUGSlogging::log('done (initializing modules)');
				}
				else
				{
					BUGSlogging::log('no modules found');
				}
			}
			else
			{
				BUGSlogging::log('Modules already loaded', 'core', BUGSlogging::LEVEL_FATAL);
			}
		}
		
		/**
		 * Adds a module to the module list
		 *
		 * @param BUGSmodule $module
		 */
		public static function addModule($module, $module_name)
		{
			if (self::$_modules === null)
			{
				self::$_modules = array();
			}
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
		 * Get uninstalled modules
		 *
		 * @return array
		 */
		public static function getUninstalledModules()
		{
			$module_path = self::getIncludePath() . 'modules/';
			$module_path_handle = opendir($module_path);
			$modules = array();
			while ($module_name = readdir($module_path_handle))
			{
				if (is_dir($module_path . $module_name) && file_exists($module_path . $module_name . '/module'))
				{
					if (self::isModuleLoaded($module_name)) continue;
					$modules[$module_name] = file_get_contents($module_path . $module_name . '/module');
				}
			}
			return $modules;
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
		public static function trigger($module, $identifier, $params = array(), $return_when_true = false)
		{
			BUGSlogging::log("Triggering $module - $identifier");
			if (isset(self::$_registeredlisteners[$module][$identifier]))
			{
				foreach (self::$_registeredlisteners[$module][$identifier] as $trigger)
				{
					try
					{
						BUGSlogging::log('Running callback function '.$trigger);
						$retval = call_user_func($trigger, $params);
						if ($return_when_true && $retval === true)
						{
							return true;
						}
						BUGSlogging::log('done (Running callback function '.$trigger.')');
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

		/**
		 * Return all permissions available
		 * 
		 * @param string $type
		 * @param integer $uid
		 * @param integer $tid
		 * @param integer $gid
		 * @param integer $target_id[optional]
		 * @param boolean $all[optional]
		 *
		 * @return array
		 */
		public static function getAllPermissions($type, $uid, $tid, $gid, $target_id = null, $all = false)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tPermissions::SCOPE, self::getScope()->getID());
			$crit->addWhere(B2tPermissions::PERMISSION_TYPE, $type);

			if (($uid + $tid + $gid) == 0 && !$all)
			{
				$crit->addWhere(B2tPermissions::UID, $uid);
				$crit->addWhere(B2tPermissions::TID, $tid);
				$crit->addWhere(B2tPermissions::GID, $gid);
			}
			else
			{
				switch (true)
				{
					case ($uid != 0):
						$crit->addWhere(B2tPermissions::UID, $uid);
					case ($tid != 0):
						$crit->addWhere(B2tPermissions::TID, $tid);
					case ($gid != 0):
						$crit->addWhere(B2tPermissions::GID, $gid);
				}
			}
			if ($target_id != null)
			{
				$crit->addWhere(B2tPermissions::TARGET_ID, $target_id);
			}
	
			$permissions = array();

			if ($res = B2DB::getTable('B2tPermissions')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$permissions[] = array('p_type' => $row->get(B2tPermissions::PERMISSION_TYPE), 'target_id' => $row->get(B2tPermissions::TARGET_ID), 'allowed' => $row->get(B2tPermissions::ALLOWED), 'uid' => $row->get(B2tPermissions::UID), 'gid' => $row->get(B2tPermissions::GID), 'tid' => $row->get(B2tPermissions::TID), 'id' => $row->get(B2tPermissions::ID));
				}
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
					if (!array_key_exists($row->get(B2tPermissions::MODULE), self::$_permissions))
					{
						self::$_permissions[$row->get(B2tPermissions::MODULE)] = array();
					}
					if (!array_key_exists($row->get(B2tPermissions::PERMISSION_TYPE), self::$_permissions[$row->get(B2tPermissions::MODULE)]))
					{
						self::$_permissions[$row->get(B2tPermissions::MODULE)][$row->get(B2tPermissions::PERMISSION_TYPE)] = array();
					}
					if (!array_key_exists($row->get(B2tPermissions::TARGET_ID), self::$_permissions[$row->get(B2tPermissions::MODULE)][$row->get(B2tPermissions::PERMISSION_TYPE)]))
					{
						self::$_permissions[$row->get(B2tPermissions::MODULE)][$row->get(B2tPermissions::PERMISSION_TYPE)][$row->get(B2tPermissions::TARGET_ID)] = array();
					}
					self::$_permissions[$row->get(B2tPermissions::MODULE)][$row->get(B2tPermissions::PERMISSION_TYPE)][$row->get(B2tPermissions::TARGET_ID)][] = array('uid' => $row->get(B2tPermissions::UID), 'gid' => $row->get(B2tPermissions::GID), 'tid' => $row->get(B2tPermissions::TID), 'allowed' => (bool) $row->get(B2tPermissions::ALLOWED));
				}
			}
			BUGSlogging::log('done (starting to cache access permissions)');
		}

		public static function deleteModulePermissions($module_name)
		{
			if (array_key_exists($module_name, self::$_permissions))
			{
				unset(self::$_permissions[$module_name]);
			}
			B2DB::getTable('B2tPermissions')->deleteModulePermissions($module_name, self::getScope()->getID());
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
		 * @param integer $uid The user id for which the permission is valid, 0 for all
		 * @param integer $gid The group id for which the permission is valid, 0 for all
		 * @param integer $tid The team id for which the permission is valid, 0 for all
		 * @param $all
		 * @return unknown_type
		 */
		public static function checkPermission($permission_type, $uid, $gid, $tid, $target_id = 0, $module_name = 'core', $explicit = false)
		{
			if (array_key_exists($module_name, self::$_permissions) &&
				array_key_exists($permission_type, self::$_permissions[$module_name]) &&
				array_key_exists($target_id, self::$_permissions[$module_name][$permission_type]))
			{
				if ($uid != 0 || $gid != 0 && $tid != 0)
				{
					if ($uid != 0)
					{
						foreach (self::$_permissions[$module_name][$permission_type][$target_id] as $permission)
						{
							if ($permission['uid'] == $uid)
							{
								return $permission['allowed'];
							}
						}
					}

					if (is_array($tid) || $tid != 0)
					{
						foreach (self::$_permissions[$module_name][$permission_type][$target_id] as $permission)
						{
							if ((is_array($tid) && in_array($permission['tid'], array_keys($tid))) || $permission['tid'] == $tid)
							{
								return $permission['allowed'];
							}
						}
					}

					if ($gid != 0)
					{
						foreach (self::$_permissions[$module_name][$permission_type][$target_id] as $permission)
						{
							if ($permission['gid'] == $gid)
							{
								return $permission['allowed'];
							}
						}
					}
				}

				foreach (self::$_permissions[$module_name][$permission_type][$target_id] as $permission)
				{
					if ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0)
					{
						return $permission['allowed'];
					}
				}

			}

			if ($explicit) return false;
			
			return BUGSsettings::isPermissive();
		}
		
		protected static function _cacheAvailablePermissions()
		{
			if (self::$_available_permissions === null)
			{
				$i18n = self::getI18n();
				self::$_available_permissions = array('user' => array(), 'general' => array(), 'project' => array());

				self::$_available_permissions['user']['b2canonlyviewownissues'] = array('description' => $i18n->__('Can only view issues reported by the user'), 'levels' => 2);
				self::$_available_permissions['general']['b2canreadallcomments'] = array('description' => $i18n->__('Can view comments that are not public'), 'levels' => 2);
				self::$_available_permissions['general']['b2addlinks'] = array('description' => $i18n->__('Can add links to issue reports'), 'levels' => 2);
				self::$_available_permissions['general']['b2uploadfiles'] = array('description' => $i18n->__('Can add files to issues reported by the user'), 'levels' => 2);
				self::$_available_permissions['general']['b2canfindissues'] = array('description' => $i18n->__('Can search for issues '), 'levels' => 2);
				self::$_available_permissions['general']['b2canvote'] = array('description' => $i18n->__('Can vote for issues'), 'levels' => 2);
				self::$_available_permissions['general']['b2candeleteissues'] = array('description' => $i18n->__('Can delete issues'), 'levels' => 2);
				self::$_available_permissions['project']['b2caneditissuefields'] = array('description' => $i18n->__('Can update issue details'), 'levels' => 2);
				self::$_available_permissions['project']['b2caneditissueusers'] = array('description' => $i18n->__('Can assign issues'), 'levels' => 2);
				self::$_available_permissions['project']['b2caneditissuetext'] = array('description' => $i18n->__('Can edit issue text'), 'levels' => 2);
				self::$_available_permissions['project']['b2caneditcomments'] = array('description' => $i18n->__('Can edit comments'), 'levels' => 4);
				self::$_available_permissions['project']['b2canaddcomments'] = array('description' => $i18n->__('Can add comments'), 'levels' => 4);
				self::$_available_permissions['project']['b2canviewcomments'] = array('description' => $i18n->__('Can view comments'), 'levels' => 4);
				self::$_available_permissions['project']['b2canaddbuilds'] = array('description' => $i18n->__('Can add versions to list of affected versions'), 'levels' => 2);
				self::$_available_permissions['project']['b2canaddcomponents'] = array('description' => $i18n->__('Can add components to list of affected components'), 'levels' => 2);
				self::$_available_permissions['project']['b2canaddeditions'] = array('description' => $i18n->__('Can add editions to list of affected editions'), 'levels' => 2);
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
				BUGSlogging::log("Setting scope from function parameter");
				$theScope = BUGSfactory::scopeLab((int) $scope);
				self::$_scope = $theScope;
				BUGSlogging::log("...done (Setting scope from function parameter)");
				return true;
			}
	
			try
			{
				$hostprefix = (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
				BUGSlogging::log("Checking if scope can be set from hostname (".$hostprefix.$_SERVER['HTTP_HOST'].")");
				$row = B2DB::getTable('B2tScopes')->getByHostname($hostprefix . $_SERVER['HTTP_HOST']);
				if ($row instanceof B2DBRow)
				{
					BUGSlogging::log("It could");
					BUGSlogging::log("Setting scope from hostname");
					$theScope = BUGSfactory::scopeLab($row->get(B2tScopes::ID), $row);
					self::$_scope = $theScope;
					BUGSlogging::log("...done (Setting scope from hostname)");
					return true;
				}
				BUGSlogging::log("It couldn't", 'main', BUGSlogging::LEVEL_WARNING);
			}
			catch (Exception $e)
			{
				BUGSlogging::log("Couldn't find a scope for hostname ".$hostprefix . $_SERVER['HTTP_HOST'], 'main', BUGSlogging::LEVEL_FATAL);
				BUGSlogging::log($e->getMessage(), 'main', BUGSlogging::LEVEL_FATAL);
				throw new Exception("Could not load scope. This is usually because the scopes table doesn't have a scope for this hostname");
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
		 * Set the currently selected project
		 * 
		 * @param BUGSproject $project The project, or null if none
		 */
		public static function setCurrentProject($project)
		{
			self::$_selected_project = $project;
		}
		
		/**
		 * Return the currently selected project if any, or null
		 * 
		 * @return BUGSproject
		 */
		public static function getCurrentProject()
		{
			return self::$_selected_project;
		}

		/**
		 * Return whether current project is set
		 *
		 * @return boolean
		 */
		public static function isProjectContext()
		{
			return (bool) (self::getCurrentProject() instanceof BUGSproject);
		}
		
		/**
		 * Set a message to be retrieved in the next request
		 * 
		 * @param string $message The message
		 */
		public static function setMessage($key, $message)
		{
			if (!array_key_exists('tbg_message', $_SESSION))
			{
				$_SESSION['tbg_message'] = array();
			}
			$_SESSION['tbg_message'][$key] = $message;
		}

		/**
		 * Whether or not there is a message in the next request
		 * 
		 * @return boolean
		 */
		public static function hasMessage($key)
		{
			if (!array_key_exists('tbg_message', $_SESSION))
			{
				return false;
			}
			else
			{
				return array_key_exists($key, $_SESSION['tbg_message']);
			}
		}
		
		/**
		 * Retrieve the message
		 * 
		 * @return string
		 */
		public static function getMessage($key)
		{
			return (self::hasMessage($key)) ? $_SESSION['tbg_message'][$key] : false;
		}
		
		/**
		 * Clear the message
		 */
		public static function clearMessage($key)
		{
			if (self::hasMessage($key))
			{
				unset($_SESSION['tbg_message'][$key]);
			}
		}

		/**
		 * Retrieve the message and clear it
		 * 
		 * @return string
		 */
		public static function getMessageAndClear($key)
		{
			if ($message = self::getMessage($key))
			{
				self::clearMessage($key);
				return $message;
			}
			return null;
		}

		/**
		 * Loads a function library
		 * 
		 * @param string $lib_name The name of the library
		 */
		public static function loadLibrary($lib_name)
		{
			if (strpos($lib_name, '/') !== false)
			{
				list ($module, $lib_name) = explode('/', $lib_name);
			}

			// Skip the library if it already exists
			if (!array_key_exists($lib_name, self::$_libs))
			{
				$lib_file_name = "{$lib_name}.inc.php";

				if (isset($module) && file_exists(self::getIncludePath() . "modules/{$module}/lib/{$lib_file_name}"))
				{
					require self::getIncludePath() . "modules/{$module}/lib/" . $lib_file_name;
					self::$_libs[$lib_name] = self::getIncludePath() . "modules/{$module}/lib/{$lib_file_name}";
				}
				elseif (file_exists(self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/' . $lib_file_name))
				{
					// Include the library from the current module if it exists
					require self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/' . $lib_file_name;
					self::$_libs[$lib_name] = self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/' . $lib_file_name;
				}
				elseif (file_exists(self::getIncludePath() . "core/lib/" . $lib_file_name))
				{
					// Include the library from the global library directory if it exists
					require self::getIncludePath() . "core/lib/" . $lib_file_name;
					self::$_libs[$lib_name] = self::getIncludePath() . "core/lib/" . $lib_file_name;
				}
				else
				{
					// Throw an exception if the library can't be found in any of
					// the above directories
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
			// Set content variable
			$content = null;
			
			// Set the template to be used when rendering the html (or other) output
			$templatePath = self::getIncludePath() . 'modules/' . $action . '/templates/';

			// Construct the action class and method name, including any pre- action(s)
			$actionClassName = $action.'Actions';
			$actionToRunName = 'run' . ucfirst($method);
			$preActionToRunName = 'pre' . ucfirst($method);

			// Set up the response object, responsible for controlling any output
			self::getResponse()->setPage(self::getRouting()->getCurrentRouteName());
			self::getResponse()->setTemplate(strtolower($method) . '.php');
			self::getResponse()->setDecoration(BUGSresponse::DECORATE_BOTH, array('header' => self::getIncludePath() . 'core/templates/header.inc.php', 'footer' => self::getIncludePath() . 'core/templates/footer.inc.php'));
			
			// Set up the action object
			$actionObject = new $actionClassName();

			// Run the specified action method set if it exists
			if (method_exists($actionObject, $actionToRunName))
			{
				// Turning on output buffering
				ob_start();
				ob_implicit_flush(0);

				BUGSlogging::log('Running main pre-execute action');
				// Running any overridden preExecute() method defined for that module
				// or the default empty one provided by BUGSaction
				if ($pre_action_retval = $actionObject->preExecute(self::getRequest(), $method))
				{
					$content = ob_get_clean();
					BUGSlogging::log('preexecute method returned something, skipping further action');
				}

				if ($content === null)
				{
					if (self::getResponse()->getHttpStatus() == 200)
					{
						// Checking for and running action-specific preExecute() function if
						// it exists
						if (method_exists($actionObject, $preActionToRunName))
						{
							BUGSlogging::log('Running custom pre-execute action');
							$actionObject->$preActionToRunName(self::getRequest(), $method);
						}
					}

					// Running main route action
					BUGSlogging::log('Running route action '.$actionToRunName.'()');
					if (self::getResponse()->getHttpStatus() == 200 && ($action_retval = $actionObject->$actionToRunName(self::getRequest())))
					{
						// If the action returns *any* output, we're done, and collect the
						// output to a variable to be outputted in context later
						$content = ob_get_clean();
						BUGSlogging::log('...done');
					}
					else
					{
						// If the action doesn't return any output (which it usually doesn't)
						// we continue on to rendering the template file for that specific action
						BUGSlogging::log('...done');
						BUGSlogging::log('Displaying template');

						// Check to see if we have a translated version of the template
						if (($templateName = self::getI18n()->hasTranslatedTemplate(self::getResponse()->getTemplate())) === false)
						{
							// Check to see if the template has been changed, and whether it's in a
							// different module, specified by "module/templatename"
							if (strpos(self::getResponse()->getTemplate(), '/'))
							{
								$newPath = explode('/', self::getResponse()->getTemplate());
								$templateName = self::getIncludePath() . 'modules/' . $newPath[0] . '/templates/' . $newPath[1] . '.php';
							}
							else
							{
								$templateName = $templatePath . self::getResponse()->getTemplate();
							}
						}

						// Check to see if the template exists and throw an exception otherwise
						if (!file_exists($templateName))
						{
							BUGSlogging::log('The template file for the ' . $method . ' action ("'.self::getResponse()->getTemplate().'") does not exist', 'core', BUGSlogging::LEVEL_FATAL);
							throw new BUGSTemplateNotFoundException('The template file for the ' . $method . ' action ("'.self::getResponse()->getTemplate().'") does not exist');
						}

						// Make all template variables available to the template, including the
						// main ones like request, user, action and response
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
						$bugs_response = self::getResponse();

						// Load the "ui" library, since this is used a lot
						self::loadLibrary('ui');

						// Include the template, buffer the output and store it in a variable
						// to be outputted in context later
						BUGSlogging::log('rendering template and buffering output');
						require $templateName;
						$content = ob_get_clean();
						BUGSlogging::log('...completed');
					}
				}

				// Render header template if any, and store the output in a variable
				ob_start();
				ob_implicit_flush(0);
				if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateHeader())
				{
					BUGSlogging::log('decorating with header');
					require self::getResponse()->getHeaderDecoration();
					$decoration_header = ob_get_clean();
				}

				// Set up the run summary, and store it in a variable
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

				// Render footer template if any, and store the output in a variable
				if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateFooter())
				{
					BUGSlogging::log('decorating with footer');
					require self::getResponse()->getFooterDecoration();
					$decoration_footer = ob_get_clean();
				}
				BUGSlogging::log('...done');
				BUGSlogging::log('rendering content');

				// Render output in correct order
				self::getResponse()->renderHeaders();

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
				if (($route = self::getRouting()->getRouteFromUrl(self::getRequest()->getParameter('url')))  || self::isInstallmode())
				{
					if (self::isInstallmode())
					{
						$route = array('module' => 'installation', 'action' => 'installIntro');
					} 
					if (is_dir(self::getIncludePath() . 'modules/' . $route['module']))
					{
						if (!file_exists(self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actions.class.php'))
						{
							throw new BUGSActionNotFoundException('The ' . $route['module'] . ' module is missing the classes/actions.class.php file, containing all the module actions');
						}
						if (!class_exists($route['module'].'Actions'))
						{
							require self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actions.class.php';
						}
						if (file_exists(self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actioncomponents.class.php') && !class_exists($route['module'].'ActionComponents'))
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
				tbg_exception('Module action "' . $route['action'] . '" does not exist for module "' . $route['module'] . '"', $e);
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

		public static function getURLhost()
		{
			return self::getScope()->getHostname();
		}

	}
	