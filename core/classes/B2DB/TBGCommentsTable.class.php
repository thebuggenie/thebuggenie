<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

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
	 */
	class TBGCommentsTable extends TBGB2DBTable 
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

		/**
		 * Return an instance of TBGCommentsTable
		 * 
		 * @return TBGCommentsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGCommentsTable');
		}
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
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

		public function getComments($target_id, $target_type, $sort_order = Criteria::SORT_ASC)
		{
			$crit = $this->getCriteria();
			if($target_id != 0) 
			{
				$crit->addWhere(self::TARGET_ID, $target_id);		
			}
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::DELETED, 0);
			$crit->addOrderBy(self::POSTED, $sort_order);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function countComments($target_id, $target_type, $include_system_comments = true)
		{
			$crit = $this->getCriteria();
			if($target_id != 0) 
			{
				$crit->addWhere(self::TARGET_ID, $target_id);		
			}
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::DELETED, 0);
			if (!$include_system_comments)
				$crit->addWhere(self::SYSTEM_COMMENT, false);
			
			return $this->doCount($crit);
		}

		public function getNextCommentNumber($target_id, $target_type)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::COMMENT_NUMBER, 'max_no', Criteria::DB_MAX, '', '+1');
			$crit->addWhere(self::TARGET_ID, $target_id);
			$crit->addWhere(self::TARGET_TYPE, $target_type);

			$row = $this->doSelectOne($crit);
			return ($row->get('max_no')) ? $row->get('max_no') : 1;
		}
		
		public function getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit = 10)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::POSTED_BY, $user_id);
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::SYSTEM_COMMENT, false);
			$crit->addOrderBy(self::POSTED, Criteria::SORT_DESC);
			$crit->setLimit($limit);
			
			return $this->doSelect($crit);
		}		

	}
