<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, main -> set_remote
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> set_remote
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Authenticate extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'authenticate';
            $this->_description = "Authenticate with a remote server";
            $this->addRequiredArgument('server_url', "The URL for the remote The Bug Genie installation");
            $this->addOptionalArgument('username', "The username to connect with. If not specified, will use the current logged in user");
            $this->_initializeUrlFopen();
        }
        
        protected function _prepare()
        {
        }

        public function do_execute()
        {
            $this->cliEcho('Authenticating with server: ');
            $this->cliEcho($this->getProvidedArgument('server_url'), 'white', 'bold');
            $this->cliEcho("\n");

            $path = THEBUGGENIE_CONFIG_PATH;
            try 
            {
                file_put_contents($path . '.remote_server', $this->getProvidedArgument('server_url'));
            }
            catch (\Exception $e)
            {
                $path = getenv('HOME') . DS;
                file_put_contents($path . '.remote_server', $this->getProvidedArgument('server_url'));
            }

            $this->cliEcho('Authenticating as user: ');
            $username = $this->getProvidedArgument('username', \thebuggenie\core\framework\Context::getCurrentCLIusername());
            $this->cliEcho($username, 'white', 'bold');
            $this->cliEcho("\n");
            file_put_contents($path . '.remote_username', $username);
            $this->_current_remote_server = file_get_contents($path . '.remote_server');
            $this->cliEcho("\n");
            $this->cliEcho('You need to authenticate using an application-specific password.');
            $this->cliEcho("\n");
            $this->cliEcho("Create an application password from your account's 'Security' tab.");
            $this->cliEcho("\n");
            $this->cliEcho("Enter the application-specific password: ", 'white', 'bold');
            $password = $this->_getCliInput();
            $response = $this->getRemoteResponse($this->getRemoteURL('api_authenticate', array('username' => $username)), array('password' => $password));
            if (!is_object($response))
            {
                throw new \Exception('An error occured when receiving authentication response from the server');
            }
            file_put_contents($path . '.remote_token', sha1($response->token));
            $this->cliEcho("Authentication successful!\n", 'white', 'bold');
        }

    }
