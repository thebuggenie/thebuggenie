<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Log table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Log table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGLogTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const TYPE_ISSUE = 1;
		
		const LOG_MILESTONE_REMOVE = 1;
		const LOG_MILESTONE_ADD = 2;
		const LOG_ISSUE_STATUS = 3;
		const LOG_ISSUE_USERS = 4;
		const LOG_ISSUE_UPDATE = 5;
		const LOG_ISSUE_ISSUETYPE = 6;
		const LOG_ISSUE_CATEGORY = 7;
		const LOG_ISSUE_REPRODUCABILITY = 8;
		const LOG_ISSUE_PERCENT = 9;
		const LOG_ISSUE_TIME_ESTIMATED = 10;
		const LOG_ISSUE_DEPENDS = 11;
		const LOG_ISSUE_RESOLUTION = 12;
		const LOG_ISSUE_PRIORITY = 13;
		const LOG_ISSUE_CLOSE = 14;
		const LOG_AFF_ADD = 15;
		const LOG_AFF_UPDATE = 16;
		const LOG_AFF_DELETE = 17;
		const LOG_TASK_ADD = 18;
		const LOG_TASK_UPDATE = 19;
		const LOG_TASK_DELETE = 20;
		const LOG_ISSUE_TEAM = 21;
		const LOG_ISSUE_REOPEN = 22;
		const LOG_TASK_COMPLETED = 23;
		const LOG_TASK_REOPENED = 24;
		const LOG_TASK_STATUS = 25;
		const LOG_TASK_ASSIGN_USER = 26;
		const LOG_TASK_ASSIGN_TEAM = 27;
		const LOG_COMMENT = 28;
		const LOG_ISSUE_CREATED = 29;
		const LOG_ISSUE_SEVERITY = 30;
		const LOG_ISSUE_MILESTONE = 31;
		const LOG_ISSUE_TIME_SPENT = 32;
		const LOG_ISSUE_ASSIGNED = 33;
		const LOG_ISSUE_OWNED = 34;
		const LOG_ISSUE_POSTED = 35;
		const LOG_ISSUE_CUSTOMFIELD_CHANGED = 36;
		const LOG_ISSUE_PAIN_BUG_TYPE = 37;
		const LOG_ISSUE_PAIN_EFFECT = 38;
		const LOG_ISSUE_PAIN_LIKELIHOOD = 39;
		const LOG_ISSUE_PAIN_CALCULATED = 40;
		const LOG_ISSUE_BLOCKED = 41;
		const LOG_ISSUE_UNBLOCKED = 42;
		
		const B2DBNAME = 'log';
		const ID = 'log.id';
		const SCOPE = 'log.scope';
		const TARGET = 'log.target';
		const TARGET_TYPE = 'log.target_type';
		const CHANGE_TYPE = 'log.change_type';
		const PREVIOUS_VALUE = 'log.previous_value';
		const CURRENT_VALUE = 'log.current_value';
		const TEXT = 'log.text';
		const TIME = 'log.time';
		const UID = 'log.uid';

		/**
		 * Return an instance of TBGLogTable
		 * 
		 * @return TBGLogTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGLogTable');
		}
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET, 10);
			parent::_addInteger(self::TARGET_TYPE, 3);
			parent::_addInteger(self::CHANGE_TYPE, 3);
			parent::_addText(self::TEXT, false);
			parent::_addText(self::PREVIOUS_VALUE, false);
			parent::_addText(self::CURRENT_VALUE, false);
			parent::_addInteger(self::TIME, 10);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function createNew($target, $target_type, $change_type, $text = null, $uid = 0, $time = null)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::TARGET, $target);
			$crit->addInsert(self::TARGET_TYPE, $target_type);
			$crit->addInsert(self::CHANGE_TYPE, $change_type);
			if ($text !== null)
			{
				$crit->addInsert(self::TEXT, $text);
			}
			if ($time === null)
			{
				$crit->addInsert(self::TIME, NOW);
			}
			else
			{
				$crit->addInsert(self::TIME, $time);
			}
			$crit->addInsert(self::UID, $uid);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET, $issue_id);
			$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
			$crit->addOrderBy(self::TIME, Criteria::SORT_ASC);
			
			$ret_arr = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$ret_arr[$row->get(self::ID)] = array('change_type' => $row->get(self::CHANGE_TYPE), 'text' => $row->get(self::TEXT), 'previous_value' => $row->get(self::PREVIOUS_VALUE), 'current_value' => $row->get(self::CURRENT_VALUE), 'timestamp' => $row->get(self::TIME), 'user_id' => $row->get(self::UID), 'target' => $row->get(self::TARGET), 'target_type' => $row->get(self::TARGET_TYPE));
				}
			}
	
			return $ret_arr;
			
		}
		
		public function getByUserID($user_id, $limit = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addOrderBy(self::TIME, Criteria::SORT_DESC);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			
			$ret_arr = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$ret_arr[$row->get(self::ID)] = array('change_type' => $row->get(self::CHANGE_TYPE), 'text' => $row->get(self::TEXT), 'previous_value' => $row->get(self::PREVIOUS_VALUE), 'current_value' => $row->get(self::CURRENT_VALUE), 'timestamp' => $row->get(self::TIME), 'user_id' => $row->get(self::UID), 'target' => $row->get(self::TARGET), 'target_type' => $row->get(self::TARGET_TYPE));
				}
			}
	
			return $ret_arr;
			
		}

		public function getByProjectID($project_id, $limit = 20, $offset = null)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(TBGIssuesTable::getTable(), TBGIssuesTable::ID, self::TARGET);
			$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
			$crit->addWhere(TBGIssuesTable::PROJECT_ID, $project_id);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			if ($offset !== null)
			{
				$crit->setOffset($offset);
			}
			
			$crit->addOrderBy(self::TIME, Criteria::SORT_DESC);

			$ret_arr = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$ret_arr[$row->get(self::ID)] = array('change_type' => $row->get(self::CHANGE_TYPE), 'text' => $row->get(self::TEXT), 'previous_value' => $row->get(self::PREVIOUS_VALUE), 'current_value' => $row->get(self::CURRENT_VALUE), 'timestamp' => $row->get(self::TIME), 'user_id' => $row->get(self::UID), 'target' => $row->get(self::TARGET), 'target_type' => $row->get(self::TARGET_TYPE));
				}
			}

			return $ret_arr;

		}

		public function getImportantByProjectID($project_id, $limit = 20, $offset = null)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(TBGIssuesTable::getTable(), TBGIssuesTable::ID, self::TARGET);
			$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
			$crit->addWhere(self::CHANGE_TYPE, array(self::LOG_ISSUE_CREATED, self::LOG_ISSUE_CLOSE), Criteria::DB_IN);
			$crit->addWhere(TBGIssuesTable::PROJECT_ID, $project_id);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			if ($offset !== null)
			{
				$crit->setOffset($offset);
			}

			$crit->addOrderBy(self::TIME, Criteria::SORT_DESC);

			$ret_arr = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$ret_arr[$row->get(self::ID)] = array('change_type' => $row->get(self::CHANGE_TYPE), 'text' => $row->get(self::TEXT), 'previous_value' => $row->get(self::PREVIOUS_VALUE), 'current_value' => $row->get(self::CURRENT_VALUE), 'timestamp' => $row->get(self::TIME), 'user_id' => $row->get(self::UID), 'target' => $row->get(self::TARGET), 'target_type' => $row->get(self::TARGET_TYPE));
				}
			}

			return $ret_arr;

		}

		public function getLast15IssueCountsByProjectID($project_id)
		{
			$retarr = array();

			for ($cc = 15; $cc >= 0; $cc--)
			{
				$crit = $this->getCriteria();
				$joinedtable = $crit->addJoin(TBGIssuesTable::getTable(), TBGIssuesTable::ID, self::TARGET);
				$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
				$crit->addWhere(self::CHANGE_TYPE, array(self::LOG_ISSUE_CREATED, self::LOG_ISSUE_CLOSE), Criteria::DB_IN);
				$crit->addWhere(TBGIssuesTable::PROJECT_ID, $project_id);
				$crit->addWhere(TBGIssuesTable::DELETED, false);
				$crit->addJoin(TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID, TBGIssuesTable::ISSUE_TYPE, array(), Criteria::DB_LEFT_JOIN, $joinedtable);
				$crit->addWhere(TBGIssueTypesTable::ICON, 'bug_report');
				$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
				$ctn = $crit->returnCriterion(self::TIME, NOW - (86400 * ($cc + 1)), Criteria::DB_GREATER_THAN_EQUAL);
				$ctn->addWhere(self::TIME, NOW - (86400 * $cc), Criteria::DB_LESS_THAN_EQUAL);
				$crit->addWhere($ctn);

				$crit2 = clone $crit;

				$crit->addWhere(self::CHANGE_TYPE, self::LOG_ISSUE_CLOSE);
				$crit2->addWhere(self::CHANGE_TYPE, self::LOG_ISSUE_CREATED);

				$retarr[0][$cc] = $this->doCount($crit);
				$retarr[1][$cc] = $this->doCount($crit2);
			}
			return $retarr;
		}

	}
