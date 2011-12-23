<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Settings table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Settings table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="settings")
	 */
	class TBGSettingsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'settings';
		const ID = 'settings.id';
		const SCOPE = 'settings.scope';
		const NAME = 'settings.name';
		const MODULE = 'settings.module';
		const VALUE = 'settings.value';
		const UID = 'settings.uid';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGSettingsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGSettingsTable');
		}

		public function _setupIndexes()
		{
			$this->_addIndex('scope_uid', array(self::SCOPE, self::UID));
		}
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 45);
			parent::_addVarchar(self::MODULE, 45);
			parent::_addVarchar(self::VALUE, 200);
			parent::_addInteger(self::UID, 10);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable());
		}
		
		public function getDefaultScope()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, 0);
			$crit->addWhere(self::NAME, 'defaultscope');
			$row = $this->doSelectOne($crit);
			return $row;
		}

		public function getSettingsForScope($scope, $uid = 0)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			$crit->addWhere(self::UID, $uid);
			$res = $this->doSelect($crit, 'none');
			return $res;
		}

		public function saveSetting($name, $module, $value, $uid, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $name);
			$crit->addWhere(self::MODULE, $module);
			$crit->addWhere(self::UID, $uid);
			$crit->addWhere(self::SCOPE, $scope);
			$res = $this->doSelectOne($crit);

			if ($res instanceof \b2db\Row)
			{
				$theID = $res->get(self::ID);
				$crit2 = new Criteria();
				$crit2->addWhere(self::NAME, $name);
				$crit2->addWhere(self::MODULE, $module);
				$crit2->addWhere(self::UID, $uid);
				$crit2->addWhere(self::SCOPE, $scope);
				$crit2->addWhere(self::ID, $theID, Criteria::DB_NOT_EQUALS);
				$res2 = $this->doDelete($crit2);
				
				$crit = $this->getCriteria();
				$crit->addUpdate(self::NAME, $name);
				$crit->addUpdate(self::MODULE, $module);
				$crit->addUpdate(self::UID, $uid);
				$crit->addUpdate(self::VALUE, $value);
				$this->doUpdateById($crit, $theID);
			}
			else
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::NAME, $name);
				$crit->addInsert(self::MODULE, $module);
				$crit->addInsert(self::VALUE, $value);
				$crit->addInsert(self::SCOPE, $scope);
				$crit->addInsert(self::UID, $uid);
				$this->doInsert($crit);
			}
		}

		public function deleteModuleSettings($module_name, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MODULE, $module_name);
			$crit->addWhere(self::SCOPE, $scope);
			$this->doDelete($crit);
		}

		public function loadFixtures(TBGScope $scope)
		{
			$i18n = TBGContext::getI18n();

			$settings = array();
			$settings[TBGSettings::SETTING_THEME_NAME] = 'oxygen';
			$settings[TBGSettings::SETTING_REQUIRE_LOGIN] = 0;
			$settings[TBGSettings::SETTING_DEFAULT_USER_IS_GUEST] = 1;
			$settings[TBGSettings::SETTING_ALLOW_REGISTRATION] = 1;
			$settings[TBGSettings::SETTING_RETURN_FROM_LOGIN] = 'referer';
			$settings[TBGSettings::SETTING_RETURN_FROM_LOGOUT] = 'home';
			$settings[TBGSettings::SETTING_SHOW_PROJECTS_OVERVIEW] = 1;
			$settings[TBGSettings::SETTING_ALLOW_USER_THEMES] = 0;
			$settings[TBGSettings::SETTING_ENABLE_UPLOADS] = 0;
			$settings[TBGSettings::SETTING_ENABLE_GRAVATARS] = 1;
			$settings[TBGSettings::SETTING_UPLOAD_RESTRICTION_MODE] = 'blacklist';
			$settings[TBGSettings::SETTING_UPLOAD_EXTENSIONS_LIST] = 'exe,bat,php,asp,jsp';
			$settings[TBGSettings::SETTING_UPLOAD_STORAGE] = 'files';
			$settings[TBGSettings::SETTING_UPLOAD_LOCAL_PATH] = THEBUGGENIE_PATH . 'files/';
			$settings[TBGSettings::SETTING_TBG_NAME] = 'The Bug Genie';
			$settings[TBGSettings::SETTING_TBG_TAGLINE] = '<b>Friendly</b> issue tracking and project management';
			$settings[TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_LANGUAGE] = 'html4strict';
			$settings[TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_NUMBERING] = '3';
			$settings[TBGSettings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL] = '10';
			$settings[TBGSettings::SETTING_SALT] = sha1(time().mt_rand(1000, 10000));
			$settings[TBGSettings::SETTING_ICONSET] = 'oxygen';

			$scope_id = $scope->getID();
			foreach ($settings as $settings_name => $settings_val)
			{
				$this->saveSetting($settings_name, 'core', $settings_val, 0, $scope_id);
			}
		}
		
	}
