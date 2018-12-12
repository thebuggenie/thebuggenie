<?php

    namespace thebuggenie\core\framework\interfaces;

    interface ModuleInterface
    {

        public function saveSetting($name, $value, $uid = 0, $scope = null);

        public function getSetting($name, $uid = 0);

        public function deleteSetting($name, $uid = 0, $scope = null);

        public function hasAccountSettings();

        public function initialize();

    }