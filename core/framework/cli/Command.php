<?php

    namespace thebuggenie\core\framework\cli;

    /**
     * CLI command class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class
     *
     * @package thebuggenie
     * @subpackage core
     */
    abstract class Command
    {

        protected static $_available_commands = null;

        protected static $_provided_arguments = null;

        protected static $_named_arguments = array();

        protected $_command_name = null;

        protected $_description = '';

        protected $_required_arguments = array();

        protected $_optional_arguments = array();
        
        protected $_module = null;
        
        protected $_scoped = false;

        abstract protected function do_execute();

        final public function __construct($module = null)
        {
            $this->_module = $module;
            $this->_setup();
        }

        final public function execute()
        {
            $this->_processArguments();
            $this->_prepare();
            $this->do_execute();
        }
        
        /**
         * Return the associated module for this command if any
         * 
         * @return \thebuggenie\core\entities\Module 
         */
        final protected function getModule()
        {
            return $this->_module;
        }

        public function getDescription()
        {
            return $this->_description;
        }
        
        protected function setScoped($val = true)
        {
            $this->_scoped = $val;
            if ($this->_scoped)
            {
                $this->addOptionalArgument('scope', 'The scope to work with (uses default scope if not provided)');
            }
        }

        public static function setAvailableCommands($available_commands)
        {
            if (self::$_available_commands !== null)
            {
                throw new \Exception('You cannot change available commands');
            }
            self::$_available_commands = $available_commands;
        }

        public static function getAvailableCommands()
        {
            return self::$_available_commands;
        }

        public static function processArguments()
        {
            if (self::$_provided_arguments == null)
            {
                self::$_provided_arguments = array();
                foreach ($GLOBALS['argv'] as $cc => $argument)
                {
                    self::$_provided_arguments[$cc] = $argument;

                    $argument_parts = explode('=', $argument, 2);
                    if (count($argument_parts) == 2)
                    {
                        $key = mb_substr($argument_parts[0], 2);
                        self::$_provided_arguments[$key] = $argument_parts[1];
                        if (!is_numeric($key))
                        {
                            self::$_named_arguments[$key] = $argument_parts[1];
                        }
                    }
                }
            }
        }

        protected function _setup() { }

        final protected function _processArguments()
        {
            $cc = 1;
            foreach ($this->_required_arguments as $key => $argument)
            {
                $cc++;
                if ($this->hasProvidedArgument($key)) continue;
                if ($this->hasProvidedArgument($cc))
                {
                    if (mb_substr(self::$_provided_arguments[$cc], 0, 2) == '--' && mb_substr(self::$_provided_arguments[$cc], 2, mb_strpos(self::$_provided_arguments[$cc], '=') - 1) != $key) continue;
                    self::$_provided_arguments[$key] = self::$_provided_arguments[$cc];
                    if (!is_numeric($key))
                    {
                        self::$_named_arguments[$key] = self::$_provided_arguments[$cc];
                    }
                    continue;
                }
            }
            foreach (self::$_provided_arguments as $key => $value)
            {
                $this->$key = $value;
            }
            $diff = array_diff(array_keys($this->_required_arguments), array_keys(self::$_named_arguments));
            if (count($diff))
            {
                throw new \Exception('Please include all required arguments. Missing arguments: '.join(', ', $diff));
            }
            foreach ($this->_optional_arguments as $key => $argument)
            {
                $cc++;
                if ($this->hasProvidedArgument($key)) continue;
                if ($this->hasProvidedArgument($cc))
                {
                    if (mb_substr(self::$_provided_arguments[$cc], 0, 2) == '--' && mb_substr(self::$_provided_arguments[$cc], 2, mb_strpos(self::$_provided_arguments[$cc], '=') - 1) != $key) continue;
                    self::$_provided_arguments[$key] = self::$_provided_arguments[$cc];
                    if (!is_numeric($key))
                    {
                        self::$_named_arguments[$key] = self::$_provided_arguments[$cc];
                    }
                    continue;
                }
            }
            if ($this->_scoped && array_key_exists('scope', self::$_named_arguments))
            {
                $scope = \thebuggenie\core\entities\tables\Scopes::getTable()->selectById(self::$_named_arguments['scope']);
                $this->cliEcho("Using scope ".$scope->getID()."\n");
                \thebuggenie\core\framework\Context::setScope($scope);
            }
        }
        
        protected function _prepare() { }

        public function getCommandName()
        {
            return $this->_command_name;
        }

        public function getProvidedArgument($key, $default_value = null)
        {
            return (array_key_exists($key, self::$_provided_arguments)) ? self::$_provided_arguments[$key] : $default_value;
        }

        public function hasProvidedArgument($key)
        {
            return array_key_exists($key, self::$_provided_arguments);
        }

        protected function addRequiredArgument($argument, $description = null)
        {
            $this->_required_arguments[$argument] = $description;
        }

        public function getRequiredArguments()
        {
            return $this->_required_arguments;
        }

        protected function addOptionalArgument($argument, $description = null)
        {
            $this->_optional_arguments[$argument] = $description;
        }

        public function getOptionalArguments()
        {
            return $this->_optional_arguments;
        }

        public function getProvidedArguments()
        {
            return self::$_provided_arguments;
        }

        public function getNamedArguments()
        {
            return self::$_named_arguments;
        }

        public static function getCommandLineName()
        {
            return $GLOBALS['argv'][0];
        }

        public function getCommandAliases()
        {
            return array();
        }

        protected function _getCliInput()
        {
            return trim(fgets(STDIN));
        }

        public function getInputConfirmation()
        {
            $retval = $this->_getCliInput();
            return (bool) (mb_strtolower(trim($retval)) == 'yes');
        }

        public function askToAccept()
        {
            return $this->getInputConfirmation();
        }

        public function askToDecline()
        {
            $retval = $this->_getCliInput();
            return !(bool) (mb_strtolower(trim($retval)) == 'no');
        }

        public function getInput($default = '')
        {
            $retval = $this->_getCliInput();
            return ($retval == '') ? $default : $retval;
        }

        public function pressEnterToContinue()
        {
            fgets(STDIN);
        }

        static public function getOS() {
            switch (true) {
                case stristr(PHP_OS, 'DAR'): return 'OS_OSX';
                case stristr(PHP_OS, 'WIN'): return 'OS_WIN';
                case stristr(PHP_OS, 'LINUX'): return 'OS_LINUX';
                default : return 'OS_UNKNOWN';
            }
        }

        public static function cli_echo($text, $color = 'white', $style = null)
        {
            if (self::getOS() === 'OS_WIN' || self::getOS() === 'OS_UNKNOWN')
            {
                $return_text = $text;
            }
            else
            {
                $fg_colors = array('black' => 29, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37);
                $op_format = array('bold' => 1, 'underline' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8);

                $return_text = "\033[" . $fg_colors[$color];
                $return_text .= ($style !== null && array_key_exists($style, $op_format)) ? ";" . $op_format[$style] : '';
                $return_text .= "m" . $text . "\033[0m";
            }

            echo $return_text;
        }

        public function cliEcho($text, $color = 'white', $style = null)
        {
            self::cli_echo($text, $color, $style);
        }

        public static function cliError($title, $exception)
        {
            $trace_elements = null;
            if ($exception instanceof \Exception)
            {
                if ($exception instanceof \thebuggenie\core\framework\exceptions\ActionNotFoundException)
                {
                    self::cli_echo("Could not find the specified action\n", 'white', 'bold');
                }
                elseif ($exception instanceof \thebuggenie\core\framework\exceptions\TemplateNotFoundException)
                {
                    self::cli_echo("Could not find the template file for the specified action\n", 'white', 'bold');
                }
                elseif ($exception instanceof \b2db\Exception)
                {
                    self::cli_echo("An exception was thrown in the B2DB framework\n", 'white', 'bold');
                }
                else
                {
                    self::cli_echo("An unhandled exception occurred:\n", 'white', 'bold');
                }
                echo self::cli_echo($exception->getMessage(), 'red', 'bold') . "\n";
                echo "\n";
                self::cli_echo('Stack trace') . ":\n";
                $trace_elements = $exception->getTrace();
            }
            else
            {
                if ($exception['code'] == 8)
                {
                    self::cli_echo('The following notice has stopped further execution:', 'white', 'bold');
                }
                else
                {
                    self::cli_echo('The following error occured:', 'white', 'bold');
                }
                echo "\n";
                echo "\n";
                self::cli_echo($title, 'red', 'bold');
                echo "\n";
                self::cli_echo("occured in\n");
                self::cli_echo($exception['file'] . ', line ' . $exception['line'], 'blue', 'bold');
                echo "\n";
                echo "\n";
                self::cli_echo("Backtrace:\n", 'white', 'bold');
                $trace_elements = debug_backtrace();
            }
            foreach ($trace_elements as $trace_element)
            {
                if (array_key_exists('class', $trace_element))
                {
                    if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'thebuggenie\core\framework\Context' && array_key_exists('function', $trace_element) && in_array($trace_element['function'], array('errorHandler', 'cliError')))
                        continue;
                    self::cli_echo($trace_element['class'] . $trace_element['type'] . $trace_element['function'] . '()');
                }
                elseif (array_key_exists('function', $trace_element))
                {
                    self::cli_echo($trace_element['function'] . '()');
                }
                else
                {
                    self::cli_echo('unknown function');
                }
                echo "\n";
                if (array_key_exists('file', $trace_element))
                {
                    self::cli_echo($trace_element['file'] . ', line ' . $trace_element['line'], 'blue', 'bold');
                }
                else
                {
                    self::cli_echo('unknown file', 'red', 'bold');
                }
                echo "\n";
            }
            if (class_exists('\\b2db\\Core'))
            {
                echo "\n";
                $sqlhits = \b2db\Core::getSQLHits();
                if (count($sqlhits))
                {
                    self::cli_echo("SQL queries:\n", 'white', 'bold');
                    try
                    {
                        $cc = 1;
                        foreach ($sqlhits as $details)
                        {
                            self::cli_echo("(" . $cc++ . ") [");
                            $str = ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms';
                            self::cli_echo($str);
                            self::cli_echo("] from ");
                            self::cli_echo($details['filename'], 'blue');
                            self::cli_echo(", line ");
                            self::cli_echo($details['line'], 'white', 'bold');
                            self::cli_echo(":\n");
                            self::cli_echo("{$details['sql']}\n");
                        }
                        echo "\n";
                    }
                    catch (\Exception $e)
                    {
                        self::cli_echo("Could not generate query list (there may be no database connection)", "red", "bold");
                    }
                }
            }
            echo "\n";
            
        }

    }