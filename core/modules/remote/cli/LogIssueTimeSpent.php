<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> log_issue_time_spent
     *
     * @package thebuggenie
     * @subpackage core
     */
    class LogIssueTimeSpent extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'log_issue_time_spent';
            $this->_description = "Log issue time spent on a remote server";
            $this->addRequiredArgument('project_key', 'The project key for the project you want to update an issue for');
            $this->addRequiredArgument('issue_id', 'The issue id for the issue you want to log time spent for');
            $this->addRequiredArgument('activitytype_id', 'The activity type id time is spent on for the issue');
            $this->addRequiredArgument('time', 'The time spent on activity type for the issue in fancy format, eg: 2 hours and 5 minutes');
            $this->addOptionalArgument('m', 'A comment to save with the changes');
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Logging time spent for ');
            $this->cliEcho($this->getProvidedArgument('project_key'), 'green');
            $this->cliEcho(' issue id ');

            $this->cliEcho($this->getProvidedArgument('issue_id'), 'yellow');
            $this->cliEcho(' on activity type id ');
            $this->cliEcho($this->getProvidedArgument('activitytype_id'), 'green');
            $this->cliEcho(' for ');
            $this->cliEcho($this->getProvidedArgument('time'), 'green');
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
                throw new \Exception("Please enter a valid message with your changes");
            }

            $url_options = array('project_key' => $this->project_key, 'issue_id' => $this->issue_id, 'entry_id' => 0, 'format' => 'json');

            $fields = array('timespent_manual' => $this->time, 'timespent_activitytype' => $this->activitytype_id, 'timespent_comment' => $message);

            $this->cliEcho("\n");

            $response = $this->getRemoteResponse($this->getRemoteURL('api_issue_edittimespent', $url_options), $fields);

            if (!is_object($response))
                throw new \Exception('Could not parse the return value from the server. Please re-check the command run.');

            if ($response->edited == 'ok')
            {
                $this->cliEcho('Logged time spent!', 'green');
            }
            else
            {
                $this->cliEcho("failed ({$response->error})", 'red');
            }

            $this->cliEcho("\n");

        }

    }
