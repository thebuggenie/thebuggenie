<?php

    namespace thebuggenie\core\entities\common;

    /**
     * Generic keyable class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Generic keyable class
     *
     * @package thebuggenie
     * @subpackage main
     */
    abstract class Keyable extends IdentifiableScoped
    {

        /**
         * The key for this item
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_key = null;

        public static function getByKeyish($key)
        {
            foreach (static::getAll() as $item)
            {
                if ($item->getKey() == str_replace(array(' ', '/', "'"), array('', '', ''), mb_strtolower($key)))
                {
                    return $item;
                }
            }
            return null;
        }

        protected function _generateKey()
        {
            if ($this->_key === null)
                $this->_key = preg_replace("/[^\pL0-9]/iu", '', mb_strtolower($this->getName()));
        }
        
        public function getKey()
        {
            $this->_generateKey();
            return $this->_key;
        }        

        public function setKey($key)
        {
            $this->_key = $key;
        }

    }
