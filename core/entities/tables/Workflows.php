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
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope_id);
            $crit->addOrderBy(self::ID, Criteria::SORT_ASC);

            return $this->select($crit);
        }

        public function getByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->doSelectById($id, $crit, false);
            return $row;
        }

        public function countWorkflows($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);

            return $this->doCount($crit);
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