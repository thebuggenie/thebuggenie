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
        const CODE = 'clients.code';
        const CONTACT = 'clients.contact';
        const TITLE = 'clients.title';
        const ADDRESS = 'clients.address';
        const NOTES = 'clients.notes';

        public function getAll($limit = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy('clients.name', \b2db\QueryColumnSort::SORT_ASC);

            if (isset($limit))
            {
                $query->setLimit($limit);
            }

            return $this->select($query);
        }

        public function doesClientNameExist($client_name)
        {
            /*
                Remove punctuation and spaces to test names: this tests for the case where "Jim's Burger's" == "Jims Burgers"
                which wouldn't make sense to have two clients named so similarly.
            */
            $puncs = str_split("`~!@#$%^&*()_-+=[]\\{}|;':\",./<>? ");
            $clow = trim(strtolower(str_replace($puncs, '', html_entity_decode($client_name, ENT_QUOTES | ENT_HTML401))));
            foreach ($this->getAll() as $client) {
                $tstr = trim(strtolower(str_replace($puncs, '', html_entity_decode($client->getName(), ENT_QUOTES | ENT_HTML401))));
                if ($clow == $tstr) {
                    return true;
                }
            }
            return false;
        }

        public function doesClientCodeExist($client_code)
        {
            $client_code = strtoupper(trim($client_code));
            $query = $this->getQuery();
            $query->where(self::CODE, $client_code);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->count($query);
        }

        public function getByCode($code)
        {
            $query = $this->getQuery();
            $query->where(self::CODE, $code, \b2db\Criterion::EQUALS);

            return $this->selectOne($query);
        }

        public function quickfind($client_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, "%{$client_name}%", \b2db\Criterion::LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

    }
