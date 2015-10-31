<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> show_issue
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, remote -> show_issue
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ShowIssue extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'show_issue';
            $this->_description = "Show detailed information about an issue on a remote server";
            $this->addRequiredArgument('project_key', 'The project key for the project you want to update an issue for');
            $this->addRequiredArgument('issue_number', 'The issue number for the issue you want to update');
            $this->addOptionalArgument('include_comments', 'Whether to include comments in the issue details (yes/no)');
            $this->addOptionalArgument('include_system_comments', 'Whether to include comments in the issue details (yes/no default no)');
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Showing detailed information about ');
            $this->cliEcho($this->getProvidedArgument('project_key'), 'green');
            $this->cliEcho(' issue ');
            $print_issue_number = $this->getProvidedArgument('issue_number');

            if (is_numeric($print_issue_number))
                $print_issue_number = '#' . $print_issue_number;

            $this->cliEcho($print_issue_number, 'yellow');
            $this->cliEcho(' on ');
            $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
            $this->cliEcho("\n");

            $url_options = array('project_key' => $this->project_key, 'issue_no' => $this->issue_number, 'format' => 'json');

            $this->cliEcho("\n");
            
            $issue = $this->getRemoteResponse($this->getRemoteURL('viewissue', $url_options));

            \thebuggenie\core\framework\Context::loadLibrary('common');
            $this->cliEcho($print_issue_number, 'green', 'bold');
            $this->cliEcho(" - ");
            $state = ($issue->state == \thebuggenie\core\entities\Issue::STATE_OPEN) ? 'OPEN' : 'CLOSED';
            $this->cliEcho("[{$state}] ", 'cyan');
            $this->cliEcho(html_entity_decode($issue->title), 'white', 'bold');
            $this->cliEcho("\n");
            $this->cliEcho("State: ", 'white', 'bold');
            $this->cliEcho($state);
            $this->cliEcho("\n");
            $this->cliEcho("Posted: ", 'white', 'bold');
            $this->cliEcho(tbg_formatTime($issue->created_at, 21) . ' (' . $issue->created_at . ')');
            $this->cliEcho("\n");
            $this->cliEcho("Posted by: ", 'white', 'bold');

            if ($issue->posted_by)
                $this->cliEcho($issue->posted_by->name);
            else
                $this->cliEcho('-');

            $this->cliEcho("\n");
            $this->cliEcho("Updated: ", 'white', 'bold');
            $this->cliEcho(tbg_formatTime($issue->updated_at, 21) . ' (' . $issue->updated_at . ')');
            $this->cliEcho("\n");
            $this->cliEcho("Assigned to: ", 'white', 'bold');

            if ($issue->assignee)
                $this->cliEcho($issue->assignee->name);
            else
                $this->cliEcho('-');

            $this->cliEcho("\n");
            $this->cliEcho("Status: ", 'white', 'bold');

            if ($issue->status)
                $this->cliEcho($issue->status->name);
            else
                $this->cliEcho('-');

            $this->cliEcho("\n");
            
            foreach ($issue->visible_fields as $field => $details)
            {
                $name = ucfirst(str_replace('_', ' ', $field));                
                $this->cliEcho("{$name}: ", 'white', 'bold');
                if ($issue->$field)
                {
                    if ($field == 'estimated_time' || $field == 'spent_time')
                    {
                        if (isset($issue->$field->points))
                            $this->cliEcho($issue->$field->points . 'p, ' . $issue->$field->hours . 'h, ' . $issue->$field->days . 'd, ' . $issue->$field->weeks . 'w, ' . $issue->$field->months . 'mo');
                        else
                            $this->cliEcho('-');
                    }
                    else
                    {
                        if (is_object($issue->$field))
                            $this->cliEcho($issue->$field->name);
                        else
                            $this->cliEcho($issue->$field);
                    }
                }
                else
                {
                    $this->cliEcho('-');
                }
                $this->cliEcho("\n");
            }

            if ($this->getProvidedArgument('include_comments', 'no') == 'yes')
            {
                $this->cliEcho("\n");
                $this->cliEcho("Comments: \n", 'white', 'bold');
                if (count($issue->comments) > 0)
                {
                    foreach ($issue->comments as $comment)
                    {
                        if ($comment->system_comment && $this->getProvidedArgument('include_system_comments', 'no') != 'yes')
                            continue;

                        $this->cliEcho('Comment #' . $comment->comment_number, 'yellow', 'bold');
                        $this->cliEcho("\n");
                        $this->cliEcho('Posted by: ', 'white', 'bold');
                        if ($comment->posted_by)
                            $this->cliEcho($comment->posted_by->name);
                        else
                            $this->cliEcho("Unknown user");
                        
                        $this->cliEcho("\n");
                        $this->cliEcho('Posted: ', 'white', 'bold');
                        $this->cliEcho(tbg_formatTime($comment->created_at, 21) . ' (' . $comment->created_at . ')');
                        $this->cliEcho("\n");
                        $this->cliEcho('Comment: ', 'white', 'bold');
                        $this->cliEcho($comment->content);
                        $this->cliEcho("\n");
                        $this->cliEcho('----------', 'white', 'bold');
                        $this->cliEcho("\n\n");
                    }
                }
                else
                {
                    $this->cliEcho('There are no comments');
                }
            }

            $this->cliEcho("\n\n");
        }

    }
