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
	 class TBGCliCommand
	 {
		 
		public function getInputConfirmation()
		{

		}
		 
		public function getInput()
		{

		}

		public static function cli_echo($text, $color = 'white', $style = null)
		{
			$fg_colors = array('black' => 29, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37);
			$op_format = array('bold' => 1, 'underline' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8);

			$return_text = "\033[" . $fg_colors[$color];
			$return_text .= ($style !== null) ? ";" . $op_format[$style] : '';
			$return_text .= "m" . $text . "\033[0m";

			echo $return_text;
		}

		public function cliEcho($text, $color = 'white', $style = null)
		{
			self::cli_echo($text, $color, $style);
		}

	}