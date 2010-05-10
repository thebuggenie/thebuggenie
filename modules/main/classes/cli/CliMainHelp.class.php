<?php

	/**
	 * CLI command class, main -> help
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> help
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainHelp extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'help';
		}

		public function getDescription()
		{
			return "Prints out help information";
		}

		public function getCommandAliases()
		{
			//return array('-h', '--help', '-?', '-help', '--h');
			return parent::getCommandAliases();
		}

		public function do_execute()
		{
			$this->cliEcho("The Bug Genie CLI help\n", 'white', 'bold');
			$this->cliEcho("Below is a list of available commands:\n\n");
			$commands = $this->getAvailableCommands();
			
			foreach ($commands as $module_name => $module_commands)
			{
				if ($module_name != 'main' && count($module_commands) > 0)
				{
					$this->cliEcho("{$module_name}:\n", 'green', 'bold');
				}
				foreach ($module_commands as $command_name => $command)
				{
					if ($module_name != 'main') $this->cliEcho("\t");
					$this->cliEcho("{$command_name}", 'green', 'bold');
					$this->cliEcho(" - {$command->getDescription()}\n");
				}
			}

			$this->cliEcho("\n");
		}

	}