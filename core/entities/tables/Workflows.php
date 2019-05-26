<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Workflows table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Workflows table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="workflows")
     * @Entity(class="\thebuggenie\core\entities\Workflow")
     */
    class Workflows extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'workflows';
        const ID = 'workflows.id';
        const SCOPE = 'workflows.scope';
        const NAME = 'workflows.name';
        const DESCRIPTION = 'workflows.description';
        const IS_ACTIVE = 'workflows.is_active';

        public function getAll($scope_id = null)
        {
            $scope_id = ($scope_id === null) ? framework\Context::getScope()->getID() : $scope_id;
            $scope_id = (is_object($scope_id)) ? $scope_id->getID() : $scope_id;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);
            $query->addOrderBy(self::ID, \b2db\QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query, false);
            return $row;
        }

        public function countWorkflows($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);

            return $this->count($query);
        }

        public function getFirstIdByScope($scope_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'id');
            $query->where(self::SCOPE, $scope_id);
            $query->addOrderBy(self::ID);
            $row = $this->rawSelectOne($query);
            return ($row) ? $row->get('id') : 0;
        }

    }