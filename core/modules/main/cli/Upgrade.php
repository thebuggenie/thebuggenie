<?php

    namespace thebuggenie\core\modules\main\cli;

    use thebuggenie\core\framework;

    /**
     * CLI command class, main -> license
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> clear-cache
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Upgrade extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'upgrade';
            $this->_description = "Upgrades the installation to the current version.";
        }

        public function do_execute()
        {
            list ($current_version, $upgrade_available) = framework\Settings::getUpgradeStatus();

            $this->cliEcho('Currently installed version: ');
            $this->cliEcho($current_version, 'white', 'bold');
            $this->cliEcho("\n");
            $this->cliEcho('Upgrading to version: ');
            $this->cliEcho(framework\Settings::getVersion(false), 'green', 'bold');
            $this->cliEcho("\n");

            if (!$upgrade_available) {
                $this->cliEcho('No upgrade necessary', 'green');
                $this->cliEcho("\n");
                return;
            } else {
                try {
                    $upgrader = new \thebuggenie\core\modules\installation\Upgrade();
                    $result = $upgrader->upgrade();
                    if ($result) {
                        $this->cliEcho("Upgrade complete!\n");
                    } else {
                        $this->cliEcho("Upgrade failed!\n", 'red');
                    }
                } catch (\Exception $e) {
                    $this->cliEcho("An error occured during the upgrade:\n", 'red', 'bold');
                    $this->cliEcho($e->getMessage() . "\n");
                }
            }
        }

    }
