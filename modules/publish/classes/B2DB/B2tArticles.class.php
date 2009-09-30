<?php

	class B2tArticles extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_articles';
		const ID = 'bugs2_articles.id';
		const TITLE = 'bugs2_articles.title';
		const ARTICLE_NAME = 'bugs2_articles.article_name';
		const CONTENT = 'bugs2_articles.content';
		const IS_NEWS = 'bugs2_articles.is_news';
		const LINK = 'bugs2_articles.link';
		const DELETED = 'bugs2_articles.deleted';
		const IS_PUBLISHED = 'bugs2_articles.is_published';
		const DATE = 'bugs2_articles.date';
		const INTRO_TEXT = 'bugs2_articles.intro_text';
		const AUTHOR = 'bugs2_articles.author';
		const ORDER = 'bugs2_articles.order';
		const RELATED_PROJECT = 'bugs2_articles.related_project';
		const RELATED_TEAM = 'bugs2_articles.related_team';
		const ICON = 'bugs2_articles.icon';
		const SCOPE = 'bugs2_articles.scope';
		
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