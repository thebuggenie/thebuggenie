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
		const KEY_MAIN_MENU_LINKS = '_mainmenu_links';
		const KEY_TBG_FACTORY = 'TBGFactory_cache';
		const KEY_I18N = '_i18n_';
		const KEY_TEXTPARSER_ISSUE_REGEX = 'TBGTextParser::getIssueRegex';
		
		/**
		* Cache types APC, filesystem (default)
		*/
		const TYPE_APC = 'apc';
		const TYPE_FILE = 'file';
		
		protected static $_enabled = true;

		protected static $_logging = false;
		
		/**
		* Cache type [apc|file].
		* If APC is present, it will be automatically set to APC [apc].
		* If no opcache present, it will fall back to caching into filesystem [file]
		*/
		protected static $_type;

		/**
		* container holding already loaded classes from filesystem so each cached file is loaded only once and later served from memory
		*/
		protected static $loaded = array();
		
		protected static function getScopedKeyIfAppliccable($key, $prepend_scope)
		{
			return ($prepend_scope) ? "{$key}." . TBGContext::getScope()->getID() : $key;
		}

		public static function get($key, $prepend_scope = true)
		{
			if (!self::isEnabled()) return null;

			$success = false;

			switch (self::$_type)
			{
				case self::TYPE_APC:
					$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
					$var = apc_fetch($key, $success);
					break;
				case self::TYPE_FILE:
				default:
					$var = self::fileGet($key, $prepend_scope);
					$success = !empty($var);

			}
			return ($success) ? $var : null;
		}

		public static function has($key, $prepend_scope = true)
		{
			if (!self::isEnabled()) return false;

			$success = false;
			
			switch (self::$_type)
			{
				case self::TYPE_APC:
					$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
					apc_fetch($key, $success);
					break;
				case self::TYPE_FILE:
				default:
					$success = self::fileHas($key, $prepend_scope);
			}

			return $success;
		}
		
		public static function add($key, $value, $prepend_scope = true)
		{
			if (!self::isEnabled()) return false;

			
			switch (self::$_type)
			{
				case self::TYPE_APC:
					$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
					apc_store($key, $value);
					break;
				case self::TYPE_FILE:
				default:
					self::fileAdd($key, $value, $prepend_scope);
			}
			
			if (self::$_logging) TBGLogging::log('Caching value for key "' . $key . '"', 'cache');
			return true;
		}
		
		public static function delete($key, $prepend_scope = true)
		{
			if (!self::isEnabled()) return false;
			
			switch (self::$_type)
			{
				case self::TYPE_APC:
					$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
					apc_delete($key);
					break;
				case self::TYPE_FILE:
				default:
					self::fileDelete($key, $prepend_scope);
			}
		}
		
		/**
		* Some keys have insuitable format for filepath, we must purify keys
		* To prevent from accidentally filtering into two the same keys, we must also add hash calculated from original key
		* @param string $key
		*/
		protected static function getKeyHash($key){
			$key = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $key);
			return $key.'-'.substr(md5(serialize($key)), 0, 5);
		}

		public static function fileHas($key, $prepend_scope = true)
		{
			if (!self::isEnabled()) return false;

			$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
			$filename = self::_getFilenameForKey($key);
			return (array_key_exists($key, self::$loaded) || file_exists($filename));
		}

		
		public static function fileAdd($key, $value, $prepend_scope = true)
		{
			if (!self::isEnabled()) return false;

			$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
			$filename = self::_getFilenameForKey($key);
			$new = !file_exists($filename);
			file_put_contents($filename, serialize($value));
			if ($new) chmod($filename, 0666);
			self::$loaded[$key] = $value;
		}
		
		public static function fileDelete($key, $prepend_scope = true)
		{
			if (!self::isEnabled()) return false;

			$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
			$filename = self::_getFilenameForKey($key);
			if (file_exists($filename)) unlink($filename);
			unset(self::$loaded[$key]);
		}
		
		public static function fileGet($key, $prepend_scope = true)
		{
			if (!self::isEnabled()) return null;

			$key = self::getScopedKeyIfAppliccable($key, $prepend_scope);
			if (!self::fileHas($key, $prepend_scope)) return null;
			
			if (array_key_exists($key, self::$loaded)){
				return self::$loaded[$key];
			}

			$filename = self::_getFilenameForKey($key);
			if (!file_exists($filename)) throw new Exception("$filename - $key");
			self::$loaded[$key] = unserialize(file_get_contents($filename));
			return self::$loaded[$key];
		}
		
		protected static function _getFilenameForKey($key)
		{
			$key = self::getKeyHash($key);
			return THEBUGGENIE_CORE_PATH . 'cache' . DS . $key . '.cache';
		}

		public static function checkEnabled()
		{
			if (self::$_enabled){
				self::$_type = function_exists('apc_add') ? self::TYPE_APC : self::TYPE_FILE;
			}
		}

		public static function getCacheType()
		{
			return self::$_type;
		}

		public static function isEnabled()
		{
			return self::$_enabled;
		}

		public static function clearCacheKeys($keys)
		{
			foreach ($keys as $key)
			{
				self::delete($key);
				self::fileDelete($key);
			}
		}

	}
	

