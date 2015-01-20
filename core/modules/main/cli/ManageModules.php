<?php

    namespace thebuggenie\core\modules\main\cli;

    /**
     * CLI command class, main -> manage_modules
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> manage_modules
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ManageModules extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'manage_modules';
            $this->_description = "Manage installed / uninstalled modules";
            $this->addRequiredArgument('action', 'The action to perform (list_installed, list_available, install or uninstall)');
            $this->addOptionalArgument('module_name', "The module to perform the action on");
        }

        protected function _listInstalled()
        {
            $this->cliEcho("\nInstalled modules:\n", 'green', 'bold');
            foreach (\thebuggenie\core\framework\Context::getModules() as $module_key => $module)
            {
                $this->cliEcho("{$module_key}: ", 'white', 'bold');
                $this->cliEcho($module->getDescription());
                $this->cliEcho("\n");
            }
            $this->cliEcho("\n");
        }

        protected function _listAvailable()
        {
            $this->cliEcho("\nAvailable modules:\n", 'green', 'bold');
            $this->cliEcho("To install a module, use the name in bold as the parameter for the install module task\n\n");
            if (count(\thebuggenie\core\framework\Context::getUninstalledModules()) > 0)
            {
                foreach (\thebuggenie\core\framework\Context::getUninstalledModules() as $module_key => $module)
                {
                    $this->cliEcho("{$module_key}: ", 'white', 'bold');
                    $this->cliEcho($module->getLongName());
                    $this->cliEcho("\n");
                }
            }
            else
            {
                $this->cliEcho("There are no available modules\n", 'red');
            }
            $this->cliEcho("\n");
        }

        protected function _installModule($module_name)
        {
            $this->cliEcho("Install module\n", 'green', 'bold');
            try
            {
                if (!$module_name || !file_exists(THEBUGGENIE_MODULES_PATH . $module_name . DS . 'module'))
                {
                    throw new \Exception("Please provide a valid module name");
                }
                elseif (\thebuggenie\core\framework\Context::isModuleLoaded($module_name))
                {
                    throw new \Exception("This module is already installed");
                }
                else
                {
                    $this->cliEcho("Installing {$module_name} ...");
                    \thebuggenie\core\entities\Module::installModule($module_name);
                    $this->cliEcho(' ok!', 'green', 'bold');
                    $this->cliEcho("\n");
                }
            }
            catch (\Exception $e)
            {
                $this->cliEcho($e->getMessage()."\n", 'red');
            }
        }

        protected function _uninstallModule($module_name)
        {
            $this->cliEcho("Uninstall module\n", 'green', 'bold');
            try
            {
                if (!$module_name || !file_exists(THEBUGGENIE_MODULES_PATH . $module_name . DS . 'module'))
                {
                    throw new \Exception("Please provide a valid module name");
                }
                elseif (!\thebuggenie\core\framework\Context::isModuleLoaded($module_name))
                {
                    throw new \Exception("This module is not installed");
                }
                else
                {
                    $this->cliEcho("Removing {$module_name} ...");
                    \thebuggenie\core\framework\Context::getModule($module_name)->uninstall();
                    $this->cliEcho(' ok!', 'green', 'bold');
                    $this->cliEcho("\n");
                }
            }
            catch (\Exception $e)
            {
                $this->cliEcho($e->getMessage()."\n", 'red');
            }
        }

        public function do_execute()
        {
            if (\thebuggenie\core\framework\Context::isInstallmode())
            {
                $this->cliEcho("Manage modules\n", 'white', 'bold');
                $this->cliEcho("The Bug Genie is not installed\n", 'red');
            }
            else
            {
                switch ($this->getProvidedArgument('action'))
                {
                    case 'list_installed':
                        $this->_listInstalled();
                        break;
                    case 'list_available':
                        $this->_listAvailable();
                        break;
                    case 'install':
                        $this->_installModule($this->getProvidedArgument('module_name'));
                        break;
                    case 'uninstall':
                        $this->_uninstallModule($this->getProvidedArgument('module_name'));
                        break;
                    default:
                        $this->cliEcho("Unknown action\n", 'red');
                }
            }
        }

    }
