<?php

	/**
	 * Settings class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Settings class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	final class TBGSettings
	{

		const ACCESS_READ = 1;
		const ACCESS_FULL = 2;
		
		const CONFIGURATION_SECTION_WORKFLOW = 1;
		const CONFIGURATION_SECTION_USERS = 2;
		const CONFIGURATION_SECTION_UPLOADS = 3;
		const CONFIGURATION_SECTION_ISSUEFIELDS = 4;
		const CONFIGURATION_SECTION_PERMISSIONS = 5;
		const CONFIGURATION_SECTION_ISSUETYPES = 6;
		const CONFIGURATION_SECTION_PROJECTS = 10;
		const CONFIGURATION_SECTION_SETTINGS = 12;
		const CONFIGURATION_SECTION_SCOPES = 14;
		const CONFIGURATION_SECTION_MODULES = 15;
		const CONFIGURATION_SECTION_IMPORT = 16;
		const CONFIGURATION_SECTION_AUTHENTICATION = 17;

		const APPEARANCE_HEADER_USE_THEME = 0;
		const APPEARANCE_HEADER_CUSTOM = 1;
		const APPEARANCE_HEADER_URL = 2;

		const FAVICON_DEFAULT = 0;
		const FAVICON_PUBLIC = 1;
		const FAVICON_CUSTOM_URL = 2;

		const SYNTAX_HIHGLIGHTING_FANCY_NUMBERS = 1;
		const SYNTAX_HIHGLIGHTING_NORMAL_NUMBERS = 2;
		const SYNTAX_HIHGLIGHTING_NO_NUMBERS = 3;

		const INFOBOX_PREFIX = 'hide_infobox_';

		const SETTING_ADMIN_GROUP = 'admingroup';
		const SETTING_ALLOW_REGISTRATION = 'allowreg';
		const SETTING_ALLOW_OPENID = 'allowopenid';
		const SETTING_ALLOW_USER_THEMES = 'userthemes';
		const SETTING_AWAYSTATE = 'awaystate';
		const SETTING_DEFAULT_CHARSET = 'charset';
		const SETTING_DEFAULT_LANGUAGE = 'language';
		const SETTING_DEFAULT_USER_IS_GUEST = 'defaultisguest';
		const SETTING_DEFAULT_USER_ID = 'defaultuserid';
		const SETTING_ENABLE_UPLOADS = 'enable_uploads';
		const SETTING_ENABLE_GRAVATARS = 'enable_gravatars';
		const SETTING_FAVICON_TYPE = 'icon_fav';
		const SETTING_FAVICON_URL = 'icon_fav_url';
		const SETTING_GUEST_GROUP = 'guestgroup';
		const SETTING_HEADER_ICON_TYPE = 'icon_header';
		const SETTING_HEADER_ICON_URL = 'icon_header_url';
		const SETTING_HEADER_LINK = 'header_link';
		const SETTING_IS_PERMISSIVE_MODE = 'permissive';
		const SETTING_IS_SINGLE_PROJECT_TRACKER = 'singleprojecttracker';
		const SETTING_KEEP_COMMENT_TRAIL_CLEAN = 'cleancomments';
		const SETTING_OFFLINESTATE = 'offlinestate';
		const SETTING_ONLINESTATE = 'onlinestate';
		const SETTING_PREVIEW_COMMENT_IMAGES = 'previewcommentimages';
		const SETTING_REGISTRATION_DOMAIN_WHITELIST = 'limit_registration';
		const SETTING_REQUIRE_LOGIN = 'requirelogin';
		const SETTING_RETURN_FROM_LOGIN = 'returnfromlogin';
		const SETTING_RETURN_FROM_LOGOUT = 'returnfromlogout';
		const SETTING_SALT = 'salt';
		const SETTING_SERVER_TIMEZONE = 'server_timezone';
		const SETTING_SHOW_PROJECTS_OVERVIEW = 'showprojectsoverview';
		const SETTING_SYNTAX_HIGHLIGHT_DEFAULT_LANGUAGE = 'highlight_default_lang';
		const SETTING_SYNTAX_HIGHLIGHT_DEFAULT_NUMBERING = 'highlight_default_numbering';
		const SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL = 'highlight_default_interval';
		const SETTING_TBG_NAME = 'b2_name';
		const SETTING_TBG_TAGLINE = 'b2_tagline';
		const SETTING_THEME_NAME = 'theme_name';
		const SETTING_UPLOAD_EXTENSIONS_LIST = 'upload_extensions_list';
		const SETTING_UPLOAD_LOCAL_PATH = 'upload_localpath';
		const SETTING_UPLOAD_MAX_FILE_SIZE = 'upload_max_file_size';
		const SETTING_UPLOAD_RESTRICTION_MODE = 'upload_restriction_mode';
		const SETTING_UPLOAD_STORAGE = 'upload_storage';
		const SETTING_USER_GROUP = 'defaultgroup';
		const SETTING_USER_TIMEZONE = 'timezone';
		const SETTING_USER_LANGUAGE = 'language';
		const SETTING_AUTH_BACKEND = 'auth_backend';
		const SETTING_MAINTENANCE_MODE = 'offline';
		const SETTING_MAINTENANCE_MESSAGE = 'offline_msg';
		const SETTING_ICONSET = 'iconset';

		static protected $_ver_mj = 3;
		static protected $_ver_mn = 2;
		static protected $_ver_rev = '0-pre';
		static protected $_ver_name = "Borg";
		static protected $_defaultscope = null;
		static protected $_settings = null;
		static protected $_loadedsettings = array();
	
		public static function forceSettingsReload()
		{
			self::$_settings = null;
		}
		
		public static function loadSettings($uid = 0)
		{
			TBGLogging::log("Loading settings");
			if (self::$_settings === null || ($uid > 0 && !array_key_exists($uid, self::$_loadedsettings)))
			{
				TBGLogging::log('Loading settings');
				if (self::$_settings === null)
					self::$_settings = array();
				
				if (!TBGContext::isInstallmode() && $uid == 0 && self::$_settings = TBGCache::get(TBGCache::KEY_SETTINGS))
				{
					TBGLogging::log('Using cached settings');
				}
				else
				{
					TBGLogging::log('Settings not cached or install mode enabled. Retrieving from database');
					if ($res = \b2db\Core::getTable('TBGSettingsTable')->getSettingsForScope(TBGContext::getScope()->getID(), $uid))
					{
						$cc = 0;
						while ($row = $res->getNextRow())
						{
							$cc++;
							self::$_settings[$row->get(TBGSettingsTable::MODULE)][$row->get(TBGSettingsTable::NAME)][$row->get(TBGSettingsTable::UID)] = $row->get(TBGSettingsTable::VALUE);
						}
						if ($cc == 0 && !TBGContext::isInstallmode() && $uid == 0)
						{
							TBGLogging::log('There were no settings stored in the database!', 'main', TBGLogging::LEVEL_FATAL);
							throw new TBGSettingsException('Could not retrieve settings from database (no settings stored)');
						}
					}
					elseif (!TBGContext::isInstallmode() && $uid == 0)
					{
						TBGLogging::log('Settings could not be retrieved from the database!', 'main', TBGLogging::LEVEL_FATAL);
						throw new TBGSettingsException('Could not retrieve settings from database');
					}
					self::$_loadedsettings[$uid] = true;
					TBGLogging::log('Retrieved');
					TBGCache::add(TBGCache::KEY_SETTINGS, self::$_settings);
				}
			}
			
			TBGLogging::log("...done");
		}

		public static function deleteModuleSettings($module_name, $scope)
		{
			if ($scope == TBGContext::getScope()->getID())
			{
				if (array_key_exists($module_name, self::$_settings))
				{
					unset(self::$_settings[$module_name]);
				}
			}
			TBGSettingsTable::getTable()->deleteModuleSettings($module_name, $scope);
		}
		
		public static function saveSetting($name, $value, $module = 'core', $scope = 0, $uid = 0)
		{
			if ($scope == 0 && $name != 'defaultscope' && $module == 'core')
			{
				if (($scope = TBGContext::getScope()) instanceof TBGScope)
				{
					$scope = $scope->getID();
				}
				else
				{
					throw new Exception('No scope loaded, cannot autoload it');
				}
			}

			\b2db\Core::getTable('TBGSettingsTable')->saveSetting($name, $module, $value, $uid, $scope);
			
			if ($scope != 0 && ((!TBGContext::getScope() instanceof TBGScope) || $scope == TBGContext::getScope()->getID()))
			{
				self::$_settings[$module][$name][$uid] = $value;
			}
			TBGCache::delete(TBGCache::KEY_SETTINGS);
		}
		
		public static function set($name, $value, $uid = 0, $module = 'core')
		{
			self::$_settings[$module][$name][$uid] = $value;
		}
	
		public static function get($name, $module = 'core', $scope = null, $uid = 0)
		{
			if (TBGContext::isInstallmode() && !TBGContext::getScope() instanceof TBGScope)
			{
				return null;
			}
			if ($scope instanceof TBGScope)
			{
				$scope = $scope->getID();
			}
			if (!TBGContext::getScope() instanceof TBGScope)
			{
				throw new Exception('The Bug Genie is not installed correctly');
			}
			if ($scope != TBGContext::getScope()->getID() && $scope !== null)
			{
				$setting = self::_loadSetting($name, $module, $scope);
				return $setting[$uid];
			}
			if (self::$_settings === null)
			{
				self::loadSettings();
			}
			if ($uid > 0 && !array_key_exists($uid, self::$_loadedsettings))
			{
				self::loadSettings($uid);
			}
			if (!is_array(self::$_settings) || !array_key_exists($module, self::$_settings))
			{
				return null;
			}
			if (!array_key_exists($name, self::$_settings[$module]))
			{
				return null;
				//self::$_settings[$name] = self::_loadSetting($name, $module, TBGContext::getScope()->getID());
			}
			if ($uid !== 0 && array_key_exists($uid, self::$_settings[$module][$name]))
			{
				return self::$_settings[$module][$name][$uid];
			}
			else
			{
				if (!array_key_exists($uid, self::$_settings[$module][$name]))
				{
					return null;
				}
				return self::$_settings[$module][$name][$uid];
			}
		}
		
		public static function getVersion($with_codename = false, $with_revision = true)
		{
			$retvar = self::$_ver_mj . '.' . self::$_ver_mn;
			if ($with_revision) $retvar .= '.' . self::$_ver_rev;
			if ($with_codename) $retvar .= ' ("' . self::$_ver_name . '")';
			return $retvar;  
		}
		
		public static function getMajorVer()
		{
			return self::$_ver_mj;
		}
		
		public static function getMinorVer()
		{
			return self::$_ver_mn;
		}
		
		public static function getRevision()
		{
			return self::$_ver_rev;
		}
		
		/**
		 * Returns the default scope
		 *
		 * @return TBGScope
		 */
		public static function getDefaultScope()
		{
			throw new Exception("This function is deprecated. Default scope is always 1");
			if (self::$_defaultscope === null)
			{
				$row = \b2db\Core::getTable('TBGSettingsTable')->getDefaultScope();
				self::$_defaultscope = TBGContext::factory()->TBGScope($row->get(TBGSettingsTable::VALUE));
			}
			return self::$_defaultscope;
		}
		
		public static function deleteSetting($name, $module = 'core', $scope = null, $uid = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$uid = ($uid === null) ? TBGContext::getUser()->getID() : $uid;

			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGSettingsTable::NAME, $name);
			$crit->addWhere(TBGSettingsTable::MODULE, $module);
			$crit->addWhere(TBGSettingsTable::SCOPE, $scope);
			$crit->addWhere(TBGSettingsTable::UID, $uid);
			
			\b2db\Core::getTable('TBGSettingsTable')->doDelete($crit);
			unset(self::$_settings[$name][$uid]);
		}
	
		private static function _loadSetting($name, $module = 'core', $scope = 0)
		{
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGSettingsTable::NAME, $name);
			$crit->addWhere(TBGSettingsTable::MODULE, $module);
			if ($scope == 0)
			{
				throw new Exception('BUGS has not been correctly installed. Please check that the default scope exists');
			}
			$crit->addWhere(TBGSettingsTable::SCOPE, $scope);
			$res = \b2db\Core::getTable('TBGSettingsTable')->doSelect($crit);
			if ($res->count() > 0)
			{
				$retarr = array();
				while ($row = $res->getNextRow())
				{
					$retarr[$row->get(TBGSettingsTable::UID)] = $row->get(TBGSettingsTable::VALUE);
				}
				return $retarr;
			}
			else
			{
				return null;
			}
		}
		
		public static function isRegistrationAllowed()
		{
			return self::isRegistrationEnabled();
		}

		public static function getAdminGroup()
		{
			return TBGContext::factory()->TBGGroup((int) self::get(self::SETTING_ADMIN_GROUP));
		}
		
		public static function isRegistrationEnabled()
		{
			return (bool) self::get(self::SETTING_ALLOW_REGISTRATION);
		}
		
		public static function getOpenIDStatus()
		{
			$setting = self::get(self::SETTING_ALLOW_OPENID);
			return ($setting === null) ? 'all' : $setting;
		}
		
		public static function isOpenIDavailable()
		{
			return (bool) (self::getOpenIDStatus() != 'none');
		}
		
		public static function isGravatarsEnabled()
		{
			return (bool) self::get(self::SETTING_ENABLE_GRAVATARS);
		}
		
		public static function getLanguage()
		{
			return self::get(self::SETTING_DEFAULT_LANGUAGE);
		}

		public static function getHTMLLanguage()
		{
			$lang = explode('_', self::getLanguage());
			return $lang[0];
		}
		
		public static function getCharset()
		{
			return self::get(self::SETTING_DEFAULT_CHARSET);
		}
		
		public static function getHeaderIconURL()
		{
			return self::get(self::SETTING_HEADER_ICON_URL);
		}
		
		public static function getHeaderLink()
		{
			return self::get(self::SETTING_HEADER_LINK);
		}
		
		public static function getFaviconURL()
		{
			switch (self::getFaviconType())
			{
				case self::FAVICON_CUSTOM_URL:
					return self::get(self::SETTING_FAVICON_URL);
				case self::FAVICON_PUBLIC:
					return TBGContext::getTBGPath()."favicon.png";
				default:
					return TBGContext::getTBGPath()."iconsets/".TBGSettings::getThemeName()."/favicon.png";
			}
		}
		
		public static function getTBGname()
		{
			return self::get(self::SETTING_TBG_NAME);
		}
	
		public static function getTBGtagline()
		{
			return self::get(self::SETTING_TBG_TAGLINE);
		}
		
		public static function isFrontpageProjectListVisible()
		{
			return (bool) self::get(self::SETTING_SHOW_PROJECTS_OVERVIEW);
		}

		public static function isSingleProjectTracker()
		{
			return (bool) self::get(self::SETTING_IS_SINGLE_PROJECT_TRACKER);
		}
		
		public static function isUsingCustomHeaderIcon()
		{
			return self::get(self::SETTING_HEADER_ICON_TYPE);
		}
		
		public static function getFaviconType()
		{
			return self::get(self::SETTING_FAVICON_TYPE);
		}

		public static function getThemeName()
		{
			return self::get(self::SETTING_THEME_NAME);
		}
	
		public static function getIconsetName()
		{
			return self::get(self::SETTING_ICONSET);
		}
		
		public static function isUserThemesEnabled()
		{
			return (bool) self::get(self::SETTING_ALLOW_USER_THEMES);
		}
		
		public static function isCommentTrailClean()
		{
			return (bool) self::get(self::SETTING_KEEP_COMMENT_TRAIL_CLEAN);
		}

		public static function isCommentImagePreviewEnabled()
		{
			return (self::get(self::SETTING_PREVIEW_COMMENT_IMAGES) !== null) ? (bool) self::get(self::SETTING_PREVIEW_COMMENT_IMAGES) : true;
		}

		public static function isLoginRequired()
		{
			return (bool) self::get(self::SETTING_REQUIRE_LOGIN);
		}
		
		public static function isDefaultUserGuest()
		{
			return (bool) self::get(self::SETTING_DEFAULT_USER_IS_GUEST);
		}
		
		public static function getDefaultUserID()
		{
			return self::get(self::SETTING_DEFAULT_USER_ID);
		}
		
		public static function allowRegistration()
		{
			return self::isRegistrationAllowed();
		}
		
		public static function getRegistrationDomainWhitelist()
		{
			return self::get(self::SETTING_REGISTRATION_DOMAIN_WHITELIST);
		}

		public static function getDefaultGroupIDs()
		{
			return array(self::get(self::SETTING_ADMIN_GROUP), self::get(self::SETTING_GUEST_GROUP), self::get(self::SETTING_USER_GROUP));
		}

		/**
		 * Return the default user group
		 *
		 * @return TBGGroup
		 */
		public static function getDefaultGroup()
		{
			try
			{
				return TBGContext::factory()->TBGGroup(self::get(self::SETTING_USER_GROUP));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getLoginReturnRoute()
		{
			return self::get(self::SETTING_RETURN_FROM_LOGIN);
		}
		
		public static function getLogoutReturnRoute()
		{
			return self::get(self::SETTING_RETURN_FROM_LOGOUT);
		}
		
		public static function isMaintenanceModeEnabled()
		{
			return (bool)self::get(self::SETTING_MAINTENANCE_MODE);
		}
		
		public static function hasMaintenanceMessage()
		{
			if (self::get(self::SETTING_MAINTENANCE_MESSAGE) == '')
			{
				return false;
			}
			return true;
		}
		
		public static function getMaintenanceMessage()
		{
			return self::get(self::SETTING_MAINTENANCE_MESSAGE);
		}
		
		public static function getOnlineState()
		{
			try
			{
				return TBGContext::factory()->TBGUserstate(self::get(self::SETTING_ONLINESTATE));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
	
		public static function getOfflineState()
		{
			try
			{
				return TBGContext::factory()->TBGUserstate(self::get(self::SETTING_OFFLINESTATE));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getPasswordSalt()
		{
			$salt = self::get(self::SETTING_SALT);
			return $salt;
		}
		
		public static function getAwayState()
		{
			try
			{
				return TBGContext::factory()->TBGUserstate(self::get(self::SETTING_AWAYSTATE));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getURLhost()
		{
			return TBGContext::getScope()->getCurrentHostname();
		}
		
		public static function getGMToffset()
		{
			return self::get(self::SETTING_SERVER_TIMEZONE);
		}
		
		public static function getUserTimezone()
		{
			return self::get(self::SETTING_USER_TIMEZONE, 'core', null, TBGContext::getUser()->getID());
		}
		
		public static function getUserLanguage()
		{
			return self::get(self::SETTING_USER_LANGUAGE, 'core', null, TBGContext::getUser()->getID());
		}
		
		public static function isUploadsEnabled()
		{
			return (bool) (TBGContext::getScope()->isUploadsEnabled() && self::get(self::SETTING_ENABLE_UPLOADS));
		}

		public static function getUploadsMaxSize($bytes = false)
		{
			return ($bytes) ? (int) (self::get(self::SETTING_UPLOAD_MAX_FILE_SIZE) * 1024 * 1024) : (int) self::get(self::SETTING_UPLOAD_MAX_FILE_SIZE);
		}

		public static function getUploadsRestrictionMode()
		{
			return self::get(self::SETTING_UPLOAD_RESTRICTION_MODE);
		}

		public static function getUploadsExtensionsList()
		{
			$extensions = (string) self::get(self::SETTING_UPLOAD_EXTENSIONS_LIST);
			$delimiter = ' ';

			switch (true)
			{
				case (mb_strpos($extensions, ',') !== false):
					$delimiter = ',';
					break;
				case (mb_strpos($extensions, ';') !== false):
					$delimiter = ';';
					break;
			}
			return explode($delimiter, $extensions);
		}

		public static function getUploadStorage()
		{
			return self::get(self::SETTING_UPLOAD_STORAGE);
		}

		public static function getUploadsLocalpath()
		{
			return self::get(self::SETTING_UPLOAD_LOCAL_PATH);
		}

		public static function isInfoBoxVisible($key)
		{
			return !(bool) self::get(self::INFOBOX_PREFIX . $key, 'core', TBGContext::getScope()->getID(), TBGContext::getUser()->getID());
		}

		public static function hideInfoBox($key)
		{
			self::saveSetting(self::INFOBOX_PREFIX . $key, 1, 'core', TBGContext::getScope()->getID(), TBGContext::getUser()->getID());
		}
		
		public static function showInfoBox($key)
		{
			self::deleteSetting(self::INFOBOX_PREFIX . $key);
		}

		public static function isPermissive()
		{
			return (bool) self::get(self::SETTING_IS_PERMISSIVE_MODE);
		}

		public static function getAll()
		{
			return self::$_settings;
		}

		public static function getDefaultSyntaxHighlightingLanguage()
		{
			return self::get(self::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_LANGUAGE);
		}

		public static function getDefaultSyntaxHighlightingNumbering()
		{
			return self::get(self::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_NUMBERING);
		}

		public static function getDefaultSyntaxHighlightingInterval()
		{
			return self::get(self::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL);
		}

		public static function getRemoteSecurityKey()
		{
			return self::getPasswordSalt();
		}
		
		public static function getAuthenticationBackend()
		{
			return self::get(self::SETTING_AUTH_BACKEND);
		}
		
		public static function isUsingExternalAuthenticationBackend()
		{
			if (TBGSettings::getAuthenticationBackend() !== null && TBGSettings::getAuthenticationBackend() !== 'tbg'): return true; else: return false; endif;
		}

	}
