<?php

namespace thebuggenie\core\framework;

use thebuggenie\core\entities\CustomDatatype;
use thebuggenie\core\entities\Datatype;
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

    const INTERNAL_MODULES = 'internal_modules';
    const EXTERNAL_MODULES = 'external_modules';

    protected static $_environment = 2;
    protected static $_debug_mode = true;
    protected static $debug_id = null;
    protected static $_configuration = null;
    protected static $_session_initialization_time = null;
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
     * @var Module[]
     */
    protected static $_modules = array();

    /**
     * List of internal modules
     *
     * @var CoreModule[]
     */
    protected static $_internal_modules = array();

    /**
     * List of internal module paths
     *
     * @var string[]
     */
    protected static $_internal_module_paths = array();

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
     * List of available permission paths
     *
     * @var array
     */
    protected static $_available_permission_paths = null;

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
     * Information about the latest available version. Should be null
     * in case the information has not been fetched (or fetching
     * failed), or an array with keys: maj, min, rev, nicever.
     *
     */
    protected static $_latest_available_version = null;

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
        // Do not run the handler for suppressed errors. Normally this should be
        // only commands where supression is done via the @ operator.
        if (error_reporting() === 0)
        {
            return false;
        }

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

    public static function getSessionLoadTime()
    {
        return self::$_session_initialization_time;
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
            if ($current_version != Settings::getVersion(false, true))
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

        $starttime = explode(' ', microtime());
        $before = $starttime[1] + $starttime[0];
        session_name(THEBUGGENIE_SESSION_NAME);
        session_start();

        $endtime = explode(' ', microtime());
        $after = $endtime[1] + $endtime[0];
        self::$_session_initialization_time = round(($after - $before), 5);

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
            self::$debug_id = uniqid();

            // The time the script was loaded
            $starttime = explode(' ', microtime());
            define('NOW', (integer) $starttime[1]);

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
        if (true || !self::isCLI())
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
                    self::loadUser(null);
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

    /**
     * @return interfaces\ModuleInterface[][]
     */
    public static function getAllModules()
    {
        return [
            self::INTERNAL_MODULES => self::$_internal_modules,
            self::EXTERNAL_MODULES => self::getModules()
        ];
    }

    protected static function loadRoutes()
    {
        Logging::log('Loading routes from routing files', 'routing');

        foreach (self::getAllModules() as $modules)
        {
            foreach ($modules as $module_name => $module)
            {
                self::getRouting()->loadRoutes($module_name);
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
        
        $log_file = (isset(self::$_configuration['core']['log_file'])) ? self::$_configuration['core']['log_file'] : null;
        if($log_file)
        {
            Logging::setLogFilePath($log_file);
            Logging::log('Log file path set. At this point, configuration is loaded & caching enabled, if possible.', 'core');
        }
        Logging::log('Done Loading Configuration', 'core');
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
        $parser = new TextParserMarkdown();

        while ($theme = readdir($theme_path_handle))
        {
            if ($theme != '.' && $theme != '..' && is_dir(THEBUGGENIE_PATH . 'themes' . DS . $theme) && file_exists(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'theme.php'))
            {
                $themes[$theme] = array(
                    'key' => $theme,
                    'name' => ucfirst($theme),
                    'version' => file_get_contents(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'VERSION'),
                    'author' => file_get_contents(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'AUTHOR'),
                    'description' => $parser->transform(file_get_contents(THEBUGGENIE_PATH . 'themes' . DS . $theme . DS . 'README.md'))
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
            self::$_user = ($user === null) ? User::identify(self::getRequest(), self::getCurrentAction(), true) : $user;
            if (self::$_user->isAuthenticated())
            {
                if (!self::getRequest()->hasCookie('original_username'))
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
        if (self::getUser() instanceof User && $user->getID() == self::getUser()->getID()) {
            return;
        }

        self::setUser($user);
        Settings::forceSettingsReload();
        self::reloadPermissionsCache();
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

                self::$_internal_module_paths[$modulename] = $modulename;
            }

            self::getCache()->add(Cache::KEY_INTERNAL_MODULES, $modules, false);
        }
        else
        {
            Logging::log('Loading cached modules');
            self::$_internal_module_paths = $modules;
        }

        foreach (self::$_internal_module_paths as $modulename)
        {
            $classname = "\\thebuggenie\\core\\modules\\{$modulename}\\" . ucfirst($modulename);
            self::$_internal_modules[$modulename] = new $classname($modulename);
            self::$_internal_modules[$modulename]->initialize();
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
     * @param Module $module
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
     * @return Module[]
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
     * @return Module[]
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
     * @return Module
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
        $query = Permissions::getTable()->getQuery();
        $query->where(Permissions::SCOPE, self::getScope()->getID());
        $query->where(Permissions::PERMISSION_TYPE, $type);

        if (($uid + $tid + $gid) == 0 && !$all)
        {
            $query->where(Permissions::UID, $uid);
            $query->where(Permissions::TID, $tid);
            $query->where(Permissions::GID, $gid);
        }
        else
        {
            switch (true)
            {
                case ($uid != 0):
                    $query->where(Permissions::UID, $uid);
                case ($tid != 0):
                    $query->where(Permissions::TID, $tid);
                case ($gid != 0):
                    $query->where(Permissions::GID, $gid);
            }
        }
        if ($target_id !== null)
        {
            $query->where(Permissions::TARGET_ID, $target_id);
        }

        $permissions = array();

        if ($res = Permissions::getTable()->rawSelect($query))
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

    public static function reloadPermissionsCache()
    {
        self::$_available_permission_paths = null;
        self::$_available_permissions = null;

        self::_cacheAvailablePermissions();
        self::cacheAllPermissions();
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

    /**
     * Calculates weight of a specific permission. Permission weight is a
     * non-negative integer value denoting what priority permission should take
     * when being applied if multiple matching permissions are found.
     *
     * In other words, if user requests access to a resource, and there are
     * multiple permissions that would grant or deny access to the user for this
     * resource, permission weight can be used to determine which permission
     * applies.
     *
     * Permission weight algorithm takes into account the following:
     *
     * - How specific is the resource associated with the permission. I.e. if
     *   permission is specified against a specific target ID, it should get
     *   higher priority than the one specified against any (all) target IDs.
     * - How specific is the designator that matches against the user. User can
     *   be matched through user ID (most specific), team ID, group ID, or "any
     *   user" specifier. The weights are set in the same order (so, uid > tid >
     *   gid > any user).
     * - What is the permission rule result - i.e. does it allow or deny
     *   access. Denying access has priority over granting access.
     *
     * The weight of the above three items is also proportional to each other,
     * that is the specificity of target ID brings more weight than specific
     * uid/gid/tid, which in turns weights more than specific rule result
     * (allowed/denied).
     *
     * @param permission array An array defining permission. Must include the following keys: uid (user ID), gid (group ID), tid (team ID), and allowed (true/false).
     * @param target_id mixed Either a non-negative integer or string designating target to which the permission applies. 0 means global target.
     *
     * @return integer A non-negative integer denoting weight of permission.
     */
    protected static function _getPermissionWeight($permission, $target_id)
    {
        // The following array contains values used for figuring out permission
        // weight based on criteria of specificity. Have a look at method
        // description for logic behind it.
        $weight_bases = array(
                              'specific_target_id' => 1000,
                              'specific_uid'       =>  750,
                              'specific_tid'       =>  500,
                              'specific_gid'       =>  250,
                              'allow_false'        =>   50,
                              'allow_true'         =>    0,
                              );

        // Assume least weight initially.
        $weight = 0;

        // Add weight based on target ID specificity.
        if ($target_id != 0)
        {
            $weight += $weight_bases['specific_target_id'];
        }

        // Apply weight based on user matching specificity.
        if ($permission['uid'] != 0)
        {
            $weight += $weight_bases['specific_uid'];
        }
        else if ($permission['tid'] != 0)
        {
            $weight += $weight_bases['specific_tid'];
        }
        else if ($permission['gid'] != 0)
        {
            $weight += $weight_bases['specific_gid'];
        }

        // Add weight based on result specificity.
        if ($permission['allowed'] === false)
        {
            $weight += $weight_bases['allow_false'];
        }
        else if ($permission['allowed'] === true)
        {
            $weight += $weight_bases['allow_true'];
        }

        return $weight;
    }

    /**
     * Checks if users that can be matched against provided user ID, group
     * membership, or team membership should be granted access to specified
     * resource.
     *
     * @see User::hasPermission() For description of module name, permission type, target ID.
     *
     * @param string module_name Name of the module associated with permission type.
     * @param string permission_type Permission type.
     * @param mixed target_id Target (object) ID, if applicable. If not applicable, set to 0. Should be either non-negative integer or string.
     * @param integer uid User ID for matching the users. Set to 0 if it should not be used for matching, or if $uid, $gid and $team_ids are all 0/empty, match any user.
     * @param integer gid Group ID for matching the users. Set to 0 if it should not be used for matching, or if $uid, $gid and $team_ids are all 0/empty, match any user.
     * @param array team_ids List of team IDs for matching the users. Set to empty array if it should not be used for matching, or if $uid, $gid and $team_ids are all 0/empty, match any user.
     *
     * @return mixed If permission matching the specified criteria has been found in database (cache, to be more precise), returns permission value (true or false). If no matching permission has been found, returns null. Receiving null means the caller needs to apply a default rule (allow or deny), which depends on caller implementation.
     */
    public static function checkPermission($module_name, $permission_type, $target_id, $uid, $gid, $team_ids)
    {
        // Default is that no permission was found/matched against user
        // specifier.
        $result = null;

        // Check if there are any permission rules stored for given module and permission type.
        if (array_key_exists($module_name, self::$_permissions) &&
            array_key_exists($permission_type, self::$_permissions[$module_name]))
        {
            // Permissions relevant to module + permission type are stored in an
            // array, grouped based on whether they are applied against specific
            // target ID or globally.
            $permission_groups = array();

            // Since we could have multiple matches, we need to keep track of
            // what permission has the most weight.
            $permission_candidate_weight = -1;

            // Populate permission groups with permissions specific to provided
            // target IDs and global permissions. Use target_id as index since
            // we need to pass it in for weight calculation.
            if (($target_id != 0 || is_string($target_id)) && array_key_exists($target_id, self::$_permissions[$module_name][$permission_type]))
            {
                $permission_groups[$target_id] = self::$_permissions[$module_name][$permission_type][$target_id];
            }

            if (array_key_exists(0, self::$_permissions[$module_name][$permission_type]))
            {
                $permission_groups[0] = self::$_permissions[$module_name][$permission_type][0];
            }

            foreach ($permission_groups as $permission_group_target_id => $permission_group)
            {
                foreach ($permission_group as $permission)
                {
                    // Permission is applicable if we can match it against the
                    // user specifier (uid, gid, or one of team IDs), or if the
                    // permission should be applied to all users.
                    if (($uid != 0 && $uid == $permission['uid']) ||
                        (count($team_ids) != 0 && in_array($permission['tid'], $team_ids)) ||
                        ($gid !=0 && $gid == $permission['gid'])  ||
                        ($permission['uid'] == 0 && $permission['gid'] == 0 && $permission['tid'] == 0))
                    {
                        // Calculate the permissions weight, and apply its
                        // result (allow/deny) if it outweighs the previously
                        // matched permission.
                        $permission_weight = self::_getPermissionWeight($permission, $permission_group_target_id);
                        if ($permission_weight > $permission_candidate_weight)
                        {
                            $permission_candidate_weight = $permission_weight;
                            $result = $permission['allowed'];
                        }
                    }
                }
            }
        }

        // Return the result (true/false/null).
        return $result;
    }

    public static function getLoadedPermissions()
    {
        return self::$_permissions;
    }

    public static function getPermissionDetails($permission, $permissions_list = null, $module_name = null)
    {
        self::_cacheAvailablePermissions();
        if ($module_name === null) {
            $permissions_list = ($permissions_list === null) ? self::$_available_permissions : $permissions_list;
        } else {
            $permissions_list = ($permissions_list === null) ? self::getModule($module_name)->getAvailablePermissions() : $permissions_list;
        }
        foreach ($permissions_list as $permission_key => $permission_info)
        {
            if (is_numeric($permission_key))
                return null;
            if ($permission_key == $permission)
                return $permission_info;

            if (in_array($permission_key, array_keys(self::$_available_permissions)) || (array_key_exists('details', $permission_info) && is_array($permission_info['details']) && count($permission_info['details'])))
            {
                $p_info = (in_array($permission_key, array_keys(self::$_available_permissions))) ? $permission_info : $permission_info['details'];
                $retval = self::getPermissionDetails($permission, $p_info, $module_name);
                if ($retval)
                    return $retval;
            }
        }
    }

    protected static function _cacheAvailablePermissions()
    {
        if (self::$_available_permissions === null)
        {
            Logging::log("Loading and caching permissions tree");
            $i18n = self::getI18n();
            self::$_available_permissions = array('user' => array(), 'general' => array(), 'project' => array());

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
            self::$_available_permissions['project']['canseeallissues'] = array('description' => $i18n->__('Can see issues reported by other users'), 'mode' => 'permissive');
            self::$_available_permissions['project']['canseeproject'] = array('description' => $i18n->__('Has access to the project'), 'details' => array());
            self::$_available_permissions['project']['canseeproject']['details']['canseeprojecthierarchy'] = array('description' => $i18n->__('Can see complete project hierarchy'));
            self::$_available_permissions['project']['canseeproject']['details']['canseeprojecthierarchy']['details']['canseeallprojecteditions'] = array('description' => $i18n->__('Can see all editions'));
            self::$_available_permissions['project']['canseeproject']['details']['canseeprojecthierarchy']['details']['canseeallprojectcomponents'] = array('description' => $i18n->__('Can see all components'));
            self::$_available_permissions['project']['canseeproject']['details']['canseeprojecthierarchy']['details']['canseeallprojectbuilds'] = array('description' => $i18n->__('Can see all releases'));
            self::$_available_permissions['project']['canseeproject']['details']['canseeprojecthierarchy']['details']['canseeallprojectmilestones'] = array('description' => $i18n->__('Can see all milestones'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access'] = array('description' => $i18n->__('Can access all project pages'), 'details' => array());
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_dashboard_access'] = array('description' => $i18n->__('Can access the project dashboard'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_planning_access'] = array('description' => $i18n->__('Can access the project agile pages without planning page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_only_planning_access'] = array('description' => $i18n->__('Can access the project planning pages'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_scrum_access'] = array('description' => $i18n->__('Can access the project scrum page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_issues_access'] = array('description' => $i18n->__('Can access the project issues search page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_roadmap_access'] = array('description' => $i18n->__('Can access the project roadmap page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_team_access'] = array('description' => $i18n->__('Can access the project team page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_statistics_access'] = array('description' => $i18n->__('Can access the project statistics page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_timeline_access'] = array('description' => $i18n->__('Can access the project timeline page'));
            self::$_available_permissions['project']['canseeproject']['details']['page_project_allpages_access']['details']['page_project_commits_access'] = array('description' => $i18n->__('Can access the project commits page'));
            self::$_available_permissions['project']['canseeproject']['details']['canseetimespent'] = array('description' => $i18n->__('Can see time spent on issues'));
            self::$_available_permissions['project']['canmanageproject'] = array('description' => $i18n->__('Can manage the project'));
            self::$_available_permissions['project']['canmanageproject']['details']['caneditprojectdetails'] = array('description' => $i18n->__('Can edit project details and settings'));
            self::$_available_permissions['project']['canmanageproject']['details']['canaddscrumsprints'] = array('description' => $i18n->__('Can manage milestones and/or sprints'));
            self::$_available_permissions['project']['canmanageproject']['details']['canmanageprojectreleases'] = array('description' => $i18n->__('Can manage project releases, editions and components'));
            self::$_available_permissions['project']['cancreateissues'] = array('description' => $i18n->__('Can create new issues'));
            self::$_available_permissions['project']['canlockandeditlockedissues'] = array('description' => $i18n->__('Can change issue access policy'));
            self::$_available_permissions['edition']['canseeedition'] = array('description' => $i18n->__('Can see this edition'));
            self::$_available_permissions['component']['canseecomponent'] = array('description' => $i18n->__('Can see this component'));
            self::$_available_permissions['build']['canseebuild'] = array('description' => $i18n->__('Can see this release'));
            self::$_available_permissions['milestone']['canseemilestone'] = array('description' => $i18n->__('Can see this milestone'));

            $arr = [
                ''    => $i18n->__('For issues reported by anyone: edit any issue details, close and delete issues'),
                'own' => $i18n->__('For own issues only: edit any issue details, close and delete issues')
            ];
            foreach ($arr as $suffix => $description) {
                self::$_available_permissions['issues']['caneditissue'.$suffix] = array('description' => $description, 'details' => array());
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuebasic'.$suffix] = array('description' => $i18n->__('Can edit title, description and reproduction steps'), 'details' => array());
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuebasic'.$suffix]['details']['caneditissuetitle'.$suffix] = array('description' => $i18n->__('Can edit title'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuebasic'.$suffix]['details']['caneditissuedescription'.$suffix] = array('description' => $i18n->__('Can edit description'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuebasic'.$suffix]['details']['caneditissuereproduction_steps'.$suffix] = array('description' => $i18n->__('Can edit steps to reproduce'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canvoteforissues'.$suffix] = array('description' => $i18n->__('Can vote for issues'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueposted_by'.$suffix] = array('description' => $i18n->__('Can edit issue poster'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueowned_by'.$suffix] = array('description' => $i18n->__('Can edit owner'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueassigned_to'.$suffix] = array('description' => $i18n->__('Can edit assignee'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuestatus'.$suffix] = array('description' => $i18n->__('Can edit status'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuecategory'.$suffix] = array('description' => $i18n->__('Can edit category'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuepriority'.$suffix] = array('description' => $i18n->__('Can edit priority'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueseverity'.$suffix] = array('description' => $i18n->__('Can edit severity'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuereproducability'.$suffix] = array('description' => $i18n->__('Can edit reproducability'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueresolution'.$suffix] = array('description' => $i18n->__('Can edit resolution'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueestimated_time'.$suffix] = array('description' => $i18n->__('Can set estimate'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuespent_time'.$suffix] = array('description' => $i18n->__('Can spend time working on issues'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuepercent_complete'.$suffix] = array('description' => $i18n->__('Can edit percent complete'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuemilestone'.$suffix] = array('description' => $i18n->__('Can set milestone'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuecolor'.$suffix] = array('description' => $i18n->__('Can edit planning color'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissueuserpain'.$suffix] = array('description' => $i18n->__('Can edit user pain'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix] = array('description' => $i18n->__('Can add/remove extra information (edition, component, release, links and files) and link issues'), 'details' => array());
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix]['details']['canaddbuilds'.$suffix] = array('description' => $i18n->__('Can add and remove affected releases / versions'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix]['details']['canaddcomponents'.$suffix] = array('description' => $i18n->__('Can add and remove affected components'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix]['details']['canaddeditions'.$suffix] = array('description' => $i18n->__('Can add and remove affected editions'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix]['details']['canaddlinkstoissues'.$suffix] = array('description' => $i18n->__('Can add and remove links'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix]['details']['canaddfilestoissues'.$suffix] = array('description' => $i18n->__('Can add and remove attachments'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['canaddextrainformationtoissues'.$suffix]['details']['canaddrelatedissues'.$suffix] = array('description' => $i18n->__('Can add and remove related issues'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['cantransitionissue'.$suffix] = array('description' => $i18n->__('Can transition issue'));
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['candeleteissues'.$suffix] = array('description' => $i18n->__('Can delete issue'));
            }

            foreach ($arr as $suffix => $description) {
                self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuecustomfields'.$suffix] = [
                    'description' => $i18n->__('Can edit custom fields for issues'),
                    'details' => []
                ];
            }

            foreach (CustomDatatype::getAll() as $cdf) {
                foreach ($arr as $suffix => $description) {
                    self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['caneditissuecustomfields'.$suffix]['details']['caneditissuecustomfields' . $cdf->getKey() . $suffix] = array('description' => $i18n->__('Can change custom field "%field_name"', array('%field_name' => $i18n->__($cdf->getDescription()))));
                }

                // Set permissions for custom option types
                if ($cdf->hasCustomOptions()) {
                    $options = $cdf->getOptions();
                    foreach ($options as $option) {
                        foreach ($arr as $suffix => $description) {
                            self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['set_datatype_' . $option->getID().$suffix] = array('description' => $i18n->__('Can change issue field to "%option_name"', array('%option_name' => $i18n->__($option->getValue()))));
                        }
                    }
                }
            }

            foreach (Datatype::getTypes() as $type => $class) {
                foreach ($arr as $suffix => $description) {
                    self::$_available_permissions['issues']['caneditissue'.$suffix]['details']['set_datatype_' . $type . $suffix] = array('description' => $i18n->__('Can change field "%type_name"', array('%type_name' => $i18n->__($type))));
                }
            }

            self::$_available_permissions['issues']['canpostseeandeditallcomments'] = array('description' => $i18n->__('Can see all comments (including non-public), post new, edit and delete all comments'), 'details' => array());
            self::$_available_permissions['issues']['canpostseeandeditallcomments']['details']['canseenonpubliccomments'] = array('description' => $i18n->__('Can see all comments including hidden'));
            self::$_available_permissions['issues']['canpostseeandeditallcomments']['details']['caneditcomments'] = array('description' => $i18n->__('Can edit all comments'));
            self::$_available_permissions['issues']['canpostseeandeditallcomments']['details']['candeletecomments'] = array('description' => $i18n->__('Can delete any comments'));
            self::$_available_permissions['issues']['canpostandeditcomments'] = array('description' => $i18n->__('Can see public comments, post new, edit own and delete own comments'), 'details' => array());
            self::$_available_permissions['issues']['canpostandeditcomments']['details']['canviewcomments'] = array('description' => $i18n->__('Can see public comments'));
            self::$_available_permissions['issues']['canpostandeditcomments']['details']['canpostcomments'] = array('description' => $i18n->__('Can post comments'));
            self::$_available_permissions['issues']['canpostandeditcomments']['details']['caneditcommentsown'] = array('description' => $i18n->__('Can edit own comments'));
            self::$_available_permissions['issues']['canpostandeditcomments']['details']['candeletecommentsown'] = array('description' => $i18n->__('Can delete own comments'));

            foreach (self::$_available_permissions as $category => $permissions) {
                self::addPermissionsPath($permissions, $category);
            }

            Logging::log("Done loading and caching permissions tree");
        }
    }

    protected static function addPermissionsPath($permissions, $category, $parent = [])
    {
        foreach ($permissions as $permission => $details) {
            self::$_available_permission_paths[$category][$permission] = array_reverse(array_values($parent));
            if (array_key_exists('details', $details)) {
                $path = $parent;
                $path[$permission] = $permission;
                self::addPermissionsPath($details['details'], $category, $path);
            }
        }
    }

    public static function permissionCheck($module, $permission, $target_id, $uid, $gid, $team_ids)
    {
        $key = 'config';

        foreach (self::$_available_permission_paths as $permission_key => $permissions) {
            if ($permission_key == 'config')
                continue;

            if (array_key_exists($permission, $permissions)) {
                $key = $permission_key;
                break;
            }
        }

        if ($key != 'config') {
            foreach (self::$_available_permission_paths[$key][$permission] as $parent_permission) {
                $value = self::checkPermission($module, $parent_permission, $target_id, $uid, $gid, $team_ids);
                if ($value !== null) {
                    return $value;
                }
            }
        }

        return self::checkPermission($module, $permission, $target_id, $uid, $gid, $team_ids);
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
        $authentication_backend = Settings::getAuthenticationBackend();
        $authentication_backend->logout();

        Event::createNew('core', 'pre_logout')->trigger();
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
        $themepath = THEBUGGENIE_PATH . 'themes' . DS . $theme . DS;
        foreach (self::getModules() as $module)
        {
            $module_path = (self::isInternalModule($module->getName())) ? THEBUGGENIE_INTERNAL_MODULES_PATH : THEBUGGENIE_MODULES_PATH;
            $module_name = $module->getName();
            if (file_exists($module_path . $module_name . DS . 'public' . DS . 'css' . DS . "{$module_name}.css")) {
                self::getResponse()->addStylesheet(self::getRouting()->generate('asset_module_css', array('module_name' => $module_name, 'css' => "{$module_name}.css")));
            }
            if (file_exists($module_path . $module_name . DS . 'public' . DS . 'js' . DS . "{$module_name}.js")) {
                self::getResponse()->addJavascript(self::getRouting()->generate('asset_module_js', array('module_name' => $module_name, 'js' => "{$module_name}.js"), false));
                //self::getResponse()->addJavascript("module/{$module_name}/{$module_name}.js");
            }
            if (file_exists($themepath . 'css' . DS . "{$module_name}.css")) {
                self::getResponse()->addStylesheet(self::getRouting()->generate('asset_css', array('theme_name' => $theme, 'css' => "{$module_name}.css")));
            }
            if (file_exists($themepath . 'js' . DS . "theme.js")) {
                self::getResponse()->addJavascript(self::getRouting()->generate('asset_js', array('theme_name' => $theme, 'js' => "theme.js"), false));
            }
            if (file_exists($basepath . 'js' . DS . "{$module_name}.js")) {
                self::getResponse()->addJavascript(self::getRouting()->generate('asset_js_unthemed', array('js' => "{$module_name}.js")));
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
                    else
                    {
                        //session_write_close();
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

    public static function bootstrap()
    {
        // Set up error and exception handling
        set_exception_handler([self::class, 'exceptionHandler']);
        set_error_handler([self::class, 'errorHandler']);
        error_reporting(E_ALL | E_NOTICE | E_STRICT);

        if (PHP_VERSION_ID < 70100)
            die('This software requires PHP 7.1.0 or newer. Please upgrade to a newer version of php to use The Bug Genie.');

        gc_enable();
        date_default_timezone_set('UTC');

        if (!defined('THEBUGGENIE_PATH'))
            die('You must define the THEBUGGENIE_PATH constant so we can find the files we need');

        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
        defined('THEBUGGENIE_CORE_PATH') || define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DS);
        defined('THEBUGGENIE_VENDOR_PATH') || define('THEBUGGENIE_VENDOR_PATH', THEBUGGENIE_PATH . 'vendor' . DS);
        defined('THEBUGGENIE_CACHE_PATH') || define('THEBUGGENIE_CACHE_PATH', THEBUGGENIE_PATH . 'cache' . DS);
        defined('THEBUGGENIE_CONFIGURATION_PATH') || define('THEBUGGENIE_CONFIGURATION_PATH', THEBUGGENIE_CORE_PATH . 'config' . DS);
        defined('THEBUGGENIE_INTERNAL_MODULES_PATH') || define('THEBUGGENIE_INTERNAL_MODULES_PATH', THEBUGGENIE_CORE_PATH . 'modules' . DS);
        defined('THEBUGGENIE_MODULES_PATH') || define('THEBUGGENIE_MODULES_PATH', THEBUGGENIE_PATH . 'modules' . DS);
        defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', '');

        self::initialize();

        if (self::isCLI()) {
            self::setupI18n();

            // Available permissions cannot be cached during
            // installation because the scope is not set-up at that
            // point. Permissions also must be cached at this point,
            // and not together with self::initializeUser since i18n
            // system must be initialised beforehand.
            if (!self::isInstallmode())
                self::_cacheAvailablePermissions();
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

            // Available permissions cannot be cached during
            // installation because the scope is not set-up at that
            // point. Permissions also must be cached at this point,
            // and not together with self::initializeUser since i18n
            // system must be initialised beforehand.
            if (!self::isInstallmode())
                self::_cacheAvailablePermissions();

            if (self::$_redirect_login == 'login') {

                Logging::log('An error occurred setting up the user object, redirecting to login', 'main', Logging::LEVEL_NOTICE);
                if (self::getRouting()->getCurrentRouteName() != 'login')
                {
                    self::setMessage('login_message_err', self::geti18n()->__('Please log in'));
                    self::setMessage('login_referer', self::getRouting()->generate(self::getRouting()->getCurrentRouteName(), self::getRequest()->getParameters()));
                }
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
                if (self::isDebugMode()) {
                    self::generateDebugInfo();
                }

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

        } catch (\thebuggenie\core\framework\exceptions\ActionNotAllowedException $e) {
            $actionObject = new \thebuggenie\core\modules\main\controllers\Common();
            $actionObject['message'] = $e->getMessage();

            self::performAction($actionObject, 'main', 'forbidden');

        } catch (\thebuggenie\core\framework\exceptions\CSRFFailureException $e) {
            \b2db\Core::closeDBLink();
            if (self::isDebugMode()) {
                self::generateDebugInfo();
            }

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
        $session_time = self::$_session_initialization_time;
        if (\b2db\Core::isInitialized())
        {
            $tbg_summary['db']['queries'] = \b2db\Core::getSQLHits();
            $tbg_summary['db']['timing'] = \b2db\Core::getSQLTiming();
            $tbg_summary['db']['objectpopulation'] = \b2db\Core::getObjectPopulationHits();
            $tbg_summary['db']['objecttiming'] = \b2db\Core::getObjectPopulationTiming();
            $tbg_summary['db']['objectcount'] = \b2db\Core::getObjectPopulationCount();
        }
        $tbg_summary['load_time'] = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
        $tbg_summary['session_initialization_time'] = ($session_time >= 1) ? round($session_time, 2) . 's' : round($session_time * 1000, 1) . 'ms';
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
            $_SESSION['___DEBUGINFO___'][self::getDebugID()] = $tbg_summary;
            while (count($_SESSION['___DEBUGINFO___']) > 25) {
                array_shift($_SESSION['___DEBUGINFO___']);
            }
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

    /**
     * Whether to serve minified asset files (JS and CSS)
     *
     * @return bool
     *   true, if asset files shall be delivered minified, false otherwise.
     */
    public static function isMinifiedAssets()
    {
        return ! empty(self::$_configuration['core']['minified_assets']);
    }

    /**
     * Retrieves information about the latest available version from
     * TBG website.
     *
     *
     * @return array
     *
     *   null, if latest available version information could not be
     *   retrieved due to errors, otherwise an array describing the
     *   latest available version with the following keys:
     *
     *   maj
     *     Major version number.
     *
     *   min
     *     Minor version number.
     *
     *   rev
     *     Revision version number.
     *
     *   nicever
     *     Formatted version string suitable for showing to user.
     */
    public static function getLatestAvailableVersionInformation()
    {
        // Use cached information if available.
        if (self::$_latest_available_version !== null)
        {
            return self::$_latest_available_version;
        }

        // Set-up client and retrieve version information.
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://thebuggenie.com/',
            'http_errors' => false]);
        $response = $client->request('GET', '/updatecheck.php');

        // Verify status code.
        if ($response->getStatusCode() == 200)
        {
            // Decode response.
            $info = json_decode($response->getBody());

            // Cache value if response was decoded and necessary
            // information was read from it.
            if (is_object($info) && isset($info->maj, $info->min, $info->rev, $info->nicever))
            {
                self::$_latest_available_version = $info;
            }
        }

        return self::$_latest_available_version;
    }

    /**
     * Checks if an update is available based on passed-in version
     * information.
     *
     * @param array version Version information. Should contain keys: maj (major version number),
     *                      min (minor version number), rev (revision number),
     *                      nicever (formatted version string that can be shown to user).
     *
     * @return bool
     *   true, if an update is available, false otherwise.
     */
    public static function isUpdateAvailable($version)
    {
        $update_available = false;

        // Check if we are out of date.
        if ($version->maj > Settings::getMajorVer())
        {
            $update_available = true;
        }
        elseif ($version->min > Settings::getMinorVer() && ($version->maj == Settings::getMajorVer()))
        {
            $update_available = true;
        }
        elseif ($version->rev > Settings::getRevision() && ($version->maj == Settings::getMajorVer()) && ($version->min == Settings::getMinorVer()))
        {
            $update_available = true;
        }

        return $update_available;
    }

}
