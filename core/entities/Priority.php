<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @method static Priority getByKeyish($key)
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Priority extends Datatype
    {

        const ITEMTYPE = Datatype::PRIORITY;

        const CRITICAL = 1;
        const HIGH = 2;
        const NORMAL = 3;
        const LOW = 4;
        const TRIVIAL = 5;

        protected static $_items = null;

        protected $_itemtype = Datatype::PRIORITY;

        protected $_abbreviation = null;

        public static function loadFixtures(Scope $scope)
        {
            $priorities = array();
            $priorities['Critical'] = self::CRITICAL;
            $priorities['High'] = self::HIGH;
            $priorities['Normal'] = self::NORMAL;
            $priorities['Low'] = self::LOW;
            $priorities['Trivial'] = self::TRIVIAL;

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
            return [
                self::CRITICAL => 'exclamation',
                self::HIGH => 'angle-up',
                self::NORMAL => 'minus',
                self::LOW => 'angle-down',
                self::TRIVIAL => 'angle-double-down'
            ];
        }

    }
