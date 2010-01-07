<?php

	class B2tSavedSearchFilters extends B2DBTable
	{
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
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::SEARCH_ID, B2DB::getTable('B2tSavedSearches'), B2tSavedSearches::ID);
		}

		public function getFiltersBySavedSearchID($savedsearch_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
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
		
	}
?>