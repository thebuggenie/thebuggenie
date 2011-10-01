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
	 */
	class TBGMilestone extends TBGIdentifiableClass  
	{

		const TYPE_REGULAR = 1;
		const TYPE_SCRUMSPRINT = 2;

		static protected $_b2dbtablename = 'TBGMilestonesTable';
		
		/**
		 * This components project
		 *
		 * @var TBGProject
		 * @Class TBGProject
		 */
		protected $_project;

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
		 */
		protected $_startingdate;
		
		/**
		 * The milestone description
		 * 
		 * @var string
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

		/**
		 * Get milestones + sprints by a project id
		 *
		 * @param integer $project_id The project id
		 *
		 * @return array
		 */
		public static function getAllByProjectID($project_id)
		{
			$milestones = array();
			if ($res = TBGMilestonesTable::getTable()->getAllByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$milestone = TBGContext::factory()->TBGMilestone($row->get(TBGMilestonesTable::ID), $row);
					$milestones[$milestone->getID()] = $milestone;
				}
			}
			return $milestones;
		}

		/**
		 * Get regular milestones by a project id
		 *
		 * @param integer $project_id The project id
		 *
		 * @return array
		 */
		public static function getMilestonesByProjectID($project_id)
		{
			$milestones = array();
			if ($res = TBGMilestonesTable::getTable()->getMilestonesByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$milestone = TBGContext::factory()->TBGMilestone($row->get(TBGMilestonesTable::ID), $row);
					$milestones[$milestone->getID()] = $milestone;
				}
			}
			return $milestones;
		}

		/**
		 * Get all sprints by a project id
		 *
		 * @param integer $project_id The project id
		 *
		 * @return array
		 */
		public static function getSprintsByProjectID($project_id)
		{
			$sprints = array();
			if ($res = TBGMilestonesTable::getTable()->getSprintsByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$sprint = TBGContext::factory()->TBGMilestone($row->get(TBGMilestonesTable::ID), $row);
					$sprints[$sprint->getID()] = $sprint;
				}
			}
			return $sprints;
		}

		protected function _construct(\b2db\Row $row, $foreign_key = null)
		{
			$this->_reached = ($this->_reacheddate > 0);
		}

		public function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseemilestone", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGMilestone::createNew', $this)->trigger();
			}
		}
		
		/**
		 * @see getName()
		 * @deprecated
		 */
		public function __toString() // required for few functions such in_array()
		{
			// magic methods cannot throw exception
			//throw new Exception("Don't print the object, use the getName() function instead");
			return $this->getName();
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
		 * Set the milestone name
		 * 
		 * @param string $name The new name
		 */
		public function setName($name)
		{
			$this->_name = $name;
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
			return $this->_getPopulatedObjectFromProperty('_project');
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
		 * Return this milestones scheduled status, as an array
		 * 		array('color' => '#code', 'status' => 'description')
		 * 
		 * @return array
		 */
		public function getScheduledStatus()
		{
			if ($this->_isscheduled)
			{
				if ($this->_reached == false)
				{
					if ($this->_scheduleddate < NOW)
					{
						for ($dcc = 1;$dcc <= 7;$dcc++)
						{
							if ($this->_scheduleddate > mktime(0, 0, 0, date('m'), date('d') - $dcc, date('Y')))
							{
								if ($dcc - 1 == 0)
								{
									return array('color' => 'D55', 'status' => 'This milestone is about a day late');
								}
								else
								{
									return array('color' => 'D55', 'status' => 'This milestone is ' . ($dcc - 1) . ' day(s) late');
								}
							}
						}
						for ($dcc = 1;$dcc <= 4;$dcc++)
						{
							if ($this->_scheduleddate > mktime(0, 0, 0, date('m'), date('d') - ($dcc * 7), date('Y')))
							{
								return array('color' => 'D55', 'status' => 'This milestone is about ' . $dcc . ' week(s) late');
							}
						}
						for ($dcc = 1;$dcc <= 12;$dcc++)
						{
							if ($this->_scheduleddate > mktime(0, 0, 0, date('m') - $dcc, date('d'), date('Y')))
							{
								return array('color' => 'D55', 'status' => 'This milestone is about ' . $dcc . ' month(s) late');
							}
						}
						return array('color' => 'D55', 'status' => 'This milestone is more than a year late');
					}
					else
					{
						for ($dcc = 0;$dcc <= 7;$dcc++)
						{
							if ($this->_scheduleddate < mktime(0, 0, 0, date('m'), date('d') + $dcc, date('Y')))
							{
								if ($dcc - 2 == 0)
								{
									return array('color' => '000', 'status' => 'This milestone is due today');
								}
								else
								{
									return array('color' => '000', 'status' => 'This milestone is scheduled for ' . ($dcc - 2) . ' days from today');
								}
							}
						}
						for ($dcc = 1;$dcc <= 4;$dcc++)
						{
							if ($this->_scheduleddate < mktime(0, 0, 0, date('m'), date('d') + ($dcc * 7), date('Y')))
							{
								return array('color' => '000', 'status' => 'This milestone is scheduled for ' . $dcc . ' week(s) from today');
							}
						}
						for ($dcc = 1;$dcc <= 12;$dcc++)
						{
							if ($this->_scheduleddate < mktime(0, 0, 0, date('m') + $dcc, date('d'), date('Y')))
							{
								return array('color' => '000', 'status' => 'This milestone is scheduled for ' . $dcc . ' month(s) from today');
							}
						}
						return array('color' => '000', 'status' => 'This milestone is scheduled for more than a year from today');
					}
				}
				elseif ($this->_reacheddate <= $this->_scheduleddate)
				{
					return array('color' => '3A3', 'status' => '<b>Reached: </b> ' . tbg_formatTime($this->_reacheddate, 6));
				}
				else
				{
					$ret_text = '<b>Reached: </b> ' . tbg_formatTime($this->_reacheddate, 6) . ', ';
					for ($dcc = 1;$dcc <= 7;$dcc++)
					{
						if ($this->_reacheddate < ($this->_scheduleddate + (86400 * $dcc)))
						{
							$ret_text .= '<b>' . ($dcc - 1) . ' day(s) late</b>';
							return array('color' => 'C33', 'status' => $ret_text);
						}
					}
					for ($dcc = 1;$dcc <= 4;$dcc++)
					{
						if ($this->_reacheddate < ($this->_scheduleddate + (604800 * $dcc)))
						{
							$ret_text .= '<b>about ' . ($dcc - 1) . ' week(s) late</b>';
							return array('color' => 'C33', 'status' => $ret_text);
						}
					}
					for ($dcc = 1;$dcc <= 12;$dcc++)
					{
						if ($this->_reacheddate < ($this->_scheduleddate + (2592000 * $dcc)))
						{
							$ret_text .= '<b>about ' . ($dcc - 1) . ' month(s) late</b>';
							return array('color' => 'C33', 'status' => $ret_text);
						}
					}
					$ret_text .= '<b>more than a year late</b>';
					return array('color' => 'C33', 'status' => $ret_text);
				}
			}
			else
			{
				return array('color' => '000', 'status' => '');
			}
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
			$this->_populateIssues();
			if (($this->countClosedIssues() == $this->countIssues()) && !$this->isSprint())
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
		public function _preDelete()
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
				$child_issues = array();
				foreach ($this->getIssues() as $issue)
				{
					foreach ($issue->getChildIssues() as $child_issue)
					{
						$child_issues[] = (int) $child_issue->getID();
					}
				}
				
				$estimations = \b2db\Core::getTable('TBGIssueEstimates')->getEstimatesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $child_issues);
				$spent_times = \b2db\Core::getTable('TBGIssueSpentTimes')->getSpentTimesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $child_issues);

				$burndowndata = array();
				//var_dump($spent_times);var_dump($estimations);die();
				/*foreach ($estimations as $key => $sum)
				{
					if ($estimations[$key] !== null)
					{
						$burndowndata[$key] = $estimations[$key] - $spent_times[$key];
					}
					else
					{
						$burndowndata[$key] = '';
					}
				}*/

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

	}
