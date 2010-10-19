<?php

	class TBGMessagesTable extends B2DBTable 
	{
		const B2DBNAME = 'messages';
		const ID = 'messages.id';
		const FROM_USER = 'messages.from_user';
		const TO_USER = 'messages.to_user';
		const IS_READ = 'messages.is_read';
		const TITLE = 'messages.title';
		const BODY = 'messages.body';
		const TO_TEAM = 'messages.to_team';
		const URGENT = 'messages.urgent';
		const FOLDER = 'messages.folder';
		const DELETED = 'messages.deleted';
		const SENT = 'messages.sent';
		const DELETED_SENT = 'messages.deleted_sent';
		const SCOPE = 'messages.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::IS_READ);
			parent::_addVarchar(self::TITLE, 300);
			parent::_addText(self::BODY, false);
			parent::_addBoolean(self::URGENT);
			parent::_addBoolean(self::DELETED);
			parent::_addInteger(self::SENT, 10);
			parent::_addBoolean(self::DELETED_SENT);
			parent::_addForeignKeyColumn(self::FROM_USER, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::TO_USER, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::TO_TEAM, B2DB::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::FOLDER, B2DB::getTable('TBGMessageFoldersTable'), TBGMessageFoldersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
	}

?>