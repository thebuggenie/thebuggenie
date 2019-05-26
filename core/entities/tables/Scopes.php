<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Table;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Scopes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Scopes table
     *
     * @package thebuggenie
     * @subpackage tables
     * 
     * @method static Scopes getTable()
     *
     * @Entity(class="\thebuggenie\core\entities\Scope")
     * @Table(name="scopes")
     */
    class Scopes extends Table
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'scopes';
        const ID = 'scopes.id';
        const ENABLED = 'scopes.enabled';
        const CUSTOM_WORKFLOWS_ENABLED = 'scopes.custom_workflows_enabled';
        const MAX_WORKFLOWS = 'scopes.max_workflows';
        const UPLOADS_ENABLED = 'scopes.uploads_enabled';
        const MAX_UPLOAD_LIMIT = 'scopes.max_upload_limit';
        const MAX_USERS = 'scopes.max_users';
        const MAX_TEAMS = 'scopes.max_teams';
        const MAX_PROJECTS = 'scopes.max_projects';
        const DESCRIPTION = 'scopes.description';
        const NAME = 'scopes.name';
        const ADMINISTRATOR = 'scopes.administrator';

        public function getByHostname($hostname)
        {
            $query = $this->getQuery();
            $query->join(ScopeHostnames::getTable(), ScopeHostnames::SCOPE_ID, self::ID);
            $query->where(ScopeHostnames::HOSTNAME, $hostname);
            $row = $this->rawSelectOne($query);
            return $row;
        }

        public function getByIds($ids)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $ids, \b2db\Criterion::IN);

            return $this->select($query);
        }

        public function getPaginationItems()
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::NAME, \b2db\QueryColumnSort::SORT_ASC);
            $query->indexBy(self::ID);

            $res = $this->rawSelect($query);
            $scope_ids = [];
            
            while ($row = $res->getNextRow()) {
                $scope_ids[] = $row[self::ID];
            }
            
            return $scope_ids;
        }

        public function getDefault()
        {
            return $this->rawSelectById(1);
        }

        public function getByHostnameOrDefault($hostname = null)
        {
            $query = $this->getQuery();
            if ($hostname !== null)
            {
                $query->join(ScopeHostnames::getTable(), ScopeHostnames::SCOPE_ID, self::ID);
                $query->where(ScopeHostnames::HOSTNAME, $hostname);
                $query->or(self::ID, 1);
                $query->addOrderBy(self::ID, 'desc');
            }
            else
            {
                $query->where(self::ID, 1);
            }

            return $this->selectOne($query);
        }

    }
