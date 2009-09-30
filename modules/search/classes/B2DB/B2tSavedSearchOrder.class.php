<?php

	class B2tSavedSearchOrder extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_savedsearchorder';
		const ID = 'bugs2_savedsearchorder.id';
		const SCOPE = 'bugs2_savedsearchorder.scope';
		const ORDER_BY = 'bugs2_savedsearchorder.order_by';
		const ORDER = 'bugs2_savedsearchorder.order';
		const SEARCH = 'bugs2_savedsearchorder.search';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::ORDER_BY, 200);
			parent::_addInteger(self::ORDER, 3);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::SEARCH, B2DB::getTable('B2tSavedSearches'), B2tSavedSearches::ID);
		}
		
	}
?>