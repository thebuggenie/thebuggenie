<?php

	/**
	 * CLI command class, main -> remote_list_issues
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> remote_list_issues
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainRemoteListIssues extends TBGCliRemoteCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'remote_list_issues';
			$this->_description = "Query a remote server for a list of available issues for a project";
			$this->addRequiredArgument("project_key", "The project key for the project you wish to list issues for (see remote_list_project)");
			$this->addOptionalArgument("state", "Filter show only [open/closed/all] issues");
			$this->addOptionalArgument("issuetype", "Filter show only issues of type [<issue type>] (see remote_list_issuetypes)");
			$this->addOptionalArgument("assigned_to", "Filter show only issues assigned to [<username>/me/none/all]");
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho('Querying ');
			$this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
			$this->cliEcho(" for list of issues ...\n\n");

			$this->cliEcho("Filters:\n", 'white', 'bold');
			$options = array('format' => 'json');
			$options["state"] = $this->getProvidedArgument("state", "all");
			$this->cliEcho("State: ");
			$this->cliEcho($options["state"], "yellow", "bold");
			$this->cliEcho("\n");

			$options["issuetype"] = $this->getProvidedArgument("issuetype", "all");
			$this->cliEcho("Issuetypes: ");
			$this->cliEcho($options["issuetype"], "yellow", "bold");
			$this->cliEcho("\n");

			$options["assigned_to"] = $this->getProvidedArgument("assigned_to", "all");
			$this->cliEcho("Assigned to: ");
			$this->cliEcho($options["assigned_to"], "yellow", "bold");
			$this->cliEcho("\n");
			$options["assigned"] = $this->getProvidedArgument("state", "all");

			$options['project_key'] = $this->getProvidedArgument('project_key');

			$response = $this->getRemoteResponse($this->getRemoteURL('project_list_issues', $options));

			$this->cliEcho("\n");
			if (!empty($response) && $response->count > 0)
			{
				TBGContext::loadLibrary('common');
				$this->cliEcho("The following {$response->count} issues were found:\n", 'white', 'bold');

				foreach ($response->issues as $issue)
				{
					$this->cliEcho("ID: {$issue->id} ", 'yellow');
					if (strtolower($options['state']) == 'all')
					{
						$this->cliEcho(($issue->_userstate == TBGIssue::STATE_OPEN) ? "[open] " : "[closed] ");
					}
					$this->cliEcho($issue->issue_no, 'green', 'bold');
					$this->cliEcho(" - ");
					$this->cliEcho($issue->title, 'white', 'bold');
					$this->cliEcho("\n");
					$this->cliEcho("Posted: ", 'blue', 'bold');
					$this->cliEcho(tbg_formatTime($issue->created_at, 21));
					$this->cliEcho(" by ");
					$this->cliEcho($issue->posted_by, 'cyan');
					$this->cliEcho("\n");
					$this->cliEcho("Updated: ", 'blue', 'bold');
					$this->cliEcho(tbg_formatTime($issue->last_updated, 21));
					$this->cliEcho("\n");
					$this->cliEcho("Assigned to: ", 'blue', 'bold');
					$this->cliEcho($issue->assigned_to, 'yellow', 'bold');
					$this->cliEcho(" | ", 'white', 'bold');
					$this->cliEcho("Status: ", 'blue', 'bold');
					$this->cliEcho($issue->status);
					$this->cliEcho("\n\n");
				}
				$this->cliEcho("If you are going to update any of these issues, use the id shown\n");
				$this->cliEcho("in front of the issue as the issue id.\n");
				$this->cliEcho("\n");
			}
			else
			{
				$this->cliEcho("No issues available matching your filters.\n\n");
			}
		}

	}