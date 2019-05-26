<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;

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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::HOSTNAME, 200, '');
        }

        public function addHostnameToScope($hostname, $scope_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::HOSTNAME, $hostname);
            $insertion->add(self::SCOPE_ID, $scope_id);
            $res = $this->rawInsert($insertion);
        }

        public function removeHostnameFromScope($hostname, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::HOSTNAME, $hostname);
            $query->where(self::SCOPE_ID, $scope_id);
            $res = $this->rawDelete($query);
        }

        public function saveScopeHostnames($hostnames, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE_ID, $scope_id);
            $res = $this->rawDelete($query);
            foreach ($hostnames as $hostname)
            {
                $this->addHostnameToScope($hostname, $scope_id);
            }
        }

        public function getHostnamesForScope($scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE_ID, $scope_id);

            $hostnames = array();
            if ($res = $this->rawSelect($query))
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
            $query = $this->getQuery();
            $query->where(self::HOSTNAME, $hostname);

            $row = $this->rawSelectOne($query);

            return ($row instanceof \b2db\Row) ? (int) $row->get(self::SCOPE_ID) : null;
        }

        public function addIndexes()
        {
            $this->setupIndexes();
        }

        protected function setupIndexes()
        {
            $this->addIndex('id_hostname', array(self::ID, self::HOSTNAME));
            $this->addIndex('scopeid_hostname', array(self::SCOPE_ID, self::HOSTNAME));
        }

    }
