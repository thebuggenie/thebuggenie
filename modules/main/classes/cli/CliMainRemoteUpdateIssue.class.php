<?php

	/**
	 * CLI command class, main -> remote_update_issue
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
			$this->addRequiredArgument('project_key', 'The project key for the project you want to update an issue for');
			$this->addRequiredArgument('issue_id', 'The ID of the issue to update (see remote_list_issues or remote_get_issue_id)');
			$this->addOptionalArgument('state', 'Change the state of the issue (open/closed)');
			$this->addOptionalArgument('m', 'A comment to save with the changes');
			parent::_setup();
		}

		public function do_execute()
		{
			$this->cliEcho('Updating ');
			$this->cliEcho($this->getProvidedArgument('project_key'), 'green');
			$this->cliEcho(' issue ID ');
			$this->cliEcho($this->getProvidedArgument('issue_id'), 'yellow');
			$this->cliEcho(' on ');
			$this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
			$this->cliEcho("\n");

			if (!$this->hasProvidedArgument('m'))
			{
				$this->cliEcho("\nPlease enter a message to save with your changes, or ");
				$this->cliEcho("CTRL+C", 'white', 'bold');
				$this->cliEcho(" to cancel ...\n");
				$this->cliEcho("Message: ", 'white', 'bold');
				$message = $this->getInput();
			}
			else
			{
				$message = $this->getProvidedArgument("m");
			}

			if (!$message)
			{
				throw new Exception("Please enter a valid message with your changes");
			}

			$url_options = array('project_key' => $this->project_key, 'issue_id' => $this->issue_id);
			$post_data = $this->getNamedArguments();
			
			foreach (array('server', 'username', 'project_key', 'issue_id', 'm') as $key)
			{
				if (array_key_exists($key, $post_data)) unset($post_data[$key]);
			}
			$url_options['format'] = 'json';

			$fields = $post_data;
			foreach ($post_data as $key => $value)
			{
				$post_data["fields[{$key}]"] = $value;
				unset($post_data[$key]);
			}
			$post_data['message'] = $message;
			
			$this->cliEcho("\n");
			$this->cliEcho("Updating fields: \n", 'white', 'bold');
			$response = $this->getRemoteResponse($this->getRemoteURL('project_update_issuedetails', $url_options), $post_data);
			foreach ($fields as $key => $value)
			{
				$this->cliEcho("  ".str_pad($key, 20), 'yellow');
				if ($response->$key && $response->$key->success)
				{
					$this->cliEcho('OK!', 'green');
				}
				else
				{
					$this->cliEcho("failed ({$response->$key->error})", 'red');
				}
				$this->cliEcho("\n");
			}

			$this->cliEcho("\n");

		}

	}