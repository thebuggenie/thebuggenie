<?php

	/**
	 * Log table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class B2tLog extends B2DBTable 
	{

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
		
		const B2DBNAME = 'bugs2_log';
		const ID = 'bugs2_log.id';
		const SCOPE = 'bugs2_log.scope';
		const TARGET = 'bugs2_log.target';
		const TARGET_TYPE = 'bugs2_log.target_type';
		const CHANGE_TYPE = 'bugs2_log.change_type';
		const PREVIOUS_VALUE = 'bugs2_log.previous_value';
		const CURRENT_VALUE = 'bugs2_log.current_value';
		const TEXT = 'bugs2_log.text';
		const TIME = 'bugs2_log.time';
		const UID = 'bugs2_log.uid';

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
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function createNew($target, $target_type, $change_type, $text = null, $uid = 0)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::TARGET, $target);
			$crit->addInsert(self::TARGET_TYPE, $target_type);
			$crit->addInsert(self::CHANGE_TYPE, $change_type);
			if ($text !== null)
			{
				$crit->addInsert(self::TEXT, $text);
			}
			$crit->addInsert(self::TIME, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::UID, $uid);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET, $issue_id);
			$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
			$crit->addOrderBy(self::TIME, B2DBCriteria::SORT_ASC);
			
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
			$crit->addOrderBy(self::TIME, B2DBCriteria::SORT_DESC);
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

		public function getByProjectID($project_id, $limit = 20)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(B2DB::getTable('B2tIssues'), B2tIssues::ID, self::TARGET);
			$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
			$crit->addWhere(B2tIssues::PROJECT_ID, $project_id);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			$crit->addOrderBy(self::TIME, B2DBCriteria::SORT_DESC);

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

		public function getImportantByProjectID($project_id, $limit = 20)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(B2DB::getTable('B2tIssues'), B2tIssues::ID, self::TARGET);
			$crit->addWhere(self::TARGET_TYPE, self::TYPE_ISSUE);
			$crit->addWhere(self::CHANGE_TYPE, array(self::LOG_ISSUE_CREATED, self::LOG_ISSUE_CLOSE), B2DBCriteria::DB_IN);
			$crit->addWhere(B2tIssues::PROJECT_ID, $project_id);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			$crit->addOrderBy(self::TIME, B2DBCriteria::SORT_DESC);

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


	}
