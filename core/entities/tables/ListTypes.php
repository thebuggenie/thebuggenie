<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * List types table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * List types table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="listtypes")
     * @Entity(class="\thebuggenie\core\entities\DatatypeBase")
     * @Entities(identifier="itemtype")
     * @SubClasses(status="\thebuggenie\core\entities\Status", category="\thebuggenie\core\entities\Category", priority="\thebuggenie\core\entities\Priority", role="\thebuggenie\core\entities\Role", resolution="\thebuggenie\core\entities\Resolution", reproducability="\thebuggenie\core\entities\Reproducability", severity="\thebuggenie\core\entities\Severity", activitytype="\thebuggenie\core\entities\ActivityType")
     */
    class ListTypes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'listtypes';
        const ID = 'listtypes.id';
        const SCOPE = 'listtypes.scope';
        const NAME = 'listtypes.name';
        const ITEMTYPE = 'listtypes.itemtype';
        const ITEMDATA = 'listtypes.itemdata';
        const APPLIES_TO = 'listtypes.applies_to';
        const APPLIES_TYPE = 'listtypes.applies_type';
        const ORDER = 'listtypes.sort_order';
        
        protected static $_item_cache = null;

        public function clearListTypeCache()
        {
            self::$_item_cache = null;
        }
        
        public function populateItemCache()
        {
            $this->_populateItemCache();
        }

        protected function _populateItemCache()
        {
            if (self::$_item_cache === null)
            {
                self::$_item_cache = array();
                $crit = $this->getCriteria();
                $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
                $crit->addOrderBy(self::ORDER, Criteria::SORT_ASC);
                $items = $this->select($crit);
                foreach ($items as $item)
                {
                    self::$_item_cache[$item->getItemtype()][$item->getID()] = $item;
                }
            }
        }
        
        public function getAllByItemType($itemtype)
        {
            $this->_populateItemCache();
            return (array_key_exists($itemtype, self::$_item_cache)) ? self::$_item_cache[$itemtype] : array();
        }

        public function getAllByItemTypeAndItemdata($itemtype, $itemdata)
        {
            $this->_populateItemCache();
            $items = (array_key_exists($itemtype, self::$_item_cache)) ? self::$_item_cache[$itemtype] : array();
            foreach ($items as $id => $item)
            {
                if ($item->getItemdata() != $itemdata) unset($items[$id]);
            }

            return $items;
        }

        public function deleteByTypeAndId($type, $id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ITEMTYPE, $type);
            $crit->addWhere(self::ID, $id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->doDelete($crit);
        }

        public function saveOptionOrder($options, $type)
        {
            foreach ($options as $key => $option_id)
            {
                $crit = $this->getCriteria();
                $crit->addUpdate(self::ORDER, $key + 1);
                $crit->addWhere(self::ITEMTYPE, $type);
                $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
                $this->doUpdateById($crit, $option_id);
            }
        }

        public function getStatusListForUpgrade()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ITEMTYPE, \thebuggenie\core\entities\Datatype::STATUS);
            $crit->addJoin(Scopes::getTable(), Scopes::ID, self::SCOPE);
            $res = $this->doSelect($crit);
            
            $statuses = array();
            while ($row = $res->getNextRow())
            {
                if (!array_key_exists($row[self::SCOPE], $statuses)) $statuses[$row[self::SCOPE]] = array('scopename' => $row[Scopes::NAME], 'statuses' => array());
                $statuses[$row[self::SCOPE]]['statuses'][$row[self::ID]] = $row[self::NAME];
            }
            
            return $statuses;
        }

    }
