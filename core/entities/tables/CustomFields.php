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
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->indexBy(self::FIELD_KEY);
            return $this->select($crit);
        }

        public function countByKey($key)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::FIELD_KEY, $key);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->doCount($crit);
        }

        public function getByKey($key)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::FIELD_KEY, $key);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->doSelectOne($crit);
        }

        public function getKeyFromID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ID, $id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $row = $this->doSelectOne($crit);
            if ($row instanceof \b2db\Row)
            {
                return $row->get(self::FIELD_KEY);
            }
            return null;
        }
    }
