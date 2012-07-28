<?php

	/**
	 * CLI command class, main -> list_scopes
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> list_scopes
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainListScopes extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'list_scopes';
			$this->_description = "List available scopes";
			parent::_setup();
		}

		public function do_execute()
		{
			$scopes = TBGScope::getAll();
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