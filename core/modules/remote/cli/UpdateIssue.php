<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> update_issue
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, remote -> update_issue
     *
     * @package thebuggenie
     * @subpackage core
     */
    class UpdateIssue extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'update_issue';
            $this->_description = "Update an issue on a remote server";
            $this->addRequiredArgument('project_key', 'The project key for the project you want to update an issue for');
            $this->addRequiredArgument('issue_number', 'The issue number for the issue you want to update');
            $this->addOptionalArgument('workflow_transition', 'The workflow transition to trigger (if any)');
            $this->addOptionalArgument('m', 'A comment to save with the changes');
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Updating ');
            $this->cliEcho($this->getProvidedArgument('project_key'), 'green');
            $this->cliEcho(' issue ');
            $print_issue_number = $this->getProvidedArgument('issue_number');

            if (is_numeric($print_issue_number))
                $print_issue_number = '#' . $print_issue_number;

            $this->cliEcho($print_issue_number, 'yellow');
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

            $url_options = array('project_key' => $this->project_key, 'issue_no' => $this->issue_number);
            $post_data = $this->getNamedArguments();
            
            foreach (array('server', 'username', 'project_key', 'issue_number', 'm') as $key)
            {
                if (array_key_exists($key, $post_data)) unset($post_data[$key]);
            }
            $url_options['format'] = 'json';

            if (array_key_exists('workflow_transition', $post_data))
            {
                $url_options['workflow_transition'] = $post_data['workflow_transition'];
                unset($post_data['workflow_transition']);
            }

            $fields = $post_data;
            foreach ($post_data as $key => $value)
            {
                if (is_numeric($key)) continue;
                $post_data["fields[{$key}]"] = $value;
                unset($post_data[$key]);
            }
            $post_data['message'] = $message;
            
            $this->cliEcho("\n");
            
            if (array_key_exists('workflow_transition', $url_options))
                $this->cliEcho("Transitioning issue: \n", 'white', 'bold');
            else
                $this->cliEcho("Updating fields: \n", 'white', 'bold');

            if (count($fields) || array_key_exists('workflow_transition', $url_options))
            {
                $response = $this->getRemoteResponse($this->getRemoteURL('project_update_issuedetails', $url_options), $post_data);

                if (!is_object($response))
                    throw new \Exception('Could not parse the return value from the server. Please re-check the command run.');

                if (array_key_exists('workflow_transition', $url_options))
                {
                    $this->cliEcho("  " . str_pad($response->applied_transition, 20), 'yellow');

                    if ($response->transition_ok)
                    {
                        $this->cliEcho('OK!', 'green');
                    }
                    else
                    {
                        $this->cliEcho('Failed!', 'red', 'bold');
                        if (isset($response->message))
                        {
                            $this->cliEcho("\n");
                            $this->cliEcho("  " . $response->message, 'red');
                        }
                    }

                    $this->cliEcho("\n");
                }
                else
                {
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
                }
            }
            else
            {
                $this->cliEcho('No fields to update, and no transition specified!', 'red', 'bold');
            }

            $this->cliEcho("\n");

        }

    }
