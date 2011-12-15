<?php

	/**
	 * @Table(name="TBGCustomFieldOptionsTable")
	 */
	class TBGCustomDatatypeOption extends TBGDatatypeBase
	{

		protected static $_items = array();

		/**
		 * This options value
		 *
		 * @var string|integer
		 * @Column(type="string", length=200)
		 */
		protected $_value = null;
		
		/**
		 * Custom field key value
		 *
		 * @var integer
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGCustomDatatype")
		 */
		protected $_customdatatype;

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
		protected function _preSave($is_new)
		{
			parent::_preSave($is_new);
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

		/**
		 * @param int $customdatatype
		 */
		public function setCustomdatatype($customdatatype)
		{
			$this->_customdatatype = $customdatatype;
		}

		/**
		 * @return int
		 */
		public function getCustomdatatype()
		{
			return $this->_customdatatype;
		}

	}
