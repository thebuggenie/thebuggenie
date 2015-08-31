<?php

namespace thebuggenie\core\framework;

use thebuggenie\core\framework\cli,
    thebuggenie\core\entities\Client,
    thebuggenie\core\entities\Module,
    thebuggenie\core\entities\Project,
    thebuggenie\core\entities\Scope,
    thebuggenie\core\entities\User,
    thebuggenie\core\entities\tables\Permissions;
use thebuggenie\core\helpers\TextParserMarkdown;

/**
 * The core class of the framework powering thebuggenie
 *
 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
 * @version 3.1
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage core
 */

/**
 * The core class of the framework powering thebuggenie
 *
 * @package thebuggenie
 * @subpackage core
 */
class Context
{

    protected static $_environment = 2;
    protected static $_debug_mode = true;
    protected static $debug_id = null;
    protected static $_configuration = null;
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
     * @var \thebuggenie\core\entities\User
     */
    protected static $_user = null;

    /**
     * List of modules
     *
     * @var array|\thebuggenie\core\entities\Module
     */
    protected static $_modules = array();

    /**
     * List of internal modules
     *
     * @var array|string
     */
    protected static $_internal_modules = array();

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
    protected static $_webroot = null;

    /**
     * Stripped version of the $_webroot
     *
     * @see $_webroot
     *
     * @var string
     */
    protected static $_stripped_webroot = null;

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
     * @var \thebuggenie\core\framework\I18n
     */
    protected static $_i18n = null;

    /**
     * The request object
     *
     * @var \thebuggenie\core\framework\Request
     */
    protected static $_request = null;

    /**
     * The current action object
     *
     * @var \thebuggenie\core\framework\Action
     */
    protected static $_action = null;

    /**
     * The response object
     *
     * @var \thebuggenie\core\framework\Response
     */
    protected static $_response = null;

    /**
     * The current scope object
     *
     * @var \thebuggenie\core\entities\Scope
     */
    protected static $_scope = null;

    /**
     * The currently selected project, if any
     *
     * @var \thebuggenie\core\entities\Project
     */
    protected static $_selected_project = null;

    /**
     * The currently selected client, if any
     *
     * @var \thebuggenie\core\entities\Client
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
     * @var \thebuggenie\core\framework\Routing
     */
    protected static $_routing = null;

    /**
     * The cache object
     *
     * @var Cache
     */
    protected static $_cache = null;

