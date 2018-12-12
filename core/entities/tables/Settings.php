<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
    use thebuggenie\core\framework,
        b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Settings table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
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
    class Settings extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'settings';
        const ID = 'settings.id';
        const SCOPE = 'settings.scope';
        const NAME = 'settings.name';
        const MODULE = 'settings.module';
        const VALUE = 'settings.value';
        const UPDATED_AT = 'settings.updated_at';
        const UID = 'settings.uid';

        protected function setupIndexes()
        {
            $this->addIndex('scope_uid', array(self::SCOPE, self::UID));
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::NAME, 45);
            parent::addVarchar(self::MODULE, 45);
            parent::addVarchar(self::VALUE, 200);
            parent::addInteger(self::UID, 10);
            parent::addInteger(self::UPDATED_AT, 10);
        }

        public function getSettingsForScope($scope, $uid = 0)
        {
            $query = $this->getQuery();
            if (framework\Context::isUpgrademode())
            {
                $query->addSelectionColumn(self::NAME);
                $query->addSelectionColumn(self::MODULE);
                $query->addSelectionColumn(self::VALUE);
                $query->addSelectionColumn(self::UID);
                $query->addSelectionColumn(self::SCOPE);
            }
            $query->where(self::UID, $uid);

            $criteria = new Criteria();
            $criteria->where(self::SCOPE, $scope);
            $criteria->or(self::SCOPE, 0);
            $query->and($criteria);

            $res = $this->rawSelect($query, 'none');
            return $res;
        }

        public function saveSetting($name, $module, $value, $uid, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::MODULE, $module);
            $query->where(self::UID, $uid);
            $query->where(self::SCOPE, $scope);
            $res = $this->rawSelectOne($query);

            if ($res instanceof \b2db\Row)
            {
                $theID = $res->get(self::ID);
                $query = $this->getQuery();
                $query->where(self::NAME, $name);
                $query->where(self::MODULE, $module);
                $query->where(self::UID, $uid);
                $query->where(self::SCOPE, $scope);
                $query->where(self::ID, $theID, \b2db\Criterion::NOT_EQUALS);
                $res2 = $this->rawDelete($query);

                $update = new Update();
                $update->add(self::NAME, $name);
                $update->add(self::MODULE, $module);
                $update->add(self::UID, $uid);
                $update->add(self::VALUE, $value);
                $update->add(self::UPDATED_AT, time());
                $this->rawUpdateById($update, $theID);
            }
            else
            {
                $insertion = new Insertion();
                $insertion->add(self::NAME, $name);
                $insertion->add(self::MODULE, $module);
                $insertion->add(self::VALUE, $value);
                $insertion->add(self::SCOPE, $scope);
                $insertion->add(self::UID, $uid);
                $insertion->add(self::UPDATED_AT, time());
                $this->rawInsert($insertion);
            }
        }

        public function deleteModuleSettings($module_name, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::MODULE, $module_name);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deleteAllUserModuleSettings($module_name, $scope = null)
        {
            $query = $this->getQuery();
            $query->where(self::MODULE, $module_name);
            $query->where(self::UID, 0, \b2db\Criterion::GREATER_THAN);
            if ($scope !== null)
            {
                $query->where(self::SCOPE, $scope);
            }
            $this->rawDelete($query);
        }

        public function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $i18n = framework\Context::getI18n();

            $settings = array();
            $settings[\thebuggenie\core\framework\Settings::SETTING_THEME_NAME] = 'oxygen';
            $settings[\thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN] = false;
            $settings[\thebuggenie\core\framework\Settings::SETTING_DEFAULT_USER_IS_GUEST] = true;
            $settings[\thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION] = true;
            $settings[\thebuggenie\core\framework\Settings::SETTING_RETURN_FROM_LOGIN] = 'referer';
            $settings[\thebuggenie\core\framework\Settings::SETTING_RETURN_FROM_LOGOUT] = 'home';
            $settings[\thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW] = true;
            $settings[\thebuggenie\core\framework\Settings::SETTING_ALLOW_USER_THEMES] = false;
            $settings[\thebuggenie\core\framework\Settings::SETTING_ENABLE_UPLOADS] = false;
            $settings[\thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS] = true;
            $settings[\thebuggenie\core\framework\Settings::SETTING_UPLOAD_RESTRICTION_MODE] = 'blacklist';
            $settings[\thebuggenie\core\framework\Settings::SETTING_UPLOAD_EXTENSIONS_LIST] = 'exe,bat,php,asp,jsp';
            $settings[\thebuggenie\core\framework\Settings::SETTING_UPLOAD_STORAGE] = 'files';
            $settings[\thebuggenie\core\framework\Settings::SETTING_UPLOAD_LOCAL_PATH] = THEBUGGENIE_PATH . 'files/';
            $settings[\thebuggenie\core\framework\Settings::SETTING_UPLOAD_ALLOW_IMAGE_CACHING] = false;
            $settings[\thebuggenie\core\framework\Settings::SETTING_UPLOAD_DELIVERY_USE_XSEND] = false;
            $settings[\thebuggenie\core\framework\Settings::SETTING_TBG_NAME] = 'The Bug Genie';
            $settings[\thebuggenie\core\framework\Settings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_LANGUAGE] = 'html';
            $settings[\thebuggenie\core\framework\Settings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_NUMBERING] = '3';
            $settings[\thebuggenie\core\framework\Settings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL] = '10';
            $settings[\thebuggenie\core\framework\Settings::SETTING_ICONSET] = 'oxygen';
            $settings[\thebuggenie\core\framework\Settings::SETTING_SERVER_TIMEZONE] = date_default_timezone_get();
            $settings[\thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED] = true;

            $scope_id = $scope->getID();
            foreach ($settings as $settings_name => $settings_val)
            {
                $this->saveSetting($settings_name, 'core', $settings_val, 0, $scope_id);
            }
        }

        public function getFileIds()
        {
            $query = $this->getQuery();
            $file_id_settings = [
                framework\Settings::SETTING_FAVICON_ID,
                framework\Settings::SETTING_HEADER_ICON_ID
            ];
            $query->where(self::NAME, $file_id_settings, \b2db\Criterion::IN);
            $query->addSelectionColumn(self::VALUE, 'file_id');

            $res = $this->rawSelect($query);
            $file_ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file_ids[$row['file_id']] = $row['file_id'];
                }
            }

            return $file_ids;
        }

    }
