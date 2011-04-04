<?php

	/**
	 * CLI command class, remote -> list_projects
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, remote -> list_projects
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliRemoteListProjects extends TBGCliRemoteCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'list_projects';
			$this->_description = "Query a remote server for a list of available projects";
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho('Querying ');
			$this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
			$this->cliEcho(" for list of projects ...\n\n");

			$response = $this->getRemoteResponse($this->getRemoteURL('list_projects', array('format' => 'json')));

			if (!empty($response))
			{
				$this->cliEcho("project_key", 'green', 'bold');
				$this->cliEcho(" - project name\n", 'white', 'bold');
				foreach ($response as $project_key => $project_name)
				{
					$this->cliEcho($project_key, 'green');
					$this->cliEcho(" - $project_name\n");
				}
				$this->cliEcho("\n");
			}
			else
			{
				$this->cliEcho("No projects available.\n\n");
			}
		}

	}