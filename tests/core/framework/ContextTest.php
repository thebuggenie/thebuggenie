<?php

    namespace thebuggenie\core\framework;

    if (!class_exists('\\thebuggenie\\core\\framework\\Context')) require THEBUGGENIE_CORE_PATH . 'framework/Context.php';
    if (!class_exists('\\thebuggenie\\core\\framework\\Settings')) require THEBUGGENIE_CORE_PATH . 'framework/Settings.php';
    if (!class_exists('\\thebuggenie\\core\\framework\\exceptions\\ConfigurationException')) require THEBUGGENIE_CORE_PATH . 'framework/exceptions/ConfigurationException.php';

    class ContextTest extends \PHPUnit_Framework_TestCase
    {

        protected static $installed_file_exists = false;

        public static function setUpBeforeClass()
        {
            $installed_file = THEBUGGENIE_PATH . 'installed';

            self::$installed_file_exists = file_exists($installed_file);
            rename($installed_file, $installed_file . '.tmp');
        }

        /**
         * @covers \thebuggenie\core\framework\Context::checkInstallMode
         *
         * @expectedException \thebuggenie\core\framework\exceptions\ConfigurationException
         * @expectedExceptionCode \thebuggenie\core\framework\exceptions\ConfigurationException::NO_VERSION_INFO
         */
        public function testInstallModeThrowsExceptionWhenNoVersionInfoPresent()
        {
            $installed_file = THEBUGGENIE_PATH . 'installed';

            file_put_contents($installed_file, "");
            \thebuggenie\core\framework\Context::checkInstallMode();
        }

        /**
         * @covers \thebuggenie\core\framework\Context::checkInstallMode
         *
         * @expectedException \thebuggenie\core\framework\exceptions\ConfigurationException
         * @expectedExceptionCode \thebuggenie\core\framework\exceptions\ConfigurationException::UPGRADE_REQUIRED
         */
        public function testInstallModeThrowsExceptionWhenIncorrectVersion()
        {
            $installed_file = THEBUGGENIE_PATH . 'installed';

            file_put_contents($installed_file, "1.0, installed today");
            \thebuggenie\core\framework\Context::checkInstallMode();
        }

        /**
         * @covers \thebuggenie\core\framework\Context::isInstallmode
         * @covers \thebuggenie\core\framework\Context::checkInstallMode
         */
        public function testInstallMode()
        {
            $installed_file = THEBUGGENIE_PATH . 'installed';

            if (file_exists($installed_file)) unlink($installed_file);
            \thebuggenie\core\framework\Context::checkInstallMode();
            $this->assertTrue(\thebuggenie\core\framework\Context::isInstallmode());

            file_put_contents($installed_file, \thebuggenie\core\framework\Settings::getMajorVer() . "." . \thebuggenie\core\framework\Settings::getMinorVer() .", installed today");
            \thebuggenie\core\framework\Context::checkInstallMode();
            $this->assertFalse(\thebuggenie\core\framework\Context::isInstallmode());
        }

        public static function tearDownAfterClass()
        {
            $installed_file = THEBUGGENIE_PATH . 'installed';
            
            (self::$installed_file_exists) ? rename($installed_file . '.tmp', $installed_file) : unlink($installed_file);
        }

    }
