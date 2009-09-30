<?php

	class B2tBillboardPosts extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_billboardposts';
		const ID = 'bugs2_billboardposts.id';
		const TITLE = 'bugs2_billboardposts.title';
		const TARGET_BOARD = 'bugs2_billboardposts.target_board';
		const ARTICLE_ID = 'bugs2_billboardposts.article_id';
		const CONTENT = 'bugs2_billboardposts.content';
		const LINK = 'bugs2_billboardposts.link';
		const IS_DELETED = 'bugs2_billboardposts.is_deleted';
		const DATE = 'bugs2_billboardposts.date';
		const AUTHOR = 'bugs2_billboardposts.author';
		const SCOPE = 'bugs2_billboardposts.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 255);
			parent::_addText(self::CONTENT, false);
			parent::_addText(self::LINK, false);
			parent::_addBoolean(self::IS_DELETED);
			parent::_addInteger(self::DATE, 10);
			parent::_addInteger(self::TARGET_BOARD, 10);
			parent::_addForeignKeyColumn(self::ARTICLE_ID, B2DB::getTable('B2tArticles'), B2tArticles::ID);
			parent::_addForeignKeyColumn(self::AUTHOR, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
	}

?>