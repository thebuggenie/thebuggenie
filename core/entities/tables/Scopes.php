<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;
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
     * @Entity(class="\thebuggenie\core\entities\Scope")
     * @Table(name="scopes")
     */
    class Scopes extends ScopedTable
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
            $crit = $this->getCriteria();
            $crit->addJoin(ScopeHostnames::getTable(), ScopeHostnames::SCOPE_ID, self::ID);
            $crit->addWhere(ScopeHostnames::HOSTNAME, $hostname);
            $row = $this->doSelectOne($crit);
            return $row;
        }

        public function getDefault()
        {
            return $this->doSelectById(1);
        }

        public function getByHostnameOrDefault($hostname = null)
        {
            $crit = $this->getCriteria();
            if ($hostname !== null)
            {
                $crit->addJoin(ScopeHostnames::getTable(), ScopeHostnames::SCOPE_ID, self::ID);
                $crit->addWhere(ScopeHostnames::HOSTNAME, $hostname);
                $crit->addOr(self::ID, 1);
                $crit->addOrderBy(self::ID, 'desc');
            }
            else
            {
                $crit->addWhere(self::ID, 1);
            }

            return $this->selectOne($crit);
        }

    }
