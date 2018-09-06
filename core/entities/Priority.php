<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Priority extends Datatype
    {

        const ITEMTYPE = Datatype::PRIORITY;

        const PRIORITY_1 = 1;
        const PRIORITY_2 = 2;
        const PRIORITY_3 = 3;
        const PRIORITY_4 = 4;
        const PRIORITY_5 = 5;

        protected static $_items = null;

        protected $_itemtype = Datatype::PRIORITY;

        protected $_abbreviation = null;

        public static function loadFixtures(Scope $scope)
        {
            $priorities = array();
            $priorities['Critical'] = self::PRIORITY_1;
            $priorities['High'] = self::PRIORITY_2;
            $priorities['Normal'] = self::PRIORITY_3;
            $priorities['Low'] = self::PRIORITY_4;
            $priorities['Trivial'] = self::PRIORITY_5;

            foreach ($priorities as $name => $itemdata)
            {
                $priority = new Priority();
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
                $this->_abbreviation = mb_substr(framework\Context::getI18n()->__($this->getName()), 0, 1);
            }

            return $this->_abbreviation;
        }

        public static function getAvailableValues()
        {
            return array(self::PRIORITY_1, self::PRIORITY_2, self::PRIORITY_3, self::PRIORITY_4, self::PRIORITY_5);
        }

    }
