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
		
		const KEY_PREMODULES_ROUTES_CACHE = '_routes';
		const KEY_POSTMODULES_ROUTES_CACHE = '_routes_postmodules';
		const KEY_PERMISSIONS_CACHE = '_permissions';
		
		protected static $_enabled = false;
		protected static $_filecache_enabled = false;
		
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
		
		public static function fileGet($key)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = THEBUGGENIE_PATH . 'core' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache';
			if (!file_exists($filename))
			{
				return null;
			}
			
			$value = unserialize(file_get_contents($filename));
			return $value;
		}
		
		public static function fileAdd($key, $value)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = THEBUGGENIE_PATH . 'core' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache';
			file_put_contents($filename, serialize(self::$_permissions));
		}
		
		public static function fileDelete($key)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = THEBUGGENIE_PATH . 'core' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $key . '.cache';
			unlink($filename);
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
