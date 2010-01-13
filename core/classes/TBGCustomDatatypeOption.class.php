<?php

	class TBGCustomDatatypeOption extends TBGDatatypeBase
	{

		protected static $_items = array();

		/**
		 * This options value
		 *
		 * @var string|integer
		 */
		protected $_value = null;

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
				if ($items = B2DB::getTable('B2tCustomFieldOptions')->getAllByKey($key))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$key][$row_id] = TBGFactory::TBGCustomDatatypeOptionLab($row_id, $row);
					}
				}
			}
			return self::$_items[$key];
		}

		/**
		 * Create a new custom data type option
		 *
		 * @param string $name The option description
		 * @param string $itemdata[optional] The color/icon if any
		 *
		 * @return TBGStatus
		 */
		public static function createNew($type, $key, $name, $value, $itemdata = null)
		{
			if ($type == TBGCustomDatatype::DROPDOWN_CHOICE_TEXT_ICON)
			{

			}
			elseif (in_array($type, array(TBGCustomDatatype::DROPDOWN_CHOICE_TEXT_COLORED, TBGCustomDatatype::DROPDOWN_CHOICE_TEXT_COLOR)))
			{
				$itemdata = ($itemdata === null || trim($itemdata) == '') ? '#FFF' : $itemdata;
				if (substr($itemdata, 0, 1) != '#')
				{
					$itemdata = '#'.$itemdata;
				}
			}
			
			$res = B2DB::getTable('B2tCustomFieldOptions')->createNew($key, $name, $value, $itemdata);
			return TBGFactory::TBGCustomDatatypeOptionLab($res->getInsertID());
		}

		/**
		 * Delete a status id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			B2DB::getTable('B2tCustomFieldOptions')->doDeleteById($id);
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
			$row = B2DB::getTable('B2tCustomFieldOptions')->getByValueAndKey($value, $key);
			if ($row)
			{
				return TBGFactory::TBGCustomDatatypeOptionLab($row->get(B2tCustomFieldOptions::ID), $row);
			}
			return null;
		}
		
		/**
		 * Constructor
		 * 
		 * @param integer $item_id The item id
		 * @param B2DBrow $row [optional] A B2DBrow to use
		 * @return 
		 */
		public function __construct($item_id, $row = null)
		{
			if ($row === null)
			{
				$row = B2DB::getTable('B2tCustomFieldOptions')->doSelectById($item_id);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_itemid = $row->get(B2tCustomFieldOptions::ID);
				$this->_itemdata = $row->get(B2tCustomFieldOptions::ITEMDATA);
				$this->_name = $row->get(B2tCustomFieldOptions::NAME);
				$this->_key = $row->get(B2tCustomFieldOptions::CUSTOMFIELDS_KEY);
				$this->_value = $row->get(B2tCustomFieldOptions::OPTION_VALUE);
				$this->_sortorder = (int) $row->get(B2tCustomFieldOptions::SORT_ORDER);
			}
			else
			{
				throw new Exception('This custom type option does not exist');
			}
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

		/**
		 * Save name, itemdata and value
		 *
		 * @return boolean
		 */
		public function save()
		{
			B2DB::getTable('B2tCustomFieldOptions')->saveById($this->_name, $this->_value, $this->_itemdata, $this->_sortorder, $this->_itemid);
		}

	}

?>