<?php

    namespace thebuggenie\core\framework;

    /**
     * Parameter holder class used in the MVC part of the framework for \thebuggenie\core\entities\Action and \thebuggenie\core\entities\ActionComponent
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Parameter holder class used in the MVC part of the framework for \thebuggenie\core\entities\Action and \thebuggenie\core\entities\ActionComponent
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class Parameterholder implements \ArrayAccess
    {
        
        protected $_property_list = array();
        
        public function __set($key, $value)
        {
            $this->_property_list[$key] = $value;
        }
        
        public function __get($property)
        {
            return ($this->hasParameter($property)) ? $this->_property_list[$property] : null; 
        }
        
        public function hasParameter($key)
        {
            return $this->__isset($key);
        }
        
        public function getParameterHolder()
        {
            return $this->_property_list;
        }
        
        public function __isset($key)
        {
            return (array_key_exists($key, $this->_property_list)) ? true : false; 
        }

        public function offsetUnset($key)
        {
            if (array_key_exists($key, $this->_property_list))
            {
                unset($this->_property_list[$key]);
            }
        }

        public function offsetSet($key, $value)
        {
            $this->__set($key, $value);
        }

        public function offsetGet($key)
        {
            return $this->__get($key);
        }

        public function offsetExists($key)
        {
            return $this->__isset($key);
        }
        
    }
