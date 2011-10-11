<?php

	/**
	 * CLI command class, main -> set_remote
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> set_remote
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliRemoteSetServer extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'set_server';
			$this->_description = "Set a default server to connect to and / or username to connect as";
			$this->addRequiredArgument('server_url', "The URL for the remote The Bug Genie installation");
			$this->addOptionalArgument('username', "The username to connect with. If not specified, will use the current logged in user");
		}

		public function do_execute()
		{
			$this->cliEcho('Saving remote server: ');
			$this->cliEcho($this->getProvidedArgument('server_url'), 'white', 'bold');
			$this->cliEcho("\n");

			$path = THEBUGGENIE_CONFIG_PATH;
			try 
			{
				file_put_contents($path . '.remote_server', $this->getProvidedArgument('server_url'));
			}
			catch (Exception $e)
			{
				$path = getenv('HOME') . DS;
				file_put_contents($path . '.remote_server', $this->getProvidedArgument('server_url'));
			}

			if ($this->hasProvidedArgument('username'))
			{
				$this->cliEcho('Saving remote username: ');
				$this->cliEcho($this->getProvidedArgument('username'), 'white', 'bold');
				$this->cliEcho("\n");
				file_put_contents($path . '.remote_username', $this->getProvidedArgument('username'));
				$this->cliEcho("\n");
				$this->cliEcho('To avoid being asked for a password, please enter the password for the remote user ');
				$this->cliEcho($this->getProvidedArgument('username'), 'white', 'bold');
				$this->cliEcho(" (a hash of the password will be stored).\nIf you don't want to store this, simply press enter:\n");
				$this->cliEcho("Enter the password for the {$this->getProvidedArgument('username')} user: ", 'white', 'bold');
				$password = $this->_getCliInput();
				$this->cliEcho("Please enter the remote security key: ", 'white', 'bold');
				$salt = $this->_getCliInput();
				if ($password != '' && $salt != '')
				{
					file_put_contents($path . '.remote_password_hash', TBGUser::hashPassword($password, $salt));
					$this->cliEcho("Authentication details saved.\n", 'white', 'bold');
				}
				else
				{
					$this->cliEcho("\n");
					$this->cliEcho("Please provide both password and security key.\n");
					$this->cliEcho("If you haven't received the security key, please contact the remote server administrator.\n\n");
					$this->cliEcho("Password hash not saved.\n", 'white', 'bold');
				}
			}
		}

	}
