<?php

	class B2tSearchFilters extends B2DBTable 
	{
		const B2DBNAME = 'searchfilters';
		const ID = 'searchfilters.id';
		const FILTER_TABLE = 'searchfilters.filter_table';
		const FILTER_TYPE = 'searchfilters.filter_type';
		const VALUES_FROM = 'searchfilters.values_from';
		const SHORT_NAME = 'searchfilters.short_name';
		const DESCRIPTION = 'searchfilters.description';
		const FILTER_FIELD = 'searchfilters.filter_field';
		const VALUE_FROM_FIELD = 'searchfilters.value_from_field';
		const NAME_FROM_FIELD = 'searchfilters.name_from_field';
		const FROM_TBL_CRIT_FIELD = 'searchfilters.from_tbl_crit_field';
		const FROM_TBL_CRIT_VALUE = 'searchfilters.from_tbl_crit_value';
		const FILTER_UNIQUE = 'searchfilters.filter_unique';
		const REQ_VALUE = 'searchfilters.req_value';
		const REQ_VALUE_FIELD = 'searchfilters.req_value_field';
		const VALUE_LENGTH = 'searchfilters.value_length';
		const VALUE_TYPE = 'searchfilters.value_type';
		const INCLUDES_NOTSET = 'searchfilters.includes_notset';
		const NOTSET_DESCRIPTION = 'searchfilters.notset_description';
		const NOTSET_VALUE = 'searchfilters.notset_value';
		const JOIN_ISSUES_ON = 'searchfilters.join_issues_on';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FILTER_TABLE, 100);
			parent::_addInteger(self::FILTER_TYPE, 5);
			parent::_addVarchar(self::VALUES_FROM, 100);
			parent::_addVarchar(self::SHORT_NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addVarchar(self::FILTER_FIELD, 60);
			parent::_addVarchar(self::VALUE_FROM_FIELD, 60);
			parent::_addVarchar(self::NAME_FROM_FIELD, 60);
			parent::_addVarchar(self::FROM_TBL_CRIT_FIELD, 60);
			parent::_addVarchar(self::FROM_TBL_CRIT_VALUE, 60);
			parent::_addBoolean(self::FILTER_UNIQUE);
			parent::_addVarchar(self::REQ_VALUE, 60);
			parent::_addVarchar(self::REQ_VALUE_FIELD, 60);
			parent::_addInteger(self::VALUE_LENGTH, 10);
			parent::_addInteger(self::VALUE_TYPE, 10);
			parent::_addBoolean(self::INCLUDES_NOTSET);
			parent::_addVarchar(self::NOTSET_DESCRIPTION, 60);
			parent::_addVarchar(self::NOTSET_VALUE, 60);
			parent::_addVarchar(self::JOIN_ISSUES_ON, 60);
		}
		
	}

?>