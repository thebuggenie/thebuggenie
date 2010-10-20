<?php

	/**
	 * Generic datatype class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	abstract class TBGDatatype extends TBGDatatypeBase
	{

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
				$rows = TBGListTypesTable::getTable()->getAllByItemType($item_type);
				if (array_key_exists($i_id, $rows)) $row = $rows[$i_id];
			}
			if ($row === null)
			{
				throw new Exception('wwwwaaat??');
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGListTypesTable::SCOPE, TBGContext::getScope()->getID());
				$crit->addWhere(TBGListTypesTable::ITEMTYPE, $item_type);
				$row = TBGListTypesTable::getTable()->doSelectById($i_id, $crit);
			}
			
			if ($row instanceof B2DBRow)
			{
				$this->_itemid = $i_id;
				$this->_itemtype = $item_type;
				$this->_appliesto = $row->get(TBGListTypesTable::APPLIES_TO);
				$this->_itemdata = $row->get(TBGListTypesTable::ITEMDATA);
				$this->_name = $row->get(TBGListTypesTable::NAME);
				$this->_sortorder = (int) $row->get(TBGListTypesTable::ORDER);
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
			$res = TBGListTypesTable::getTable()->createNew($name, $itemtype, $itemdata);
			return $res;
		}

		public static function getTypes()
		{
			$types = array();
			$types['status'] = 'TBGStatus';
			$types['priority'] = 'TBGPriority';
			$types['category'] = 'TBGCategory';
			$types['severity'] = 'TBGSeverity';
			$types['reproducability'] = 'TBGReproducability';
			$types['resolution'] = 'TBGResolution';
			
			return $types;
		}
		
		/**
		 * Save name and itemdata
		 *
		 * @return boolean
		 */
		public function save()
		{
			TBGListTypesTable::getTable()->saveById($this->_name, $this->_itemdata, $this->_sortorder, $this->_itemid);
		}

		public function isBuiltin()
		{
			return true;
		}

	}
