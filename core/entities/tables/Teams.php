<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Teams table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Teams table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="teams")
     * @Entity(class="\thebuggenie\core\entities\Team")
     */
    class Teams extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'teams';
        const ID = 'teams.id';
        const SCOPE = 'teams.scope';
        const NAME = 'teams.name';
        const ONDEMAND = 'teams.ondemand';

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ONDEMAND, false);
            $crit->addOrderBy('teams.name', Criteria::SORT_ASC);

            return $this->select($crit);
        }

        public function doesTeamNameExist($team_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $team_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->doCount($crit);
        }

        public function doesIDExist($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ONDEMAND, false);
            $crit->addWhere(self::ID, $id);
            return $this->doCount($crit);
        }

        public function quickfind($team_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, "%{$team_name}%", Criteria::DB_LIKE);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ONDEMAND, false);

            return $this->select($crit);
        }

        public function countTeams()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ONDEMAND, false);

            return $this->doCount($crit);
        }

    }
