<?php

	/**
	 * CLI command class, main -> revert_auth_backend
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> revert_auth_backend
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainRevertAuthBackend extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'revert_auth_backend';
			$this->_description = "Reverts the auth backend back to the default";
//			$this->addOptionalArgument('accept_license', 'Set to "yes" to auto-accept license');
		}

		public function do_execute()
		{
			$this->cliEcho("\n");
			$this->cliEcho("Revert authentication backend\n", 'white', 'bold');
			$this->cliEcho("This command is useful if you've managed to lock yourself.\n");
			$this->cliEcho("out due to an authentication backend change gone bad.\n\n");

			if (TBGSettings::getAuthenticationBackend() == 'tbg' || TBGSettings::getAuthenticationBackend() == null)
			{
				$this->cliEcho("You are currently using the default authentication backend.\n\n");
			}
			else
			{
				$this->cliEcho("Please type 'yes' if you want to revert to the default authentication backend: ");
				$this->cliEcho("\n");
				if ($this->getInput() == 'yes')
				{
					TBGSettings::saveSetting(TBGSettings::SETTING_AUTH_BACKEND, 'tbg');
					$this->cliEcho("Authentication backend reverted.\n\n");
				}
				else
				{
					$this->cliEcho("No changes made.\n\n");
				}
			}
		}

	}