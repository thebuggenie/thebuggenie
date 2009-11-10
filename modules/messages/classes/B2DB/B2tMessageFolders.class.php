<?php

	class B2tMessageFolders extends B2DBTable 
	{
		const B2DBNAME = 'messagefolders';
		const ID = 'messagefolders.id';
		const UID = 'messagefolders.uid';
		const FOLDERNAME = 'messagefolders.foldername';
		const PARENT_FOLDER = 'messagefolders.parent_folder';
		const SCOPE = 'messagefolders.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FOLDERNAME, 60);
			parent::_addInteger(self::PARENT_FOLDER, 10);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
	}

?>