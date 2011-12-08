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
		
		const B2DB_TABLE_VERSION = 2;
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
		const ASSIGNEE_TEAM = 'issues.assigned_team';
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

//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//			parent::_addInteger(self::ISSUE_NO, 10);
//			parent::_addVarchar(self::TITLE, 200);
//			parent::_addInteger(self::POSTED, 10);
//			parent::_addInteger(self::LAST_UPDATED, 10);
//			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
//			parent::_addText(self::DESCRIPTION, false);
//			parent::_addBoolean(self::STATE);
//			parent::_addForeignKeyColumn(self::POSTED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
//			parent::_addInteger(self::OWNER, 10);
//			parent::_addInteger(self::OWNER_TYPE, 2);
//			parent::_addFloat(self::USER_PAIN, 3);
//			parent::_addInteger(self::PAIN_BUG_TYPE, 3);
//			parent::_addInteger(self::PAIN_EFFECT, 3);
//			parent::_addInteger(self::PAIN_LIKELIHOOD, 3);
//			parent::_addInteger(self::ASSIGNED_TO, 10);
//			parent::_addText(self::REPRODUCTION_STEPS, false);
//			parent::_addForeignKeyColumn(self::RESOLUTION, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
//			parent::_addForeignKeyColumn(self::ISSUE_TYPE, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
//			parent::_addForeignKeyColumn(self::STATUS, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
//			parent::_addForeignKeyColumn(self::PRIORITY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
//			parent::_addForeignKeyColumn(self::CATEGORY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
//			parent::_addForeignKeyColumn(self::SEVERITY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
//			parent::_addForeignKeyColumn(self::REPRODUCABILITY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
//			parent::_addVarchar(self::SCRUMCOLOR, 7, '#FFFFFF');
//			parent::_addInteger(self::ESTIMATED_MONTHS, 10);
//			parent::_addInteger(self::ESTIMATED_WEEKS, 10);
//			parent::_addInteger(self::ESTIMATED_DAYS, 10);
//			parent::_addInteger(self::ESTIMATED_HOURS, 10);
//			parent::_addInteger(self::ESTIMATED_POINTS);
//			parent::_addInteger(self::SPENT_MONTHS, 10);
//			parent::_addInteger(self::SPENT_WEEKS, 10);
//			parent::_addInteger(self::SPENT_DAYS, 10);
//			parent::_addInteger(self::SPENT_HOURS, 10);
//			parent::_addInteger(self::VOTES_TOTAL, 10);
//			parent::_addInteger(self::SPENT_POINTS);
//			parent::_addInteger(self::PERCENT_COMPLETE, 2);
//			parent::_addInteger(self::ASSIGNED_TYPE, 2);
//			parent::_addInteger(self::DUPLICATE_OF, 10);
//			parent::_addBoolean(self::DELETED);
//			parent::_addBoolean(self::BLOCKING);
//			parent::_addBoolean(self::LOCKED);
//			parent::_addForeignKeyColumn(self::BEING_WORKED_ON_BY_USER, TBGUsersTable::getTable(), TBGUsersTable::ID);
//			parent::_addInteger(self::BEING_WORKED_ON_BY_USER_SINCE, 10);
//			parent::_addForeignKeyColumn(self::MILESTONE, TBGMilestonesTable::getTable(), TBGMilestonesTable::ID);
//			parent::_addForeignKeyColumn(self::WORKFLOW_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}

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

		public static function getValidSearchFilters()
		{
			return array('project_id', 'text', 'state', 'issuetype', 'status', 'resolution', 'reproducability', 'category', 'severity', 'priority', 'posted_by', 'assignee_user', 'assignee_team', 'owner_user', 'owner_team', 'component', 'build', 'edition', 'posted', 'last_updated');
		}

		public function getCountsByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::DELETED, 0);

			$crit2 = clone $crit;

			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		public function getCountsByProjectIDandIssuetype($project_id, $issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::ISSUE_TYPE, $issuetype_id);
			$crit->addWhere(self::DELETED, 0);
			
			$crit2 = clone $crit;
			
			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		protected function _getCountByProjectIDAndColumn($project_id, $column)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::ID, 'column_count', Criteria::DB_COUNT);
			$crit->addSelectionColumn($column);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addGroupBy($column);
			$crit->addWhere(self::DELETED, 0);

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
					if (!array_key_exists($row->get($column), $retarr)) $retarr[$row->get($column)] = array('open' => 0, 'closed' => 0);
					$retarr[$row->get($column)]['closed'] = $row->get('column_count');
				}
			}
			if ($res2)
			{
				while ($row = $res2->getNextRow())
				{
					if (!array_key_exists($row->get($column), $retarr)) $retarr[$row->get($column)] = array('open' => 0, 'closed' => 0);
					$retarr[$row->get($column)]['open'] = $row->get('column_count');
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
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if (!$milestone_id)
			{
				$crit->addWhere(self::MILESTONE, null);
			}
			else
			{
				$crit->addWhere(self::MILESTONE, $milestone_id);
			}
			$crit->addWhere(self::DELETED, 0);
			if ($exclude_tasks)
			{
				$crit->addJoin(TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID, self::ISSUE_TYPE);
				$crit->addWhere(TBGIssueTypesTable::TASK, false);
			}
			
			$crit2 = clone $crit;
			
			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		public function getOpenAffectedIssuesByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, 0);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function getPriorityCountByProjectID($project_id)
		{
			return $this->_getCountByProjectIDAndColumn($project_id, self::PRIORITY);
		}

		public function getStatusCountByProjectID($project_id)
		{
			return $this->_getCountByProjectIDAndColumn($project_id, self::STATUS);
		}

		public function getCategoryCountByProjectID($project_id)
		{
			return $this->_getCountByProjectIDAndColumn($project_id, self::CATEGORY);
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
			$crit->addWhere(self::PROJECT_ID, $id);
			$crit->addWhere(self::DELETED, 0);
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
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::DELETED, 0);
			$row = $this->doSelectById($id, $crit, false);
			return $row;
		}
		
		public function getCountByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, 0);
			$res = $this->doCount($crit);
			return $res;
		}
		
		public function getNextIssueNumberForProductID($p_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $p_id);
			$crit->addWhere(self::DELETED, 0);
			$crit->addSelectionColumn(self::ISSUE_NO, 'issueno', Criteria::DB_MAX, '', '+1');
			$row = $this->doSelectOne($crit, 'none');
			$issue_no = $row->get('issueno');
			return ($issue_no < 1) ? 1 : $issue_no;
		}
		
		public function getByPrefixAndIssueNo($prefix, $issue_no)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(TBGProjectsTable::PREFIX, mb_strtolower($prefix), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
			$crit->addWhere(TBGProjectsTable::DELETED, false);
			$crit->addWhere(self::ISSUE_NO, $issue_no);
			$crit->addWhere(self::DELETED, 0);
			$row = $this->doSelectOne($crit);
			return $row;
		}

		public function getByProjectIDAndIssueNo($project_id, $issue_no)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::ISSUE_NO, $issue_no);
			$crit->addWhere(self::DELETED, 0);
			return $this->selectOne($crit);
