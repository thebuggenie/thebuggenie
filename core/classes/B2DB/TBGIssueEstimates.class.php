<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue estimates table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue estimates table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name='issue_estimates')
	 */
	class TBGIssueEstimates extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issue_estimates';
		const ID = 'issue_estimates.id';
		const SCOPE = 'issue_estimates.scope';
		const ISSUE_ID = 'issue_estimates.issue_id';
		const EDITED_BY = 'issue_estimates.edited_by';
		const EDITED_AT = 'issue_estimates.edited_at';
		const ESTIMATED_MONTHS = 'issue_estimates.estimated_months';
		const ESTIMATED_WEEKS = 'issue_estimates.estimated_weeks';
		const ESTIMATED_DAYS = 'issue_estimates.estimated_days';
		const ESTIMATED_HOURS = 'issue_estimates.estimated_hours';
		const ESTIMATED_POINTS = 'issue_estimates.estimated_points';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::EDITED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addInteger(self::EDITED_AT, 10);
			parent::_addInteger(self::ESTIMATED_MONTHS, 10);
			parent::_addInteger(self::ESTIMATED_WEEKS, 10);
			parent::_addInteger(self::ESTIMATED_DAYS, 10);
			parent::_addInteger(self::ESTIMATED_HOURS, 10);
			parent::_addFloat(self::ESTIMATED_POINTS);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function saveEstimate($issue_id, $months, $weeks, $days, $hours, $points)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ESTIMATED_MONTHS, $months);
			$crit->addInsert(self::ESTIMATED_WEEKS, $weeks);
			$crit->addInsert(self::ESTIMATED_DAYS, $days);
			$crit->addInsert(self::ESTIMATED_HOURS, $hours);
			$crit->addInsert(self::ESTIMATED_POINTS, $points);
			$crit->addInsert(self::ISSUE_ID, $issue_id);
			$crit->addInsert(self::EDITED_AT, time());
			$crit->addInsert(self::EDITED_BY, TBGContext::getUser()->getID());
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$this->doInsert($crit);
		}

		public function getEstimatesByDateAndIssueIDs($startdate, $enddate, $issue_ids)
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
								$details[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_POINTS);
							}
							foreach ($hours_retarr as $key => &$details)
							{
								if ($key < $date) continue;
								$details[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_HOURS);
							}
						}
						else
						{
							$hours_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_HOURS);
							$points_retarr[$row->get(self::ISSUE_ID)] = $row->get(self::ESTIMATED_POINTS);
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
		
	}
