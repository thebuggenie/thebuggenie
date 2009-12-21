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
	abstract class BUGSdatatype extends BUGSidentifiableclass implements BUGSidentifiable
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
		 * Item type status
		 *
		 */
		const STATUS = 'status';
		
		/**
		 * Item type priority
		 *
		 */
		const PRIORITY = 'priority';
		
		/**
		 * Item type reproducability
		 *
		 */
		const REPRODUCABILITY = 'reproducability';
		
		/**
		 * Item type resolution
		 *
		 */
		const RESOLUTION = 'resolution';
		
		/**
		 * Item type severity
		 *
		 */
		const SEVERITY = 'severity';
		
		/**
		 * Item type issue type
		 *
		 */
		const ISSUETYPE = 'issuetype';
		
		/**
		 * Item type category
		 *
		 */
		const CATEGORY = 'category';
		
		/**
		 * Item type user state 
		 *
		 */
		const USERSTATE = 'userstate';
		
		abstract function __construct($item_id, $row = null);
		
		/**
		 * Constructor
		 *
		 * @param integer $i_id
		 * @param string $item_type
		 */
		protected function initialize($i_id, $item_type, $row = null)
		{
			if ($row === null)
			{
				$rows = B2DB::getTable('B2tListTypes')->getAllByItemType($item_type);
				if (array_key_exists($i_id, $rows)) $row = $rows[$i_id];
			}
			if ($row === null)
			{
				throw new Exception('wwwwaaat??');
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tListTypes::SCOPE, BUGScontext::getScope()->getID());
				$crit->addWhere(B2tListTypes::ITEMTYPE, $item_type);
				$row = B2DB::getTable('B2tListTypes')->doSelectById($i_id, $crit);
			}
			
			if ($row instanceof B2DBRow)
			{
				$this->_itemid = $i_id;
				$this->_itemtype = $item_type;
				$this->_appliesto = $row->get(B2tListTypes::APPLIES_TO);
				$this->_itemdata = $row->get(B2tListTypes::ITEMDATA);
				$this->_name = $row->get(B2tListTypes::NAME);
			}
			else
			{
				throw new Exception('This data type does not exist');
			}
			
		}

		/**
		 * Create a new field option and return the row
		 *
		 * @param string $name
		 * @param string $itemtype
		 * @param mixed $itemdata
		 *
		 * @return B2DBResultset
		 */
		protected static function _createNew($name, $itemtype, $itemdata = null)
		{
			$res = B2DB::getTable('B2tListTypes')->createNew($itemdata, $itemtype, $name);
			return $res;
		}

		public static function getTypes()
		{
			$types = array();
			$types['status'] = 'BUGSstatus';
			$types['priority'] = 'BUGSpriority';
			$types['category'] = 'BUGScategory';
			$types['severity'] = 'BUGSseverity';
			$types['reproducability'] = 'BUGSreproducability';
			$types['resolution'] = 'BUGSresolution';
			
			return $types;
		}
		
		public function getName()
		{
			return $this->_name;
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
		
	}
