<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use thebuggenie\core\entities\Project;

    /**
     * Permissions table
     *
     * @method static Permissions getTable()
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="permissions")
     */
    class Permissions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
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
        const ROLE_ID = 'permissions.role_id';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::PERMISSION_TYPE, 100);
            parent::_addVarchar(self::TARGET_ID, 200, 0);
            parent::_addBoolean(self::ALLOWED);
            parent::_addVarchar(self::MODULE, 50);
            parent::_addForeignKeyColumn(self::UID, Users::getTable());
            parent::_addForeignKeyColumn(self::GID, Groups::getTable());
            parent::_addForeignKeyColumn(self::TID, Teams::getTable());
            parent::_addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }
        
        protected function _setupIndexes()
        {
            $this->_addIndex('scope', array(self::SCOPE));
        }

        public function getAll($scope_id = null)
        {
            $scope_id = ($scope_id === null) ? framework\Context::getScope()->getID() : $scope_id;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope_id);
            $res = $this->doSelect($crit, 'none');
            return $res;
        }
        
        public function removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope, $role_id = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $uid);
            $crit->addWhere(self::GID, $gid);
            $crit->addWhere(self::TID, $tid);
            $crit->addWhere(self::MODULE, $module);
            $crit->addWhere(self::PERMISSION_TYPE, $permission_type);
            $crit->addWhere(self::TARGET_ID, $target_id);
            $crit->addWhere(self::SCOPE, $scope);
            if ($role_id !== null)
            {
                $crit->addWhere(self::ROLE_ID, $role_id);
            }
            
            $res = $this->doDelete($crit);
        }

        public function deleteAllPermissionsForCombination($uid, $gid, $tid, $target_id = 0, $role_id = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $uid);
            $crit->addWhere(self::GID, $gid);
            $crit->addWhere(self::TID, $tid);
            if ($target_id == 0)
            {
                $crit->addWhere(self::TARGET_ID, $target_id);
            }
            else
            {
                $ctn = $crit->returnCriterion(self::TARGET_ID, $target_id);
                $ctn->addOr(self::TARGET_ID, 0);
                $crit->addWhere($ctn);
            }
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if ($role_id !== null)
            {
                $crit->addWhere(self::ROLE_ID, $role_id);
            }

            $res = $this->doDelete($crit);
        }

        public function setPermission($uid, $gid, $tid, $allowed, $module, $permission_type, $target_id, $scope, $role_id = null)
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
            if ($role_id !== null)
            {
                $crit->addInsert(self::ROLE_ID, $role_id);
            }
            
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

        public function deleteRolePermissions($role_id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::SCOPE, $scope);
            $this->doDelete($crit);
        }

        /**
         * Removes the specified permission associated with the role.
         *
         * @param role_id Role ID.
         * @param module Module.
         * @param permission_type Permission type.
         * @param scope Scope. If null, current scope will be used.
         */
        public function deleteRolePermission($role_id, $module, $permission_type, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::MODULE, $module);
            $crit->addWhere(self::PERMISSION_TYPE, $permission_type);
            $crit->addWhere(self::SCOPE, $scope);
            $this->doDelete($crit);
        }

        public function deletePermissionsByRoleAndUser($role_id, $user_id, $scope)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope);
            $this->doDelete($crit);
        }

        public function deletePermissionsByRoleAndTeam($role_id, $team_id, $scope)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::TEAM_ID, $team_id);
            $crit->addWhere(self::SCOPE, $scope);
            $this->doDelete($crit);
        }

        public function loadFixtures(\thebuggenie\core\entities\Scope $scope, $admin_group_id, $guest_group_id)
        {
            $scope_id = $scope->getID();

            // Creating public searches, noone.
            $this->setPermission(0, 0, 0, false, 'core', 'cancreatepublicsearches', 0, $scope_id);

            // Common pages, everyone.
            $this->setPermission(0, 0, 0, true, 'core', 'page_home_access', 0, $scope_id);
            $this->setPermission(0, 0, 0, true, 'core', 'page_about_access', 0, $scope_id);
            $this->setPermission(0, 0, 0, true, 'core', 'page_search_access', 0, $scope_id);
            $this->setPermission(0, 0, 0, true, 'core', 'page_confirm_scope_access', 0, $scope_id);

            // Search for issues, everyone.
            $this->setPermission(0, 0, 0, true, 'core', 'canfindissues', 0, $scope_id);

            // Search for issues and save private searches, everyone except guests.
            $this->setPermission(0, 0, 0, true, 'core', 'canfindissuesandsavesearches', 0, $scope_id);
            $this->setPermission(0, $guest_group_id, 0, false, 'core', 'canfindissuesandsavesearches', 0, $scope_id);

            // Account page, everyone except guests.
            $this->setPermission(0, 0, 0, true, 'core', 'page_account_access', 0, $scope_id);
            $this->setPermission(0, $guest_group_id, 0, false, 'core', 'page_account_access', 0, $scope_id);

            // Global dashboard, everyone except guests.
            $this->setPermission(0, 0, 0, true, 'core', 'page_dashboard_access', 0, $scope_id);
            $this->setPermission(0, $guest_group_id, 0, false, 'core', 'page_dashboard_access', 0, $scope_id);

            // Explicit full access for administrator group.
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canaddextrainformationtoissues', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'cancreateandeditissues', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'cancreatepublicsearches', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'candeleteissues', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'candoscrumplanning', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'caneditissue', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'caneditissuecustomfields', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'caneditmainmenu', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canfindissues', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canfindissuesandsavesearches', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canlockandeditlockedissues', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canpostseeandeditallcomments', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canpostseeandeditallcomments', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'cansaveconfig', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canseeproject', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canseetimespent', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'canvoteforissues', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_about_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_account_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_clientlist_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_confirm_scope_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_dashboard_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_home_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_project_allpages_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_search_access', 0, $scope_id);
            $this->setPermission(0, $admin_group_id, 0, true, 'core', 'page_teamlist_access', 0, $scope_id);
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
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $crit->addInsert(self::PERMISSION_TYPE, $permission['permission_type']);
                $crit->addInsert(self::TARGET_ID, $permission['target_id']);
                $crit->addInsert($mode, $new_id);
                $crit->addInsert(self::ALLOWED, $permission['allowed']);
                $crit->addInsert(self::MODULE, $permission['module']);
                $res = $this->doInsert($crit);
            }
        }

        /**
         * Adds permission for the specified role to permission table based on
         * user and group role memberships in projects.
         *
         * @param role Role to which permission should be granted.
         * @param rolepermission Role permission to grant.
         */
        public function addRolePermission(\thebuggenie\core\entities\Role $role, \thebuggenie\core\entities\RolePermission $rolepermission)
        {
            // NOTE: When updating this method, make sure to update both user
            // and team-specific code. They are reperatitive, but kept separate
            // for clarity.

            // Retrieve user assignments based on role.
            $assigned_users = ProjectAssignedUsers::getTable()->getAssignmentsByRoleID($role->getID());

            // Iterate over assignments.
            foreach ($assigned_users as $assigned_user)
            {
                // Extract project entity.
                $project_id = $assigned_user->get(ProjectAssignedUsers::PROJECT_ID);
                $projects = Project::getAllByIDs(array($project_id));
                if ( ! isset($projects[$project_id])) {
                    continue;
                }
                $project = $projects[$project_id];

                // Determine values that need to be inserted.
                $target_id = $rolepermission->getExpandedTargetIDForProject($project);
                $user_id = $assigned_user->get(ProjectAssignedUsers::USER_ID);
                $module = $rolepermission->getModule();
                $role_id = $role->getID();
                $permission_type = $rolepermission->getPermission();
                $scope_id = framework\Context::getScope()->getID();

                // Determine if permission already exists.
                $crit = $this->getCriteria();
                $crit->addWhere(self::SCOPE, $scope_id);
                $crit->addWhere(self::PERMISSION_TYPE, $permission_type);
                $crit->addWhere(self::TARGET_ID, $target_id);
                $crit->addWhere(self::UID, $user_id);
                $crit->addWhere(self::ALLOWED, true);
                $crit->addWhere(self::MODULE, $module);
                $crit->addWhere(self::ROLE_ID, $role_id);
                $res = $this->doSelect($crit, 'none');

                // If permission does not exist, add it.
                if (!$res)
                {
                    $crit = $this->getCriteria();
                    $crit->addInsert(self::SCOPE, $scope_id);
                    $crit->addInsert(self::PERMISSION_TYPE, $permission_type);
                    $crit->addInsert(self::TARGET_ID, $target_id);
                    $crit->addInsert(self::UID, $user_id);
                    $crit->addInsert(self::ALLOWED, true);
                    $crit->addInsert(self::MODULE, $module);
                    $crit->addInsert(self::ROLE_ID, $role_id);
                    $this->doInsert($crit);
                }
            }

            // Retrieve team assignments based on role.
            $assigned_teams = ProjectAssignedTeams::getTable()->getAssignmentsByRoleID($role->getID());

            // Iterate over assignments.
            foreach ($assigned_teams as $assigned_team)
            {
                // Extract project entity.
                $project_id = $assigned_team->get(ProjectAssignedTeams::PROJECT_ID);
                $project = Project::getAllByIDs(array($project_id))[$project_id];

                // Determine values that need to be inserted.
                $target_id = $rolepermission->getExpandedTargetIDForProject($project);
                $team_id = $assigned_team->get(ProjectAssignedTeams::TEAM_ID);
                $module = $rolepermission->getModule();
                $role_id = $role->getID();
                $permission_type = $rolepermission->getPermission();
                $scope_id = framework\Context::getScope()->getID();

                // Determine if permission already exists.
                $crit = $this->getCriteria();
                $crit->addWhere(self::SCOPE, $scope_id);
                $crit->addWhere(self::PERMISSION_TYPE, $permission_type);
                $crit->addWhere(self::TARGET_ID, $target_id);
                $crit->addWhere(self::TID, $team_id);
                $crit->addWhere(self::ALLOWED, true);
                $crit->addWhere(self::MODULE, $module);
                $crit->addWhere(self::ROLE_ID, $role_id);
                $res = $this->doSelect($crit, 'none');

                // If permission does not exist, add it.
                if (!$res)
                {
                    $crit = $this->getCriteria();
                    $crit->addInsert(self::SCOPE, $scope_id);
                    $crit->addInsert(self::PERMISSION_TYPE, $permission_type);
                    $crit->addInsert(self::TARGET_ID, $target_id);
                    $crit->addInsert(self::TID, $team_id);
                    $crit->addInsert(self::ALLOWED, true);
                    $crit->addInsert(self::MODULE, $module);
                    $crit->addInsert(self::ROLE_ID, $role_id);
                    $this->doInsert($crit);
                }
            }
        }

        public function getByPermissionTargetIDAndModule($permission, $target_id, $module = 'core')
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PERMISSION_TYPE, $permission);
            $crit->addWhere(self::TARGET_ID, $target_id);
            $crit->addWhere(self::MODULE, $module);

            $permissions = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $target = null;
                    if ($uid = $row->get(self::UID))
                    {
                        $target = \thebuggenie\core\entities\User::getB2DBTable()->selectById($uid);
                    }
                    if ($tid = $row->get(self::TID))
                    {
                        $target = \thebuggenie\core\entities\Team::getB2DBTable()->selectById($tid);
                    }
                    if ($gid = $row->get(self::GID))
                    {
                        $target = \thebuggenie\core\entities\Group::getB2DBTable()->selectById($gid);
                    }
                    if ($target instanceof \thebuggenie\core\entities\common\Identifiable)
                    {
                        $permissions[] = array('target' => $target, 'allowed' => (boolean) $row->get(self::ALLOWED), 'user_id' => $row->get(self::UID), 'team_id' => $row->get(self::TID), 'group_id' => $row->get(self::GID));
                    }
                }
            }
            return $permissions;
        }
        
        public function deleteByPermissionTargetIDAndModule($permission, $target_id, $module = 'core')
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PERMISSION_TYPE, $permission);
            $crit->addWhere(self::TARGET_ID, $target_id);
            $crit->addWhere(self::MODULE, $module);
            $this->doDelete($crit);
        }

    }
