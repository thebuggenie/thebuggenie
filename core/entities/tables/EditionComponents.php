<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework;

    /**
     * Edition components table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Edition components table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="editioncomponents")
     */
    class EditionComponents extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'editioncomponents';
        const ID = 'editioncomponents.id';
        const SCOPE = 'editioncomponents.scope';
        const EDITION = 'editioncomponents.edition';
        const COMPONENT = 'editioncomponents.component';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::EDITION, Editions::getTable(), Editions::ID);
            parent::addForeignKeyColumn(self::COMPONENT, Components::getTable(), Components::ID);
        }

        public function getByEditionID($edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function deleteByEditionID($edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $res = $this->rawDelete($query);
            return $res;
        }

        public function getByEditionIDandComponentID($edition_id, $component_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->where(self::COMPONENT, $component_id);

            return $this->count($query);
        }

        public function addEditionComponent($edition_id, $component_id)
        {
            if ($this->getByEditionIDandComponentID($edition_id, $component_id) == 0)
            {
                $insertion = new Insertion();
                $insertion->add(self::EDITION, $edition_id);
                $insertion->add(self::COMPONENT, $component_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $res = $this->rawInsert($insertion);

                return true;
            }
            return false;
        }

        public function removeEditionComponent($edition_id, $component_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->where(self::COMPONENT, $component_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function deleteByComponentID($component_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMPONENT, $component_id);
            $res = $this->rawDelete($query);
            return $res;
        }

    }
