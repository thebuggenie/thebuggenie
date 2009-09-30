<?php

	class B2tCalendars extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_calendars';
		const ID = 'bugs2_calendars.id';
		const SCOPE = 'bugs2_calendars.scope';
		const UID = 'bugs2_calendars.uid';
		const GID = 'bugs2_calendars.gid';
		const TID = 'bugs2_calendars.tid';
		const EXCLUSIVE = 'bugs2_calendars.exclusive';
		const SHARED = 'bugs2_calendars.shared';
		const NAME = 'bugs2_calendars.name';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::EXCLUSIVE);
			parent::_addBoolean(self::SHARED);
			parent::_addVarchar(self::NAME, 100, '');
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::GID, B2DB::getTable('B2tGroups'), B2tGroups::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
	}

?>