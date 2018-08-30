<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria;
    use b2db\Row;

    /**
     * Issue spent times table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
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
     * @Entity(class="\thebuggenie\core\entities\IssueSpentTime")
     */
    class IssueSpentTimes extends ScopedTable
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
        const SPENT_MINUTES = 'issue_spenttimes.spent_minutes';
        const SPENT_POINTS = 'issue_spenttimes.spent_points';
        const ACTIVITY_TYPE = 'issue_spenttimes.activity_type';

        public function getSpentTimesByDateAndIssueIDs($startdate, $enddate, $issue_ids)
        {
            $points_retarr = array();
            $hours_retarr = array();
            $minutes_retarr = array();
            if ($startdate && $enddate)
            {
                $sd = $startdate;
                while ($sd <= $enddate)
                {
                    $points_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = array();
                    $hours_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = array();
                    $minutes_retarr[mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd))] = array();
                    $sd += 86400;
                }
            }

            if (count($issue_ids))
            {
                $crit = $this->getCriteria();
                $points_retarr_keys = array_keys($points_retarr);
                $hours_retarr_keys = array_keys($hours_retarr);
                $minutes_retarr_keys = array_keys($minutes_retarr);

                if ($startdate && $enddate)
                {
                    $crit->addWhere(self::EDITED_AT, $startdate, Criteria::DB_GREATER_THAN_EQUAL);
                    $crit->addWhere(self::EDITED_AT, $enddate, Criteria::DB_LESS_THAN_EQUAL);
                }

                $crit->addWhere(self::ISSUE_ID, $issue_ids, Criteria::DB_IN);
                $crit->addOrderBy(self::EDITED_AT, Criteria::SORT_ASC);

                if ($res = $this->doSelect($crit))
                {
                    while ($row = $res->getNextRow())
                    {
                        if ($startdate && $enddate)
                        {
                            $sd = $row->get(self::EDITED_AT);
                            $date = mktime(0, 0, 1, date('m', $sd), date('d', $sd), date('Y', $sd));
                            foreach ($points_retarr_keys as $k => $key)
                            {
                                if ($key < $date) continue;
                                if (array_key_exists($k + 1, $points_retarr_keys))
                                {
                                    if ($sd >= $key && $sd < $points_retarr_keys[$k + 1])
                                        $points_retarr[$key][] = $row->get(self::SPENT_POINTS);
                                }
                                else
                                {
                                    if ($sd >= $key)
                                        $points_retarr[$key][] = $row->get(self::SPENT_POINTS);
                                }
                            }
                            foreach ($hours_retarr_keys as $k => $key)
                            {
                                if ($key < $date) continue;
                                if (array_key_exists($k + 1, $hours_retarr_keys))
                                {
                                    if ($sd >= $key && $sd < $hours_retarr_keys[$k + 1])
                                        $hours_retarr[$key][] = $row->get(self::SPENT_HOURS);
                                }
                                else
                                {
                                    if ($sd >= $key)
                                        $hours_retarr[$key][] = $row->get(self::SPENT_HOURS);
                                }
                            }
                            foreach ($minutes_retarr_keys as $k => $key)
                            {
                                if ($key < $date) continue;
                                if (array_key_exists($k + 1, $minutes_retarr_keys))
                                {
                                    if ($sd >= $key && $sd < $minutes_retarr_keys[$k + 1])
                                        $minutes_retarr[$key][] = $row->get(self::SPENT_MINUTES);
                                }
                                else
                                {
                                    if ($sd >= $key)
                                        $minutes_retarr[$key][] = $row->get(self::SPENT_MINUTES);
                                }
                            }
                        }
                        else
                        {
                            if (!isset($hours_retarr[$row->get(self::ISSUE_ID)])) $hours_retarr[$row->get(self::ISSUE_ID)] = array();
                            if (!isset($points_retarr[$row->get(self::ISSUE_ID)])) $hours_retarr[$row->get(self::ISSUE_ID)] = array();
                            if (!isset($minutes_retarr[$row->get(self::ISSUE_ID)])) $minutes_retarr[$row->get(self::ISSUE_ID)] = array();
                            if (!isset($points_retarr[$row->get(self::ISSUE_ID)])) $minutes_retarr[$row->get(self::ISSUE_ID)] = array();
                            $hours_retarr[$row->get(self::ISSUE_ID)][] = $row->get(self::SPENT_HOURS);
                            $minutes_retarr[$row->get(self::ISSUE_ID)][] = $row->get(self::SPENT_MINUTES);
                            $points_retarr[$row->get(self::ISSUE_ID)][] = $row->get(self::SPENT_POINTS);
                        }
                    }
                }
            }

            foreach ($points_retarr as $key => $vals)
                $points_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

            foreach ($hours_retarr as $key => $vals)
                $hours_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

            foreach ($minutes_retarr as $key => $vals)
                $minutes_retarr[$key] = (count($vals)) ? array_sum($vals) : 0;

            $returnarr = array('points' => $points_retarr, 'hours' => $hours_retarr, 'minutes' => $minutes_retarr);

            if ($startdate && $enddate)
            {
                $crit2 = $this->getCriteria();
                $crit2->addSelectionColumn(self::SPENT_POINTS, 'spent_points', Criteria::DB_SUM);
                $crit2->addSelectionColumn(self::SPENT_HOURS, 'spent_hours', Criteria::DB_SUM);
                $crit2->addSelectionColumn(self::SPENT_MINUTES, 'spent_minutes', Criteria::DB_SUM);
                $crit2->addWhere(self::EDITED_AT, $startdate, Criteria::DB_LESS_THAN);

                if (count($issue_ids)) $crit2->addWhere(self::ISSUE_ID, $issue_ids, Criteria::DB_IN);

                if ($res2 = $this->doSelectOne($crit2))
                {
                    $returnarr['points_spent_before'] = $res2->get('spent_points');
                    $returnarr['hours_spent_before'] = $res2->get('spent_hours');
                    $returnarr['minutes_spent_before'] = $res2->get('spent_minutes');
                }
            }

            return $returnarr;
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
            $crit->addUpdate(self::SPENT_MINUTES, $row[self::SPENT_MINUTES] - $prev_times['minutes']);
            $crit->addUpdate(self::SPENT_HOURS, $row[self::SPENT_HOURS] - $prev_times['hours']);
            $crit->addUpdate(self::SPENT_DAYS, $row[self::SPENT_DAYS] - $prev_times['days']);
            $crit->addUpdate(self::SPENT_WEEKS, $row[self::SPENT_WEEKS] - $prev_times['weeks']);
            $crit->addUpdate(self::SPENT_MONTHS, $row[self::SPENT_MONTHS] - $prev_times['months']);

            $this->doUpdateById($crit, $row[self::ID]);
        }

        public function fixHours($row)
        {
            if ($row[self::SPENT_HOURS] == 0) return;

            $crit = $this->getCriteria();
            $crit->addUpdate(self::SPENT_HOURS, $row[self::SPENT_HOURS] * 100);
            $this->doUpdateById($crit, $row[self::ID]);
        }

        public function fixScopes()
        {
            $issue_scopes = [];
            $issue_crit = Issues::getTable()->getCriteria();
            $issue_crit->addSelectionColumn(Issues::SCOPE);
            $issue_crit->addSelectionColumn(Issues::ID);

            $issues_res = Issues::getTable()->doSelect($issue_crit);

            if (!$issues_res) {
                return;
            }

            while ($row = $issues_res->getNextRow()) {
                $issue_scopes[$row->getID()] = $row->get(Issues::SCOPE);
            }

            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID);
            $crit->addSelectionColumn(self::ISSUE_ID);
            $crit->addWhere(self::SCOPE, 0);
            $res = $this->doSelect($crit);

            $fixRow = function (Row $row) use ($issue_scopes) {
                $issue_id = $row->get(self::ISSUE_ID);
                if (!isset($issue_scopes[$issue_id])) {
                    return;
                }

                $crit = $this->getCriteria();
                $crit->addUpdate(self::SCOPE, $issue_scopes[$issue_id]);
                $this->doUpdateById($crit, $row->getID());
            };

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $fixRow($row);
                }
            }
        }

        public function getSpentTimeSumsByIssueId($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            $crit->addSelectionColumn(self::SPENT_POINTS, 'points', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_MINUTES, 'minutes', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_HOURS, 'hours', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_DAYS, 'days', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_MONTHS, 'months', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_WEEKS, 'weeks', Criteria::DB_SUM);

            return $this->doSelectOne($crit);
        }

    }
