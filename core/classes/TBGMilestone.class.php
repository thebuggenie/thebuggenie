<?php

	/**
	 * Milestone class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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

		/**
		 * This components project
		 *
		 * @var TBGProject
		 */
		protected $_project;

		/**
		 * Whether the milestone has been reached
		 * 
		 * @var boolean
		 */
		protected $_isreached;
		
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
		 * Estimated points total
		 *
		 * @var integer
		 */
		protected $_points;

		/**
		 * Estimated hours total
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
			if ($res = B2DB::getTable('TBGMilestonesTable')->getAllByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$milestone = TBGFactory::TBGMilestoneLab($row->get(TBGMilestonesTable::ID), $row);
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
			if ($res = B2DB::getTable('TBGMilestonesTable')->getMilestonesByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$milestone = TBGFactory::TBGMilestoneLab($row->get(TBGMilestonesTable::ID), $row);
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
			if ($res = B2DB::getTable('TBGMilestonesTable')->getSprintsByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$sprint = TBGFactory::TBGMilestoneLab($row->get(TBGMilestonesTable::ID), $row);
					$sprints[$sprint->getID()] = $sprint;
				}
			}
			return $sprints;
		}

		/**
		 * Create a new milestone and return it
		 * 
		 * @param string $name The milestone name
		 * @param integer $project_id The project id
		 * 
		 * @return TBGMilestone
		 */
		public static function createNew($name, $type, $project_id)
		{
			$m_id = B2DB::getTable('TBGMilestonesTable')->createNew($name, $type, $project_id);
			TBGContext::setPermission('b2milestoneaccess', $m_id, 'core', 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
			return TBGFactory::TBGMilestoneLab($m_id);
		}
		
		/**
		 * Constructor function
		 *
		 * @param integer $b_id The milestone id
		 * @param B2DBrow $row[optional] a database row with the necessary information if available
		 */
		public function __construct($m_id, $row = null)
		{
			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$row = B2DB::getTable('TBGMilestonesTable')->doSelectById($m_id, $crit);
			}
			
			if ($row instanceof B2DBRow)
			{
				$this->_name = $row->get(TBGMilestonesTable::NAME);
				$this->_itemid = $row->get(TBGMilestonesTable::ID);
				$this->_itemtype = $row->get(TBGMilestonesTable::MILESTONE_TYPE);
				$this->_isvisible = (bool) $row->get(TBGMilestonesTable::VISIBLE);
				$this->_isscheduled = (bool) $row->get(TBGMilestonesTable::SCHEDULED);
				$this->_isreached = (bool) $row->get(TBGMilestonesTable::REACHED);
				$this->_scheduleddate = $row->get(TBGMilestonesTable::SCHEDULED);
				$this->_isstarting = (bool) $row->get(TBGMilestonesTable::STARTING);
				$this->_startingdate = $row->get(TBGMilestonesTable::STARTING);
				$this->_reacheddate = $row->get(TBGMilestonesTable::REACHED);
				$this->_description = $row->get(TBGMilestonesTable::DESCRIPTION);
				$this->_project = $row->get(TBGMilestonesTable::PROJECT);
			}
			else
			{
				throw new Exception('This milestone does not exist');
			}
		}

		/**
		 * @see getName()
		 * @deprecated
		 */
		public function __toString()
		{
			throw new Exception("Don't print the object, use the getName() function instead");
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
		
		protected function _populatePoints()
		{
			if ($this->_points === null)
			{
				$this->_points = array();
				list($this->_points['estimated'], $this->_points['spent']) = B2DB::getTable('TBGIssuesTable')->getTotalPointsByMilestoneID($this->getID());
			}
		}
		
		/**
		 * Get total estimated points for issues assigned to this milestone
		 *  
		 * @return integer
		 */
		public function getPointsEstimated()
		{
			$this->_populatePoints();
			return (int) $this->_points['estimated'];
		}

		/**
		 * Get total spent points for issues assigned to this milestone
		 *  
		 * @return integer
		 */
		public function getPointsSpent()
		{
			$this->_populatePoints();
			return (int) $this->_points['spent'];
		}

		protected function _populateHours()
		{
			if ($this->_hours === null)
			{
				$this->_hours = array();
				list($this->_hours['estimated'], $this->_hours['spent']) = B2DB::getTable('TBGIssuesTable')->getTotalHoursByMilestoneID($this->getID());
			}
		}

		/**
		 * Get total estimated hours for issues assigned to this milestone
		 *
		 * @return integer
		 */
		public function getHoursEstimated()
		{
			$this->_populateHours();
			return (int) $this->_hours['estimated'];
		}

		public function clearEstimates()
		{
			$this->_hours = null;
			$this->_points = null;
		}

		/**
		 * Get total spent hours for issues assigned to this milestone
		 *
		 * @return integer
		 */
		public function getHoursSpent()
		{
			$this->_populateHours();
			return (int) $this->_hours['spent'];
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
				if ($res = B2DB::getTable('TBGIssuesTable')->getByMilestone($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$theIssue = TBGFactory::TBGIssueLab($row->get(TBGIssuesTable::ID));
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
			return $this->_isscheduled;
		}
		
		/**
		 * Set whether or not the milestone is scheduled for release
		 * 
		 * @param boolean $scheduled[optional] scheduled or not (default true)
		 */
		public function setScheduled($scheduled = true)
		{
			$this->_isscheduled = $scheduled;
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
		 * Return this milestones project
		 * 
		 * @return TBGProject
		 */
		public function getProject()
		{
			if (is_numeric($this->_project))
			{
				try
				{
					$this->_project = TBGFactory::projectLab($this->_project);
				}
				catch (Exception $e)
				{
					$this->_project = null;
				}
			}
			return $this->_project;
		}
		
		/**
		 * Whether this milestone has been reached or not
		 * 
		 * @return boolean
		 */
		public function isReached()
		{
			return $this->_isreached;
		}
		
		/**
		 * Whether or not this milestone is overdue
		 * 
		 * @return boolean
		 */
		public function isOverdue()
		{
			return ($this->getScheduledDate() < time() && !$this->isReached()) ? true : false;
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
			if ($date == 0)
			{
				$this->setScheduled(false);
			}
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
		 * Return whether or not this milestone is visible
		 * 
		 * @return boolean
		 */
		public function isVisible()
		{
			return $this->_isvisible;
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
				if ($this->_isreached == false)
				{
					if ($this->_scheduleddate < $_SERVER["REQUEST_TIME"])
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
			if ($this->_issues == null)
			{
				$this->_populateIssues();
			}
			if ($this->_closed_issues == count($this->_issues) && !$this->isSprint())
			{
				B2DB::getTable('TBGMilestonesTable')->setReached($this->getID());
			}
		}
		
		/**
		 * Save changes made to the milestone
		 */
		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGMilestonesTable::NAME, $this->_name);
			$crit->addUpdate(TBGMilestonesTable::MILESTONE_TYPE, $this->_itemtype);
			$crit->addUpdate(TBGMilestonesTable::DESCRIPTION, $this->_description);
			$crit->addUpdate(TBGMilestonesTable::STARTING, $this->_startingdate);
			if ($this->_isscheduled)
			{
				$crit->addUpdate(TBGMilestonesTable::SCHEDULED, $this->_scheduleddate);
			}
			else
			{
				$crit->addUpdate(TBGMilestonesTable::SCHEDULED, 0);
				$this->_scheduleddate = 0;
			}
			if ($this->_isstarting)
			{
				$crit->addUpdate(TBGMilestonesTable::STARTING, $this->_startingdate);
			}
			else
			{
				$crit->addUpdate(TBGMilestonesTable::STARTING, 0);
				$this->_startingdate = 0;
			}
			$res = B2DB::getTable('TBGMilestonesTable')->doUpdateById($crit, $this->getID());
		}

		/**
		 * Delete this milestone
		 */
		public function delete()
		{
			B2DB::getTable('TBGMilestonesTable')->doDeleteById($this->getID());
			B2DB::getTable('TBGIssuesTable')->clearMilestone($this->getID());
		}
		
		/**
		 * Whether or not the current user has access to this milestone
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return TBGContext::getUser()->hasPermission("b2milestoneaccess", $this->getID(), "core");			
		}

		/**
		 * Whether or not the milestone is ongoing
		 *
		 * @return boolean
		 */
		public function isCurrent()
		{
			if (!$this->getStartingDate() || !$this->isScheduled()) return false;
			if ($this->getStartingDate() <= time() && $this->getScheduledDate() >= time()) return true;
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
		 * Whether or not this milestone has starting date set
		 *
		 * @return boolean
		 */
		public function isStarting()
		{
			return $this->_isstarting;
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
				
				//var_dump($child_issues);die();
				
				$estimations = B2DB::getTable('TBGIssueEstimates')->getEstimatesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $child_issues);
				$spent_times = B2DB::getTable('TBGIssueSpentTimes')->getSpentTimesByDateAndIssueIDs($this->getStartingDate(), $this->getScheduledDate(), $child_issues);

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

	}
	