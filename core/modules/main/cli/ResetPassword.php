<?php

    namespace thebuggenie\core\modules\main\cli;

    /**
     * CLI command class, main -> reset_password
     *
     * @author Asaf Ohayon <asaf@hadasa-oss.net>
     * @version 0.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> reset_password
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ResetPassword extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'reset_password';
            $this->_description = "Reset user password";
            $this->addRequiredArgument('user_name', "The user to reset password for");
            $this->addRequiredArgument('user_pass', "The new password");
        }

        public function do_execute()
        {
            $username = $this->getProvidedArgument('user_name');
            $password = $this->getProvidedArgument('user_pass');

            $user = \thebuggenie\core\entities\User::getByUsername($username);

            var_dump($user);
            
            $this->cliEcho("The module was created successfully!\n", 'green');        
        }
        
    }
