<?php

    namespace thebuggenie\core\modules\installation\upgrade_415;

    use thebuggenie\core\entities\common\Changeable;

    /**
     * Issue class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @method boolean isTitleChanged() Whether the title is changed or not
     * @method boolean isSpentTimeChanged() Whether the spent_time is changed or not
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_415\IssuesTable")
     */
    class Issue extends Changeable
    {
        /**
         * @Column(type="string", name="name", length=255)
         */
        protected $_title;

        /**
         * @Column(type="string", name="shortname", length=255)
         */
        protected $_shortname;

        /**
         * The issue number
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_issue_no;

        /**
         * The issue type
         *
         * @var \thebuggenie\core\entities\Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issuetype")
         */
        protected $_issuetype;

        /**
         * The project which this issue affects
         *
         * @var \thebuggenie\core\entities\Project
         * @access protected
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project_id;

        /**
         * This issues long description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description;

        /**
         * The syntax used for this issue's long description
         *
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_description_syntax;

        /**
         * This issues reproduction steps
         *
         * @var string
         * @Column(type="text")
         */
        protected $_reproduction_steps;

        /**
         * The syntax used for this issue's reproduction steps
         *
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_reproduction_steps_syntax;

        protected $_reproduction_steps_parser = null;

        /**
         * When the issue was posted
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_posted;

        /**
         * When the issue was last updated
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_last_updated;

        /**
         * Who posted the issue
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_posted_by;

        /**
         * The project assignee if team
         *
         * @var \thebuggenie\core\entities\Team
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Team")
         */
        protected $_assignee_team;

        /**
         * The project assignee if user
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_assignee_user;

        /**
         * What kind of bug this is
         *
         * @var integer
         * @Column(type="integer", length=3)
         */
        protected $_pain_bug_type;

        /**
         * What effect this bug has on users
         *
         * @var integer
         * @Column(type="integer", length=3)
         */
        protected $_pain_effect;

        /**
         * How likely users are to experience this bug
         *
         * @var integer
         * @Column(type="integer", length=3)
         */
        protected $_pain_likelihood;

        /**
         * Calculated user pain score
         *
         * @var float
         * @Column(type="float")
         */
        protected $_user_pain = 0.00;

        /**
         * The resolution
         *
         * @var \thebuggenie\core\entities\Resolution
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Resolution")
         */
        protected $_resolution;

        /**
         * The issues' state (open or closed)
         *
         * @var integer
         * @Column(type="integer", length=2)
         */
        protected $_state = self::STATE_OPEN;

        /**
         * The category
         *
         * @var \thebuggenie\core\entities\Category
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Category")
         */
        protected $_category;

        /**
         * The status
         *
         * @var \thebuggenie\core\entities\Status
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Status")
         */
        protected $_status;

        /**
         * The prioroty
         *
         * @var \thebuggenie\core\entities\Priority
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Priority")
         */
        protected $_priority;

        /**
         * The reproducability
         *
         * @var \thebuggenie\core\entities\Reproducability
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Reproducability")
         */
        protected $_reproducability;

        /**
         * The severity
         *
         * @var \thebuggenie\core\entities\Severity
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Severity")
         */
        protected $_severity;

        /**
         * The scrum color
         *
         * @var string
         * @Column(type="string", length=7, default="#FFFFFF")
         */
        protected $_scrumcolor;

        /**
         * The estimated time (months) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_months;

        /**
         * The estimated time (weeks) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_weeks;

        /**
         * The estimated time (days) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_days;

        /**
         * The estimated time (hours) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_hours;

        /**
         * The estimated time (points) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_estimated_points;

        /**
         * The time spent (months) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_months;

        /**
         * The time spent (weeks) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_weeks;

        /**
         * The time spent (days) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_days;

        /**
         * The time spent (hours) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_hours;

        /**
         * The time spent (points) to fix this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_spent_points;

        /**
         * How far along the issus is
         *
         * @var integer
         * @Column(type="integer", length=2)
         */
        protected $_percent_complete;

        /**
         * Which user is currently working on this issue
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_being_worked_on_by_user;

        /**
         * When the last user started working on the issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_being_worked_on_by_user_since;

        /**
         * Whether the issue is deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = false;

        /**
         * Whether the issue is blocking the next release
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_blocking = false;

        /**
         * Sum of votes for this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_votes_total = null;

        /**
         * Milestone sorting order for this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_milestone_order = null;

        /**
         * The issue this issue is a duplicate of
         *
         * @var \thebuggenie\core\entities\Issue
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issue")
         */
        protected $_duplicate_of;

        /**
         * The milestone this issue is assigned to
         *
         * @var \thebuggenie\core\entities\Milestone
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Milestone")
         */
        protected $_milestone;

        /**
         * Whether the issue is locked for changes
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_locked;

        /**
         * Whether the issue is locked for changes to category
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_locked_category;

        /**
         * The issues current step in the associated workflow
         *
         * @var \thebuggenie\core\entities\WorkflowStep
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\WorkflowStep")
         */
        protected $_workflow_step_id;

    }
