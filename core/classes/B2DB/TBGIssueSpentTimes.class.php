<?php

	/**
	 * Issue spent times table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue spent times table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssueSpentTimes extends B2DBTable
	{

		const B2DBNAME = 'issue_spenttimes';
		const ID = 'issue_spenttimes.id';
		const SCOPE = 'issue_spenttimes.scope';
		const ISSUE_ID = 'issue_spenttimes.issue_id';
		const EDITED_BY = 'issue_spenttimes.edited_by';
		const EDITED_AT = 'issue_spenttimes.edited_at';
		const SPENT_MONTHS = 'issue_spenttimes.spent_months';
		const SPENT_WEEKS = 'issue_spenttimes.spent_weeks';
		const SPENT_DAYS = 'issue_spenttimes.spent_days';
		const SPENT_HOURS = 'issue_spenttimes.spent_hours';
		const SPENT_POINTS = 'issue_spenttimes.spent_points';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, B2DB::getTable('TBGIssuesTable'), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::EDITED_BY, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addInteger(self::EDITED_AT, 10);
			parent::_addInteger(self::SPENT_MONTHS, 10);
			parent::_addInteger(self::SPENT_WEEKS, 10);
			parent::_addInteger(self::SPENT_DAYS, 10);
			parent::_addInteger(self::SPENT_HOURS, 10);
			parent::_addFloat(self::SPENT_POINTS);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
		}

		public function saveSpentTime($issue_id, $months, $weeks, $days, $hours, $points)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::SPENT_MONTHS, $months);
			$crit->addInsert(self::SPENT_WEEKS, $weeks);
			$crit->addInsert(self::SPENT_DAYS, $days);
			$crit->addInsert(self::SPENT_HOURS, $hours);
			$crit->addInsert(self::SPENT_POINTS, $points);
			$crit->addInsert(self::ISSUE_ID, $issue_id);
			$crit->addInsert(self::EDITED_AT, time());
			$crit->addInsert(self::EDITED_BY, TBGContext::getUser()->getID());
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$this->doInsert($crit);
		}

		public function getSpentTimesByDateAndIssueIDs($startdate, $enddate, $issue_ids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITED_AT, $startdate, B2DBCriteria::DB_GREATER_THAN_EQUAL);
			$crit->addWhere(self::EDITED_AT, $enddate, B2DBCriteria::DB_LESS_THAN_EQUAL);
			$crit->addWhere(self::ISSUE_ID, $issue_ids, B2DBCriteria::DB_IN);
			$crit->addOrderBy(self::EDITED_AT, B2DBCriteria::SORT_ASC);
			$res = $this->doSelect($crit);

			$retarr = array();
			while ($startdate < $enddate)
			{
				$retarr[date('md', $startdate)] = array();
				$startdate += 86400;
			}
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$retarr[date('md', $row->get(self::EDITED_AT))][$row->get(self::ISSUE_ID)] = $row->get(self::SPENT_POINTS);
				}
			}
			foreach ($retarr as $key => $vals)
			{
				$retarr[$key] = (count($vals)) ? array_sum($vals) : null;
			}

			return $retarr;
		}

	}
