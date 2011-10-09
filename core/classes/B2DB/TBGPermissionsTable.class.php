<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Permissions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
	class TBGPermissionsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
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

		/**
		 * Return an instance of this table
		 *
		 * @return TBGPermissionsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGPermissionsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::PERMISSION_TYPE, 100);
			parent::_addVarchar(self::TARGET_ID, 200, 0);
			parent::_addBoolean(self::ALLOWED);
			parent::_addVarchar(self::MODULE, 50);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::GID, TBGGroupsTable::getTable(), TBGGroupsTable::ID);
			parent::_addForeignKeyColumn(self::TID, Core::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		protected function _setupIndexes()
		{
			$this->_addIndex('scope', array(self::SCOPE));
		}

		public function getAll($scope_id = null)
		{
			$scope_id = ($scope_id === null) ? TBGContext::getScope()->getID() : $scope_id;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope_id);
			$res = $this->doSelect($crit, 'none');
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
			$crit->addInsert(self::UID, (int) $uid);
			$crit->addInsert(self::GID, (int) $gid);
			$crit->addInsert(self::TID, (int) $tid);
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

		public function loadFixtures(TBGScope $scope, $admin_group_id, $guest_group_id)
		{
			$scope_id = $scope->getID();
			
			$this->setPermission(0, $admin_group_id, 0, true, 'core', 'cansaveconfig', 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', 'page_account_access', 0, $scope_id);
			$this->setPermission(0, $guest_group_id, 0, false, 'core', 'page_account_access', 0, $scope_id);
			$this->setPermission(0, 0, 0, false, 'core', 'candoscrumplanning', 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', 'cancreateandeditissues', 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', 'canfindissuesandsavesearches', 0, $scope_id);
			$this->setPermission(0, 0, 0, false, 'core', 'cancreatepublicsearches', 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', 'cancreatepublicsearches', 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', 'caneditmainmenu', 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', 'caneditissuecustomfieldsown', 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', 'canpostandeditcomments', 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', 'canpostseeandeditallcomments', 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "canseeproject", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', 'candoscrumplanning', 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "page_project_allpages_access", 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', "page_home_access", 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', "page_about_access", 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', "page_dashboard_access", 0, $scope_id);
			$this->setPermission(0, 0, 0, true, 'core', "page_search_access", 0, $scope_id);
			$this->setPermission(0, $guest_group_id, 0, false, 'core', "page_dashboard_access", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "page_teamlist_access", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "page_clientlist_access", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "canvoteforissues", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "canlockandeditlockedissues", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "cancreateandeditissues", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "caneditissue", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "candeleteissues", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "caneditissuecustomfields", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "canaddextrainformationtoissues", 0, $scope_id);
			$this->setPermission(0, $admin_group_id, 0, true, 'core', "canpostseeandeditallcomments", 0, $scope_id);
		}

		public function cloneGroupPermissions($cloned_group_id, $new_group_id)
		{
			return $this->_clonePermissions($cloned_group_id, $new_group_id, 'group');
		}

		public function cloneTeamPermissions($cloned_group_id, $new_group_id)
		{
			return $this->_clonePermissions($cloned_group_id, $new_group_id, 'group');
		}

		protected function _clonePermissions($cloned_id, $new_id, $mode)
		{
			$crit = $this->getCriteria();
			switch ($mode)
			{
				case 'group':
					$mode = self::GID;
					break;
				case 'team':
					$mode = self::TID;
					break;
			}
			$crit->addWhere($mode, $cloned_id);
			$permissions_to_add = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$permissions_to_add[] = array('target_id' => $row->get(self::TARGET_ID), 'permission_type' => $row->get(self::PERMISSION_TYPE), 'allowed' => $row->get(self::ALLOWED), 'module' => $row->get(self::MODULE));
				}
			}

			foreach ($permissions_to_add as $permission)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$crit->addInsert(self::PERMISSION_TYPE, $permission['permission_type']);
				$crit->addInsert(self::TARGET_ID, $permission['target_id']);
				$crit->addInsert($mode, $new_id);
				$crit->addInsert(self::ALLOWED, $permission['allowed']);
				$crit->addInsert(self::MODULE, $permission['module']);
				$res = $this->doInsert($crit);
			}
		}
		
	}
