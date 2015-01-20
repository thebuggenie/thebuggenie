<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Edition assigned teams table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Edition assigned teams table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="editionassignedteams")
     */
    class EditionAssignedTeams extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'editionassignedteams';
        const ID = 'editionassignedteams.id';
        const SCOPE = 'editionassignedteams.scope';
        const TEAM_ID = 'editionassignedteams.uid';
        const ROLE_ID = 'editionassignedteams.role_id';
        const EDITION_ID = 'editionassignedteams.edition_id';
        
        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::EDITION_ID, Editions::getTable());
            parent::_addForeignKeyColumn(self::TEAM_ID, Teams::getTable());
            parent::_addForeignKeyColumn(self::ROLE_ID, ListTypes::getTable());
        }
        
        public function deleteByEditionID($edition_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::EDITION_ID, $edition_id);
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

        public function addTeamToEdition($edition_id, $team, $role)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::EDITION_ID, $edition_id);
            $crit->addWhere(self::TEAM_ID, $team->getID());
            if (!$this->doCount($crit))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::TEAM_ID, $team->getID());
                $crit->addInsert(self::EDITION_ID, $edition_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $this->doInsert($crit);
                return true;
            }
            return false;
        }

        public function removeTeamFromEdition($team, $edition_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::EDITION_ID, $edition_id);
            $crit->addWhere(self::TEAM_ID, $team);
            $this->doDelete($crit);
        }

    }
