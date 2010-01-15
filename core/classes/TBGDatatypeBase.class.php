<?php

	/**
	 * Generic datatype class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	abstract class TBGDatatypeBase extends TBGIdentifiableClass implements TBGIdentifiable
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
		protected $_appliesto = null;

		/**
		 * Sort order of this item
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_sortorder = null;

		abstract function __construct($item_id, $row = null);
		
		public function getName()
		{
			return $this->_name;
		}
		
		/**
		 * Set the datatype name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		public function getID()
		{
			return $this->_itemid;
		}

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

		public static function getAvailableFields($builtin_only = false)
		{
			$builtin_types = array('description', 'reproduction_steps', 'status', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'owner', 'assignee', 'editions', 'builds', 'components', 'estimated_time', 'elapsed_time', 'milestone', 'user_pain');
			
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
			$this->_sortorder = $order;
		}

		public function getOrder()
		{
			return (int) $this->_sortorder;
		}

	}
