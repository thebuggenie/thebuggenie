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
			parent::_addVarchar(self::ARTICLE_NAME, 255);
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

		public function loadFixtures($scope)
		{
			$crit = $this->getCriteria();

			$crit->addInsert(self::ARTICLE_NAME, 'IndexMessage');
			$crit->addInsert(self::TITLE, 'Welcome to The Bug Genie');
			$crit->addInsert(self::AUTHOR, 0);
			$crit->addInsert(self::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::INTRO_TEXT, '');
			$crit->addInsert(self::CONTENT, "Thank you for installing The Bug Genie!
 
Please take a few moments setting up your new issue tracker, by clicking the [[TBG_ROUTE:configure|Configure]] menu option in the top menu.
From this page you can configure The Bug Genie the way you want.

For more information on getting started, have a look at GettingStarted, ConfiguringTheBugGenie and CreatingIssues.");
			$crit->addInsert(self::IS_NEWS, 1);
			$crit->addInsert(self::IS_PUBLISHED, 1);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
		}

		public function getArticleByName($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $name);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$row = $this->doSelectOne($crit);

			return $row;
		}

	}

?>