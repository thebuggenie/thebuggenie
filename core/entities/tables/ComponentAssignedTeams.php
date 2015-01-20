<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Component assigned teams table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Component assigned teams table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="componentassignedteams")
     */
    class ComponentAssignedTeams extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'componentassignedteams';
        const ID = 'componentassignedteams.id';
        const SCOPE = 'componentassignedteams.scope';
        const TEAM_ID = 'componentassignedteams.uid';
        const ROLE_ID = 'componentassignedteams.role_id';
        const COMPONENT_ID = 'componentassignedteams.component_id';
        
        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::COMPONENT_ID, Components::getTable());
            parent::_addForeignKeyColumn(self::TEAM_ID, Teams::getTable());
            parent::_addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }
        
        public function deleteByComponentID($component_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::COMPONENT_ID, $component_id);
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

        public function addTeamToComponent($component_id, $team, $role)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::COMPONENT_ID, $component_id);
            $crit->addWhere(self::TEAM_ID, $team->getID());
            if (!$this->doCount($crit))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::TEAM_ID, $team->getID());
                $crit->addInsert(self::COMPONENT_ID, $component_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $this->doInsert($crit);
                return true;
            }
            return false;
        }

        public function removeTeamFromComponent($team, $component_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::COMPONENT_ID, $component_id);
            $crit->addWhere(self::TEAM_ID, $team);
            $this->doDelete($crit);
        }

    }
