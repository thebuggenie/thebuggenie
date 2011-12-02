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

		public function deleteByTypeAndId($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ITEMTYPE, $type);
			$crit->addWhere(self::ID, $id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doDelete($crit);
		}
		
	}
