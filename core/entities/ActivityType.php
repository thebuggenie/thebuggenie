<?php

    namespace thebuggenie\core\entities;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class ActivityType extends Datatype
    {

        const ITEMTYPE = Datatype::ACTIVITYTYPE;

        protected static $_items = null;

        protected $_key = null;
        
        protected $_itemtype = Datatype::ACTIVITYTYPE;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            foreach (array("Investigation", "Documentation", "Development", "Testing", "Deployment") as $name)
            {
                $activitytype = new \thebuggenie\core\entities\ActivityType();
                $activitytype->setName($name);
                $activitytype->setItemdata('');
                $activitytype->setScope($scope);
                $activitytype->save();
            }
        }

        public static function getActivityTypeByKeyish($key)
        {
            return self::getByKeyish($key);
        }

    }
