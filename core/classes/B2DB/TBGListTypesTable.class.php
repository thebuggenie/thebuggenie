<?php

	/**
	 * List types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class TBGListTypesTable extends B2DBTable 
	{

		const B2DBNAME = 'listtypes';
		const ID = 'listtypes.id';
		const SCOPE = 'listtypes.scope';
		const NAME = 'listtypes.name';
		const ITEMTYPE = 'listtypes.itemtype';
		const ITEMDATA = 'listtypes.itemdata';
		const APPLIES_TO = 'listtypes.applies_to';
		const APPLIES_TYPE = 'listtypes.applies_type';
		const ORDER = 'listtypes.order';
		
		protected static $_item_cache = null;
		
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
				$crit->addOrderBy(self::ORDER, B2DBCriteria::SORT_ASC);
				if ($res = $this->doSelect($crit))
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

			$trans = B2DB::startTransaction();
			$crit = $this->getCriteria();
			$crit->addWhere(self::ITEMTYPE, $itemtype);
			$crit->addSelectionColumn(self::ORDER, 'sortorder', B2DBCriteria::DB_MAX, '', '+1');
			$row = $this->doSelectOne($crit, 'none');
			$sort_order = (int) $row->get('sortorder');

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
			$trans->commitAndEnd();
			
			return $res;
		}

		public function loadFixtures($scope)
		{
			$_categories = array();
			$_categories['General category'] = '';

			foreach ($_categories as $name => $itemdata)
			{
				$this->createNew($name, TBGDatatype::CATEGORY, $itemdata, $scope);
			}

			$priorities = array();
			$priorities['Critical'] = 1;
			$priorities['Needs to be fixed'] = 2;
			$priorities['Must fix before next release'] = 3;
			$priorities['Low'] = 4;
			$priorities['Normal'] = 5;

			foreach ($priorities as $name => $itemdata)
			{
				$this->createNew($name, TBGDatatype::PRIORITY, $itemdata, $scope);
			}

			$reproducabilities = array();
			$reproducabilities["Can't reproduce"] = '';
			$reproducabilities['Rarely'] = '';
			$reproducabilities['Often'] = '';
			$reproducabilities['Always'] = '';

			foreach ($reproducabilities as $name => $itemdata)
			{
				$this->createNew($name, TBGDatatype::REPRODUCABILITY, $itemdata, $scope);
			}

			$resolutions = array();
			$resolutions["CAN'T REPRODUCE"] = '';
			$resolutions["WON'T FIX"] = '';
			$resolutions["NOT AN ISSUE"] = '';
			$resolutions["WILL FIX IN NEXT RELEASE"] = '';
			$resolutions["RESOLVED"] = '';
			$resolutions["CAN'T FIX"] = '';

			foreach ($resolutions as $name => $itemdata)
			{
				$this->createNew($name, TBGDatatype::RESOLUTION, $itemdata, $scope);
			}

			$severities = array();
			$severities['Low'] = '';
			$severities['Normal'] = '';
			$severities['Critical'] = '';

			foreach ($severities as $name => $itemdata)
			{
				$this->createNew($name, TBGDatatype::SEVERITY, $itemdata, $scope);
			}

			$statuses = array();
			$statuses['Not reviewed'] = '#FFF';
			$statuses['Collecting information'] = '#C2F533';
			$statuses['Confirmed'] = '#FF55AA';
			$statuses['Not a bug'] = '#44FC1D';
			$statuses['Being worked on'] = '#5C5';
			$statuses['Near completion'] = '#7D3';
			$statuses['Ready for QA'] = '#55C';
			$statuses['Testing / QA'] = '#77C';
			$statuses['Closed'] = '#C2F588';
			$statuses['Postponed'] = '#FA5';
			$statuses['Done'] = '#7D3';
			$statuses['Fixed'] = '#5C5';

			foreach ($statuses as $name => $itemdata)
			{
				$this->createNew($name, TBGDatatype::STATUS, $itemdata, $scope);
			}
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
