<?php

	/**
	 * CLI command class, main -> remote_update_issue
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> remote_update_issue
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainRemoteUpdateIssue extends TBGCliRemoteCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'remote_update_issue';
			$this->_description = "Update an issue on a remote server";
			$this->addRequiredArgument('issue_id', 'The ID of the issue to update (see remote_list_issues or remote_get_issue_id)');
			$this->addOptionalArgument('state', 'Change the state of the issue (open/closed)');
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho('Updating issue with ID ');
			$this->cliEcho($this->getProvidedArgument('issue_id'), 'yellow');
			$this->cliEcho(' on ');
			$this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');

			$response = $this->getRemoteResponse($this->getRemoteURL('list_projects', array('format' => 'json')));

		}

	}