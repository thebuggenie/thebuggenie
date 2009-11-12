<?php

	/**
	 * Logging class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Logging class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class BUGSlogging
	{
		const LEVEL_INFO = 1;
		const LEVEL_NOTICE = 5;
		const LEVEL_WARNING = 10;
		const LEVEL_WARNING_RISK = 15;
		const LEVEL_FATAL = 20;
		
		protected static $_logging = true;
		
		protected static $_logfile;
		//protected static $_logfile = '/var/www/dev/thebuggenie/b2.log';
		
		protected static $_logonajaxcalls = true;
		
		protected static $_entries = array();

		protected static $_loglevel = 10;
		
		public static function log($message, $category = 'main', $level = 1)
		{
			BUGScontext::ping();
			if (!self::$_logging) return false;
			if (self::$_loglevel > $level) return false;
			if (self::$_logonajaxcalls || !(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == '')))
			{
				if (self::$_logfile !== null)
				{
					file_put_contents(self::$_logfile, strtoupper(self::getLevelName($level)) . " [{$category}] {$message}\n", FILE_APPEND);
				}
				$time_msg = (($load_time = BUGScontext::getLoadtime()) >= 1) ? round($load_time, 2) . ' seconds' : round(($load_time * 1000), 3) . ' ms';
				
				self::$_entries[] = array('category' => $category, 'time' => $time_msg, 'message' => $message, 'level' => $level);
			}
		}
		
		public static function getLevelName($level)
		{
			switch ($level)
			{
				case self::LEVEL_INFO:
					return 'info';
				case self::LEVEL_NOTICE:
					return 'notice';
				case self::LEVEL_WARNING:
					return 'warning';
				case self::LEVEL_WARNING_RISK:
					return 'risk';
				case self::LEVEL_FATAL:
					return 'fatal';
			}
		}
		
		public static function getCategoryColor($category)
		{
			switch ($category)
			{
				case 'main':
					return "55C";
				case 'B2DB':
					return "33B";
				case 'routing':
					return "5C5";
				case 'i18n':
					return "A83";
				case 'search':
					return "2FA";
				case 'publish':
					return "A79";
				default: 
					return "999";
			}
		}
		
		public static function getEntries()
		{
			return self::$_entries;
		}
		
		public static function isEnabled()
		{
			return self::$_logging;
		}
		
	}
	