<?php

    namespace thebuggenie\core\entities\tables;

    /**
     * Module permissions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Module permissions table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="modulepermissions")
     */
    class ModulePermissions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'modulepermissions';
        const ID = 'modulepermissions.id';
        const SCOPE = 'modulepermissions.scope';
        const MODULE_NAME = 'modulepermissions.module_name';
        const UID = 'modulepermissions.uid';
        const GID = 'modulepermissions.gid';
        const TID = 'modulepermissions.tid';
        const ALLOWED = 'modulepermissions.allowed';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::MODULE_NAME, 50);
            parent::_addBoolean(self::ALLOWED);
            parent::_addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
            parent::_addForeignKeyColumn(self::GID, Groups::getTable(), Groups::ID);
            parent::_addForeignKeyColumn(self::TID, Teams::getTable(), Teams::ID);
        }
        
        public function deleteByModuleAndUIDandGIDandTIDandScope($module_name, $uid, $gid, $tid, $scope)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::MODULE_NAME, $module_name);
            $crit->addWhere(self::UID, $uid);
            $crit->addWhere(self::GID, $gid);
            $crit->addWhere(self::TID, $tid);
            $crit->addWhere(self::SCOPE, $scope);
            $res = $this->doDelete($crit);
        }
        
        public function setPermissionByModuleAndUIDandGIDandTIDandScope($module_name, $uid, $gid, $tid, $allowed, $scope)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::MODULE_NAME, $module_name);
            $crit->addInsert(self::ALLOWED, $allowed);
            $crit->addInsert(self::UID, $uid);
            $crit->addInsert(self::GID, $gid);
            $crit->addInsert(self::TID, $tid);
            $crit->addInsert(self::SCOPE, $scope);
            $res = $this->doInsert($crit);
        }
        
    }
