<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * Generic datatype class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Generic datatype class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    abstract class Datatype extends DatatypeBase
    {
        /**
         * Item type status
         *
         */
        const STATUS = 'status';
        
        /**
         * Item type priority
         *
         */
        const PRIORITY = 'priority';
        
        /**
         * Item type reproducability
         *
         */
        const REPRODUCABILITY = 'reproducability';
        
        /**
         * Item type resolution
         *
         */
        const RESOLUTION = 'resolution';
        
        /**
         * Item type severity
         *
         */
        const SEVERITY = 'severity';
        
        /**
         * Item type issue type
         *
         */
        const ISSUETYPE = 'issuetype';
        
        /**
         * Item type category
         *
         */
        const CATEGORY = 'category';
        
        /**
         * Item type project role
         *
         */
        const ROLE = 'role';
        
        /**
         * Item type activity type
         *
         */
        const ACTIVITYTYPE = 'activitytype';

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            Category::loadFixtures($scope);
            Priority::loadFixtures($scope);
            Reproducability::loadFixtures($scope);
            Resolution::loadFixtures($scope);
            Severity::loadFixtures($scope);
            Status::loadFixtures($scope);
            Role::loadFixtures($scope);
            ActivityType::loadFixtures($scope);
            foreach (self::getTypes() as $type => $class)
            {
                framework\Context::setPermission('set_datatype_'.$type, 0, 'core', 0, 0, 0, true, $scope->getID());
            }
        }
        
        public static function getTypes()
        {
            $types = array();
            $types[self::STATUS] = '\thebuggenie\core\entities\Status';
            $types[self::PRIORITY] = '\thebuggenie\core\entities\Priority';
            $types[self::CATEGORY] = '\thebuggenie\core\entities\Category';
            $types[self::SEVERITY] = '\thebuggenie\core\entities\Severity';
            $types[self::REPRODUCABILITY] = '\thebuggenie\core\entities\Reproducability';
            $types[self::RESOLUTION] = '\thebuggenie\core\entities\Resolution';
            $types[self::ACTIVITYTYPE] = '\thebuggenie\core\entities\ActivityType';

            $types = \thebuggenie\core\framework\Event::createNew('core', 'Datatype::getTypes', null, array(), $types)->trigger()->getReturnList();
            
            return $types;
        }

        public function isBuiltin()
        {
            return true;
        }
        
        public function canBeDeleted()
        {
            return true;
        }

        public static function has($item_id)
        {
            $items = static::getAll();
            return array_key_exists($item_id, $items);
        }

        /**
         * Returns all severities available
         *
         * @return array
         */
        public static function getAll()
        {
            return tables\ListTypes::getTable()->getAllByItemType(static::ITEMTYPE);
        }

    }
