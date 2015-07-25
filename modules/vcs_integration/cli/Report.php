<?php

    namespace thebuggenie\modules\vcs_integration\cli;

    use thebuggenie\modules\vcs_integration\Vcs_integration;

    /**
     * CLI command class, vcs_integration -> report
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * CLI command class, vcs_integration -> report
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     */
    class Report extends \thebuggenie\core\framework\cli\Command
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
                $project_id = $this->getProvidedArgument('projectid');
                $project_row = \thebuggenie\core\entities\tables\Projects::getTable()->getById($project_id, false);
                \thebuggenie\core\framework\Context::setScope(new \thebuggenie\core\entities\Scope($project_row[\thebuggenie\core\entities\tables\Projects::SCOPE]));
                $project = new \thebuggenie\core\entities\Project($project_id, $project_row);
            }
            catch (\Exception $e)
            {
                $this->cliEcho("The project with the ID ".$this->getProvidedArgument('projectid')." does not exist\n", 'red', 'bold');
                exit;
            }
            
            $author = $this->getProvidedArgument('author');
            $new_rev = $this->getProvidedArgument('revno');
            $commit_msg = $this->getProvidedArgument('log');
            $changed = $this->getProvidedArgument('changed');
            $old_rev = $this->getProvidedArgument('oldrev', null);
            $date = $this->getProvidedArgument('date', null);
            $branch = $this->getProvidedArgument('branch', null);
            
            if (\thebuggenie\core\framework\Settings::get('access_method_'.$project->getKey()) == Vcs_integration::ACCESS_HTTP)
            {
                $this->cliEcho("This project uses the HTTP access method, and so access via the CLI has been disabled\n", 'red', 'bold');
                exit;
            }

            if ($old_rev === null && !ctype_digit($new_rev))
            {
                $this->cliEcho("Error: if only the new revision is specified, it must be a number so that old revision can be calculated from it (by substracting 1 from new revision number).");
            }
            else if ($old_rev === null)
            {
                $old_rev = $new_rev - 1;
            }

            $output = Vcs_integration::processCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author, $branch);
            $this->cliEcho($output);
        }
    }
