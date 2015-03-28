<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> list_issues
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, remote -> list_issues
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ListIssues extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_issues';
            $this->_description = "Query a remote server for a list of available issues for a project";
            $this->addRequiredArgument("project_key", "The project key for the project you wish to list issues for (see remote_list_project)");
            $this->addOptionalArgument("state", "Filter show only [open/closed/all] issues");
            $this->addOptionalArgument("issuetype", "Filter show only issues of type [<issue type>] (see remote_list_issuetypes)");
            $this->addOptionalArgument("assigned_to", "Filter show only issues assigned to [<username>/me/none/all]");
            $this->addOptionalArgument("detailed", 'Whether to show a detailed issue list or not [yes/no] (default <no>)');
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
                \thebuggenie\core\framework\Context::loadLibrary('common');
                $this->cliEcho("The following {$response->count} issues were found:\n", 'white', 'bold');

                foreach ($response->issues as $issue)
                {
                    //$this->cliEcho("ID: {$issue->id} ", 'yellow');
                    if (mb_strtolower($options['state']) == 'all')
                    {
                        $this->cliEcho(($issue->state == \thebuggenie\core\entities\Issue::STATE_OPEN) ? "[open] " : "[closed] ");
                    }
                    $this->cliEcho($issue->issue_no, 'green', 'bold');
                    $this->cliEcho(" - ");
                    $this->cliEcho(html_entity_decode($issue->title), 'white', 'bold');
                    $this->cliEcho("\n");
                    if ($this->getProvidedArgument('detailed', 'no') == 'yes')
                    {
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
                }
                $this->cliEcho("\n");
                $this->cliEcho("If you are going to update or query any of these issues, use the \n");
                $this->cliEcho("issue number shown in front of the issue (do not include the \n");
                $this->cliEcho("issue type), ex:\n");
                $this->cliEcho("./tbg_cli", 'green');
                $this->cliEcho(" remote:update_issue projectname ");
                $this->cliEcho("300\n", 'white', 'bold');
                $this->cliEcho("./tbg_cli", 'green');
                $this->cliEcho(" remote:show_issue projectname ");
                $this->cliEcho("300\n", 'white', 'bold');
                $this->cliEcho("./tbg_cli", 'green');
                $this->cliEcho(" remote:list_transitions projectname ");
                $this->cliEcho("300\n", 'white', 'bold');
                $this->cliEcho("\nor\n");
                $this->cliEcho("./tbg_cli", 'green');
                $this->cliEcho(" remote:update_issue projectname ");
                $this->cliEcho("PREFIX-12\n", 'white', 'bold');
                $this->cliEcho("./tbg_cli", 'green');
                $this->cliEcho(" remote:show_issue projectname ");
                $this->cliEcho("PREFIX-12\n", 'white', 'bold');
                $this->cliEcho("./tbg_cli", 'green');
                $this->cliEcho(" remote:list_transitions projectname ");
                $this->cliEcho("PREFIX-12\n", 'white', 'bold');
                $this->cliEcho("\n");
                $this->cliEcho("\n");
            }
            else
            {
                $this->cliEcho("No issues available matching your filters.\n\n");
            }
        }

    }
