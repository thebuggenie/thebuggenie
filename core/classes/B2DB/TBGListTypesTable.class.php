<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * List types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * List types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="listtypes")
	 * @Entity(class="TBGDatatypeBase")
	 * @Entities(identifier="itemtype")
	 * @SubClasses(status="TBGStatus", category="TBGCategory", priority="TBGPriority", role="TBGRole", resolution="TBGResolution", reproducability="TBGReproducability", severity="TBGSeverity")
	 */
	class TBGListTypesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'listtypes';
		const ID = 'listtypes.id';
		const SCOPE = 'listtypes.scope';
		const NAME = 'listtypes.name';
		const ITEMTYPE = 'listtypes.itemtype';
		const ITEMDATA = 'listtypes.itemdata';
		const APPLIES_TO = 'listtypes.applies_to';
		const APPLIES_TYPE = 'listtypes.applies_type';
		const ORDER = 'listtypes.sort_order';
		
		protected static $_item_cache = null;

		public function clearListTypeCache()
		{
			self::$_item_cache = null;
		}
		
		protected function _populateItemCache()
		{
			if (self::$_item_cache === null)
			{
				self::$_item_cache = array();
				$crit = $this->getCriteria();
				$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
				$crit->addOrderBy(self::ORDER, Criteria::SORT_ASC);
				$items = $this->select($crit);
				foreach ($items as $item)
				{
					self::$_item_cache[$item->getItemtype()][$item->getID()] = $item;
				}
			}
		}
		
		public function getAllByItemType($itemtype)
		{
			$this->_populateItemCache();
			return (array_key_exists($itemtype, self::$_item_cache)) ? self::$_item_cache[$itemtype] : array();
		}

		public function getAllByItemTypeAndItemdata($itemtype, $itemdata)
		{
			$this->_populateItemCache();
			$items = (array_key_exists($itemtype, self::$_item_cache)) ? self::$_item_cache[$itemtype] : array();
			foreach ($items as $id => $item)
			{
				if ($item->getItemdata() != $itemdata) unset($items[$id]);
			}

			return $items;
		}

		/**
		 * Returns the next order number for the specified item type. Useful for
		 * adding new items at the bottom of a list.
		 *
		 * The number is calculated by finding the largest order number amongst the
		 * items, and incrementing this number by 1.
		 *
		 * @param itemtype string Item type.
		 *
		 * @return integer Next smallest available order number.
		 */
		public function getNextOrderByItemType($itemtype)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ITEMTYPE, $itemtype);
			$crit->addSelectionColumn(self::ORDER, 'next_order', Criteria::DB_MAX, '', '+1');

			return $this->doSelectOne($crit, 'none')->get('next_order');
		}

		public function deleteByTypeAndId($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ITEMTYPE, $type);
			$crit->addWhere(self::ID, $id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doDelete($crit);
		}

		public function saveOptionOrder($options, $type)
		{
			foreach ($options as $key => $option_id)
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::ORDER, $key + 1);
				$crit->addWhere(self::ITEMTYPE, $type);
				$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
				$this->doUpdateById($crit, $option_id);
			}
		}

	}
