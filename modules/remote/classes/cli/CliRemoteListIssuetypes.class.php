<?php

	/**
	 * CLI command class, remote -> list_issuetypes
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, remote -> list_issuetypes
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliRemoteListIssuetypes extends TBGCliRemoteCommand
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

			$response = $this->getRemoteResponse($this->getRemoteURL('list_issuetypes', array('format' => 'json')));

			if (!empty($response))
			{
				$this->cliEcho("Available issuetypes\n", 'white', 'bold');
				foreach ($response as $issuetype)
				{
					$this->cliEcho("$issuetype\n", 'yellow');
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