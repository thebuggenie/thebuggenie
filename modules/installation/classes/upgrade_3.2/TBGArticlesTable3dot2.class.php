<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * @Table(name="articles_32")
	 */
	class TBGArticlesTable3dot2 extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'articles';
		const ID = 'articles.id';
		const NAME = 'articles.name';
		const CONTENT = 'articles.content';
		const IS_PUBLISHED = 'articles.is_published';
		const DATE = 'articles.date';
		const AUTHOR = 'articles.author';
		const SCOPE = 'articles.scope';
		
		public function __construct()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 255);
			parent::_addText(self::CONTENT, false);
			parent::_addBoolean(self::IS_PUBLISHED);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::AUTHOR, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}

