<?php

    namespace thebuggenie\core\entities\tables;

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
     * @Table(name="scopehostnames")
     */
    class ScopeHostnames extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'scopehostnames';
        const ID = 'scopehostnames.id';
        const SCOPE_ID = 'scopehostnames.scope_id';
        const SCOPE = 'scopehostnames.scope_id';
        const HOSTNAME = 'scopehostnames.hostname';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::HOSTNAME, 200, '');
        }

        public function addHostnameToScope($hostname, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::HOSTNAME, $hostname);
            $crit->addInsert(self::SCOPE_ID, $scope_id);
            $res = $this->doInsert($crit);
        }

        public function removeHostnameFromScope($hostname, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::HOSTNAME, $hostname);
            $crit->addWhere(self::SCOPE_ID, $scope_id);
            $res = $this->doDelete($crit);
        }

        public function saveScopeHostnames($hostnames, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE_ID, $scope_id);
            $res = $this->doDelete($crit);
            foreach ($hostnames as $hostname)
            {
                $this->addHostnameToScope($hostname, $scope_id);
            }
        }

        public function getHostnamesForScope($scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE_ID, $scope_id);

            $hostnames = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $hostnames[$row->get(self::ID)] = $row->get(self::HOSTNAME);
                }
            }

            return $hostnames;
        }

        public function getScopeIDForHostname($hostname)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::HOSTNAME, $hostname);

            $row = $this->doSelectOne($crit);

            return ($row instanceof \b2db\Row) ? (int) $row->get(self::SCOPE_ID) : null;
        }

    }
