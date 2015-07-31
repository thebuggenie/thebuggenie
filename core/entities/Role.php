<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Role extends Datatype 
    {

        const ITEMTYPE = Datatype::ROLE;

        protected static $_items = null;
        
        protected $_itemtype = Datatype::ROLE;

        /**
         * @Relates(class="\thebuggenie\core\entities\RolePermission", collection=true, foreign_column="role_id")
         */
        protected $_permissions = null;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
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
                $role = new \thebuggenie\core\entities\Role();
                $role->setName($name);
                $role->setScope($scope);
                $role->save();
                foreach ($permissions as $k => $permission)
                {
                    $p = new \thebuggenie\core\entities\RolePermission();
                    $p->setPermission($permission['permission']);

                    if (array_key_exists('target_id', $permission)) $p->setTargetID($permission['target_id']);
                    if (array_key_exists('module', $permission)) $p->setModule($permission['module']);
                    
                    $role->addPermission($p);
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
            return tables\ListTypes::getTable()->getAllByItemTypeAndItemdata(self::ROLE, null);
        }

        /**
         * Returns all project roles available for a specific project
         *
         * @return array
         */
        public static function getByProjectID($project_id)
        {
            return tables\ListTypes::getTable()->getAllByItemTypeAndItemdata(self::ROLE, $project_id);
        }

        protected function _preDelete()
        {
            tables\RolePermissions::getTable()->clearPermissionsForRole($this->getID());
            tables\ProjectAssignedTeams::getTable()->deleteByRoleID($this->getID());
            tables\ProjectAssignedUsers::getTable()->deleteByRoleID($this->getID());
        }

        public function isSystemRole()
        {
            return !(bool) $this->getItemdata();
        }

        /**
         * Return the associated project if any
         * 
         * @return Project
         */
        public function getProject()
        {
            return ($this->getItemdata()) ? \thebuggenie\core\entities\Project::getB2DBTable()->selectById((int) $this->getItemdata()) : null;
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

        /**
         * Removes a set of permissions
         *
         * @param array|\thebuggenie\core\entities\RolePermission $permissions
         */
        public function removePermissions($permissions)
        {
            foreach ($permissions as $permission)
            {
                $this->removePermission($permission);
            }
        }

        public function removePermission(\thebuggenie\core\entities\RolePermission $permission)
        {
            $this->_populatePermissions();
            $permission_id = $permission->getID();
            unset($this->_permissions[$permission_id]);
            tables\Permissions::getTable()->deleteRolePermission($this->getID(), $permission->getPermission(), $permission->getExpandedTargetID($this));
            $permission->delete();
        }

        public function addPermissions($permissions)
        {
            foreach ($permissions as $permission)
            {
                $this->addPermission($permission);
            }
        }

        public function addPermission(\thebuggenie\core\entities\RolePermission $permission)
        {
            $permission->setRole($this);
            $permission->save();
            if ($this->_permissions !== null)
            {
                $this->_permissions[$permission->getID()] = $permission;
            }
            tables\Permissions::getTable()->addRolePermission($this, $permission);
        }

        /**
         * Returns all permissions assigned to this role
         * 
         * @return array|\thebuggenie\core\entities\RolePermission An array of all permissions
         */
        public function getPermissions()
        {
            $this->_populatePermissions();
            return $this->_permissions;
        }

        public function hasPermission($permission_key, $module = 'core', $target_id = null)
        {
            foreach ($this->getPermissions() as $role_permission)
            {
                if ($role_permission->getPermission() == $permission_key && $role_permission->getModule() == $module && $role_permission->getTargetID() == $target_id) return true;
            }

            return false;
        }

    }
