<?php

	class TBGBillboardPostsTable extends TBGB2DBTable 
	{
		const B2DBNAME = 'billboardposts';
		const ID = 'billboardposts.id';
		const TITLE = 'billboardposts.title';
		const TARGET_BOARD = 'billboardposts.target_board';
		const ARTICLE_ID = 'billboardposts.article_id';
		const CONTENT = 'billboardposts.content';
		const LINK = 'billboardposts.link';
		const IS_DELETED = 'billboardposts.is_deleted';
		const DATE = 'billboardposts.date';
		const AUTHOR = 'billboardposts.author';
		const SCOPE = 'billboardposts.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 255);
			parent::_addText(self::CONTENT, false);
			parent::_addText(self::LINK, false);
			parent::_addBoolean(self::IS_DELETED);
			parent::_addInteger(self::DATE, 10);
			parent::_addInteger(self::TARGET_BOARD, 10);
			parent::_addForeignKeyColumn(self::ARTICLE_ID, TBGArticlesTable::getTable(), TBGArticlesTable::ID);
			parent::_addForeignKeyColumn(self::AUTHOR, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function loadFixtures(TBGScope $scope)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_ID, 1);
			$crit->addInsert(self::AUTHOR, 1);
			$crit->addInsert(self::DATE, NOW);
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::TITLE, 'Welcome to the billboards!');
			$crit->addInsert(self::TARGET_BOARD, 0);
			$crit->addInsert(self::CONTENT, '[p]This is a post on the billboards - a place where users and developers can share comments, ideas, links, etc. There is a global billboard, and each team has its own billboard.[/p]');
			$res = $this->doInsert($crit);
		}
	}

