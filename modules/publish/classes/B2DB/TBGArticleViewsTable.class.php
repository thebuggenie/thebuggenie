<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	class TBGArticleViewsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'articleviews';
		const ID = 'articleviews.id';
		const ARTICLE_ID = 'articleviews.article_id';
		const USER_ID = 'articleviews.user_id';
		const SCOPE = 'articleviews.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::ARTICLE_ID, TBGArticlesTable::getTable(), TBGArticlesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
	}

