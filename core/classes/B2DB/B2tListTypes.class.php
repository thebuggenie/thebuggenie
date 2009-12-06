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

		public function loadFixtures($scope)
		{
			$i18n = BUGScontext::getI18n();
			
			$b2_categories = array();
			$b2_categories['General category'] = '';

			foreach ($b2_categories as $list_name => $list_data)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ITEMDATA, $list_data);
				$crit->addInsert(self::ITEMTYPE, 'b2_categories');
				$crit->addInsert(self::NAME, $list_name);
				$crit->addInsert(self::SCOPE, $scope);
				$this->doInsert($crit);
			}

			$b2_prioritytypes = array();
			$b2_prioritytypes['Critical'] = 1;
			$b2_prioritytypes['Needs to be fixed'] = 2;
			$b2_prioritytypes['Must fix before next release'] = 3;
			$b2_prioritytypes['Low'] = 4;
			$b2_prioritytypes['Normal'] = 5;

			foreach ($b2_prioritytypes as $list_name => $list_data)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ITEMDATA, $list_data);
				$crit->addInsert(self::ITEMTYPE, 'b2_prioritytypes');
				$crit->addInsert(self::NAME, $list_name);
				$crit->addInsert(self::SCOPE, $scope);
				$this->doInsert($crit);
			}

			$b2_reprotypes = array();
			$b2_reprotypes["Can't reproduce"] = '';
			$b2_reprotypes['Rarely'] = '';
			$b2_reprotypes['Often'] = '';
			$b2_reprotypes['Always'] = '';

			foreach ($b2_reprotypes as $list_name => $list_data)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ITEMDATA, $list_data);
				$crit->addInsert(self::ITEMTYPE, 'b2_reprotypes');
				$crit->addInsert(self::NAME, $list_name);
				$crit->addInsert(self::SCOPE, $scope);
				$this->doInsert($crit);
			}

			$b2_resolutiontypes = array();
			$b2_resolutiontypes["CAN'T REPRODUCE"] = '';
			$b2_resolutiontypes["WON'T FIX"] = '';
			$b2_resolutiontypes["NOT AN ISSUE"] = '';
			$b2_resolutiontypes["WILL FIX IN NEXT RELEASE"] = '';
			$b2_resolutiontypes["RESOLVED"] = '';
			$b2_resolutiontypes["CAN'T FIX"] = '';

			foreach ($b2_resolutiontypes as $list_name => $list_data)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ITEMDATA, $list_data);
				$crit->addInsert(self::ITEMTYPE, 'b2_resolutiontypes');
				$crit->addInsert(self::NAME, $list_name);
				$crit->addInsert(self::SCOPE, $scope);
				$this->doInsert($crit);
			}

			$b2_severitylevels = array();
			$b2_severitylevels['Low'] = '';
			$b2_severitylevels['Normal'] = '';
			$b2_severitylevels['Critical'] = '';

			$cc = 0;
			foreach ($b2_severitylevels as $list_name => $list_data)
			{
				$cc++;
				$crit = $this->getCriteria();
				$crit->addInsert(self::ITEMDATA, $list_data);
				$crit->addInsert(self::ITEMTYPE, 'b2_severitylevels');
				$crit->addInsert(self::NAME, $list_name);
				$crit->addInsert(self::SCOPE, $scope);
				$res = $this->doInsert($crit);
				if ($cc == 3)
				{
					BUGSsettings::saveSetting('defaultseverityfornewissues', $res->getInsertID(), 'core', $scope);
				}
			}

			$b2_statustypes = array();
			$b2_statustypes['Not reviewed'] = '#FFF';
			$b2_statustypes['Collecting information'] = '#C2F533';
			$b2_statustypes['Confirmed'] = '#FF55AA';
			$b2_statustypes['Not a bug'] = '#44FC1D';
			$b2_statustypes['Being worked on'] = '#5C5';
			$b2_statustypes['Near completion'] = '#7D3';
			$b2_statustypes['Ready for QA'] = '#55C';
			$b2_statustypes['Testing / QA'] = '#77C';
			$b2_statustypes['Closed'] = '#C2F588';
			$b2_statustypes['Postponed'] = '#FA5';
			$b2_statustypes['Done'] = '#7D3';
			$b2_statustypes['Fixed'] = '#5C5';

			foreach ($b2_statustypes as $list_name => $list_data)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ITEMDATA, $list_data);
				$crit->addInsert(self::ITEMTYPE, 'b2_statustypes');
				$crit->addInsert(self::NAME, $list_name);
				$crit->addInsert(self::SCOPE, $scope);
				$this->doInsert($crit);
			}
		}
		
	}
