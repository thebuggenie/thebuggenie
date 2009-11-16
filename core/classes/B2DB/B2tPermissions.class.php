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
		const B2DBNAME = 'permissions';
		const ID = 'permissions.id';
		const SCOPE = 'permissions.scope';
		const PERMISSION_TYPE = 'permissions.permission_type';
		const TARGET_ID = 'permissions.target_id';
		const UID = 'permissions.uid';
		const GID = 'permissions.gid';
		const TID = 'permissions.tid';
		const ALLOWED = 'permissions.allowed';
		const MODULE = 'permissions.module';

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

		public function deleteModulePermissions($module_name, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MODULE, $module_name);
			$crit->addWhere(self::SCOPE, $scope);
			$this->doDelete($crit);
		}

		public function loadFixtures($scope_id, $admin_group_id, $guest_group_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ALLOWED, true);
			$crit->addInsert(self::SCOPE, $scope_id);
			$crit->addInsert(self::PERMISSION_TYPE, 'b2viewconfig');
			$crit->addInsert(self::TARGET_ID, 0);
			$crit->addInsert(self::GID, $admin_group_id);
			$crit->addInsert(self::MODULE, 'core');
			$this->doInsert($crit);

			for ($cc = 1; $cc <= 16; $cc++)
			{
				if ($cc == 13) continue;
				$crit = $this->getCriteria();
				$crit->addInsert(self::ALLOWED, 1);
				$crit->addInsert(self::SCOPE, $scope_id);
				$crit->addInsert(self::PERMISSION_TYPE, 'b2saveconfig');
				$crit->addInsert(self::TARGET_ID, $cc);
				$crit->addInsert(self::GID, $admin_group_id);
				$crit->addInsert(self::MODULE, 'core');
				$this->doInsert($crit);
			}

			$crit = $this->getCriteria();
			$crit->addInsert(self::ALLOWED, 1);
			$crit->addInsert(self::SCOPE, $scope_id);
			$crit->addInsert(self::PERMISSION_TYPE, 'b2noaccountaccess');
			$crit->addInsert(self::TARGET_ID, 0);
			$crit->addInsert(self::GID, $guest_group_id);
			$crit->addInsert(self::MODULE, 'core');
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::ALLOWED, 1);
			$crit->addInsert(self::SCOPE, $scope_id);
			$crit->addInsert(self::TARGET_ID, 0);
			$crit->addInsert(self::PERMISSION_TYPE, 'b2canreportissues');
			$crit->addInsert(self::MODULE, 'core');
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::ALLOWED, 1);
			$crit->addInsert(self::SCOPE, $scope_id);
			$crit->addInsert(self::TARGET_ID, 0);
			$crit->addInsert(self::PERMISSION_TYPE, 'b2canfindissues');
			$crit->addInsert(self::MODULE, 'core');
			$this->doInsert($crit);
		}
		
	}
