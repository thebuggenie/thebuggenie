<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Workflow schemes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Workflow schemes table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="workflow_schemes")
     * @Entity(class="\thebuggenie\core\entities\WorkflowScheme")
     */
    class WorkflowSchemes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'workflow_schemes';
        const ID = 'workflow_schemes.id';
        const SCOPE = 'workflow_schemes.scope';
        const NAME = 'workflow_schemes.name';
        const DESCRIPTION = 'workflow_schemes.description';

        public function getAll($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->addOrderBy(self::ID, \b2db\QueryColumnSort::SORT_ASC);

            $res = $this->select($query);

            return $res;
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query, false);
            return $row;
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