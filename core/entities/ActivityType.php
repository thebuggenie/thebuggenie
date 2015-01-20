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
            $activitytypes["Investigation"] = '';
            $activitytypes["Documentation"] = '';
            $activitytypes["Development"] = '';
            $activitytypes["Testing"] = '';
            $activitytypes["Deployment"] = '';

            foreach ($activitytypes as $name => $itemdata)
            {
                $activitytype = new \thebuggenie\core\entities\ActivityType();
                $activitytype->setName($name);
                $activitytype->setItemdata($itemdata);
                $activitytype->setScope($scope);
                $activitytype->save();
            }
        }

        public static function getActivityTypeByKeyish($key)
        {
            foreach (self::getAll() as $activitytype)
            {
                if ($activitytype->getKey() == str_replace(array(' ', '/', "'"), array('', '', ''), mb_strtolower($key)))
                {
                    return $activitytype;
                }
            }
            return null;
        }

        protected function _generateKey()
        {
            $this->_key = str_replace(array(' ', '/', "'"), array('', '', ''), mb_strtolower($this->getName()));
        }
        
        public function getKey()
        {
            if ($this->_key == null)
            {
                $this->_generateKey();
            }
            return $this->_key;
        }
        
    }
