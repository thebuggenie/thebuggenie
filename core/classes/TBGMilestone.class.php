<?php

	/**
	 * Milestone class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Milestone class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 *
	 * @Table(name="TBGMilestonesTable")
	 */
	class TBGMilestone extends TBGIdentifiableScopedClass
	{

		const TYPE_REGULAR = 1;
		const TYPE_SCRUMSPRINT = 2;

		/**
		 * This components project
		 *
		 * @var TBGProject
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGProject")
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
		 * The milestone description
		 * 
		 * @var string
		 * @Column(type="text")
		 */
		protected $_description;
		
		/**
		 * Internal cache of issues assigned
		 * 
		 * @var string
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
		 * @var integer
		 */
		protected $_points;

		/**
		 * Hours spent or estimated
		 *
		 * @var integer
		 */
		protected $_hours;

		/**
		 * Calculated burndown data
		 *
		 * @var array
		 */
		protected $_burndowndata;

		protected function _construct(\b2db\Row $row, $foreign_key = null)
		{
			$this->_reached = ($this->_reacheddate > 0);
		}

		protected function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseemilestone", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGMilestone::_postSave', $this)->trigger();
			}
		}
		
		/**
		 * Returns an array with issues
		 *
		 * @return array
		 */
		public function getIssues()
		{
			$this->_populateIssues();
			return $this->_issues;
		}
		
		protected function _populatePointsAndTime()
		{
			if ($this->_points === null)
			{
				$this->_points = array('estimated' => 0, 'spent' => 0);
				$this->_hours = array('estimated' => 0, 'spent' => 0);
				
				if ($res = TBGIssuesTable::getTable()->getPointsAndTimeByMilestone($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_points['estimated'] += $res->get('estimated_points');
						$this->_points['spent'] += $res->get('spent_points');
						$this->_hours['estimated'] += $res->get('estimated_hours');
						$this->_hours['spent'] += $res->get('spent_hours');
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
			return $this->getProject()->countIssuesByMilestone($this->getID(), $this->isSprint());
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
			return $this->getProject()->countClosedIssuesByMilestone($this->getID(), $this->isSprint());
		}
		
		/**
		 * Populates the internal array with issues
		 */
		protected function _populateIssues()
		{
			if ($this->_issues == null)
			{
				$this->_issues = array();
				if ($res = TBGIssuesTable::getTable()->getByMilestone($this->getID(), $this->getProject()->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$theIssue = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID));
						if ($this->isSprint() && $theIssue->getIssueType()->isTask()) continue;
						$this->_issues[$theIssue->getID()] = $theIssue;
						if ($theIssue->getState() == TBGIssue::STATE_CLOSED)
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
		 * @return TBGProject
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
			return ($this->getScheduledDate() && $this->getScheduledDate() < time() && !$this->isReached()) ? true : false;
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
		 * @param boolean $scheduled[optional] scheduled or not (default true)
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
		 * @param boolean $starting[optional] starting or not (default true)
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
			if ($this->countClosedIssues() == $this->countIssues())
			{
				TBGMilestonesTable::getTable()->setReached($this->getID());
				$this->_reacheddate = NOW;
			}
			elseif ($this->hasReachedDate())
			{
				TBGMilestonesTable::getTable()->clearReached($this->getID());
				$this->_reacheddate = null;
			}
		}
		
		/**
		 * Delete this milestone
		 */
		protected function _preDelete()
		{
			TBGIssuesTable::getTable()->clearMilestone($this->getID());
		}
		
		/**
		 * Whether or not the current user can access the milestones
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return ($this->getProject()->canSeeAllMilestones() || TBGContext::getUser()->hasPermission('canseemilestone', $this->getID()));
		}

		/**
		 * Whether or not the milestone is ongoing
		 *
		 * @return boolean
		 */
		public function isCurrent()
		{
			if (!$this->isScheduled()) return false;
			if ($this->getStartingDate() <= time() && $this->getScheduledDate() >= time()) return true;
			if (!$this->isStarting() && $this->isScheduled()) return true;
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
				
				$estimations = TBGIssueEstimates::getTable()->getEstimatesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $issues);
				$spent_times = TBGIssueSpentTimes::getTable()->getSpentTimesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $issues);

				$burndowndata = array();

				$this->_burndowndata = array('estimations' => $estimations, 'spent_times' => $spent_times);
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
			TBGContext::loadLibrary('common');
			$i18n = TBGContext::getI18n();
			if ($this->hasStartingDate() && $this->hasScheduledDate())
			{
				if ($this->getStartingDate() < time() && $this->getScheduledDate() < time())
				{
					return $i18n->__('%milestone_name% (started %start_date% - ended %end_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($this->getStartingDate(), 23), '%end_date%' => tbg_formatTime($this->getScheduledDate(), 23)));
				}
				elseif ($this->getStartingDate() < time() && $this->getScheduledDate() > time())
				{
					return $i18n->__('%milestone_name% (started %start_date% - ends %end_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($this->getStartingDate(), 23), '%end_date%' => tbg_formatTime($this->getScheduledDate(), 23)));
				}
				elseif ($this->getStartingDate() > time())
				{
					return $i18n->__('%milestone_name% (starts %start_date% - ended %end_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($this->getStartingDate(), 23), '%end_date%' => tbg_formatTime($this->getScheduledDate(), 23)));
				}
			}
			elseif ($this->hasStartingDate())
			{
				if ($this->getStartingDate() < time())
				{
					return $i18n->__('%milestone_name% (started %start_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($this->getStartingDate(), 23)));
				}
				else
				{
					return $i18n->__('%milestone_name% (starts %start_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($this->getStartingDate(), 23)));
				}
			}
			elseif ($this->hasScheduledDate())
			{
				if ($this->getScheduledDate() < time())
				{
					return $i18n->__('%milestone_name% (released: %date%)', array('%milestone_name%' => '', '%date%' => tbg_formatTime($this->getScheduledDate(), 23)));
				}
				else
				{
					return $i18n->__('%milestone_name% (will be released: %date%)', array('%milestone_name%' => '', '%date%' => tbg_formatTime($this->getScheduledDate(), 23)));
				}
			}
			elseif ($this->hasReachedDate())
			{
				return $i18n->__('%milestone_name% (reached: %date%)', array('%milestone_name%' => '', '%date%' => tbg_formatTime($this->getReachedDate(), 23)));
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
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

	}
