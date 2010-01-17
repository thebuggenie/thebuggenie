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

		const B2DBNAME = 'comments';
		const ID = 'comments.id';
		const SCOPE = 'comments.scope';
		const TARGET_ID = 'comments.target_id';
		const TARGET_TYPE = 'comments.target_type';
		const TITLE = 'comments.title';
		const CONTENT = 'comments.content';
		const IS_PUBLIC = 'comments.is_public';
		const POSTED_BY = 'comments.posted_by';
		const POSTED = 'comments.posted';
		const UPDATED_BY = 'comments.updated_by';
		const UPDATED = 'comments.updated';
		const DELETED = 'comments.deleted';
		const MODULE = 'comments.module';
		const SYSTEM_COMMENT = 'comments.system_comment';

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

		public function getComments($target_id, $target_type)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET_ID, $target_id);
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::DELETED, 0);
			$crit->addOrderBy(self::POSTED, 'desc');
			$res = $this->doSelect($crit);
			return $res;
		}

	}
