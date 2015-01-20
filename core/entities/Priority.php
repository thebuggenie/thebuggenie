<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Priority extends Datatype
    {

        const ITEMTYPE = Datatype::PRIORITY;

        protected static $_items = null;

        protected $_itemtype = Datatype::PRIORITY;

        protected $_abbreviation = null;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $priorities = array();
            $priorities['Critical'] = 1;
            $priorities['Needs to be fixed'] = 2;
            $priorities['Must fix before next release'] = 3;
            $priorities['Normal'] = 4;
            $priorities['Low'] = 5;

            foreach ($priorities as $name => $itemdata)
            {
                $priority = new \thebuggenie\core\entities\Priority();
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

    }
