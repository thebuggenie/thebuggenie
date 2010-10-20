<?php

	class TBGCalendarsTable extends B2DBTable 
	{
		const B2DBNAME = 'calendars';
		const ID = 'calendars.id';
		const SCOPE = 'calendars.scope';
		const UID = 'calendars.uid';
		const GID = 'calendars.gid';
		const TID = 'calendars.tid';
		const EXCLUSIVE = 'calendars.exclusive';
		const SHARED = 'calendars.shared';
		const NAME = 'calendars.name';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::EXCLUSIVE);
			parent::_addBoolean(self::SHARED);
			parent::_addVarchar(self::NAME, 100, '');
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::GID, TBGGroupsTable::getTable(), TBGGroupsTable::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
	}

