<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> list_transitions
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, remote -> list_transitions
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ListWorkflowTransitions extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_transitions';
            $this->_description = "Show available workflow transitions for an issue";
            $this->addRequiredArgument('project_key', 'The project key for the project containing the issue you want to see transitions for');
            $this->addRequiredArgument('issue_number', 'The issue number of the issue to show transitions for');
            $this->addOptionalArgument('transition', 'The name of a transition to show more details about');
            parent::_setup();
        }

        public function do_execute()
        {
            if ($transition = $this->getProvidedArgument('transition'))
            {
                $this->cliEcho('Listing details for transition action ');
                $this->cliEcho($this->getProvidedArgument('transition'), 'yellow', 'bold');
                $this->cliEcho(' on ');
                $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
                $this->cliEcho("\n");
            }
            else
            {
                $this->cliEcho('Listing available transitions for issue ');
                $print_issue_number = $this->getProvidedArgument('issue_number');
                
                if (is_numeric($print_issue_number))
                    $print_issue_number = '#' . $print_issue_number;

                $this->cliEcho($print_issue_number, 'yellow');
                $this->cliEcho(' on ');
                $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
                $this->cliEcho("\n");
                $this->cliEcho("Transitions shown in ");
                $this->cliEcho("yellow", 'yellow', 'bold');
                $this->cliEcho(" requires you to pass parameters when applied to an issue\n");
            }

            $url_options = array('project_key' => $this->project_key, 'issue_no' => $this->issue_number, 'format' => 'json');
            $response = $this->getRemoteResponse($this->getRemoteURL('project_list_workflowtransitions', $url_options));
            $this->cliEcho("\n");

            if (!$transition)
            {
                $this->cliEcho("Available transitions:\n", 'white', 'bold');

                foreach ($response as $transition)
                {
                    $color = ($transition->template) ? 'yellow' : 'cyan';
                    $this->cliEcho($transition->name . ": ", $color, 'bold');
                    $this->cliEcho($transition->description . "\n");
                }
            }
            else
            {
                $key = str_replace(' ', '', mb_strtolower($transition));
                foreach ($response as $available_transition)
                {
                    if (mb_strpos(str_replace(' ', '', mb_strtolower($available_transition->name)), $key) !== false)
                    {
                        $color = ($available_transition->template) ? 'yellow' : 'cyan';
                        $this->cliEcho($available_transition->name . ": ", $color, 'bold');
                        $this->cliEcho($available_transition->description . "\n");
                        if ($available_transition->template)
                        {
                            $this->cliEcho("\n");
                            $this->cliEcho("This transition requires the following data to be passed:\n", 'white', 'bold');
                            foreach ($available_transition->post_validation as $validation_action)
                            {
                                switch ($validation_action->name)
                                {
                                    case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID:
                                        $print_name = "Status";
                                        break;
                                    case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID:
                                        $print_name = "Resolution";
                                        break;
                                    case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID:
                                        $print_name = "Priority";
                                        break;
                                    case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID:
                                        $print_name = "Reproducability";
                                        break;
                                    default:
                                        $print_name = $validation_action->name;
                                }
                                $this->cliEcho($print_name, 'green', 'bold');
                                $this->cliEcho(" must be one of these values:\n");
                                $this->cliEcho($validation_action->values, 'yellow');
                                $this->cliEcho("\n");
                            }
                        }
                        else
                        {
                            $this->cliEcho("This transition does not require any specific data passed to it.\n");
                        }
                        break;
                    }
                }
            }

            $this->cliEcho("\n");

        }

    }
