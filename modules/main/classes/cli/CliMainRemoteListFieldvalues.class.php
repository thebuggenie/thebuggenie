<?php

	/**
	 * CLI command class, main -> remote_list_fieldvalues
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> remote_list_fieldvalues
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainRemoteListFieldvalues extends TBGCliRemoteCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'remote_list_fieldvalues';
			$this->_description = "Query a remote server for a list of available field values";
			$this->addRequiredArgument('issue_field', 'An issue field to show available values for');
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho("Not implemented\n");
			return null;
			
			/*$this->cliEcho('Querying ');
			$this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
			$this->cliEcho(" for issuefields valid for issue types {$issuetype} for project {$project_key}\n\n");

			$response = $this->getRemoteResponse($this->getRemoteURL('project_list_issuefields', array('issuetype' => $issuetype, 'project_key' => $project_key, 'format' => 'json')));

			if (!empty($response))
			{
				$this->cliEcho($issuetype, 'yellow', 'bold');
				$this->cliEcho(" has the following available issue fields:\n");
				foreach ($response->issuefields as $field_key)
				{
					$this->cliEcho("  {$field_key}\n", 'yellow');
				}
				$this->cliEcho("\n");
				$this->cliEcho("When using ");
				$this->cliEcho('remote_update_issue', 'green');
				$this->cliEcho(" to update an issue, pass any of these issue fields\n");
				$this->cliEcho("as a valid parameter to update the issue details.\n");
				$this->cliEcho("\n");
				$this->cliEcho("Check the documentation for ");
				$this->cliEcho('remote_update_issue', 'green');
				$this->cliEcho(" for more information.\n");

			}
			else
			{
				$this->cliEcho("No issue fields available.\n\n");
			}*/
		}

	}