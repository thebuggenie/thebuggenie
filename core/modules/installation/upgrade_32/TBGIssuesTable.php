<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Issues table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.2
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
     * @Table(name='issues_32')
     */
    class TBGIssuesTable extends ScopedTable
    {

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

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addInteger(self::ISSUE_NO, 10);
            parent::_addVarchar(self::TITLE, 255);
            parent::_addInteger(self::POSTED, 10);
            parent::_addInteger(self::LAST_UPDATED, 10);
            parent::_addInteger(self::PROJECT_ID, 10);
            parent::_addText(self::DESCRIPTION, false);
            parent::_addBoolean(self::STATE);
            parent::_addInteger(self::POSTED_BY, 10);
            parent::_addFloat(self::USER_PAIN, 3);
            parent::_addInteger(self::PAIN_BUG_TYPE, 3);
            parent::_addInteger(self::PAIN_EFFECT, 3);
            parent::_addInteger(self::PAIN_LIKELIHOOD, 3);
            parent::_addText(self::REPRODUCTION_STEPS, false);
            parent::_addInteger(self::RESOLUTION, 10);
            parent::_addInteger(self::ISSUE_TYPE, 10);
            parent::_addInteger(self::STATUS, 10);
            parent::_addInteger(self::PRIORITY, 10);
            parent::_addInteger(self::CATEGORY, 10);
            parent::_addInteger(self::SEVERITY, 10);
            parent::_addInteger(self::REPRODUCABILITY, 10);
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
            parent::_addInteger(self::DUPLICATE_OF, 10);
            parent::_addBoolean(self::DELETED);
            parent::_addBoolean(self::BLOCKING);
            parent::_addBoolean(self::LOCKED);
            parent::_addInteger(self::BEING_WORKED_ON_BY_USER, 10);
            parent::_addInteger(self::BEING_WORKED_ON_BY_USER_SINCE, 10);
            parent::_addInteger(self::MILESTONE, 10);
            parent::_addInteger(self::WORKFLOW_STEP_ID, 10);
            parent::_addInteger(self::SCOPE, 10);
            parent::_addInteger(self::OWNER_USER, 10);
            parent::_addInteger(self::OWNER_TEAM, 10);
            parent::_addInteger(self::ASSIGNEE_TEAM, 10);
            parent::_addInteger(self::ASSIGNEE_USER, 10);
        }

    }
