<?php

	/**
	 * CLI command class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	abstract class TBGCliCommand
	{

		protected static $_available_commands = null;

		protected static $_provided_arguments = null;

		protected static $_named_arguments = array();

		protected $_command_name = null;

		protected $_description = '';

		protected $_required_arguments = array();

		protected $_optional_arguments = array();
		
		protected $_module = null;

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
		 * @return TBGModule 
		 */
		final protected function getModule()
		{
			return $this->_module;
		}

		public function getDescription()
		{
			return $this->_description;
		}

		public static function setAvailableCommands($available_commands)
		{
			if (self::$_available_commands !== null)
			{
				throw new Exception('You cannot change available commands');
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
				throw new Exception('Please include all required arguments. Missing arguments: '.join(', ', $diff));
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

		public static function cli_echo($text, $color = 'white', $style = null)
		{
			if (PHP_OS == 'Windows')
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

	}