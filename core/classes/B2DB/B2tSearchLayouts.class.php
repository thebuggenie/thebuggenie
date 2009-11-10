<?php

	class B2tSearchLayouts extends B2DBTable 
	{
		const B2DBNAME = 'searchlayouts';
		const ID = 'searchlayouts.id';
		const SCOPE = 'searchlayouts.scope';
		const NAME = 'searchlayouts.name';
		const WIDTH = 'searchlayouts.width';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 255);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
?>