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
					switch (true) {
						case (in_array($ad_info[1][0], array('"', "'")) && in_array($ad_info[1][count($ad_info[1]) - 1], array('"', "'"))):
							$value = trim(str_replace(array('"', "'"), array('', ''), $ad_info[1]));
							break;
						case (in_array($ad_info[1], array('true', 'false'))):
							$value = ($ad_info[1] == 'true') ? true : false;
							break;
						case (is_numeric($ad_info[1])):
							$value = (integer) $ad_info[1];
							break;
						case (defined($ad_info[1])):
							$value = array('type' => 'constant', 'value' => $value);
							break;
						default:
							$value = trim($ad_info[1]);
					}
					$this->_data[trim($ad_info[0])] = $value;
				}
			}
		}

		public function hasProperty($property)
		{
			return array_key_exists($property, $this->_data);
		}

		public function getProperty($property, $default_value = null)
		{
			return ($this->hasProperty($property)) ? $this->_data[$property] : $default_value;
		}

		public function getProperties()
		{
			return $this->_data;
		}

	}
