<?php

	class B2tSavedSearches extends B2DBTable 
	{
		const B2DBNAME = 'savedsearches';
		const ID = 'savedsearches.id';
		const SCOPE = 'savedsearches.scope';
		const NAME = 'savedsearches.name';
		const LAYOUT = 'savedsearches.layout';
		const DESCRIPTION = 'savedsearches.description';
		const APPLIES_TO = 'savedsearches.applies_to';
		const IS_PUBLIC = 'savedsearches.is_public';
		const UID = 'savedsearches.uid';
		const GROUPBY = 'savedsearches.groupby';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::DESCRIPTION, 255, '');
			parent::_addBoolean(self::IS_PUBLIC);
			parent::_addVarchar(self::GROUPBY, 30);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::APPLIES_TO, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::LAYOUT, B2DB::getTable('B2tSearchLayouts'), B2tSearchLayouts::ID);
		}
		
	}
?>