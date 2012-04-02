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
		protected $_customfield_id;

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
			$this->_customfield_id = $customdatatype;
		}

		/**
		 * @return TBGCustomDatatype
		 */
		public function getCustomdatatype()
		{
			if (!$this->_customfield_id instanceof TBGCustomDatatype)
			{
				$this->_b2dbLazyload('_customfield_id');
			}
			return $this->_customfield_id;
		}

		public function getType()
		{
			return parent::getItemtype();
		}

	}
