<?php

    namespace thebuggenie\core\framework;

    /**
     * Core module class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Core module class
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    abstract class CoreModule implements interfaces\ModuleInterface
    {

        protected $name;

        public function __construct($name)
        {
            $this->name = $name;
        }

        public function getName()
        {
            return $this->name;
        }

        /**
         * Save a setting
         *
         * @param string $name The settings key / name of the setting to store
         * @param mixed $value The value to store
         * @param int $scope A scope id (or 0 to apply to all scopes)
         * @param int $uid A user id to save settings for
         * @throws \Exception
         */
        public function saveSetting($name, $value, $uid = 0, $scope = null)
        {
            $scope = ($scope === null) ? Context::getScope()->getID() : $scope;
            Settings::saveSetting($name, $value, $this->getName(), $scope, $uid);
        }

        public function getSetting($name, $uid = 0)
        {
            return Settings::get($name, $this->getName(), Context::getScope()->getID(), $uid);
        }

        public function deleteSetting($name, $uid = 0, $scope = null)
        {
            Settings::deleteSetting($name, $this->getName(), $scope, $uid);
        }

        public function hasAccountSettings()
        {
            return false;
        }

        public function initialize() {}

        public function getAccountSettingsLogo() {}

        public function getAccountSettingsName() {}

    }
