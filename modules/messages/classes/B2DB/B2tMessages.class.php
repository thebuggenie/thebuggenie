<?php

	class B2tMessages extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_messages';
		const ID = 'bugs2_messages.id';
		const FROM_USER = 'bugs2_messages.from_user';
		const TO_USER = 'bugs2_messages.to_user';
		const IS_READ = 'bugs2_messages.is_read';
		const TITLE = 'bugs2_messages.title';
		const BODY = 'bugs2_messages.body';
		const TO_TEAM = 'bugs2_messages.to_team';
		const URGENT = 'bugs2_messages.urgent';
		const FOLDER = 'bugs2_messages.folder';
		const DELETED = 'bugs2_messages.deleted';
		const SENT = 'bugs2_messages.sent';
		const DELETED_SENT = 'bugs2_messages.deleted_sent';
		const SCOPE = 'bugs2_messages.scope';
		
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
			parent::_addForeignKeyColumn(self::FROM_USER, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::TO_USER, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::TO_TEAM, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::FOLDER, B2DB::getTable('B2tMessageFolders'), B2tMessageFolders::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
	}

?>