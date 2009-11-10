<?php

	class B2tSearchFields extends B2DBTable 
	{
		const B2DBNAME = 'searchfields';
		const ID = 'searchfields.id';
		const SCOPE = 'searchfields.scope';
		const VALUE = 'searchfields.value';
		const OPERATOR = 'searchfields.operator';
		const SEARCH = 'searchfields.search';
		const FILTER_ID = 'searchfields.filter_id';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::VALUE, 200);
			parent::_addVarchar(self::OPERATOR, 40);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::SEARCH, B2DB::getTable('B2tSavedSearches'), B2tSavedSearches::ID);
			parent::_addForeignKeyColumn(self::FILTER_ID, B2DB::getTable('B2tSearchFilters'), B2tSearchFilters::ID);
		}
		
	}
?>