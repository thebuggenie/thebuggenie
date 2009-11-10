<?php

	class B2tArticles extends B2DBTable 
	{
		const B2DBNAME = 'articles';
		const ID = 'articles.id';
		const TITLE = 'articles.title';
		const ARTICLE_NAME = 'articles.article_name';
		const CONTENT = 'articles.content';
		const IS_NEWS = 'articles.is_news';
		const LINK = 'articles.link';
		const DELETED = 'articles.deleted';
		const IS_PUBLISHED = 'articles.is_published';
		const DATE = 'articles.date';
		const INTRO_TEXT = 'articles.intro_text';
		const AUTHOR = 'articles.author';
		const ORDER = 'articles.order';
		const RELATED_PROJECT = 'articles.related_project';
		const RELATED_TEAM = 'articles.related_team';
		const ICON = 'articles.icon';
		const SCOPE = 'articles.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 255);
			parent::_addText(self::INTRO_TEXT, false);
			parent::_addText(self::CONTENT, false);
			parent::_addBoolean(self::IS_NEWS);
			parent::_addText(self::LINK, false);
			parent::_addBoolean(self::IS_PUBLISHED);
			parent::_addBoolean(self::DELETED);
			parent::_addInteger(self::DATE, 10);
			parent::_addInteger(self::ORDER, 5);
			parent::_addVarchar(self::ICON, 50, '');
			parent::_addForeignKeyColumn(self::AUTHOR, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::RELATED_PROJECT, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::RELATED_TEAM, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
	}

?>