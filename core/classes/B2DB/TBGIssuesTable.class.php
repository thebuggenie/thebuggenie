<?php

	/**
	 * Issues table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issues table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssuesTable extends B2DBTable 
	{
		const B2DBNAME = 'issues';
		const ID = 'issues.id';
		const SCOPE = 'issues.scope';
		const ISSUE_NO = 'issues.issue_no';
		const TITLE = 'issues.title';
		const POSTED = 'issues.posted';
		const LAST_UPDATED = 'issues.last_updated';
		const PROJECT_ID = 'issues.project_id';
		const LONG_DESCRIPTION = 'issues.long_description';
		const REPRODUCTION = 'issues.reproduction';
		const ISSUE_TYPE = 'issues.issue_type';
		const RESOLUTION = 'issues.resolution';
		const STATE = 'issues.state';
		const POSTED_BY = 'issues.posted_by';
		const OWNED_BY = 'issues.owned_by';
		const ASSIGNED_TO = 'issues.assigned_to';
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
		const ASSIGNED_TYPE = 'issues.assigned_type';
		const USER_WORKING_ON = 'issues.user_working_on';
		const USER_WORKED_ON_SINCE = 'issues.user_worked_on_since';
		const USER_PAIN = 'issues.user_pain';
		const PAIN_BUG_TYPE = 'issues.pain_bug_type';
		const PAIN_EFFECT = 'issues.pain_effect';
		const PAIN_LIKELIHOOD = 'issues.pain_likelihood';
		const OWNED_TYPE = 'issues.owned_type';
		const DUPLICATE = 'issues.duplicate';
		const DELETED = 'issues.deleted';
		const BLOCKING = 'issues.blocking';
		const LOCKED = 'issues.locked';
		const MILESTONE = 'issues.milestone';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGIssuesTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGIssuesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::ISSUE_NO, 10);
			parent::_addVarchar(self::TITLE, 200);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::LAST_UPDATED, 10);
			parent::_addForeignKeyColumn(self::PROJECT_ID, B2DB::getTable('TBGProjectsTable'), TBGProjectsTable::ID);
			parent::_addText(self::LONG_DESCRIPTION, false);
			parent::_addBoolean(self::STATE);
			parent::_addForeignKeyColumn(self::POSTED_BY, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addInteger(self::OWNED_BY, 10);
			parent::_addFloat(self::USER_PAIN, 1);
			parent::_addInteger(self::PAIN_BUG_TYPE, 3);
			parent::_addInteger(self::PAIN_EFFECT, 3);
			parent::_addInteger(self::PAIN_LIKELIHOOD, 3);
			parent::_addInteger(self::ASSIGNED_TO, 10);
			parent::_addText(self::REPRODUCTION, false);
			parent::_addForeignKeyColumn(self::RESOLUTION, B2DB::getTable('TBGListTypesTable'), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE_TYPE, B2DB::getTable('TBGIssueTypesTable'), TBGIssueTypesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS, B2DB::getTable('TBGListTypesTable'), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::PRIORITY, B2DB::getTable('TBGListTypesTable'), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::CATEGORY, B2DB::getTable('TBGListTypesTable'), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::SEVERITY, B2DB::getTable('TBGListTypesTable'), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::REPRODUCABILITY, B2DB::getTable('TBGListTypesTable'), TBGListTypesTable::ID);
			parent::_addVarchar(self::SCRUMCOLOR, 7, '#FFFFFF');
			parent::_addInteger(self::ESTIMATED_MONTHS, 10);
			parent::_addInteger(self::ESTIMATED_WEEKS, 10);
			parent::_addInteger(self::ESTIMATED_DAYS, 10);
			parent::_addInteger(self::ESTIMATED_HOURS, 10);
			parent::_addFloat(self::ESTIMATED_POINTS);
			parent::_addInteger(self::SPENT_MONTHS, 10);
			parent::_addInteger(self::SPENT_WEEKS, 10);
			parent::_addInteger(self::SPENT_DAYS, 10);
			parent::_addInteger(self::SPENT_HOURS, 10);
			parent::_addFloat(self::SPENT_POINTS);
			parent::_addInteger(self::PERCENT_COMPLETE, 2);
			parent::_addInteger(self::ASSIGNED_TYPE, 2);
			parent::_addInteger(self::OWNED_TYPE, 2);
			parent::_addBoolean(self::DUPLICATE);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::BLOCKING);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::USER_WORKING_ON, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addInteger(self::USER_WORKED_ON_SINCE, 10);
			parent::_addForeignKeyColumn(self::MILESTONE, B2DB::getTable('TBGMilestonesTable'), TBGMilestonesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
		}

		public static function getValidSearchFilters()
		{
			return array('project_id', 'text', 'state', 'issue_type', 'status', 'resolution', 'category', 'severity', 'priority', 'posted_by', 'assigned_to', 'assigned_type');
		}

		public function getCountsByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$crit2 = clone $crit;

			$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		public function getLast30IssueCountsByProjectID($project_id)
		{
			$retarr = array();

			for ($cc = 30; $cc >= 0; $cc--)
			{
				$crit = $this->getCriteria();
				$crit->addWhere(self::PROJECT_ID, $project_id);
				$crit->addWhere(self::DELETED, false);
				$crit->addJoin(B2DB::getTable('TBGIssueTypesTable'), TBGIssueTypesTable::ID, self::ISSUE_TYPE);
				$crit->addWhere(TBGIssueTypesTable::ICON, array('developer_report', 'task'), B2DBCriteria::DB_NOT_IN);
				$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
				$ctn = $crit->returnCriterion(self::POSTED, $_SERVER['REQUEST_TIME'] - (86400 * 31), B2DBCriteria::DB_GREATER_THAN_EQUAL);
				$ctn->addWhere(self::POSTED, $_SERVER['REQUEST_TIME'] - (86400 * $cc), B2DBCriteria::DB_LESS_THAN_EQUAL);
				$crit->addWhere($ctn);

				$crit2 = clone $crit;

				$crit->addWhere(self::STATE, TBGIssue::STATE_CLOSED);

				$retarr[0][$cc] = $this->doCount($crit);
				$retarr[1][$cc] = $this->doCount($crit2);
			}
			return $retarr;
		}

		public function getCountsByProjectIDandIssuetype($project_id, $issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
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
			$crit->addSelectionColumn(self::ID, 'column_count', B2DBCriteria::DB_COUNT);
			$crit->addSelectionColumn($column);
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
		
		public function getCountsByProjectIDandMilestone($project_id, $milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::MILESTONE, $milestone_id);
			
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

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit, false);
			return $row;
		}
		
		public function getCountByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doCount($crit);
			return $res;
		}
		
		public function createNewWithTransaction($title, $issue_type, $p_id, $issue_id = null)
		{
			$trans = B2DB::startTransaction();
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $p_id);
			$crit->addSelectionColumn(self::ISSUE_NO, 'issueno', B2DBCriteria::DB_MAX, '', '+1');
			$row = $this->doSelectOne($crit, 'none');
			$issue_no = $row->get('issueno');
			if ($issue_no < 1) $issue_no = 1;
			
			$status_id = (int) TBGFactory::projectLab($p_id)->getDefaultStatusID();
			
			$crit = $this->getCriteria();
			$posted = $_SERVER["REQUEST_TIME"];
			
			if ($issue_id !== null)
			{
				$crit->addInsert(self::ID, $issue_id);
			}
			$crit->addInsert(self::ISSUE_NO, (int) $issue_no);
			$crit->addInsert(self::POSTED, $posted);
			$crit->addInsert(self::LAST_UPDATED, $posted);
			$crit->addInsert(self::TITLE, $title);
			$crit->addInsert(self::PROJECT_ID, $p_id);
			$crit->addInsert(self::ISSUE_TYPE, $issue_type);
			$crit->addInsert(self::POSTED_BY, TBGContext::getUser()->getUID());
			$crit->addInsert(self::STATUS, $status_id);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			$trans->commitAndEnd();
			return ($issue_id === null) ? $res->getInsertID() : $issue_id;
		}
		
		public function getByPrefixAndIssueNo($prefix, $issue_no)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(TBGProjectsTable::PREFIX, $prefix);
			$crit->addWhere(self::ISSUE_NO, $issue_no);
			$row = $this->doSelectOne($crit, array(self::PROJECT_ID));
			return $row;
		}

		public function getByProjectIDAndIssueNo($project_id, $issue_no)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::ISSUE_NO, $issue_no);
			$row = $this->doSelectOne($crit);
			return $row;
		}

		public function setDuplicate($issue_id, $duplicate_of)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::DUPLICATE, $duplicate_of);
			$this->doUpdateById($crit, $issue_id);
		}
		
		public function getByMilestone($milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MILESTONE, $milestone_id);
			$crit->addWhere(self::DELETED, 0);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByProjectIDandNoMilestone($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MILESTONE, 0);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, 0);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function clearMilestone($milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::MILESTONE, 0);
			$crit->addWhere(self::MILESTONE, $milestone_id);
			$this->doUpdate($crit);
		}
		
		public function getOpenIssuesByTeamAssigned($team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ASSIGNED_TO, $team_id);
			$crit->addWhere(self::ASSIGNED_TYPE, TBGIdentifiableClass::TYPE_TEAM);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}
				
		public function getOpenIssuesByUserAssigned($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ASSIGNED_TO, $user_id);
			$crit->addWhere(self::ASSIGNED_TYPE, TBGIdentifiableClass::TYPE_USER);
			$crit->addWhere(self::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function getRecentByProjectIDandIssueType($project_id, $issuetypes, $limit = 5)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(B2DB::getTable('TBGIssueTypesTable'), TBGIssueTypesTable::ID, self::ISSUE_TYPE);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(TBGIssueTypesTable::ICON, $issuetypes, B2DBCriteria::DB_IN);
			$crit->addOrderBy(self::POSTED, B2DBCriteria::SORT_DESC);
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}

			$res = $this->doSelect($crit);

			return $res;
		}
		
		public function getTotalPointsByMilestoneID($milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::ESTIMATED_POINTS, 'estimated_points', B2DBCriteria::DB_SUM);
			$crit->addSelectionColumn(self::SPENT_POINTS, 'spent_points', B2DBCriteria::DB_SUM);
			$crit->addWhere(self::MILESTONE, $milestone_id);
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
			$crit->addSelectionColumn(self::ESTIMATED_HOURS, 'estimated_hours', B2DBCriteria::DB_SUM);
			$crit->addSelectionColumn(self::SPENT_HOURS, 'spent_hours', B2DBCriteria::DB_SUM);
			$crit->addWhere(self::MILESTONE, $milestone_id);
			if ($res = $this->doSelectOne($crit))
			{
				return array($res->get('estimated_hours'), $res->get('spent_hours'));
			}
			else
			{
				return array(0, 0);
			}
		}

		public function findIssues($filters = array(), $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null)
		{
			$crit = $this->getCriteria();
			if (count($filters) > 0)
			{
				foreach ($filters as $filter => $filter_info)
				{
					if (!in_array($filter, self::getValidSearchFilters()))
					{
						$crit->addJoin(B2DB::getTable('TBGIssueCustomFieldsTable'), TBGIssueCustomFieldsTable::ISSUE_ID, TBGIssuesTable::ID);
						break;
					}
				}

				foreach ($filters as $filter => $filter_info)
				{
					if (array_key_exists('value', $filter_info) && in_array($filter_info['operator'], array('=', '!=', '<=', '>=', '<', '>')))
					{
						if ($filter == 'text')
						{
							if ($filter_info['value'] != '')
							{
								$searchterm = (strpos($filter_info['value'], '%') !== false) ? $filter_info['value'] : "%{$filter_info['value']}%";
								if ($filter_info['operator'] == '=')
								{
									$ctn = $crit->returnCriterion(self::TITLE, $searchterm, B2DBCriteria::DB_LIKE);
									$ctn->addOr(self::LONG_DESCRIPTION, $searchterm, B2DBCriteria::DB_LIKE);
									$ctn->addOr(self::REPRODUCTION, $searchterm, B2DBCriteria::DB_LIKE);
								}
								else
								{
									$ctn = $crit->returnCriterion(self::TITLE, $searchterm, B2DBCriteria::DB_NOT_LIKE);
									$ctn->addWhere(self::LONG_DESCRIPTION, $searchterm, B2DBCriteria::DB_NOT_LIKE);
									$ctn->addWhere(self::REPRODUCTION, $searchterm, B2DBCriteria::DB_NOT_LIKE);
								}
								$crit->addWhere($ctn);
							}
						}
						elseif (in_array($filter, self::getValidSearchFilters()))
						{
							$crit->addWhere($this->getB2DBName().'.'.$filter, $filter_info['value'], $filter_info['operator']);
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
									$searchterm = (strpos($filter_info['value'], '%') !== false) ? $filter_info['value'] : "%{$filter_info['value']}%";
									if ($filter_info['operator'] == '=')
									{
										$ctn = $crit->returnCriterion(self::TITLE, $searchterm, B2DBCriteria::DB_LIKE);
										$ctn->addOr(self::LONG_DESCRIPTION, $searchterm, B2DBCriteria::DB_LIKE);
										$ctn->addOr(self::REPRODUCTION, $searchterm, B2DBCriteria::DB_LIKE);
									}
									else
									{
										$ctn = $crit->returnCriterion(self::TITLE, $searchterm, B2DBCriteria::DB_NOT_LIKE);
										$ctn->addWhere(self::LONG_DESCRIPTION, $searchterm, B2DBCriteria::DB_NOT_LIKE);
										$ctn->addWhere(self::REPRODUCTION, $searchterm, B2DBCriteria::DB_NOT_LIKE);
									}
									$crit->addWhere($ctn);
								}
							}
							else
							{
								$ctn = $crit->returnCriterion($this->getB2DBName().'.'.$filter, $first_val['value'], $first_val['operator']);
								if (count($filter_info) > 0)
								{
									foreach ($filter_info as $single_filter)
									{
										if (in_array($single_filter['operator'], array('=', '!=', '<=', '>=', '<', '>')))
										{
											$ctn->addOr($this->getB2DBName().'.'.$filter, $single_filter['value'], $single_filter['operator']);
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

			if ($groupby !== null)
			{
				$grouporder = ($grouporder !== null) ? (($grouporder == 'asc') ? B2DBCriteria::SORT_ASC : B2DBCriteria::SORT_DESC) : B2DBCriteria::SORT_ASC;
				switch ($groupby)
				{
					case 'category':
						$crit->addOrderBy(TBGListTypesTable::CATEGORY, $grouporder);
						break;
					case 'status':
						$crit->addOrderBy(self::STATUS, $grouporder);
						break;
					case 'milestone':
						$crit->addOrderBy(self::MILESTONE, $grouporder);
						break;
					case 'assignee':
						$crit->addOrderBy(self::ASSIGNED_TYPE);
						$crit->addOrderBy(self::ASSIGNED_TO, $grouporder);
						break;
					case 'state':
						$crit->addOrderBy(self::STATE, $grouporder);
						break;
					case 'severity':
						$crit->addOrderBy(self::SEVERITY, $grouporder);
						break;
					case 'user_pain':
						$crit->addOrderBy(self::USER_PAIN, $grouporder);
						break;
					case 'resolution':
						$crit->addOrderBy(self::RESOLUTION, $grouporder);
						break;
					case 'priority':
						$crit->addOrderBy(self::PRIORITY, $grouporder);
						break;
					case 'issuetype':
						$crit->addOrderBy(self::ISSUE_TYPE, $grouporder);
						break;
				}
			}

			$crit2 = clone $crit;
			$count = $this->doCount($crit2);

			if ($results_per_page != 0)
			{
				$crit->setLimit($results_per_page);
			}

			if ($offset != 0)
			{
				$crit->setOffset($offset);
			}

			$res = $this->doSelect($crit);

			return array($res, $count);

		}
		
	}
