<?php

    namespace thebuggenie\core\modules\remote\cli;

    /**
     * CLI command class, remote -> list_fieldvalues
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, remote -> list_fieldvalues
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ListFieldvalues extends \thebuggenie\core\framework\cli\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'list_fieldvalues';
            $this->_description = "Query a remote server for a list of available field values";
            $this->addRequiredArgument('field_key', 'The key for an issue field to show available values for');
            $this->addOptionalArgument('project_key', "The key for a project to retrieve values for in case of project specific values (e.g. milestone)");
            parent::_setup();
        }

        public function do_execute()
        {
            $this->cliEcho('Querying ');
            $this->cliEcho($this->_getCurrentRemoteServer(), 'white', 'bold');
            $this->cliEcho(" for valid use of issue field ");
            $this->cliEcho($this->field_key, 'yellow');
            $this->cliEcho("\n\n");

            $options = array('field_key' => $this->field_key, 'format' => 'json');
            if ($this->hasProvidedArgument('project_key'))
            {
                $options['project_key'] = $this->getProvidedArgument('project_key');
            }
            $response = $this->getRemoteResponse($this->getRemoteURL('remote_list_fieldvalues', $options));

            if (!empty($response))
            {
                $this->cliEcho($this->field_key, 'yellow', 'bold');
                $this->cliEcho("\n");
                $this->cliEcho('Type: ', 'white', 'bold');
                $this->cliEcho($response->description."\n");
                switch ($response->type)
                {
                    case 'choice':
                        $this->cliEcho("Available values:\n", 'white', 'bold');
                        if (count($response->choices))
                        {
                            foreach ($response->choices as $value)
                            {
                                $this->cliEcho("  {$value}\n", 'yellow');
                            }
                        }
                        else
                        {
                            if ($this->field_key == 'milestone' && !$this->hasProvidedArgument('project_key'))
                            {
                                $this->cliEcho("  You need to specify a project key to retrieve milestone values\n", 'red');
                            }
                            else
                            {
                                $this->cliEcho("  There doesn't seem to be any available values\n", 'cyan');
                            }
                        }
                    case 'single_line_input':
                        break;
                    case 'time':
                        $this->cliEcho("You can enter any combination of weeks, days, hours,\n");
                        $this->cliEcho("minutes and/or points. Separate them with commas.\n\n");
                        $this->cliEcho("Examples:\n", 'white', 'bold');
                        $this->cliEcho("  2 days, 3 points\n", 'cyan');
                        $this->cliEcho("  1 week\n", 'cyan');
                        $this->cliEcho("  1 day, 2 hours\n", 'cyan');
                        break;
                    case 'select_user':
                        $this->cliEcho("Available values:\n", 'white', 'bold');
                        $this->cliEcho("  me\n", 'yellow');
                        $this->cliEcho("  none\n", 'yellow');
                        $this->cliEcho("  <username>\n", 'yellow');
                        $this->cliEcho("Where ");
                        $this->cliEcho("<username>", 'yellow');
                        $this->cliEcho(" is the username of any existing user.\n");
                        break;
                }
                $this->cliEcho("\n");
                $this->cliEcho("When using ");
                $this->cliEcho('remote:update_issue', 'green');
                $this->cliEcho(" to update an issue, pass any combination of a\n");
                $this->cliEcho("field key and a valid value as a parameter to update the issue details.\n");
                $this->cliEcho("The value is case-insensitive, and may also be written without spaces.\n");
                $this->cliEcho("\n");
                $this->cliEcho("For more information, check the documentation for ");
                $this->cliEcho('remote:update_issue', 'green');
                $this->cliEcho(".\n");

            }
            else
            {
                $this->cliEcho("This field doesn't seem right.\n\n");
            }
        }

    }
