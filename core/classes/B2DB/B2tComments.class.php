<?php

	/**
	 * Comments table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Comments table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tComments extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_comments';
		const ID = 'bugs2_comments.id';
		const SCOPE = 'bugs2_comments.scope';
		const TARGET_ID = 'bugs2_comments.target_id';
		const TARGET_TYPE = 'bugs2_comments.target_type';
		const TITLE = 'bugs2_comments.title';
		const CONTENT = 'bugs2_comments.content';
		const IS_PUBLIC = 'bugs2_comments.is_public';
		const POSTED_BY = 'bugs2_comments.posted_by';
		const POSTED = 'bugs2_comments.posted';
		const UPDATED_BY = 'bugs2_comments.updated_by';
		const UPDATED = 'bugs2_comments.updated';
		const DELETED = 'bugs2_comments.deleted';
		const MODULE = 'bugs2_comments.module';
		const SYSTEM_COMMENT = 'bugs2_comments.system_comment';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_ID, 10);
			parent::_addInteger(self::TARGET_TYPE, 3);
			parent::_addVarchar(self::TITLE, 100);
			parent::_addText(self::CONTENT, false);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::UPDATED, 10);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::IS_PUBLIC, true);
			parent::_addVarchar(self::MODULE, 50);
			parent::_addBoolean(self::SYSTEM_COMMENT);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::UPDATED_BY, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::POSTED_BY, B2DB::getTable('B2tUsers'), B2tUsers::ID);
		}
		
	}
