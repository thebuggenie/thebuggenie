<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Issue types table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issue types table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuetypes")
     * @Entity(class="\thebuggenie\core\entities\Issuetype")
     */
    class IssueTypes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'issuetypes';
        const ID = 'issuetypes.id';
        const SCOPE = 'issuetypes.scope';
        const NAME = 'issuetypes.name';
        const DESCRIPTION = 'issuetypes.description';
        const ICON = 'issuetypes.icon';
        const TASK = 'issuetypes.task';

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->select($crit);
        }

        public function getAllIDsByScopeID($scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope_id);
            $crit->addSelectionColumn(self::ID, 'id');
            $res = $this->doSelect($crit);

            $ids = array();
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $id = $row->get('id');
                    $ids[$id] = $id;
                }
            }

            return $ids;
        }

        public function getBugReportTypeIDs()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ICON, 'bug_report');
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doSelect($crit);

            $retarr = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $retarr[] = $row->get(self::ID);
                }
            }

            return $retarr;
        }

    }
