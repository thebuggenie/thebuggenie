<?php

    namespace thebuggenie\core\entities;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Status extends Datatype 
    {

        const ITEMTYPE = Datatype::STATUS;

        protected static $_items = null;
        
        protected $_itemtype = Datatype::STATUS;
        
        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $statuses = array();
            $statuses['New'] = '#FFF';
            $statuses['Investigating'] = '#C2F533';
            $statuses['Confirmed'] = '#FF55AA';
            $statuses['Not a bug'] = '#44FC1D';
            $statuses['Being worked on'] = '#5C5';
            $statuses['Near completion'] = '#7D3';
            $statuses['Ready for testing / QA'] = '#55C';
            $statuses['Testing / QA'] = '#77C';
            $statuses['Closed'] = '#C2F588';
            $statuses['Postponed'] = '#FA5';
            $statuses['Done'] = '#7D3';
            $statuses['Fixed'] = '#5C5';

            foreach ($statuses as $name => $itemdata)
            {
                $status = new \thebuggenie\core\entities\Status();
                $status->setName($name);
                $status->setItemdata($itemdata);
                $status->setScope($scope);
                $status->save();
            }
        }

        /**
         * Return the status color
         * 
         * @return string
         */
        public function getColor()
        {
            return $this->_itemdata;
        }
        
        public function hasLinkedWorkflowStep()
        {
            return (bool) tables\WorkflowSteps::getTable()->countByStatusID($this->getID());
        }
        
        public function canBeDeleted()
        {
            return !$this->hasLinkedWorkflowStep();
        }
        
        public function setItemdata($itemdata)
        {
            $this->_itemdata = (substr($itemdata, 0, 1) == '#' ? '' : '#' ) . $itemdata;
        }

    }
