<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGRole extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::ROLE;

		/**
		 * @Relates(collection=true, joinclass="TBGRolePermissionsTable", foreign_column="role_id", column="permission")
		 */
		protected $_permissions = null;

		public static function loadFixtures(TBGScope $scope)
		{
			$roles = array();
			$roles['Developer'] = array();
			$roles['Project manager'] = array();
			$roles['Tester'] = array();
			$roles['Documentation editor'] = array();
			
			foreach ($roles as $name => $permissions)
			{
				$role = new TBGRole();
				$role->setName($name);
				$role->setScope($scope);
				$role->save();
				$role->setPermissions($permissions);
			}
		}
		
		/**
		 * Returns all project roles available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemTypeAndItemdata(self::ROLE, null);
		}

		public static function getByProjectID($project_id)
		{
			return TBGListTypesTable::getTable()->getAllByItemTypeAndItemdata(self::ROLE, $project_id);
		}

		protected function _preDelete()
		{
			TBGProjectAssignedTeamsTable::getTable()->deleteByRoleID($this->getID());
			TBGProjectAssignedUsersTable::getTable()->deleteByRoleID($this->getID());
			TBGEditionAssignedTeamsTable::getTable()->deleteByRoleID($this->getID());
			TBGEditionAssignedUsersTable::getTable()->deleteByRoleID($this->getID());
			TBGComponentAssignedTeamsTable::getTable()->deleteByRoleID($this->getID());
			TBGComponentAssignedUsersTable::getTable()->deleteByRoleID($this->getID());
		}

		public function isSystemRole()
		{
			return !(bool) $this->getItemdata();
		}

		public function getProject()
		{
			return ($this->getItemdata()) ? TBGContext::factory()->TBGProject((int) $this->getItemdata()) : null;
		}

		public function setProject($project)
		{
			$this->setItemdata((is_object($project)) ? $project->getID() : $project);
		}

		protected function _populatePermissions()
		{
			if ($this->_permissions === null)
			{
				$this->_b2dbLazyload('_permissions');
			}
		}

		public function setPermissions($permissions)
		{
			TBGRolePermissionsTable::getTable()->clearPermissionsForRole($this->getID());
			foreach ($permissions as $permission)
			{
				TBGRolePermissionsTable::getTable()->addPermissionForRole($this->getID(), $permission);
			}
		}

		public function getPermissions()
		{
			$this->_populatePermissions();
			return $this->_permissions;
		}

		public function hasPermission($permission_key)
		{
			$permissions = $this->getPermissions();
			return in_array($permission_key, $permissions);
		}

	}
