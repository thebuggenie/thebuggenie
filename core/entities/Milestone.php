<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Milestone class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Milestone class
     *
     * @package thebuggenie
     * @subpackage main
     *
         * @method \thebuggenie\core\entities\tables\Milestones getB2DBTable Returns an instance of the associated table object
         *
     * @Table(name="\thebuggenie\core\entities\tables\Milestones")
     */
    class Milestone extends IdentifiableScoped
    {

        const TYPE_REGULAR = 1;
        const TYPE_SCRUMSPRINT = 2;

        /**
         * This milestone's project
         *
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_itemtype;

        /**
         * Whether the milestone has been reached
         *
         * @var boolean
         */
        protected $_reached;

        /**
         * Whether the milestone has been closed
         *
         * @var boolean
         * @Column(type="boolean", default=false)
         */
        protected $_closed;

        /**
         * When the milestone was reached
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_reacheddate;

        /**
         * Whether the milestone has been scheduled for release
         *
         * @var boolean
         */
        protected $_isscheduled;

        /**
         * When the milestone is scheduled for release
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_scheduleddate;

        /**
         * Whether the milestone has been scheduled for start
         *
         * @var boolean
         */
        protected $_isstarting;

        /**
         * When the milestone is scheduled to start
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_startingdate;

        /**
         * Whether the milestone is visible on the project roadmap
         *
         * @var boolean
         * @Column(type="boolean", default=true)
         */
        protected $_visible_roadmap = true;

        /**
         * Whether the milestone is available for issues
         *
         * @var boolean
         * @Column(type="boolean", default=true)
         */
        protected $_visible_issues = true;

        /**
         * The milestone description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description;

        /**
         * Internal cache of issues assigned
         *
         * @var array|Issue
         */
        protected $_issues = null;

        /**
         * Number of closed issues
         *
         * @var integer
         */
        protected $_closed_issues;

        /**
         * Points spent or estimated
         *
         * @var array
         */
        protected $_points;

        /**
         * Hours spent or estimated
         *
         * @var array
         */
        protected $_hours;

        /**
         * Calculated burndown data
         *
         * @var array
         */
        protected $_burndowndata;

        /**
         * Sort order of this item
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order = null;

        protected function _construct(\b2db\Row $row, $foreign_key = null)
        {
            $this->_reached = ($this->_reacheddate > 0);
        }

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                framework\Context::setPermission("canseemilestone", $this->getID(), "core", 0, framework\Context::getUser()->getGroup()->getID(), 0, true);
                \thebuggenie\core\framework\Event::createNew('core', 'Milestone::_postSave', $this)->trigger();
            }
        }

        public static function doesIDExist($id)
        {
            return (bool) static::getB2DBTable()->doesIDExist($id);
        }

        /**
         * Returns an array with issues
         *
         * @return array|Issue
         */
        public function getIssues()
        {
            $this->_populateIssues();
            return $this->_issues;
        }

        /**
         * Returns an array with issues
         *
         * @return array
         */
        public function getNonChildIssues($epic_issuetype_id)
        {
            $issues = $this->getIssues();
            foreach ($issues as $id => $issue)
            {
                if ($issue->isChildIssue())
                {
                    foreach ($issue->getParentIssues() as $parent)
                    {
                        if ($parent->getIssuetype()->getID() != $epic_issuetype_id) unset($issues[$id]);
                    }
                }
                elseif ($issue->getIssuetype()->getID() == $epic_issuetype_id)
                {
                    unset($issues[$id]);
                }
            }

            return $issues;
        }

        protected function _populatePointsAndTime()
        {
            if ($this->_points === null)
            {
                $this->_points = array('estimated' => 0, 'spent' => 0);
                $this->_hours = array('estimated' => 0, 'spent' => 0);

                if ($res = tables\Issues::getTable()->getPointsAndTimeByMilestone($this->getID()))
                {
                    while ($row = $res->getNextRow())
                    {
                        $points = ($res->get('state') == Issue::STATE_CLOSED && $res->get('estimated_points') > $res->get('spent_points')) ? $res->get('estimated_points') : $res->get('spent_points');
                        $hours = ($res->get('state') == Issue::STATE_CLOSED && $res->get('estimated_hours') > $res->get('spent_hours')) ? $res->get('estimated_hours') : $res->get('spent_hours');
                        $this->_points['estimated'] += $res->get('estimated_points');
                        $this->_points['spent'] += $points;
                        $this->_hours['estimated'] += $res->get('estimated_hours');
                        $this->_hours['spent'] += round($hours / 100, 2);
                    }
                }
            }
        }

        /**
         * Get total estimated points for issues assigned to this milestone
         *
         * @return integer
         */
        public function getPointsEstimated()
        {
            $this->_populatePointsAndTime();
            return (int) $this->_points['estimated'];
        }

        /**
         * Get total spent points for issues assigned to this milestone
         *
         * @return integer
         */
        public function getPointsSpent()
        {
            $this->_populatePointsAndTime();
            return (int) $this->_points['spent'];
        }

        /**
         * Get total estimated hours for issues assigned to this milestone
         *
         * @return integer
         */
        public function getHoursEstimated()
        {
            $this->_populatePointsAndTime();
            return (int) $this->_hours['estimated'];
        }

        /**
         * Get total spent hours for issues assigned to this milestone
         *
         * @return integer
         */
        public function getHoursSpent()
        {
            $this->_populatePointsAndTime();
            return (int) $this->_hours['spent'];
        }

        public function clearEstimates()
        {
            $this->_hours = null;
            $this->_points = null;
        }

        /**
         * Return number of issues assigned to this milestone
         *
         * @return integer
         */
        public function countIssues()
        {
            return $this->getProject()->countIssuesByMilestone($this->getID());
        }

        /**
         * Return number of open issues assigned to this milestone
         *
         * @return integer
         */
        public function countOpenIssues()
        {
            return $this->getProject()->countOpenIssuesByMilestone($this->getID());
        }

        /**
         * Return number of closed issues assigned to this milestone
         *
         * @return integer
         */
        public function countClosedIssues()
        {
            return $this->getProject()->countClosedIssuesByMilestone($this->getID());
        }

        /**
         * Populates the internal array with issues
         */
        protected function _populateIssues()
        {
            if ($this->_issues == null)
            {
                $this->_issues = array();
                if ($issues = tables\Issues::getTable()->getByMilestone($this->getID(), $this->getProject()->getID()))
                {
                    foreach ($issues as $issue)
                    {
                        $this->_issues[$issue->getID()] = $issue;
                        if ($issue->isClosed())
                        {
                            $this->_closed_issues++;
                        }
                    }
                }
            }
        }

        /**
         * Return the number of closed issues
         *
         * @return integer
         */
        public function getClosedIssues()
        {
            return $this->_closed_issues;
        }

        /**
         * Get the description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }

        public function hasDescription()
        {
            return (bool) ($this->getDescription() != '');
        }

        /**
         * Set the milestone description
         *
         * @param string $description The description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Whether or not the milestone has been scheduled for release
         *
         * @return boolean
         */
        public function isScheduled()
        {
            return ($this->getScheduledDate() > 0);
        }

        /**
         * Set the milestone type
         *
         * @param integer $type
         */
        public function setType($type)
        {
            $this->_itemtype = $type;
        }

        /**
         * Get the milestone type
         *
         * @return integer
         */
        public function getType()
        {
            return $this->_itemtype;
        }

        /**
         * Returns the parent project
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }

        public function setProject($project)
        {
            $this->_project = $project;
        }

        /**
         * Whether this milestone has been reached or not
         *
         * @return boolean
         */
        public function isReached()
        {
            return $this->_reached;
        }

        /**
         * Whether or not this milestone is overdue
         *
         * @return boolean
         */
        public function isOverdue()
        {
            return ($this->getScheduledDate() && $this->getScheduledDate() < NOW && !$this->isReached()) ? true : false;
        }

        /**
         * Return when this milestone was reached
         *
         * @return integer
         */
        public function getReachedDate()
        {
            return $this->_reacheddate;
        }

        /**
         * Set whether or not the milestone is scheduled for finishing
         *
         * @param boolean $scheduled [optional] scheduled or not (default true)
         */
        public function setScheduled($scheduled = true)
        {
            $this->_isscheduled = $scheduled;
        }

        /**
         * Return when this milestone is scheduled for release
         *
         * @return integer
         */
        public function getScheduledDate()
        {
            return $this->_scheduleddate;
        }

        /**
         * Set this milestones scheduled release date
         *
         * @param integer $date The timestamp for release
         */
        public function setScheduledDate($date)
        {
            $this->_scheduleddate = $date;
        }

        /**
         * Return the year the milestone is scheduled for release
         *
         * @return integer
         */
        public function getScheduledYear()
        {
            return date("Y", $this->_scheduleddate);
        }

        /**
         * Return the month the milestone is scheduled for release
         *
         * @return integer
         */
        public function getScheduledMonth()
        {
            return date("n", $this->_scheduleddate);
        }

        /**
         * Return the day the milestone is scheduled for release
         *
         * @return integer
         */
        public function getScheduledDay()
        {
            return date("j", $this->_scheduleddate);
        }

        /**
         * Return when this milestone is starting
         *
         * @return integer
         */
        public function getStartingDate()
        {
            return $this->_startingdate;
        }

        /**
         * Set this milestones starting date
         *
         * @param integer $date The timestamp for the starting date
         */
        public function setStartingDate($date)
        {
            $this->_startingdate = $date;
        }

        /**
         * Set whether or not the milestone is scheduled for start
         *
         * @param boolean $starting [optional] starting or not (default true)
         */
        public function setStarting($starting = true)
        {
            $this->_isstarting = $starting;
        }

        /**
         * Return the year the milestone is starting
         *
         * @return integer
         */
        public function getStartingYear()
        {
            return date("Y", $this->_startingdate);
        }

        /**
         * Return the month the milestone is starting
         *
         * @return integer
         */
        public function getStartingMonth()
        {
            return date("n", $this->_startingdate);
        }

        /**
         * Return the day the milestone is starting
         *
         * @return integer
         */
        public function getStartingDay()
        {
            return date("j", $this->_startingdate);
        }

        /**
         * Set this milestones reached date
         *
         * @param integer $date The timestamp for the reached date
         */
        public function setReachedDate($date)
        {
            $this->_reacheddate = $date;
        }

        /**
         * Set whether or not the milestone is scheduled for start
         *
         * @param boolean $reached [optional] reached or not (default true)
         */
        public function setReached($reached = true)
        {
            $this->_reached = $reached;
        }

        /**
         * Return the year the milestone is reached
         *
         * @return integer
         */
        public function getReachedYear()
        {
            return date("Y", $this->_reacheddate);
        }

        /**
         * Return the month the milestone is reached
         *
         * @return integer
         */
        public function getReachedMonth()
        {
            return date("n", $this->_reacheddate);
        }

        /**
         * Return the day the milestone is reached
         *
         * @return integer
         */
        public function getReachedDay()
        {
            return date("j", $this->_reacheddate);
        }

        /**
         * Returns the milestones progress
         *
         * @return integer
         */
        public function getPercentComplete()
        {
            if ($this->getType() == self::TYPE_REGULAR)
            {
                return $this->getProject()->getClosedPercentageByMilestone($this->getID());
            }
            else
            {
                if ($this->getPointsEstimated() > 0)
                {
                    $multiplier = 100 / $this->getPointsEstimated();
                    $pct = $this->getPointsSpent() * $multiplier;
                }
                else
                {
                    $pct = 0;
                }
            }
            return (int) $pct;
        }

        /**
         * Figure out this milestones status
         */
        public function updateStatus()
        {
            $all_issues_closed = (bool) ($this->countClosedIssues() == $this->countIssues());
            if (!$this->hasReachedDate() && $all_issues_closed)
            {
                $this->_reacheddate = NOW;
            }
            elseif ($this->hasReachedDate() && !$all_issues_closed)
            {
                $this->_reacheddate = null;
            }
        }

        /**
         * Delete this milestone
         */
        protected function _preDelete()
        {
            tables\Issues::getTable()->clearMilestone($this->getID());
        }

        /**
         * Whether or not the current user can access the milestones
         *
         * @return boolean
         */
        public function hasAccess()
        {
            return ($this->getProject()->canSeeAllMilestones() || framework\Context::getUser()->hasPermission('canseemilestone', $this->getID()));
        }

        /**
         * Whether or not the milestone is ongoing
         *
         * @return boolean
         */
        public function isCurrent()
        {
            if (!$this->isScheduled()) return false;
            if (($this->isStarting() && $this->getStartingDate() <= NOW) && $this->getScheduledDate() >= NOW) return true;
            if (!$this->isStarting() && $this->isScheduled() && $this->getScheduledDate() >= NOW) return true;
            return $this->isOverdue();
        }

        /**
         * Whether or not this milestone has starting date set
         *
         * @return boolean
         */
        public function hasStartingDate()
        {
            return (bool) $this->getStartingDate();
        }

        /**
         * Whether or not this milestone has starting date set
         *
         * @return boolean
         */
        public function hasScheduledDate()
        {
            return (bool) $this->getScheduledDate();
        }

        /**
         * Whether or not this milestone has reached date set
         *
         * @return boolean
         */
        public function hasReachedDate()
        {
            return (bool) $this->getReachedDate();
        }

        /**
         * Whether or not this milestone has starting date set
         *
         * @return boolean
         */
        public function isStarting()
        {
            return ($this->getStartingDate() > 0);
        }

        protected function _populateBurndownData()
        {
            if ($this->_burndowndata === null)
            {
                $this->_burndowndata = array();
                $issues = array();
                foreach ($this->getIssues() as $issue)
                {
                    $issues[] = (int) $issue->getID();
                    foreach ($issue->getChildIssues() as $child_issue)
                    {
                        $issues[] = (int) $child_issue->getID();
                    }
                }

                $estimations = tables\IssueEstimates::getTable()->getEstimatesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $issues);
                $spent_times = tables\IssueSpentTimes::getTable()->getSpentTimesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $issues);

                $burndown = array();
                foreach ($spent_times['hours'] as $key => $val)
                {
                    $spent_times['hours'][$key] = round($spent_times['hours'][$key] / 100, 2);
                }
                foreach ($estimations['hours'] as $key => $val)
                {
                    $burndown['hours'][$key] = (array_key_exists($key, $spent_times['hours'])) ? $val - $spent_times['hours'][$key] : $val;
                }
                foreach ($estimations['points'] as $key => $val)
                {
                    $burndown['points'][$key] = (array_key_exists($key, $spent_times['points'])) ? $val - $spent_times['points'][$key] : $val;
                }

                $this->_burndowndata = array('estimations' => $estimations, 'spent_times' => $spent_times, 'burndown' => $burndown);
            }
        }

        public function getBurndownData()
        {
            $this->_populateBurndownData();
            return $this->_burndowndata;
        }

        public function isSprint()
        {
            return (bool) ($this->_itemtype == self::TYPE_SCRUMSPRINT);
        }

        public function getDateString()
        {
            framework\Context::loadLibrary('common');
            $i18n = framework\Context::getI18n();
            if ($this->hasStartingDate() && $this->hasScheduledDate())
            {
                if ($this->getStartingDate() < NOW && $this->getScheduledDate() < NOW)
                {
                    return $i18n->__('%milestone_name (started %start_date - ended %end_date)', array('%milestone_name' => '', '%start_date' => tbg_formatTime($this->getStartingDate(), 23, true, true), '%end_date' => tbg_formatTime($this->getScheduledDate(), 23, true, true)));
                }
                elseif ($this->getStartingDate() < NOW && $this->getScheduledDate() > NOW)
                {
                    return $i18n->__('%milestone_name (started %start_date - ends %end_date)', array('%milestone_name' => '', '%start_date' => tbg_formatTime($this->getStartingDate(), 23, true, true), '%end_date' => tbg_formatTime($this->getScheduledDate(), 23, true, true)));
                }
                elseif ($this->getStartingDate() > NOW)
                {
                    return $i18n->__('%milestone_name (starts %start_date - ended %end_date)', array('%milestone_name' => '', '%start_date' => tbg_formatTime($this->getStartingDate(), 23, true, true), '%end_date' => tbg_formatTime($this->getScheduledDate(), 23, true, true)));
                }
            }
            elseif ($this->hasStartingDate())
            {
                if ($this->getStartingDate() < NOW)
                {
                    return $i18n->__('%milestone_name (started %start_date)', array('%milestone_name' => '', '%start_date' => tbg_formatTime($this->getStartingDate(), 23, true, true)));
                }
                else
                {
                    return $i18n->__('%milestone_name (starts %start_date)', array('%milestone_name' => '', '%start_date' => tbg_formatTime($this->getStartingDate(), 23, true, true)));
                }
            }
            elseif ($this->hasScheduledDate())
            {
                if ($this->getScheduledDate() < NOW)
                {
                    return $i18n->__('%milestone_name (released: %date)', array('%milestone_name' => '', '%date' => tbg_formatTime($this->getScheduledDate(), 23, true, true)));
                }
                else
                {
                    return $i18n->__('%milestone_name (will be released: %date)', array('%milestone_name' => '', '%date' => tbg_formatTime($this->getScheduledDate(), 23, true, true)));
                }
            }
            elseif ($this->hasReachedDate())
            {
                return $i18n->__('%milestone_name (reached: %date)', array('%milestone_name' => '', '%date' => tbg_formatTime($this->getReachedDate(), 23, true, true)));
            }

            return $i18n->__('Not scheduled');
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the milestone name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        public function setVisibleRoadmap($visible = true)
        {
            $this->_visible_roadmap = $visible;
        }

        public function getVisibleRoadmap()
        {
            return $this->_visible_roadmap;
        }

        public function isVisibleRoadmap()
        {
            return $this->getVisibleRoadmap();
        }

        public function setVisibleIssues($visible = true)
        {
            $this->_visible_issues = $visible;
        }

        public function getVisibleIssues()
        {
            return $this->_visible_issues;
        }

        public function isVisibleIssues()
        {
            return $this->getVisibleIssues();
        }

        public function getClosed()
        {
            return $this->_closed;
        }

        public function isClosed()
        {
            return $this->getClosed();
        }

        public function setClosed($closed)
        {
            $this->_closed = $closed;
        }

        public function setOrder($order)
        {
            $this->_sort_order = $order;
        }

        public function getOrder()
        {
            return (int) $this->_sort_order;
        }

    }
