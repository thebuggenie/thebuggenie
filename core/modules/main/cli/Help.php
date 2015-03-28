<?php

    namespace thebuggenie\core\modules\main\cli;

    /**
     * CLI command class, main -> help
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> help
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Help extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'help';
            $this->_description = "Prints out help information";
            $this->addOptionalArgument('command', "Show help for the command specified");
        }

        public function do_execute()
        {
            $this->cliEcho("The Bug Genie CLI help\n", 'white', 'bold');

            if ($this->hasProvidedArgument(2))
            {
                $module_command = explode(':', $this->getProvidedArgument(2));
                $module_name = (count($module_command) == 2) ? $module_command[0] : 'main';
                $command = (count($module_command) == 2) ? $module_command[1] : $module_command[0];

                $commands = self::getAvailableCommands();

                if (array_key_exists($module_name, $commands) && array_key_exists($command, $commands[$module_name]))
                {
                    $this->cliEcho("\n");
                    $class = $commands[$module_name][$command];
                    $this->cliEcho("Usage: ", 'white', 'bold');
                    $this->cliEcho(\thebuggenie\core\framework\cli\Command::getCommandLineName() . " ");
                    if ($module_name != 'main')
                    {
                        $this->cliEcho($module_name.':', 'green', 'bold');
                    }
                    $this->cliEcho($class->getCommandName() . " ", 'green', 'bold');

                    $hasArguments = false;
                    foreach ($class->getRequiredArguments() as $argument => $description)
                    {
                        $this->cliEcho($argument . ' ', 'magenta', 'bold');
                        $hasArguments = true;
                    }
                    foreach ($class->getOptionalArguments() as $argument => $description)
                    {
                        $this->cliEcho('[' . $argument . '] ', 'magenta');
                        $hasArguments = true;
                    }
                    $this->cliEcho("\n");
                    $this->cliEcho($class->getDescription(), 'white', 'bold');
                    $this->cliEcho("\n\n");

                    if ($hasArguments)
                    {
                        $this->cliEcho("Argument descriptions:\n", 'white', 'bold');
                        foreach ($class->getRequiredArguments() as $argument => $description)
                        {
                            $this->cliEcho("  {$argument}", 'magenta', 'bold');
                            if ($description != '')
                            {
                                $this->cliEcho(" - {$description}");
                            }
                            else
                            {
                                $this->cliEcho(" - No description provided");
                            }
                            $this->cliEcho("\n");
                        }
                        foreach ($class->getOptionalArguments() as $argument => $description)
                        {
                            $this->cliEcho("  [{$argument}]", 'magenta');
                            if ($description != '')
                            {
                                $this->cliEcho(" - {$description}");
                            }
                            else
                            {
                                $this->cliEcho(" - No description provided");
                            }
                            $this->cliEcho("\n");
                        }
                        $this->cliEcho("\n");
                        $this->cliEcho("Parameters must be passed either in the order described above\nor in the following format:\n");
                        $this->cliEcho("--parameter_name=value", 'magenta');
                        $this->cliEcho("\n\n");
                    }
                }
                else
                {
                    $this->cliEcho("\n");
                    $this->cliEcho("Unknown command\n", 'red', 'bold');
                    $this->cliEcho("Type " . \thebuggenie\core\framework\cli\Command::getCommandLineName() . ' ', 'white', 'bold');
                    $this->cliEcho('help', 'green', 'bold');
                    $this->cliEcho(" for more information about the cli tool.\n\n");
                }
            }
            else
            {
                $this->cliEcho("Below is a list of available commands:\n");
                $this->cliEcho("Type ");
                $this->cliEcho(\thebuggenie\core\framework\cli\Command::getCommandLineName() . ' ', 'white', 'bold');
                $this->cliEcho('help', 'green', 'bold');
                $this->cliEcho(' command', 'magenta');
                $this->cliEcho(" for more information about a specific command.\n\n");
                $commands = $this->getAvailableCommands();

                foreach ($commands as $module_name => $module_commands)
                {
                    if ($module_name != 'main' && count($module_commands) > 0)
                    {
                        $this->cliEcho("\n{$module_name}:\n", 'green', 'bold');
                    }
                    ksort($module_commands, SORT_STRING);
                    foreach ($module_commands as $command_name => $command)
                    {
                        if ($module_name != 'main') $this->cliEcho("  ");
                        $this->cliEcho("{$command_name}", 'green', 'bold');
                        $this->cliEcho(" - {$command->getDescription()}\n");
                    }

                    if (count($commands) > 1 && $module_name == 'remote')
                    {
                        $this->cliEcho("\nModule commands, use ");
                        $this->cliEcho("module_name:command_name", 'green');
                        $this->cliEcho(" to run:");
                    }
                }

                $this->cliEcho("\n");
            }
        }

    }
