<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Issues table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issues table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Issues getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Issue selectById(integer $id, Criteria $crit = null, $join = 'all') Retrieves an issue
     *
     * @Entity(class="\thebuggenie\core\entities\Issue")
     * @Table(name='issues')
     */
    class Issues extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 3;
        const B2DBNAME = 'issues';
        const ID = 'issues.id';
        const SCOPE = 'issues.scope';
        const ISSUE_NO = 'issues.issue_no';
        const TITLE = 'issues.name';
        const POSTED = 'issues.posted';
        const LAST_UPDATED = 'issues.last_updated';
        const PROJECT_ID = 'issues.project_id';
        const DESCRIPTION = 'issues.description';
        const REPRODUCTION_STEPS = 'issues.reproduction_steps';
        const ISSUE_TYPE = 'issues.issuetype';
        const RESOLUTION = 'issues.resolution';
        const STATE = 'issues.state';
        const POSTED_BY = 'issues.posted_by';
        const OWNER_USER = 'issues.owner_user';
        const OWNER_TEAM = 'issues.owner_team';
        const STATUS = 'issues.status';
        const PRIORITY = 'issues.priority';
        const SEVERITY = 'issues.severity';
        const CATEGORY = 'issues.category';
        const REPRODUCABILITY = 'issues.reproducability';
        const SCRUMCOLOR = 'issues.scrumcolor';
        const ESTIMATED_MONTHS = 'issues.estimated_months';
        const ESTIMATED_WEEKS = 'issues.estimated_weeks';
        const ESTIMATED_DAYS = 'issues.estimated_days';
        const ESTIMATED_HOURS = 'issues.estimated_hours';
        const ESTIMATED_POINTS = 'issues.estimated_points';
        const SPENT_MONTHS = 'issues.spent_months';
        const SPENT_WEEKS = 'issues.spent_weeks';
        const SPENT_DAYS = 'issues.spent_days';
        const SPENT_HOURS = 'issues.spent_hours';
        const SPENT_POINTS = 'issues.spent_points';
        const PERCENT_COMPLETE = 'issues.percent_complete';
        const ASSIGNEE_USER = 'issues.assignee_user';
        const ASSIGNEE_TEAM = 'issues.assignee_team';
        const BEING_WORKED_ON_BY_USER = 'issues.being_worked_on_by_user';
        const BEING_WORKED_ON_BY_USER_SINCE = 'issues.being_worked_on_by_user_since';
        const USER_PAIN = 'issues.user_pain';
        const PAIN_BUG_TYPE = 'issues.pain_bug_type';
        const PAIN_EFFECT = 'issues.pain_effect';
        const PAIN_LIKELIHOOD = 'issues.pain_likelihood';
        const DUPLICATE_OF = 'issues.duplicate_of';
        const DELETED = 'issues.deleted';
        const BLOCKING = 'issues.blocking';
        const LOCKED = 'issues.locked';
        const WORKFLOW_STEP_ID = 'issues.workflow_step_id';
        const MILESTONE = 'issues.milestone';
        const VOTES_TOTAL = 'issues.votes_total';
        const MILESTONE_ORDER = 'issues.milestone_order';

        protected function _setupIndexes()
        {
            $this->_addIndex('project', self::PROJECT_ID);
            $this->_addIndex('project', self::PROJECT_ID);
            $this->_addIndex('last_updated', self::LAST_UPDATED);
            $this->_addIndex('deleted', self::DELETED);
            $this->_addIndex('deleted_project', array(self::DELETED, self::PROJECT_ID));
            $this->_addIndex('deleted_state_project', array(self::DELETED, self::STATE, self::PROJECT_ID));
            $this->_addIndex('deleted_project_issueno', array(self::DELETED, self::ISSUE_NO, self::PROJECT_ID));
        }

        public function getCountsByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $crit2 = clone $crit;

            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_CLOSED);
            $crit2->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);
            return array($this->doCount($crit), $this->doCount($crit2));
        }

        public function getCountsByProjectIDandIssuetype($project_id, $issuetype_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ISSUE_TYPE, $issuetype_id);

            $crit2 = clone $crit;

            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_CLOSED);
            $crit2->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);
            return array($this->doCount($crit), $this->doCount($crit2));
        }

        protected function _getCountByProjectIDAndColumn($project_id, $column)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addSelectionColumn(self::ID, 'column_count', Criteria::DB_COUNT);
            $crit->addSelectionColumn($column);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addGroupBy($column);

            $crit2 = clone $crit;

            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_CLOSED);
            $crit2->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $res1 = $this->doSelect($crit, 'none');
            $res2 = $this->doSelect($crit2, 'none');
            $retarr = array();

            if ($res1)
            {
                while ($row = $res1->getNextRow())
                {
                    $col = (int) $row->get($column);
                    if (!array_key_exists($col, $retarr)) $retarr[$col] = array('open' => 0, 'closed' => 0);
                    $retarr[$col]['closed'] = $row->get('column_count');
                }
            }
            if ($res2)
            {
                while ($row = $res2->getNextRow())
                {
                    $col = (int) $row->get($column);
                    if (!array_key_exists($col, $retarr)) $retarr[$col] = array('open' => 0, 'closed' => 0);
                    $retarr[$col]['open'] = $row->get('column_count');
                }
            }

            foreach ($retarr as $column_id => $details)
            {
                if (array_sum($details) > 0)
                {
                    $multiplier = 100 / array_sum($details);
                    $retarr[$column_id]['percentage'] = $details['open'] * $multiplier;
                }
                else
                {
                    $retarr[$column_id]['percentage'] = 0;
                }
            }

            return $retarr;
        }

        public function getMilestoneDistributionDetails($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::MILESTONE, $milestone_id);
            $total = $this->doCount($crit);

            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::STATUS);
            $crit->addSelectionColumn(self::ID, 'counts', Criteria::DB_COUNT);
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::MILESTONE, $milestone_id);
            $crit->addGroupBy(self::STATUS);

            $res = $this->doSelect($crit);
            $statuses = array('total' => $total, 'details' => array());

            if ($res)
            {
                $multiplier = 100 / $total;
                $total_percent = 0;
                while ($row = $res->getNextRow())
                {
                    $counts = $row['counts'];
                    $pct = round($counts * $multiplier, 2);
                    $total_percent += $pct;
                    if ($total_percent > 100) $pct -= $total_percent - 100;
                    $status = $row[self::STATUS];
                    $statuses['details'][$status] = array('id' => $status, 'count' => $counts, 'percent' => $pct);
                }
            }

            return $statuses;
        }

        public function getCountsByProjectIDandMilestone($project_id, $milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if (!$milestone_id)
            {
                $ctn = $crit->returnCriterion(self::MILESTONE, null);
                $ctn->addOr(self::MILESTONE, 0);
                $crit->addWhere($ctn);
            }
            else
            {
                $crit->addWhere(self::MILESTONE, $milestone_id);
            }

            $crit2 = clone $crit;
            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_CLOSED);
            $crit2->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);
            return array($this->doCount($crit), $this->doCount($crit2));
        }

        public function getPriorityCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::PRIORITY);
        }

        public function getSeverityCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::SEVERITY);
        }

        public function getStatusCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::STATUS);
        }

        public function getCategoryCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::CATEGORY);
        }

        public function getWorkflowStepCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::WORKFLOW_STEP_ID);
        }

        public function getResolutionCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::RESOLUTION);
        }

        public function getReproducabilityCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::REPRODUCABILITY);
        }

        public function getStateCountByProjectID($project_id)
        {
            return $this->_getCountByProjectIDAndColumn($project_id, self::STATE);
        }

        public function getOpenIssuesByProjectIDAndIssuetypeID($project_id, $issuetype_id)
        {
            $crit = new Criteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::ISSUE_TYPE, $issuetype_id);
            $issues = $this->select($crit);

            foreach ($issues as $key => $issue)
            {
                if (!$issue->hasAccess()) unset($issues[$key]);
            }

            return $issues;
        }

        public function getByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->doSelectById($id, $crit);
        }

        public function getIssueByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->selectById($id, $crit);
        }

        public function getCountByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $res = $this->doCount($crit);
            return $res;
        }

        public function getNextIssueNumberForProductID($p_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $p_id);
            $crit->addSelectionColumn(self::ISSUE_NO, 'issueno', Criteria::DB_MAX, '', '+1');
            $row = $this->doSelectOne($crit, 'none');
            $issue_no = $row->get('issueno');
            return ($issue_no < 1) ? 1 : $issue_no;
        }

        public function getByPrefixAndIssueNo($prefix, $issue_no)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addJoin(Projects::getTable(), Projects::ID, self::PROJECT_ID);
            $crit->addWhere(Projects::PREFIX, mb_strtolower($prefix), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
            $crit->addWhere(Projects::DELETED, false);
            $crit->addWhere(self::ISSUE_NO, $issue_no);
            return $this->selectOne($crit, false);
        }

        public function getByProjectIDAndIssueNo($project_id, $issue_no)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::ISSUE_NO, $issue_no);
            return $this->selectOne($crit, false);
        }

        public function setDuplicate($issue_id, $duplicate_of)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::DUPLICATE_OF, $duplicate_of);
            $this->doUpdateById($crit, $issue_id);
        }

        public function getByMilestone($milestone_id, $project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            if (!$milestone_id)
            {
                $ctn = $crit->returnCriterion(self::MILESTONE, null);
                $ctn->addOr(self::MILESTONE, 0);
                $crit->addWhere($ctn);
                $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);
                $crit->addWhere(self::PROJECT_ID, $project_id);
            }
            else
            {
                $crit->addWhere(self::MILESTONE, $milestone_id);
            }
            $crit->addOrderBy(self::MILESTONE_ORDER, Criteria::SORT_DESC);

            return $this->select($crit);
        }

        public function setOrderByIssueId($order, $issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addUpdate(self::MILESTONE_ORDER, $order);
            $this->doUpdateById($crit, $issue_id);
        }

        public function getPointsAndTimeByMilestone($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            if (!$milestone_id)
            {
                $crit->addWhere(self::MILESTONE, null);
            }
            else
            {
                $crit->addWhere(self::MILESTONE, $milestone_id);
            }
            $crit->addSelectionColumn(self::STATE, 'state');
            $crit->addSelectionColumn(self::ESTIMATED_POINTS, 'estimated_points');
            $crit->addSelectionColumn(self::ESTIMATED_HOURS, 'estimated_hours');
            $crit->addSelectionColumn(self::ESTIMATED_DAYS, 'estimated_days');
            $crit->addSelectionColumn(self::ESTIMATED_WEEKS, 'estimated_weeks');
            $crit->addSelectionColumn(self::ESTIMATED_MONTHS, 'estimated_months');
            $crit->addSelectionColumn(self::SPENT_POINTS, 'spent_points');
            $crit->addSelectionColumn(self::SPENT_HOURS, 'spent_hours');
            $crit->addSelectionColumn(self::SPENT_DAYS, 'spent_days');
            $crit->addSelectionColumn(self::SPENT_WEEKS, 'spent_weeks');
            $crit->addSelectionColumn(self::SPENT_MONTHS, 'spent_months');
            $res = $this->doSelect($crit);
            return $res;
        }

        public function getByProjectIDandNoMilestone($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::MILESTONE, null);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $res = $this->doSelect($crit);
            return $res;
        }

        public function getByProjectIDandNoMilestoneandTypes($project_id, $issuetypes)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::MILESTONE, null);
            $crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $res = $this->doSelect($crit);
            return $res;
        }

        public function getByProjectIDandNoMilestoneandTypesandState($project_id, $issuetypes, $state)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::MILESTONE, null);
            $crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::STATE, $state);
            $res = $this->doSelect($crit);
            return $res;
        }

        public function clearMilestone($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::MILESTONE, 0);
            $crit->addUpdate(self::LAST_UPDATED, time());
            $crit->addWhere(self::MILESTONE, $milestone_id);
            $this->doUpdate($crit);
        }

        public function getOpenIssuesByTeamAssigned($team_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ASSIGNED_TEAM, $team_id);
            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $res = $this->select($crit);

            return $res;
        }

        public function getOpenIssuesByUserAssigned($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ASSIGNEE_USER, $user_id);
            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $res = $this->select($crit);

            return $res;
        }

        public function getOpenIssuesByProjectIDAndIssueTypes($project_id, $issuetypes, $order_by=null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            if ($order_by != null)
            {
                $crit->addOrderBy($order_by);
            }

            $res = $this->doSelect($crit);

            return $res;
        }

        public function getRecentByProjectIDandIssueType($project_id, $issuetype, $limit = 10)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::ISSUE_TYPE, $issuetype);
            $crit->addOrderBy(self::POSTED, Criteria::SORT_DESC);
            if ($limit !== null)
                $crit->setLimit($limit);

            $res = $this->doSelect($crit);

            return $res;
        }

        public function getTotalPointsByMilestoneID($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addSelectionColumn(self::ESTIMATED_POINTS, 'estimated_points', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_POINTS, 'spent_points', Criteria::DB_SUM);
            if (!$milestone_id)
            {
                $crit->addWhere(self::MILESTONE, null);
            }
            else
            {
                $crit->addWhere(self::MILESTONE, $milestone_id);
            }
            if ($res = $this->doSelectOne($crit))
            {
                return array($res->get('estimated_points'), $res->get('spent_points'));
            }
            else
            {
                return array(0, 0);
            }
        }

        public function getTotalHoursByMilestoneID($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addSelectionColumn(self::ESTIMATED_HOURS, 'estimated_hours', Criteria::DB_SUM);
            $crit->addSelectionColumn(self::SPENT_HOURS, 'spent_hours', Criteria::DB_SUM);
            if (!$milestone_id)
            {
                $crit->addWhere(self::MILESTONE, null);
            }
            else
            {
                $crit->addWhere(self::MILESTONE, $milestone_id);
            }
            if ($res = $this->doSelectOne($crit))
            {
                return array($res->get('estimated_hours'), $res->get('spent_hours'));
            }
            else
            {
                return array(0, 0);
            }
        }

        protected function _getLastUpdatedArrayFromResultset(\b2db\Resultset $res)
        {
            $ids = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $ids[] = array('issue_id' => $row->get('id'), 'last_updated' => $row->get('last_updated'));
                }
            }

            return $ids;
        }

        public function getUpdatedIssueIDsByTimestampAndProjectIDAndMilestoneID($last_updated, $project_id, $milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID, 'id');
            $crit->addSelectionColumn(self::LAST_UPDATED, 'last_updated');
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::MILESTONE, $milestone_id, Criteria::DB_EQUALS);
            $crit->addWhere(self::LAST_UPDATED, $last_updated, Criteria::DB_GREATER_THAN_EQUAL);

            $res = $this->doSelect($crit);
            return ($res) ? $this->_getLastUpdatedArrayFromResultset($res) : array();
        }

        public function getUpdatedIssueIDsByTimestampAndProjectIDAndIssuetypeID($last_updated, $project_id, $issuetype_id = null)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID, 'id');
            $crit->addSelectionColumn(self::LAST_UPDATED, 'last_updated');
            $crit->addWhere(self::PROJECT_ID, $project_id);
            if ($issuetype_id === null)
            {
                $crit->addWhere(self::MILESTONE, 0, Criteria::DB_NOT_EQUALS);
            }
            else
            {
                $crit->addWhere(self::ISSUE_TYPE, $issuetype_id);
            }
            $crit->addWhere(self::LAST_UPDATED, $last_updated, Criteria::DB_GREATER_THAN_EQUAL);

            $res = $this->doSelect($crit);
            return ($res) ? $this->_getLastUpdatedArrayFromResultset($res) : array();
        }

        public function markIssuesDeletedByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addUpdate(self::DELETED, true);

            $this->doUpdate($crit);
        }

        public static function parseFilter($crit, $filter, $filters, $ctn = null)
        {
            if (is_array($filter))
            {
                foreach ($filter as $single_filter)
                {
                    $ctn = self::parseFilter($crit, $single_filter, $filters, $ctn);
                }
            }
            elseif ($filter instanceof \thebuggenie\core\entities\SearchFilter)
            {
                if ($filter->hasValue())
                {
                    $ctn = $filter->addToCriteria($crit, $filters, $ctn);
                    if ($ctn !== null) $crit->addWhere($ctn);
                }
            }
        }

        public function findIssues($filters = array(), $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null, $sortfields = array(self::LAST_UPDATED => 'asc'))
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if (count($filters) > 0)
            {
                $crit->addJoin(IssueCustomFields::getTable(), IssueCustomFields::ISSUE_ID, Issues::ID);
                $crit->addJoin(IssueAffectsComponent::getTable(), IssueAffectsComponent::ISSUE, self::ID);
                $crit->addJoin(IssueAffectsEdition::getTable(), IssueAffectsEdition::ISSUE, self::ID);
                $crit->addJoin(IssueAffectsBuild::getTable(), IssueAffectsBuild::ISSUE, self::ID);

                $filter_keys = array_keys($filters);
                foreach ($filters as $filter)
                {
                    self::parseFilter($crit, $filter, $filter_keys);
                }
            }

            $crit->addSelectionColumn(self::ID);
            $crit->setDistinct();

            if ($offset != 0)
                $crit->setOffset($offset);

            $crit2 = clone $crit;
            $count = $this->doCount($crit2);

            if ($count > 0)
            {
                $crit3 = $this->getCriteria();

                if ($results_per_page != 0)
                    $crit->setLimit($results_per_page);

                if ($offset != 0)
                    $crit->setOffset($offset);

                if ($groupby !== null)
                {
                    $grouporder = ($grouporder !== null) ? (($grouporder == 'asc') ? Criteria::SORT_ASC : Criteria::SORT_DESC) : Criteria::SORT_ASC;
                    switch ($groupby)
                    {
                        case 'category':
                            $crit->addJoin(ListTypes::getTable(), ListTypes::ID, self::CATEGORY);
                            $crit->addSelectionColumn(ListTypes::NAME);
                            $crit->addOrderBy(ListTypes::NAME, $grouporder);
                            $crit3->addJoin(ListTypes::getTable(), ListTypes::ID, self::CATEGORY);
                            $crit3->addOrderBy(ListTypes::NAME, $grouporder);
                            break;
                        case 'status':
                            $crit->addJoin(ListTypes::getTable(), ListTypes::ID, self::STATUS);
                            $crit->addSelectionColumn(self::STATUS);
                            $crit->addOrderBy(ListTypes::ORDER, Criteria::SORT_DESC);
                            $crit3->addJoin(ListTypes::getTable(), ListTypes::ID, self::STATUS);
                            $crit3->addOrderBy(ListTypes::ORDER, Criteria::SORT_DESC);
                            break;
                        case 'milestone':
                            $crit->addSelectionColumn(self::MILESTONE);
                            $crit->addSelectionColumn(self::PERCENT_COMPLETE);
                            $crit->addOrderBy(self::MILESTONE, $grouporder);
                            $crit->addOrderBy(self::PERCENT_COMPLETE, 'desc');
                            $crit3->addOrderBy(self::MILESTONE, $grouporder);
                            $crit3->addOrderBy(self::PERCENT_COMPLETE, 'desc');
                            break;
                        case 'assignee':
                            $crit->addSelectionColumn(self::ASSIGNEE_TEAM);
                            $crit->addSelectionColumn(self::ASSIGNEE_USER);
                            $crit->addOrderBy(self::ASSIGNEE_TEAM);
                            $crit->addOrderBy(self::ASSIGNEE_USER, $grouporder);
                            $crit3->addOrderBy(self::ASSIGNEE_TEAM);
                            $crit3->addOrderBy(self::ASSIGNEE_USER, $grouporder);
                            break;
                        case 'posted_by':
                            $crit->addJoin(Users::getTable(), Users::ID, self::POSTED_BY);
                            $crit3->addJoin(Users::getTable(), Users::ID, self::POSTED_BY);
                            $crit->addSelectionColumn(self::POSTED_BY);
                            $crit->addSelectionColumn(Users::UNAME);
                            $crit->addOrderBy(Users::UNAME, $grouporder);
                            $crit3->addOrderBy(Users::UNAME, $grouporder);
                            break;
                        case 'state':
                            $crit->addSelectionColumn(self::STATE);
                            $crit->addOrderBy(self::STATE, $grouporder);
                            $crit3->addOrderBy(self::STATE, $grouporder);
                            break;
                        case 'posted':
                            $crit->addSelectionColumn(self::POSTED);
                            $crit->addOrderBy(self::POSTED, $grouporder);
                            $crit3->addOrderBy(self::POSTED, $grouporder);
                            break;
                        case 'severity':
                            $crit->addJoin(ListTypes::getTable(), ListTypes::ID, self::SEVERITY);
                            $crit->addSelectionColumn(self::SEVERITY);
                            $crit->addOrderBy(ListTypes::ORDER, $grouporder);
                            $crit3->addJoin(ListTypes::getTable(), ListTypes::ID, self::SEVERITY);
                            $crit3->addOrderBy(ListTypes::ORDER, $grouporder);
                            break;
                        case 'user_pain':
                            $crit->addSelectionColumn(self::USER_PAIN);
                            $crit->addOrderBy(self::USER_PAIN, $grouporder);
                            $crit3->addOrderBy(self::USER_PAIN, $grouporder);
                            break;
                        case 'votes':
                            $crit->addSelectionColumn(self::VOTES_TOTAL);
                            $crit->addOrderBy(self::VOTES_TOTAL, $grouporder);
                            $crit3->addOrderBy(self::VOTES_TOTAL, $grouporder);
                            break;
                        case 'resolution':
                            $crit->addJoin(ListTypes::getTable(), ListTypes::ID, self::RESOLUTION);
                            $crit->addSelectionColumn(self::RESOLUTION);
                            $crit->addOrderBy(ListTypes::ORDER, $grouporder);
                            $crit3->addJoin(ListTypes::getTable(), ListTypes::ID, self::RESOLUTION);
                            $crit3->addOrderBy(ListTypes::ORDER, $grouporder);
                            break;
                        case 'priority':
                            $crit->addJoin(ListTypes::getTable(), ListTypes::ID, self::PRIORITY);
                            $crit->addSelectionColumn(self::PRIORITY);
                            $crit->addOrderBy(ListTypes::ORDER, $grouporder);
                            $crit3->addJoin(ListTypes::getTable(), ListTypes::ID, self::PRIORITY);
                            $crit3->addOrderBy(ListTypes::ORDER, $grouporder);
                            break;
                        case 'issuetype':
                            $crit->addJoin(IssueTypes::getTable(), IssueTypes::ID, self::ISSUE_TYPE);
                            $crit->addSelectionColumn(IssueTypes::NAME);
                            $crit->addOrderBy(IssueTypes::NAME, $grouporder);
                            $crit3->addJoin(IssueTypes::getTable(), IssueTypes::ID, self::ISSUE_TYPE);
                            $crit3->addOrderBy(IssueTypes::NAME, $grouporder);
                            break;
                        case 'edition':
                            $crit->addJoin(IssueAffectsEdition::getTable(), IssueAffectsEdition::ISSUE, self::ID);
                            $crit->addJoin(Editions::getTable(), Editions::ID, IssueAffectsEdition::EDITION, array(), Criteria::DB_LEFT_JOIN, IssueAffectsEdition::getTable());
                            $crit->addSelectionColumn(Editions::NAME);
                            $crit->addOrderBy(Editions::NAME, $grouporder);
                            $crit3->addJoin(IssueAffectsEdition::getTable(), IssueAffectsEdition::ISSUE, self::ID);
                            $crit3->addJoin(Editions::getTable(), Editions::ID, IssueAffectsEdition::EDITION, array(), Criteria::DB_LEFT_JOIN, IssueAffectsEdition::getTable());
                            $crit3->addOrderBy(Editions::NAME, $grouporder);
                            break;
                        case 'build':
                            $crit->addJoin(IssueAffectsBuild::getTable(), IssueAffectsBuild::ISSUE, self::ID);
                            $crit->addJoin(Builds::getTable(), Builds::ID, IssueAffectsBuild::BUILD, array(), Criteria::DB_LEFT_JOIN, IssueAffectsBuild::getTable());
                            $crit->addSelectionColumn(Builds::NAME);
                            $crit->addOrderBy(Builds::NAME, $grouporder);
                            $crit3->addJoin(IssueAffectsBuild::getTable(), IssueAffectsBuild::ISSUE, self::ID);
                            $crit3->addJoin(Builds::getTable(), Builds::ID, IssueAffectsBuild::BUILD, array(), Criteria::DB_LEFT_JOIN, IssueAffectsBuild::getTable());
                            $crit3->addOrderBy(Builds::NAME, $grouporder);
                            break;
                        case 'component':
                            $crit->addJoin(IssueAffectsComponent::getTable(), IssueAffectsComponent::ISSUE, self::ID);
                            $crit->addJoin(Components::getTable(), Components::ID, IssueAffectsComponent::COMPONENT, array(), Criteria::DB_LEFT_JOIN, IssueAffectsComponent::getTable());
                            $crit->addSelectionColumn(Components::NAME);
                            $crit->addOrderBy(Components::NAME, $grouporder);
                            $crit3->addJoin(IssueAffectsComponent::getTable(), IssueAffectsComponent::ISSUE, self::ID);
                            $crit3->addJoin(Components::getTable(), Components::ID, IssueAffectsComponent::COMPONENT, array(), Criteria::DB_LEFT_JOIN, IssueAffectsComponent::getTable());
                            $crit3->addOrderBy(Components::NAME, $grouporder);
                            break;
                    }
                }

                foreach ($sortfields as $field => $sortorder)
                {
                    $crit->addSelectionColumn($field);
                    $crit->addOrderBy($field, $sortorder);
                }

                $res = $this->doSelect($crit, 'none');
                $ids = array();

                if ($res)
                {
                    while ($row = $res->getNextRow())
                    {
                        $ids[] = $row->get(self::ID);
                    }
                    $ids = array_reverse($ids);

                    $crit3->addWhere(self::ID, $ids, Criteria::DB_IN);
                    foreach ($sortfields as $field => $sortorder)
                    {
                        $crit3->addOrderBy($field, $sortorder);
                    }

                    $res = $this->doSelect($crit3);
                    $rows = $res->getAllRows();
                }
                else
                {
                    $rows = array();
                }

                unset($res);

                return array($rows, $count, $ids);
            }
            else
            {
                return array(null, 0, array());
            }

        }

        public function getDuplicateIssuesByIssueNo($issue_no)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addSelectionColumn(self::ID);
            $crit->addWhere(self::DUPLICATE_OF, $issue_no);

            return $this->select($crit);
        }

        public function saveVotesTotalForIssueID($votes_total, $issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::VOTES_TOTAL, $votes_total);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $this->doUpdateById($crit, $issue_id);
        }

        /**
         * Return list of issue reported by an user
         *
         * @param int $user_id user ID
         * @param int $limit [optional] number of issues to retrieve
         *
         * @return array|Issue
         */
        public function getIssuesPostedByUser($user_id, $limit = 15)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::POSTED_BY, $user_id);
            $crit->addOrderBy(self::POSTED, Criteria::SORT_DESC);
            $crit->setLimit($limit);

            return $this->select($crit);
        }

        /**
         * Move issues from one step to another for a given issue type and conversions
         * @param \thebuggenie\core\entities\Project $project
         * @param \thebuggenie\core\entities\Issuetype $type
         * @param array $conversions
         *
         * $conversions should be an array containing arrays:
         * array (
         *         array(oldstep, newstep)
         *         ...
         * )
         */
        public function convertIssueStepByIssuetype(\thebuggenie\core\entities\Project $project, \thebuggenie\core\entities\Issuetype $type, array $conversions)
        {
            foreach ($conversions as $conversion)
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::PROJECT_ID, $project->getID());
                $crit->addWhere(self::ISSUE_TYPE, $type->getID());
                $crit->addWhere(self::WORKFLOW_STEP_ID, $conversion[0]);

                $crit->addUpdate(self::WORKFLOW_STEP_ID, $conversion[1]);
                $this->doUpdate($crit);
            }
        }

        public function getNextIssueFromIssueIDAndProjectID($issue_id, $project_id, $only_open = false)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ID, $issue_id, Criteria::DB_GREATER_THAN);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::DELETED, false);
            if ($only_open) $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $crit->addOrderBy(self::ISSUE_NO, Criteria::SORT_ASC);

            return $this->selectOne($crit);
        }

        public function getNextIssueFromIssueMilestoneOrderAndMilestoneID($milestone_order, $milestone_id, $only_open = false)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::MILESTONE_ORDER, $milestone_order, Criteria::DB_GREATER_THAN);
            $crit->addWhere(self::MILESTONE, $milestone_id);
            $crit->addWhere(self::DELETED, false);
            if ($only_open) $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $crit->addOrderBy(self::MILESTONE_ORDER, Criteria::SORT_ASC);
            $crit->addOrderBy(self::ID, Criteria::SORT_ASC);

            return $this->selectOne($crit);
        }

        public function getPreviousIssueFromIssueIDAndProjectID($issue_id, $project_id, $only_open = false)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ID, $issue_id, Criteria::DB_LESS_THAN);
            $crit->addWhere(self::PROJECT_ID, $project_id);
            $crit->addWhere(self::DELETED, false);
            if ($only_open) $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $crit->addOrderBy(self::ISSUE_NO, Criteria::SORT_DESC);

            return $this->selectOne($crit);
        }

        public function getPreviousIssueFromIssueMilestoneOrderAndMilestoneID($milestone_order, $milestone_id, $only_open = false)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::MILESTONE_ORDER, $milestone_order, Criteria::DB_LESS_THAN);
            $crit->addWhere(self::MILESTONE, $milestone_id);
            $crit->addWhere(self::DELETED, false);
            if ($only_open) $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);

            $crit->addOrderBy(self::MILESTONE_ORDER, Criteria::SORT_DESC);
            $crit->addOrderBy(self::ID, Criteria::SORT_DESC);

            return $this->selectOne($crit);
        }

        public function fixHours($issue_id)
        {
            $times = IssueSpentTimes::getTable()->getSpentTimeSumsByIssueId($issue_id);
            $crit = $this->getCriteria();
            $crit->addUpdate(self::SPENT_HOURS, $times['hours']);
            $this->doUpdateById($crit, $issue_id);
        }

        public function touchIssue($issue_id, $last_updated = null)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::LAST_UPDATED, isset($last_updated) ? $last_updated : time());
            $this->doUpdateById($crit, $issue_id);
        }

        public function reAssignIssuesByMilestoneIds($current_milestone_id, $new_milestone_id, $milestone_order = null)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::LAST_UPDATED, time());
            $crit->addUpdate(self::MILESTONE, $new_milestone_id);
            
            if ($milestone_order !== null) $crit->addUpdate(self::MILESTONE_ORDER, $milestone_order);

            $crit->addWhere(self::MILESTONE, $current_milestone_id);
            $crit->addWhere(self::STATE, \thebuggenie\core\entities\Issue::STATE_OPEN);
            $this->doUpdate($crit);
        }

        public function assignMilestoneIDbyIssueIDs($milestone_id, $issue_ids)
        {
            if (!empty($issue_ids))
            {
                $crit = $this->getCriteria();
                $crit->addUpdate(self::LAST_UPDATED, time());
                $crit->addUpdate(self::MILESTONE, $milestone_id);
                $crit->addWhere(self::ID, $issue_ids, Criteria::DB_IN);
                $this->doUpdate($crit);
            }
        }

    }
