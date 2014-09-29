<?php

    /**
     * @Table(name="TBGListTypesTable")
     */
    class TBGPriority extends TBGDatatype
    {

        const ITEMTYPE = TBGDatatype::PRIORITY;

        protected static $_items = null;

        protected $_itemtype = TBGDatatype::PRIORITY;

        protected $_abbreviation = null;

        public static function loadFixtures(TBGScope $scope)
        {
            $priorities = array();
            $priorities['Critical'] = 1;
            $priorities['Needs to be fixed'] = 2;
            $priorities['Must fix before next release'] = 3;
            $priorities['Normal'] = 4;
            $priorities['Low'] = 5;

            foreach ($priorities as $name => $itemdata)
            {
                $priority = new TBGPriority();
                $priority->setName($name);
                $priority->setItemdata($itemdata);
                $priority->setScope($scope);
                $priority->save();
            }
        }

        public function getValue()
        {
            return $this->_itemdata;
        }

        public function getAbbreviation()
        {
            if ($this->_abbreviation === null)
            {
                $this->_abbreviation = mb_substr(TBGContext::getI18n()->__($this->getName()), 0, 1);
            }

            return $this->_abbreviation;
        }

    }
