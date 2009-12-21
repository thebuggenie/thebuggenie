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
	class B2tListTypes extends B2DBTable 
	{

		const B2DBNAME = 'listtypes';
		const ID = 'listtypes.id';
		const SCOPE = 'listtypes.scope';
		const NAME = 'listtypes.name';
		const ITEMTYPE = 'listtypes.itemtype';
		const ITEMDATA = 'listtypes.itemdata';
		const APPLIES_TO = 'listtypes.applies_to';
		const APPLIES_TYPE = 'listtypes.applies_type';
		
		protected static $_item_cache = null;
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::ITEMTYPE, 25);
			parent::_addText(self::ITEMDATA, false);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addInteger(self::APPLIES_TYPE, 3);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
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
				$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
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
			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::ITEMTYPE, $itemtype);
			if ($itemdata !== null)
			{
				$crit->addInsert(self::ITEMDATA, $itemdata);
			}
			$crit->addInsert(self::SCOPE, $scope);
			
			return $this->doInsert($crit);
		}

		public function loadFixtures($scope)
		{
			$_categories = array();
			$_categories['General category'] = '';

			foreach ($_categories as $name => $itemdata)
			{
				$this->createNew($itemdata, BUGSdatatype::CATEGORY, $name, $scope);
			}

			$priorities = array();
			$priorities['Critical'] = 1;
			$priorities['Needs to be fixed'] = 2;
			$priorities['Must fix before next release'] = 3;
			$priorities['Low'] = 4;
			$priorities['Normal'] = 5;

			foreach ($priorities as $name => $itemdata)
			{
				$this->createNew($itemdata, BUGSdatatype::PRIORITY, $name, $scope);
			}

			$reproducabilities = array();
			$reproducabilities["Can't reproduce"] = '';
			$reproducabilities['Rarely'] = '';
			$reproducabilities['Often'] = '';
			$reproducabilities['Always'] = '';

			foreach ($reproducabilities as $name => $itemdata)
			{
				$this->createNew($itemdata, BUGSdatatype::REPRODUCABILITY, $name, $scope);
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
				$this->createNew($itemdata, BUGSdatatype::RESOLUTION, $name, $scope);
			}

			$severities = array();
			$severities['Low'] = '';
			$severities['Normal'] = '';
			$severities['Critical'] = '';

			foreach ($severities as $name => $itemdata)
			{
				$this->createNew($itemdata, BUGSdatatype::SEVERITY, $name, $scope);
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
				$this->createNew($itemdata, BUGSdatatype::STATUS, $name, $scope);
			}
		}

		public function deleteByTypeAndId($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ITEMTYPE, $type);
			$crit->addWhere(self::ID, $id);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());

			$res = $this->doDelete($crit);
		}
		
	}
