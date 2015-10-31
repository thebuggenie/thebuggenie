<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\entities\tables\Modules;
    use thebuggenie\core\entities\tables\Settings;
    use thebuggenie\core\framework;

    /**
     * Module class, extended by all thebuggenie modules
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Module class, extended by all thebuggenie modules
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Modules")
     */
    abstract class Module extends IdentifiableScoped
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
            $scope_id = ($scope) ? $scope->getID() : framework\Context::getScope()->getID();
            if (!framework\Context::getScope() instanceof \thebuggenie\core\entities\Scope) throw new \Exception('No scope??');

            framework\Logging::log('installing module ' . $module_name);
            $transaction = \b2db\Core::startTransaction();
            try
            {
                $module = tables\Modules::getTable()->installModule($module_name, $scope_id);
                $module->install($scope_id);
                $transaction->commitAndEnd();
            }
            catch (\Exception $e)
            {
                $transaction->rollback();
                throw $e;
            }
            framework\Logging::log('done (installing module ' . $module_name . ')');

            return $module;
        }

        public static function unloadModule($module_key)
        {
            $module = framework\Context::getModule($module_key);
            $module->disable();
            unset($module);
            framework\Context::unloadModule($module_key);
        }

        protected function _addListeners() { }

        abstract protected function _initialize();

        protected function _install($scope) { }

        protected function _uninstall() { }

        protected function _upgrade() { }

        /**
         * Class constructor
         */
        final public function _construct(\b2db\Row $row, $foreign_key = null)
        {
            if ($this->_version != $row->get(tables\Modules::VERSION))
            {
                throw new \Exception('This module must be upgraded to the latest version');
            }
        }

        protected function _loadFixtures($scope) { }

        final public function install($scope)
        {
            try
            {
                framework\Context::clearRoutingCache();
                framework\Context::clearPermissionsCache();
                $this->_install($scope);
                $b2db_classpath = THEBUGGENIE_MODULES_PATH . $this->_name . DS . 'entities' . DS . 'tables';

                if ($scope == framework\Settings::getDefaultScopeID() && is_dir($b2db_classpath))
                {
                    $b2db_classpath_handle = opendir($b2db_classpath);
                    while ($table_class_file = readdir($b2db_classpath_handle))
                    {
                        if (($tablename = mb_substr($table_class_file, 0, mb_strpos($table_class_file, '.'))) != '')
                        {
                            \b2db\Core::getTable("\\thebuggenie\\modules\\".$this->_name."\\entities\\tables\\".$tablename)->create();
                        }
                    }
                }
                $this->_loadFixtures($scope);
            }
            catch (\Exception $e)
            {
                throw $e;
            }
        }

        public function log($message, $level = 1)
        {
            framework\Logging::log($message, $this->getName(), $level);
        }

        public static function disableModule($module_id)
        {
            tables\Modules::getTable()->disableModuleByID($module_id);
        }

        public static function removeModule($module_id)
        {
            tables\Modules::getTable()->removeModuleByID($module_id);
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
            $crit->addUpdate(tables\Modules::ENABLED, 1);
            tables\Modules::getTable()->doUpdateById($crit, $this->getID());
            $this->_enabled = true;
        }

        final public function upgrade()
        {
            framework\Context::clearRoutingCache();
            framework\Context::clearPermissionsCache();
            $this->_upgrade();
            $this->save();
            Modules::getTable()->setModuleVersion($this->_name, static::VERSION);
        }

        final public function uninstall($scope = null)
        {
            if ($this->isCore())
            {
                throw new \Exception('Cannot uninstall core modules');
            }
            $this->_uninstall();
            $this->delete();
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            framework\Settings::deleteModuleSettings($this->getName(), $scope);
            framework\Context::deleteModulePermissions($this->getName(), $scope);
            framework\Context::clearRoutingCache();
            framework\Context::clearPermissionsCache();
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
            throw new \Exception('Trying to call function ' . $func . '() in module ' . $this->_shortname . ', but the function does not exist');
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
            $this->_availablepermissions[$permission_name] = array('description' => framework\Context::getI18n()->__($description), 'target_id' => $target);
        }

        public function getAvailablePermissions()
        {
            return $this->_availablepermissions;
        }

        public function getAvailableCommandLineCommands()
        {
            return array();
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
            return framework\Settings::get($setting, $this->getName(), framework\Context::getScope()->getID(), $uid);
        }

        public function saveSetting($setting, $value, $uid = 0, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            return framework\Settings::saveSetting($setting, $value, $this->getName(), $scope, $uid);
        }

        public function deleteSetting($setting, $uid = 0, $scope = null)
        {
            return framework\Settings::deleteSetting($setting, $this->getName(), $scope, $uid);
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
            if ($this->_version != static::VERSION)
            {
                return true;
            }
            return false;
        }

        public function addRoute($key, $url, $function, $params = array(), $options = array(), $module_name = null)
        {
            $module_name = ($module_name !== null) ? $module_name : $this->getName();
            $this->_routes[] = array($key, $url, $module_name, $function, $params, $options);
        }

        final public function initialize()
        {
            $this->_initialize();
            if ($this->isEnabled())
            {
                $this->_addListeners();
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

        public function loadHelpTitle($topic)
        {
            return $topic;
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
            return framework\Context::geti18n()->__($this->_account_settings_name);
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

        public function postConfigSettings(framework\Request $request)
        {

        }

        public function postAccountSettings(framework\Request $request)
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

        public static function downloadPlugin($plugin_type, $plugin_key)
        {
            try
            {
                $client = new \Net_Http_Client();
                $client->get('http://www.thebuggenie.com/'.$plugin_type.'s/'.$plugin_key . '.json');
                $plugin_json = json_decode($client->getBody());
            }
            catch (\Exception $e) {}

            if (isset($plugin_json) && $plugin_json !== false) {
                $filename = THEBUGGENIE_CACHE_PATH . $plugin_type . '_' . $plugin_json->key . '.zip';
                $client->get($plugin_json->download);
                if ($client->getResponse()->getStatus() != 200)
                {
                    throw new framework\exceptions\ModuleDownloadException("", framework\exceptions\ModuleDownloadException::JSON_NOT_FOUND);
                }
                file_put_contents($filename, $client->getBody());
                $module_zip = new \ZipArchive();
                $module_zip->open($filename);
                switch ($plugin_type) {
                    case 'addon':
                        $target_folder = THEBUGGENIE_MODULES_PATH;
                        break;
                    case 'theme':
                        $target_folder = THEBUGGENIE_PATH . 'themes';
                        break;
                }
                $module_zip->extractTo(realpath($target_folder));
                $module_zip->close();
                unlink($filename);
            } else {
                throw new framework\exceptions\ModuleDownloadException("", framework\exceptions\ModuleDownloadException::FILE_NOT_FOUND);
            }
        }

        public static function downloadModule($module_key)
        {
            self::downloadPlugin('addon', $module_key);
        }

        public static function downloadTheme($theme_key)
        {
            self::downloadPlugin('theme', $theme_key);
        }

    }
