<?php

	class B2tSVNintegration extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_svnintegration';
		const ID = 'bugs2_svnintegration.id';
		const SCOPE = 'bugs2_svnintegration.scope';
		const ISSUE_NO = 'bugs2_svnintegration.issue_no';
		const FILE_NAME = 'bugs2_svnintegration.file_name';
		const OLD_REV = 'bugs2_svnintegration.old_rev';
		const NEW_REV = 'bugs2_svnintegration.new_rev';
		const AUTHOR = 'bugs2_svnintegration.author';
		const DATE = 'bugs2_svnintegration.date';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addText(self::FILE_NAME, false);
			parent::_addVarchar(self::OLD_REV, 10);
			parent::_addVarchar(self::NEW_REV, 10);
			parent::_addVarchar(self::AUTHOR, 100);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::ISSUE_NO, B2DB::getTable('B2tIssues'), B2tIssues::ID);
		}
	}

?>