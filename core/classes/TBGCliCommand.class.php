<?php

	/**
	 * CLI command class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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

		protected $_command_name = null;

		protected $_required_arguments = array();

		protected $_optional_arguments = array();

		abstract function do_execute();

		abstract function getDescription();

		final function __construct()
		{
			$this->_setup();
		}

		final function execute()
		{
			$this->_processArguments();
			$this->do_execute();
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

					$argument_parts = explode('=', $argument, 1);
					if (count($argument_parts) == 2)
					{
						self::$_provided_arguments[substr($argument_parts[0], 2)] = $argument_parts[1];
					}
				}
			}
		}

		protected function _setup() { }

		protected function _processArguments()
		{
			$cc = 1;
			foreach ($this->_required_arguments as $key => $argument)
			{
				$cc++;
				if ($this->hasProvidedArgument($key)) continue;
				if ($this->hasProvidedArgument($cc)) continue;

				throw new Exception('Please include all required arguments');
			}
		}

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

		protected function addRequiredParameter($parameter, $description = null)
		{
			$this->_required_arguments[$parameter] = $description;
		}

		protected function addOptionalParameter($parameter, $description = null)
		{
			$this->_optional_arguments[$parameter] = $description;
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
			return (bool) (strtolower(trim($retval)) == 'yes');
		}

		public function askToAccept()
		{
			return $this->getInputConfirmation();
		}

		public function askToDecline()
		{
			$retval = $this->_getCliInput();
			return !(bool) (strtolower(trim($retval)) == 'no');
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
			$fg_colors = array('black' => 29, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37);
			$op_format = array('bold' => 1, 'underline' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8);

			$return_text = "\033[" . $fg_colors[$color];
			$return_text .= ($style !== null && array_key_exists($style, $op_format)) ? ";" . $op_format[$style] : '';
			$return_text .= "m" . $text . "\033[0m";

			echo $return_text;
		}

		public function cliEcho($text, $color = 'white', $style = null)
		{
			self::cli_echo($text, $color, $style);
		}

	}