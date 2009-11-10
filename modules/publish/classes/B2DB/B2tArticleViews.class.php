<?php

	class B2tArticleViews extends B2DBTable 
	{
		const B2DBNAME = 'articleviews';
		const ID = 'articleviews.id';
		const ARTICLE_ID = 'articleviews.article_id';
		const USER_ID = 'articleviews.user_id';
		const SCOPE = 'articleviews.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::USER_ID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::ARTICLE_ID, B2DB::getTable('B2tArticles'), B2tArticles::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
	}

?>