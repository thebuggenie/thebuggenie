<?php

	/**
	 * Cache class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Cache class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGCache
	{
		protected static $_enabled = false;
		
		public static function get($key)
		{
			if (!self::isEnabled()) return null;
			$success = false;
			$var = apc_fetch($key, $success);
			return ($success) ? $var : null;
		}
		
		public static function add($key, $value)
		{
			if (!self::isEnabled())
			{
				TBGLogging::log('Can not cache value for key "' . $key . '"', 'cache');
				return false;
			}
			apc_store($key, $value);
			TBGLogging::log('Caching value for key "' . $key . '"', 'cache');
			return true;
		}
		
		public static function delete($key)
		{
			if (!self::isEnabled()) return null;
			apc_delete($key);
		}
		
		public static function isEnabled()
		{
			if (self::$_enabled === null || self::$_enabled == true)
			{
				self::$_enabled = function_exists('apc_add');
			}
			return self::$_enabled;
		}
	}
