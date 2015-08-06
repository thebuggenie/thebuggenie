<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Project assigned teams table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Project assigned teams table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="projectassignedteams")
     */
    class ProjectAssignedTeams extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'projectassignedteams';
        const ID = 'projectassignedteams.id';
        const SCOPE = 'projectassignedteams.scope';
        const TEAM_ID = 'projectassignedteams.uid';
        const PROJECT_ID = 'projectassignedteams.project_id';
        const ROLE_ID = 'projectassignedteams.role_id';
        
        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::PROJECT_ID, Projects::getTable());
            parent::_addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
            parent::_addForeignKeyColumn(self::TEAM_ID, Teams::getTable());
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

        public function addTeamToProject($project_id, $team_id, $role_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::TEAM_ID, $team_id);
            $crit->addWhere(self::ROLE_ID, $role_id);
            if (!$this->doCount($crit))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::PROJECT_ID, $project_id);
                $crit->addInsert(self::TEAM_ID, $team_id);
                $crit->addInsert(self::ROLE_ID, $role_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $this->doInsert($crit);
                return true;
            }
            return false;
        }

        public function removeTeamFromProject($project_id, $team)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::TEAM_ID, $team);
            $this->doDelete($crit);
        }
        
        public function getProjectsByTeamID($team)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::TEAM_ID, $team);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doSelect($crit);
            
            $projects = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $pid = $row->get(self::PROJECT_ID);
                    $projects[$pid] = $pid;
                }
            }
            
            return $projects;
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
                    $roles[$row->get(self::TEAM_ID)][] = new \thebuggenie\core\entities\Role($row->get(self::ROLE_ID));
                }
            }

            return $roles;
        }

        public function getTeamsByRoleID($role_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::TEAM_ID, 'tid');
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $teams = array();

            if ($res = $this->doSelect($crit, 'none'))
            {
                while ($row = $res->getNextRow())
                {
                    $tid = $row['tid'];
                    if (!array_key_exists($tid, $teams))
                        $teams[$tid] = new \thebuggenie\core\entities\Team($tid);
                }
            }

            return $teams;
        }

        public function getTeamsByRoleIDAndProjectID($role_id, $project_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::TEAM_ID, 'tid');
            $crit->addWhere(self::ROLE_ID, $role_id);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $teams = array();

            if ($res = $this->doSelect($crit, 'none'))
            {
                while ($row = $res->getNextRow())
                {
                    $tid = $row['tid'];
                    if (!array_key_exists($tid, $teams))
                        $teams[$tid] = new \thebuggenie\core\entities\Team($tid);
                }
            }

            return $teams;
        }

    }
