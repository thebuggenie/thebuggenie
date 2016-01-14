<?php

    namespace thebuggenie\core\modules\installation\upgrade_415;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Log item class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_415\IssueSpentTimesTable")
     */
    class IssueSpentTime extends IdentifiableScoped
    {

        /**
         * The issue time is logged against
         *
         * @var \thebuggenie\core\entities\Issue
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issue")
         */
        protected $_issue_id;

        /**
         * Who logged time
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_edited_by;

        /**
         * The type of activity time is logged for
         *
         * @var \thebuggenie\core\entities\ActivityType
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\ActivityType")
         */
        protected $_activity_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_edited_at;

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
         * @Column(type="text")
         */
        protected $_comment;

    }
