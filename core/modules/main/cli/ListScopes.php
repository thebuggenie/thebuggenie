<?php

    namespace thebuggenie\core\modules\main\cli;

    /**
     * CLI command class, main -> list_scopes
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> list_scopes
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ListScopes extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'list_scopes';
            $this->_description = "List available scopes";
            parent::_setup();
        }

        public function do_execute()
        {
            $scopes = \thebuggenie\core\entities\Scope::getAll();
            $this->cliEcho("The ID for the default scope has an asterisk next to it\n\n");

            $this->cliEcho("ID", 'white', 'bold');
            $this->cliEcho(" - hostname(s)\n", 'white', 'bold');
            foreach ($scopes as $scope_id => $scope)
            {
                $this->cliEcho($scope_id, 'white', 'bold');
                if ($scope->isDefault())
                {
                    $this->cliEcho('*', 'white', 'bold');
                    $this->cliEcho(" - all unspecified hostnames\n");
                }
                else
                {
                    $this->cliEcho(" - ".join(', ', $scope->getHostnames())."\n");
                }
            }
            $this->cliEcho("\n");
        }

    }
