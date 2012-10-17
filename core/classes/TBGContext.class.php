<?php

	/**
	 * The core class of the B2 engine
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
		const PREDEFINED_SEARCH_PROJECT_WISHLIST = 10;
		const PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO = 6;
		const PREDEFINED_SEARCH_PROJECT_MOST_VOTED = 7;
		const PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH = 8;
		const PREDEFINED_SEARCH_PROJECT_REPORTED_LAST_NUMBEROF_TIMEUNITS = 9;
		const PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES = 3;
		const PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES = 4;
		const PREDEFINED_SEARCH_MY_REPORTED_ISSUES = 5;
		const PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES = 11;
		
		protected static $_environment = 2;

		protected static $_debug_mode = true;

		protected static $debug_id = null;
		
		protected static $_partials_visited = array();
		
		/**
		 * Outdated modules
		 * 
		 * @var array
		 */
		protected static $_outdated_modules = null;

		/**
		 * The current user
		 *
		 * @var TBGUser
		 */
		protected static $_user = null;
		
		/**
		 * List of modules 
		 * 
		 * @var array
		 */
		protected static $_modules = array();
		
		/**
		 * List of permissions
		 *  
		 * @var array
		 */
		protected static $_permissions = array();
		
		/**
		 * List of available permissions
		 * 
		 * @var array
		 */
		protected static $_available_permissions = null;
		
		/**
		 * The include path
		 * 
		 * @var string
		 */
		protected static $_includepath = null;
		
		/**
		 * The path to thebuggenie relative from url server root
		 * 
		 * @var string
		 */
		protected static $_tbgpath = null;
		
		/**
		 * Stripped version of the $_tbgpath
		 * 
		 * @see $_tbgpath
		 * 
		 * @var string
		 */
		protected static $_stripped_tbgpath = null;
		
		/**
		 * Whether we're in installmode or not
		 * 
		 * @var boolean
		 */
		protected static $_installmode = false;
		
		/**
		 * Whether we're in upgrademode or not
		 * 
		 * @var boolean
		 */
		protected static $_upgrademode = false;
		
		/**
		 * The i18n object
		 *
		 * @var TBGI18n
		 */
		protected static $_i18n = null;
		
		/**
		 * The request object
		 * 
		 * @var TBGRequest
		 */
		protected static $_request = null;
		
		/**
		 * The response object
		 * 
		 * @var TBGResponse
		 */
		protected static $_response = null;
		
		/**
		 * The current scope object
		 *
		 * @var TBGScope
		 */
		protected static $_scope = null;

		/**
		 * The TBGFactory instance
		 *
		 * @var TBGFactory
		 */
		protected static $_factory = null;
		
		/**
		 * The currently selected project, if any
		 * 
		 * @var TBGProject
		 */
		protected static $_selected_project = null;
		
		/**
		 * The currently selected client, if any
		 * 
		 * @var TBGClient
		 */
		protected static $_selected_client = null;
		
		/**
		 * Used to determine when the b2 engine started loading
		 * 
		 * @var integer
		 */
		protected static $_loadstart = null;
		
		/**
		 * List of classpaths
		 * 
		 * @var array
		 */
		protected static $_classpaths = array();
		
		/**
		 * List of loaded libraries
		 * 
		 * @var string
		 */
		protected static $_libs = array();
		
		/**
		 * The routing object
		 * 
		 * @var TBGRouting
		 */
		protected static $_routing = null;

		/**
		 * Messages passed on from the previous request
		 *
		 * @var array
		 */
		protected static $_messages = null;

		protected static $_redirect_login = null;
		
		/**
		 * Do you want to enable minifcation of javascript and css?
		 * 
		 * @var boolean
		 */
		protected static $_minify_enabled = false;

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
		 * Returns whether or minify is enabled
		 * 
		 * @return boolean
		 */
		public static function isMinifyEnabled()
		{
			return self::$_minify_enabled;
		}

		public static function setMinifyEnabled($value = true)
		{
			self::$_minify_enabled = $value;
		}

		/**
		 * Returns whether or not we're in upgrade mode
		 * 
		 * @return boolean
		 */
		public static function isUpgrademode()
		{
			return self::$_upgrademode;
		}

		protected static function cliError($title, $exception)
		{
			$trace_elements = null;
			if ($exception instanceof Exception)
			{
				if ($exception instanceof TBGActionNotFoundException)
				{
					TBGCliCommand::cli_echo("Could not find the specified action\n", 'white', 'bold');
				}
				elseif ($exception instanceof TBGTemplateNotFoundException)
				{
					TBGCliCommand::cli_echo("Could not find the template file for the specified action\n", 'white', 'bold');
				}
				elseif ($exception instanceof \b2db\Exception)
				{
					TBGCliCommand::cli_echo("An exception was thrown in the B2DB framework\n", 'white', 'bold');
				}
				else
				{
					TBGCliCommand::cli_echo("An unhandled exception occurred:\n", 'white', 'bold');
				}
				echo TBGCliCommand::cli_echo($exception->getMessage(), 'red', 'bold')."\n";
				echo "\n";
				TBGCliCommand::cli_echo('Stack trace').":\n";
				$trace_elements = $exception->getTrace();
			}
			else
			{
				if ($exception['code'] == 8)
				{
					TBGCliCommand::cli_echo('The following notice has stopped further execution:', 'white', 'bold');
				}
				else
				{
					TBGCliCommand::cli_echo('The following error occured:', 'white', 'bold');
				}
				echo "\n";
				echo "\n";
				TBGCliCommand::cli_echo($title, 'red', 'bold');
				echo "\n";
				TBGCliCommand::cli_echo("occured in\n");
				TBGCliCommand::cli_echo($exception['file'].', line '.$exception['line'], 'blue', 'bold');
				echo "\n";
				echo "\n";
				TBGCliCommand::cli_echo("Backtrace:\n", 'white', 'bold');
				$trace_elements = debug_backtrace();
			}
			foreach ($trace_elements as $trace_element)
			{
				if (array_key_exists('class', $trace_element))
				{
					if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'TBGContext' && array_key_exists('function', $trace_element) && in_array($trace_element['function'], array('errorHandler', 'cliError'))) continue;
					TBGCliCommand::cli_echo($trace_element['class'].$trace_element['type'].$trace_element['function'].'()');
				}
				elseif (array_key_exists('function', $trace_element))
				{
					TBGCliCommand::cli_echo($trace_element['function'].'()');
				}
				else
				{
					TBGCliCommand::cli_echo('unknown function');
				}
				echo "\n";
				if (array_key_exists('file', $trace_element))
				{
					TBGCliCommand::cli_echo($trace_element['file'].', line '.$trace_element['line'], 'blue', 'bold');
				}
				else
				{
					TBGCliCommand::cli_echo('unknown file', 'red', 'bold');
				}
				echo "\n";
			}
			if (class_exists('\\b2db\\Core'))
			{
				echo "\n";
				$sqlhits = \b2db\Core::getSQLHits();
				if (count($sqlhits))
				{
					TBGCliCommand::cli_echo("SQL queries:\n", 'white', 'bold');
					try
					{
						$cc = 1;
						foreach ($sqlhits as $details)
						{
							TBGCliCommand::cli_echo("(".$cc++.") [");
							$str = ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms';
							TBGCliCommand::cli_echo($str);
							TBGCliCommand::cli_echo("] from ");
							TBGCliCommand::cli_echo($details['filename'], 'blue');
							TBGCliCommand::cli_echo(", line ");
							TBGCliCommand::cli_echo($details['line'], 'white', 'bold');
							TBGCliCommand::cli_echo(":\n");
							TBGCliCommand::cli_echo("{$details['sql']}\n");
						}
						echo "\n";
					}
					catch (Exception $e)
					{
						TBGCliCommand::cli_echo("Could not generate query list (there may be no database connection)", "red", "bold");
					}
				}
			}
			echo "\n";
		}

		/**
		 * Displays a nicely formatted exception message
		 *
		 * @param string $title
		 * @param \Exception $exception
		 */
		public static function exceptionHandler($exception)
		{
			if (self::isDebugMode() && !self::isInstallmode()) self::generateDebugInfo();

			if (self::getRequest() instanceof TBGRequest && self::getRequest()->isAjaxCall()) {
				self::getResponse()->ajaxResponseText(404, $exception->getMessage());
			}

			if (self::isCLI()) {
				self::cliError($exception->getMessage(), $exception);
			} else {
				self::getResponse()->cleanBuffer();
				require THEBUGGENIE_CORE_PATH . 'templates' . DS . 'error.php';
			}
			die();
		}

		public static function errorHandler($code, $error, $file, $line)
		{
			if (self::isDebugMode()) self::generateDebugInfo();

			if (self::getRequest() instanceof TBGRequest && self::getRequest()->isAjaxCall()) {
				self::getResponse()->ajaxResponseText(404, $error);
			}

			$details = compact('code', 'error', 'file', 'line');

			if (self::isCLI()) {
				self::cliError($error, $details);
			} else {
				//if (self::getResponse() instanceof TBGResponse) self::getResponse()->cleanBuffer();
				self::getResponse()->cleanBuffer();
				require THEBUGGENIE_CORE_PATH . 'templates' . DS . 'error.php';
			}
			die();
		}

		/**
		 * Add a path to the list of searched paths in the autoloader
		 * Class files must contain one class with the same name as the class
		 * in the form of Classname.class.php
		 * 
		 * @param string $path The path where the class files are
		 * 
		 * @return null
		 */
		public static function autoloadNamespace($namespace, $path)
		{
			$path = realpath($path);
			if (!file_exists($path)) throw new Exception("Cannot add {$path} to autoload, since the path doesn't exist");
			self::$_classpaths[$namespace] = $path;
		}
		
		public static function addAutoloaderClassPath($path)
		{
			$path = realpath($path);
			if (!file_exists($path)) return; // throw new Exception("Cannot add {$path} to autoload, since the path doesn't exist");

			if (file_exists($path . DS . 'actions.class.php'))
				require_once $path . DS . 'actions.class.php';

			if (file_exists($path . DS . 'actioncomponents.class.php'))
				require_once $path . DS . 'actioncomponents.class.php';

			self::$_classpaths[0][] = $path;
		}
		
		/**
		 * Returns the classpaths that has been registered to the autoloader
		 *
		 * @return array
		 */
		public static function getAutoloadedNamespaces()
		{
			if (!array_key_exists(0, self::$_classpaths)) self::$_classpaths[0] = array();
			return self::$_classpaths;
		}
		
		/**
		 * Magic autoload function to make sure classes are autoloaded when used
		 * 
		 * @param $classname
		 */
		public static function autoload($classname)
		{
			$class_details = explode('\\', $classname);
			$namespaces = self::getAutoloadedNamespaces();
			if (count($class_details) > 1)
			{
				$classname_element = array_pop($class_details);
				$orig_class_details = $class_details;
				$cc = count($class_details);
				while (!empty($class_details))
				{
					$namespace = join('\\', $class_details);
					if (array_key_exists($namespace, $namespaces))
					{
						for ($ccc = 1; $ccc <= $cc; $ccc++) array_shift($orig_class_details);

						$classpath = (count($orig_class_details)) ? join(DS, $orig_class_details) . DS : '';
						$basepath = $namespaces[$namespace];
						$filename = $basepath . DS . $classpath . $classname_element . '.class.php';
						$filename_alternate = $basepath . DS . $classpath . "classes" . DS . $classname_element . ".class.php";
						break;
					}
					array_pop($class_details);
					$cc--;
				}
			}
			else
			{
				foreach ($namespaces[0] as $classpath)
				{
					if (file_exists($classpath . DS . $classname . '.class.php'))
					{
						$filename = $classpath . DS . $classname . '.class.php';
						break;
					}
				}
			}
			if (isset($filename) && file_exists($filename))
			{
				require $filename;
				return;
			}
			elseif (isset($filename_alternate) && file_exists($filename_alternate))
			{
				require $filename_alternate;
				return;
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
				self::$_stripped_tbgpath = mb_substr(self::getTBGPath(), 0, mb_strlen(self::getTBGPath()) - 1);
			}
			return self::$_stripped_tbgpath;
		}

		/**
		 * Set the subdirectory part of the url, from the url
		 */
		protected static function _setTBGPath()
		{
			self::$_tbgpath = defined('TBG_CLI') ? '.' : dirname($_SERVER['PHP_SELF']);
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
		 * Get the time from when we started loading
		 * 
		 * @param integer $precision
		 * @return integer
		 */
		public static function getLoadtime($precision = 5)
		{
			$endtime = explode(' ', microtime());
			return round((($endtime[1] + $endtime[0]) - self::$_loadstart), $precision);
		}
		
		public static function checkInstallMode()
		{
			if (!is_readable(THEBUGGENIE_PATH . 'installed'))
			{
				self::$_installmode = true;
			}
			elseif (is_readable(THEBUGGENIE_PATH . 'upgrade'))
			{
				self::$_installmode = true;
				self::$_upgrademode = true;
				\b2db\Core::setCachingEnabled(false);
			}
			elseif (!\b2db\Core::isInitialized())
			{
				throw new Exception("The Bug Genie seems installed, but B2DB isn't configured. This usually indicates an error with the installation. Try removing the file ".THEBUGGENIE_PATH."installed and try again.");
			}
		}

		public static function initializeSession()
		{
			TBGLogging::log('Initializing session');
			session_name(THEBUGGENIE_SESSION_NAME);
			session_start();
			TBGLogging::log('done (initializing session)');
		}

		/**
		 * Initialize the context
		 * 
		 * @return null
		 */
		public static function initialize()
		{
			if (self::$_debug_mode) self::$debug_id = uniqid();
			try
			{
				// The time the script was loaded
				$starttime = explode(' ', microtime());
				define('NOW', (integer) $starttime[1]);

				// Set up error and exception handling
				set_exception_handler(array('TBGContext', 'exceptionHandler'));
				set_error_handler(array('TBGContext', 'errorHandler'));
				error_reporting(E_ALL | E_NOTICE | E_STRICT);

				// Set the start time
				self::setLoadStart($starttime[1] + $starttime[0]);
				TBGLogging::log('Initializing Caspar framework');
				TBGLogging::log('PHP_SAPI says "' . PHP_SAPI . '"');
				TBGLogging::log('We are version "' . TBGSettings::getVersion() . '"');

				if (!is_writable(THEBUGGENIE_CORE_PATH . DIRECTORY_SEPARATOR . 'cache'))
					throw new Exception('The cache directory is not writable. Please correct the permissions of core/cache, and try again');

				if (!self::isCLI() && !ini_get('session.auto_start'))
					self::initializeSession();

				TBGCache::checkEnabled();
				if (TBGCache::isEnabled())
				{
					TBGLogging::log((TBGCache::getCacheType() == TBGCache::TYPE_APC) ? 'Caching enabled: APC, filesystem' : 'Caching enabled: filesystem');
				}
				else
				{
					TBGLogging::log('No caching available');
				}

				TBGLogging::log('Loading B2DB');
				if (self::isCLI()) \b2db\Core::setHTMLException(false);
				\b2db\Core::initialize(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
				TBGLogging::log('...done (Initializing B2DB)');

				if (\b2db\Core::isInitialized())
				{
					TBGLogging::log('Database connection details found, connecting');
					\b2db\Core::doConnect();
					TBGLogging::log('...done (Database connection details found, connecting)');
				}

				TBGLogging::log('...done');

				TBGLogging::log('Initializing context');

				mb_internal_encoding("UTF-8");
				mb_language('uni');
				mb_http_output("UTF-8");

				self::checkInstallMode();

				TBGLogging::log('Loading pre-module routes');
				self::loadPreModuleRoutes();
				TBGLogging::log('done (loading pre-module routes)');

				TBGLogging::log('Loading scope');
				self::setScope();
				TBGLogging::log('done (loading scope)');

				if (!self::$_installmode) self::setupCoreListeners();

				TBGLogging::log('Loading modules');
				self::loadModules();
				TBGLogging::log('done (loading modules)');

				if (!self::$_installmode) self::initializeUser();

				TBGLogging::log('Initializing i18n');
				self::setupI18n();
				TBGLogging::log('done (initializing i18n)');

				TBGLogging::log('Loading post-module routes');
				self::loadPostModuleRoutes();
				TBGLogging::log('done (loading post-module routes)');

				TBGLogging::log('...done');
				TBGLogging::log('...done initializing');

				TBGLogging::log('Caspar framework loaded');
			}
			catch (Exception $e)
			{
				if (!self::isCLI() && !self::isInstallmode())
					throw $e;
			}
		}
		
		protected static function setupI18n()
		{
			if (TBGContext::isCLI())
				return null;

			$language = (self::$_user instanceof TBGUser) ? self::$_user->getLanguage() : TBGSettings::getLanguage();
			
			if (self::$_user instanceof TBGUser && self::$_user->getLanguage() == 'sys')
			{
				$language = TBGSettings::getLanguage();
			}
			
			TBGLogging::log('Loading i18n strings');
			if (!self::$_i18n = TBGCache::get(TBGCache::KEY_I18N.$language, false))
			{
				TBGLogging::log("Loading strings from file ({$language})");
				self::$_i18n = new TBGI18n($language);
				if (!self::isInstallmode()) TBGCache::add(TBGCache::KEY_I18N.$language, self::$_i18n, false);
			}
			else
			{
				TBGLogging::log('Using cached i18n strings');
			}
			self::$_i18n->initialize();
			TBGLogging::log('...done');
		}

		protected static function initializeUser()
		{
			TBGLogging::log('Loading user');
			try
			{
				TBGLogging::log('is this logout?');
				if (self::getRequest()->getParameter('logout'))
				{
					TBGLogging::log('yes');
					self::logout();
				}
				else
				{
					TBGLogging::log('no');
					TBGLogging::log('sets up user object');
					$event = TBGEvent::createNew('core', 'pre_login');
					$event->trigger();

					if ($event->isProcessed())
						self::loadUser($event->getReturnValue());
					elseif (!self::isCLI())
						self::loadUser();
					else
						self::$_user = new TBGUser();

					TBGEvent::createNew('core', 'post_login', self::getUser())->trigger();

					TBGLogging::log('loaded');
					TBGLogging::log('caching permissions');
					self::cacheAllPermissions();
					TBGLogging::log('done (caching permissions)');
				}
			}
			catch (Exception $e)
			{
				TBGLogging::log("Something happened while setting up user: ". $e->getMessage(), 'main', TBGLogging::LEVEL_WARNING);
				$allow_anonymous_routes = array('register', 'register1', 'register2', 'activate', 'reset_password', 'captcha', 'login', 'getBackdropPartial', 'serve', 'doLogin');
				if (!self::isCLI() && (self::getRouting()->getCurrentRouteModule() != 'main' || !in_array(self::getRouting()->getCurrentRouteAction(), $allow_anonymous_routes)))
				{
					TBGContext::setMessage('login_message_err', $e->getMessage());
					self::$_redirect_login = true;
				}
				else
				{
					self::$_user = self::factory()->TBGUser(TBGSettings::getDefaultUserID());
				}
			}
			TBGLogging::log('...done');
		}

		protected static function setupCoreListeners()
		{
			TBGEvent::listen('core', 'TBGFile::hasAccess', 'TBGProject::listen_TBGFile_hasAccess');
			TBGEvent::listen('core', 'TBGFile::hasAccess', 'TBGSettings::listen_TBGFile_hasAccess');
		}

		public static function clearRoutingCache()
		{
			if (!TBGCache::isEnabled()) return;
			foreach (array(TBGCache::KEY_PREMODULES_ROUTES_CACHE, TBGCache::KEY_POSTMODULES_ROUTES_CACHE) as $key)
			{
				TBGCache::delete($key, false);
				TBGCache::fileDelete($key, false);
			}
		}

		public static function clearMenuLinkCache()
		{
			if (!TBGCache::isEnabled()) return;
			foreach (array(TBGCache::KEY_MAIN_MENU_LINKS) as $key)
			{
				TBGCache::delete($key);
				TBGCache::fileDelete($key);
			}
		}

		protected static function loadPreModuleRoutes()
		{
			TBGLogging::log('Loading first batch of routes', 'routing');
			if (self::isInstallmode() || !($routes_1 = TBGCache::get(TBGCache::KEY_PREMODULES_ROUTES_CACHE, false)))
			{
				if (self::isInstallmode() || !($routes_1 = TBGCache::fileGet(TBGCache::KEY_PREMODULES_ROUTES_CACHE, false)))
				{
					TBGLogging::log('generating routes', 'routing');
					require THEBUGGENIE_CORE_PATH . 'load_routes.inc.php';
					if (!self::isInstallmode()) TBGCache::fileAdd(TBGCache::KEY_PREMODULES_ROUTES_CACHE, self::getRouting()->getRoutes(), false);
				}
				else
				{
					TBGLogging::log('using disk cached routes', 'routing');
					self::getRouting()->setRoutes($routes_1);
				}
				if (!self::isInstallmode()) TBGCache::add(TBGCache::KEY_PREMODULES_ROUTES_CACHE, self::getRouting()->getRoutes(), false);
			}
			else
			{
				TBGLogging::log('loading routes from cache', 'routing');
				self::getRouting()->setRoutes($routes_1);
			}
			TBGLogging::log('...done', 'routing');
		}

		protected static function loadPostModuleRoutes()
		{
			TBGLogging::log('Loading last batch of routes', 'routing');
			if (self::isInstallmode() || !($routes = TBGCache::get(TBGCache::KEY_POSTMODULES_ROUTES_CACHE, false)))
			{
				if (self::isInstallmode() || !($routes = TBGCache::fileGet(TBGCache::KEY_POSTMODULES_ROUTES_CACHE, false)))
				{
					TBGLogging::log('generating postmodule routes', 'routing');
					require THEBUGGENIE_CORE_PATH . 'load_routes_postmodules.inc.php';
					if (!self::isInstallmode()) TBGCache::fileAdd(TBGCache::KEY_POSTMODULES_ROUTES_CACHE, self::getRouting()->getRoutes(), false);
				}
				else
				{
					TBGLogging::log('using disk cached postmodule routes', 'routing');
					self::getRouting()->setRoutes($routes);
				}
				if (!self::isInstallmode()) TBGCache::add(TBGCache::KEY_POSTMODULES_ROUTES_CACHE, self::getRouting()->getRoutes(), false);
			}
			else
			{
				TBGLogging::log('loading postmodule routes from cache', 'routing');
				self::getRouting()->setRoutes($routes);
			}
			TBGLogging::log('...done', 'routing');
		}

		/**
		 * Returns the factory object
		 *
		 * @return TBGFactory
		 */
		public static function factory()
		{
			if (!self::$_factory instanceof TBGFactory)
			{
				self::$_factory = new TBGFactory();
			}
			return self::$_factory;
		}

		/**
		 * Returns the request object
		 * 
		 * @return TBGRequest
		 */
		public static function getRequest()
		{
			if (!self::$_request instanceof TBGRequest)
			{
				self::$_request = new TBGRequest();
			}
			return self::$_request;
		}
		
		/**
		 * Returns the response object
		 * 
		 * @return TBGResponse
		 */
		public static function getResponse()
		{
			if (!self::$_response instanceof TBGResponse)
			{
				self::$_response = new TBGResponse();
			}
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
				TBGLogging::log('Changing language to '.$language);
				self::$_i18n = new TBGI18n($language);
				self::$_i18n->initialize();
			}
		}
		
		/**
		 * Get the i18n object
		 *
		 * @return TBGI18n
		 */
		public static function getI18n()
		{
			if (!self::isI18nInitialized())
			{
				TBGLogging::log('Cannot access the translation object until the i18n system has been initialized!', 'i18n', TBGLogging::LEVEL_WARNING);
				throw new Exception('Cannot access the translation object until the i18n system has been initialized!');
				//self::reinitializeI18n(self::getUser()->getLanguage());
			}
			return self::$_i18n;
		}

		public static function isI18nInitialized()
		{
			return (self::$_i18n instanceof TBGI18n);
		}
		
		/**
		 * Get available themes
		 * 
		 * @return array
		 */
		public static function getThemes()
		{
			$theme_path_handle = opendir(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS);
			$themes = array();
			
			while ($theme = readdir($theme_path_handle))
			{
				if ($theme != '.' && $theme != '..' && is_dir(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . $theme) && file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . $theme . DS . 'theme.php')) 
				{ 
					$themes[] = $theme; 
				}
			}
			
			return $themes;
		}

		public static function getIconSets()
		{
			$icon_path_handle = opendir(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'iconsets' . DS);
			$icons = array();
			
			while ($icon = readdir($icon_path_handle))
			{
				if ($icon != '.' && $icon != '..' && is_dir(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'iconsets' . DS . $icon)) 
				{ 
					$icons[] = $icon; 
				}
			}
			
			return $icons;
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
					if (!self::getRequest()->hasCookie('tbg3_original_username'))
					{
						self::$_user->updateLastSeen();
					}
					if (!TBGContext::getScope()->isDefault() && !self::getRequest()->isAjaxCall() && !in_array(self::getRouting()->getCurrentRouteName(), array('add_scope', 'serve', 'debug', 'logout')) && !self::$_user->isGuest() && !self::$_user->isConfirmedMemberOfScope(TBGContext::getScope()))
					{
						self::getResponse()->headerRedirect(self::getRouting()->generate('add_scope'));
					}
					self::$_user->setTimezone(TBGSettings::getUserTimezone());
					self::$_user->setLanguage(TBGSettings::getUserLanguage());
					self::$_user->save();
					if (!(self::$_user->getGroup() instanceof TBGGroup))
					{
						throw new Exception('This user account belongs to a group that does not exist anymore. <br>Please contact the system administrator.');
					}
				}
			}
			catch (Exception $e)
			{
				self::$_user = new TBGUser();
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

		public static function switchUserContext(TBGUser $user)
		{
			self::setUser($user);
			TBGSettings::forceSettingsReload();
			self::cacheAllPermissions();
		}

		/**
		 * Loads and initializes all modules
		 */
		public static function loadModules()
		{
			TBGLogging::log('Loading modules');
			if (self::isInstallmode()) return;

			$modules = array();

			TBGLogging::log('getting modules from database');
			$module_paths = array();

			if ($res = \b2db\Core::getTable('TBGModulesTable')->getAll())
			{
				while ($moduleRow = $res->getNextRow())
				{
					$module_name = $moduleRow->get(TBGModulesTable::MODULE_NAME);
					$classname = $moduleRow->get(TBGModulesTable::CLASSNAME);
					$moduleClassPath = THEBUGGENIE_MODULES_PATH . $module_name . DS . "classes" . DS;
					self::addAutoloaderClassPath($moduleClassPath);
					self::addAutoloaderClassPath($moduleClassPath . 'B2DB' . DS);
					self::addAutoloaderClassPath($moduleClassPath . 'cli' . DS);
					if ($classname == '' || $classname == 'TBGModule')
						throw new Exception('Cannot load module "' . $module_name . '" as class TBGModule - modules should extend the TBGModule class with their own class.');

					self::$_modules[$module_name] = new $classname($moduleRow->get(TBGModulesTable::ID), $moduleRow);
				}
			}
			TBGLogging::log('done (setting up module objects)');
			TBGLogging::log('initializing modules');
			if (!empty(self::$_modules))
			{
				foreach (self::$_modules as $module_name => $module)
				{
					$module->initialize();
				}
				TBGLogging::log('done (initializing modules)');
			}
			else
			{
				TBGLogging::log('no modules found');
			}
			TBGLogging::log('...done');
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
		 * @return array|TBGModule
		 */
		public static function getModules()
		{
			return self::$_modules;
		}
		
		/**
		 * Returns an array of modules which need upgrading
		 * 
		 * @return array
		 */
		public static function getOutdatedModules()
		{
			if (self::$_outdated_modules == null)
			{
				self::$_outdated_modules = array();
				foreach (self::getModules() as $module)
				{
					if ($module->isOutdated())
					{
						self::$_outdated_modules[] = $module;
					}
				}
			}
			
			return self::$_outdated_modules;
		}

		/**
		 * Get uninstalled modules
		 *
		 * @return array
		 */
		public static function getUninstalledModules()
		{
			$module_path_handle = opendir(THEBUGGENIE_MODULES_PATH);
			$modules = array();
			while ($module_name = readdir($module_path_handle))
			{
				if (is_dir(THEBUGGENIE_MODULES_PATH . $module_name) && file_exists(THEBUGGENIE_MODULES_PATH . $module_name . DS . 'module'))
				{
					if (self::isModuleLoaded($module_name)) continue;
					$modules[$module_name] = file_get_contents(THEBUGGENIE_MODULES_PATH . $module_name . DS . 'module');
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
			$crit = new \b2db\Criteria();
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

			if ($res = \b2db\Core::getTable('TBGPermissionsTable')->doSelect($crit))
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
			TBGLogging::log('caches permissions');
			self::$_permissions = array();
			
			if (!self::isInstallmode() && $permissions = TBGCache::get(TBGCache::KEY_PERMISSIONS_CACHE))
			{
				self::$_permissions = $permissions;
				TBGLogging::log('Using cached permissions');
			}
			else
			{
				if (self::isInstallmode() || !$permissions = TBGCache::fileGet(TBGCache::KEY_PERMISSIONS_CACHE))
				{
					TBGLogging::log('starting to cache access permissions');
					if ($res = \b2db\Core::getTable('TBGPermissionsTable')->getAll())
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
					if (!self::isInstallmode()) TBGCache::fileAdd(TBGCache::KEY_PERMISSIONS_CACHE, self::$_permissions);
				}
				else
				{
					self::$_permissions = $permissions;
				}
				if (!self::isInstallmode()) TBGCache::add(TBGCache::KEY_PERMISSIONS_CACHE, self::$_permissions);
			}
			TBGLogging::log('...cached');
		}

		public static function deleteModulePermissions($module_name, $scope)
		{
			if ($scope == TBGContext::getScope()->getID())
			{
				if (array_key_exists($module_name, self::$_permissions))
				{
					unset(self::$_permissions[$module_name]);
				}
			}
			TBGPermissionsTable::getTable()->deleteModulePermissions($module_name, $scope);
		}

		public static function clearPermissionsCache()
		{
			TBGCache::delete(TBGCache::KEY_PERMISSIONS_CACHE);
			TBGCache::fileDelete(TBGCache::KEY_PERMISSIONS_CACHE);
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
			
			TBGPermissionsTable::getTable()->removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope);
			self::clearPermissionsCache();

			if ($recache) self::cacheAllPermissions();
		}

		public static function removeAllPermissionsForCombination($uid, $gid, $tid, $target_id = 0, $module = 'core', $scope = null)
		{
			$scope = ($scope !== null) ? $scope : self::getScope()->getID();
			TBGPermissionsTable::getTable()->deleteAllPermissionsForCombination($uid, $gid, $tid, $target_id, $module, $scope);
			self::clearPermissionsCache();
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
			self::clearPermissionsCache();

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
		
		protected static function _permissionsCheck($permissions, $uid, $gid, $tid)
		{
			try
			{
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
									if ($pp['uid'] == $uid) {
										return $pp['allowed'];
									}
								}
							}
							elseif ($permission['uid'] == $uid) return $permission['allowed'];
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
							elseif ((is_array($tid) && in_array($permission['tid'], array_keys($tid))) || $permission['tid'] == $tid)
							{
								return $permission['allowed'];
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
							elseif ($permission['gid'] == $gid) return $permission['allowed'];
						}
					}
				}
	
				foreach ($permissions as $key => $permission)
				{
					if (!array_key_exists('uid', $permission))
					{
						foreach ($permission as $pkey => $pp)
						{
							if ($pp['uid'] + $pp['gid'] + $pp['tid'] == 0) return $pp['allowed'];
						}
					}
					elseif ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0) return $permission['allowed'];
				}
			}
			catch (Exception $e) { }
			
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
		public static function checkPermission($permission_type, $uid, $gid, $tid, $target_id = 0, $module_name = 'core')
		{
			$uid = (int) $uid;
			$gid = (int) $gid;
			$retval = null;
			if (array_key_exists($module_name, self::$_permissions) &&
				array_key_exists($permission_type, self::$_permissions[$module_name]) &&
				(array_key_exists($target_id, self::$_permissions[$module_name][$permission_type]) || $target_id === null))
			{
				if (array_key_exists(0, self::$_permissions[$module_name][$permission_type]))
				{
					$permissions_notarget = self::$_permissions[$module_name][$permission_type][0];
				}
				
				$permissions_target = (array_key_exists($target_id, self::$_permissions[$module_name][$permission_type])) ? self::$_permissions[$module_name][$permission_type][$target_id] : array();
				
				$retval = self::_permissionsCheck($permissions_target, $uid, $gid, $tid);
				
				if (array_key_exists(0, self::$_permissions[$module_name][$permission_type]))
				{
					$retval = ($retval !== null) ? $retval : self::_permissionsCheck($permissions_notarget, $uid, $gid, $tid);
				}
				
				if ($retval !== null) return $retval;
			}

			return $retval;
		}

		public static function getLoadedPermissions()
		{
			return self::$_permissions;
		}
		
		public static function getPermissionDetails($permission, $permissions_list = null)
		{
			self::_cacheAvailablePermissions();
			$permissions_list = ($permissions_list === null) ? self::$_available_permissions : $permissions_list;
			foreach ($permissions_list as $permission_key => $permission_info)
			{
				if (is_numeric($permission_key)) return null;
				if ($permission_key == $permission) return $permission_info;
				
				if (in_array($permission_key, array_keys(self::$_available_permissions)) || (array_key_exists('details', $permission_info) && is_array($permission_info['details']) && count($permission_info['details'])))
				{
					$p_info = (in_array($permission_key, array_keys(self::$_available_permissions))) ? $permission_info : $permission_info['details'];
					$retval = self::getPermissionDetails($permission, $p_info);
					if ($retval) return $retval;
				}
			}
		}

		protected static function _cacheAvailablePermissions()
		{
			if (self::$_available_permissions === null)
			{
				$i18n = self::getI18n();
				self::$_available_permissions = array('user' => array(), 'general' => array(), 'project' => array());

				self::$_available_permissions['user']['canseeallissues'] = array('description' => $i18n->__('Can see issues reported by other users'), 'mode' => 'permissive');
				self::$_available_permissions['user']['canseegroupissues'] = array('description' => $i18n->__('Can see issues reported by users in the same group'), 'mode' => 'permissive');
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
				self::$_available_permissions['general']['canfindissuesandsavesearches']['details']['cancreatepublicsearches'] = array('description' => $i18n->__('Can create saved searches that are public'));
				self::$_available_permissions['general']['caneditmainmenu'] = array('description' => $i18n->__('Can edit main menu'));
				self::$_available_permissions['pages']['page_home_access'] = array('description' => $i18n->__('Can access the frontpage'));
				self::$_available_permissions['pages']['page_dashboard_access'] = array('description' => $i18n->__('Can access the user dashboard'));
				self::$_available_permissions['pages']['page_search_access'] = array('description' => $i18n->__('Can access the search page'));
				self::$_available_permissions['pages']['page_about_access'] = array('description' => $i18n->__('Can access the "About" page'));
				self::$_available_permissions['pages']['page_account_access'] = array('description' => $i18n->__('Can access the "My account" page'), 'details' => array());
				self::$_available_permissions['pages']['page_account_access']['details']['canchangepassword'] = array('description' => $i18n->__('Can change own password'), 'mode' => 'permissive');
				self::$_available_permissions['pages']['page_teamlist_access'] = array('description' => $i18n->__('Can see list of teams in header menu'));
				self::$_available_permissions['pages']['page_clientlist_access'] = array('description' => $i18n->__('Can access all clients'));
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
				self::$_available_permissions['project']['canseeprojecthierarchy']['details']['canseeallprojectbuilds'] = array('description' => $i18n->__('Can see all releases'));
				self::$_available_permissions['project']['canseeprojecthierarchy']['details']['canseeallprojectmilestones'] = array('description' => $i18n->__('Can see all milestones'));
				self::$_available_permissions['project']['candoscrumplanning'] = array('description' => $i18n->__('Can manage stories, tasks, sprints and backlog on the project planning page'), 'details' => array());
				self::$_available_permissions['project']['candoscrumplanning']['details']['canaddscrumuserstories'] = array('description' => $i18n->__('Can add new issues/tasks/stories to the backlog on the project planning page'));
				self::$_available_permissions['project']['candoscrumplanning']['details']['candoscrumplanning_backlog'] = array('description' => $i18n->__('Can manage the backlog on the project planning page'));
				self::$_available_permissions['project']['candoscrumplanning']['details']['canaddscrumsprints'] = array('description' => $i18n->__('Can add milestones/sprints on the project planning page'));
				self::$_available_permissions['project']['candoscrumplanning']['details']['canassignscrumuserstoriestosprints'] = array('description' => $i18n->__('Can (re-)assign issues/tasks/stories to milestones/sprints on the project planning page'));
				self::$_available_permissions['project']['canmanageproject'] = array('description' => $i18n->__('Can manage project'));
				self::$_available_permissions['project']['canmanageproject']['details']['canmanageprojectreleases'] = array('description' => $i18n->__('Can manage project releases and components'));
				self::$_available_permissions['project']['canmanageproject']['details']['caneditprojectdetails'] = array('description' => $i18n->__('Can edit project details and settings'));
				self::$_available_permissions['edition']['canseeedition'] = array('description' => $i18n->__('Can see this edition'));
				self::$_available_permissions['component']['canseecomponent'] = array('description' => $i18n->__('Can see this component'));
				self::$_available_permissions['build']['canseebuild'] = array('description' => $i18n->__('Can see this release'));
				self::$_available_permissions['milestone']['canseemilestone'] = array('description' => $i18n->__('Can see this milestone'));
				self::$_available_permissions['issues']['canvoteforissues'] = array('description' => $i18n->__('Can vote for issues'));
				self::$_available_permissions['issues']['canlockandeditlockedissues'] = array('description' => $i18n->__('Can toggle issue access between restricted and public'));
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
				self::$_available_permissions['issues']['canaddextrainformationtoissues'] = array('description' => $i18n->__('Can add/remove extra information and link issues (edition, component, release, links and files) to issues'), 'details' => array());
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
			elseif (mb_substr($applies_to, 0, 7) == 'module_')
			{
				$module_name = mb_substr($applies_to, 7);
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
		
		/**
		 * Log out the current user (does not work when auth method is set to http)
		 */
		public static function logout()
		{
			if (TBGSettings::isUsingExternalAuthenticationBackend())
			{
				$mod = TBGContext::getModule(TBGSettings::getAuthenticationBackend());
				$mod->logout();
			}
			
			TBGEvent::createNew('core', 'pre_logout')->trigger();
			self::getResponse()->deleteCookie('tbg3_username');
			self::getResponse()->deleteCookie('tbg3_password');
			self::getResponse()->deleteCookie('THEBUGGENIE');
			session_regenerate_id(true);
			TBGEvent::createNew('core', 'post_logout')->trigger();
		}

		/**
		 * Find and set the current scope
		 * 
		 * @param integer $scope Specify a scope to set for this request
		 */
		public static function setScope($scope = null)
		{
			TBGLogging::log("Setting current scope");
			if ($scope !== null)
			{
				TBGLogging::log("Setting scope from function parameter");
				self::$_scope = $scope;
				TBGSettings::forceSettingsReload();
				TBGLogging::log("...done (Setting scope from function parameter)");
				return true;
			}
	
			$row = null;
			try
			{
				$hostname = null;
				if (!self::isCLI() && !self::isInstallmode())
				{
					TBGLogging::log("Checking if scope can be set from hostname (".$_SERVER['HTTP_HOST'].")");
					$hostname = $_SERVER['HTTP_HOST'];
				}
				
				if (!self::isUpgrademode() && !self::isInstallmode())
					$scope = TBGScopesTable::getTable()->getByHostnameOrDefault($hostname);
				
				if (!$scope instanceof TBGScope)
				{
					TBGLogging::log("It couldn't", 'main', TBGLogging::LEVEL_WARNING);
					if (!self::isInstallmode())
						throw new Exception("The Bug Genie isn't set up to work with this server name.");
					else
						return;
				}
				
				TBGLogging::log("Setting scope {$scope->getID()} from hostname");
				self::$_scope = $scope;
				TBGSettings::forceSettingsReload();
				TBGSettings::loadSettings();
				TBGLogging::log("...done (Setting scope from hostname)");
				return true;
			}
			catch (Exception $e)
			{
				if (self::isCLI())
				{
					TBGLogging::log("Couldn't set up default scope.", 'main', TBGLogging::LEVEL_FATAL);
					throw new Exception("Could not load default scope. Error message was: " . $e->getMessage());
				}
				elseif (!self::isInstallmode())
				{
					throw $e;
					TBGLogging::log("Couldn't find a scope for hostname {$_SERVER['HTTP_HOST']}", 'main', TBGLogging::LEVEL_FATAL);
					TBGLogging::log($e->getMessage(), 'main', TBGLogging::LEVEL_FATAL);
					throw new Exception("Could not load scope. This is usually because the scopes table doesn't have a scope for this hostname");
				}
				else
				{
					TBGLogging::log("Couldn't find a scope for hostname {$_SERVER['HTTP_HOST']}, but we're in installmode so continuing anyway");
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
		
		public static function populateBreadcrumbs()
		{
			$childbreadcrumbs = array();
			
			if (self::$_selected_project instanceof TBGProject)
			{
				$t = self::$_selected_project;
				
				$hierarchy_breadcrumbs = array();
				$projects_processed = array();
				
				while ($t instanceof TBGProject)
				{
					if (array_key_exists($t->getKey(), $projects_processed))
					{
						// We have a cyclic dependency! Oh no!
						// If this happens, throw an exception
						
						throw new Exception(TBGContext::geti18n()->__('A loop has been found in the project heirarchy. Go to project configuration, and alter the subproject setting for this project so that this project is not a subproject of one which is a subproject of this one.'));
						continue;
					}
					else
					{
						$all_projects = array_merge(TBGProject::getAllRootProjects(true), TBGProject::getAllRootProjects(false));
						// If this is a root project, display a list of other root projects, then t is null
						if (!($t->hasParent()) && count($all_projects) > 1)
						{
							$itemsubmenulinks = array();
							foreach ($all_projects as $child)
							{
								if (!$child->hasAccess()) continue;
								$itemsubmenulinks[] = array('url' => self::getRouting()->generate('project_dashboard', array('project_key' => $child->getKey())), 'title' => $child->getName());
							}
							
							$hierarchy_breadcrumbs[] = array($t, $itemsubmenulinks);
							
							$projects_processed[$t->getKey()] = $t;
							
							$t = null;
							continue;
						}
						elseif (!($t->hasParent()))
						{
							$hierarchy_breadcrumbs[] = array($t, null);
							
							$projects_processed[$t->getKey()] = $t;
							
							$t = null;
							continue;
						}
						else
						{
							// What we want to do here is to build a list of the children of the parent unless we are the only one
							$parent = $t->getParent();
							$children = $parent->getChildren();
							
							$itemsubmenulinks = null;
							
							if ($parent->hasChildren() && count($children) > 1)
							{
								$itemsubmenulinks = array();
								foreach ($children as $child)
								{
									if (!$child->hasAccess()) continue;
									$itemsubmenulinks[] = array('url' => self::getRouting()->generate('project_dashboard', array('project_key' => $child->getKey())), 'title' => $child->getName());
								}
							}
							
							$hierarchy_breadcrumbs[] = array($t, $itemsubmenulinks);
							
							$projects_processed[$t->getKey()] = $t;
							
							$t = $parent;
							continue;
						}
					}
				}
				
				$clientsubmenulinks = null;
				if (self::$_selected_project->hasClient())
				{
					$clientsubmenulinks = array();
					foreach (TBGClient::getAll() as $client)
					{
						if ($client->hasAccess())
							$clientsubmenulinks[] = array('url' => self::getRouting()->generate('client_dashboard', array('client_id' => $client->getID())), 'title' => $client->getName());
					}
					self::setCurrentClient(self::$_selected_project->getClient());
				}
				if (mb_strtolower(TBGSettings::getTBGname()) != mb_strtolower(self::$_selected_project->getName()) || self::isClientContext())
				{
					self::getResponse()->addBreadcrumb(TBGSettings::getTBGName(), self::getRouting()->generate('home'));
					if (self::isClientContext())
					{
						self::getResponse()->addBreadcrumb(self::getCurrentClient()->getName(), self::getRouting()->generate('client_dashboard', array('client_id' => self::getCurrentClient()->getID())), $clientsubmenulinks);
					}
				}
				
				// Add root breadcrumb first, so reverse order
				$hierarchy_breadcrumbs = array_reverse($hierarchy_breadcrumbs);
				
				foreach ($hierarchy_breadcrumbs as $breadcrumb)
				{
					$class = null;
					if ($breadcrumb[0]->getKey() == self::getCurrentProject()->getKey())
					{
						$class = 'selected_project';
					}
					self::getResponse()->addBreadcrumb($breadcrumb[0]->getName(), self::getRouting()->generate('project_dashboard', array('project_key' => $breadcrumb[0]->getKey())), $breadcrumb[1], $class);					
				}
			}
			else
			{
				self::getResponse()->addBreadcrumb(TBGSettings::getTBGName(), self::getRouting()->generate('home'));
			}
		}
		
		/**
		 * Set the currently selected project
		 * 
		 * @param TBGProject $project The project, or null if none
		 */
		public static function setCurrentProject($project)
		{
			self::getResponse()->setBreadcrumb(null);
			self::$_selected_project = $project;
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
		 * @param string $key The key
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
			throw new TBGCSRFFailureException($message);
		}

		/**
		 * Loads a function library
		 * 
		 * @param string $lib_name The name of the library
		 */
		public static function loadLibrary($lib_name)
		{
			if (mb_strpos($lib_name, '/') !== false)
			{
				list ($module, $lib_name) = explode('/', $lib_name);
			}

			// Skip the library if it already exists
			if (!array_key_exists($lib_name, self::$_libs))
			{
				$lib_file_name = "{$lib_name}.inc.php";

				if (isset($module) && file_exists(THEBUGGENIE_MODULES_PATH . $module . DS . 'lib' . DS . $lib_file_name))
				{
					require THEBUGGENIE_MODULES_PATH . $module . DS . 'lib' . DS . $lib_file_name;
					self::$_libs[$lib_name] = THEBUGGENIE_MODULES_PATH . $module . DS . 'lib' . DS . $lib_file_name;
				}
				elseif (file_exists(THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . $lib_file_name))
				{
					// Include the library from the current module if it exists
					require THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . $lib_file_name;
					self::$_libs[$lib_name] = THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . $lib_file_name;
				}
				elseif (file_exists(THEBUGGENIE_CORE_PATH . 'lib' . DS . $lib_file_name))
				{
					// Include the library from the global library directory if it exists
					require THEBUGGENIE_CORE_PATH . 'lib' . DS . $lib_file_name;
					self::$_libs[$lib_name] = THEBUGGENIE_CORE_PATH . 'lib' . DS . $lib_file_name;
				}
				else
				{
					// Throw an exception if the library can't be found in any of
					// the above directories
					TBGLogging::log("The \"{$lib_name}\" library does not exist in either " . THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . ' or ' . THEBUGGENIE_CORE_PATH . 'lib' . DS, 'core', TBGLogging::LEVEL_FATAL);
					throw new TBGLibraryNotFoundException("The \"{$lib_name}\" library does not exist in either " . THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . ' or ' . THEBUGGENIE_CORE_PATH . 'lib' . DS);
				}
			}
		}
		
		public static function visitPartial($template_name, $time)
		{
			if (!self::$_debug_mode) return;
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
		
		protected static function getVisitedPartials()
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
			$templatePath = THEBUGGENIE_MODULES_PATH . $action . DS . 'templates' . DS;

			// Construct the action class and method name, including any pre- action(s)
			$actionClassName = $action.'Actions';
			$actionToRunName = 'run' . ucfirst($method);
			$preActionToRunName = 'pre' . ucfirst($method);

			// Set up the response object, responsible for controlling any output
			self::getResponse()->setPage(self::getRouting()->getCurrentRouteName());
			self::getResponse()->setTemplate(mb_strtolower($method) . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php');
			self::getResponse()->setupResponseContentType(self::getRequest()->getRequestedFormat());
			self::setCurrentProject(null);
			
			// Set up the action object
			$actionObject = new $actionClassName();

			// Run the specified action method set if it exists
			if (method_exists($actionObject, $actionToRunName))
			{
				// Turning on output buffering
				ob_start('mb_output_handler');
				ob_implicit_flush(0);

				if (self::getRouting()->isCurrentRouteCSRFenabled())
				{
					// If the csrf check fails, don't proceed
					if (!self::checkCSRFtoken(true))
					{
						return true;
					}
				}

				if (self::$_debug_mode)
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
						if (self::$_debug_mode) $visited_templatename = "{$actionClassName}::preExecute()";
					}
				}

				if ($content === null)
				{
					$action_retval = null;
					if (self::getResponse()->getHttpStatus() == 200)
					{
						// Checking for and running action-specific preExecute() function if
						// it exists
						if (method_exists($actionObject, $preActionToRunName))
						{
							TBGLogging::log('Running custom pre-execute action');
							$actionObject->$preActionToRunName(self::getRequest(), $method);
						}

						// Running main route action
						TBGLogging::log('Running route action '.$actionToRunName.'()');
						if (self::$_debug_mode)
						{
							$time = explode(' ', microtime());
							$action_pretime = $time[1] + $time[0];
						}
						$action_retval = $actionObject->$actionToRunName(self::getRequest());
						if (self::$_debug_mode)
						{
							$time = explode(' ', microtime());
							$action_posttime = $time[1] + $time[0];
							TBGContext::visitPartial("{$actionClassName}::{$actionToRunName}", $action_posttime - $action_pretime);
						}
					}
					if (self::getResponse()->getHttpStatus() == 200 && $action_retval)
					{
						// If the action returns *any* output, we're done, and collect the
						// output to a variable to be outputted in context later
						$content = ob_get_clean();
						TBGLogging::log('...done');
					}
					elseif (!$action_retval)
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
							if (mb_strpos(self::getResponse()->getTemplate(), '/'))
							{
								$newPath = explode('/', self::getResponse()->getTemplate());
								$templateName = THEBUGGENIE_MODULES_PATH . $newPath[0] . DS . 'templates' . DS . $newPath[1] . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php';
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
				elseif (self::$_debug_mode)
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
				TBGLogging::log('rendering final content');
				
				if (TBGSettings::isMaintenanceModeEnabled() && !mb_strstr(self::getRouting()->getCurrentRouteName(), 'configure'))
				{
					if (!file_exists(THEBUGGENIE_CORE_PATH . 'templates/offline.inc.php'))
					{
						throw new TBGTemplateNotFoundException('Can not find offline mode template');
					}
					ob_start('mb_output_handler');
					ob_implicit_flush(0);
					require THEBUGGENIE_CORE_PATH . 'templates/offline.inc.php';
					$content = ob_get_clean();
				}

				// Render output in correct order
				self::getResponse()->renderHeaders();

				if (self::getResponse()->getDecoration() == TBGResponse::DECORATE_DEFAULT && !self::getRequest()->isAjaxCall())
				{
					ob_start('mb_output_handler');
					ob_implicit_flush(0);
					require THEBUGGENIE_CORE_PATH . 'templates/layout.php';
					ob_flush();
				}
				else
				{
					// Render header template if any, and store the output in a variable
					if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateHeader())
					{
						TBGLogging::log('decorating with header');
						if (!file_exists(self::getResponse()->getHeaderDecoration()))
						{
							throw new TBGTemplateNotFoundException('Can not find header decoration: '. self::getResponse()->getHeaderDecoration());
						}
						require self::getResponse()->getHeaderDecoration();
					}

					echo $content;

					TBGLogging::log('...done (rendering content)');

					// Render footer template if any
					if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateFooter())
					{
						TBGLogging::log('decorating with footer');
						if (!file_exists(self::getResponse()->getFooterDecoration()))
						{
							throw new TBGTemplateNotFoundException('Can not find footer decoration: '. self::getResponse()->getFooterDecoration());
						}
						require self::getResponse()->getFooterDecoration();
					}

					TBGLogging::log('...done');
				}
				TBGLogging::log('done (rendering final content)');

				if (self::isDebugMode()) self::getI18n()->addMissingStringsToStringsFile();
				
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
			if (!$links = TBGCache::get(TBGCache::KEY_MAIN_MENU_LINKS))
			{
				$links = TBGLinksTable::getTable()->getMainLinks();
				if (!self::isInstallmode()) TBGCache::add(TBGCache::KEY_MAIN_MENU_LINKS, $links);
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
					if (self::isUpgrademode())
					{
						$route = array('module' => 'installation', 'action' => 'upgrade');
					}
					elseif (self::isInstallmode())
					{
						$route = array('module' => 'installation', 'action' => 'installIntro');
					}
					if (self::$_redirect_login)
					{
						TBGLogging::log('An error occurred setting up the user object, redirecting to login', 'main', TBGLogging::LEVEL_NOTICE);
						TBGContext::setMessage('login_message_err', TBGContext::geti18n()->__('Please log in'));
						self::getResponse()->headerRedirect(self::getRouting()->generate('login_page'), 403);
					}
					if (is_dir(THEBUGGENIE_MODULES_PATH . $route['module']))
					{
						if (!file_exists(THEBUGGENIE_MODULES_PATH . $route['module'] . DS . 'classes' . DS . 'actions.class.php'))
						{
							throw new TBGActionNotFoundException('The ' . $route['module'] . ' module is missing the classes/actions.class.php file, containing all the module actions');
						}
						if (!class_exists($route['module'].'Actions') && !class_exists($route['module'].'ActionComponents'))
						{
							self::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . $route['module'] . DS . 'classes' . DS);
						}
						if (self::performAction($route['module'], $route['action']))
						{
							if (self::isDebugMode()) self::generateDebugInfo();
							if (\b2db\Core::isInitialized())
							{
								\b2db\Core::closeDBLink();
							}
							return true;
						}
					}
					else
					{
						throw new Exception('Cannot load the ' . $route['module'] . ' module');
					}
				}
				else
				{
					require THEBUGGENIE_MODULES_PATH . 'main' . DS . 'classes' . DS . 'actions.class.php';
					self::performAction('main', 'notFound');
					if (self::isDebugMode()) self::generateDebugInfo();
				}
			}
			catch (TBGTemplateNotFoundException $e)
			{
				\b2db\Core::closeDBLink();
				header("HTTP/1.0 404 Not Found", true, 404);
				throw $e;
			}
			catch (TBGActionNotFoundException $e)
			{
				\b2db\Core::closeDBLink();
				header("HTTP/1.0 404 Not Found", true, 404);
				throw $e;
			}
			catch (TBGCSRFFailureException $e)
			{
				\b2db\Core::closeDBLink();
				if (self::isDebugMode()) self::generateDebugInfo();
				$this->getResponse()->setHttpStatus(301);
				$message = $e->getMessage();

				if (self::getRequest()->getRequestedFormat() == 'json')
				{
					$this->getResponse()->setContentType('application/json');
					$message = json_encode(array('message' => $message));
				}

				$this->getResponse()->renderHeaders();
				echo $message;
			}
			catch (Exception $e)
			{
				\b2db\Core::closeDBLink();
				header("HTTP/1.0 404 Not Found", true, 404);
				throw $e;
			}
		}

		protected static function generateDebugInfo()
		{
			$tbg_summary = array();
			$load_time = self::getLoadtime();
			if (\b2db\Core::isInitialized())
			{
				$tbg_summary['db']['queries'] = \b2db\Core::getSQLHits();
				$tbg_summary['db']['timing'] = \b2db\Core::getSQLTiming();
			}
			$tbg_summary['load_time'] = ($load_time >= 1) ? round($load_time, 2) . ' seconds' : round($load_time * 1000, 1) . 'ms';
			$tbg_summary['scope'] = array();
			$scope = self::getScope();
			$tbg_summary['scope']['id'] = $scope instanceof TBGScope ? $scope->getID() : 'unknown';
			$tbg_summary['scope']['hostnames'] = ($scope instanceof TBGScope && \b2db\Core::isConnected()) ? implode(', ', $scope->getHostnames()) : 'unknown';
			$tbg_summary['settings'] = TBGSettings::getAll();
			$tbg_summary['partials'] = self::getVisitedPartials();
			if (self::$_i18n instanceof TBGI18n) {
				foreach (self::getI18n()->getMissingStrings() as $text) {
					TBGLogging::log('The text "' . $text . '" does not exist in list of translated strings, and was added automatically', 'i18n', TBGLogging::LEVEL_NOTICE);
				}
			}
			$tbg_summary['log'] = TBGLogging::getEntries();
			$tbg_summary['routing'] = array('name' => self::getRouting()->getCurrentRouteName(), 'module' => self::getRouting()->getCurrentRouteModule(), 'action' => self::getRouting()->getCurrentRouteAction());
			if (isset($_SESSION))
			{
				if (!array_key_exists('___DEBUGINFO___', $_SESSION))
				{
					$_SESSION['___DEBUGINFO___'] = array();
				}
				$_SESSION['___DEBUGINFO___'][self::$debug_id] = $tbg_summary;
				while (count($_SESSION['___DEBUGINFO___']) > 10)
					array_shift($_SESSION['___DEBUGINFO___']);
			}
		}

		public static function getDebugData($debug_id)
		{
			if (!array_key_exists('___DEBUGINFO___', $_SESSION)) return null;
			if (!array_key_exists($debug_id, $_SESSION['___DEBUGINFO___'])) return null;
			
			return $_SESSION['___DEBUGINFO___'][$debug_id];
		}

		public static function getDebugID()
		{
			return self::$debug_id;
		}

		public static function getURLhost()
		{
			return self::getScope()->getCurrentHostname();
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
			return self::$_debug_mode;
		}

		public static function setDebugMode($value = true)
		{
			self::$_debug_mode = $value;
		}

	}
