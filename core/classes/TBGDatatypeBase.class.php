<?php

	/**
	 * Generic datatype class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Generic datatype class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	abstract class TBGDatatypeBase extends TBGIdentifiableClass
	{

		/**
		 * Item type
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_itemtype = null;
		
		/**
		 * Extra data for that data type (if any)
		 *
		 * @var string
		 * @access protected
		 */
		protected $_itemdata = null;
		
		/**
		 * ID of project which this item applies to (if any)
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_applies_to = null;

		/**
		 * Sort order of this item
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_sort_order = null;

		protected $_key = null;

		/**
		 * Returns the itemdata associated with the datatype (if any)
		 *
		 * @return string
		 * @access public
		 */
		public function getItemdata()
		{
			return $this->_itemdata;
		}

		/**
		 * Set the itemdata
		 *
		 * @param string $itemdata
		 */
		public function setItemdata($itemdata)
		{
			$this->_itemdata = $itemdata;
		}
		
		/**
		 * Invoked when trying to print the object
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->_name;
		}
		
		public function getItemtype()
		{
			return $this->_itemtype;
		}
		
		public function setItemtype($itemtype)
		{
			$this->_itemtype = $itemtype;
		}

		public static function getAvailableFields($builtin_only = false)
		{
			$builtin_types = array('description', 'reproduction_steps', 'status', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'owner', 'assignee', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone', 'user_pain', 'votes');
			
			if ($builtin_only) return $builtin_types;

			$customtypes = TBGCustomDatatype::getAll();
			$types = array_merge($builtin_types, array_keys($customtypes));
			
			return $types;
		}

		public function getPermissionsKey()
		{
			return 'set_datatype_' . $this->_itemtype;
		}

		public function canUserSet(TBGUser $user)
		{
			return $user->hasPermission($this->getPermissionsKey(), $this->getID(), 'core', true, true);
		}

		public function setOrder($order)
		{
			$this->_sort_order = $order;
		}

		public function getOrder()
		{
			return (int) $this->_sort_order;
		}

		protected function _generateKey()
		{
			if ($this->_key === null)
				$this->_key = preg_replace("/[^0-9a-zA-Z]/i", '', mb_strtolower($this->getName()));
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

		public function toJSON()
		{
			return array('id' => $this->getID(), 'itemdata' => $this->getItemdata(), 'itemtype' => $this->_itemtype, 'name' => $this->getName(), 'key' => $this->getKey());
		}

	}
