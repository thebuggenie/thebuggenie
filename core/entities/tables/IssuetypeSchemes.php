<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Issuetype schemes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issuetype schemes table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuetype_schemes")
     * @Entity(class="\thebuggenie\core\entities\IssuetypeScheme")
     */
    class IssuetypeSchemes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issuetype_schemes';
        const ID = 'issuetype_schemes.id';
        const SCOPE = 'issuetype_schemes.scope';
        const NAME = 'issuetype_schemes.name';
        const DESCRIPTION = 'issuetype_schemes.description';

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOrderBy(self::ID, Criteria::SORT_ASC);

            return $this->select($crit);
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