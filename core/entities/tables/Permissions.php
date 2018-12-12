<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria;
    use b2db\Insertion;
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::PERMISSION_TYPE, 100);
            parent::addVarchar(self::TARGET_ID, 200, 0);
            parent::addBoolean(self::ALLOWED);
            parent::addVarchar(self::MODULE, 50);
            parent::addForeignKeyColumn(self::UID, Users::getTable());
            parent::addForeignKeyColumn(self::GID, Groups::getTable());
            parent::addForeignKeyColumn(self::TID, Teams::getTable());
            parent::addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }
        
        protected function setupIndexes()
        {
            $this->addIndex('scope', array(self::SCOPE));
        }

        public function getAll($scope_id = null)
        {
            $scope_id = ($scope_id === null) ? framework\Context::getScope()->getID() : $scope_id;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);
            $res = $this->rawSelect($query, 'none');
            return $res;
        }
        
        public function removeSavedPermission($uid, $gid, $tid, $module, $permission_type, $target_id, $scope, $role_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $uid);
            $query->where(self::GID, $gid);
            $query->where(self::TID, $tid);
            $query->where(self::MODULE, $module);
            $query->where(self::PERMISSION_TYPE, $permission_type);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::SCOPE, $scope);
            if ($role_id !== null)
            {
                $query->where(self::ROLE_ID, $role_id);
            }
            
            $res = $this->rawDelete($query);
        }

        public function deleteAllPermissionsForCombination($uid, $gid, $tid, $target_id = 0, $role_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $uid);
            $query->where(self::GID, $gid);
            $query->where(self::TID, $tid);
            if ($target_id == 0)
            {
                $query->where(self::TARGET_ID, $target_id);
            }
            else
            {
                $criteria = new Criteria();
                $criteria->where(self::TARGET_ID, $target_id);
                $criteria->or(self::TARGET_ID, 0);
                $query->and($criteria);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($role_id !== null)
            {
                $query->where(self::ROLE_ID, $role_id);
            }

            $res = $this->rawDelete($query);
        }

        public function setPermission($uid, $gid, $tid, $allowed, $module, $permission_type, $target_id, $scope, $role_id = null)
        {
            $insertion = new Insertion();
            $insertion->add(self::UID, (int) $uid);
            $insertion->add(self::GID, (int) $gid);
            $insertion->add(self::TID, (int) $tid);
            $insertion->add(self::ALLOWED, $allowed);
            $insertion->add(self::MODULE, $module);
            $insertion->add(self::PERMISSION_TYPE, $permission_type);
            $insertion->add(self::TARGET_ID, $target_id);
            $insertion->add(self::SCOPE, $scope);
            if ($role_id !== null)
            {
                $insertion->add(self::ROLE_ID, $role_id);
            }
            
            $res = $this->rawInsert($insertion);
            return $res->getInsertID();
        }

        public function deleteModulePermissions($module_name, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::MODULE, $module_name);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deleteRolePermissions($role_id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
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
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::MODULE, $module);
            $query->where(self::PERMISSION_TYPE, $permission_type);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deletePermissionsByRoleAndUser($role_id, $user_id, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deletePermissionsByRoleAndTeam($role_id, $team_id, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::ROLE_ID, $role_id);
            $query->where(self::TEAM_ID, $team_id);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
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
            $query = $this->getQuery();
            switch ($mode)
            {
                case 'group':
                    $mode = self::GID;
                    break;
                case 'team':
                    $mode = self::TID;
                    break;
            }
            $query->where($mode, $cloned_id);
            $permissions_to_add = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $permissions_to_add[] = array('target_id' => $row->get(self::TARGET_ID), 'permission_type' => $row->get(self::PERMISSION_TYPE), 'allowed' => $row->get(self::ALLOWED), 'module' => $row->get(self::MODULE));
                }
            }

            foreach ($permissions_to_add as $permission)
            {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $insertion->add(self::PERMISSION_TYPE, $permission['permission_type']);
                $insertion->add(self::TARGET_ID, $permission['target_id']);
                $insertion->add($mode, $new_id);
                $insertion->add(self::ALLOWED, $permission['allowed']);
                $insertion->add(self::MODULE, $permission['module']);
                $res = $this->rawInsert($insertion);
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
                $query = $this->getQuery();
                $query->where(self::SCOPE, $scope_id);
                $query->where(self::PERMISSION_TYPE, $permission_type);
                $query->where(self::TARGET_ID, $target_id);
                $query->where(self::UID, $user_id);
                $query->where(self::ALLOWED, true);
                $query->where(self::MODULE, $module);
                $query->where(self::ROLE_ID, $role_id);
                $res = $this->rawSelect($query, 'none');

                // If permission does not exist, add it.
                if (!$res)
                {
                    $insertion = new Insertion();
                    $insertion->add(self::SCOPE, $scope_id);
                    $insertion->add(self::PERMISSION_TYPE, $permission_type);
                    $insertion->add(self::TARGET_ID, $target_id);
                    $insertion->add(self::UID, $user_id);
                    $insertion->add(self::ALLOWED, true);
                    $insertion->add(self::MODULE, $module);
                    $insertion->add(self::ROLE_ID, $role_id);
                    $this->rawInsert($insertion);
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
                $query = $this->getQuery();
                $query->where(self::SCOPE, $scope_id);
                $query->where(self::PERMISSION_TYPE, $permission_type);
                $query->where(self::TARGET_ID, $target_id);
                $query->where(self::TID, $team_id);
                $query->where(self::ALLOWED, true);
                $query->where(self::MODULE, $module);
                $query->where(self::ROLE_ID, $role_id);
                $res = $this->rawSelect($query, 'none');

                // If permission does not exist, add it.
                if (!$res)
                {
                    $insertion = new Insertion();
                    $insertion->add(self::SCOPE, $scope_id);
                    $insertion->add(self::PERMISSION_TYPE, $permission_type);
                    $insertion->add(self::TARGET_ID, $target_id);
                    $insertion->add(self::TID, $team_id);
                    $insertion->add(self::ALLOWED, true);
                    $insertion->add(self::MODULE, $module);
                    $insertion->add(self::ROLE_ID, $role_id);
                    $this->rawInsert($insertion);
                }
            }
        }

        public function getByPermissionTargetIDAndModule($permission, $target_id, $module = 'core')
        {
            $query = $this->getQuery();
            $query->where(self::PERMISSION_TYPE, $permission);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);

            $permissions = array();
            if ($res = $this->rawSelect($query))
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
            $query = $this->getQuery();
            $query->where(self::PERMISSION_TYPE, $permission);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::MODULE, $module);
            $this->rawDelete($query);
        }

    }
