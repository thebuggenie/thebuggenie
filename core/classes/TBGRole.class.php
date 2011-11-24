<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGRole extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::ROLE;

		/**
		 * @Relates(collection=true, joinclass="TBGRolePermissionsTable", foreign_column="permission")
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
				foreach ($permissions as $permission)
				{
					$this->getB2DBTable()->addPermissionToRole($this->getID(), $permission);
				}
			}
		}
		
		/**
		 * Returns all project roles available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemType(self::ROLE);
		}

		public static function getByProjectID($project_id)
		{
			return TBGListTypesTable::getTable()->getAllByItemTypeAndItemdata(self::ROLE, $project_id);
		}

		public function isSystemRole()
		{
			return false;
			return !(bool) $this->getItemdata();
		}

		public function getProject()
		{
			return TBGContext::getFactory()->TBGProject((int) $this->getItemdata());
		}

		protected function _populatePermissions()
		{
			if ($this->_permissions === null)
			{
				$this->_b2dbLazyload('_permissions');
			}
		}

		public function getPermissions()
		{
			$this->_populatePermissions();
			return $this->_permissions;
		}

	}
