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
		const KEY_TEXTPARSER_ISSUE_REGEX = 'TBGTextParser::getIssueRegex';
		
		protected static $_enabled = false;

		protected static $_logging = false;
		
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
				if (self::$_logging) TBGLogging::log('Key "' . $key . '" not available when cache is disabled', 'cache');
				return false;
			}
			apc_store($key, $value);
			if (self::$_logging) TBGLogging::log('Caching value for key "' . $key . '"', 'cache');
			return true;
		}
		
		public static function delete($key)
		{
			if (!self::isEnabled()) return null;
			apc_delete($key);
		}
		
		protected static function _getFilenameForKey($key)
		{
			return THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
		}

		public static function fileHas($key)
		{
			if (!self::isEnabled()) return false;
			$filename = self::_getFilenameForKey($key);
			return file_exists($filename);
		}

		public static function fileGet($key)
		{
			if (!self::isEnabled()) return null;
			if (!self::fileHas($key)) return null;
			$filename = self::_getFilenameForKey($key);
			$value = unserialize(file_get_contents($filename));
			return $value;
		}
		
		public static function fileAdd($key, $value)
		{
			$filename = self::_getFilenameForKey($key);
			file_put_contents($filename, serialize($value));
		}
		
		public static function fileDelete($key)
		{
			$filename = self::_getFilenameForKey($key);
			unlink($filename);
		}
		
		public static function checkEnabled()
		{
			if (self::$_enabled) self::$_enabled = function_exists('apc_add');
		}

		public static function isEnabled()
		{
			return self::$_enabled;
		}
	}
