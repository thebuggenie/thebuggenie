<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\Project;

    /**
     * Project assigned users table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Project assigned users table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="projectassignedusers")
     */
    class ProjectAssignedUsers extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'projectassignedusers';
        const ID = 'projectassignedusers.id';
        const SCOPE = 'projectassignedusers.scope';
        const USER_ID = 'projectassignedusers.uid';
        const PROJECT_ID = 'projectassignedusers.project_id';
        const ROLE_ID = 'projectassignedusers.role_id';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::PROJECT_ID, Projects::getTable());
            parent::_addForeignKeyColumn(self::USER_ID, Users::getTable());
            parent::_addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }

        public function deleteByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $res = $this->doDelete($crit);
            return $res;
        }

        public function deleteByRoleID($role_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ROLE_ID, $role_id);
            $res = $this->doDelete($crit);
            return $res;
        }

        public function addUserToProject($project_id, $user_id, $role_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::ROLE_ID, $role_id);
            if (!$this->doCount($crit))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::PROJECT_ID, $project_id);
                $crit->addInsert(self::USER_ID, $user_id);
                $crit->addInsert(self::ROLE_ID, $role_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $this->doInsert($crit);
                return true;
            }
            return false;
        }

        public function getProjectsByUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::PROJECT_ID, 'pid');
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $projects = array();

            if ($res = $this->doSelect($crit, 'none'))
            {
                while ($row = $res->getNextRow())
                {
                    $pid = $row['pid'];
                    if (!array_key_exists($pid, $projects))
                        $projects[$pid] = new Project($pid);
                }
            }

            return $projects;
        }

        public function removeUserFromProject($project_id, $user)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::USER_ID, $user);
            $this->doDelete($crit);
        }

        public function getRolesForProject($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $res = $this->doSelect($crit);

            $roles = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $roles[$row->get(self::USER_ID)][] = new \thebuggenie\core\entities\Role($row->get(self::ROLE_ID));
                }
            }

            return $roles;
        }

        public function getUsersByRoleID($role_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::USER_ID, 'uid');
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $users = array();

            if ($res = $this->doSelect($crit, 'none'))
            {
                while ($row = $res->getNextRow())
                {
                    $uid = $row['uid'];
                    if (!array_key_exists($uid, $users))
                        $users[$uid] = new \thebuggenie\core\entities\User($uid);
                }
            }

            return $users;
        }

    }
