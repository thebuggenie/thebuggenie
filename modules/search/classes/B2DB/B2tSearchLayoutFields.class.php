<?php

	class B2tSearchLayoutFields extends B2DBTable 
	{
		const B2DBNAME = 'searchlayoutfields';
		const ID = 'searchlayoutfields.id';
		const SCOPE = 'searchlayoutfields.scope';
		const FIELD = 'searchlayoutfields.field';
		const LENGTH = 'searchlayoutfields.length';
		const WIDTH = 'searchlayoutfields.width';
		const HEIGHT = 'searchlayoutfields.height';
		const ICON = 'searchlayoutfields.icon';
		const ORDER = 'searchlayoutfields.order';
		const LAYOUT = 'searchlayoutfields.layout';
		const FIELD_TYPE = 'searchlayoutfields.field_type';
		const ROW = 'searchlayoutfields.row';
		const SPAN_COLS = 'searchlayoutfields.span_cols';
		const SPAN_ROWS = 'searchlayoutfields.span_rows';
		const ALIGN = 'searchlayoutfields.align';
		const INCLUDE_DESC = 'searchlayoutfields.include_desc';

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