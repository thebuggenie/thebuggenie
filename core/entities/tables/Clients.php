<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Clients table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Clients table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="clients")
     * @Entity(class="\thebuggenie\core\entities\Client")
     */
    class Clients extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'clients';
        const ID = 'clients.id';
        const SCOPE = 'clients.scope';
        const NAME = 'clients.name';
        const WEBSITE = 'clients.website';
        const EMAIL = 'clients.email';
        const TELEPHONE = 'clients.telephone';
        const FAX = 'clients.fax';

        public function getAll($limit = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOrderBy('clients.name', Criteria::SORT_ASC);

            if (isset($limit))
            {
                $crit->setLimit($limit);
            }

            return $this->select($crit);
        }

        public function doesClientNameExist($client_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $client_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->doCount($crit);
        }

        public function quickfind($client_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, "%{$client_name}%", Criteria::DB_LIKE);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

    }
