<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\Team;
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
     * @method Team selectById()
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
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ONDEMAND, false);
            $query->addOrderBy('teams.name', \b2db\QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function doesTeamNameExist($team_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $team_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->count($query);
        }

        public function doesIDExist($id)
        {
            $query = $this->getQuery();
            $query->where(self::ONDEMAND, false);
            $query->where(self::ID, $id);
            return $this->count($query);
        }

        public function quickfind($team_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, "%{$team_name}%", \b2db\Criterion::LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ONDEMAND, false);

            return $this->select($query);
        }

        public function countTeams()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ONDEMAND, false);

            return $this->count($query);
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope_ondemand', array(self::SCOPE, self::ONDEMAND));
        }

    }
