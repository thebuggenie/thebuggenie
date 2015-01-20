<?php

    namespace thebuggenie\core\entities;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Severity extends Datatype 
    {

        const ITEMTYPE = Datatype::SEVERITY;

        protected static $_items = null;
        
        protected $_itemtype = Datatype::SEVERITY;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $severities = array();
            $severities['Low'] = '';
            $severities['Normal'] = '';
            $severities['Critical'] = '';

            foreach ($severities as $name => $itemdata)
            {
                $severity = new \thebuggenie\core\entities\Severity();
                $severity->setName($name);
                $severity->setItemdata($itemdata);
                $severity->setScope($scope);
                $severity->save();
            }
        }
        
    }
