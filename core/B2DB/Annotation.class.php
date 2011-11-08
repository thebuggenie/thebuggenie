<?php

	namespace b2db;
	
	class Annotation
	{

		protected $_key;
		protected $_data = array();

		public function __construct($key, $annotation_data)
		{
			$this->_key = $key;
			
			$this->_data = array();
			$ad = explode(',', $annotation_data);
			$ad_size = count($ad);
			foreach ($ad as $a_item) {
				$ad_info = explode('=', $a_item);
				if (array_key_exists(1, $ad_info)) {
					$this->_data[trim($ad_info[0])] = trim(str_replace(array('"', "'"), array('', ''), $ad_info[1]));
				}
			}
		}

		public function hasProperty($property)
		{
			return array_key_exists($property, $this->_data);
		}

		public function getProperty($property)
		{
			return ($this->hasProperty($property)) ? $this->_data[$property] : null;
		}

	}
