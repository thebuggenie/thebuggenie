<?php

	/**
	 * CLI command class, vcs_integration -> report
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * CLI command class, vcs_integration -> report
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class CliVCS_IntegrationReport extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'report_commit';
			$this->_description = "Report a new commit to an issue";
			$this->addRequiredArgument('projectid', "Project ID");
			$this->addRequiredArgument('author', "Username of the committer");
			$this->addRequiredArgument('revno', "Revision number or hash of this commit");
			$this->addRequiredArgument('log', "Log entry from commit");
			$this->addRequiredArgument('changed', "List of added, deleted and modified files, one per line prefixed with A/D/U as appropriate");
			
			$this->addOptionalArgument('oldrev', "Revision number or hash of previous revision");
			$this->addOptionalArgument('date', "POSIX timestamp of commit");
			$this->addOptionalArgument('branch', "Branch this commit affects");
		}

		public function do_execute()
		{
			/* Prepare variables */			
			try
			{
				$row = TBGProjectsTable::getTable()->doSelectById($project);
				$project = new TBGProject($project, $row);
				TBGContext::setScope($project->getScope());
			}
			catch (Exception $e)
			{
				$this->cliEcho("The project with the ID ".$this->getProvidedArgument('projectid')." does not exist\n", 'red', 'bold');
				exit;
			}
			
			$author = $this->getProvidedArgument('author');
			$new_rev = $this->getProvidedArgument('revno');
			$commit_msg = $this->getProvidedArgument('log');
			$changed = $this->getProvidedArgument('changed');
			$old_rev = $this->getProvidedArgument('oldrev', $new_rev - 1);
			$date = $this->getProvidedArgument('date', null);
			$branch = $this->getProvidedArgument('branch', null);
			
			if (TBGSettings::get('access_method_'.$project->getKey()) == TBGVCSIntegration::ACCESS_HTTP)
			{
				$this->cliEcho("This project uses the HTTP access method, and so access via the CLI has been disabled\n", 'red', 'bold');
				exit;
			}

			if ((!is_numeric($new_rev) && is_numeric($old_rev)) || (is_numeric($new_rev) && !is_numeric($old_rev)))
			{
				$this->cliEcho("If the old revision is specified, it must be the same format as the new revision (number or hash)\n", 'red', 'bold');
				exit;
			}
			
			$output = TBGVCSIntegration::processCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author, $branch);
			$this->cliEcho($output);
		}
	}