<?php

	class TBGSVNintegrationTable extends B2DBTable 
	{
		const B2DBNAME = 'svnintegration';
		const ID = 'svnintegration.id';
		const SCOPE = 'svnintegration.scope';
		const ISSUE_NO = 'svnintegration.issue_no';
		const FILE_NAME = 'svnintegration.file_name';
		const OLD_REV = 'svnintegration.old_rev';
		const NEW_REV = 'svnintegration.new_rev';
		const AUTHOR = 'svnintegration.author';
		const DATE = 'svnintegration.date';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addText(self::FILE_NAME, false);
			parent::_addVarchar(self::OLD_REV, 10);
			parent::_addVarchar(self::NEW_REV, 10);
			parent::_addVarchar(self::AUTHOR, 100);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE_NO, B2DB::getTable('TBGIssuesTable'), TBGIssuesTable::ID);
		}
	}