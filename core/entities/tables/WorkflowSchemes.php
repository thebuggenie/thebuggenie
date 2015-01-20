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
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addOrderBy(self::ID, Criteria::SORT_ASC);

            $res = $this->select($crit);

            return $res;
        }

        public function getByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->doSelectById($id, $crit, false);
            return $row;
        }

        public function getFirstIdByScope($scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID, 'id');
            $crit->addWhere(self::SCOPE, $scope_id);
            $crit->addOrderBy(self::ID);
            $row = $this->doSelectOne($crit);
            return ($row) ? $row->get('id') : 0;
        }

    }