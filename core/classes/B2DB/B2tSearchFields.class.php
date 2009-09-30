<?php

	class B2tSearchFields extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_searchfields';
		const ID = 'bugs2_searchfields.id';
		const SCOPE = 'bugs2_searchfields.scope';
		const VALUE = 'bugs2_searchfields.value';
		const OPERATOR = 'bugs2_searchfields.operator';
		const SEARCH = 'bugs2_searchfields.search';
		const FILTER_ID = 'bugs2_searchfields.filter_id';
		
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