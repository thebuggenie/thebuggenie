<?php

	/**
	 * Settings class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	final class BUGSsettings
	{

		const CONFIGURATION_SECTION_SETTINGS = 12;
		const CONFIGURATION_SECTION_PROJECTS = 10;
		const CONFIGURATION_SECTION_PERMISSIONS = 5;
		const CONFIGURATION_SECTION_UPLOADS = 3;
		const CONFIGURATION_SECTION_SCOPES = 14;
		const CONFIGURATION_SECTION_IMPORT = 16;
		const CONFIGURATION_SECTION_ISSUETYPES = 6;
		const CONFIGURATION_SECTION_ISSUEFIELDS = 4;
		const CONFIGURATION_SECTION_USERS = 2;
		const CONFIGURATION_SECTION_MODULES = 15;

		static protected $_ver_mj = null;
		static protected $_ver_mn = null;
		static protected $_ver_rev = null;
		static protected $_ver_name = null;
		static protected $_defaultscope = null;
		static protected $_settings = null;
	
		public static function loadSettings()
		{
			if (self::$_settings === null)
			{
				BUGSlogging::log('Loading all settings');
				self::$_settings = array();
				self::$_ver_mj = 3;
				self::$_ver_mn = 0;
				self::$_ver_rev = '0a (dev)';
				self::$_ver_name = 'Make it mine';
				if (self::$_settings = BUGScache::get('settings'))
				{
					BUGSlogging::log('Using cached settings');
				}
				else
				{
					BUGSlogging::log('Settings not cached. Retrieving from database');
					if ($res = B2DB::getTable('B2tSettings')->getSettingsForEnabledScope(BUGScontext::getScope()->getID()))
					{
						$cc = 0;
						while ($row = $res->getNextRow())
						{
							$cc++;
							self::$_settings[$row->get(B2tSettings::MODULE)][$row->get(B2tSettings::NAME)][$row->get(B2tSettings::UID)] = $row->get(B2tSettings::VALUE);
						}
						if ($cc == 0)
						{
							BUGSlogging::log('There were no settings stored in the database!', 'main', BUGSlogging::LEVEL_FATAL);
							throw new Exception('Could not retrieve settings from database');
						}
					}
					else
					{
						BUGSlogging::log('Settings could not be retrieved from the database!', 'main', BUGSlogging::LEVEL_FATAL);
						throw new Exception('Could not retrieve settings from database');
					}
					BUGSlogging::log('Retrieved');
					BUGScache::add('settings', self::$_settings);
				}
			}
		}

		public static function deleteModuleSettings($module_name)
		{
			if (array_key_exists($module_name, self::$_settings))
			{
				unset(self::$_settings[$module_name]);
			}
			B2DB::getTable('B2tSettings')->deleteModuleSettings($module_name, BUGScontext::getScope()->getID());
		}
		
		public static function saveSetting($name, $value, $module = 'core', $scope = 0, $uid = 0)
		{
			if ($scope == 0 && $name != 'defaultscope' && $module == 'core')
			{
				if (($scope = BUGScontext::getScope()) instanceof BUGSscope)
				{
					$scope = $scope->getID();
				}
				elseif (BUGScontext::isInstallmode())
				{
					$scope = 1;
				}
				else
				{
					throw new Exception('No scope loaded, cannot autoload it');
				}
			}

			B2DB::getTable('B2tSettings')->saveSetting($name, $module, $value, $uid, $scope);
			
			if ($scope != 0 && ((!BUGScontext::getScope() instanceof BUGSscope) || $scope == BUGScontext::getScope()->getID()))
			{
				self::$_settings[$module][$name][$uid] = $value;
			}
			BUGScache::delete('settings');
		}
		
		public static function set($name, $value, $uid = 0, $module = 'core')
		{
			self::$_settings[$module][$name][$uid] = $value;
		}
	
		public static function get($name, $module = 'core', $scope = null, $uid = 0)
		{
			if (BUGScontext::isInstallmode() && !BUGScontext::getScope() instanceof BUGSscope)
			{
				return null;
			}
			if ($scope instanceof BUGSscope)
			{
				throw new Exception('Oops!');
			}
			if (!BUGScontext::getScope() instanceof BUGSscope)
			{
				throw new Exception('BUGS 2 is not installed correctly');
			}
			if ($scope != BUGScontext::getScope()->getID() && $scope !== null)
			{
				$setting = self::_loadSetting($name, $module, $scope);
				return $setting[$uid];
			}
			if (self::$_settings === null)
			{
				self::loadSettings();
			}
			if (!is_array(self::$_settings) || !array_key_exists($module, self::$_settings))
			{
				return null;
			}
			if (!array_key_exists($name, self::$_settings[$module]))
			{
				return null;
				//self::$_settings[$name] = self::_loadSetting($name, $module, BUGScontext::getScope()->getID());
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
		
		public static function getVersion($with_codename = false)
		{
			$retvar = self::$_ver_mj . '.' . self::$_ver_mn . '.' . self::$_ver_rev;
			if ($with_codename) $retvar .= ' ("' . self::$_ver_name . '")';
			return $retvar;  
		}
		
		/**
		 * Returns the default scope
		 *
		 * @return BUGSscope
		 */
		public static function getDefaultScope()
		{
			throw new Exception("This function is deprecated. Default scope is always 1");
			if (self::$_defaultscope === null)
			{
				$row = B2DB::getTable('B2tSettings')->getDefaultScope();
				self::$_defaultscope = BUGSfactory::scopeLab($row->get(B2tSettings::VALUE));
			}
			return self::$_defaultscope;
		}
		
		public static function deleteSetting($name, $module = 'core', $value = '', $scope = 0, $uid = 0)
		{
			if ($scope == 0)
			{
				$scope = BUGScontext::getScope()->getID();
			}
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSettings::NAME, $name);
			$crit->addWhere(B2tSettings::MODULE, $module);
			$crit->addWhere(B2tSettings::SCOPE, $scope);
			$crit->addWhere(B2tSettings::UID, $uid);
			if ($value != "")
			{
				$crit->addWhere(B2tSettings::VALUE, $value);
			}
			B2DB::getTable('B2tSettings')->doDelete($crit);
			unset(self::$_settings[$name][$uid]);
		}
	
		private static function _loadSetting($name, $module = 'core', $scope = 0)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSettings::NAME, $name);
			$crit->addWhere(B2tSettings::MODULE, $module);
			if ($scope == 0)
			{
				throw new Exception('BUGS has not been correctly installed. Please check that the default scope exists');
			}
			$crit->addWhere(B2tSettings::SCOPE, $scope);
			$res = B2DB::getTable('B2tSettings')->doSelect($crit);
			if ($res->count() > 0)
			{
				$retarr = array();
				while ($row = $res->getNextRow())
				{
					$retarr[$row->get(B2tSettings::UID)] = $row->get(B2tSettings::VALUE);
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
			return BUGSfactory::groupLab((int) self::get('admingroup'));
		}
		
		public static function isRegistrationEnabled()
		{
			return (bool) self::get('allowreg');
		}
		
		public static function getLanguage()
		{
			return self::get('language');
		}
		
		public static function getCharset()
		{
			return self::get('charset');
		}
		
		public static function getTBGname()
		{
			return self::get('b2_name');
		}
	
		public static function getTBGtagline()
		{
			return self::get('b2_tagline');
		}
		
		public static function isProjectOverviewEnabled()
		{
			return (bool) self::get('showprojectsoverview');
		}

		public static function isSingleProjectTracker()
		{
			return (bool) self::get('singleprojecttracker');
		}

		public static function getThemeName()
		{
			return self::get('theme_name');
		}
		
		public static function isUserThemesEnabled()
		{
			return (bool) self::get('user_themes');
		}
		
		public static function isCommentTrailClean()
		{
			return (bool) self::get('cleancomments');
		}
		
		public static function isLoginRequired()
		{
			return (bool) self::get('requirelogin');
		}
		
		public static function showLoginBox()
		{
			return (bool) self::get('showloginbox');
		}
		
		public static function isLoginBoxVisible()
		{
			return self::showLoginBox();
		}
		
		public static function isDefaultUserGuest()
		{
			return (bool) self::get('defaultisguest');
		}
		
		public static function getDefaultUserID()
		{
			return self::get('defaultuserid');
		}
		
		public static function allowRegistration()
		{
			return self::isRegistrationAllowed();
		}
		
		public static function getRegistrationDomainWhitelist()
		{
			return self::get('limit_registration');
		}
		
		public static function getDefaultGroup()
		{
			try
			{
				return BUGSfactory::groupLab(self::get('defaultgroup'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getLoginReturnRoute()
		{
			return self::get('returnfromlogin');
		}
		
		public static function getLogoutReturnRoute()
		{
			return self::get('returnfromlogout');
		}
		
		public static function getOnlineState()
		{
			try
			{
				return BUGSfactory::userstateLab(self::get('onlinestate'));
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
				return BUGSfactory::userstateLab(self::get('offlinestate'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getAwayState()
		{
			try
			{
				return BUGSfactory::userstateLab(self::get('awaystate'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getURLhost()
		{
			return BUGScontext::getScope()->getHostname();
		}
		
		public static function getURLsubdir()
		{
			return self::get('url_subdir');
		}
		
		public static function getLocalPath()
		{
			return self::get('local_path');
		}
		
		public static function getGMToffset()
		{
			return self::get('server_timezone');
		}
		
		public static function getUserTimezone()
		{
			return self::get('timezone', 'core', null, BUGScontext::getUser()->getID());
		}
		
		public static function getAuthenticationMethod()
		{
			return self::get('authentication_method');
		}
		
		public static function isUploadsEnabled()
		{
			return (bool) self::get('enable_uploads');
		}

		public static function getIssueTypeBugReport()
		{
			return self::get('issuetype_bug_report');
		}
		
		public static function getIssueTypeFeatureRequest()
		{
			return self::get('issuetype_feature_request');
		}

		public static function getIssueTypeEnhancement()
		{
			return self::get('issuetype_enhancement');
		}

		public static function getIssueTypeTask()
		{
			return self::get('issuetype_task');
		}

		public static function getIssueTypeUserStory()
		{
			return self::get('issuetype_user_story');
		}

		public static function getIssueTypeIdea()
		{
			return self::get('issuetype_idea');
		}
		
		public static function isInfoBoxVisible($key)
		{
			return !(bool) self::get('hide_infobox_' . $key, 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getID());
		}

		public static function hideInfoBox($key)
		{
			self::saveSetting('hide_infobox_' . $key, 1, 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getID());
		}
		
		public static function showInfoBox($key)
		{
			self::deleteSetting('hide_infobox_' . $key, 'core', '', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getID());
		}

		public static function isPermissive()
		{
			return (bool) self::get('permissive');
		}

	}
