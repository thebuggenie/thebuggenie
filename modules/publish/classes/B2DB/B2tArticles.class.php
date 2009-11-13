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

		public function loadFixtures($scope)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::TITLE, 'Welcome to The Bug Genie');
			$crit->addInsert(self::AUTHOR, 1);
			$crit->addInsert(self::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::ICON, 'install');
			$crit->addInsert(self::INTRO_TEXT, '[p]This is a test article to display the article module.[/p]');
			$crit->addInsert(self::CONTENT, '[p]"BUGS" has been reworked, rewritten and rebuilt from scratch to make your development life easier. Several new features has been added to make BUGS 2 the most powerful and versatile solution for software developers. [/p][p]
[/p][p]In addition to these features, BUGS 2\'s now has a module-based architecture as well as our new B2DB PHP database ORM. This makes extending and improving the functionality in BUGS 2 much easier than before. [/p][p]
[/p][p]BUGS 2 comes with an extensive online help system, which makes using BUGS 2 easier than with it\'s predecessor. Online help links are included in several places, and will give you helpful tips & guides where you need them. [/p][p]
[/p][p][b]Highlights includes:[/b]
[/p][list][*]Improved user management with detailed access control[*]Improved search functionality with grouping support[*]Improved messaging functionality with folders and search[*]Improved issue reporting wizard with automated duplicate search[*][b]New[/b]: Articles & news[*][b]New[/b]: Subversion integration[*][b]New[/b]: Calendars with events, meetings and todo\'s[*][b]New[/b]: Global and team billboards[*][b]New: [/b]Project dashboard with live statistics and overview[*][b]New: [/b]Automated roadmap generation[*][b]New: [/b]Support for issue sub-tasks[*][b]New: [/b]Support for related issues[*][b]New: [/b]Voting[/list][p]We\'re very happy with how BUGS 2 has turned out, so try out the new features, build, extend and improve upon the functionality already in BUGS 2, and see how your life as a developer will be a lot easier![/p]');
			$crit->addInsert(self::IS_NEWS, 1);
			$crit->addInsert(self::IS_PUBLISHED, 1);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
		}

	}

?>