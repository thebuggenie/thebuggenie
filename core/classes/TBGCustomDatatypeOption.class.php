<?php

	class TBGCustomDatatypeOption extends TBGDatatypeBase
	{

		protected static $_b2dbtablename = 'TBGCustomFieldOptionsTable';

		protected static $_items = array();

		/**
		 * This options value
		 *
		 * @var string|integer
		 */
		protected $_value = null;
		
		protected $_sort_order = null;
		
		/**
		 * Custom field key value
		 *
		 * @var string
		 */
		protected $_customfield_key;

		/**
		 * Returns all options available for a custom type
		 * 
		 * @return array 
		 */		
		public static function getAllByKey($key)
		{
			if (!array_key_exists($key, self::$_items))
			{
				self::$_items[$key] = array();
				if ($items = \b2db\Core::getTable('TBGCustomFieldOptionsTable')->getAllByKey($key))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$key][$row_id] = TBGContext::factory()->TBGCustomDatatypeOption($row_id, $row);
					}
				}
			}
			return self::$_items[$key];
		}

		/**
		 * Return a custom data type option by value and key
		 *
		 * @param string|integer $value
		 * @param string $key
		 *
		 * @return TBGCustomDatatypeOption
		 */
		public static function getByValueAndKey($value, $key)
		{
			$row = \b2db\Core::getTable('TBGCustomFieldOptionsTable')->getByValueAndKey($value, $key);
			if ($row)
			{
				return TBGContext::factory()->TBGCustomDatatypeOption($row->get(TBGCustomFieldOptionsTable::ID), $row);
			}
			return null;
		}
		
		/**
		 * Create a new custom data type option
		 *
		 * @param string $name The option description
		 * @param string $itemdata[optional] The color/icon if any
		 *
		 * @return TBGStatus
		 */
		public function _preSave($is_new)
		{
			if ($this->getItemtype() == TBGCustomDatatype::DROPDOWN_CHOICE_TEXT_ICON)
			{

			}
			elseif (in_array($this->getItemtype(), array(TBGCustomDatatype::DROPDOWN_CHOICE_TEXT_COLORED, TBGCustomDatatype::DROPDOWN_CHOICE_TEXT_COLOR)))
			{
				$itemdata = ($this->getItemdata() === null || trim($this->getItemdata()) == '') ? '#FFF' : $this->getItemdata();
				if (mb_substr($itemdata, 0, 1) != '#')
				{
					$itemdata = '#'.$itemdata;
				}
				$this->setItemdata($itemdata);
			}
		}

		public function getKey()
		{
			return $this->_customfield_key;
		}
		
		public function setKey($key)
		{
			$this->_customfield_key = $key;
		}
		
		/**
		 * Return the options color (if applicable)
		 * 
		 * @return string
		 */
		public function getColor()
		{
			return $this->_itemdata;
		}
		
		public function canBeDeleted()
		{
			return true;
		}

		/**
		 * Return the options icon (if applicable)
		 * 
		 * @return string
		 */
		public function getIcon()
		{
			return $this->_itemdata;
		}

		public function isBuiltin()
		{
			return false;
		}

		public function getValue()
		{
			return $this->_value;
		}

		public function setValue($value)
		{
			$this->_value = $value;
		}

	}
