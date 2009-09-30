<?php

	class B2tSavedSearches extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_savedsearches';
		const ID = 'bugs2_savedsearches.id';
		const SCOPE = 'bugs2_savedsearches.scope';
		const NAME = 'bugs2_savedsearches.name';
		const LAYOUT = 'bugs2_savedsearches.layout';
		const DESCRIPTION = 'bugs2_savedsearches.description';
		const APPLIES_TO = 'bugs2_savedsearches.applies_to';
		const IS_PUBLIC = 'bugs2_savedsearches.is_public';
		const UID = 'bugs2_savedsearches.uid';
		const GROUPBY = 'bugs2_savedsearches.groupby';

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