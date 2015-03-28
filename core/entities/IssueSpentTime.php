<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Log item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Log item class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\IssueSpentTimes")
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

        public static function getSpentHoursValue($hours)
        {
            $hours = trim(str_replace(array(','), array('.'), $hours));
            $hours *= 100;

            return $hours;
        }

        protected function _preSave($is_new)
        {
            if ($is_new && $this->_edited_at == 0) $this->_edited_at = time();
        }

        protected function _postSave($is_new)
        {
            $this->_recalculateIssueTimes();
        }

        protected function _recalculateIssueTimes()
        {
            $times = tables\IssueSpentTimes::getTable()->getSpentTimeSumsByIssueId($this->getIssueID());
            $this->getIssue()->setSpentPoints($times['points']);
            $this->getIssue()->setSpentHours($times['hours']);
            $this->getIssue()->setSpentDays($times['days']);
            $this->getIssue()->setSpentWeeks($times['weeks']);
            $this->getIssue()->setSpentMonths($times['months']);
        }

        protected function _postDelete()
        {
            $this->_recalculateIssueTimes();
        }

        public function getUser()
        {
            return $this->_b2dbLazyload('_edited_by');
        }

        public function setUser($uid)
        {
            $this->_edited_by = $uid;
        }

        public function getActivityType()
        {
            return $this->_b2dbLazyload('_activity_type');
        }

        public function setActivityType($activity_type)
        {
            $this->_activity_type = $activity_type;
        }

        public function getActivityTypeID()
        {
            return ($this->getActivityType() instanceof \thebuggenie\core\entities\ActivityType) ? $this->getActivityType()->getID() : 0;
        }

        /**
         * @return \thebuggenie\core\entities\Issue the related issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyload('_issue_id');
        }

        public function getIssueID()
        {
            return (is_object($this->_issue_id)) ? $this->_issue_id->getID() : (int) $this->_issue_id;
        }

        public function setIssue($issue_id)
        {
            $this->_issue_id = $issue_id;
        }

        public function getEditedAt()
        {
            return $this->_edited_at;
        }

        public function setEditedAt($time)
        {
            $this->_edited_at = $time;
        }

        /**
         * Returns an array with the spent time
         *
         * @return array
         */
        public function getSpentTime()
        {
            return array('months' => (int) $this->_spent_months, 'weeks' => (int) $this->_spent_weeks, 'days' => (int) $this->_spent_days, 'hours' => round($this->_spent_hours / 100, 2), 'points' => (int) $this->_spent_points);
        }

        /**
         * Returns the spent months
         *
         * @return integer
         */
        public function getSpentMonths()
        {
            return (int) $this->_spent_months;
        }

        /**
         * Returns the spent weeks
         *
         * @return integer
         */
        public function getSpentWeeks()
        {
            return (int) $this->_spent_weeks;
        }

        /**
         * Returns the spent days
         *
         * @return integer
         */
        public function getSpentDays()
        {
            return (int) $this->_spent_days;
        }

        /**
         * Returns the spent hours
         *
         * @return integer
         */
        public function getSpentHours()
        {
            return (int) $this->_spent_hours;
        }

        /**
         * Returns the spent points
         *
         * @return integer
         */
        public function getSpentPoints()
        {
            return (int) $this->_spent_points;
        }

        /**
         * Returns an array with the spent time
         *
         * @see getSpentTime()
         *
         * @return array
         */
        public function getTimeSpent()
        {
            return $this->getSpentTime();
        }

        /**
         * Set spent months
         *
         * @param integer $months The number of months spent
         */
        public function setSpentMonths($months)
        {
            $this->_spent_months = $months;
        }

        /**
         * Set spent weeks
         *
         * @param integer $weeks The number of weeks spent
         */
        public function setSpentWeeks($weeks)
        {
            $this->_spent_weeks = $weeks;
        }

        /**
         * Set spent days
         *
         * @param integer $days The number of days spent
         */
        public function setSpentDays($days)
        {
            $this->_spent_days = $days;
        }

        /**
         * Set spent hours
         *
         * @param integer $hours The number of hours spent
         */
        public function setSpentHours($hours)
        {
            $this->_spent_hours = $hours;
        }

        /**
         * Set spent points
         *
         * @param integer $points The number of points spent
         */
        public function setSpentPoints($points)
        {
            $this->_spent_points = $points;
        }

        public function getComment()
        {
            return $this->_comment;
        }

        public function setComment($comment)
        {
            $this->_comment = $comment;
        }

    }
