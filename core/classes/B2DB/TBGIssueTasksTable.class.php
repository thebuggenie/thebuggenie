<?php

	/**
	 * Issue tasks table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGIssueTasksTable extends TBGB2DBTable 
	{

		const B2DBNAME = 'issuetasks';
		const ID = 'issuetasks.id';
		const SCOPE = 'issuetasks.scope';
		const ISSUE = 'issuetasks.issue';
		const TITLE = 'issuetasks.title';
		const CONTENT = 'issuetasks.content';
		const STATUS = 'issuetasks.status';
		const ISSUE_STATUS = 'issuetasks.issue_status';
		const COMPLETED = 'issuetasks.completed';
		const ASSIGNED_TO = 'issuetasks.assigned_to';
		const ASSIGNED_TYPE = 'issuetasks.assigned_type';
		const POSTED = 'issuetasks.posted';
		const UPDATED = 'issuetasks.updated';
		const DUE = 'issuetasks.due';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addVarchar(self::TITLE, 200);
			parent::_addText(self::CONTENT, false);
			parent::_addInteger(self::ISSUE_STATUS, 5);
			parent::_addBoolean(self::COMPLETED);
			parent::_addInteger(self::ASSIGNED_TO, 10);
			parent::_addInteger(self::ASSIGNED_TYPE, 3);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::UPDATED, 10);
			parent::_addInteger(self::DUE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
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
			$crit->addInsert(self::POSTED, NOW);
			$crit->addInsert(self::UPDATED, NOW);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
	}
