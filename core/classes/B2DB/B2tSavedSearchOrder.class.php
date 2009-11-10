<?php

	class B2tSavedSearchOrder extends B2DBTable 
	{
		const B2DBNAME = 'savedsearchorder';
		const ID = 'savedsearchorder.id';
		const SCOPE = 'savedsearchorder.scope';
		const ORDER_BY = 'savedsearchorder.order_by';
		const ORDER = 'savedsearchorder.order';
		const SEARCH = 'savedsearchorder.search';

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