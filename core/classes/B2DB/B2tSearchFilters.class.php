<?php

	class B2tSearchFilters extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_searchfilters';
		const ID = 'bugs2_searchfilters.id';
		const FILTER_TABLE = 'bugs2_searchfilters.filter_table';
		const FILTER_TYPE = 'bugs2_searchfilters.filter_type';
		const VALUES_FROM = 'bugs2_searchfilters.values_from';
		const SHORT_NAME = 'bugs2_searchfilters.short_name';
		const DESCRIPTION = 'bugs2_searchfilters.description';
		const FILTER_FIELD = 'bugs2_searchfilters.filter_field';
		const VALUE_FROM_FIELD = 'bugs2_searchfilters.value_from_field';
		const NAME_FROM_FIELD = 'bugs2_searchfilters.name_from_field';
		const FROM_TBL_CRIT_FIELD = 'bugs2_searchfilters.from_tbl_crit_field';
		const FROM_TBL_CRIT_VALUE = 'bugs2_searchfilters.from_tbl_crit_value';
		const FILTER_UNIQUE = 'bugs2_searchfilters.filter_unique';
		const REQ_VALUE = 'bugs2_searchfilters.req_value';
		const REQ_VALUE_FIELD = 'bugs2_searchfilters.req_value_field';
		const VALUE_LENGTH = 'bugs2_searchfilters.value_length';
		const VALUE_TYPE = 'bugs2_searchfilters.value_type';
		const INCLUDES_NOTSET = 'bugs2_searchfilters.includes_notset';
		const NOTSET_DESCRIPTION = 'bugs2_searchfilters.notset_description';
		const NOTSET_VALUE = 'bugs2_searchfilters.notset_value';
		const JOIN_ISSUES_ON = 'bugs2_searchfilters.join_issues_on';
		
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