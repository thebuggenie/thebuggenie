<?php

	/**
	 * The core class of the B2 engine
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGContext
	{

		const PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES = 1;
		const PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES = 2;
		const PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO = 6;
		const PREDEFINED_SEARCH_PROJECT_MOST_VOTED = 7;
		const PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES = 3;
		const PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES = 4;
		const PREDEFINED_SEARCH_MY_REPORTED_ISSUES = 5;
		
		static protected $_environment = 2;

		static protected $debug_mode = false;
		
		static protected $_partials_visited = array();

		/**
		 * The current user
		 *
		 * @var TBGUser
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
		 * @var TBGI18n
		 */
		static protected $_i18n = null;
		
		/**
		 * The request object
		 * 
		 * @var TBGRequest
		 */
		static protected $_request = null;
		
		/**
		 * The response object
		 * 
		 * @var TBGResponse
		 */
		static protected $_response = null;
		
		/**
		 * The current scope object
		 *
		 * @var TBGScope
		 */
		static protected $_scope = null;

		/**
		 * The TBGFactory instance
		 *
		 * @var TBGFactory
		 */
		static protected $_factory = null;
		
		/**
		 * The currently selected project, if any
		 * 
		 * @var TBGProject
		 */
		static protected $_selected_project = null;
		
		/**
		 * The currently selected client, if any
		 * 
		 * @var TBGClient
		 */
		static protected $_selected_client = null;
		
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
		 * List of loaded libraries
		 * 
		 * @var string
		 */
		static protected $_libs = array();
		
		/**
		 * The routing object
		 * 
		 * @var TBGRouting
		 */
		static protected $_routing = null;

		/**
		 * Messages passed on from the previous request
		 *
		 * @var array
		 */
		static protected $_messages = null;

		static protected $_redirect_login = null;

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
				TBGLogging::log('Invalid classpath, appending directory separator', 'main', TBGLogging::LEVEL_WARNING);
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
		 * Setup the routing object with CLI parameters
		 *
		 * @param string $module
		 * @param string $action
		 */
		public static function setCLIRouting($module, $action)
		{
			self::$_routing->setCurrentRouteModule($module);
			self::$_routing->setCurrentRouteAction($action);
			self::$_routing->setCurrentRouteName('cli');
			self::$_routing->setCurrentRouteCSRFenabled(false);
		}

		/**
		 * Returns the routing object
		 * 
		 * @return TBGRouting
		 */
		public static function getRouting()
		{
			if (!self::$_routing)
			{
				self::$_routing = new TBGRouting();
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
			$_SESSION['b2lastreloadtime'] = NOW;
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
				self::$_includepath = TBGSettings::get('local_path', 'core');
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
			if (stristr(PHP_OS, 'WIN')) { self::$_tbgpath = str_replace("\\", "/", self::$_tbgpath); /* Windows adds a \ to the URL which we don't want */ }
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
		
		public static function checkInstallMode()
		{
			if (!is_readable(THEBUGGENIE_PATH . 'installed'))
			{
				self::$_installmode = true;
			}
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
				TBGLogging::log('Loading request');
				self::$_request = new TBGRequest();
				self::$_factory = new TBGFactory();
				TBGLogging::log('...done');
				if (!self::isCLI())
				{
					TBGLogging::log('Loading response');
					self::$_response = new TBGResponse();
					TBGLogging::log('...done');
				}
				self::checkInstallMode();
				if (!self::$_installmode && !class_exists('B2DB'))
				{
					throw new Exception("The Bug Genie seems installed, but B2DB isn't configured. This usually indicates an error with the installation. Try removing the file ".THEBUGGENIE_PATH."installed and try again.");
				}

				TBGLogging::log('Loading first batch of routes', 'routing');
				if (!($routes_1 = TBGCache::get('routes_1')))
				{
					TBGLogging::log('generating routes', 'routing');
					require THEBUGGENIE_PATH . 'core/load_routes.inc.php';
					TBGCache::add('routes_1', self::getRouting()->getRoutes());
				}
				else
				{
					TBGLogging::log('loading routes from cache', 'routing');
					self::getRouting()->setRoutes($routes_1);
				}
				TBGLogging::log('...done', 'routing');
				
				TBGLogging::log("Setting current scope");
				try
				{
					self::setScope();
					TBGLogging::log("Loading settings");
					try
					{
						TBGSettings::loadSettings();
					}
					catch (Exception $e)
					{
						if (!self::isCLI())
						{
							throw $e;
						}
						else
						{
							self::$_installmode = true;
						}
					}
					TBGLogging::log("...done");
				}
				catch (Exception $e)
				{
					if (!self::isInstallmode()) 
					{
						throw $e;
					}
					TBGLogging::log("Ignoring scope exception since we're in installmode");
				}
				TBGLogging::log("...done");
				

				if (self::$_installmode)
				{
					self::$_modules = array();
					return true;
				}

				if (!self::isCLI())
				{
					TBGLogging::log('Loading i18n strings');
					if (!$cached_i18n = TBGCache::get('i18n_'.TBGSettings::get('language')))
					{
						TBGLogging::log('Loading strings from file');
						TBGLogging::log(TBGSettings::get('language'));
						self::$_i18n = new TBGI18n(TBGSettings::get('language'));
						self::$_i18n->initialize();
						TBGCache::add('i18n_'.TBGSettings::get('language'), self::$_i18n);
					}
					else
					{
						TBGLogging::log('Using cached i18n strings');
						self::$_i18n = $cached_i18n;
					}
					TBGLogging::log('...done');
				}

				TBGLogging::log('Loading modules');
				self::loadModules();
				TBGLogging::log('...done');

				TBGLogging::log('Loading user');
				try
				{
					TBGLogging::log('is this logout?');
					if (self::getRequest()->getParameter('logout'))
					{
						TBGLogging::log('yes');
						TBGEvent::createNew('core', 'pre_logout')->trigger();
						self::logout();
						TBGEvent::createNew('core', 'post_logout')->trigger();
					}
					else
					{
						TBGLogging::log('no');
						TBGLogging::log('sets up user object');
						$event = TBGEvent::createNew('core', 'pre_login');
						$event->trigger();

						if ($event->isProcessed())
						{
							self::loadUser($event->getReturnValue());
						}
						else
						{
							self::loadUser();
						}
						TBGEvent::createNew('core', 'post_login', self::getUser())->trigger();

						TBGLogging::log('loaded');
						TBGLogging::log('caches permissions');
						self::cacheAllPermissions();
						TBGLogging::log('...cached');
					}
				}
				catch (Exception $e)
				{
					TBGLogging::log("Something happened while setting up user: ". $e->getMessage(), 'main', TBGLogging::LEVEL_WARNING);
					if (!self::isCLI() && (self::getRouting()->getCurrentRouteModule() != 'main' || self::getRouting()->getCurrentRouteAction() != 'register1' && self::getRouting()->getCurrentRouteAction() != 'register2' && self::getRouting()->getCurrentRouteAction() != 'activate' && self::getRouting()->getCurrentRouteAction() != 'reset_password' && self::getRouting()->getCurrentRouteAction() != 'captcha' && self::getRouting()->getCurrentRouteAction() != 'login' && self::getRouting()->getCurrentRouteAction() != 'getBackdropPartial'))
					{
						self::$_redirect_login = true;
					}
					else
					{
						self::$_user = self::factory()->TBGUser(TBGSettings::getDefaultUserID());
					}
				}

				TBGLogging::log('...done');
				TBGLogging::log('Loading last batch of routes', 'routing');
				if (!($routes = TBGCache::get('routes_2')))
				{
					TBGLogging::log('generating routes', 'routing');
					require THEBUGGENIE_PATH . 'core/load_routes_postmodules.inc.php';
					TBGCache::add('routes_2', self::getRouting()->getRoutes());
				}
				else
				{
					TBGLogging::log('loading routes from cache', 'routing');
					self::getRouting()->setRoutes($routes);
				}
				TBGLogging::log('...done', 'routing');

				TBGLogging::log('...done initializing');
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		/**
		 * Returns the factory object
		 *
		 * @return TBGFactory
		 */
		public static function factory()
		{
			return self::$_factory;
		}

		/**
		 * Returns the request object
		 * 
		 * @return TBGRequest
		 */
		public static function getRequest()
		{
			return self::$_request;
		}
		
		/**
		 * Returns the response object
		 * 
		 * @return TBGResponse
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
				self::$_i18n = new TBGI18n(TBGSettings::get('language'));
			}
			else
			{
				self::$_i18n = new TBGI18n($language);
			}
		}
		
		/**
		 * Get the i18n object
		 *
		 * @return TBGI18n
		 */
		public static function getI18n()
		{
			if (!self::$_i18n instanceof TBGI18n)
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
			$theme_path_handle = opendir(self::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR);
			$themes = array();
			
			while ($theme = readdir($theme_path_handle))
			{
				if (strstr($theme, '.') == '' && $theme != 'modules') 
				{ 
					$themes[] = $theme; 
				}
			}
			
			return $themes;
		}
		
		/**
		 * Load the user object into the user property
		 * 
		 * @return TBGUser
		 */
		public static function loadUser($user = null)
		{
			try
			{
				self::$_user = ($user === null) ? TBGUser::loginCheck(self::getRequest()->getParameter('tbg3_username'), self::getRequest()->getParameter('tbg3_password')) : $user;
				if (self::$_user->isAuthenticated())
				{
					if (self::$_user->isOffline() || self::$_user->isAway())
					{
						self::$_user->setOnline();
					}
					self::$_user->updateLastSeen();
					self::$_user->save();
					if (!(self::$_user->getGroup() instanceof TBGGroup))
					{
						throw new Exception('This user account belongs to a group that does not exist anymore. <br>Please contact the system administrator.');
					}
				}
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
		 * @return TBGUser
		 */
		public static function getUser()
		{
			return self::$_user;
		}
		
		/**
		 * Set the current user
		 * 
		 * @param TBGUser $user 
		 */
		public static function setUser(TBGUser $user)
		{
			self::$_user = $user;
		}
		
		/**
		 * Loads and initializes all modules
		 */
		public static function loadModules()
		{
			if (self::$_modules === null)
			{
				self::$_modules = array();
				if (self::isInstallmode()) return;
				$modules = array();

				if ($module_paths = TBGCache::get('module_paths'))
				{
					TBGLogging::log('using cached modules');
					foreach ($module_paths as $moduleClassPath)
					{
						try
						{
							self::addClasspath($moduleClassPath);
						}
						catch (Exception $e) { } // ignore "dir not exists" errors
					}
					$modules = TBGCache::get('modules');
					foreach ($modules as $module)
					{
						self::$_modules[$module] = unserialize(TBGCache::get("module_{$module}"));
					}
					TBGLogging::log('done');
				}
				else
				{
					TBGLogging::log('getting modules from database');
					$module_paths = array();

					if ($res = B2DB::getTable('TBGModulesTable')->getAll())
					{
						while ($moduleRow = $res->getNextRow())
						{
							$module_name = $moduleRow->get(TBGModulesTable::MODULE_NAME);
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
					TBGLogging::log('done (getting modules from database)');
					TBGCache::add('module_paths', $module_paths);
					TBGCache::add('modules', array_keys($modules));
					TBGLogging::log('setting up module objects');
					foreach ($modules as $module_name => $moduleRow)
					{
						$classname = $moduleRow->get(TBGModulesTable::CLASSNAME);
						if ($classname != '' && $classname != 'TBGModule')
						{
							if (class_exists($classname))
							{
								self::getI18n()->loadModuleStrings($module_name);
								self::$_modules[$module_name] = new $classname($moduleRow->get(TBGModulesTable::ID), $moduleRow);
								TBGCache::add("module_{$module_name}", serialize(self::$_modules[$module_name]));
							}
							else
							{
								TBGLogging::log('Cannot load module "' . $module_name . '" as class "' . $classname . '", the class is not defined in the classpaths.', 'modules', TBGLogging::LEVEL_WARNING_RISK);
								TBGLogging::log('Removing module "' . $module_name . '" as it cannot be loaded', 'modules', TBGLogging::LEVEL_NOTICE);
								TBGModule::removeModule($moduleRow->get(TBGModulesTable::ID));
							}
						}
						else
						{
							throw new Exception('Cannot load module "' . $module_name . '" as class TBGModule - modules should extend the TBGModule class with their own class.');
						}
					}
					TBGLogging::log('done (setting up module objects)');

					TBGLogging::log('caching module access permissions');
					if (!self::isCLI()) TBGModule::cacheAllAccessPermissions();
					TBGLogging::log('done (caching module access permissions)');
				}

				TBGLogging::log('initializing modules');
				if (!empty(self::$_modules))
				{
					foreach (self::$_modules as $module_name => $module)
					{
						$module->initialize();
					}
					TBGModule::loadModuleListeners(array_keys(self::$_modules));
					TBGLogging::log('done (initializing modules)');
				}
				else
				{
					TBGLogging::log('no modules found');
				}
			}
			else
			{
				TBGLogging::log('Modules already loaded', 'core', TBGLogging::LEVEL_FATAL);
			}
		}
		
		/**
		 * Adds a module to the module list
		 *
		 * @param TBGModule $module
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
		 * @return TBGModule
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
			$crit->addWhere(TBGPermissionsTable::SCOPE, self::getScope()->getID());
			$crit->addWhere(TBGPermissionsTable::PERMISSION_TYPE, $type);

			if (($uid + $tid + $gid) == 0 && !$all)
			{
				$crit->addWhere(TBGPermissionsTable::UID, $uid);
				$crit->addWhere(TBGPermissionsTable::TID, $tid);
				$crit->addWhere(TBGPermissionsTable::GID, $gid);
			}
			else
			{
				switch (true)
				{
					case ($uid != 0):
						$crit->addWhere(TBGPermissionsTable::UID, $uid);
					case ($tid != 0):
						$crit->addWhere(TBGPermissionsTable::TID, $tid);
					case ($gid != 0):
						$crit->addWhere(TBGPermissionsTable::GID, $gid);
				}
			}
			if ($target_id != null)
			{
				$crit->addWhere(TBGPermissionsTable::TARGET_ID, $target_id);
			}
	
			$permissions = array();

			if ($res = B2DB::getTable('TBGPermissionsTable')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$permissions[] = array('p_type' => $row->get(TBGPermissionsTable::PERMISSION_TYPE), 'target_id' => $row->get(TBGPermissionsTable::TARGET_ID), 'allowed' => $row->get(TBGPermissionsTable::ALLOWED), 'uid' => $row->get(TBGPermissionsTable::UID), 'gid' => $row->get(TBGPermissionsTable::GID), 'tid' => $row->get(TBGPermissionsTable::TID), 'id' => $row->get(TBGPermissionsTable::ID));
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
			
			if ($permissions = TBGCache::get('permissions'))
			{
				self::$_permissions = $permissions;
				TBGLogging::log('Using cached permissions');
			}
			else
			{
				TBGLogging::log('starting to cache access permissions');
				if ($res = B2DB::getTable('TBGPermissionsTable')->getAll())
				{
					while ($row = $res->getNextRow())
					{
						if (!array_key_exists($row->get(TBGPermissionsTable::MODULE), self::$_permissions))
						{
							self::$_permissions[$row->get(TBGPermissionsTable::MODULE)] = array();
						}
						if (!array_key_exists($row->get(TBGPermissionsTable::PERMISSION_TYPE), self::$_permissions[$row->get(TBGPermissionsTable::MODULE)]))
						{
							self::$_permissions[$row->get(TBGPermissionsTable::MODULE)][$row->get(TBGPermissionsTable::PERMISSION_TYPE)] = array();
						}
						if (!array_key_exists($row->get(TBGPermissionsTable::TARGET_ID), self::$_permissions[$row->get(TBGPermissionsTable::MODULE)][$row->get(TBGPermissionsTable::PERMISSION_TYPE)]))
						{
							self::$_permissions[$row->get(TBGPermissionsTable::MODULE)][$row->get(TBGPermissionsTable::PERMISSION_TYPE)][$row->get(TBGPermissionsTable::TARGET_ID)] = array();
						}
						self::$_permissions[$row->get(TBGPermissionsTable::MODULE)][$row->get(TBGPermissionsTable::PERMISSION_TYPE)][$row->get(TBGPermissionsTable::TARGET_ID)][] = array('uid' => $row->get(TBGPermissionsTable::UID), 'gid' => $row->get(TBGPermissionsTable::GID), 'tid' => $row->get(TBGPermissionsTable::TID), 'allowed' => (bool) $row->get(TBGPermissionsTable::ALLOWED));
					}
				}
				TBGLogging::log('done (starting to cache access permissions)');
				TBGCache::add('permissions', self::$_permissions);
			}
		}

		public static function deleteModulePermissions($module_name)
		{
			if (array_key_exists($module_name, self::$_permissions))
			{
				unset(self::$_permissions[$module_name]);
			}
			B2DB::getTable('TBGPermissionsTable')->deleteModulePermissions($module_name, self::getScope()->getID());
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
			
			B2DB::getTable('TBGPermissionsTable')->removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope);
			
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
		 * @param boolean $allowed Allowed or not
		 * @param integer $scope[optional] A specified scope if not the default
		 */
		public static function setPermission($permission_type, $target_id, $module, $uid, $gid, $tid, $allowed, $scope = null)
		{
			if ($scope === null) $scope = self::getScope()->getID();
			
			self::removePermission($permission_type, $target_id, $module, $uid, $gid, $tid, false, $scope);
			TBGPermissionsTable::getTable()->setPermission($uid, $gid, $tid, $allowed, $module, $permission_type, $target_id, $scope);
			
			self::cacheAllPermissions();
		}

		public static function isPermissionSet($type, $permission_key, $id, $target_id = 0, $module_name = 'core')
		{
			if (array_key_exists($module_name, self::$_permissions) &&
				array_key_exists($permission_key, self::$_permissions[$module_name]) &&
				array_key_exists($target_id, self::$_permissions[$module_name][$permission_key]))
			{
				if ($type == 'group')
				{
					foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
					{
						if ($permission['gid'] == $id) return $permission['allowed'];
					}
				}
				if ($type == 'user')
				{
					foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
					{
						if ($permission['uid'] == $id) return $permission['allowed'];
					}
				}
				if ($type == 'team')
				{
					foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
					{
						if ($permission['tid'] == $id) return $permission['allowed'];
					}
				}
				if ($type == 'everyone')
				{
					foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
					{
						if ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0)
						{
							return $permission['allowed'];
						}
					}
				}
			}
			return null;
		}
	
		/**
		 * Check to see if a specified user/group/team has access
		 * 
		 * @param string $permission_type The permission type 
		 * @param integer $uid The user id for which the permission is valid, 0 for all
		 * @param integer $gid The group id for which the permission is valid, 0 for all
		 * @param integer $tid The team id for which the permission is valid, 0 for all
		 * @param integer $target_id[optional] The target id
		 * @param string $module_name[optional] The name of the module for which the permission is valid
		 * @param boolean $explicit[optional] whether to check for an explicit permission and return false if not set
		 * @param boolean $permissive[optional] whether to return false or true when explicit fails
		 * 
		 * @return unknown_type
		 */
		public static function checkPermission($permission_type, $uid, $gid, $tid, $target_id = 0, $module_name = 'core', $explicit = false, $permissive = false)
		{
			if (array_key_exists($module_name, self::$_permissions) &&
				array_key_exists($permission_type, self::$_permissions[$module_name]) &&
				(array_key_exists($target_id, self::$_permissions[$module_name][$permission_type]) || $target_id === null))
			{
				$permissions = ($target_id === null) ? self::$_permissions[$module_name][$permission_type] : self::$_permissions[$module_name][$permission_type][$target_id];
				if ($uid != 0 || $gid != 0 || $tid != 0)
				{
					if ($uid != 0)
					{
						foreach ($permissions as $key => $permission)
						{
							if (!array_key_exists('uid', $permission))
							{
								foreach ($permission as $pkey => $pp)
								{
									if ($pp['uid'] == $uid) return $pp['allowed'];
								}
							}
							else
							{
								if ($permission['uid'] == $uid) return $permission['allowed'];
							}
						}
					}

					if (is_array($tid) || $tid != 0)
					{
						foreach ($permissions as $key => $permission)
						{
							if (!array_key_exists('tid', $permission))
							{
								foreach ($permission as $pkey => $pp)
								{
									if ((is_array($tid) && in_array($pp['tid'], array_keys($tid))) || $pp['tid'] == $tid)
									{
										return $pp['allowed'];
									}
								}
							}
							else
							{
								if ((is_array($tid) && in_array($permission['tid'], array_keys($tid))) || $permission['tid'] == $tid)
								{
									return $permission['allowed'];
								}
							}
						}
					}

					if ($gid != 0)
					{
						foreach ($permissions as $key => $permission)
						{
							if (!array_key_exists('gid', $permission))
							{
								foreach ($permission as $pkey => $pp)
								{
									if ($pp['gid'] == $gid) return $pp['allowed'];
								}
							}
							else
							{
								if ($permission['gid'] == $gid) return $permission['allowed'];
							}
						}
					}
				}

				foreach ($permissions as $key => $permission)
				{
					if (!array_key_exists('uid', $permission))
					{
						foreach ($permission as $pkey => $pp)
						{
							if ($pp['uid'] + $pp['gid'] + $pp['tid'] == 0) return $permission['allowed'];
						}
					}
					else
					{
						if ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0) return $permission['allowed'];
					}
				}

			}

			if ($explicit) return $permissive;
			
			return TBGSettings::isPermissive();
		}
		
		protected static function _cacheAvailablePermissions()
		{
			if (self::$_available_permissions === null)
			{
				$i18n = self::getI18n();
				self::$_available_permissions = array('user' => array(), 'general' => array(), 'project' => array());

				self::$_available_permissions['user']['canseeallissues'] = array('description' => $i18n->__('Can see issues reported by other users'), 'mode' => 'permissive');
				self::$_available_permissions['configuration']['cansaveconfig'] = array('description' => $i18n->__('Can access the configuration page and edit all configuration'), 'details' => array());
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Settings" configuration page'), 'target_id' => 12));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Settings" configuration page'), 'target_id' => 12));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Permissions" configuration page'), 'target_id' => 5));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Permissions" configuration page'), 'target_id' => 5));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Uploads" configuration page'), 'target_id' => 3));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Uploads" configuration page'), 'target_id' => 3));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Scopes" configuration page'), 'target_id' => 14));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Scopes" configuration page'), 'target_id' => 14));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Import" configuration page'), 'target_id' => 16));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Import" configuration page'), 'target_id' => 16));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Projects" configuration page'), 'target_id' => 10));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Projects" configuration page'), 'target_id' => 10));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Issue types" configuration page'), 'target_id' => 6));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Issue types" configuration page'), 'target_id' => 6));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Issue fields" configuration page'), 'target_id' => 4));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Issue fields" configuration page'), 'target_id' => 4));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Users, teams and groups" configuration page'), 'target_id' => 2));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Users, teams and groups" configuration page'), 'target_id' => 2));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Modules" and any module configuration page'), 'target_id' => 15));
				self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Modules" configuration page and any modules'), 'target_id' => 15));
				self::$_available_permissions['general']['canfindissuesandsavesearches'] = array('description' => $i18n->__('Can search for issues and create saved searches'), 'details' => array());
				self::$_available_permissions['general']['canfindissuesandsavesearches']['details']['canfindissues'] = array('description' => $i18n->__('Can search for issues'));
				//self::$_available_permissions['general']['canfindissuesandsavesearches']['details']['cancreatesavedsearches'] = array('description' => $i18n->__('Can create saved searches'));
				self::$_available_permissions['general']['canfindissuesandsavesearches']['details']['cancreatepublicsearches'] = array('description' => $i18n->__('Can create saved searches that are public'));
				self::$_available_permissions['general']['caneditmainmenu'] = array('description' => $i18n->__('Can edit main menu'));
				self::$_available_permissions['pages']['page_home_access'] = array('description' => $i18n->__('Can access the frontpage'));
				self::$_available_permissions['pages']['page_dashboard_access'] = array('description' => $i18n->__('Can access the user dashboard'));
				self::$_available_permissions['pages']['page_search_access'] = array('description' => $i18n->__('Can access the search page'));
				self::$_available_permissions['pages']['page_about_access'] = array('description' => $i18n->__('Can access the "About" page'));
				self::$_available_permissions['pages']['page_account_access'] = array('description' => $i18n->__('Can access the "My account" page'));
				self::$_available_permissions['pages']['page_teamlist_access'] = array('description' => $i18n->__('Can see list of teams in header menu'));
				self::$_available_permissions['pages']['page_clientlist_access'] = array('description' => $i18n->__('Can see list of clients in header menu'));
				self::$_available_permissions['project_pages']['page_project_allpages_access'] = array('description' => $i18n->__('Can access all project pages'), 'details' => array());
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_dashboard_access'] = array('description' => $i18n->__('Can access the project dashboard'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_planning_access'] = array('description' => $i18n->__('Can access the project planning page'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_scrum_access'] = array('description' => $i18n->__('Can access the project scrum page'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_issues_access'] = array('description' => $i18n->__('Can access the project issues search page'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_roadmap_access'] = array('description' => $i18n->__('Can access the project roadmap page'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_team_access'] = array('description' => $i18n->__('Can access the project team page'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_statistics_access'] = array('description' => $i18n->__('Can access the project statistics page'));
				self::$_available_permissions['project_pages']['page_project_allpages_access']['details']['page_project_timeline_access'] = array('description' => $i18n->__('Can access the project timeline page'));
				self::$_available_permissions['project']['canseeproject'] = array('description' => $i18n->__('Can see that project exists'));
				self::$_available_permissions['project']['canseeprojecthierarchy'] = array('description' => $i18n->__('Can see complete project hierarchy'));
				self::$_available_permissions['project']['canseeprojecthierarchy']['details']['canseeallprojecteditions'] = array('description' => $i18n->__('Can see all editions'));
				self::$_available_permissions['project']['canseeprojecthierarchy']['details']['canseeallprojectcomponents'] = array('description' => $i18n->__('Can see all components'));
				self::$_available_permissions['project']['canseeprojecthierarchy']['details']['canseeallprojectbuilds'] = array('description' => $i18n->__('Can see all release'));
				self::$_available_permissions['project']['canseeprojecthierarchy']['details']['canseeallprojectmilestones'] = array('description' => $i18n->__('Can see all milestones'));
				self::$_available_permissions['project']['candoscrumplanning'] = array('description' => $i18n->__('Can manage stories, tasks, sprints and backlog on the sprint planning page'), 'details' => array());
				self::$_available_permissions['project']['candoscrumplanning']['details']['canaddscrumuserstories'] = array('description' => $i18n->__('Can add new user stories to the backlog on the sprint planning page'));
				self::$_available_permissions['project']['candoscrumplanning']['details']['candoscrumplanning_backlog'] = array('description' => $i18n->__('Can manage the backlog on the sprint planning page'));
				self::$_available_permissions['project']['candoscrumplanning']['details']['canaddscrumsprints'] = array('description' => $i18n->__('Can add sprints on the sprint planning page'));
				self::$_available_permissions['project']['candoscrumplanning']['details']['canassignscrumuserstoriestosprints'] = array('description' => $i18n->__('Can add stories to sprints on the sprint planning page'));
				self::$_available_permissions['project']['canmanageproject'] = array('description' => $i18n->__('Can manage project'));
				self::$_available_permissions['project']['canmanageproject']['details']['canmanageprojectreleases'] = array('description' => $i18n->__('Can manage project releases and components'));
				self::$_available_permissions['project']['canmanageproject']['details']['caneditprojectdetails'] = array('description' => $i18n->__('Can edit project details and settings'));
				self::$_available_permissions['edition']['canseeedition'] = array('description' => $i18n->__('Can see this edition'));
				self::$_available_permissions['component']['canseecomponent'] = array('description' => $i18n->__('Can see this component'));
				self::$_available_permissions['build']['canseebuild'] = array('description' => $i18n->__('Can see this release'));
				self::$_available_permissions['milestone']['canseemilestone'] = array('description' => $i18n->__('Can see this milestone'));
				self::$_available_permissions['issues']['canvoteforissues'] = array('description' => $i18n->__('Can vote for issues'));
				self::$_available_permissions['issues']['canlockandeditlockedissues'] = array('description' => $i18n->__('Can lock and edit locked issues'));
				self::$_available_permissions['issues']['cancreateandeditissues'] = array('description' => $i18n->__('Can create issues, edit basic information on issues reported by the user and close/re-open them'), 'details' => array());
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['cancreateissues'] = array('description' => $i18n->__('Can create new issues'), 'details' => array());
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['caneditissuebasicown'] = array('description' => $i18n->__('Can edit title and description on issues reported by the user'), 'details' => array());
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['caneditissuebasicown']['details']['caneditissuetitleown'] = array('description' => $i18n->__('Can edit issue title on issues reported by the user'));
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['caneditissuebasicown']['details']['caneditissuedescriptionown'] = array('description' => $i18n->__('Can edit issue description on issues reported by the user'));
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['caneditissuebasicown']['details']['caneditissuereproduction_stepsown'] = array('description' => $i18n->__('Can edit steps to reproduce on issues reported by the user'));
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['canclosereopenissuesown'] = array('description' => $i18n->__('Can close and reopen issues reported by the user'), 'details' => array());
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['canclosereopenissuesown']['details']['cancloseissuesown'] = array('description' => $i18n->__('Can close issues reported by the user'));
				self::$_available_permissions['issues']['cancreateandeditissues']['details']['canclosereopenissuesown']['details']['canreopenissuesown'] = array('description' => $i18n->__('Can re-open issues reported by the user'));
				self::$_available_permissions['issues']['caneditissue'] = array('description' => $i18n->__('Can delete, close, reopen and update any issue details and progress'), 'details' => array());
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuebasic'] = array('description' => $i18n->__('Can edit title and description on any issues'), 'details' => array());
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuebasic']['details']['caneditissuetitle'] = array('description' => $i18n->__('Can edit any issue title'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuebasic']['details']['caneditissuedescription'] = array('description' => $i18n->__('Can edit any issue description'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuebasic']['details']['caneditissuereproduction_steps'] = array('description' => $i18n->__('Can edit any issue steps to reproduce'));
				self::$_available_permissions['issues']['caneditissue']['details']['candeleteissues'] = array('description' => $i18n->__('Can delete issues'));
				self::$_available_permissions['issues']['caneditissue']['details']['canclosereopenissues'] = array('description' => $i18n->__('Can close any issues'));
				self::$_available_permissions['issues']['caneditissue']['details']['canclosereopenissues']['details']['cancloseissues'] = array('description' => $i18n->__('Can close any issues'));
				self::$_available_permissions['issues']['caneditissue']['details']['canclosereopenissues']['details']['canreopenissues'] = array('description' => $i18n->__('Can re-open any issues'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissueposted_by'] = array('description' => $i18n->__('Can edit issue posted by'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissueowned_by'] = array('description' => $i18n->__('Can edit issue owned by'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissueassigned_to'] = array('description' => $i18n->__('Can edit issue assigned_to'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuestatus'] = array('description' => $i18n->__('Can edit issue status'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuecategory'] = array('description' => $i18n->__('Can edit issue category'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuepriority'] = array('description' => $i18n->__('Can edit issue priority'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissueseverity'] = array('description' => $i18n->__('Can edit issue severity'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuereproducability'] = array('description' => $i18n->__('Can edit issue reproducability'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissueresolution'] = array('description' => $i18n->__('Can edit issue resolution'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissueestimated_time'] = array('description' => $i18n->__('Can estimate issues'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuespent_time'] = array('description' => $i18n->__('Can spend time working on issues'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuepercent_complete'] = array('description' => $i18n->__('Can edit issue percent complete'));
				self::$_available_permissions['issues']['caneditissue']['details']['caneditissuemilestone'] = array('description' => $i18n->__('Can set issue milestone'));
				self::$_available_permissions['issues']['caneditissuecustomfieldsown'] = array('description' => $i18n->__('Can change custom field values for issues reported by the user'), 'details' => array());
				self::$_available_permissions['issues']['caneditissuecustomfields'] = array('description' => $i18n->__('Can change custom field values for any issues'), 'details' => array());
				foreach (TBGCustomDatatype::getAll() as $cdf)
				{
					self::$_available_permissions['issues']['caneditissuecustomfieldsown']['details']['caneditissuecustomfields'.$cdf->getKey().'own'] = array('description' => $i18n->__('Can change custom field "%field_name%" for issues reported by the user', array('%field_name%' => $cdf->getDescription())));
					self::$_available_permissions['issues']['caneditissuecustomfields']['details']['caneditissuecustomfields'.$cdf->getKey()] = array('description' => $i18n->__('Can change custom field "%field_name%" for any issues', array('%field_name%' => $cdf->getDescription())));
				}
				self::$_available_permissions['issues']['canaddextrainformationtoissues'] = array('description' => $i18n->__('Can add/remove extra information (edition, component, release, links and files) to issues'), 'details' => array());
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddbuildsown'] = array('description' => $i18n->__('Can add releases / versions to list of affected versions for issues reported by the user'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddbuilds'] = array('description' => $i18n->__('Can add releases / versions to list of affected versions for any issues'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddcomponentsown'] = array('description' => $i18n->__('Can add components to list of affected components for issues reported by the user'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddcomponents'] = array('description' => $i18n->__('Can add components to list of affected components for any issues'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddeditionsown'] = array('description' => $i18n->__('Can add editions to list of affected editions for issues reported by the user'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddeditions'] = array('description' => $i18n->__('Can add editions to list of affected editions for any issues'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddlinkstoissuesown'] = array('description' => $i18n->__('Can add links to issues reported by the user'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddlinkstoissues'] = array('description' => $i18n->__('Can add links to any issues'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddfilestoissuesown'] = array('description' => $i18n->__('Can add files to and remove own files from issues reported by the user'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddfilestoissues'] = array('description' => $i18n->__('Can add files to and remove own files from any issues'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canremovefilesfromissuesown'] = array('description' => $i18n->__('Can remove any attachments from issues reported by the user'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canremovefilesfromissues'] = array('description' => $i18n->__('Can remove any attachments from any issues'));
				self::$_available_permissions['issues']['canaddextrainformationtoissues']['details']['canaddrelatedissues'] = array('description' => $i18n->__('Can add related issues to other issues'));
				self::$_available_permissions['issues']['canpostandeditcomments'] = array('description' => $i18n->__('Can see public comments, post new, edit own and delete own comments'), 'details' => array());
				self::$_available_permissions['issues']['canpostandeditcomments']['details']['canviewcomments'] = array('description' => $i18n->__('Can see public comments'));
				self::$_available_permissions['issues']['canpostandeditcomments']['details']['canpostcomments'] = array('description' => $i18n->__('Can post comments'));
				self::$_available_permissions['issues']['canpostandeditcomments']['details']['caneditcommentsown'] = array('description' => $i18n->__('Can edit own comments'));
				self::$_available_permissions['issues']['canpostandeditcomments']['details']['candeletecommentsown'] = array('description' => $i18n->__('Can delete own comments'));
				self::$_available_permissions['issues']['canpostseeandeditallcomments'] = array('description' => $i18n->__('Can see all comments (including non-public), post new, edit and delete all comments'), 'details' => array());
				self::$_available_permissions['issues']['canpostseeandeditallcomments']['details']['canseenonpubliccomments'] = array('description' => $i18n->__('Can see all comments including hidden'));
				self::$_available_permissions['issues']['canpostseeandeditallcomments']['details']['caneditcomments'] = array('description' => $i18n->__('Can edit all comments'));
				self::$_available_permissions['issues']['canpostseeandeditallcomments']['details']['candeletecomments'] = array('description' => $i18n->__('Can delete any comments'));
				self::$_available_permissions['pages']['page_account_access']['details']['canchangepassword'] = array('description' => $i18n->__('Can change own password'), 'mode' => 'permissive');
				//self::trigger('core', 'cachepermissions', array('permissions' => &self::$_available_permissions));
			}
		}
		
		/**
		 * Returns all permissions available for a specific identifier
		 *  
		 * @param string $applies_to The identifier
		 * 
		 * @return array
		 */
		public static function getAvailablePermissions($applies_to = null)
		{
			self::_cacheAvailablePermissions();
			if ($applies_to === null)
			{
				$list = self::$_available_permissions;
				$retarr = array();
				foreach ($list as $key => $details)
				{
					foreach ($details as $dkey => $dd)
					{
						$retarr[$dkey] = $dd;
					}
				}
				foreach (TBGContext::getModules() as $module_key => $module)
				{
					$retarr['module_'.$module_key] = array();
					foreach ($module->getAvailablePermissions() as $mpkey => $mp)
					{
						$retarr['module_'.$module_key][$mpkey] = $mp;
					}
				}
				return $retarr;
			}
			if (array_key_exists($applies_to, self::$_available_permissions))
			{
				return self::$_available_permissions[$applies_to];
			}
			elseif (substr($applies_to, 0, 7) == 'module_')
			{
				$module_name = substr($applies_to, 7);
				if (self::isModuleLoaded($module_name))
				{
					return self::getModule($module_name)->getAvailablePermissions();
				}
			}
			else
			{
				return array();
			}
		}
		
		public static function getProjectAssigneeDefaultPermissionSet($ownable, $type)
		{
			$return_values = array();
			if ($ownable instanceof TBGProject)
			{
				$return_values[] = 'page_project_allpages_access';
				$return_values[] = 'canseeproject';
				$return_values[] = 'canseeprojecthierarchy';
				$return_values[] = 'cancreateandeditissues';
				$return_values[] = 'canpostandeditcomments';
			}
			elseif ($ownable instanceof TBGEdition)
			{
				$return_values[] = 'canseeedition';
			}
			elseif ($ownable instanceof TBGComponent)
			{
				$return_values[] = 'canseecomponent';
			}
			
			switch ($type)
			{
				case '_leader':
					$return_values[] = 'canmanageproject';
					$return_values[] = 'candoscrumplanning';
					break;
				case '_owner':
					$return_values[] = 'canmanageproject';
					$return_values[] = 'candoscrumplanning';
					break;
				case '_qa_responsible':
					$return_values[] = 'candoscrumplanning';
					$return_values[] = 'caneditissue';
					$return_values[] = 'caneditissuecustomfields';
					$return_values[] = 'canaddextrainformationtoissues';
					break;
				case TBGProjectAssigneesTable::TYPE_DEVELOPER:
					$return_values[] = 'candoscrumplanning';
					$return_values[] = 'caneditissue';
					$return_values[] = 'caneditissuecustomfields';
					$return_values[] = 'canaddextrainformationtoissues';
					break;
				case TBGProjectAssigneesTable::TYPE_PROJECTMANAGER:
					$return_values[] = 'candoscrumplanning';
					$return_values[] = 'caneditissue';
					$return_values[] = 'caneditissuecustomfields';
					$return_values[] = 'canaddextrainformationtoissues';
					break;
				case TBGProjectAssigneesTable::TYPE_TESTER:
					$return_values[] = 'caneditissue';
					$return_values[] = 'caneditissuecustomfields';
					$return_values[] = 'canaddextrainformationtoissues';
					break;
				case TBGProjectAssigneesTable::TYPE_DOCUMENTOR:
					$return_values[] = 'caneditissue';
					$return_values[] = 'caneditissuecustomfields';
					$return_values[] = 'canaddextrainformationtoissues';
					break;
			}
			
			return $return_values;
		}
		
		/**
		 * Log out the current user (does not work when auth method is set to http)
		 */
		public static function logout()
		{
			self::getResponse()->deleteCookie('tbg3_username');
			self::getResponse()->deleteCookie('tbg3_password');
			self::getResponse()->deleteCookie('THEBUGGENIE');
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
				TBGLogging::log("Setting scope from function parameter");
				self::$_scope = $scope;
				TBGSettings::forceSettingsReload();
				TBGLogging::log("...done (Setting scope from function parameter)");
				return true;
			}
	
			$hostprefix = (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
			try
			{
				if (!class_exists('TBGScopesTable')) throw new Exception('B2DB not configured to look for B2DB table classes');
				if (!self::isCLI())
				{
					TBGLogging::log("Checking if scope can be set from hostname (".$hostprefix.$_SERVER['HTTP_HOST'].")");
					$row = TBGScopesTable::getTable()->getByHostname($hostprefix . $_SERVER['HTTP_HOST']);
				}
				else
				{
					$row = TBGScopesTable::getTable()->getDefault();
				}
				if ($row instanceof B2DBRow)
				{
					TBGLogging::log("It could");
					TBGLogging::log("Setting scope from hostname");
					$theScope = TBGContext::factory()->TBGScope($row->get(TBGScopesTable::ID), $row);
					self::$_scope = $theScope;
					TBGSettings::forceSettingsReload();
					TBGLogging::log("...done (Setting scope from hostname)");
					return true;
				}
				TBGLogging::log("It couldn't", 'main', TBGLogging::LEVEL_WARNING);
				throw new Exception("The Bug Genie isn't set up to work with this server name.");
			}
			catch (Exception $e)
			{
				if (self::isCLI())
				{
					TBGLogging::log("Couldn't set up default scope.", 'main', TBGLogging::LEVEL_FATAL);
					throw new Exception("Could not load default scope. Error message was: " . $e->getMessage());
				}
				else
				{
					TBGLogging::log("Couldn't find a scope for hostname ".$hostprefix . $_SERVER['HTTP_HOST'], 'main', TBGLogging::LEVEL_FATAL);
					TBGLogging::log($e->getMessage(), 'main', TBGLogging::LEVEL_FATAL);
					throw new Exception("Could not load scope. This is usually because the scopes table doesn't have a scope for this hostname");
				}
			}
		}

		/**
		 * Returns current scope
		 *
		 * @return TBGScope
		 */
		public static function getScope()
		{
			return self::$_scope;
		}
		
		/**
		 * Set the currently selected project
		 * 
		 * @param TBGProject $project The project, or null if none
		 */
		public static function setCurrentProject($project)
		{
			self::$_selected_project = $project;
			if (self::$_selected_project instanceof TBGProject && self::$_selected_project->hasClient())
			{
				self::setCurrentClient(self::$_selected_project->getClient());
			}
			
		}
		
		/**
		 * Return the currently selected project if any, or null
		 * 
		 * @return TBGProject
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
			return (bool) (self::getCurrentProject() instanceof TBGProject);
		}
		
		/**
		 * Set the currently selected client
		 * 
		 * @param TBGClient $client The client, or null if none
		 */
		public static function setCurrentClient($client)
		{
			self::$_selected_client = $client;
		}
		
		/**
		 * Return the currently selected client if any, or null
		 * 
		 * @return TBGClient
		 */
		public static function getCurrentClient()
		{
			return self::$_selected_client;
		}

		/**
		 * Return whether current client is set
		 *
		 * @return boolean
		 */
		public static function isClientContext()
		{
			return (bool) (self::getCurrentClient() instanceof TBGClient);
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

		protected static function _setupMessages()
		{
			if (self::$_messages === null)
			{
				self::$_messages = array();
				if (array_key_exists('tbg_message', $_SESSION))
				{
					self::$_messages = $_SESSION['tbg_message'];
					unset($_SESSION['tbg_message']);
				}
			}
		}

		/**
		 * Whether or not there is a message in the next request
		 * 
		 * @return boolean
		 */
		public static function hasMessage($key)
		{
			self::_setupMessages();
			return array_key_exists($key, self::$_messages);
		}
		
		/**
		 * Retrieve a message passed on from the previous request
		 *
		 * @param string $key A message identifier
		 *
		 * @return string
		 */
		public static function getMessage($key)
		{
			return (self::hasMessage($key)) ? self::$_messages[$key] : null;
		}
		
		/**
		 * Clear the message
		 */
		public static function clearMessage($key)
		{
			if (self::hasMessage($key))
			{
				unset(self::$_messages[$key]);
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

		public static function generateCSRFtoken()
		{
			if (!array_key_exists('csrf_token', $_SESSION) || $_SESSION['csrf_token'] == '')
			{
				$_SESSION['csrf_token'] = str_replace('.', '_', uniqid(rand(), TRUE));
			}
			return $_SESSION['csrf_token'];
		}

		public static function checkCSRFtoken($handle_response = false)
		{
			$token = self::generateCSRFtoken();
			if ($token == self::getRequest()->getParameter('csrf_token')) return true;

			$message = self::getI18n()->__('An authentication error occured. Please reload your page and try again');
			/*if ($handle_response)
			{
				self::$_response->setHttpStatus(301);
				if (self::getRequest()->getRequestedFormat() == 'json')
				{
					self::$_response->setContentType('application/json');
					echo json_encode(array('message' => $message));
				}
				else
				{
					echo $message;
				}
			}
			else
			{*/
				throw new TBGCSRFFailureException($message);
			//}
			return false;
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
					TBGLogging::log("The \"{$lib_name}\" library does not exist in either " . self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/ or ' . self::getIncludePath() . "core/lib/", 'core', TBGLogging::LEVEL_FATAL);
					throw new TBGLibraryNotFoundException("The \"{$lib_name}\" library does not exist in either " . self::getIncludePath() . 'modules/' . self::getRouting()->getCurrentRouteModule() . '/lib/ or ' . self::getIncludePath() . "core/lib/");
				}
			}
		}
		
		public static function visitPartial($template_name, $time)
		{
			if (!self::$debug_mode) return;
			if (!array_key_exists($template_name, self::$_partials_visited))
			{
				self::$_partials_visited[$template_name] = array('time' => $time, 'count' => 1);
			}
			else
			{
				self::$_partials_visited[$template_name]['count']++;
				self::$_partials_visited[$template_name]['time'] += $time;
			}
		}
		
		public static function getVisitedPartials()
		{
			return self::$_partials_visited;
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
			self::getResponse()->setTemplate(strtolower($method) . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php');
			self::getResponse()->setupResponseContentType(self::getRequest()->getRequestedFormat());
			
			// Set up the action object
			$actionObject = new $actionClassName();

			// Run the specified action method set if it exists
			if (method_exists($actionObject, $actionToRunName))
			{
				// Turning on output buffering
				ob_start();
				ob_implicit_flush(0);

				if (self::getRouting()->isCurrentRouteCSRFenabled())
				{
					// If the csrf check fails, don't proceed
					if (!self::checkCSRFtoken(true))
					{
						return true;
					}
				}

				if (self::$debug_mode)
				{
					$time = explode(' ', microtime());
					$pretime = $time[1] + $time[0];
				}
				if ($content === null)
				{
					TBGLogging::log('Running main pre-execute action');
					// Running any overridden preExecute() method defined for that module
					// or the default empty one provided by TBGAction
					if ($pre_action_retval = $actionObject->preExecute(self::getRequest(), $method))
					{
						$content = ob_get_clean();
						TBGLogging::log('preexecute method returned something, skipping further action');
						if (self::$debug_mode) $visited_templatename = "{$actionObject}::preExecute()";
					}
				}

				if ($content === null)
				{
					if (self::getResponse()->getHttpStatus() == 200)
					{
						// Checking for and running action-specific preExecute() function if
						// it exists
						if (method_exists($actionObject, $preActionToRunName))
						{
							TBGLogging::log('Running custom pre-execute action');
							$actionObject->$preActionToRunName(self::getRequest(), $method);
						}
					}

					// Running main route action
					TBGLogging::log('Running route action '.$actionToRunName.'()');
					if (self::$debug_mode)
					{
						$time = explode(' ', microtime());
						$action_pretime = $time[1] + $time[0];
					}
					$action_retval = $actionObject->$actionToRunName(self::getRequest());
					if (self::$debug_mode)
					{
						$time = explode(' ', microtime());
						$action_posttime = $time[1] + $time[0];
						TBGContext::visitPartial("{$actionClassName}::{$actionToRunName}", $action_posttime - $action_pretime);					
					}
					if (self::getResponse()->getHttpStatus() == 200 && $action_retval)
					{
						// If the action returns *any* output, we're done, and collect the
						// output to a variable to be outputted in context later
						$content = ob_get_clean();
						TBGLogging::log('...done');
					}
					else
					{
						// If the action doesn't return any output (which it usually doesn't)
						// we continue on to rendering the template file for that specific action
						TBGLogging::log('...done');
						TBGLogging::log('Displaying template');

						// Check to see if we have a translated version of the template
						if (($templateName = self::getI18n()->hasTranslatedTemplate(self::getResponse()->getTemplate())) === false)
						{
							// Check to see if the template has been changed, and whether it's in a
							// different module, specified by "module/templatename"
							if (strpos(self::getResponse()->getTemplate(), '/'))
							{
								$newPath = explode('/', self::getResponse()->getTemplate());
								$templateName = self::getIncludePath() . 'modules/' . $newPath[0] . '/templates/' . $newPath[1] . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php';
							}
							else
							{
								$templateName = $templatePath . self::getResponse()->getTemplate();
							}
						}

						// Check to see if the template exists and throw an exception otherwise
						if (!file_exists($templateName))
						{
							TBGLogging::log('The template file for the ' . $method . ' action ("'.self::getResponse()->getTemplate().'") does not exist', 'core', TBGLogging::LEVEL_FATAL);
							throw new TBGTemplateNotFoundException('The template file for the ' . $method . ' action ("'.self::getResponse()->getTemplate().'") does not exist');
						}

						self::loadLibrary('common');
						// Present template for current action
						TBGActionComponent::presentTemplate($templateName, $actionObject->getParameterHolder());
						$content = ob_get_clean();
						TBGLogging::log('...completed');
					}
				}
				elseif (self::$debug_mode)
				{
					$time = explode(' ', microtime());
					$posttime = $time[1] + $time[0];
					TBGContext::visitPartial($visited_templatename, $posttime - $pretime);					
				}

				if (!isset($tbg_response))
				{
					/**
					 * @global TBGRequest The request object
					 */
					$tbg_request = self::getRequest();

					/**
					 * @global TBGUser The user object
					 */
					$tbg_user = self::getUser();

					/**
					 * @global TBGResponse The action object
					 */
					$tbg_response = self::getResponse();

					// Load the "ui" library, since this is used a lot
					self::loadLibrary('ui');
				}

				self::loadLibrary('common');
				
				// Render header template if any, and store the output in a variable
				ob_start();
				ob_implicit_flush(0);
				if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateHeader())
				{
					TBGLogging::log('decorating with header');
					require self::getResponse()->getHeaderDecoration();
					$decoration_header = ob_get_clean();
				}

				// Set up the run summary, and store it in a variable
				ob_start();
				ob_implicit_flush(0);
				$load_time = self::getLoadtime();
				if (class_exists('B2DB'))
				{
					$tbg_summary['db_queries'] = B2DB::getSQLHits();
					$tbg_summary['db_timing'] = B2DB::getSQLTiming();
				}
				$tbg_summary['load_time'] = ($load_time >= 1) ? round($load_time, 2) . ' seconds' : round($load_time * 1000, 1) . 'ms';
				$tbg_summary['scope_id'] = self::getScope() instanceof TBGScope ? self::getScope()->getID() : 'unknown';
				self::ping();

				// Render footer template if any, and store the output in a variable
				if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateFooter())
				{
					TBGLogging::log('decorating with footer');
					require self::getResponse()->getFooterDecoration();
					$decoration_footer = ob_get_clean();
				}
				TBGLogging::log('...done');
				TBGLogging::log('rendering content');

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
				TBGLogging::log('...done (rendering content)');

				//self::getI18n()->addMissingStringsToStringsFile();
				
				return true;
			}
			else
			{
				TBGLogging::log("Cannot find the method {$actionToRunName}() in class {$actionClassName}.", 'core', TBGLogging::LEVEL_FATAL);
				throw new TBGActionNotFoundException("Cannot find the method {$actionToRunName}() in class {$actionClassName}. Make sure the method exists.");
			}
		}
		
		/**
		 * Returns all the links on the frontpage
		 * 
		 * @return array
		 */
		public static function getMainLinks()
		{
			if (!$links = TBGCache::get('core_main_links'))
			{
				$links = B2DB::getTable('TBGLinksTable')->getMainLinks();
				TBGCache::add('core_main_links', $links);
			}
			return $links;
		}
		
		/**
		 * Launches the MVC framework
		 */
		public static function go()
		{
			TBGLogging::log('Dispatching');
			try
			{
				if (($route = self::getRouting()->getRouteFromUrl(self::getRequest()->getParameter('url', null, false)))  || self::isInstallmode())
				{
					if (self::isInstallmode())
					{
						$route = array('module' => 'installation', 'action' => 'installIntro');
					}
					if (self::$_redirect_login)
					{
						TBGLogging::log('An error occurred setting up the user object, redirecting to login', 'main', TBGLogging::LEVEL_NOTICE);
						self::getResponse()->headerRedirect(self::getRouting()->generate('login_redirect'), 403);
					}
					if (is_dir(self::getIncludePath() . 'modules/' . $route['module']))
					{
						if (!file_exists(self::getIncludePath() . 'modules/' . $route['module'] . '/classes/actions.class.php'))
						{
							throw new TBGActionNotFoundException('The ' . $route['module'] . ' module is missing the classes/actions.class.php file, containing all the module actions');
						}
						if (!class_exists($route['module'].'Actions') && !class_exists($route['module'].'ActionComponents'))
						{
							self::addClasspath(self::getIncludePath() . 'modules/' . $route['module'] . '/classes/');
						}
						if (self::performAction($route['module'], $route['action']))
						{
							if (class_exists('B2DB'))
							{
								B2DB::closeDBLink();
							}
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
					require self::getIncludePath() . 'modules/main/classes/actions.class.php';
					self::performAction('main', 'notFound');
				}
			}
			catch (TBGTemplateNotFoundException $e)
			{
				B2DB::closeDBLink();
				TBGContext::setLoadedAt();
				header("HTTP/1.0 404 Not Found", true, 404);
				tbg_exception('Template file does not exist for current action', $e);
			}
			catch (TBGActionNotFoundException $e)
			{
				B2DB::closeDBLink();
				TBGContext::setLoadedAt();
				header("HTTP/1.0 404 Not Found", true, 404);
				tbg_exception('Module action "' . $route['action'] . '" does not exist for module "' . $route['module'] . '"', $e);
			}
			catch (TBGCSRFFailureException $e)
			{
				B2DB::closeDBLink();
				TBGContext::setLoadedAt();
				self::$_response->setHttpStatus(301);
				$message = $e->getMessage();

				if (self::getRequest()->getRequestedFormat() == 'json')
				{
					self::$_response->setContentType('application/json');
					$message = json_encode(array('message' => $message));
				}

				self::$_response->renderHeaders();
				echo $message;
			}
			catch (Exception $e)
			{
				B2DB::closeDBLink();
				TBGContext::setLoadedAt();
				header("HTTP/1.0 404 Not Found", true, 404);
				tbg_exception('An error occured', $e);
			}
		}

		public static function getURLhost()
		{
			return self::getScope()->getHostname();
		}

		public static function getMimeType($filename)
		{
			if (function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
				$content_type = finfo_file($finfo, $filename);
				finfo_close($finfo);
			}
			else
			{
				$content_type = mime_content_type($filename);
			}
			return $content_type;
		}

		public static function isCLI()
		{
			return (PHP_SAPI == 'cli');
		}

		public static function getCurrentCLIusername()
		{
			$processUser = posix_getpwuid(posix_geteuid());
			return $processUser['name'];
		}

		public static function isDebugMode()
		{
			return self::$debug_mode;
		}

	}
	