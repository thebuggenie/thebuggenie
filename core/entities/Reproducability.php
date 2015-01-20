<?php

    namespace thebuggenie\core\entities;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Reproducability extends Datatype 
    {

        const ITEMTYPE = Datatype::REPRODUCABILITY;

        protected static $_items = null;
        
        protected $_itemtype = Datatype::REPRODUCABILITY;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $reproducabilities = array();
            $reproducabilities["Can't reproduce"] = '';
            $reproducabilities['Rarely'] = '';
            $reproducabilities['Often'] = '';
            $reproducabilities['Always'] = '';

            foreach ($reproducabilities as $name => $itemdata)
            {
                $reproducability = new \thebuggenie\core\entities\Reproducability();
                $reproducability->setName($name);
                $reproducability->setItemdata($itemdata);
                $reproducability->setScope($scope);
                $reproducability->save();
            }
        }

    }
