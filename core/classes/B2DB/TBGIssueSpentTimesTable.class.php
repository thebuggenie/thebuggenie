<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue spent times table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue spent times table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issue_spenttimes")
	 * @Entity(class="TBGIssueSpentTime")
	 */
	class TBGIssueSpentTimesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 2;
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
		const ACTIVITY_TYPE = 'issue_spenttimes.activity_type';

//		protected function _initialize()
//		{
//			parent::_setup(self::B2DBNAME, self::ID);
//			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
//			parent::_addForeignKeyColumn(self::EDITED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
//			parent::_addInteger(self::EDITED_AT, 10);
//			parent::_addInteger(self::SPENT_MONTHS, 10);
//			parent::_addInteger(self::SPENT_WEEKS, 10);
//			parent::_addInteger(self::SPENT_DAYS, 10);
//			parent::_addInteger(self::SPENT_HOURS, 10);
//			parent::_addFloat(self::SPENT_POINTS);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}
//
//		public function saveSpentTime($issue_id, $months, $weeks, $days, $hours, $points)
//		{
//			$crit = $this->getCriteria();
//			$crit->addInsert(self::SPENT_MONTHS, $months);
//			$crit->addInsert(self::SPENT_WEEKS, $weeks);
//			$crit->addInsert(self::SPENT_DAYS, $days);
//			$crit->addInsert(self::SPENT_HOURS, $hours);
//			$crit->addInsert(self::SPENT_POINTS, $points);
//			$crit->addInsert(self::ISSUE_ID, $issue_id);
//			$crit->addInsert(self::EDITED_AT, NOW);
//			$crit->addInsert(self::EDITED_BY, TBGContext::getUser()->getID());
//			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
//			$this->doInsert($crit);
//		}

		public function getSpentTimesByDateAndIssueIDs($startdate, $enddate, $issue_ids)
		{
			$points_retarr = array();
			$hours_retarr = array();
			if ($startdate && $enddate)
			{
				$sd = $startdate;
				while ($sd <= $enddate)
				{
					$points_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = array();
					$hours_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = array();
					$sd += 86400;
				}
			}

			if (count($issue_ids))
			{
				$crit = $this->getCriteria();
				if ($startdate && $enddate)
					$crit->addWhere(self::EDITED_AT, $enddate, \b2db\Criteria::DB_LESS_THAN_EQUAL);

				$crit->addWhere(self::ISSUE_ID, $issue_ids, \b2db\Criteria::DB_IN);
				$crit->addOrderBy(self::EDITED_AT, \b2db\Criteria::SORT_ASC);

				if ($res = $this->doSelect($crit))
				{
					while ($row = $res->getNextRow())
					{
						if ($startdate && $enddate)
						{
							$sd = ($row->get(self::EDITED_AT) >= $startdate) ? $row->get(self::EDITED_AT) : $startdate;
							$date = mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd));
							foreach ($points_retarr as $key => &$details)
							{
								if ($key < $date) continue;
								$details[$row->get(self::ISSUE_ID)] = $row->get(self::SPENT_POINTS);
							}
							foreach ($hours_retarr as $key => &$details)
							{
								if ($key < $date) continue;
								$details[$row->get(self::ISSUE_ID)] = $row->get(self::SPENT_HOURS);
							}
						}
						else
						{
							$hours_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::SPENT_HOURS);
							$points_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::SPENT_POINTS);
						}
					}
				}
			}

			if ($startdate && $enddate)
			{
				foreach ($points_retarr as $key => $vals)
					$points_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

				foreach ($hours_retarr as $key => $vals)
					$hours_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;
			}

			return array('points' => $points_retarr, 'hours' => $hours_retarr);
		}
		
		public function getAllSpentTimesForFixing()
		{
			$crit = $this->getCriteria();
			$crit->addOrderBy(self::ISSUE_ID, Criteria::SORT_ASC);
			$crit->addOrderBy(self::ID, Criteria::SORT_ASC);
			
			$res = $this->doSelect($crit);
			$ret_arr = array();
			
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$ret_arr[$row[self::ISSUE_ID]][] = $row;
				}
			}
			
			return $ret_arr;
		}
		
		public function fixRow($row, $prev_times)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::SPENT_POINTS, $row[self::SPENT_POINTS] - $prev_times['points']);
			$crit->addUpdate(self::SPENT_HOURS, $row[self::SPENT_HOURS] - $prev_times['hours']);
			$crit->addUpdate(self::SPENT_DAYS, $row[self::SPENT_DAYS] - $prev_times['days']);
			$crit->addUpdate(self::SPENT_WEEKS, $row[self::SPENT_WEEKS] - $prev_times['weeks']);
			$crit->addUpdate(self::SPENT_MONTHS, $row[self::SPENT_MONTHS] - $prev_times['months']);
			
			$this->doUpdateById($crit, $row[self::ID]);
		}

		public function getSpentTimeSumsByIssueId($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addSelectionColumn(self::SPENT_POINTS, 'points', Criteria::DB_SUM);
			$crit->addSelectionColumn(self::SPENT_HOURS, 'hours', Criteria::DB_SUM);
			$crit->addSelectionColumn(self::SPENT_DAYS, 'days', Criteria::DB_SUM);
			$crit->addSelectionColumn(self::SPENT_MONTHS, 'months', Criteria::DB_SUM);
			$crit->addSelectionColumn(self::SPENT_WEEKS, 'weeks', Criteria::DB_SUM);

			return $this->doSelectOne($crit);
		}

	}
