<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	class TBGSavedSearchFiltersTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'savedsearchfilters';
		const ID = 'savedsearchfilters.id';
		const SCOPE = 'savedsearchfilters.scope';
		const VALUE = 'savedsearchfilters.value';
		const OPERATOR = 'savedsearchfilters.operator';
		const SEARCH_ID = 'savedsearchfilters.search_id';
		const FILTER_KEY = 'savedsearchfilters.filter_key';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::VALUE, 200);
			parent::_addVarchar(self::OPERATOR, 40);
			parent::_addVarchar(self::FILTER_KEY, 100);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::SEARCH_ID, Core::getTable('TBGSavedSearchesTable'), TBGSavedSearchesTable::ID);
		}

		public function getFiltersBySavedSearchID($savedsearch_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::SEARCH_ID, $savedsearch_id);

			$retarr = array();

			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					if (!array_key_exists($row->get(self::FILTER_KEY), $retarr)) $retarr[$row->get(self::FILTER_KEY)] = array();
					$retarr[$row->get(self::FILTER_KEY)][] = array('operator' => $row->get(self::OPERATOR), 'value' => $row->get(self::VALUE));
				}
			}

			return $retarr;
		}

		protected function _saveFilterForSavedSearch($saved_search_id, $filter_key, $value, $operator)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::SEARCH_ID, $saved_search_id);
			$crit->addInsert(self::FILTER_KEY, $filter_key);
			$crit->addInsert(self::VALUE, $value);
			$crit->addInsert(self::OPERATOR, $operator);
			$this->doInsert($crit);
		}

		public function deleteBySearchID($saved_search_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::SEARCH_ID, $saved_search_id);
			$this->doDelete($crit);
		}

		public function saveFiltersForSavedSearch($saved_search_id, $filters)
		{
			foreach ($filters as $filter => $filter_info)
			{
				if (array_key_exists('value', $filter_info))
				{
					$this->_saveFilterForSavedSearch($saved_search_id, $filter, $filter_info['value'], $filter_info['operator']);
				}
				else
				{
					foreach ($filter_info as $k => $single_filter)
					{
						$this->_saveFilterForSavedSearch($saved_search_id, $filter, $single_filter['value'], $single_filter['operator']);
					}
				}
			}
			
		}
		
	}
