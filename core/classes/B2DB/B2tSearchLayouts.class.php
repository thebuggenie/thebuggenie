<?php

	class B2tSearchLayouts extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_searchlayouts';
		const ID = 'bugs2_searchlayouts.id';
		const SCOPE = 'bugs2_searchlayouts.scope';
		const NAME = 'bugs2_searchlayouts.name';
		const WIDTH = 'bugs2_searchlayouts.width';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 255);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
?>