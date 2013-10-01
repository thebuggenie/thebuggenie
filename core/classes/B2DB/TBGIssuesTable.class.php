<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issues table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issues table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 * 
	 * @Entity(class="TBGIssue")
	 * @Table(name='issues')
	 */
	class TBGIssuesTable extends TBGB2DBTable 
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

		public function _migrateData(\b2db\Table $old_table)
		{
			$sqls = array();
			$tn = $this->_getTableNameSQL();
			switch ($old_table->getVersion())
			{
				case 1:
					$sqls[] = "UPDATE {$tn} SET owner_team = owner WHERE owner_type = 2";
					$sqls[] = "UPDATE {$tn} SET owner_user = owner WHERE owner_type = 1";
					$sqls[] = "UPDATE {$tn} SET assignee_team = assigned_to WHERE assigned_type = 2";
					$sqls[] = "UPDATE {$tn} SET assignee_user = assigned_to WHERE assigned_type = 1";
					$sqls[] = "UPDATE {$tn} SET name = title";
					break;
			}
			foreach ($sqls as $sql)
			{
				$statement = \b2db\Statement::getPreparedStatement($sql);
				$res = $statement->performQuery('update');
			}
		}

		public function getCountsByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$crit2 = clone $crit;

			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		public function getCountsByProjectIDandIssuetype($project_id, $issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::ISSUE_TYPE, $issuetype_id);
			
			$crit2 = clone $crit;
			
			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
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

			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);

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
		
		public function getCountsByProjectIDandMilestone($project_id, $milestone_id, $exclude_tasks = false)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
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
			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		public function getOpenAffectedIssuesByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doSelect($crit);
			return $res;
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
		
		public function getIssuesByProjectID($id)
		{
			$crit = new Criteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::PROJECT_ID, $id);
			$results = $this->doSelect($crit);

			if (!is_object($results) || count($results) == 0)
			{
				return false;
			}
			
			$data = array();
			
			/* Build revision details */
			while ($results->next())
			{
				$data[] = TBGContext::factory()->TBGIssue($results->get(TBGIssuesTable::ID));
			}
			
			return $data;
		}

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			return $this->doSelectById($id, $crit);
		}
		
		public function getIssueByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
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
			$crit->addJoin(TBGProjectsTable::getTable(), TBGProjectsTable::ID, self::PROJECT_ID);
			$crit->addWhere(TBGProjectsTable::PREFIX, mb_strtolower($prefix), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
			$crit->addWhere(TBGProjectsTable::DELETED, false);
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
				$crit->addWhere(self::PROJECT_ID, $project_id);
			}
			else
			{
				$crit->addWhere(self::MILESTONE, $milestone_id);
			}
			$crit->addOrderBy(self::MILESTONE_ORDER, Criteria::SORT_DESC);
			
			return $this->select($crit);
		}
		
		public function setOrderByMilestoneIdAndIssueId($order, $milestone_id, $issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
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
			$crit->addUpdate(self::MILESTONE, null);
			$crit->addWhere(self::MILESTONE, $milestone_id);
			$this->doUpdate($crit);
		}
		
		public function getOpenIssuesByTeamAssigned($team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::ASSIGNED_TEAM, $team_id);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}
				
		public function getOpenIssuesByUserAssigned($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::ASSIGNEE_USER, $user_id);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function getOpenIssuesByProjectIDAndIssueTypes($project_id, $issuetypes, $order_by=null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			
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
			elseif ($filter instanceof TBGSearchFilter)
			{
				if ($filter->hasValue())
				{
					$ctn = $filter->addToCriteria($crit, $filters, $ctn);
					if ($ctn !== null) $crit->addWhere($ctn);
				}
			}
		}

		public function findIssues($filters = array(), $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null, $dateorder = 'asc')
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if (count($filters) > 0)
			{
				$crit->addJoin(TBGIssueCustomFieldsTable::getTable(), TBGIssueCustomFieldsTable::ISSUE_ID, TBGIssuesTable::ID);
				$crit->addJoin(TBGIssueAffectsComponentTable::getTable(), TBGIssueAffectsComponentTable::ISSUE, self::ID);
				$crit->addJoin(TBGIssueAffectsEditionTable::getTable(), TBGIssueAffectsEditionTable::ISSUE, self::ID);
				$crit->addJoin(TBGIssueAffectsBuildTable::getTable(), TBGIssueAffectsBuildTable::ISSUE, self::ID);

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
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::CATEGORY);
							$crit->addSelectionColumn(TBGListTypesTable::NAME);
							$crit->addOrderBy(TBGListTypesTable::NAME, $grouporder);
							$crit3->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::CATEGORY);
							$crit3->addOrderBy(TBGListTypesTable::NAME, $grouporder);
							break;
						case 'status':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::STATUS);
							$crit->addSelectionColumn(self::STATUS);
							$crit->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
							$crit3->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::STATUS);
							$crit3->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
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
						case 'state':
							$crit->addSelectionColumn(self::STATE);
							$crit->addOrderBy(self::STATE, $grouporder);
							$crit3->addOrderBy(self::STATE, $grouporder);
							break;
						case 'severity':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::SEVERITY);
							$crit->addSelectionColumn(self::SEVERITY);
							$crit->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
							$crit3->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::SEVERITY);
							$crit3->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
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
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::RESOLUTION);
							$crit->addSelectionColumn(self::RESOLUTION);
							$crit->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
							$crit3->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::RESOLUTION);
							$crit3->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
							break;
						case 'priority':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::PRIORITY);
							$crit->addSelectionColumn(self::PRIORITY);
							$crit->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
							$crit3->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::PRIORITY);
							$crit3->addOrderBy(TBGListTypesTable::ORDER, Criteria::SORT_DESC);
							break;
						case 'issuetype':
							$crit->addJoin(TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID, self::ISSUE_TYPE);
							$crit->addSelectionColumn(TBGIssueTypesTable::NAME);
							$crit->addOrderBy(TBGIssueTypesTable::NAME, $grouporder);
							$crit3->addJoin(TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID, self::ISSUE_TYPE);
							$crit3->addOrderBy(TBGIssueTypesTable::NAME, $grouporder);
							break;
						case 'edition':
							$crit->addJoin(TBGIssueAffectsEditionTable::getTable(), TBGIssueAffectsEditionTable::ISSUE, self::ID);
							$crit->addJoin(TBGEditionsTable::getTable(), TBGEditionsTable::ID, TBGIssueAffectsEditionTable::EDITION, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsEditionTable::getTable());
							$crit->addSelectionColumn(TBGEditionsTable::NAME);
							$crit->addOrderBy(TBGEditionsTable::NAME, $grouporder);
							$crit3->addJoin(TBGIssueAffectsEditionTable::getTable(), TBGIssueAffectsEditionTable::ISSUE, self::ID);
							$crit3->addJoin(TBGEditionsTable::getTable(), TBGEditionsTable::ID, TBGIssueAffectsEditionTable::EDITION, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsEditionTable::getTable());
							$crit3->addOrderBy(TBGEditionsTable::NAME, $grouporder);
							break;
						case 'build':
							$crit->addJoin(TBGIssueAffectsBuildTable::getTable(), TBGIssueAffectsBuildTable::ISSUE, self::ID);
							$crit->addJoin(TBGBuildsTable::getTable(), TBGBuildsTable::ID, TBGIssueAffectsBuildTable::BUILD, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsBuildTable::getTable());
							$crit->addSelectionColumn(TBGBuildsTable::NAME);
							$crit->addOrderBy(TBGBuildsTable::NAME, $grouporder);
							$crit3->addJoin(TBGIssueAffectsBuildTable::getTable(), TBGIssueAffectsBuildTable::ISSUE, self::ID);
							$crit3->addJoin(TBGBuildsTable::getTable(), TBGBuildsTable::ID, TBGIssueAffectsBuildTable::BUILD, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsBuildTable::getTable());
							$crit3->addOrderBy(TBGBuildsTable::NAME, $grouporder);
							break;
						case 'component':
							$crit->addJoin(TBGIssueAffectsComponentTable::getTable(), TBGIssueAffectsComponentTable::ISSUE, self::ID);
							$crit->addJoin(TBGComponentsTable::getTable(), TBGComponentsTable::ID, TBGIssueAffectsComponentTable::COMPONENT, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsComponentTable::getTable());
							$crit->addSelectionColumn(TBGComponentsTable::NAME);
							$crit->addOrderBy(TBGComponentsTable::NAME, $grouporder);
							$crit3->addJoin(TBGIssueAffectsComponentTable::getTable(), TBGIssueAffectsComponentTable::ISSUE, self::ID);
							$crit3->addJoin(TBGComponentsTable::getTable(), TBGComponentsTable::ID, TBGIssueAffectsComponentTable::COMPONENT, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsComponentTable::getTable());
							$crit3->addOrderBy(TBGComponentsTable::NAME, $grouporder);
							break;
					}
				}
				
				$crit->addSelectionColumn(self::LAST_UPDATED);
				$crit->addOrderBy(self::LAST_UPDATED, $dateorder);

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
					$crit3->addOrderBy(self::LAST_UPDATED, $dateorder);

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
			
			return $this->doSelect($crit);
		}

		public function saveVotesTotalForIssueID($votes_total, $issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::VOTES_TOTAL, $votes_total);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$this->doUpdateById($crit, $issue_id);
		}

		/**
		 * Return list of issue reported by an user
		 *
		 * @param int $user_id user ID
		 * @param int $limit number of issues to  retrieve [optional]
		 * @param Criteria $sort sort order [optional]
		 *
		 * @return B2DBResultset
		 */
		public function getIssuesPostedByUser($user_id, $limit=null, $sort=Criteria::SORT_DESC)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addJoin(TBGProjectsTable::getTable(), TBGProjectsTable::ID, self::PROJECT_ID);
			$crit->addWhere(self::POSTED_BY, $user_id);
			$crit->addOrderBy(self::POSTED, $sort);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			
			return $this->doSelect($crit);
		}

		/**
		 * Move issues from one step to another for a given issue type and conversions
		 * @param TBGProject $project
		 * @param TBGIssuetype $type
		 * @param array $conversions
		 * 
		 * $conversions should be an array containing arrays:
		 * array (
		 * 		array(oldstep, newstep)
		 * 		...
		 * )
		 */
		public function convertIssueStepByIssuetype(TBGProject $project, TBGIssuetype $type, array $conversions)
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
			if ($only_open) $crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);

			$crit->addOrderBy(self::ISSUE_NO, Criteria::SORT_ASC);

			return $this->selectOne($crit);
		}

		public function getPreviousIssueFromIssueIDAndProjectID($issue_id, $project_id, $only_open = false)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ID, $issue_id, Criteria::DB_LESS_THAN);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			if ($only_open) $crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);

			$crit->addOrderBy(self::ISSUE_NO, Criteria::SORT_DESC);

			return $this->selectOne($crit);
		}

		public function fixHours($issue_id)
		{
			$times = TBGIssueSpentTimesTable::getTable()->getSpentTimeSumsByIssueId($issue_id);
			$crit = $this->getCriteria();
			$crit->addUpdate(self::SPENT_HOURS, $times['hours']);
			$this->doUpdate($crit);
		}

	}
