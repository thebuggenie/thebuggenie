<?php

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
	 * @Table(name="issues")
	 */
	class TBGIssuesTable3dot1 extends TBGB2DBTable
	{
		
		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issues';
		const ID = 'issues.id';
		const SCOPE = 'issues.scope';
		const ISSUE_NO = 'issues.issue_no';
		const TITLE = 'issues.title';
		const POSTED = 'issues.posted';
		const LAST_UPDATED = 'issues.last_updated';
		const PROJECT_ID = 'issues.project_id';
		const DESCRIPTION = 'issues.description';
		const REPRODUCTION_STEPS = 'issues.reproduction_steps';
		const ISSUE_TYPE = 'issues.issuetype';
		const RESOLUTION = 'issues.resolution';
		const STATE = 'issues.state';
		const POSTED_BY = 'issues.posted_by';
		const OWNER = 'issues.owner';
		const OWNER_TYPE = 'issues.owner_type';
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

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addInteger(self::ISSUE_NO, 10);
			parent::_addVarchar(self::TITLE, 200);
			parent::_addInteger(self::POSTED, 10);
			parent::_addInteger(self::LAST_UPDATED, 10);
			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::STATE);
			parent::_addForeignKeyColumn(self::POSTED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addInteger(self::OWNER, 10);
			parent::_addInteger(self::OWNER_TYPE, 2);
			parent::_addFloat(self::USER_PAIN, 3);
			parent::_addInteger(self::PAIN_BUG_TYPE, 3);
			parent::_addInteger(self::PAIN_EFFECT, 3);
			parent::_addInteger(self::PAIN_LIKELIHOOD, 3);
			parent::_addInteger(self::ASSIGNED_TO, 10);
			parent::_addText(self::REPRODUCTION_STEPS, false);
			parent::_addForeignKeyColumn(self::RESOLUTION, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE_TYPE, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::PRIORITY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::CATEGORY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::SEVERITY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addForeignKeyColumn(self::REPRODUCABILITY, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addVarchar(self::SCRUMCOLOR, 7, '#FFFFFF');
			parent::_addInteger(self::ESTIMATED_MONTHS, 10);
			parent::_addInteger(self::ESTIMATED_WEEKS, 10);
			parent::_addInteger(self::ESTIMATED_DAYS, 10);
			parent::_addInteger(self::ESTIMATED_HOURS, 10);
			parent::_addInteger(self::ESTIMATED_POINTS);
			parent::_addInteger(self::SPENT_MONTHS, 10);
			parent::_addInteger(self::SPENT_WEEKS, 10);
			parent::_addInteger(self::SPENT_DAYS, 10);
			parent::_addInteger(self::SPENT_HOURS, 10);
			parent::_addInteger(self::VOTES_TOTAL, 10);
			parent::_addInteger(self::SPENT_POINTS);
			parent::_addInteger(self::PERCENT_COMPLETE, 2);
			parent::_addInteger(self::ASSIGNED_TYPE, 2);
			parent::_addInteger(self::DUPLICATE_OF, 10);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::BLOCKING);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::BEING_WORKED_ON_BY_USER, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addInteger(self::BEING_WORKED_ON_BY_USER_SINCE, 10);
			parent::_addForeignKeyColumn(self::MILESTONE, TBGMilestonesTable::getTable(), TBGMilestonesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
