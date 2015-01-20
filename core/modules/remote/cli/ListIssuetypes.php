<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> list_issuetypes
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, remote -> list_issuetypes
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ListIssuetypes extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_issuetypes';
            $this->_description = "Query a remote server for a list of available issue types";
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Querying ');
            $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
            $this->cliEcho(" for list of issuetypes ...\n\n");

            $response = $this->getRemoteResponse($this->getRemoteURL('remote_list_issuetypes', array('format' => 'json')));

            if (!empty($response))
            {
                $this->cliEcho("issuetype_key", 'yellow', 'bold');
                $this->cliEcho(" - Description\n", 'white', 'bold');
                foreach ($response as $key => $issuetype)
                {
                    $this->cliEcho("$key", 'yellow');
                    $this->cliEcho(" - $issuetype\n");
                }
                $this->cliEcho("\n");
                $this->cliEcho("When using ");
                $this->cliEcho('list_issues', 'green');
                $this->cliEcho(" to query for issues, you can pass any of these\n");
                $this->cliEcho("issue types as a valid parameter for the issue type.\n");
                $this->cliEcho("The value is not case sensitive and you can specify the issue type with or without\n");
                $this->cliEcho("spaces, so i.e. the issue type 'Bug report' can be written as 'bugreport'\n\n");
            }
            else
            {
                $this->cliEcho("No issue types available.\n\n");
            }
        }

    }
