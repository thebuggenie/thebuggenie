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

		const B2DBNAME = 'bugs2_listtypes';
		const ID = 'bugs2_listtypes.id';
		const SCOPE = 'bugs2_listtypes.scope';
		const NAME = 'bugs2_listtypes.name';
		const ITEMTYPE = 'bugs2_listtypes.itemtype';
		const ITEMDATA = 'bugs2_listtypes.itemdata';
		const APPLIES_TO = 'bugs2_listtypes.applies_to';
		const APPLIES_TYPE = 'bugs2_listtypes.applies_type';
		
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
			$b2_categories[$i18n->__('General category')] = '';

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
			$b2_prioritytypes[$i18n->__('Critical')] = 1;
			$b2_prioritytypes[$i18n->__('Needs to be fixed')] = 2;
			$b2_prioritytypes[$i18n->__('Must fix before next release')] = 3;
			$b2_prioritytypes[$i18n->__('Low')] = 4;
			$b2_prioritytypes[$i18n->__('Normal')] = 5;

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
			$b2_reprotypes[$i18n->__("Can't reproduce")] = '';
			$b2_reprotypes[$i18n->__('Rarely')] = '';
			$b2_reprotypes[$i18n->__('Often')] = '';
			$b2_reprotypes[$i18n->__('Always')] = '';

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
			$b2_resolutiontypes[$i18n->__("CAN'T REPRODUCE")] = '';
			$b2_resolutiontypes[$i18n->__("WON'T FIX")] = '';
			$b2_resolutiontypes[$i18n->__("NOT AN ISSUE")] = '';
			$b2_resolutiontypes[$i18n->__("WILL FIX IN NEXT RELEASE")] = '';
			$b2_resolutiontypes[$i18n->__("RESOLVED")] = '';
			$b2_resolutiontypes[$i18n->__("CAN'T FIX")] = '';

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
			$b2_severitylevels[$i18n->__('Low')] = '';
			$b2_severitylevels[$i18n->__('Normal')] = '';
			$b2_severitylevels[$i18n->__('Critical')] = '';

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
			$b2_statustypes[$i18n->__('Not reviewed')] = '#FFF';
			$b2_statustypes[$i18n->__('Collecting information')] = '#C2F533';
			$b2_statustypes[$i18n->__('Confirmed')] = '#FF55AA';
			$b2_statustypes[$i18n->__('Not a bug')] = '#44FC1D';
			$b2_statustypes[$i18n->__('Being worked on')] = '#5C5';
			$b2_statustypes[$i18n->__('Near completion')] = '#7D3';
			$b2_statustypes[$i18n->__('Ready for QA')] = '#55C';
			$b2_statustypes[$i18n->__('Testing / QA')] = '#77C';
			$b2_statustypes[$i18n->__('Closed')] = '#C2F588';
			$b2_statustypes[$i18n->__('Postponed')] = '#FA5';
			$b2_statustypes[$i18n->__('Done')] = '#7D3';
			$b2_statustypes[$i18n->__('Fixed')] = '#5C5';

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
