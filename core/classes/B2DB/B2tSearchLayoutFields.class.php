<?php

	class B2tSearchLayoutFields extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_searchlayoutfields';
		const ID = 'bugs2_searchlayoutfields.id';
		const SCOPE = 'bugs2_searchlayoutfields.scope';
		const FIELD = 'bugs2_searchlayoutfields.field';
		const LENGTH = 'bugs2_searchlayoutfields.length';
		const WIDTH = 'bugs2_searchlayoutfields.width';
		const HEIGHT = 'bugs2_searchlayoutfields.height';
		const ICON = 'bugs2_searchlayoutfields.icon';
		const ORDER = 'bugs2_searchlayoutfields.order';
		const LAYOUT = 'bugs2_searchlayoutfields.layout';
		const FIELD_TYPE = 'bugs2_searchlayoutfields.field_type';
		const ROW = 'bugs2_searchlayoutfields.row';
		const SPAN_COLS = 'bugs2_searchlayoutfields.span_cols';
		const SPAN_ROWS = 'bugs2_searchlayoutfields.span_rows';
		const ALIGN = 'bugs2_searchlayoutfields.align';
		const INCLUDE_DESC = 'bugs2_searchlayoutfields.include_desc';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FIELD, 100);
			parent::_addInteger(self::LENGTH, 10);
			parent::_addInteger(self::WIDTH, 10);
			parent::_addInteger(self::HEIGHT, 10);
			parent::_addBoolean(self::ICON);
			parent::_addInteger(self::ORDER, 10);
			parent::_addInteger(self::FIELD_TYPE, 10);
			parent::_addInteger(self::ROW, 10);
			parent::_addInteger(self::SPAN_COLS, 10);
			parent::_addInteger(self::SPAN_ROWS, 10);
			parent::_addVarchar(self::ALIGN, 10);
			parent::_addBoolean(self::INCLUDE_DESC);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::LAYOUT, B2DB::getTable('B2tSearchLayouts'), B2tSearchLayouts::ID);
		}
		
	}

?>