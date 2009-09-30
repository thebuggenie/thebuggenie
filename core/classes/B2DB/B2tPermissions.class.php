<?php

	/**
	 * Permissions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Permissions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tPermissions extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_permissions';
		const ID = 'bugs2_permissions.id';
		const SCOPE = 'bugs2_permissions.scope';
		const PERMISSION_TYPE = 'bugs2_permissions.permission_type';
		const TARGET_ID = 'bugs2_permissions.target_id';
		const UID = 'bugs2_permissions.uid';
		const GID = 'bugs2_permissions.gid';
		const TID = 'bugs2_permissions.tid';
		const ALLOWED = 'bugs2_permissions.allowed';
		const MODULE = 'bugs2_permissions.module';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::PERMISSION_TYPE, 100);
			parent::_addVarchar(self::TARGET_ID, 10, 0);
			parent::_addBoolean(self::ALLOWED);
			parent::_addVarchar(self::MODULE, 50);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::GID, B2DB::getTable('B2tGroups'), B2tGroups::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $uid);
			$crit->addWhere(self::GID, $gid);
			$crit->addWhere(self::TID, $tid);
			$crit->addWhere(self::MODULE, $module);
			$crit->addWhere(self::PERMISSION_TYPE, $permission_type);
			$crit->addWhere(self::TARGET_ID, $target_id);
			$crit->addWhere(self::SCOPE, $scope);
			
			$res = $this->doDelete($crit);
		}

		public function setPermission($uid, $gid, $tid, $allowed, $module, $permission_type, $target_id, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::UID, $uid);
			$crit->addInsert(self::GID, $gid);
			$crit->addInsert(self::TID, $tid);
			$crit->addInsert(self::ALLOWED, $allowed);
			$crit->addInsert(self::MODULE, $module);
			$crit->addInsert(self::PERMISSION_TYPE, $permission_type);
			$crit->addInsert(self::TARGET_ID, $target_id);
			$crit->addInsert(self::SCOPE, $scope);
			
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
	}
