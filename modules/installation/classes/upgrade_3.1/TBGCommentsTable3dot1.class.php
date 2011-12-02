<?php

	/**
	 * Comments table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Comments table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="comments")
	 */
	class TBGCommentsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'comments';
		const ID = 'comments.id';
		const SCOPE = 'comments.scope';
		const TARGET_ID = 'comments.target_id';
		const TARGET_TYPE = 'comments.target_type';
		const CONTENT = 'comments.content';
		const IS_PUBLIC = 'comments.is_public';
		const POSTED_BY = 'comments.posted_by';
		const POSTED = 'comments.posted';
		const UPDATED_BY = 'comments.updated_by';
		const UPDATED = 'comments.updated';
		const DELETED = 'comments.deleted';
		const MODULE = 'comments.module';
		const COMMENT_NUMBER = 'comments.comment_number';
		const SYSTEM_COMMENT = 'comments.system_comment';

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_ID, 10);
			parent::_addInteger(self::TARGET_TYPE, 3);
			parent::_addText(self::CONTENT, false);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::UPDATED, 10);
			parent::_addInteger(self::COMMENT_NUMBER, 10);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::IS_PUBLIC, true);
			parent::_addVarchar(self::MODULE, 50);
			parent::_addBoolean(self::SYSTEM_COMMENT);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::UPDATED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::POSTED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
		}

	}
