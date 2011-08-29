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
	 */
	class TBGListTypesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
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

		/**
		 * Return an instance of this table
		 *
		 * @return TBGListTypesTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGListTypesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::ITEMTYPE, 25);
			parent::_addText(self::ITEMDATA, false);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addInteger(self::ORDER, 3);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
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
				if ($res = $this->doSelect($crit, false))
				{
					while ($row = $res->getNextRow())
					{
						self::$_item_cache[$row->get(self::ITEMTYPE)][$row->get(self::ID)] = $row;
					}
				}
			}
		}
		
		public function getAllByItemType($itemtype)
		{
			$this->_populateItemCache();
			if (array_key_exists($itemtype, self::$_item_cache))
			{
				return self::$_item_cache[$itemtype];
			}
			else
			{
				return null;
			}
		}

		public function createNew($name, $itemtype, $itemdata = null, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;

			$crit = $this->getCriteria();
			$crit->addWhere(self::ITEMTYPE, $itemtype);
			$crit->addSelectionColumn(self::ORDER, 'sortorder', Criteria::DB_MAX, '', '+1');
			$row = $this->doSelectOne($crit, 'none');
			$sort_order = (int) $row->get('sortorder');
			$sort_order = ($sort_order > 0) ? $sort_order : 1;

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::ITEMTYPE, $itemtype);
			$crit->addInsert(self::ORDER, $sort_order);
			if ($itemdata !== null)
			{
				$crit->addInsert(self::ITEMDATA, $itemdata);
			}
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			
			return $res;
		}

		public function saveById($name, $itemdata, $order, $id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::NAME, $name);
			$crit->addUpdate(self::ITEMDATA, $itemdata);
			$crit->addUpdate(self::ORDER, $order);

			$res = $this->doUpdateById($crit, $id);
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
