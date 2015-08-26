<?php

    namespace thebuggenie\core\entities\common;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Colorizable extends \thebuggenie\core\entities\Datatype
    {

        /**
         * Return the item color
         * 
         * @return string
         */
        public function getColor()
        {
            $itemdata = $this->_itemdata;
            if (strlen($itemdata) == 4) {
                $i = str_split($itemdata);
                return ($i[0].$i[1].$i[1].$i[2].$i[2].$i[3].$i[3]);
            } else {
                return $itemdata;
            }
        }

        public function getTextColor()
        {
            if (!\thebuggenie\core\framework\Context::isCLI())
            {
                \thebuggenie\core\framework\Context::loadLibrary('ui');
            }

            $rgb = hex2rgb($this->_itemdata);

            if (! $rgb) return '#333333';

            return 0.299*$rgb['red'] + 0.587*$rgb['green'] + 0.114*$rgb['blue'] > 170 ? '#333333' : '#FFFFFF';
        }

        public function setItemdata($itemdata)
        {
            $this->_itemdata = (substr($itemdata, 0, 1) == '#' ? '' : '#' ) . $itemdata;
        }

        public function setColor($color)
        {
            $this->setItemdata($color);
        }

    }
