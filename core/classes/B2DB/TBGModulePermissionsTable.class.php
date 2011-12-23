<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Module permissions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
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
	class TBGModulePermissionsTable extends TBGB2DBTable 
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
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::GID, TBGGroupsTable::getTable(), TBGGroupsTable::ID);
			parent::_addForeignKeyColumn(self::TID, Core::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
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
