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
	class B2tIssues extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_issues';
		const ID = 'bugs2_issues.id';
		const SCOPE = 'bugs2_issues.scope';
		const ISSUE_NO = 'bugs2_issues.issue_no';
		const TITLE = 'bugs2_issues.title';
		const POSTED = 'bugs2_issues.posted';
		const LAST_UPDATED = 'bugs2_issues.last_updated';
		const PROJECT_ID = 'bugs2_issues.project_id';
		const LONG_DESCRIPTION = 'bugs2_issues.long_description';
		const REPRODUCTION = 'bugs2_issues.reproduction';
		const ISSUE_TYPE = 'bugs2_issues.issue_type';
		const RESOLUTION = 'bugs2_issues.resolution';
		const STATE = 'bugs2_issues.state';
		const POSTED_BY = 'bugs2_issues.posted_by';
		const OWNED_BY = 'bugs2_issues.owned_by';
		const ASSIGNED_TO = 'bugs2_issues.assigned_to';
		const STATUS = 'bugs2_issues.status';
		const PRIORITY = 'bugs2_issues.priority';
		const SEVERITY = 'bugs2_issues.severity';
		const CATEGORY = 'bugs2_issues.category';
		const REPRODUCABILITY = 'bugs2_issues.reproducability';
		const SCRUMCOLOR = 'bugs2_issues.scrumcolor';
		const ESTIMATED_MONTHS = 'bugs2_issues.estimated_months';
		const ESTIMATED_WEEKS = 'bugs2_issues.estimated_weeks';
		const ESTIMATED_DAYS = 'bugs2_issues.estimated_days';
		const ESTIMATED_HOURS = 'bugs2_issues.estimated_hours';
		const ESTIMATED_POINTS = 'bugs2_issues.estimated_points';
		const SPENT_MONTHS = 'bugs2_issues.spent_months';
		const SPENT_WEEKS = 'bugs2_issues.spent_weeks';
		const SPENT_DAYS = 'bugs2_issues.spent_days';
		const SPENT_HOURS = 'bugs2_issues.spent_hours';
		const SPENT_POINTS = 'bugs2_issues.spent_points';
		const PERCENT_COMPLETE = 'bugs2_issues.percent_complete';
		const ASSIGNED_TYPE = 'bugs2_issues.assigned_type';
		const USER_WORKING_ON = 'bugs2_issues.user_working_on';
		const USER_WORKED_ON_SINCE = 'bugs2_issues.user_worked_on_since';
		const OWNED_TYPE = 'bugs2_issues.owned_type';
		const DUPLICATE = 'bugs2_issues.duplicate';
		const DELETED = 'bugs2_issues.deleted';
		const BLOCKING = 'bugs2_issues.blocking';
		const MILESTONE = 'bugs2_issues.milestone';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::ISSUE_NO, 10);
			parent::_addVarchar(self::TITLE, 200);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::LAST_UPDATED, 10);
			parent::_addForeignKeyColumn(self::PROJECT_ID, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addText(self::LONG_DESCRIPTION, false);
			parent::_addBoolean(self::STATE);
			parent::_addForeignKeyColumn(self::POSTED_BY, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addInteger(self::OWNED_BY, 10);
			parent::_addInteger(self::ASSIGNED_TO, 10);
			parent::_addText(self::REPRODUCTION, false);
			parent::_addForeignKeyColumn(self::RESOLUTION, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addForeignKeyColumn(self::ISSUE_TYPE, B2DB::getTable('B2tIssueTypes'), B2tIssueTypes::ID);
			parent::_addForeignKeyColumn(self::STATUS, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addForeignKeyColumn(self::PRIORITY, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addForeignKeyColumn(self::CATEGORY, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addForeignKeyColumn(self::SEVERITY, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addForeignKeyColumn(self::REPRODUCABILITY, B2DB::getTable('B2tListTypes'), B2tListTypes::ID);
			parent::_addVarchar(self::SCRUMCOLOR, 7, '#FFFFFF');
			parent::_addInteger(self::ESTIMATED_MONTHS, 10);
			parent::_addInteger(self::ESTIMATED_WEEKS, 10);
			parent::_addInteger(self::ESTIMATED_DAYS, 10);
			parent::_addInteger(self::ESTIMATED_HOURS, 10);
			parent::_addInteger(self::ESTIMATED_POINTS, 10);
			parent::_addInteger(self::SPENT_MONTHS, 10);
			parent::_addInteger(self::SPENT_WEEKS, 10);
			parent::_addInteger(self::SPENT_DAYS, 10);
			parent::_addInteger(self::SPENT_HOURS, 10);
			parent::_addInteger(self::SPENT_POINTS, 10);
			parent::_addInteger(self::PERCENT_COMPLETE, 2);
			parent::_addInteger(self::ASSIGNED_TYPE, 2);
			parent::_addInteger(self::OWNED_TYPE, 2);
			parent::_addBoolean(self::DUPLICATE);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::BLOCKING);
			parent::_addForeignKeyColumn(self::USER_WORKING_ON, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addInteger(self::USER_WORKED_ON_SINCE, 10);
			parent::_addForeignKeyColumn(self::MILESTONE, B2DB::getTable('B2tMilestones'), B2tMilestones::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getCountsByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			
			$crit2 = clone $crit;
			
			$crit->addWhere(self::STATE, BUGSissue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, BUGSissue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}
		
		public function getCountsByProjectIDandIssuetype($project_id, $issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(self::ISSUE_TYPE, $issuetype_id);
			
			$crit2 = clone $crit;
			
			$crit->addWhere(self::STATE, BUGSissue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, BUGSissue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}
		
		public function getCountsByProjectIDandMilestone($project_id, $milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(self::MILESTONE, $milestone_id);
			
			$crit2 = clone $crit;
			
			$crit->addWhere(self::STATE, BUGSissue::STATE_CLOSED);
			$crit2->addWhere(self::STATE, BUGSissue::STATE_OPEN);
			return array($this->doCount($crit), $this->doCount($crit2));
		}

		public function getOpenAffectedIssuesByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::STATE, BUGSissue::STATE_OPEN);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
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
			
			$crit2 = new B2DBCriteria();
			$crit2->setFromTable($this, true);
			//$crit2->addJoin(B2DB::getTable('B2tProjects'));
			$crit2->addWhere(self::PROJECT_ID, $p_id);
			
			$crit2->addSelectionColumn(self::ISSUE_NO, '', B2DBCriteria::DB_MAX);
			//$crit2->addSelectionColumn(B2tProjects::DEFAULT_STATUS);
			$row = $this->doSelectOne($crit2, 'none');
			$status_id = (int) BUGSfactory::projectLab($p_id)->getDefaultStatusID();
			
			$crit = $this->getCriteria();
			$posted = $_SERVER["REQUEST_TIME"];
			
			if ($issue_id !== null)
			{
				$crit->addInsert(self::ID, $issue_id);
			}
			$crit->addInsert(self::ISSUE_NO, (int) $row->get(self::ISSUE_NO) + 1);
			$crit->addInsert(self::POSTED, $posted);
			$crit->addInsert(self::LAST_UPDATED, $posted);
			$crit->addInsert(self::TITLE, $title);
			$crit->addInsert(self::PROJECT_ID, $p_id);
			$crit->addInsert(self::ISSUE_TYPE, $issue_type);
			$crit->addInsert(self::POSTED_BY, BUGScontext::getUser()->getUID());
			$crit->addInsert(self::STATUS, $status_id);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			$trans->commitAndEnd();
			return ($issue_id === null) ? $res->getInsertID() : $issue_id;
		}
		
		public function createNewInitialIssueForProject($title, $description, $issue_type, $p_id, $issue_id = null)
		{
			$project = BUGSfactory::projectLab($p_id);
			$posted = $_SERVER["REQUEST_TIME"];
			
			$crit = $this->getCriteria();
			if ($issue_id !== null)
			{
				$crit->addInsert(self::ID, $issue_id);
			}
			$crit->addInsert(self::ISSUE_NO, 1);
			$crit->addInsert(self::POSTED, $posted);
			$crit->addInsert(self::LAST_UPDATED, $posted);
			$crit->addInsert(self::TITLE, $title);
			$crit->addInsert(self::PROJECT_ID, $p_id);
			$crit->addInsert(self::LONG_DESCRIPTION, $description);
			$crit->addInsert(self::ISSUE_TYPE, $issue_type);
			$crit->addInsert(self::POSTED_BY, BUGScontext::getUser()->getUID());
			$crit->addInsert(self::STATUS, $row->get(B2tProjects::DEFAULT_STATUS));
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return ($issue_id === null) ? $res->getInsertID() : $issue_id;
		}
		
		public function getByPrefixAndIssueNo($prefix, $issue_no)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(B2tProjects::PREFIX, $prefix);
			$crit->addWhere(self::ISSUE_NO, $issue_no);
			$row = $this->doSelectOne($crit, array(self::PROJECT_ID));
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
			$crit->addWhere(self::ASSIGNED_TYPE, BUGSidentifiableclass::TYPE_TEAM);
			$crit->addWhere(self::STATE, BUGSissue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}
				
		public function getOpenIssuesByUserAssigned($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ASSIGNED_TO, $user_id);
			$crit->addWhere(self::ASSIGNED_TYPE, BUGSidentifiableclass::TYPE_USER);
			$crit->addWhere(self::STATE, BUGSissue::STATE_OPEN);
			$crit->addWhere(self::DELETED, 0);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function getRecentByProjectIDandIssueType($project_id, $issuetypes, $limit = 5)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(B2DB::getTable('B2tIssueTypes'), B2tIssueTypes::ID, self::ISSUE_TYPE);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(B2tIssueTypes::ICON, $issuetypes, B2DBCriteria::DB_IN);
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
			$crit->addSelectionColumn(self::ESTIMATED_POINTS, 'points', B2DBCriteria::DB_SUM);
			$crit->addWhere(self::MILESTONE, $milestone_id);
			if ($res = $this->doSelectOne($crit))
			{
				return $res->get('points');
			}
			else
			{
				return 0;
			}
		}
				
		
	}
