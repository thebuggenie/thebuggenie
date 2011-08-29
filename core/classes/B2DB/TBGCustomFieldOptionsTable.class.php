<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Custom field options table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom field options table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGCustomFieldOptionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'customfieldoptions';
		const ID = 'customfieldoptions.id';
		const NAME = 'customfieldoptions.name';
		const ITEMDATA = 'customfieldoptions.itemdata';
		const OPTION_VALUE = 'customfieldoptions.value';
		const SORT_ORDER = 'customfieldoptions.sort_order';
		const CUSTOMFIELDS_KEY = 'customfieldoptions.customfield_key';
		const SCOPE = 'customfieldoptions.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::OPTION_VALUE, 100);
			parent::_addVarchar(self::ITEMDATA, 100);
			parent::_addInteger(self::SORT_ORDER, 100);
			parent::_addVarchar(self::CUSTOMFIELDS_KEY, 100);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function createNew($key, $name, $value, $itemdata = null, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;

			$trans = Core::startTransaction();
			$crit = $this->getCriteria();
			$crit->addWhere(self::CUSTOMFIELDS_KEY, $key);
			$crit->addSelectionColumn(self::SORT_ORDER, 'sortorder', Criteria::DB_MAX, '', '+1');
			$row = $this->doSelectOne($crit, 'none');
			$sort_order = (int) $row->get('sortorder');
			$sort_order = ($sort_order > 0) ? $sort_order : 1;

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::OPTION_VALUE, $value);
			$crit->addInsert(self::CUSTOMFIELDS_KEY, $key);
			$crit->addInsert(self::SORT_ORDER, $sort_order);
			if ($itemdata !== null)
			{
				$crit->addInsert(self::ITEMDATA, $itemdata);
			}
			$crit->addInsert(self::SCOPE, $scope);
			$trans->commitAndEnd();

			return $this->doInsert($crit);
		}

		public function getAllByKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CUSTOMFIELDS_KEY, $key);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::SORT_ORDER, Criteria::SORT_ASC);
			
			$res = $this->doSelect($crit);
			
			$retval = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$retval[$row->get(self::ID)] = $row;
				}
			}

			return $retval;
		}

		public function getByValueAndKey($value, $key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CUSTOMFIELDS_KEY, $key);
			$crit->addWhere(self::OPTION_VALUE, $value);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$row = $this->doSelectOne($crit);

			return $row;
		}

		public function saveById($name, $value, $itemdata, $sortorder, $id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::NAME, $name);
			$crit->addUpdate(self::OPTION_VALUE, $value);
			$crit->addUpdate(self::ITEMDATA, $itemdata);
			$crit->addUpdate(self::SORT_ORDER, $sortorder);

			$res = $this->doUpdateById($crit, $id);
		}
		
		public function doDeleteByFieldKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CUSTOMFIELDS_KEY, $key);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
		
			$res = $this->doSelect($crit);
			
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$this->doDeleteById($row->get(self::ID));
				}
			}
		}

	}
