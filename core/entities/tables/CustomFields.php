<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Custom fields table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Custom fields table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="customfields")
     * @Entity(class="\thebuggenie\core\entities\CustomDatatype")
     */
    class CustomFields extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'customfields';
        const ID = 'customfields.id';
        const FIELD_NAME = 'customfields.name';
        const FIELD_DESCRIPTION = 'customfields.description';
        const FIELD_INSTRUCTIONS = 'customfields.instructions';
        const FIELD_KEY = 'customfields.key';
        const FIELD_TYPE = 'customfields.itemtype';
        const SCOPE = 'customfields.scope';

        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->indexBy(self::FIELD_KEY);
            return $this->select($query);
        }

        public function countByKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::FIELD_KEY, $key);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function getByKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::FIELD_KEY, $key);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->rawSelectOne($query);
        }

        public function getKeyFromID($id)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $row = $this->rawSelectOne($query);
            if ($row instanceof \b2db\Row)
            {
                return $row->get(self::FIELD_KEY);
            }
            return null;
        }
    }
