<?php

	/**
	 * CLI command class, vcs_integration -> report
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 ** @version 3.0
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
			$this->addRequiredArgument('projectid', "Project ID number");
			$this->addRequiredArgument('author', "Username of the committer");
			$this->addRequiredArgument('revno', "Revision number or hash of this commit");
			$this->addRequiredArgument('log', "Log entry from commit");
			$this->addRequiredArgument('changed', "List of added, deleted and modified files, one per line prefixed with A/D/U as appropriate");
			
			$this->addOptionalArgument('oldrev', "Revision number or hash of previous revision");
			$this->addOptionalArgument('date', "POSIX timestamp of commit");
		}

		public function do_execute()
		{
			/* Prepare variables */
			$project = $this->getProvidedArgument('projectid');
			$author = $this->getProvidedArgument('author');
			$new_rev = $this->getProvidedArgument('revno');
			$commit_msg = $this->getProvidedArgument('log');
			$changed = $this->getProvidedArgument('changed');
			$old_rev = $this->getProvidedArgument('oldrev', $new_rev - 1);
			$date = $this->getProvidedArgument('date', null);
			
			if ((TBGContext::getModule('vcs_integration')->isUsingHTTPMethod()))
			{
				$this->cliEcho("This access method has been disallowed\n", 'red', 'bold');
				exit;
			}

			if ((!is_numeric($new_rev) && is_numeric($old_rev)) || (is_numeric($new_rev) && !is_numeric($old_rev)))
			{
				$this->cliEcho("If the old revision is specified, it must be the same format as the new revision (number or hash)\n", 'red', 'bold');
				exit;
			}
			
			$output = TBGContext::getModule('vcs_integration')->addNewCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author);
			$this->cliEcho($output);
		}
	}