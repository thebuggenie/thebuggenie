<?php

	/**
	 * CLI command class, main -> remove_scope
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> remove_scope
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainRemoveScope extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'remove_scope';
			$this->_description = "Remove an existing scope";
			$this->addRequiredArgument('hostname', 'The hostname for this scope');
			parent::_setup();
		}

		public function do_execute()
		{
			$hostname = $this->getProvidedArgument('hostname');
			$this->cliEcho("Removing scope with hostname {$hostname} ..");
			$scope = TBGScopesTable::getTable()->getByHostname($hostname);
			if ($scope instanceof TBGScope && !$scope->isDefault())
			{
				$scope->delete();
				$this->cliEcho(".done!\n");
			}
			else
			{
				$this->cliEcho(" invalid scope!\n", 'red');
			}
		}

	}