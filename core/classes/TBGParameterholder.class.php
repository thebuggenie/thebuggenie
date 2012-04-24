<?php

	/**
	 * Parameter holder class used in the MVC part of the framework for TBGAction and TBGActionComponent
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * Parameter holder class used in the MVC part of the framework for TBGAction and TBGActionComponent
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class TBGParameterholder implements ArrayAccess
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
