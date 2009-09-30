<?php

	/**
	 * Issue tasks table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue tasks table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tIssueTasks extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_issuetasks';
		const ID = 'bugs2_issuetasks.id';
		const SCOPE = 'bugs2_issuetasks.scope';
		const ISSUE = 'bugs2_issuetasks.issue';
		const TITLE = 'bugs2_issuetasks.title';
		const CONTENT = 'bugs2_issuetasks.content';
		const STATUS = 'bugs2_issuetasks.status';
		const ISSUE_STATUS = 'bugs2_issuetasks.issue_status';
		const COMPLETED = 'bugs2_issuetasks.completed';
		const ASSIGNED_TO = 'bugs2_issuetasks.assigned_to';
		const ASSIGNED_TYPE = 'bugs2_issuetasks.assigned_type';
		const POSTED = 'bugs2_issuetasks.posted';
		const UPDATED = 'bugs2_issuetasks.updated';
		const DUE = 'bugs2_issuetasks.due';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE, B2DB::getTable('B2tIssues'), B2tIssues::ID);
			parent::_addVarchar(self::TITLE, 200);
			parent::_addText(self::CONTENT, false);
			parent::_addInteger(self::ISSUE_STATUS, 5);
			parent::_addBoolean(self::COMPLETED);
			parent::_addInteger(self::ASSIGNED_TO, 10);
			parent::_addInteger(self::ASSIGNED_TYPE, 3);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::UPDATED, 10);
			parent::_addInteger(self::DUE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::STATUS, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
		}
		
		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_id);
			$crit->addOrderBy(self::ID, B2DBCriteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function createNew($title, $content, $issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ISSUE, $issue_id);
			$crit->addInsert(self::TITLE, $title);
			$crit->addInsert(self::CONTENT, $content);
			$crit->addInsert(self::POSTED, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::UPDATED, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
	}
