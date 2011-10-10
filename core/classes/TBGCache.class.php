<?php

	/**
	 * Cache class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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

		const KEY_SCOPES = '_scopes';
		const KEY_PREMODULES_ROUTES_CACHE = '_routes';
		const KEY_POSTMODULES_ROUTES_CACHE = '_routes_postmodules';
		const KEY_PERMISSIONS_CACHE = '_permissions';
		const KEY_USERSTATES_CACHE = 'TBGUserstate::getAll';
		const KEY_MODULE_PATHS = '_module_paths';
		const KEY_MODULES = '_modules';
		const KEY_SETTINGS = '_settings';
		const KEY_TEXTPARSER_ISSUE_REGEX = 'TBGTextParser::getIssueRegex';
		
		protected static $_enabled = false;
		protected static $_filecache_enabled = false;
		
		public static function get($key)
		{
			if (!self::isEnabled()) return null;
			$success = false;
			$var = apc_fetch($key, $success);
			return ($success) ? $var : null;
		}

		public static function has($key)
		{
			if (!self::isEnabled()) return false;
			$success = false;
			apc_fetch($key, $success);
			return $success;
		}
		
		public static function add($key, $value)
		{
			if (!self::isEnabled())
			{
				TBGLogging::log('Key "' . $key . '" not cached', 'cache');
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
			$filename = THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
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
			$filename = THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
			file_put_contents($filename, serialize($value));
		}
		
		public static function fileDelete($key)
		{
			if (!self::$_filecache_enabled) return null;
			$filename = THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
			unlink($filename);
		}
		
		public static function isEnabled()
		{
			if (self::$_enabled)
			{
				self::$_enabled = function_exists('apc_add');
			}
			return self::$_enabled;
		}
	}