    /**
     * Messages passed on from the previous request
     *
     * @var array
     */
    protected static $_messages = null;
    protected static $_redirect_login = null;

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
        cli\Command::cliError($title, $exception);
    }

    /**
     * Displays a nicely formatted exception message
     *
     * @param \Exception $exception
     */
    public static function exceptionHandler($exception)
    {
        if (self::isDebugMode() && !self::isInstallmode())
            self::generateDebugInfo();

        if (self::getRequest() instanceof Request && self::getRequest()->isAjaxCall())
        {
            self::getResponse()->ajaxResponseText(404, $exception->getMessage());
        }

        if (self::isCLI())
        {
            self::cliError($exception->getMessage(), $exception);
        }
        else
        {
            self::getResponse()->cleanBuffer();
            require THEBUGGENIE_CORE_PATH . 'templates' . DS . 'error.php';
        }
        die();
    }

    public static function errorHandler($code, $error, $file, $line)
    {
        if (self::isDebugMode())
            self::generateDebugInfo();

        if (self::getRequest() instanceof Request && self::getRequest()->isAjaxCall())
        {
            self::getResponse()->ajaxResponseText(404, $error);
        }

        $details = compact('code', 'error', 'file', 'line');

        if (self::isCLI())
        {
            self::cliError($error, $details);
        }
        else
        {
            self::getResponse()->cleanBuffer();
            require THEBUGGENIE_CORE_PATH . 'templates' . DS . 'error.php';
        }
        die();
    }

    /**
     * Setup the routing object with CLI parameters
     *
     * @param string $module
     * @param string $action
     */
    public static function setCLIRouting($module, $action)
    {
        $routing = self::getRouting();
        $routing->setCurrentRouteModule($module);
        $routing->setCurrentRouteAction($action);
        $routing->setCurrentRouteName('cli');
        $routing->setCurrentRouteCSRFenabled(false);
    }

    /**
     * Returns the routing object
     *
     * @return \thebuggenie\core\framework\Routing
     */
    public static function getRouting()
    {
        if (!self::$_routing)
        {
            self::$_routing = new \thebuggenie\core\framework\Routing();
        }
        return self::$_routing;
    }

    /**
     * Returns the cache object
     *
     * @return Cache
     */
    public static function getCache()
    {
        if (!self::$_cache)
        {
            self::$_cache = new Cache();
        }
        return self::$_cache;
    }

    /**
     * Get the subdirectory part of the url
     *
     * @return string
     */
    public static function getWebroot()
    {
        if (self::$_webroot === null)
        {
            self::_setWebroot();
        }
        return self::$_webroot;
    }

    /**
     * Get the subdirectory part of the url, stripped
     *
     * @return string
     */
    public static function getStrippedWebroot()
    {
        if (self::$_stripped_webroot === null)
        {
            self::$_stripped_webroot = (self::isCLI()) ? '' : rtrim(self::getWebroot(), '/');
        }
        return self::$_stripped_webroot;
    }

    /**
     * Set the subdirectory part of the url, from the url
     */
    protected static function _setWebroot()
    {
        self::$_webroot = defined('\thebuggenie\core\entities\_CLI') ? '.' : dirname($_SERVER['PHP_SELF']);
        if (stristr(PHP_OS, 'WIN'))
        {
            self::$_webroot = str_replace("\\", "/", self::$_webroot); /* Windows adds a \ to the URL which we don't want */
        }
        if (self::$_webroot[strlen(self::$_webroot) - 1] != '/')
            self::$_webroot .= '/';
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

    public static function isInitialized()
    {
        return (self::$_loadstart !== null);
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
            self::getCache()->disable();
        }
        else
        {
            $version_info = explode(',', file_get_contents(THEBUGGENIE_PATH . 'installed'));
            if (count($version_info) < 2)
                throw new exceptions\ConfigurationException("Version information not present", exceptions\ConfigurationException::NO_VERSION_INFO);

            $current_version = $version_info[0];
            if ($current_version != Settings::getVersion(false, false))
                throw new exceptions\ConfigurationException("You are trying to use a newer version of The Bug Genie than the one you installed", exceptions\ConfigurationException::UPGRADE_REQUIRED);

            self::$_installmode = false;
            self::$_upgrademode = false;
        }
        if (self::$_installmode)
        {
            Logging::log('Installation mode enabled');
        }
        if (self::$_upgrademode)
        {
            Logging::log('Upgrade mode enabled');
        }
    }

    public static function isReadySetup()
    {
        return (!(self::isInstallmode() || self::isUpgrademode()));
    }

    public static function initializeSession()
    {
        Logging::log('Initializing session');
        session_name(THEBUGGENIE_SESSION_NAME);
        session_start();
        Logging::log('done (initializing session)');
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
            // The time the script was loaded
            $starttime = explode(' ', microtime());
            define('NOW', (integer) $starttime[1]);

            // Set up error and exception handling
            set_exception_handler(array('\thebuggenie\core\framework\Context', 'exceptionHandler'));
            set_error_handler(array('\thebuggenie\core\framework\Context', 'errorHandler'));
            error_reporting(E_ALL | E_NOTICE | E_STRICT);

            // Set the start time
            self::setLoadStart($starttime[1] + $starttime[0]);

            self::checkInstallMode();
            self::getCache()->setPrefix(str_replace('.', '_', Settings::getVersion()));

            if (!self::isReadySetup())
            {
                self::getCache()->disable();
            }
            else
            {
                self::getCache()->checkEnabled();
                if (self::getCache()->isEnabled())
                {
                    Logging::log((self::getCache()->getCacheType() == Cache::TYPE_APC) ? 'Caching enabled: APC, filesystem' : 'Caching enabled: filesystem');
                }
                else
                {
                    Logging::log('No caching available');
                }
            }

            self::loadConfiguration();

            if (self::$_debug_mode)
                self::$debug_id = uniqid();

            Logging::log('Initializing Caspar framework');
            Logging::log('PHP_SAPI says "' . PHP_SAPI . '"');
            Logging::log('We are version "' . Settings::getVersion() . '"');
            Logging::log('Debug mode: ' . ((self::$_debug_mode) ? 'yes' : 'no'));

            if (!self::isCLI() && !ini_get('session.auto_start'))
                self::initializeSession();

            Logging::log('Loading B2DB');

            if (array_key_exists('b2db', self::$_configuration))
                \b2db\Core::initialize(self::$_configuration['b2db'], self::getCache());
            else
                \b2db\Core::initialize(array(), self::getCache());

            if (self::isReadySetup() && !\b2db\Core::isInitialized())
            {
                throw new exceptions\ConfigurationException("The Bug Genie seems installed, but B2DB isn't configured.", exceptions\ConfigurationException::NO_B2DB_CONFIGURATION);
            }

            Logging::log('...done (Initializing B2DB)');

            if (\b2db\Core::isInitialized() && self::isReadySetup())
            {
                Logging::log('Database connection details found, connecting');
                \b2db\Core::doConnect();
                Logging::log('...done (Database connection details found, connecting)');
            }

            Logging::log('...done');

            Logging::log('Initializing context');

            mb_internal_encoding("UTF-8");
            mb_language('uni');
            mb_http_output("UTF-8");

            Logging::log('Loading scope');
            self::setScope();
            Logging::log('done (loading scope)');

            self::loadInternalModules();
            if (!self::isInstallmode())
            {
                self::setupCoreListeners();
                self::loadModules();
            }

            if (!self::getRouting()->hasCachedRoutes())
            {
                self::loadRoutes();
            }
            else
            {
                self::loadCachedRoutes();
            }

            Logging::log('...done');
            Logging::log('...done initializing');

            Logging::log('Caspar framework loaded');
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    protected static function setupI18n()
    {
        Logging::log('Initializing i18n');
        if (!self::isCLI())
        {
            $language = (self::$_user instanceof User) ? self::$_user->getLanguage() : Settings::getLanguage();

            if (self::$_user instanceof User && self::$_user->getLanguage() == 'sys')
            {
                $language = Settings::getLanguage();
            }

            Logging::log("Initializing i18n with language {$language}");
            self::$_i18n = new I18n($language);
            self::$_i18n->initialize();
        }
        Logging::log('done (initializing i18n)');
    }

    protected static function initializeUser()
    {
        Logging::log('Loading user');
        try
        {
            Logging::log('is this logout?');
            if (self::getRequest()->getParameter('logout'))
            {
                Logging::log('yes');
                self::logout();
            }
            else
            {
                Logging::log('no');
                Logging::log('sets up user object');
                $event = Event::createNew('core', 'pre_login');
                $event->trigger();

                if ($event->isProcessed())
                    self::loadUser($event->getReturnValue());
                elseif (!self::isCLI())
                    self::loadUser();
                else
                    self::$_user = new User();

                Event::createNew('core', 'post_login', self::getUser())->trigger();

                Logging::log('loaded');
                Logging::log('caching permissions');
                self::cacheAllPermissions();
                Logging::log('done (caching permissions)');
            }
        }
        catch (exceptions\ElevatedLoginException $e)
        {
            Logging::log("Could not reauthenticate elevated permissions: " . $e->getMessage(), 'main', Logging::LEVEL_INFO);
            self::setMessage('elevated_login_message_err', $e->getMessage());
            self::$_redirect_login = 'elevated_login';
        }
        catch (\Exception $e)
        {
            Logging::log("Something happened while setting up user: " . $e->getMessage(), 'main', Logging::LEVEL_WARNING);

            $is_anonymous_route = self::isCLI() || self::getRouting()->isCurrentRouteAnonymousRoute();

            if (!$is_anonymous_route)
            {
                self::setMessage('login_message_err', $e->getMessage());
                self::$_redirect_login = 'login';
            }
            else
            {
                self::$_user = User::getB2DBTable()->selectById(Settings::getDefaultUserID());
            }
        }
        Logging::log('...done');
    }

    protected static function setupCoreListeners()
    {
        Event::listen('core', 'thebuggenie\core\entities\File::hasAccess', '\thebuggenie\core\entities\Project::listen_thebuggenie_core_entities_File_hasAccess');
        Event::listen('core', 'thebuggenie\core\entities\File::hasAccess', '\thebuggenie\core\entities\Build::listen_thebuggenie_core_entities_File_hasAccess');
        Event::listen('core', 'thebuggenie\core\entities\File::hasAccess', '\thebuggenie\core\framework\Settings::listen_thebuggenie_core_entities_File_hasAccess');
    }

    public static function clearRoutingCache()
    {
        self::getCache()->delete(Cache::KEY_ROUTES_CACHE, true, true);
        self::getCache()->delete(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, true, true);
        self::getCache()->delete(Cache::KEY_ANNOTATION_LISTENERS_CACHE, true, true);
        self::getCache()->fileDelete(Cache::KEY_ROUTES_CACHE, true, true);
        self::getCache()->fileDelete(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, true, true);
        self::getCache()->fileDelete(Cache::KEY_ANNOTATION_LISTENERS_CACHE, true, true);
    }

    public static function clearMenuLinkCache()
    {
        if (!self::getCache()->isEnabled())
            return;
        foreach (array(Cache::KEY_MAIN_MENU_LINKS) as $key)
        {
            self::getCache()->delete($key);
            self::getCache()->fileDelete($key);
        }
    }

    protected static function loadEventListeners($event_listeners)
    {
        Logging::log('Loading event listeners');
        foreach ($event_listeners as $listener)
        {
            list($event_module, $event_identifier, $module, $method) = $listener;
            Event::listen($event_module, $event_identifier, array(self::getModule($module), $method));
        }
        Logging::log('... done (loading event listeners)');
    }

    protected static function loadRoutes()
    {
        Logging::log('Loading routes from routing files', 'routing');

        foreach (array('internal' => self::$_internal_modules, 'external' => self::getModules()) as $module_type => $modules)
        {
            foreach ($modules as $module_name => $module)
            {
                self::getRouting()->loadRoutes($module_name, $module_type);
            }
        }
        self::getRouting()->loadYamlRoutes(\THEBUGGENIE_CONFIGURATION_PATH . 'routes.yml');
        self::loadEventListeners(self::getRouting()->getAnnotationListeners());

        if (!self::isInstallmode())
        {
            self::getRouting()->cache();
        }

        Logging::log('...done (loading routes from routing file)', 'routing');
    }

    protected static function loadCachedRoutes()
    {
        Logging::log('Loading routes from cache', 'routing');
        $routes = self::getCache()->get(Cache::KEY_ROUTES_CACHE);
        $component_override_map = self::getCache()->get(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE);
        $annotation_listeners = self::getCache()->get(Cache::KEY_ANNOTATION_LISTENERS_CACHE);
        if ($routes === null)
        {
            Logging::log('Loading routes from disk cache', 'routing');
            $routes = self::getCache()->fileGet(Cache::KEY_ROUTES_CACHE);
        }
        if ($component_override_map === null)
        {
            Logging::log('Loading component override mappings from disk cache', 'routing');
            $component_override_map = self::getCache()->fileGet(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE);
        }
        if ($annotation_listeners === null)
        {
            Logging::log('Loading event listeners from disk cache', 'routing');
            $annotation_listeners = self::getCache()->fileGet(Cache::KEY_ANNOTATION_LISTENERS_CACHE);
        }

        if ($routes === null || $component_override_map === null || $annotation_listeners === null)
            throw new exceptions\CacheException('There is an issue with the cache. Clear the cache and try again.');

        Logging::log('Setting routes from cache', 'routing');
        self::getRouting()->setRoutes($routes);
        Logging::log('Setting component override mappings from cache', 'routing');
        self::getRouting()->setComponentOverrideMap($component_override_map);
        Logging::log('Setting annotation listeners from cache', 'routing');
        self::loadEventListeners($annotation_listeners);
        Logging::log('...done', 'routing');
    }

    protected static function loadConfiguration()
    {
        Logging::log('Loading configuration from cache', 'core');
        if (self::isReadySetup())
        {
            $configuration = self::getCache()->get(Cache::KEY_CONFIGURATION, false);
            if (!$configuration)
            {
                Logging::log('Loading configuration from disk cache', 'core');
                $configuration = self::getCache()->fileGet(Cache::KEY_CONFIGURATION, false);
            }
        }

        if (!self::isReadySetup() || !$configuration)
        {
            Logging::log('Loading configuration from files', 'core');
            $config_filename = \THEBUGGENIE_CONFIGURATION_PATH . "settings.yml";
            $b2db_filename = \THEBUGGENIE_CONFIGURATION_PATH . "b2db.yml";
            if (!file_exists($config_filename))
                throw new \Exception("The configuration file ({$config_filename} does not exist.");

            $config = \Spyc::YAMLLoad($config_filename);
            $b2db_config = \Spyc::YAMLLoad($b2db_filename);
            $configuration = array_merge($config, $b2db_config);

            if (self::isReadySetup())
            {
                self::getCache()->fileAdd(Cache::KEY_CONFIGURATION, $configuration, false);
                self::getCache()->add(Cache::KEY_CONFIGURATION, $configuration, false);
            }
        }
        self::$_configuration = $configuration;

        self::$_debug_mode = self::$_configuration['core']['debug'];
        Logging::log('...done', 'core');
    }

    /**
     * Returns the request object
     *
     * @return \thebuggenie\core\framework\Request
     */
    public static function getRequest()
    {
        if (!self::$_request instanceof Request)
        {
            self::$_request = new Request();
        }
        return self::$_request;
    }

    /**
     * Returns the current action object
     *
     * @return \thebuggenie\core\framework\Action
     */
    public static function getCurrentAction()
    {
        return self::$_action;
    }

    /**
     * Returns the response object
     *
     * @return \thebuggenie\core\framework\Response
     */
    public static function getResponse()
    {
        if (!self::$_response instanceof Response)
        {
            self::$_response = new Response();
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
            self::$_i18n = new I18n(Settings::get('language'));
        }
        else
        {
            Logging::log('Changing language to ' . $language);
            self::$_i18n = new I18n($language);
            self::$_i18n->initialize();
        }
    }

    /**
     * Get the i18n object
     *
     * @return \thebuggenie\core\framework\I18n
     */
    public static function getI18n()
    {
        if (!self::isI18nInitialized())
        {
            Logging::log('Cannot access the translation object until the i18n system has been initialized!', 'i18n', Logging::LEVEL_WARNING);
            throw new \Exception('Cannot access the translation object until the i18n system has been initialized!');
        }
        return self::$_i18n;
    }

    public static function isI18nInitialized()
    {
        return (self::$_i18n instanceof I18n);
    }

    /**
     * Get available themes
     *
     * @return array
     */
    public static function getThemes()
    {
        $theme_path_handle = opendir(THEBUGGENIE_PATH . 'themes' . DS);
        $themes = array();

        while ($theme = readdir($theme_path_handle))
        {
            if ($theme != '.' && $theme != '..' && is_dir(THEBUGGENIE_PATH . 'themes' . DS . $theme) && file_exists(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'theme.php'))
            {
                $themes[$theme] = array(
                    'key' => $theme,
                    'name' => ucfirst($theme),
                    'version' => file_get_contents(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'VERSION'),
                    'author' => file_get_contents(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'AUTHOR'),
                    'description' => TextParserMarkdown::defaultTransform(file_get_contents(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'README.md'))
                );
            }
        }

        return $themes;
    }

    /**
     * Load the user object into the user property
     *
     * @return \thebuggenie\core\entities\User
     */
    public static function loadUser($user = null)
    {
        try
        {
            self::$_user = ($user === null) ? User::loginCheck(self::getRequest(), self::getCurrentAction()) : $user;
            if (self::$_user->isAuthenticated())
            {
                if (!self::getRequest()->hasCookie('tbg3_original_username'))
                {
                    self::$_user->updateLastSeen();
                }
                if (!self::getScope()->isDefault() && !self::getRequest()->isAjaxCall() && !in_array(self::getRouting()->getCurrentRouteName(), array('add_scope', 'debugger', 'logout')) && !self::$_user->isGuest() && !self::$_user->isConfirmedMemberOfScope(self::getScope()))
                {
                    self::getResponse()->headerRedirect(self::getRouting()->generate('add_scope'));
                }
                self::$_user->save();
                if (!(self::$_user->getGroup() instanceof \thebuggenie\core\entities\Group))
                {
                    throw new \Exception('This user account belongs to a group that does not exist anymore. <br>Please contact the system administrator.');
                }
            }
        }
        catch (exceptions\ElevatedLoginException $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            self::$_user = new User();
            throw $e;
        }
        return self::$_user;
    }

    /**
     * Returns the user object
     *
     * @return \thebuggenie\core\entities\User
     */
    public static function getUser()
    {
        return self::$_user;
    }

    /**
     * Set the current user
     *
     * @param \thebuggenie\core\entities\User $user
     */
    public static function setUser(User $user)
    {
        self::$_user = $user;
    }

    public static function switchUserContext(User $user)
    {
        self::setUser($user);
        Settings::forceSettingsReload();
        self::cacheAllPermissions();
    }

    /**
     * Loads and initializes internal modules
     */
    public static function loadInternalModules()
    {
        Logging::log('Loading internal modules');

        $modules = self::getCache()->get(Cache::KEY_INTERNAL_MODULES, false);
        if (self::isReadySetup() || !$modules)
        {
            foreach (scandir(THEBUGGENIE_INTERNAL_MODULES_PATH) as $modulename)
            {
                if (in_array($modulename, array('.', '..')) || !is_dir(THEBUGGENIE_INTERNAL_MODULES_PATH . $modulename))
                    continue;

                self::$_internal_modules[$modulename] = $modulename;
            }
        }
        else
        {
            Logging::log('Loading cached modules');
            self::$_internal_modules = $modules;
        }

        Logging::log('...done (loading internal modules)');
    }

    /**
     * Loads and initializes all modules
     */
    public static function loadModules()
    {
        Logging::log('Loading modules');

        if (self::isInstallmode())
            return;

        if (self::isUpgrademode())
        {
            self::$_modules = Module::getB2DBTable()->getAllNames();
            return;
        }

        Logging::log('getting modules from database');
        self::$_modules = Module::getB2DBTable()->getAll();
        Logging::log('done (setting up module objects)');

        Logging::log('initializing modules');
        if (!empty(self::$_modules))
        {
            foreach (self::$_modules as $module)
            {
                $module->initialize();
            }
            Logging::log('done (initializing modules)');
        }
        else
        {
            Logging::log('no modules found');
        }
        Logging::log('...done (loading modules)');
    }

    public static function finishUpgrading()
    {
        self::$_upgrademode = false;
        self::$_installmode = false;
        self::loadModules();
    }

    /**
     * Adds a module to the module list
     *
     * @param \thebuggenie\core\entities\Module $module
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
     * Unloads a loaded module
     *
     * @param string $module_name The name of the module to unload
     */
    public static function unloadModule($module_name)
    {
        if (isset(self::$_modules[$module_name]))
        {
            unset(self::$_modules[$module_name]);
            Event::clearListeners($module_name);
        }
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
     * @return array|\thebuggenie\core\entities\Module
     */
    public static function getUninstalledModules()
    {
        $module_path_handle = opendir(THEBUGGENIE_MODULES_PATH);
        $modules = array();
        while ($module_name = readdir($module_path_handle))
        {
            if (is_dir(THEBUGGENIE_MODULES_PATH . $module_name) && file_exists(THEBUGGENIE_MODULES_PATH . $module_name . DS . ucfirst($module_name) . '.php'))
            {
                if (self::isModuleLoaded($module_name))
                    continue;
                $module_class = "\\thebuggenie\\modules\\{$module_name}\\".ucfirst($module_name);
                if (class_exists($module_class))
                {
                    $modules[$module_name] = new $module_class();
                }
            }
        }
        return $modules;
    }

    /**
     * Returns a specified module
     *
     * @param string $module_name
     *
     * @return \thebuggenie\core\entities\Module
     */
    public static function getModule($module_name)
    {
        if (!self::isModuleLoaded($module_name) && !isset(self::$_internal_modules[$module_name]))
        {
            throw new \Exception("The module '{$module_name}' is not loaded");
        }
        else
        {
            return (isset(self::$_internal_modules[$module_name])) ? self::$_internal_modules[$module_name] : self::$_modules[$module_name];
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
        return isset(self::$_modules[$module_name]);
    }

    /**
     * Return all permissions available
     *
     * @param string $type
     * @param integer $uid
     * @param integer $tid
     * @param integer $gid
     * @param integer $target_id [optional]
     * @param boolean $all [optional]
     *
     * @return array
     */
    public static function getAllPermissions($type, $uid, $tid, $gid, $target_id = null, $all = false)
    {
        $crit = new \b2db\Criteria();
        $crit->addWhere(Permissions::SCOPE, self::getScope()->getID());
        $crit->addWhere(Permissions::PERMISSION_TYPE, $type);

        if (($uid + $tid + $gid) == 0 && !$all)
        {
            $crit->addWhere(Permissions::UID, $uid);
            $crit->addWhere(Permissions::TID, $tid);
            $crit->addWhere(Permissions::GID, $gid);
        }
        else
        {
            switch (true)
            {
                case ($uid != 0):
                    $crit->addWhere(Permissions::UID, $uid);
                case ($tid != 0):
                    $crit->addWhere(Permissions::TID, $tid);
                case ($gid != 0):
                    $crit->addWhere(Permissions::GID, $gid);
            }
        }
        if ($target_id !== null)
        {
            $crit->addWhere(Permissions::TARGET_ID, $target_id);
        }

        $permissions = array();

        if ($res = Permissions::getTable()->doSelect($crit))
        {
            while ($row = $res->getNextRow())
            {
                $permissions[] = array('p_type' => $row->get(Permissions::PERMISSION_TYPE), 'target_id' => $row->get(Permissions::TARGET_ID), 'allowed' => $row->get(Permissions::ALLOWED), 'uid' => $row->get(Permissions::UID), 'gid' => $row->get(Permissions::GID), 'tid' => $row->get(Permissions::TID), 'id' => $row->get(Permissions::ID));
            }
        }

        return $permissions;
    }

    /**
     * Cache all permissions
     */
    public static function cacheAllPermissions()
    {
        Logging::log('caches permissions');
        self::$_permissions = array();

        if (!self::isInstallmode() && $permissions = self::getCache()->get(Cache::KEY_PERMISSIONS_CACHE))
        {
            self::$_permissions = $permissions;
            Logging::log('Using cached permissions');
        }
        else
        {
            if (self::isInstallmode() || !$permissions = self::getCache()->fileGet(Cache::KEY_PERMISSIONS_CACHE))
            {
                Logging::log('starting to cache access permissions');
                if ($res = Permissions::getTable()->getAll())
                {
                    while ($row = $res->getNextRow())
                    {
                        if (!array_key_exists($row->get(Permissions::MODULE), self::$_permissions))
                        {
                            self::$_permissions[$row->get(Permissions::MODULE)] = array();
                        }
                        if (!array_key_exists($row->get(Permissions::PERMISSION_TYPE), self::$_permissions[$row->get(Permissions::MODULE)]))
                        {
                            self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)] = array();
                        }
                        if (!array_key_exists($row->get(Permissions::TARGET_ID), self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)]))
                        {
                            self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)][$row->get(Permissions::TARGET_ID)] = array();
                        }
                        self::$_permissions[$row->get(Permissions::MODULE)][$row->get(Permissions::PERMISSION_TYPE)][$row->get(Permissions::TARGET_ID)][] = array('uid' => $row->get(Permissions::UID), 'gid' => $row->get(Permissions::GID), 'tid' => $row->get(Permissions::TID), 'allowed' => (bool) $row->get(Permissions::ALLOWED), 'role_id' => $row->get(Permissions::ROLE_ID));
                    }
                }
                Logging::log('done (starting to cache access permissions)');
                if (!self::isInstallmode())
                    self::getCache()->fileAdd(Cache::KEY_PERMISSIONS_CACHE, self::$_permissions);
            }
            else
            {
                self::$_permissions = $permissions;
            }
            if (!self::isInstallmode())
                self::getCache()->add(Cache::KEY_PERMISSIONS_CACHE, self::$_permissions);
        }
        Logging::log('...cached');
    }

    public static function deleteModulePermissions($module_name, $scope)
    {
        if ($scope == self::getScope()->getID())
        {
            if (array_key_exists($module_name, self::$_permissions))
            {
                unset(self::$_permissions[$module_name]);
            }
        }
        Permissions::getTable()->deleteModulePermissions($module_name, $scope);
    }

    public static function clearPermissionsCache()
    {
        self::getCache()->delete(Cache::KEY_PERMISSIONS_CACHE, true, true);
        self::getCache()->fileDelete(Cache::KEY_PERMISSIONS_CACHE, true, true);
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
    public static function removePermission($permission_type, $target_id, $module, $uid, $gid, $tid, $recache = true, $scope = null, $role_id = null)
    {
        if ($scope === null)
            $scope = self::getScope()->getID();

        Permissions::getTable()->removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope, $role_id);
        self::clearPermissionsCache();

        if ($recache)
            self::cacheAllPermissions();
    }

    public static function removeAllPermissionsForCombination($uid, $gid, $tid, $target_id = 0, $role_id = null)
    {
        Permissions::getTable()->deleteAllPermissionsForCombination($uid, $gid, $tid, $target_id, $role_id);
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
     * @param integer $scope [optional] A specified scope if not the default
     */
    public static function setPermission($permission_type, $target_id, $module, $uid, $gid, $tid, $allowed, $scope = null, $role_id = null)
    {
        if ($scope === null)
            $scope = self::getScope()->getID();

        if ($role_id === null)
        {
            self::removePermission($permission_type, $target_id, $module, $uid, $gid, $tid, false, $scope, 0);
        }
        Permissions::getTable()->setPermission($uid, $gid, $tid, $allowed, $module, $permission_type, $target_id, $scope, $role_id);
        self::clearPermissionsCache();

        self::cacheAllPermissions();
    }

    public static function isPermissionSet($type, $permission_key, $id, $target_id = 0, $module_name = 'core', $without_role = null)
    {
        if (array_key_exists($module_name, self::$_permissions) &&
                array_key_exists($permission_key, self::$_permissions[$module_name]) &&
                array_key_exists($target_id, self::$_permissions[$module_name][$permission_key]))
        {
            if ($type == 'group')
            {
                foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
                {
                    if ($permission['gid'] == $id && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                        return $permission['allowed'];
                }
            }
            if ($type == 'user')
            {
                foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
                {
                    if ($permission['uid'] == $id && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                        return $permission['allowed'];
                }
            }
            if ($type == 'team')
            {
                foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
                {
                    if ($permission['tid'] == $id && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                        return $permission['allowed'];
                }
            }
            if ($type == 'everyone')
            {
                foreach (self::$_permissions[$module_name][$permission_key][$target_id] as $permission)
                {
                    if ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0 && (($without_role == true && $permission['role_id'] == 0) || ($without_role == false && $permission['role_id'] != 0)))
                    {
                        return $permission['allowed'];
                    }
                }
            }
        }
        return null;
    }

    protected static function _permissionsCheck($permissions, $uid, $gid, $tid, $permission_roles_allowed, $target_id)
    {
        try
        {
            if (! is_array($permission_roles_allowed))
            {
                $permission_roles_allowed = array();
            }
            if ($uid != 0 || $gid != 0 || $tid != 0)
            {
                if ($uid != 0)
                {
                    $new_permission_roles_allowed = array();
                    foreach ($permissions as $key => $permission)
                    {
                        if (!array_key_exists('uid', $permission))
                        {
                            foreach ($permission as $pkey => $pp)
                            {
                                if ($pp['uid'] == $uid)
                                {
                                    if ($pp['role_id'] == 0)
                                    {
                                        return $pp['allowed'];
                                    }

                                    $new_permission_roles_allowed[] = $pp;
                                }
                            }
                        }
                        elseif ($permission['uid'] == $uid)
                        {
                            if ($permission['role_id'] == 0)
                            {
                                return $permission['allowed'];
                            }

                            $new_permission_roles_allowed[] = $permission;
                        }
                    }
                    if (count($new_permission_roles_allowed))
                    {
                        return array_merge($permission_roles_allowed, $new_permission_roles_allowed);
                    }
                }

                if (is_array($tid) || $tid != 0)
                {
                    $new_permission_roles_allowed = array();
                    foreach ($permissions as $key => $permission)
                    {
                        if (!array_key_exists('tid', $permission))
                        {
                            foreach ($permission as $pkey => $pp)
                            {
                                if ((is_array($tid) && in_array($pp['tid'], array_keys($tid))) || $pp['tid'] == $tid)
                                {
                                    if ($pp['role_id'] == 0)
                                    {
                                        return $pp['allowed'];
                                    }

                                    if ($target_id == 0 && self::getCurrentProject() instanceof \thebuggenie\core\entities\Project)
                                    {
                                        $target_id = self::getCurrentProject()->getID();
                                    }

                                    if ($target_id != 0)
                                    {
                                        $role_assigned_teams = \thebuggenie\core\entities\tables\ProjectAssignedTeams::getTable()->getTeamsByRoleIDAndProjectID($pp['role_id'], $target_id);

                                        if (is_array($tid))
                                        {
                                            foreach ($tid as $team)
                                            {
                                                if (array_key_exists($team->getID(), $role_assigned_teams))
                                                {
                                                    $new_permission_roles_allowed[] = $pp;
                                                    break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if (array_key_exists($tid, $role_assigned_teams))
                                            {
                                                $new_permission_roles_allowed[] = $pp;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $new_permission_roles_allowed[] = $pp;
                                    }
                                }
                            }
                        }
                        elseif ((is_array($tid) && in_array($permission['tid'], array_keys($tid))) || $permission['tid'] == $tid)
                        {
                            if ($permission['role_id'] == 0)
                            {
                                return $permission['allowed'];
                            }

                            if ($target_id == 0 && self::getCurrentProject() instanceof \thebuggenie\core\entities\Project)
                            {
                                $target_id = self::getCurrentProject()->getID();
                            }

                            if ($target_id != 0)
                            {
                                $role_assigned_teams = \thebuggenie\core\entities\tables\ProjectAssignedTeams::getTable()->getTeamsByRoleIDAndProjectID($permission['role_id'], $target_id);

                                if (is_array($tid))
                                {
                                    foreach ($tid as $team)
                                    {
                                        if (array_key_exists($team->getID(), $role_assigned_teams))
                                        {
                                            $new_permission_roles_allowed[] = $permission;
                                            break;
                                        }
                                    }
                                }
                                else
                                {
                                    if (array_key_exists($tid, $role_assigned_teams))
                                    {
                                        $new_permission_roles_allowed[] = $permission;
                                    }
                                }
                            }
                            else
                            {
                                $new_permission_roles_allowed[] = $permission;
                            }
                        }

                    }
                    if (count($new_permission_roles_allowed))
                    {
                        return array_merge($permission_roles_allowed, $new_permission_roles_allowed);
                    }
                }

                if ($gid != 0)
                {
                    $new_permission_roles_allowed = array();
                    foreach ($permissions as $key => $permission)
                    {
                        if (!array_key_exists('gid', $permission))
                        {
                            foreach ($permission as $pkey => $pp)
                            {
                                if ($pp['gid'] == $gid)
                                {
                                    if ($pp['role_id'] == 0)
                                    {
                                        return $pp['allowed'];
                                    }

                                    $new_permission_roles_allowed[] = 1;
                                }
                            }
                        }
                        elseif ($permission['gid'] == $gid)
                        {
                            if ($permission['role_id'] == 0)
                            {
                                return $permission['allowed'];
                            }

                            $new_permission_roles_allowed[] = 1;
                        }
                    }
                    if (count($new_permission_roles_allowed))
                    {
                        return array_merge($permission_roles_allowed, $new_permission_roles_allowed);
                    }
                }
            }

            $new_permission_roles_allowed = array();
            foreach ($permissions as $key => $permission)
            {
                if (!array_key_exists('uid', $permission))
                {
                    foreach ($permission as $pkey => $pp)
                    {
                        if ($pp['uid'] + $pp['gid'] + $pp['tid'] == 0)
                        {
                            if ($pp['role_id'] == 0)
                            {
                                return $pp['allowed'];
                            }

                            $new_permission_roles_allowed[] = 1;
                        }
                    }
                }
                elseif ($permission['uid'] + $permission['gid'] + $permission['tid'] == 0)
                {
                    if ($permission['role_id'] == 0)
                    {
                        return $permission['allowed'];
                    }

                    $new_permission_roles_allowed[] = 1;
                }
            }
            if (count($new_permission_roles_allowed))
            {
                return array_merge($permission_roles_allowed, $new_permission_roles_allowed);
            }
        }
        catch (\Exception $e)
        {

        }

        return null;
    }

    /**
     * Check to see if a specified user/group/team has access
     *
     * @param string $permission_type The permission type
     * @param integer $uid The user id for which the permission is valid, 0 for all
     * @param integer $gid The group id for which the permission is valid, 0 for all
     * @param mixed $tid The team id (or an array of teams or team ids) for which the permission is valid, 0 for all
     * @param integer $target_id [optional] The target id
     * @param string $module_name [optional] The name of the module for which the permission is valid
     *
     * @return unknown_type
     */
    public static function checkPermission($permission_type, $uid, $gid, $tid, $target_id = 0, $module_name = 'core', $check_global_role = true)
    {
        $uid = (int) $uid;
        $gid = (int) $gid;
        $retval = null;
        if (array_key_exists($module_name, self::$_permissions) &&
                array_key_exists($permission_type, self::$_permissions[$module_name]))
        {
            if ($check_global_role && array_key_exists(0, self::$_permissions[$module_name][$permission_type]) && $target_id != 0)
            {
                $permissions_notarget = self::$_permissions[$module_name][$permission_type][0];
            }

            if (array_key_exists($target_id, self::$_permissions[$module_name][$permission_type]))
            {
                $permissions_target = (array_key_exists($target_id, self::$_permissions[$module_name][$permission_type])) ? self::$_permissions[$module_name][$permission_type][$target_id] : array();

                $retval = self::_permissionsCheck($permissions_target, $uid, $gid, $tid, array(), $target_id);

            }

            if ($check_global_role && array_key_exists(0, self::$_permissions[$module_name][$permission_type]) && $target_id != 0)
            {
                $retval = ($retval !== null && ! is_array($retval)) ? $retval : self::_permissionsCheck($permissions_notarget, $uid, $gid, $tid, $retval, $target_id);
            }

            if (is_array($retval)) return true;

            if ($retval !== null)
                return $retval;
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
            if (is_numeric($permission_key))
                return null;
            if ($permission_key == $permission)
                return $permission_info;

            if (in_array($permission_key, array_keys(self::$_available_permissions)) || (array_key_exists('details', $permission_info) && is_array($permission_info['details']) && count($permission_info['details'])))
            {
                $p_info = (in_array($permission_key, array_keys(self::$_available_permissions))) ? $permission_info : $permission_info['details'];
                $retval = self::getPermissionDetails($permission, $p_info);
                if ($retval)
                    return $retval;
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
            self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('canviewconfig' => array('description' => $i18n->__('Read-only access: "Themes" configuration page and any themes'), 'target_id' => 19));
            self::$_available_permissions['configuration']['cansaveconfig']['details'][] = array('cansaveconfig' => array('description' => $i18n->__('Read + write access: "Themes" configuration page and any themes'), 'target_id' => 19));
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
            self::$_available_permissions['project']['candoscrumplanning']['details']['canaddscrumsprints'] = array('description' => $i18n->__('Can add milestones/sprints on the project planning page'));
            self::$_available_permissions['project']['candoscrumplanning']['details']['canassignscrumuserstoriestosprints'] = array('description' => $i18n->__('Can (re-)assign issues/tasks/stories to milestones/sprints/backlog on the project planning page'));
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
            foreach (\thebuggenie\core\entities\CustomDatatype::getAll() as $cdf)
            {
                self::$_available_permissions['issues']['caneditissuecustomfieldsown']['details']['caneditissuecustomfields' . $cdf->getKey() . 'own'] = array('description' => $i18n->__('Can change custom field "%field_name" for issues reported by the user', array('%field_name' => $i18n->__($cdf->getDescription()))));
                self::$_available_permissions['issues']['caneditissuecustomfields']['details']['caneditissuecustomfields' . $cdf->getKey()] = array('description' => $i18n->__('Can change custom field "%field_name" for any issues', array('%field_name' => $i18n->__($cdf->getDescription()))));

                // Set permissions for custom option types
                if ($cdf->hasCustomOptions())
                {
                    $options = $cdf->getOptions();
                    foreach ($options as $option)
                    {
                        self::$_available_permissions['issues']['set_datatype_' . $option->getID()] = array('description' => $i18n->__('Can change issue field to "%option_name" for issues reported by the user', array('%option_name' => $i18n->__($option->getValue()))));
                    }//endforeach
                }//endif
            }
            foreach (\thebuggenie\core\entities\Datatype::getTypes() as $type => $class)
            {
                self::$_available_permissions['issues']['set_datatype_' . $type] = array('description' => $i18n->__('Can change issue field "%type_name" for issues reported by the user', array('%type_name' => $i18n->__($type))));
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
            foreach (self::getModules() as $module_key => $module)
            {
                $retarr['module_' . $module_key] = array();
                foreach ($module->getAvailablePermissions() as $mpkey => $mp)
                {
                    $retarr['module_' . $module_key][$mpkey] = $mp;
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
        if (Settings::isUsingExternalAuthenticationBackend())
        {
            $mod = self::getModule(Settings::getAuthenticationBackend());
            $mod->logout();
        }

        Event::createNew('core', 'pre_logout')->trigger();
        self::getResponse()->deleteCookie('tbg3_username');
        self::getResponse()->deleteCookie('tbg3_password');
        self::getResponse()->deleteCookie('tbg3_elevated_password');
        self::getResponse()->deleteCookie('tbg3_persona_session');
        self::getResponse()->deleteCookie('THEBUGGENIE');
        session_regenerate_id(true);
        Event::createNew('core', 'post_logout')->trigger();
    }

    /**
     * Find and set the current scope
     *
     * @param Scope $scope Specify a scope to set for this request
     */
    public static function setScope($scope = null)
    {
        Logging::log("Setting current scope");
        if ($scope !== null)
        {
            Logging::log("Setting scope from function parameter");
            self::$_scope = $scope;
            Settings::forceSettingsReload();
            Logging::log("...done (Setting scope from function parameter)");
            return true;
        }

        $row = null;
        try
        {
            $hostname = null;
            if (!self::isCLI() && !self::isInstallmode())
            {
                Logging::log("Checking if scope can be set from hostname (" . $_SERVER['HTTP_HOST'] . ")");
                $hostname = $_SERVER['HTTP_HOST'];
            }

            if (!self::isUpgrademode() && !self::isInstallmode())
                $scope = \thebuggenie\core\entities\tables\Scopes::getTable()->getByHostnameOrDefault($hostname);

            if (!$scope instanceof Scope)
            {
                Logging::log("It couldn't", 'main', Logging::LEVEL_WARNING);
                if (!self::isInstallmode())
                    throw new \Exception("The Bug Genie isn't set up to work with this server name.");
                else
                    return;
            }

            Logging::log("Setting scope {$scope->getID()} from hostname");
            self::$_scope = $scope;
            Settings::forceSettingsReload();
            Settings::loadSettings();
            Logging::log("...done (Setting scope from hostname)");
            return true;
        }
        catch (\Exception $e)
        {
            if (self::isCLI())
            {
                Logging::log("Couldn't set up default scope.", 'main', Logging::LEVEL_FATAL);
                throw new \Exception("Could not load default scope. Error message was: " . $e->getMessage());
            }
            elseif (!self::isInstallmode())
            {
                Logging::log("Couldn't find a scope for hostname {$_SERVER['HTTP_HOST']}", 'main', Logging::LEVEL_FATAL);
                Logging::log($e->getMessage(), 'main', Logging::LEVEL_FATAL);
                throw new \Exception("Could not load scope. This is usually because the scopes table doesn't have a scope for this hostname");
            }
            else
            {
                Logging::log("Couldn't find a scope for hostname {$_SERVER['HTTP_HOST']}, but we're in installmode so continuing anyway");
            }
        }
    }

    /**
     * Returns current scope
     *
     * @return Scope
     */
    public static function getScope()
    {
        return self::$_scope;
    }

    public static function populateBreadcrumbs()
    {
        $childbreadcrumbs = array();

        if (self::$_selected_project instanceof Project)
        {
            $t = self::$_selected_project;

            $hierarchy_breadcrumbs = array();
            $projects_processed = array();

            while ($t instanceof Project)
            {
                if (array_key_exists($t->getKey(), $projects_processed))
                {
                    // We have a cyclic dependency! Oh no!
                    // If this happens, throw an exception

                    throw new \Exception(self::geti18n()->__('A loop has been found in the project heirarchy. Go to project configuration, and alter the subproject setting for this project so that this project is not a subproject of one which is a subproject of this one.'));
                }

                $projects_processed[$t->getKey()] = $t;

                $itemsubmenulinks = self::getResponse()->getPredefinedBreadcrumbLinks('project_summary', $t);

                if ($t->hasChildren())
                {
                    $itemsubmenulinks[] = array('separator' => true);
                    foreach ($t->getChildren() as $child)
                    {
                        if (!$child->hasAccess())
                            continue;
                        $itemsubmenulinks[] = array('url' => self::getRouting()->generate('project_dashboard', array('project_key' => $child->getKey())), 'title' => $child->getName());
                    }
                }

                $hierarchy_breadcrumbs[] = array($t, $itemsubmenulinks);

                if ($t->hasParent())
                {
                    $parent = $t->getParent();
                    $t = $t->getParent();
                }
                else
                {
                    $t = null;
                }
            }

            if (self::$_selected_project->hasClient())
            {
                self::setCurrentClient(self::$_selected_project->getClient());
            }
            if (mb_strtolower(Settings::getSiteHeaderName()) != mb_strtolower(self::$_selected_project->getName()) || self::isClientContext())
            {
                self::getResponse()->addBreadcrumb(Settings::getSiteHeaderName(), self::getRouting()->generate('home'), self::getResponse()->getPredefinedBreadcrumbLinks('main_links', self::$_selected_project));
                if (self::isClientContext())
                {
                    self::getResponse()->addBreadcrumb(self::getCurrentClient()->getName(), self::getRouting()->generate('client_dashboard', array('client_id' => self::getCurrentClient()->getID())), self::getResponse()->getPredefinedBreadcrumbLinks('client_list'));
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
            self::getResponse()->addBreadcrumb(Settings::getSiteHeaderName(), self::getRouting()->generate('home'), self::getResponse()->getPredefinedBreadcrumbLinks('main_links'));
        }
    }

    /**
     * Set the currently selected project
     *
     * @param \thebuggenie\core\entities\Project $project The project, or null if none
     */
    public static function setCurrentProject($project)
    {
        self::getResponse()->setBreadcrumb(null);
        self::$_selected_project = $project;
    }

    /**
     * Return the currently selected project if any, or null
     *
     * @return \thebuggenie\core\entities\Project
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
        return (bool) (self::getCurrentProject() instanceof Project);
    }

    /**
     * Set the currently selected client
     *
     * @param \thebuggenie\core\entities\Client $client The client, or null if none
     */
    public static function setCurrentClient($client)
    {
        self::$_selected_client = $client;
    }

    /**
     * Return the currently selected client if any, or null
     *
     * @return \thebuggenie\core\entities\Client
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
        return (bool) (self::getCurrentClient() instanceof Client);
    }

    /**
     * Set a message to be retrieved in the next request
     *
     * @param string $key The key
     * @param mixed $message The message
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

    public static function checkCSRFtoken()
    {
        $token = self::generateCSRFtoken();
        if ($token == self::getRequest()->getParameter('csrf_token'))
            return true;

        $message = self::getI18n()->__('An authentication error occured. Please reload your page and try again');
        throw new exceptions\CSRFFailureException($message);
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
                Logging::log("The \"{$lib_name}\" library does not exist in either " . THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . ' or ' . THEBUGGENIE_CORE_PATH . 'lib' . DS, 'core', Logging::LEVEL_FATAL);
                throw new exceptions\LibraryNotFoundException("The \"{$lib_name}\" library does not exist in either " . THEBUGGENIE_MODULES_PATH . self::getRouting()->getCurrentRouteModule() . DS . 'lib' . DS . ' or ' . THEBUGGENIE_CORE_PATH . 'lib' . DS);
            }
        }
    }

    public static function visitPartial($template_name, $time)
    {
        if (!self::$_debug_mode)
            return;
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

    public static function isInternalModule($module)
    {
        return isset(self::$_internal_modules[$module]);
    }

    protected static function setupLayoutProperties($content)
    {
        $basepath = THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS;
        $theme = \thebuggenie\core\framework\Settings::getThemeName();
        foreach (self::getModules() as $module)
        {
            if (file_exists($basepath . 'css' . DS . $theme . DS . "{$module->getName()}.css")) {
                self::getResponse()->addStylesheet(self::getRouting()->generate('asset_css', array('theme_name' => $theme, 'css' => "{$module->getName()}.css")));
            }
            if (file_exists($basepath . 'js' . DS . "{$module->getName()}.js")) {
                self::getResponse()->addJavascript(self::getRouting()->generate('asset_js_unthemed', array('js' => "{$module->getName()}.js")));
            }
        }

        list ($localjs, $externaljs) = self::getResponse()->getJavascripts();
        $webroot = self::getWebroot();

        $values = compact('content', 'localjs', 'externaljs', 'webroot');
        return $values;
    }

    /**
     * Performs an action.
     *
     * @param $action
     * @param string $module Name of the action module
     * @param string $method Name of the action method to run
     *
     * @return bool
     * @throws \Exception
     */
    public static function performAction($action, $module, $method)
    {
        // Set content variable
        $content = null;

        // Set the template to be used when rendering the html (or other) output
        $templateBasePath = (self::isInternalModule($module)) ? THEBUGGENIE_INTERNAL_MODULES_PATH : THEBUGGENIE_MODULES_PATH;
        $templatePath = $templateBasePath . $module . DS . 'templates' . DS;

        $actionClassName = get_class($action);
        $actionToRunName = 'run' . ucfirst($method);
        $preActionToRunName = 'pre' . ucfirst($method);

        // Set up the response object, responsible for controlling any output
        self::getResponse()->setPage(self::getRouting()->getCurrentRouteName());
        self::getResponse()->setTemplate(mb_strtolower($method) . '.' . self::getRequest()->getRequestedFormat() . '.php');
        self::getResponse()->setupResponseContentType(self::getRequest()->getRequestedFormat());
        self::setCurrentProject(null);

        // Run the specified action method set if it exists
        if (method_exists($action, $actionToRunName))
        {
            // Turning on output buffering
            ob_start('mb_output_handler');
            ob_implicit_flush(0);

            if (self::getRouting()->isCurrentRouteCSRFenabled())
            {
                // If the csrf check fails, don't proceed
                if (!self::checkCSRFtoken())
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
                Logging::log('Running main pre-execute action');
                // Running any overridden preExecute() method defined for that module
                // or the default empty one provided by \thebuggenie\core\framework\Action
                if ($pre_action_retval = $action->preExecute(self::getRequest(), $method))
                {
                    $content = ob_get_clean();
                    Logging::log('preexecute method returned something, skipping further action');
                    if (self::$_debug_mode)
                        $visited_templatename = "{$actionClassName}::preExecute()";
                }
            }

            if ($content === null)
            {
                $action_retval = null;
                if (self::getResponse()->getHttpStatus() == 200)
                {
                    // Checking for and running action-specific preExecute() function if
                    // it exists
                    if (method_exists($action, $preActionToRunName))
                    {
                        Logging::log('Running custom pre-execute action');
                        $action->$preActionToRunName(self::getRequest(), $method);
                    }

                    // Running main route action
                    Logging::log('Running route action ' . $actionToRunName . '()');
                    if (self::$_debug_mode)
                    {
                        $time = explode(' ', microtime());
                        $action_pretime = $time[1] + $time[0];
                    }
                    $action_retval = $action->$actionToRunName(self::getRequest());
                    if (self::$_debug_mode)
                    {
                        $time = explode(' ', microtime());
                        $action_posttime = $time[1] + $time[0];
                        self::visitPartial("{$actionClassName}::{$actionToRunName}()", $action_posttime - $action_pretime);
                    }
                }
                if (self::getResponse()->getHttpStatus() == 200 && $action_retval)
                {
                    // If the action returns *any* output, we're done, and collect the
                    // output to a variable to be outputted in context later
                    $content = ob_get_clean();
                    Logging::log('...done');
                }
                elseif (!$action_retval)
                {
                    // If the action doesn't return any output (which it usually doesn't)
                    // we continue on to rendering the template file for that specific action
                    Logging::log('...done');
                    Logging::log('Displaying template');

                    // Check to see if we have a translated version of the template
                    if ($method == 'notFound' && $module == 'main')
                    {
                        $templateName = $templatePath . self::getResponse()->getTemplate();
                    }
                    elseif (!self::isReadySetup() || ($templateName = self::getI18n()->hasTranslatedTemplate(self::getResponse()->getTemplate())) === false)
                    {
                        // Check to see if any modules provide an alternate template
                        $event = Event::createNew('core', "self::performAction::renderTemplate")->triggerUntilProcessed(array('class' => $actionClassName, 'action' => $actionToRunName));
                        if ($event->isProcessed())
                        {
                            $templateName = $event->getReturnValue();
                        }

                        // Check to see if the template has been changed, and whether it's in a
                        // different module, specified by "module/templatename"
                        if (mb_strpos(self::getResponse()->getTemplate(), '/'))
                        {
                            $newPath = explode('/', self::getResponse()->getTemplate());
                            $templateName = (self::isInternalModule($newPath[0])) ? THEBUGGENIE_INTERNAL_MODULES_PATH : THEBUGGENIE_MODULES_PATH;
                            $templateName .= $newPath[0] . DS . 'templates' . DS . $newPath[1] . '.' . self::getRequest()->getRequestedFormat() . '.php';
                        }
                        else
                        {
                            $templateName = $templatePath . self::getResponse()->getTemplate();
                        }
                    }

                    // Check to see if the template exists and throw an exception otherwise
                    if (!isset($templateName) || !file_exists($templateName))
                    {
                        Logging::log('The template file for the ' . $method . ' action ("' . self::getResponse()->getTemplate() . '") does not exist', 'core', Logging::LEVEL_FATAL);
                        Logging::log('Trying to load file "' . $templateName . '"', 'core', Logging::LEVEL_FATAL);
                        throw new exceptions\TemplateNotFoundException('The template file for the ' . $method . ' action ("' . self::getResponse()->getTemplate() . '") does not exist');
                    }

                    self::loadLibrary('common');
                    // Present template for current action
                    ActionComponent::presentTemplate($templateName, $action->getParameterHolder());
                    $content = ob_get_clean();
                    Logging::log('...completed');
                }
            }
            elseif (self::$_debug_mode)
            {
                $time = explode(' ', microtime());
                $posttime = $time[1] + $time[0];
                self::visitPartial($visited_templatename, $posttime - $pretime);
            }

            Logging::log('rendering final content');

            // Set core layout path
            self::getResponse()->setLayoutPath(THEBUGGENIE_CORE_PATH . 'templates');

            // Trigger event for rendering (so layout path can be overwritten)
            \thebuggenie\core\framework\Event::createNew('core', '\thebuggenie\core\framework\Context::renderBegins')->trigger();

            if (Settings::isMaintenanceModeEnabled() && !mb_strstr(self::getRouting()->getCurrentRouteName(), 'configure'))
            {
                if (!file_exists(self::getResponse()->getLayoutPath() . DS . 'offline.inc.php'))
                {
                    throw new exceptions\TemplateNotFoundException('Can not find offline mode template');
                }
                ob_start('mb_output_handler');
                ob_implicit_flush(0);
                ActionComponent::presentTemplate(self::getResponse()->getLayoutPath() . DS . 'offline.inc.php');
                $content = ob_get_clean();
            }

            // Render output in correct order
            self::getResponse()->renderHeaders();

            if (self::getResponse()->getDecoration() == Response::DECORATE_DEFAULT && !self::getRequest()->isAjaxCall())
            {
                if (!file_exists(self::getResponse()->getLayoutPath() . DS . 'layout.php'))
                {
                    throw new exceptions\TemplateNotFoundException('Can not find layout template');
                }
                ob_start('mb_output_handler');
                ob_implicit_flush(0);
                $layoutproperties = self::setupLayoutProperties($content);
                ActionComponent::presentTemplate(self::getResponse()->getLayoutPath() . DS . 'layout.php', $layoutproperties);
                ob_flush();
            }
            else
            {
                // Render header template if any, and store the output in a variable
                if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateHeader())
                {
                    Logging::log('decorating with header');
                    if (!file_exists(self::getResponse()->getHeaderDecoration()))
                    {
                        throw new exceptions\TemplateNotFoundException('Can not find header decoration: ' . self::getResponse()->getHeaderDecoration());
                    }
                    ActionComponent::presentTemplate(self::getResponse()->getHeaderDecoration());
                }

                echo $content;

                // Trigger event for ending the rendering
                \thebuggenie\core\framework\Event::createNew('core', '\thebuggenie\core\framework\Context::renderEnds')->trigger();

                Logging::log('...done (rendering content)');

                // Render footer template if any
                if (!self::getRequest()->isAjaxCall() && self::getResponse()->doDecorateFooter())
                {
                    Logging::log('decorating with footer');
                    if (!file_exists(self::getResponse()->getFooterDecoration()))
                    {
                        throw new exceptions\TemplateNotFoundException('Can not find footer decoration: ' . self::getResponse()->getFooterDecoration());
                    }
                    ActionComponent::presentTemplate(self::getResponse()->getFooterDecoration());
                }

                Logging::log('...done');
            }
            Logging::log('done (rendering final content)');

            return true;
        }
        else
        {
            Logging::log("Cannot find the method {$actionToRunName}() in class {$actionClassName}.", 'core', Logging::LEVEL_FATAL);
            throw new exceptions\ActionNotFoundException("Cannot find the method {$actionToRunName}() in class {$actionClassName}. Make sure the method exists.");
        }
    }

    /**
     * Launches the MVC framework
     */
    public static function go()
    {
        Logging::log('Dispatching');
        try {
            if (($route = self::getRouting()->getRouteFromUrl(self::getRequest()->getParameter('url', null, false))) || self::isInstallmode()) {

                if (self::isUpgrademode()) {
                    $route = array('module' => 'installation', 'action' => 'upgrade');
                } elseif (self::isInstallmode()) {
                    $route = array('module' => 'installation', 'action' => 'installIntro');
                }

                if (!self::isInternalModule($route['module'])) {
                    if (is_dir(THEBUGGENIE_MODULES_PATH . $route['module'])) {
                        if (!file_exists(THEBUGGENIE_MODULES_PATH . $route['module'] . DS . 'controllers' . DS . 'Main.php')) {
                            throw new \thebuggenie\core\framework\exceptions\ActionNotFoundException(
                                'The `' . $route['module'] . '` module is missing a `/controllers/Main.php` controller, containing the module its initial actions.'
                            );
                        }
                    } else {
                        throw new \Exception('Cannot load the ' . $route['module'] . ' module');
                    }
                    $actionClassBase = "\\thebuggenie\\modules\\".$route['module'].'\\controllers\\';
                } else {
                    $actionClassBase = "\\thebuggenie\\core\\modules\\".$route['module'].'\\controllers\\';
                }

                /**
                 * Set up the action object by identifying the Controller from the action. The following actions can
                 * be resolved by the Framework:
                 *
                 *  actionName          => /controllers/Main.php::runActionName()
                 *  ::actionName        => /controllers/Main.php::runActionName()
                 *  Other::actionName   => /controllers/Other.php::runActionName()
                 *
                 **/

                // If a separate controller is defined within the action name
                if (strpos($route['action'], '::')) {
                    $routing = explode('::', $route['action']);

                    $moduleController = $actionClassBase . $routing[0];
                    $moduleMethod = $routing[1];

                    if (class_exists($moduleController) && is_callable($moduleController, 'run'.ucfirst($moduleMethod))) {
                        $actionObject = new $moduleController();
                    } else {
                        throw new \Exception('The `' . $route['action'] . '` controller action is not callable');
                    }
                } else {
                    $actionClassName = $actionClassBase . 'Main';
                    $actionObject = new $actionClassName();
                    $moduleMethod = $route['action'];
                }
                $moduleName = $route['module'];
            } else {
                // Default
                $actionObject = new \thebuggenie\core\modules\main\controllers\Common();
                $moduleName = 'main';
                $moduleMethod = 'notFound';
            }

            self::$_action = $actionObject;

            if (!self::isInstallmode())
                self::initializeUser();

            self::setupI18n();

            if (self::$_redirect_login == 'login') {

                Logging::log('An error occurred setting up the user object, redirecting to login', 'main', Logging::LEVEL_NOTICE);
                if (self::getRouting()->getCurrentRouteName() != 'login')
                    self::setMessage('login_message_err', self::geti18n()->__('Please log in'));
                self::getResponse()->headerRedirect(self::getRouting()->generate('login_page'), 403);
            }

            if (self::$_redirect_login == 'elevated_login') {
                Logging::log('Elevated permissions required', 'main', Logging::LEVEL_NOTICE);
                if (self::getRouting()->getCurrentRouteName() != 'elevated_login')
                    self::setMessage('elevated_login_message_err', self::geti18n()->__('Please re-enter your password to continue'));
                $actionObject = new \thebuggenie\core\modules\main\controllers\Main();
                $moduleName = 'main';
                $moduleMethod = 'elevatedLogin';
            }

            if (self::performAction($actionObject, $moduleName, $moduleMethod)) {
                if (self::isDebugMode())
                    self::generateDebugInfo();
                if (\b2db\Core::isInitialized())
                {
                    \b2db\Core::closeDBLink();
                }
                return true;
            }

        } catch (\thebuggenie\core\framework\exceptions\TemplateNotFoundException $e) {
            \b2db\Core::closeDBLink();
            //header("HTTP/1.0 404 Not Found", true, 404);
            throw $e;

        } catch (\thebuggenie\core\framework\exceptions\ActionNotFoundException $e) {
            \b2db\Core::closeDBLink();
            header("HTTP/1.0 404 Not Found", true, 404);
            throw $e;

        } catch (\thebuggenie\core\framework\exceptions\CSRFFailureException $e) {
            \b2db\Core::closeDBLink();
            if (self::isDebugMode())
                self::generateDebugInfo();
            self::getResponse()->setHttpStatus(301);
            $message = $e->getMessage();

            if (self::getRequest()->getRequestedFormat() == 'json')
            {
                self::getResponse()->setContentType('application/json');
                $message = json_encode(array('message' => $message));
            }

            self::getResponse()->renderHeaders();
            echo $message;

        } catch (\Exception $e) {
            \b2db\Core::closeDBLink();
            //header("HTTP/1.0 404 Not Found", true, 404);
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
            $tbg_summary['db']['objectpopulation'] = \b2db\Core::getObjectPopulationHits();
            $tbg_summary['db']['objecttiming'] = \b2db\Core::getObjectPopulationTiming();
            $tbg_summary['db']['objectcount'] = \b2db\Core::getObjectPopulationCount();
        }
        $tbg_summary['load_time'] = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
        $tbg_summary['scope'] = array();
        $scope = self::getScope();
        $tbg_summary['scope']['id'] = $scope instanceof Scope ? $scope->getID() : 'unknown';
        $tbg_summary['scope']['hostnames'] = ($scope instanceof Scope && \b2db\Core::isConnected()) ? implode(', ', $scope->getHostnames()) : 'unknown';
        $tbg_summary['settings'] = Settings::getAll();
        $tbg_summary['memory'] = memory_get_usage();
        $tbg_summary['partials'] = self::getVisitedPartials();
        $tbg_summary['log'] = Logging::getEntries();
        $tbg_summary['routing'] = array('name' => self::getRouting()->getCurrentRouteName(), 'module' => self::getRouting()->getCurrentRouteModule(), 'action' => self::getRouting()->getCurrentRouteAction());
        if (isset($_SESSION))
        {
            if (!array_key_exists('___DEBUGINFO___', $_SESSION))
            {
                $_SESSION['___DEBUGINFO___'] = array();
            }
            $_SESSION['___DEBUGINFO___'][self::$debug_id] = $tbg_summary;
            while (count($_SESSION['___DEBUGINFO___']) > 25)
                array_shift($_SESSION['___DEBUGINFO___']);
        }
    }

    public static function getDebugData($debug_id)
    {
        if (!array_key_exists('___DEBUGINFO___', $_SESSION))
            return null;
        if (!array_key_exists($debug_id, $_SESSION['___DEBUGINFO___']))
            return null;

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
        if (extension_loaded('posix'))
        {
            // Original code
            $processUser = posix_getpwuid(posix_geteuid());
            return $processUser['name'];
        }
        else
        {
            // Try to get CLI process owner without the POSIX extension
            $environmentUser = getenv('USERNAME');
            if ($environmentUser === false)
            {
                $environmentUser = 'Unknown';
            }
            return $environmentUser;
        }
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
