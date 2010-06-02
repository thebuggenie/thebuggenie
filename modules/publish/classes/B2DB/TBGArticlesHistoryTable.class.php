<?php

	class TBGArticlesHistoryTable extends B2DBTable
	{
		const B2DBNAME = 'articleshistory';
		const ID = 'articleshistory.id';
		const TITLE = 'articleshistory.title';
		const ARTICLE_NAME = 'articleshistory.article_name';
		const OLD_CONTENT = 'articleshistory.old_content';
		const NEW_CONTENT = 'articleshistory.new_content';
		const DATE = 'articleshistory.date';
		const AUTHOR = 'articleshistory.author';
		const SCOPE = 'articleshistory.scope';
		
		/**
		 * Return an instance of this table
		 *
		 * @return TBGArticlesHistoryTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGArticlesHistoryTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 255);
			parent::_addVarchar(self::ARTICLE_NAME, 255);
			parent::_addText(self::OLD_CONTENT, false);
			parent::_addText(self::NEW_CONTENT, false);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::AUTHOR, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
		}

		public function getHistoryByArticleName($article_name)
		{

		}

	}

?>