//			return $row;
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
			if (!$milestone_id)
			{
				$crit->addWhere(self::MILESTONE, null);
				$crit->addWhere(self::PROJECT_ID, $project_id);
			}
			else
			{
				$crit->addWhere(self::MILESTONE, $milestone_id);
			}
			$crit->addWhere(self::DELETED, 0);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getPointsAndTimeByMilestone($milestone_id)
		{
			$crit = $this->getCriteria();
			if (!$milestone_id)
			{
				$crit->addWhere(self::MILESTONE, null);
			}
			else
			{
				$crit->addWhere(self::MILESTONE, $milestone_id);
			}
			$crit->addWhere(self::DELETED, 0);
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
			$crit->addWhere(self::MILESTONE, null);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByProjectIDandNoMilestoneandTypes($project_id, $issuetypes)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MILESTONE, null);
			$crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByProjectIDandNoMilestoneandTypesandState($project_id, $issuetypes, $state)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MILESTONE, null);
			$crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
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
			$crit->addWhere(self::ASSIGNED_TEAM, $team_id);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}
				
		public function getOpenIssuesByUserAssigned($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ASSIGNEE_USER, $user_id);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function getOpenIssuesByProjectIDAndIssueTypes($project_id, $issuetypes, $order_by=null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::ISSUE_TYPE, $issuetypes, Criteria::DB_IN);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
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

		public function findIssues($filters = array(), $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			if (count($filters) > 0)
			{
				$crit->addJoin(TBGIssueCustomFieldsTable::getTable(), TBGIssueCustomFieldsTable::ISSUE_ID, TBGIssuesTable::ID);
				$crit->addJoin(TBGIssueAffectsComponentTable::getTable(), TBGIssueAffectsComponentTable::ISSUE, self::ID);
				$crit->addJoin(TBGIssueAffectsEditionTable::getTable(), TBGIssueAffectsEditionTable::ISSUE, self::ID);
				$crit->addJoin(TBGIssueAffectsBuildTable::getTable(), TBGIssueAffectsBuildTable::ISSUE, self::ID);
//				$crit->addJoin(TBGMilestonesTable::getTable(), TBGMilestonesTable::ID, self::MILESTONE);

				foreach ($filters as $filter => $filter_info)
				{
					if ($filter == 'component')
					{
						$dbname = TBGIssueAffectsComponentTable::getTable();
					}
					elseif ($filter == 'edition')
					{
						$dbname = TBGIssueAffectsEditionTable::getTable();
					}
					elseif ($filter == 'build')
					{
						$dbname = TBGIssueAffectsBuildTable::getTable();
					}
					else
					{
						$dbname = $this->getB2DBName();
					}

					if (array_key_exists('value', $filter_info) && in_array($filter_info['operator'], array('=', '!=', '<=', '>=', '<', '>')))
					{
						if ($filter == 'text')
						{
							if ($filter_info['value'] != '')
							{
								$searchterm = (mb_strpos($filter_info['value'], '%') !== false) ? $filter_info['value'] : "%{$filter_info['value']}%";
								if ($filter_info['operator'] == '=')
								{
									$ctn = $crit->returnCriterion(self::TITLE, $searchterm, Criteria::DB_LIKE);
									$ctn->addOr(self::DESCRIPTION, $searchterm, Criteria::DB_LIKE);
									$ctn->addOr(self::REPRODUCTION_STEPS, $searchterm, Criteria::DB_LIKE);
									$ctn->addOr(TBGIssueCustomFieldsTable::OPTION_VALUE, $searchterm, Criteria::DB_LIKE);
								}
								else
								{
									$ctn = $crit->returnCriterion(self::TITLE, $searchterm, Criteria::DB_NOT_LIKE);
									$ctn->addWhere(self::DESCRIPTION, $searchterm, Criteria::DB_NOT_LIKE);
									$ctn->addWhere(self::REPRODUCTION_STEPS, $searchterm, Criteria::DB_NOT_LIKE);
									$ctn->addOr(TBGIssueCustomFieldsTable::OPTION_VALUE, $searchterm, Criteria::DB_NOT_LIKE);
								}
								$crit->addWhere($ctn);
							}
						}
						elseif (in_array($filter, self::getValidSearchFilters()))
						{
							$crit->addWhere($dbname.'.'.$filter, $filter_info['value'], urldecode($filter_info['operator']));
						}
						elseif (TBGCustomDatatype::doesKeyExist($filter))
						{
							$customdatatype = TBGCustomDatatype::getByKey($filter);
							$ctn = $crit->returnCriterion(TBGIssueCustomFieldsTable::CUSTOMFIELDS_ID, $customdatatype->getID());
							$ctn->addWhere(TBGIssueCustomFieldsTable::OPTION_VALUE, $filter_info['value'], $filter_info['operator']);
							$crit->addWhere($ctn);
						}
					}
					else
					{
						if (in_array($filter, self::getValidSearchFilters()))
						{
							$first_val = array_shift($filter_info);
							if ($filter == 'text')
							{
								$filter_info = $first_val;
								if ($filter_info['value'] != '')
								{
									$searchterm = (mb_strpos($filter_info['value'], '%') !== false) ? $filter_info['value'] : "%{$filter_info['value']}%";
									if ($filter_info['operator'] == '=')
									{
										$ctn = $crit->returnCriterion(self::TITLE, $searchterm, Criteria::DB_LIKE);
										$ctn->addOr(self::DESCRIPTION, $searchterm, Criteria::DB_LIKE);
										$ctn->addOr(self::REPRODUCTION_STEPS, $searchterm, Criteria::DB_LIKE);
									}
									else
									{
										$ctn = $crit->returnCriterion(self::TITLE, $searchterm, Criteria::DB_NOT_LIKE);
										$ctn->addWhere(self::DESCRIPTION, $searchterm, Criteria::DB_NOT_LIKE);
										$ctn->addWhere(self::REPRODUCTION_STEPS, $searchterm, Criteria::DB_NOT_LIKE);
									}
									$crit->addWhere($ctn);
								}
							}
							else
							{
								$ctn = $crit->returnCriterion($dbname.'.'.$filter, $first_val['value'], urldecode($first_val['operator']));
								if (count($filter_info) > 0)
								{
									foreach ($filter_info as $single_filter)
									{
										if (in_array($single_filter['operator'], array('=', '<=', '>=', '<', '>')) && !in_array($filter, array('posted', 'last_updated')))
										{
											$ctn->addOr($dbname.'.'.$filter, $single_filter['value'], urldecode($single_filter['operator']));
										}
										elseif ($single_filter['operator'] == '!=' || in_array($filter, array('posted', 'last_updated')))
										{
											$ctn->addWhere($dbname.'.'.$filter, $single_filter['value'], urldecode($single_filter['operator']));
										}
									}
								}
								$crit->addWhere($ctn);
							}
						}
						elseif (TBGCustomDatatype::doesKeyExist($filter))
						{
							$customdatatype = TBGCustomDatatype::getByKey($filter);
							$first_val = array_shift($filter_info);
							$ctn = $crit->returnCriterion(TBGIssueCustomFieldsTable::CUSTOMFIELDS_ID, $customdatatype->getID());
							$ctn->addWhere(TBGIssueCustomFieldsTable::OPTION_VALUE, $first_val['value'], $first_val['operator']);
							if (count($filter_info) > 0)
							{
								foreach ($filter_info as $single_filter)
								{
									if (in_array($single_filter['operator'], array('=', '!=', '<=', '>=', '<', '>')))
									{
										$ctn->addOr(TBGIssueCustomFieldsTable::OPTION_VALUE, $single_filter['value'], $single_filter['operator']);
									}
								}
							}
							$crit->addWhere($ctn);
						}
					}
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
							break;
						case 'status':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::STATUS);
							$crit->addSelectionColumn(self::STATUS);
							$crit->addOrderBy(self::STATUS, $grouporder);
							break;
						case 'milestone':
							$crit->addSelectionColumn(self::MILESTONE);
							$crit->addSelectionColumn(self::PERCENT_COMPLETE);
							$crit->addOrderBy(self::MILESTONE, $grouporder);
							$crit->addOrderBy(self::PERCENT_COMPLETE, 'desc');
							break;
						case 'assignee':
							$crit->addSelectionColumn(self::ASSIGNEE_TEAM);
							$crit->addSelectionColumn(self::ASSIGNEE_USER);
							$crit->addOrderBy(self::ASSIGNEE_TEAM);
							$crit->addOrderBy(self::ASSIGNEE_USER, $grouporder);
							break;
						case 'state':
							$crit->addSelectionColumn(self::STATE);
							$crit->addOrderBy(self::STATE, $grouporder);
							break;
						case 'severity':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::SEVERITY);
							$crit->addSelectionColumn(self::SEVERITY);
							$crit->addOrderBy(self::SEVERITY, $grouporder);
							break;
						case 'user_pain':
							$crit->addSelectionColumn(self::USER_PAIN);
							$crit->addOrderBy(self::USER_PAIN, $grouporder);
							break;
						case 'votes':
							$crit->addSelectionColumn(self::VOTES_TOTAL);
							$crit->addOrderBy(self::VOTES_TOTAL, $grouporder);
							break;
						case 'resolution':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::RESOLUTION);
							$crit->addSelectionColumn(self::RESOLUTION);
							$crit->addOrderBy(self::RESOLUTION, $grouporder);
							break;
						case 'priority':
							$crit->addJoin(TBGListTypesTable::getTable(), TBGListTypesTable::ID, self::PRIORITY);
							$crit->addSelectionColumn(self::PRIORITY);
							$crit->addOrderBy(self::PRIORITY, $grouporder);
							break;
						case 'issuetype':
							$crit->addJoin(TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID, self::ISSUE_TYPE);
							$crit->addSelectionColumn(TBGIssueTypesTable::NAME);
							$crit->addOrderBy(TBGIssueTypesTable::NAME, $grouporder);
							break;
						case 'edition':
							$crit->addJoin(TBGIssueAffectsEditionTable::getTable(), TBGIssueAffectsEditionTable::ISSUE, self::ID);
							$crit->addJoin(TBGEditionsTable::getTable(), TBGEditionsTable::ID, TBGIssueAffectsEditionTable::EDITION, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsEditionTable::getTable());
							$crit->addSelectionColumn(TBGEditionsTable::NAME);
							$crit->addOrderBy(TBGEditionsTable::NAME, $grouporder);
							break;
						case 'build':
							$crit->addJoin(TBGIssueAffectsBuildTable::getTable(), TBGIssueAffectsBuildTable::ISSUE, self::ID);
							$crit->addJoin(TBGBuildsTable::getTable(), TBGBuildsTable::ID, TBGIssueAffectsBuildTable::BUILD, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsBuildTable::getTable());
							$crit->addSelectionColumn(TBGBuildsTable::NAME);
							$crit->addOrderBy(TBGBuildsTable::NAME, $grouporder);
							break;
						case 'component':
							$crit->addJoin(TBGIssueAffectsComponentTable::getTable(), TBGIssueAffectsComponentTable::ISSUE, self::ID);
							$crit->addJoin(TBGComponentsTable::getTable(), TBGComponentsTable::ID, TBGIssueAffectsComponentTable::COMPONENT, array(), Criteria::DB_LEFT_JOIN, TBGIssueAffectsComponentTable::getTable());
							$crit->addSelectionColumn(TBGComponentsTable::NAME);
							$crit->addOrderBy(TBGComponentsTable::NAME, $grouporder);
							break;
					}
				}
				
				$crit->addSelectionColumn(self::LAST_UPDATED);
				$crit->addOrderBy(self::LAST_UPDATED, 'asc');

				$res = $this->doSelect($crit, 'none');
				$ids = array();

				while ($row = $res->getNextRow())
				{
					$ids[] = $row->get(self::ID);
				}
				
				$ids = array_reverse($ids);
				
				$crit2 = $this->getCriteria();
				$crit2->addWhere(self::ID, $ids, Criteria::DB_IN);
				$crit2->addOrderBy(self::ID, $ids);
				
				$res = $this->doSelect($crit2);
				
				return array($res, $count);
			}
			else
			{
				return array(null, 0);
			}

		}
		
		public function getDuplicateIssuesByIssueNo($issue_no)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::ID);
			$crit->addWhere(self::DUPLICATE_OF, $issue_no);
			
			return $this->doSelect($crit);
		}

		public function saveVotesTotalForIssueID($votes_total, $issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::VOTES_TOTAL, $votes_total);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doUpdateById($crit, $issue_id);
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
			$crit->addJoin(TBGProjectsTable::getTable(), TBGProjectsTable::ID, self::PROJECT_ID);
			$crit->addWhere(self::POSTED_BY, $user_id);
			$crit->addWhere(self::DELETED, 0);
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

	}
