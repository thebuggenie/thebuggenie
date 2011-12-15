<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGRole extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::ROLE;

		/**
		 * @Relates(class="TBGRolePermission", collection=true, foreign_column="role_id")
		 */
		protected $_permissions = null;

		public static function loadFixtures(TBGScope $scope)
		{
			$roles = array();
			$roles['Developer'] = array(
				array('permission' => 'page_project_allpages_access'),
				array('permission' => 'canseeproject'),
				array('permission' => 'canseeprojecthierarchy'),
				array('permission' => 'candoscrumplanning'),
				array('permission' => 'canvoteforissues'),
				array('permission' => 'canlockandeditlockedissues'),
				array('permission' => 'cancreateandeditissues'),
				array('permission' => 'caneditissue'),
				array('permission' => 'caneditissuecustomfields'),
				array('permission' => 'canaddextrainformationtoissues'),
				array('permission' => 'canpostseeandeditallcomments'),
				array('permission' => 'readarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
				array('permission' => 'editarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
				array('permission' => 'deletearticle', 'module' => 'publish', 'target_id' => '%project_key%'),
			);
			$roles['Project manager'] = array(
				array('permission' => 'page_project_allpages_access'),
				array('permission' => 'canseeproject'),
				array('permission' => 'canseeprojecthierarchy'),
				array('permission' => 'candoscrumplanning'),
				array('permission' => 'canvoteforissues'),
				array('permission' => 'canlockandeditlockedissues'),
				array('permission' => 'cancreateandeditissues'),
				array('permission' => 'caneditissue'),
				array('permission' => 'caneditissuecustomfields'),
				array('permission' => 'canaddextrainformationtoissues'),
				array('permission' => 'canpostseeandeditallcomments'),
				array('permission' => 'readarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
				array('permission' => 'editarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
				array('permission' => 'deletearticle', 'module' => 'publish', 'target_id' => '%project_key%'),
			);
			$roles['Tester'] = array(
				array('permission' => 'page_project_allpages_access'),
				array('permission' => 'canseeproject'),
				array('permission' => 'canseeprojecthierarchy'),
				array('permission' => 'canvoteforissues'),
				array('permission' => 'cancreateandeditissues'),
				array('permission' => 'caneditissuecustomfields'),
				array('permission' => 'canaddextrainformationtoissues'),
				array('permission' => 'canpostandeditcomments'),
				array('permission' => 'readarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
				array('permission' => 'editarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
			);
			$roles['Documentation editor'] = array(
				array('permission' => 'page_project_allpages_access'),
				array('permission' => 'canseeproject'),
				array('permission' => 'canseeprojecthierarchy'),
				array('permission' => 'canvoteforissues'),
				array('permission' => 'cancreateandeditissues'),
				array('permission' => 'canaddextrainformationtoissues'),
				array('permission' => 'canpostandeditcomments'),
				array('permission' => 'readarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
				array('permission' => 'editarticle', 'module' => 'publish', 'target_id' => '%project_key%'),
			);
			
			foreach ($roles as $name => $permissions)
			{
				$role = new TBGRole();
				$role->setName($name);
				$role->setScope($scope);
				$role->save();
				foreach ($permissions as $k => $permission)
				{
					$p = new TBGRolePermission();
					$p->setPermission($permission['permission']);

					if (array_key_exists('target_id', $permission)) $p->setTargetID($permission['target_id']);
					if (array_key_exists('module', $permission)) $p->setModule($permission['module']);
					
					$permissions[$k] = $p;
				}
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

		/**
		 * Returns all project roles available for a specific project
		 *
		 * @return array
		 */
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
				$permission->setRole($this);
				$permission->save();
			}
		}

		public function getPermissions()
		{
			$this->_populatePermissions();
			return $this->_permissions;
		}

		public function hasPermission($permission_key, $module, $target_id = null)
		{
			foreach ($this->getPermissions() as $role_permission)
			{
				if ($role_permission->getPermission() == $permission_key && $role_permission->getModule() == $module && $role_permission->getTargetID() == $target_id) return true;
			}

			return false;
		}

	}
