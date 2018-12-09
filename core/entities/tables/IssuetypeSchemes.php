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
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::ID, \b2db\QueryColumnSort::SORT_ASC);

            return $this->select($query);
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