<?php

	/**
	 * Issue class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Issue class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class BUGSissue extends BUGSchangeableitem
	{
	
		/**
		 * Open issue state
		 * 
		 * @static integer
		 */
		const STATE_OPEN = 0;
		
		/**
		 * Closed issue state
		 * 
		 * @static integer
		 */
		const STATE_CLOSED = 1;
		
		/**
		 * Array of links attached to this issue
		 *
		 * @var array
		 */
		protected $_links = array();
	
		/**
		 * Array of files attached to this issue
		 *
		 * @var array
		 */
		protected $_files = array();
		
		/**
		 * The issue title
		 *
		 * @var string
		 */
		protected $_title;
		
		/**
		 * The issue number
		 *
		 * @var integer
		 */
		protected $_issue_no;
		
		/**
		 * The unique id of the issue, used in the database
		 *
		 * @var integer
		 */
		protected $_issue_uniqueid;
		
		/**
		 * The issue type
		 *
		 * @var BUGSdatatype
		 */
		protected $_issuetype;
		
		/**
		 * The project which this issue affects
		 *
		 * @var BUGSproject
		 * @access protected
		 */
		protected $_project;
		
		/**
		 * The affected editions for this issue
		 *
		 * @var array
		 */
		protected $_editions = null;
		
		/**
		 * The affected builds for this issue
		 * 
		 * @var array
		 */
		protected $_builds = null;
		
		/**
		 * The affected components for this issue
		 * 
		 * @var array
		 */
		protected $_components = null;

		/**
		 * This issues long description
		 * 
		 * @var string
		 */
		protected $_description;
		
		/**
		 * This issues reproduction steps
		 * 
		 * @var string
		 */
		protected $_reproduction_steps;
		
		/**
		 * When the issue was posted
		 * 
		 * @var integer
		 */
		protected $_posted;
		
		/**
		 * When the issue was last updated
		 * 
		 * @var integer
		 */
		protected $_last_updated;
		
		/**
		 * Who posted the issue
		 * 
		 * @var BUGSidentifiable
		 */
		protected $_postedby;
		
		/**
		 * Who owns the issue
		 * 
		 * @var BUGSidentifiable
		 */
		protected $_ownedby;
		
		/**
		 * Owner type
		 * 
		 * @var integer
		 */
		protected $_ownedtype;
		
		/**
		 * Whos assigned the issue
		 * 
		 * @var BUGSidentifiable
		 */
		protected $_assignedto;
		
		/**
		 * Assignee type
		 * 
		 * @var BUGSidentifiable
		 */
		protected $_assignedtype;
		
		/**
		 * The resolution
		 * 
		 * @var BUGSdatatype
		 */
		protected $_resolution;
		
		/**
		 * The issues' state (open or closed)
		 * 
		 * @var integer
		 */
		protected $_state;
		
		/**
		 * The category
		 * 
		 * @var BUGSdatatype
		 */
		protected $_category;
		
		/**
		 * The status
		 * 
		 * @var BUGSdatatype
		 */
		protected $_status;
		
		/**
		 * The prioroty
		 * 
		 * @var BUGSdatatype
		 */
		protected $_priority;
		
		/**
		 * The reproducability
		 * 
		 * @var BUGSdatatype
		 */
		protected $_reproducability;
		
		/**
		 * The severity
		 * 
		 * @var BUGSdatatype
		 */
		protected $_severity;

		/**
		 * The scrum color
		 *
		 * @var string
		 */
		protected $_scrumcolor;

		/**
		 * The estimated time (months) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedmonths;

		/**
		 * The estimated time (weeks) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedweeks;

		/**
		 * The estimated time (days) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimateddays;

		/**
		 * The estimated time (hours) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedhours;

		/**
		 * The estimated time (points) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedpoints;

		/**
		 * The time spent (months) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentmonths;

		/**
		 * The time spent (weeks) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentweeks;

		/**
		 * The time spent (days) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentdays;

		/**
		 * The time spent (hours) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spenthours;

		/**
		 * The time spent (points) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentpoints;
		
		/**
		 * How far along the issus is
		 * 
		 * @var integer
		 */
		protected $_percentcompleted;
		
		/**
		 * Which user is currently working on this issue
		 * 
		 * @var BUGSuser
		 */
		protected $_being_worked_on_by;
		
		/**
		 * When the last user started working on the issue
		 * 
		 * @var integer
		 */
		protected $_being_worked_on_since;
		
		/**
		 * List of tasks for this issue
		 * 
		 * @var array
		 */
		protected $_tasks;

		/**
		 * List of tags for this issue
		 *
		 * @var array
		 */
		protected $_tags;

		/**
		 * List of related users
		 * 
		 * @var array
		 */
		protected $_related_users;
		
		/**
		 * Whether the issue is deleted
		 * 
		 * @var boolean
		 */
		protected $_deleted;
		
		/**
		 * Whether the issue is blocking the next release
		 * 
		 * @var boolean
		 */
		protected $_blocking = false;

		/**
		 * The number of votes for this issue
		 * 
		 * @var integer
		 */
		protected $_votes = null;
		
		/**
		 * This issues comments
		 * 
		 * @var array
		 */
		protected $_comments;
		
		/**
		 * The issue this issue is a duplicate of
		 * 
		 * @var BUGSissue
		 */
		protected $_duplicateof;
		
		/**
		 * The milestone this issue is assigned to
		 * 
		 * @var BUGSmilestone
		 */
		protected $_milestone;
		
		/**
		 * List of issues this issue depends on
		 * 
		 * @var array
		 */
		protected $_parent_issues;
		
		/**
		 * List of issues that depends on this issue
		 * 
		 * @var array
		 */
		protected $_child_issues;
		
		/**
		 * List of log entries
		 * 
		 * @var array
		 */
		protected $_log_entries;
		
		/**
		 * Count the number of open and closed issues for a specific project id
		 * 
		 * @param integer $project_id The project ID
		 * 
		 * @return array
		 */
		static function getIssueCountsByProjectID($project_id)
		{
			return B2DB::getTable('B2tIssues')->getCountsByProjectID($project_id);
		}

		/**
		 * Count the number of open and closed issues for a specific project id
		 * and issue type id
		 * 
		 * @param integer $project_id The project ID
		 * @param integer $issuetype_id The issue type ID
		 * 
		 * @return array
		 */
		static function getIssueCountsByProjectIDandIssuetype($project_id, $issuetype_id)
		{
			return B2DB::getTable('B2tIssues')->getCountsByProjectIDandIssuetype($project_id, $issuetype_id);
		}

		/**
		 * Count the number of open and closed issues for a specific project id
		 * and milestone id
		 * 
		 * @param integer $project_id The project ID
		 * @param integer $milestone_id The milestone ID
		 * 
		 * @return array
		 */
		static function getIssueCountsByProjectIDandMilestone($project_id, $milestone_id)
		{
			return B2DB::getTable('B2tIssues')->getCountsByProjectIDandMilestone($project_id, $milestone_id);
		}
		
		/**
		 * Creates a new issue and returns it
		 *
		 * @param string $title The title
		 * @param string $description The description
		 * @param integer $issuetype The issue type
		 * @param integer $category The category
		 * @param integer $p_id The Project ID for the issue
		 * @param integer $issue_id[optional] specific issue_id
		 * 
		 * @return BUGSissue
		 */
		static function createNew($title, $issuetype, $p_id, $issue_id = null)
		{
			try
			{
				$i_id = B2DB::getTable('B2tIssues')->createNewWithTransaction($title, $issuetype, $p_id, $issue_id);
				
				$theIssue = BUGSfactory::BUGSissueLab($i_id);
				$theIssue->addLogEntry(B2tLog::LOG_ISSUE_CREATED);

				BUGScontext::trigger('core', 'BUGSIssue::createNew', $theIssue);
				return $theIssue;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns a BUGSissue from an issue no
		 *
		 * @param string $issue_no An integer or issue number
		 * 
		 * @return BUGSissue
		 */
		static function getIssueFromLink($issue_no)
		{
			$theIssue = null;
			if (is_numeric($issue_no))
			{
				try
				{
					$theIssue = BUGSfactory::BUGSissueLab((int) $issue_no);
					if ($theIssue->getProject()->usePrefix())
					{
						return null;
					}
				}
				catch (Exception $e)
				{
					return null;
				}
			}
			else
			{
				$issue_no = explode('-', strtoupper($issue_no));
				BUGSlogging::log('exploding');
				if (count($issue_no) == 2 && $row = B2DB::getTable('B2tIssues')->getByPrefixAndIssueNo($issue_no[0], $issue_no[1]))
				{
					$theIssue = BUGSfactory::BUGSissueLab($row->get(B2tIssues::ID), $row);
					if (!$theIssue->getProject()->usePrefix())
					{
						return null;
					}
				}
				BUGSlogging::log('exploding done');
			}
		
			return ($theIssue instanceof BUGSissue) ? $theIssue : null;
		}
		
		public function __call($method, $parameters = null)
		{
			return parent::__call($method, $parameters);
		}
		
		/**
		 * Construct a new issue
		 *
		 * @param integer $i_id
		 * @param B2DBRow $row
		 */
		public function __construct($i_id, $row = null)
		{
			if (!is_numeric($i_id))
			{
				throw new Exception('Please specify an issue id');
			}
			if ($row === null)
			{
				$row = B2DB::getTable('B2tIssues')->getByID($i_id, false);
			}
	
			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified issue id does not exist');
			}
			
			$this->_title 					= $row->get(B2tIssues::TITLE);
			$this->_project					= $row->get(B2tIssues::PROJECT_ID);
			$this->_issue_no 				= $row->get(B2tIssues::ISSUE_NO);
			$this->_issuetype 				= $row->get(B2tIssues::ISSUE_TYPE);
			$this->_issue_uniqueid 			= $row->get(B2tIssues::ID);
			$this->_description 			= $row->get(B2tIssues::LONG_DESCRIPTION);
			$this->_reproduction_steps		= $row->get(B2tIssues::REPRODUCTION);
			$this->_posted 					= $row->get(B2tIssues::POSTED);
			$this->_last_updated 			= $row->get(B2tIssues::LAST_UPDATED);
			$this->_resolution 				= $row->get(B2tIssues::RESOLUTION);
			$this->_state 					= $row->get(B2tIssues::STATE);
			$this->_status 					= $row->get(B2tIssues::STATUS);
			$this->_priority 				= $row->get(B2tIssues::PRIORITY);
			$this->_severity 				= $row->get(B2tIssues::SEVERITY);
			$this->_category 				= $row->get(B2tIssues::CATEGORY);
			$this->_reproducability 		= $row->get(B2tIssues::REPRODUCABILITY);
			$this->_scrumcolor				= $row->get(B2tIssues::SCRUMCOLOR);
			$this->_postedby 				= $row->get(B2tIssues::POSTED_BY);
			$this->_ownedby 				= $row->get(B2tIssues::OWNED_BY);
			$this->_ownedtype				= $row->get(B2tIssues::OWNED_TYPE);
			$this->_assignedto	 			= $row->get(B2tIssues::ASSIGNED_TO);
			$this->_assignedtype			= $row->get(B2tIssues::ASSIGNED_TYPE);
			$this->_blocking 				= (bool) $row->get(B2tIssues::BLOCKING);
			$this->_duplicateof 			= $row->get(B2tIssues::DUPLICATE);
			$this->_estimatedmonths			= $row->get(B2tIssues::ESTIMATED_MONTHS);
			$this->_estimatedweeks			= $row->get(B2tIssues::ESTIMATED_WEEKS);
			$this->_estimateddays			= $row->get(B2tIssues::ESTIMATED_DAYS);
			$this->_estimatedhours			= $row->get(B2tIssues::ESTIMATED_HOURS);
			$this->_estimatedpoints			= $row->get(B2tIssues::ESTIMATED_POINTS);
			$this->_spentmonths				= $row->get(B2tIssues::SPENT_MONTHS);
			$this->_spentweeks				= $row->get(B2tIssues::SPENT_WEEKS);
			$this->_spentdays				= $row->get(B2tIssues::SPENT_DAYS);
			$this->_spenthours				= $row->get(B2tIssues::SPENT_HOURS);
			$this->_spentpoints				= $row->get(B2tIssues::SPENT_POINTS);
			$this->_percentcompleted 		= $row->get(B2tIssues::PERCENT_COMPLETE);
			$this->_milestone 				= $row->get(B2tIssues::MILESTONE);
			$this->_being_worked_on_by		= $row->get(B2tIssues::USER_WORKING_ON);
			$this->_being_worked_on_since	= $row->get(B2tIssues::USER_WORKED_ON_SINCE);
			$this->_deleted 				= (bool) $row->get(B2tIssues::DELETED);
			
			$this->_mergeChangedProperties();
		}
		
		/**
		 * @deprecated
		 */
		public function __toString()
		{
			throw new Exception("Don't print the issue, use getFormattedTitle() instead.");
		}
		
		/**
		 * Print the issue number and title nicely formatted
		 *
		 * @return string
		 */
		public function getFormattedTitle()
		{
			return $this->getFormattedIssueNo() . ' - ' . $this->_title;
		}
		
		/**
		 * Whether or not the current user can access the issue
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			BUGSlogging::log('checking access to issue ' . $this->getFormattedIssueNo());
			$i_id = $this->getID();
			$project_id = $this->getProjectID();
			if (BUGScontext::getUser()->hasPermission("b2viewissue", $i_id) == true)
			{
				BUGSlogging::log('done checking, allowed');
				return true;
			}
			elseif (BUGScontext::getUser()->hasPermission("b2notviewissue", $i_id) == false)
			{
				if (BUGScontext::getUser()->hasPermission("b2projectaccess", $project_id))
				{
					if (BUGScontext::getUser()->hasPermission('b2canonlyviewownissues', 0) == false)
					{
						BUGSlogging::log('done checking, allowed');
						return true;
					}
					if ($this->getPostedByID() == BUGScontext::getUser()->getID())
					{
						BUGSlogging::log('done checking, allowed');
						return true;
					}
					if ($this->getOwnerType() == BUGSidentifiableclass::TYPE_USER && $this->getOwnerID() == BUGScontext::getUser()->getID())
					{
						BUGSlogging::log('done checking, allowed');
						return true;
					}
					if ($this->getAssigneeType() == BUGSidentifiableclass::TYPE_USER && $this->getAssigneeID() == BUGScontext::getUser()->getID())
					{
						BUGSlogging::log('done checking, allowed');
						return true;
					}
				}
			}
			BUGSlogging::log('done checking, denied');
			return false;
		}
		
		/**
		 * Returns the project for this issue
		 *
		 * @return BUGSproject
		 */
		public function getProject()
		{
			if (is_numeric($this->_project))
			{
				try
				{
					$this->_project = BUGSfactory::projectLab($this->_project);
				}
				catch (Exception $e) 
				{
					$this->_project = null;
				}
			}
			return $this->_project;
		}
		
		/**
		 * Returns the project id for this issue
		 * 
		 * @return integer
		 */
		public function getProjectID()
		{
			$project = $this->getProject();
			return ($project instanceof BUGSproject) ? $project->getID() : null;
		}
		
		/**
		 * Populates the affected items
		 */
		protected function _populateAffected()
		{
			if ($this->_editions === null && $this->_builds === null && $this->_components === null)
			{
				$this->_editions = array();
				$this->_builds = array();
				$this->_components = array();
		
				if ($res = B2DB::getTable('B2tIssueAffectsEdition')->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$this->_editions[] = array(	'edition' => BUGSfactory::editionLab($row->get(B2tIssueAffectsEdition::EDITION)),
														'status' => BUGSfactory::BUGSstatusLab($row->get(B2tIssueAffectsEdition::STATUS), $row),
														'confirmed' => (bool) $row->get(B2tIssueAffectsEdition::CONFIRMED),
														'a_id' => $row->get(B2tIssueAffectsEdition::ID));
						}
						catch (Exception $e) {}
					}
				}
				
				if ($res = B2DB::getTable('B2tIssueAffectsBuild')->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$this->_builds[] = array(	'build' => BUGSfactory::buildLab($row->get(B2tIssueAffectsBuild::BUILD)),
														'status' => BUGSfactory::BUGSstatusLab($row->get(B2tIssueAffectsBuild::STATUS), $row),
														'confirmed' => (bool) $row->get(B2tIssueAffectsBuild::CONFIRMED),
														'a_id' => $row->get(B2tIssueAffectsBuild::ID));
						}
						catch (Exception $e) {}
					}
				}
				
				if ($res = B2DB::getTable('B2tIssueAffectsComponent')->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$this->_components[] = array(	'component' => BUGSfactory::componentLab($row->get(B2tIssueAffectsComponent::COMPONENT)),
															'status' => BUGSfactory::BUGSstatusLab($row->get(B2tIssueAffectsComponent::STATUS), $row),
															'confirmed' => (bool) $row->get(B2tIssueAffectsComponent::CONFIRMED),
															'a_id' => $row->get(B2tIssueAffectsComponent::ID));
						}
						catch (Exception $e) {}
					}
				}
			}			
		}
		
		/**
		 * Returns the unique id for this issue
		 *
		 * @return integer
		 */
		public function getID()
		{
			return $this->_issue_uniqueid;
		}
		
		/**
		 * Returns the issue no for this issue
		 * 
		 * @return string
		 */
		public function getIssueNo()
		{
			return $this->_issue_no;
		}
		
		/**
		 * Returns the title for this issue
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_title;
		}
		
		/**
		 * Whether or not this issue is a duplicate of another issue
		 * 
		 * @return boolean
		 */
		public function isDuplicate()
		{
			return ($this->getDuplicateOf() instanceof BUGSissue) ? true : false;
		}
		
		/**
		 * Mark this issue as a duplicate of another issue
		 * 
		 * @param integer $d_id Issue ID for the duplicated issue
		 */
		public function setDuplicateOf($d_id)
		{
			B2DB::getTable('B2tIssues')->setDuplicate($this->getID(), $d_id);
			$this->_duplicateof = $d_id;
		}
		
		/**
		 * Clears the issue from being a duplicate
		 */
		public function clearDuplicate()
		{
			$this->setDuplicateOf(0);
		}
		
		/**
		 * Returns the issue which this is a duplicate of
		 *
		 * @return BUGSissue
		 */
		public function getDuplicateOf()
		{
			if (is_numeric($this->_duplicateof))
			{
				try
				{
					$this->_duplicateof = BUGSfactory::BUGSissueLab($this->_duplicateof);
				}
				catch (Exception $e) 
				{
					$this->_duplicateof = null;
				}
			}
			return $this->_duplicateof;
		}
		
		/**
		 * Check whether or not the user can edit issue details
		 * 
		 * @return boolean
		 */
		public function canEditIssueDetails()
		{
			if (!$this->getProject()->canChangeIssuesWithoutWorkingOnThem())
			{
				if (!$this->isBeingWorkedOn())
					return false;
				
				if ($this->getUserWorkingOnIssue()->getID() == BUGScontext::getUser()->getID())
					return true;
					
				return false;
			}
		}
		
		/**
		 * Return if the user can edit title
		 *
		 * @return boolean
		 */
		public function canEditTitle()
		{
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditDescription()
		{
		}
		
		/**
		 * Return if the user can edit posted by
		 *
		 * @return boolean
		 */
		public function canEditPostedBy()
		{
		}

		/**
		 * Return if the user can edit assigned to
		 *
		 * @return boolean
		 */
		public function canEditAssignedTo()
		{
		}
		
		/**
		 * Return if the user can edit owned by
		 *
		 * @return boolean
		 */
		public function canEditOwnedBy()
		{
		}
		
		/**
		 * Return if the user can edit status
		 *
		 * @return boolean
		 */
		public function canEditStatus()
		{
		}
		
		/**
		 * Return if the user can edit category
		 *
		 * @return boolean
		 */
		public function canEditCategory()
		{
		}
		
		/**
		 * Return if the user can edit resolution
		 *
		 * @return boolean
		 */
		public function canEditResolution()
		{
		}
		
		/**
		 * Return if the user can edit reproducability
		 *
		 * @return boolean
		 */
		public function canEditReproducability()
		{
		}
		
		/**
		 * Return if the user can edit severity
		 *
		 * @return boolean
		 */
		public function canEditSeverity()
		{
		}
		
		/**
		 * Return if the user can edit priority
		 *
		 * @return boolean
		 */
		public function canEditPriority()
		{
		}
		
		/**
		 * Return if the user can edit estimated time
		 *
		 * @return boolean
		 */
		public function canEditEstimatedTime()
		{
		}
		
		/**
		 * Return if the user can edit spent time
		 *
		 * @return boolean
		 */
		public function canEditSpentTime()
		{
		}
		
		/**
		 * Return if the user can edit progress (percent)
		 *
		 * @return boolean
		 */
		public function canEditPercentage()
		{
		}

		/**
		 * Return if the user can edit milestone
		 *
		 * @return boolean
		 */
		public function canEditMilestone()
		{
		}
		
		/**
		 * Return if the user can delete the issue
		 *
		 * @return boolean
		 */
		public function canDeleteIssue()
		{
		}
		
		/**
		 * Return if the user can start working on the issue
		 * 
		 * @return boolean
		 */
		public function canStartWorkingOnIssue()
		{
		}
	
		/**
		 * Returns a complete issue no
		 * 
		 * @param boolean $link_formatted[optional] Whether to include the # if it's only numeric (default false)
		 * 
		 * @return string
		 */
		public function getFormattedIssueNo($link_formatted = false)
		{
			if ($this->getProject()->usePrefix())
			{
				return $this->getProject()->getPrefix() . '-' . $this->getIssueNo();
			}
			else
			{
				return (($link_formatted) ? '#' : '') . $this->getID();
			}
		}
	
		/**
		 * Returns the issue type for this issue
		 *
		 * @return BUGSissuetype
		 */
		public function getIssueType()
		{
			if (is_numeric($this->_issuetype))
			{
				try
				{
					$this->_issuetype = BUGSfactory::BUGSissuetypeLab($this->_issuetype);
				}
				catch (Exception $e) 
				{
					$this->_issuetype = null;
				}
			}
			return $this->_issuetype;
		}
		
		/**
		 * Return timestamp for when the issue was posted
		 *
		 * @return integer
		 */
		public function getPosted()
		{
			return $this->_posted;
		}
		
		/**
		 * Returns the issue status
		 *
		 * @return BUGSdatatype
		 */
		public function getStatus()
		{
			if (is_numeric($this->_status))
			{
				try
				{
					$this->_status = BUGSfactory::BUGSstatusLab($this->_status);
				}
				catch (Exception $e)
				{
					$this->_status = null;
				}
			}
			return $this->_status;
		}
	
		/**
		 * Returns the editions for this issue
		 *
		 * @return array Returns an array with 'edition' (BUGSEdition), 'status' (BUGSDatatype), 'confirmed' (boolean) and 'a_id'
		 */
		public function getEditions()
		{
			$this->_populateAffected();
			return $this->_editions;
		}
		
		/**
		 * Returns the builds for this issue
		 *
		 * @return array Returns an array with 'build' (BUGSbuild), 'status' (BUGSdatatype), 'confirmed' (boolean) and 'a_id'
		 */
		public function getBuilds()
		{
			$this->_populateAffected();
			return $this->_builds;
		}
	
		/**
		 * Returns the components for this issue
		 *
		 * @return array Returns an array with 'component' (BUGSComponent), 'status' (BUGSdatatype), 'confirmed' (boolean) and 'a_id'
		 */
		public function getComponents()
		{
			$this->_populateAffected();
			return $this->_components;
		}
		
		/**
		 * Returns a string-formatted time based on project setting
		 *
		 * @param array $time array of weeks, days and hours
		 * 
		 * @return string
		 */
		public function getFormattedTime($time)
		{
			$values = array();
			$i18n = BUGScontext::getI18n();
			if (array_key_exists('months', $time) && $time['months'] > 0)
			{
				$values[] = ($time['months'] == 1) ? $i18n->__('1 month') : $i18n->__('%number_of% months', array('%number_of%' => $time['months']));
			}
			if (array_key_exists('weeks', $time) && $time['weeks'] > 0)
			{
				$values[] = ($time['weeks'] == 1) ? $i18n->__('1 week') : $i18n->__('%number_of% weeks', array('%number_of%' => $time['weeks']));
			}
			if (array_key_exists('days', $time) && $time['days'] > 0)
			{
				$values[] = ($time['days'] == 1) ? $i18n->__('1 day') : $i18n->__('%number_of% days', array('%number_of%' => $time['days']));
			}
			if (array_key_exists('hours', $time) && $time['hours'] > 0)
			{
				$values[] = ($time['hours'] == 1) ? $i18n->__('1 hour') : $i18n->__('%number_of% hours', array('%number_of%' => $time['hours']));
			}
			$retval = join(', ', $values);
			
			if (array_key_exists('points', $time) && $time['points'] > 0)
			{
				if (!empty($values))
				{
					$retval .= ' / ';
				}
				$retval .= ($time['points'] == 1) ? $i18n->__('1 point') : $i18n->__('%number_of% points', array('%number_of%' => $time['points']));
			}
			
			return $retval;
		}
	
		/**
		 * Attach a link to the issue
		 * 
		 * @param string $url The url of the link
		 * @param string $description[optional] a description
		 */
		public function attachLink($url, $description = null)
		{
			$res = B2DB::getTable('B2tLinks')->addLinkToIssue($this->getID(), $url, $description);
		}

		/**
		 * Attach a file to the issue
		 * 
		 * @param string $filename The filename (relative to the files/ subdirectory)
		 * @param string $description[optional] a description
		 */
		public function attachFile($filename, $description = null)
		{
			B2DB::getTable('B2tFiles')->addFileToIssue($this->getID(), $filename, $description);
		}

		/**
		 * populates related issues
		 */
		protected function _populateRelatedIssues()
		{
			if ($this->_parent_issues === null && $this->_child_issues === null)
			{
				$this->_parent_issues = array();
				$this->_child_issues = array();
				
				if ($res = B2DB::getTable('B2tIssueRelations')->getRelatedIssues($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							if ($row->get(B2tIssueRelations::PARENT_ID) == $this->getID())
							{
								$this->_parent_issues[$row->get(B2tIssueRelations::ID)] = BUGSfactory::BUGSissueLab($row->get(B2tIssueRelations::CHILD_ID));
							}
							else
							{
								$this->_child_issues[$row->get(B2tIssueRelations::ID)] = BUGSfactory::BUGSissueLab($row->get(B2tIssueRelations::PARENT_ID));
							}
						}
						catch (Exception $e) {}
					}
				}
			}
		}
		
		/**
		 * Return issues relating to this
		 * 
		 * @return array
		 */
		public function getParentIssues()
		{
			$this->_populateRelatedIssues();
			return $this->_parent_issues;
		} 

		/**
		 * Return related issues
		 * 
		 * @return array
		 */
		public function getChildIssues()
		{
			$this->_populateRelatedIssues();
			return $this->_child_issues;
		} 
		
		/**
		 * Returns the number of votes for this issue
		 * 
		 * @return unknown_type
		 */
		public function getVotes()
		{
			if ($this->_votes === null)
			{
				$this->_votes = B2DB::getTable('B2tVotes')->getNumberOfVotesForIssue($this->getID());
			}
			return $this->_votes;
		}

		/**
		 * Whether or not the current user has voted
		 * 
		 * @return boolean
		 */
		public function hasUserVoted()
		{
			return (bool) B2DB::getTable('B2tVotes')->getByUserIdAndIssueId(BUGScontext::getUser()->getID(), $this->getID());
		}
	
		/**
		 * Vote for this issue, returns false if user cant vote or has voted before
		 * 
		 * @return boolean
		 */
		public function vote()
		{
			if (!BUGScontext::getUser()->hasPermission("b2cantvote", $this->getID()) || $this->hasUserVoted())
			{
				return false;
			}
			else
			{
				B2DB::getTable('B2tVotes')->addByUserIdAndIssueId(BUGScontext::getUser()->getID(), $this->getID());
				return true;
			}
		}
	
		/**
		 * Returns an array with tasks
		 *
		 * @return array
		 */
		public function getTasks()
		{
			if ($this->getProject()->isTasksEnabled())
			{
				if ($this->_tasks == null)
				{
					$this->_tasks = array();
					if ($res = B2DB::getTable('B2tIssueTasks')->getByIssueID($this->getID()))
					{
						while ($row = $resultset->getNextRow())
						{
							$this->_tasks[$row->get(B2tIssueTasks::ID)] = BUGSfactory::taskLab($row->get(B2tIssueTasks::ID), $row);
						}
					}
				}
			}
	
			return $this->_tasks;
		}

		/**
		 * Returns an array of tags
		 *
		 * @return array
		 */
		public function getTags()
		{
			if ($this->_tags == null)
			{
				$this->_tags = array();
				if ($res = B2DB::getTable('B2tIssueTags')->getByIssueID($this->getID()))
				{
					while ($row = $resultset->getNextRow())
					{
						$this->_tags[$row->get(B2tIssueTags::ID)] = $row->get(B2tIssueTags::TAG_NAME);
					}
				}
			}

			return $this->_tasks;
		}

		/**
		 * Returns whether or not the issue has been deleted
		 *
		 * @return boolean
		 */
		public function isDeleted()
		{
			return $this->_deleted;
		}
	
		/**
		 * Returns the issue title
		 *
		 * @return string
		 */
		public function getTitle()
		{
			return $this->_title;
		}
		
		/**
		 * Set the title
		 * 
		 * @param string $title The new title to set
		 */
		public function setTitle($title)
		{
			if (trim($title) == '')
			{
				throw new Exception("Can't set an empty title");
			}
			$this->_addChangedProperty('_title', $title);
		}
		
		/**
		 * Returns the description
		 *
		 * @return string
		 */
		public function getDescription()
		{
			return $this->_description;
		}
		
		/**
		 * Return whether or not this issue has a description set
		 * 
		 * @return boolean
		 */
		public function hasDescription()
		{
			return (bool) (trim($this->getDescription()) != '');
		}
	
		/**
		 * Set the description
		 * 
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_addChangedProperty('_description', $description);
		}
	
		/**
		 * Returns the issues reproduction steps
		 *
		 * @return string
		 */
		public function getReproductionSteps()
		{
			return $this->_reproduction_steps;
		}
		
		/**
		 * Set the reproduction steps
		 * 
		 * @param string $reproduction_steps
		 */
		public function setReproductionSteps($reproduction_steps)
		{
			$this->_addChangedProperty('_reproduction_steps', $reproduction_steps);
		}
		
		/**
		 * Returns the category
		 *
		 * @return BUGSdatatype
		 */
		public function getCategory()
		{
			if (is_numeric($this->_category))
			{
				try
				{
					$this->_category = BUGSfactory::BUGScategoryLab($this->_category);
				}
				catch (Exception $e)
				{
					$this->_category = null;
				}
			}
			return $this->_category;
		}
		
		/**
		 * Set the category
		 * 
		 * @param integer $category_id The category ID to change to
		 */
		public function setCategory($category_id)
		{
			$this->_addChangedProperty('_category', $category_id);
		}

		/**
		 * Set the status
		 * 
		 * @param integer $status_id The status ID to change to
		 */
		public function setStatus($status_id)
		{
			$this->_addChangedProperty('_status', $status_id);			
		}
		
		/**
		 * Returns the reproducability
		 *
		 * @return BUGSdatatype
		 */
		public function getReproducability()
		{
			if (is_numeric($this->_reproducability))
			{
				try
				{
					$this->_reproducability = BUGSfactory::BUGSreproducabilityLab($this->_reproducability);
				}
				catch (Exception $e)
				{
					$this->_reproducability = null;
				}
			}
			return $this->_reproducability;
		}
		
		/**
		 * Set the reproducability
		 * 
		 * @param integer $reproducability_id The reproducability id to change to
		 */
		public function setReproducability($reproducability_id)
		{
			$this->_addChangedProperty('_reproducability', $reproducability_id);
		}
	
		/**
		 * Returns the priority
		 *
		 * @return BUGSdatatype
		 */
		public function getPriority()
		{
			if (is_numeric($this->_priority))
			{
				try
				{
					$this->_priority = BUGSfactory::BUGSpriorityLab($this->_priority);
				}
				catch (Exception $e)
				{
					$this->_priority = null;
				}
			}
			return $this->_priority;
		}

		/**
		 * Set the priority
		 *
		 * @param integer $priority_id The priority id to change to
		 */
		public function setPriority($priority_id)
		{
			$this->_addChangedProperty('_priority', $priority_id);
		}

		/**
		 * Returns the scrum color
		 *
		 * @return string
		 */
		public function getScrumColor()
		{
			return $this->_scrumcolor;
		}

		/**
		 * Set the priority
		 *
		 * @param integer $priority_id The priority id to change to
		 */
		public function setScrumColor($color)
		{
			$this->_addChangedProperty('_scrumcolor', $color);
		}

		/**
		 * Returns the assigned milestone if any
		 *
		 * @return BUGSmilestone
		 */
		public function getMilestone()
		{
			if (is_numeric($this->_milestone))
			{
				try
				{
					$this->_milestone = BUGSfactory::milestoneLab($this->_milestone);
				}
				catch (Exception $e)
				{
					$this->_milestone = null;
				}
			}
			return $this->_milestone;
		}
		
		/**
		 * Set the milestone
		 * 
		 * @param integer $milestone_id The milestone id to assign
		 */
		public function setMilestone($milestone_id)
		{
			$this->_addChangedProperty('_milestone', $milestone_id);
		}

		/**
		 * Remove the assigned milestone
		 */
		public function removeMilestone()
		{
			$this->setMilestone(0);
		}
	
		/**
		 * Remove a dependant issue
		 * 
		 * @param integer $issue_id The issue ID to remove
		 */
		public function removeDependantIssue($issue_id)
		{
			if ($row = B2DB::getTable('B2tIssueRelations')->getIssueRelation($this->getID(), $issue_id))
			{
				$related_issue = BUGSfactory::BUGSissueLab($issue_id);
				if ($row->get(B2tIssueRelations::PARENT_ID) == $this->getID())
				{
					$this->_removeChildIssue($related_issue, $row->get(B2tIssueRelations::ID));
				}
				else
				{
					$this->_removeParentIssue($related_issue, $row->get(B2tIssueRelations::ID));
				}
			}
		}
		
		/**
		 * Removes a related issue
		 *
		 * @see removeDependantIssue()
		 * 
		 * @param BUGSissue $related_issue The issue to remove relations from
		 * @param integer $relation_id The relation id to delete
		 */
		protected function _removeParentIssue($related_issue, $relation_id)
		{
			$this->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $related_issue->getFormattedIssueNo())));
			$this->addSystemComment(__('Issue dependancy removed'), __('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $related_issue->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
			
			$related_issue->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())));
			$related_issue->addSystemComment(__('Issue dependancy removed'), __('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
			
			if ($this->_parent_issues !== null && array_key_exists($relation_id, $this->_parent_issues))
			{
				unset($this->_parent_issues[$relation_id]);
			}
		}
		
		/**
		 * Removes a related issue
		 * 
		 * @see removeDependantIssue()
		 * 
		 * @param BUGSissue $related_issue The issue to remove relations from
		 * @param integer $relation_id The relation id to delete
		 */
		protected function _removeChildIssue($related_issue, $relation_id)
		{
			$this->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $related_issue->getFormattedIssueNo())));
			$this->addSystemComment(__('Issue dependancy removed'), __('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $related_issue->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
			
			$related_issue->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())));
			$related_issue->addSystemComment(__('Issue dependancy removed'), __('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
			
			if ($this->_child_issues !== null && array_key_exists($relation_id, $this->_child_issues))
			{
				unset($this->_child_issues[$relation_id]);
			}
		}

		/**
		 * Add a related issue
		 * 
		 * @param BUGSissue $related_issue
		 * 
		 * @return boolean
		 */
		public function addParentIssue(BUGSissue $related_issue)
		{
			if (!$row = B2DB::getTable('B2tIssueRelations')->getIssueRelation($this->getID(), $related_issue->getID()))
			{
				$res = B2DB::getTable('B2tIssueRelations')->addParentIssue($this->getID(), $related_issue->getID());
				
				$this->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('This issue now depends on the solution of issue %issue_no%', array('%issue_no%' => $related_issue->getFormattedIssueNo())));
				$this->addSystemComment(__('Issue dependancy added'), __('This issue now depends on the solution of issue %issue_no%', array('%issue_no%' => $related_issue->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
				
				$related_issue->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('Issue %issue_no% now depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())));
				$related_issue->addSystemComment(__('Issue dependancy added'), __('Issue %issue_no% now depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
				
				return true;
			}
			return false;
		}

		/**
		 * Add a related issue
		 * 
		 * @param BUGSissue $related_issue
		 * 
		 * @return boolean
		 */
		public function addChildIssue(BUGSissue $related_issue)
		{
			if (!$row = B2DB::getTable('B2tIssueRelations')->getIssueRelation($this->getID(), $related_issue->getID()))
			{
				$res = B2DB::getTable('B2tIssueRelations')->addChildIssue($this->getID(), $related_issue->getID());
				
				$this->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('Issue %issue_no% now depends on the solution of this issue', array('%issue_no%' => $related_issue->getFormattedIssueNo())));
				$this->addSystemComment(__('Issue dependancy removed'), __('Issue %issue_no% now depends on the solution of this issue', array('%issue_no%' => $related_issue->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
				
				$related_issue->addLogEntry(B2tLog::LOG_ISSUE_DEPENDS, __('This issue now depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())));
				$related_issue->addSystemComment(__('Issue dependancy removed'), __('This issue now depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())), BUGScontext::getUser()->getUID());
				
				return true;
			}
			return false;
		}

		/**
		 * Return the assignee
		 *
		 * @return BUGSidentifiableclass
		 */
		public function getAssignee()
		{
			if (is_numeric($this->_assignedto))
			{
				try
				{
					if ($this->_assignedtype == BUGSidentifiableclass::TYPE_USER)
					{
						$this->_assignedto = BUGSfactory::userLab($this->_assignedto);
					}
					elseif ($this->_assignedtype == BUGSidentifiableclass::TYPE_TEAM)
					{
						$this->_assignedto = BUGSfactory::teamLab($this->_assignedto);
					}
				}
				catch (Exception $e)
				{
					$this->_assignedto = null;
					$this->_assignedtype = null;
				}
			}
	
			return $this->_assignedto;
		}
		
		/**
		 * Whether or not the issue is assigned to someone
		 *
		 * @return boolean
		 */
		public function isAssigned()
		{
			return (bool) ($this->getAssignee() instanceof BUGSidentifiableclass);
		}
		
		/**
		 * Returns the assignee type
		 *
		 * @return integer
		 */
		public function getAssigneeType()
		{
			$assignee = $this->getAssignee();
			return ($assignee instanceof BUGSidentifiableclass) ? $assignee->getType() : null;
		}
		
		/**
		 * Return the assignee id
		 *
		 * @return integer
		 */
		public function getAssigneeID()
		{
			$assignee = $this->getAssignee();
			return ($assignee instanceof BUGSidentifiableclass) ? $assignee->getID() : null;
		}
		
		/**
		 * Assign the issue
		 * 
		 * @param BUGSidentifiableclass $assignee The user/team you want to assign it to
		 */
		public function setAssignee(BUGSidentifiableclass $assignee)
		{
			$this->_addChangedProperty('_assignedto', $assignee->getID());
			$this->_addChangedProperty('_assignedtype', $assignee->getType());
		}
		
		/**
		 * Set issue assignee to noone
		 */
		public function unsetAssignee()
		{
			$this->_addChangedProperty('_assignedto', 0);
			$this->_addChangedProperty('_assignedtype', 0);
		}
		
		/**
		 * Return the owner
		 *
		 * @return BUGSidentifiableclass
		 */
		public function getOwner()
		{
			if (is_numeric($this->_ownedby))
			{
				try
				{
					if ($this->_ownedtype == BUGSidentifiableclass::TYPE_USER)
					{
						$this->_ownedby = BUGSfactory::userLab($this->_ownedby);
					}
					elseif ($this->_ownedtype == BUGSidentifiableclass::TYPE_TEAM)
					{
						$this->_ownedby = BUGSfactory::teamLab($this->_ownedby);
					}
				}
				catch (Exception $e)
				{
					$this->_ownedby = null;
					$this->_ownedtype = null;
				}
			}
	
			return $this->_ownedby;
		}
		
		/**
		 * Whether or not the issue is owned by someone
		 *
		 * @return boolean
		 */
		public function isOwned()
		{
			return (bool) ($this->getOwner() instanceof BUGSidentifiableclass);
		}
		
		/**
		 * Returns the owner type
		 *
		 * @return integer
		 */
		public function getOwnerType()
		{
			$owner = $this->getOwner();
			return ($owner instanceof BUGSidentifiableclass) ? $owner->getType() : null;
		}
		
		/**
		 * Return the owner id
		 *
		 * @return integer
		 */
		public function getOwnerID()
		{
			$owner = $this->getOwner();
			return ($owner instanceof BUGSidentifiableclass) ? $owner->getID() : null;
		}
		
		/**
		 * Set issue owner
		 * 
		 * @param BUGSidentifiableclass $owner The user/team you want to own the issue
		 */
		public function setOwner(BUGSidentifiableclass $owner)
		{
			$this->_addChangedProperty('_ownedby', $owner->getID());
			$this->_addChangedProperty('_ownedtype', $owner->getType());
		}
		
		/**
		 * Set issue owner to noone
		 */
		public function unsetOwner()
		{
			$this->_addChangedProperty('_ownedby', 0);
			$this->_addChangedProperty('_ownedtype', 0);
		}
		
		/**
		 * Return the poster
		 *
		 * @return BUGSuser
		 */
		public function getPostedBy()
		{
			if (is_numeric($this->_postedby))
			{
				try
				{
					$this->_postedby = BUGSfactory::userLab($this->_postedby);
				}
				catch (Exception $e)
				{
					$this->_postedby = null;
				}
			}
	
			return $this->_postedby;
		}
		
		/**
		 * Whether or not the issue is posted by someone
		 *
		 * @return boolean
		 */
		public function isPostedBy()
		{
			return (bool) ($this->getPostedBy() instanceof BUGSidentifiable);
		}
		
		/**
		 * Return the poster id
		 *
		 * @return integer
		 */
		public function getPostedByID()
		{
			$poster = $this->getPostedBy();
			return ($poster instanceof BUGSidentifiable) ? $poster->getID() : null;
		}
		
		/**
		 * Set issue poster
		 * 
		 * @param BUGSidentifiableclass $poster The user/team you want to have posted the issue
		 */
		public function setPostedBy(BUGSidentifiableclass $poster)
		{
			$this->_addChangedProperty('_postedby', $poster->getID());
		}
		
		/**
		 * Returns the percentage completed
		 * 
		 * @return integer
		 */
		public function getPercentCompleted()
		{
			return (int) $this->_percentcompleted;
		}
		
		/**
		 * Set percentage completed
		 * 
		 * @param integer $percentage
		 */
		public function setPercentCompleted($percentage)
		{
			$this->_addChangedProperty('_percentcompleted', $percentage);
		}
	
		/**
		 * Returns the resolution
		 *
		 * @return BUGSdatatype
		 */
		public function getResolution()
		{
			if (is_numeric($this->_resolution))
			{
				try
				{
					$this->_resolution = BUGSfactory::BUGSresolutionLab($this->_resolution);
				}
				catch (Exception $e)
				{
					$this->_resolution = null;
				}
			}
			return $this->_resolution;
		}
		
		/**
		 * Set the resolution
		 * 
		 * @param integer $resolution_id The resolution ID you want to set it to
		 */
		public function setResolution($resolution_id)
		{
			$this->_addChangedProperty('_resolution', $resolution_id);
		}

		/**
		 * Returns the severity
		 *
		 * @return BUGSdatatype
		 */
		public function getSeverity()
		{
			if (is_numeric($this->_severity))
			{
				try
				{
					$this->_severity = BUGSfactory::BUGSseverityLab($this->_severity);
				}
				catch (Exception $e)
				{
					$this->_severity = null;
				}
			}
			return $this->_severity;
		}

		/**
		 * Set the severity
		 * 
		 * @param integer $severity_id The severity ID you want to set it to
		 */
		public function setSeverity($severity_id)
		{
			$this->_addChangedProperty('_severity', $severity_id);
		}
	
		/**
		 * Set the issue type
		 * 
		 * @param integer $issuetype_id The issue type ID you want to set
		 */
		public function setIssuetype($issuetype_id)
		{
			$this->_addChangedProperty('_issuetype', $issuetype_id);
		}
	
		/**
		 * Returns an array with the estimated time
		 *
		 * @return array
		 */
		public function getEstimatedTime()
		{
			return array('months' => (int) $this->_estimatedmonths, 'weeks' => (int) $this->_estimatedweeks, 'days' => (int) $this->_estimateddays, 'hours' => (int) $this->_estimatedhours, 'points' => (int) $this->_estimatedpoints);
		}
		
		/**
		 * Returns the estimated months
		 * 
		 * @return integer
		 */
		public function getEstimatedMonths()
		{
			return (int) $this->_estimatedmonths;
		}

		/**
		 * Returns the estimated weeks
		 * 
		 * @return integer
		 */
		public function getEstimatedWeeks()
		{
			return (int) $this->_estimatedweeks;
		}

		/**
		 * Returns the estimated days
		 * 
		 * @return integer
		 */
		public function getEstimatedDays()
		{
			return (int) $this->_estimateddays;
		}
		
		/**
		 * Returns the estimated hours
		 * 
		 * @return integer
		 */
		public function getEstimatedHours()
		{
			return (int) $this->_estimatedhours;
		}
		
		/**
		 * Returns the estimated points
		 * 
		 * @return integer
		 */
		public function getEstimatedPoints()
		{
			return (int) $this->_estimatedpoints;
		}
		
		/**
		 * Turns a string into a months/weeks/days/hours/points array
		 * 
		 * @param string $string The string to convert
		 * 
		 * @return array
		 */
		protected function _convertFancyStringToTime($string)
		{
			$retarr = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'points' => 0);
			$string = strtolower(trim($string));
			$time_arr = preg_split('/(\,|\/|and|or|plus)/', $string);
			foreach ($time_arr as $time_elm)
			{
				$time_parts = explode(' ', trim($time_elm));
				if (is_array($time_parts) && count($time_parts) > 1)
				{
					switch (true)
					{
						case stristr($time_parts[1], 'month'):
							$retarr['months'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'week'):
							$retarr['weeks'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'day'):
							$retarr['days'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'hour'):
							$retarr['hours'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'point'):
							$retarr['points'] = (int) trim($time_parts[0]);
							break;
					}
				}
			}
			return $retarr;
		}
		
		/**
		 * Returns whether or not there is an estimated time for this issue
		 * 
		 * @return boolean
		 */
		public function hasEstimatedTime()
		{
			$time = $this->getEstimatedTime();
			return (array_sum($time) > 0) ? true : false;
		}
		
		/**
		 * Set estimated time
		 *
		 * @param integer $time
		 */
		public function setEstimatedTime($time)
		{
			if (is_numeric($time))
			{
				$this->_addChangedProperty('_estimatedmonths', 0);
				$this->_addChangedProperty('_estimatedweeks', 0);
				$this->_addChangedProperty('_estimateddays', 0);
				if ($this->getProject()->getTimeUnit() == BUGSproject::TIME_UNIT_HOURS)
				{
					$this->_addChangedProperty('_estimatedpoints', 0);
					$this->_addChangedProperty('_estimatedhours', (int) $time);
				}
				elseif ($this->getProject()->getTimeUnit() == BUGSproject::TIME_UNIT_POINTS)
				{
					$this->_addChangedProperty('_estimatedhours', 0);
					$this->_addChangedProperty('_estimatedpoints', (int) $time);
				}
				else
				{
					$this->_addChangedProperty('_estimatedhours', 0);
					$this->_addChangedProperty('_estimatedpoints', 0);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_estimatedmonths', $time['months']);
				$this->_addChangedProperty('_estimatedweeks', $time['weeks']);
				$this->_addChangedProperty('_estimateddays', $time['days']);
				$this->_addChangedProperty('_estimatedhours', $time['hours']);
				$this->_addChangedProperty('_estimatedpoints', $time['points']);
			}
		}
		
		/**
		 * Set estimated months
		 * 
		 * @param integer $months The number of months estimated
		 */
		public function setEstimatedMonths($months)
		{
			$this->_addChangedProperty('_estimatedmonths', $months);
		}
	
		/**
		 * Set estimated weeks
		 * 
		 * @param integer $weeks The number of weeks estimated
		 */
		public function setEstimatedWeeks($weeks)
		{
			$this->_addChangedProperty('_estimatedweeks', $weeks);
		}
	
		/**
		 * Set estimated days
		 * 
		 * @param integer $days The number of days estimated
		 */
		public function setEstimatedDays($days)
		{
			$this->_addChangedProperty('_estimateddays', $days);
		}
	
		/**
		 * Set estimated hours
		 * 
		 * @param integer $hours The number of hours estimated
		 */
		public function setEstimatedHours($hours)
		{
			$this->_addChangedProperty('_estimatedhours', $hours);
		}
	
		/**
		 * Set estimated points
		 * 
		 * @param integer $points The number of points estimated
		 */
		public function setEstimatedPoints($points)
		{
			$this->_addChangedProperty('_estimatedpoints', $points);
		}
		
		/**
		 * Check to see whether the estimated time is changed
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeChanged()
		{
			return (bool) ($this->isEstimatedMonthsChanged() || $this->isEstimatedWeeksChanged() || $this->isEstimatedDaysChanged() || $this->isEstimatedHoursChanged() || $this->isEstimatedPointsChanged());
		}

		/**
		 * Check to see whether the estimated time is merged
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeMerged()
		{
			return (bool) ($this->isEstimatedMonthsMerged() || $this->isEstimatedWeeksMerged() || $this->isEstimatedDaysMerged() || $this->isEstimatedHoursMerged() || $this->isEstimatedPointsMerged());
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertEstimatedTime()
		{
			$this->revertEstimatedMonths();
			$this->revertEstimatedWeeks();
			$this->revertEstimatedDays();
			$this->revertEstimatedHours();
			$this->revertEstimatedPoints();
		}
	
		/**
		 * Check to see whether the owner is changed
		 * 
		 * @return boolean
		 */
		public function isOwnedByChanged()
		{
			return (bool) ($this->isOwnedTypeChanged() || $this->_isPropertyChanged('_ownedby'));
		}

		/**
		 * Check to see whether the owner is merged
		 * 
		 * @return boolean
		 */
		public function isOwnedByMerged()
		{
			return (bool) ($this->isOwnedTypeMerged() || $this->_isPropertyMerged('_ownedby'));
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertOwnedBy()
		{
			$this->revertOwnedType();
			$this->_revertPropertyChange('_ownedby');
		}

		/**
		 * Check to see whether the assignee is changed
		 * 
		 * @return boolean
		 */
		public function isAssignedToChanged()
		{
			return (bool) ($this->isAssignedTypeChanged() || $this->_isPropertyChanged('_assignedto'));
		}

		/**
		 * Check to see whether the owner is merged
		 * 
		 * @return boolean
		 */
		public function isAssignedToMerged()
		{
			return (bool) ($this->isAssignedTypeMerged() || $this->_isPropertyMerged('_assignedto'));
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertAssignedTo()
		{
			$this->revertAssignedType();
			$this->_revertPropertyChange('_assignedto');
		}

		/**
		 * Returns an array with the spent time
		 *
		 * @return array
		 */
		public function getSpentTime()
		{
			return array('months' => (int) $this->_spentmonths, 'weeks' => (int) $this->_spentweeks, 'days' => (int) $this->_spentdays, 'hours' => (int) $this->_spenthours, 'points' => (int) $this->_spentpoints);
		}
		
		/**
		 * Returns the spent months
		 * 
		 * @return integer
		 */
		public function getSpentMonths()
		{
			return (int) $this->_spentmonths;
		}

		/**
		 * Returns the spent weeks
		 * 
		 * @return integer
		 */
		public function getSpentWeeks()
		{
			return (int) $this->_spentweeks;
		}

		/**
		 * Returns the spent days
		 * 
		 * @return integer
		 */
		public function getSpentDays()
		{
			return (int) $this->_spentdays;
		}
		
		/**
		 * Returns the spent hours
		 * 
		 * @return integer
		 */
		public function getSpentHours()
		{
			return (int) $this->_spenthours;
		}
		
		/**
		 * Returns the spent points
		 * 
		 * @return integer
		 */
		public function getSpentPoints()
		{
			return (int) $this->_spentpoints;
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
		 * Set time spent on this issue
		 *
		 * @param integer $time
		 */
		public function setSpentTime($time)
		{
			if (is_numeric($time))
			{
				$this->_addChangedProperty('_spentmonths', 0);
				$this->_addChangedProperty('_spentweeks', 0);
				$this->_addChangedProperty('_spentdays', 0);
				if ($this->getProject()->getTimeUnit() == BUGSproject::TIME_UNIT_HOURS)
				{
					$this->_addChangedProperty('_spentpoints', 0);
					$this->_addChangedProperty('_spenthours', (int) $time);
				}
				elseif ($this->getProject()->getTimeUnit() == BUGSproject::TIME_UNIT_POINTS)
				{
					$this->_addChangedProperty('_spenthours', 0);
					$this->_addChangedProperty('_spentpoints', (int) $time);
				}
				else
				{
					$this->_addChangedProperty('_spenthours', 0);
					$this->_addChangedProperty('_spentpoints', 0);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_spentmonths', $time['months']);
				$this->_addChangedProperty('_spentweeks', $time['weeks']);
				$this->_addChangedProperty('_spentdays', $time['days']);
				$this->_addChangedProperty('_spenthours', $time['hours']);
				$this->_addChangedProperty('_spentpoints', $time['points']);
			}
		}
		
		/**
		 * Add to spent time
		 *
		 * @param integer $time
		 */
		public function addSpentTime($time)
		{
			if (is_numeric($time))
			{
				if ($this->getProject()->getTimeUnit() == BUGSproject::TIME_UNIT_HOURS)
				{
					$this->_addChangedProperty('_spenthours', $this->_spenthours + (int) $time);
				}
				elseif ($this->getProject()->getTimeUnit() == BUGSproject::TIME_UNIT_POINTS)
				{
					$this->_addChangedProperty('_spentpoints', $this->_spentpoints + (int) $time);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_spentmonths', $this->_spentmonths + $time['months']);
				$this->_addChangedProperty('_spentweeks', $this->_spentweeks + $time['weeks']);
				$this->_addChangedProperty('_spentdays', $this->_spentdays + $time['days']);
				$this->_addChangedProperty('_spenthours', $this->_spenthours + $time['hours']);
				$this->_addChangedProperty('_spentpoints', $this->_spentpoints + $time['points']);
			}
		}		

		/**
		 * Set spent months
		 * 
		 * @param integer $months The number of months spent
		 */
		public function setSpentMonths($months)
		{
			$this->_addChangedProperty('_spentmonths', $months);
		}
	
		/**
		 * Set spent weeks
		 * 
		 * @param integer $weeks The number of weeks spent
		 */
		public function setSpentWeeks($weeks)
		{
			$this->_addChangedProperty('_spentweeks', $weeks);
		}
	
		/**
		 * Set spent days
		 * 
		 * @param integer $days The number of days spent
		 */
		public function setSpentDays($days)
		{
			$this->_addChangedProperty('_spentdays', $days);
		}
	
		/**
		 * Set spent hours
		 * 
		 * @param integer $hours The number of hours spent
		 */
		public function setSpentHours($hours)
		{
			$this->_addChangedProperty('_spenthours', $hours);
		}
	
		/**
		 * Set spent points
		 * 
		 * @param integer $points The number of points spent
		 */
		public function setSpentPoints($points)
		{
			$this->_addChangedProperty('_spentpoints', $points);
		}

		/**
		 * Add spent months
		 * 
		 * @param integer $months The number of months spent
		 */
		public function addSpentMonths($months)
		{
			$this->_addChangedProperty('_spentmonths', $this->_spentmonths + $months);
		}
	
		/**
		 * Add spent weeks
		 * 
		 * @param integer $weeks The number of weeks spent
		 */
		public function addSpentWeeks($weeks)
		{
			$this->_addChangedProperty('_spentweeks', $this->_spentweeks + $weeks);
		}
	
		/**
		 * Add spent days
		 * 
		 * @param integer $days The number of days spent
		 */
		public function addSpentDays($days)
		{
			$this->_addChangedProperty('_spentdays', $this->_spentdays + $days);
		}
	
		/**
		 * Add spent hours
		 * 
		 * @param integer $hours The number of hours spent
		 */
		public function addSpentHours($hours)
		{
			$this->_addChangedProperty('_spenthours', $this->_spenthours + $hours);
		}
	
		/**
		 * Add spent points
		 * 
		 * @param integer $points The number of points spent
		 */
		public function addSpentPoints($points)
		{
			$this->_addChangedProperty('_spentpoints', $this->_spentpoints + $points);
		}
		
		/**
		 * Check to see whether the spent time is changed
		 * 
		 * @return boolean
		 */
		public function isSpentTimeChanged()
		{
			return (bool) ($this->isSpentMonthsChanged() || $this->isSpentWeeksChanged() || $this->isSpentDaysChanged() || $this->isSpentHoursChanged() || $this->isSpentPointsChanged());
		}

		/**
		 * Check to see whether the spent time is merged
		 * 
		 * @return boolean
		 */
		public function isSpentTimeMerged()
		{
			return (bool) ($this->isSpentMonthsMerged() || $this->isSpentWeeksMerged() || $this->isSpentDaysMerged() || $this->isSpentHoursMerged() || $this->isSpentPointsMerged());
		}
		
		/**
		 * Reverts spent time
		 */
		public function revertSpentTime()
		{
			$this->revertSpentMonths();
			$this->revertSpentWeeks();
			$this->revertSpentDays();
			$this->revertSpentHours();
			$this->revertSpentPoints();
		}
		
		/**
		 * Returns whether or not there is an spent time for this issue
		 * 
		 * @return boolean
		 */
		public function hasSpentTime()
		{
			$time = $this->getSpentTime();
			return (array_sum($time) > 0) ? true : false;
		}
		
		/**
		 * Returns the timestamp for when the issue was last updated
		 *
		 * @return integer
		 */
		public function getLastUpdatedTime()
		{
			return $this->_last_updated;
		}
	
		/**
		 * Returns the issues state
		 *
		 * @return integer
		 */
		public function getState()
		{
			return $this->_state;
		}
		
		/**
		 * Whether or not the issue is closed
		 * 
		 * @see getState()
		 * @see isOpen()
		 * 
		 * @return boolean
		 */
		public function isClosed()
		{
			return ($this->getState() == self::STATE_CLOSED) ? true : false;
		}
		
		/**
		 * Whether or not the issue is open
		 * 
		 * @see getState()
		 * @see isClosed()
		 * 
		 * @return boolean
		 */
		public function isOpen()
		{
			return !$this->isClosed();
		}
		
		/**
		 * Set the issue state
		 * 
		 * @param integer $state The state
		 */
		public function setState($state)
		{
			if (!in_array($state, array(self::STATE_CLOSED, self::STATE_OPEN)))
			{
				return false;
			}

			$this->_addChangedProperty('_state', $state);
			
			return true;
		}
		
		/**
		 * Close the issue
		 */
		public function close()
		{
			$this->setState(self::STATE_CLOSED);
		}
	
		/**
		 * (Re-)open the issue
		 */
		public function open()
		{
			$this->setState(self::STATE_OPEN);
		}
	
		/**
		 * Add a build to the list of affected builds
		 * 
		 * @param BUGSbuild $build The build to add
		 * 
		 * @return boolean
		 */
		public function addAffectedBuild($build)
		{
			if ($this->getProject() && $this->getProject()->isBuildsEnabled())
			{
				if ($retval = B2DB::getTable('B2tIssueAffectsBuild')->setIssueAffected($this->getID(), $build->getID()))
				{
					$this->addLogEntry(B2tLog::LOG_AFF_ADD, __("'%release_name%' added", array('%release_name%' => $build->getName())));
					$this->addSystemComment(__("Affected releases"), __('[b]%release_name%[/b] has been added to the list of affected releases', array('%release_name%' => $build->getName())), BUGScontext::getUser()->getUID());
				}
				return $retval;
			}
			return false;
		}
	
		/**
		 * Add an edition to the list of affected editions
		 * 
		 * @param BUGSedition $edition The edition to add
		 * 
		 * @return boolean
		 */
		public function addAffectedEdition($edition)
		{
			if ($this->getProject() && $this->getProject()->isEditionsEnabled())
			{
				if ($retval = B2DB::getTable('B2tIssueAffectsEdition')->setIssueAffected($this->getID(), $edition->getID()))
				{
					$this->addLogEntry(B2tLog::LOG_AFF_ADD, __("'%edition_name%' added", array('%edition_name%' => $edition->getName())));
					$this->addSystemComment(__("Affected editions"), __('[b]%edition_name%[/b] has been added to the list of affected editions', array('%edition_name%' => $edition->getName())), BUGScontext::getUser()->getUID());
				}
				return $retval;
			}
			return false;
		}
	
		/**
		 * Add a component to the list of affected components
		 * 
		 * @param BUGScomponent $component The component to add
		 * 
		 * @return boolean
		 */
		public function addAffectedComponent($component)
		{
			if ($this->getProject() && $this->getProject()->isComponentsEnabled())
			{
				if ($retval = B2DB::getTable('B2tIssueAffectsComponent')->setIssueAffected($this->getID(), $component->getID()))
				{
					$this->addLogEntry(B2tLog::LOG_AFF_ADD, __("'%component_name%' added", array('%component_name%' => $component->getName())));
					$this->addSystemComment(__("Affected components"), __('[b]%component_name%[/b] has been added to the list of affected components', array('%component_name%' => $component->getName())), BUGScontext::getUser()->getUID());
				}
				return $retval;
			}
			return false;
		}
		
		/**
		 * Remove an affected edition, component or version
		 * 
		 * @see removeAffectedEdition()
		 * @see removeAffectedBuild()
		 * @see removeAffectedComponent()
		 * 
		 * @param BUGSversionitem $item The item you want to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedItem($item)
		{
			switch (get_class($item))
			{
				case 'BUGSedition':
					return $this->removeAffectedEdition($item);
					break;
				case 'BUGSbuild':
					return $this->removeAffectedBuild($item);
					break;
				case 'BUGScomponent':
					return $this->removeAffectedComponent($item);
					break;
			}
			
			return false;
		}
		
		/**
		 * Remove an affected edition
		 * 
		 * @see removeAffectedItem()
		 * @see removeAffectedBuild()
		 * @see removeAffectedComponent()
		 * 
		 * @param BUGSedition $item The edition to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedEdition($item)
		{
			if (B2DB::getTable('B2tIssueAffectsEdition')->deleteByIssueIDandEditionID($this->getID(), $item->getID()))
			{
				$aff_name = $item->getName();
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' removed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(__("Affected editions"), __("[s][b]%edition_name%[/b] has been removed from the list of affected editions[/s]", array('%edition_name%' => $item->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected build
		 *
		 * @see removeAffectedItem()
		 * @see removeAffectedEdition()
		 * @see removeAffectedComponent()
		 * 
		 * @param BUGSbuild $item The build to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedBuild($item)
		{
			if (B2DB::getTable('B2tIssueAffectsBuild')->deleteByIssueIDandBuildID($this->getID(), $item->getID()))
			{
				$aff_name = $item->getName();
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' removed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(__("Affected releases"), __("[s][b]%release_name%[/b] has been removed from the list of affected releases[/s]", array('%release_name%' => $item->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected component
		 *
		 * @see removeAffectedItem()
		 * @see removeAffectedEdition()
		 * @see removeAffectedBuild()
		 * 
		 * @param BUGScomponent $item The component to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedComponent($item)
		{
			if (B2DB::getTable('B2tIssueAffectsComponent')->deleteByIssueIDandComponentID($this->getID(), $item->getID()))
			{
				$aff_name = $item->getName();
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' removed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(__("Affected components"), __("[s][b]%component_name%[/b] has been removed from the list of affected components[/s]", array('%component_name%' => $item->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}

		/**
		 * Mark an affected edition, build or component as confirmed or unconfirmed
		 * 
		 * @see confirmAffectedEdition()
		 * @see confirmAffectedBuild()
		 * @see confirmAffectedComponent()
		 * 
		 * @param BUGSversionitem $item The item to mark as confirmed/unconfirmed
		 * @param boolean $confirmed[optional] Confirmed or unconfirumed
		 * 
		 * @return boolean
		 */
		public function confirmAffectedItem($item, $confirmed = true)
		{
			switch (get_class($item))
			{
				case 'BUGSedition':
					return $this->confirmAffectedEdition($item, $confirmed);
					break;
				case 'BUGSbuild':
					return $this->confirmAffectedBuild($item, $confirmed);
					break;
				case 'BUGScomponent':
					return $this->confirmAffectedComponent($item, $confirmed);
					break;
			}
			return false;
		}
		
		/**
		 * Remove an affected edition
		 * 
		 * @see confirmAffectedItem()
		 * @see confirmAffectedBuild()
		 * @see confirmAffectedComponent()
		 * 
		 * @param BUGSedition $item The edition to remove
		 * @param boolean $confirmed[optional] Whether it's confirmed or not
		 * 
		 * @return boolean
		 */
		public function confirmAffectedEdition($item, $confirmed = true)
		{
			if (B2DB::getTable('B2tIssueAffectsEdition')->confirmByIssueIDandEditionID($this->getID(), $item->getID(), $confirmed))
			{
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' confirmed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(__("Affected editions"), __("[b]%edition_name%[/b] has been confirmed for this issue", array('%edition_name%' => $item->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected build
		 *
		 * @see confirmAffectedItem()
		 * @see confirmAffectedEdition()
		 * @see confirmAffectedComponent()
		 * 
		 * @param BUGSbuild $item The build to remove
		 * @param boolean $confirmed[optional] Whether it's confirmed or not
		 * 
		 * @return boolean
		 */
		public function confirmAffectedBuild($item, $confirmed = true)
		{
			if (B2DB::getTable('B2tIssueAffectsBuild')->confirmByIssueIDandBuildID($this->getID(), $item->getID(), $confirmed))
			{
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' confirmed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(__("Affected releases"), __("[b]%release_name%[/b] has been confirmed for this issue", array('%release_name%' => $item->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected component
		 *
		 * @see confirmAffectedItem()
		 * @see confirmAffectedEdition()
		 * @see confirmAffectedBuild()
		 * 
		 * @param BUGScomponent $item The component to remove
		 * @param boolean $confirmed[optional] Whether it's confirmed or not
		 * 
		 * @return boolean
		 */
		public function confirmAffectedComponent($item, $confirmed = true)
		{
			if (B2DB::getTable('B2tIssueAffectsComponent')->confirmByIssueIDandComponentID($this->getID(), $item->getID(), $confirmed))
			{
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' confirmed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(__("Affected components"), __("[b]%component_name%[/b] has been confirmed for this issue", array('%component_name%' => $item->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Set the status of an affected item for this issue
		 * 
		 * @param BUGSversionitem $item The item to set status for
		 * @param BUGSdatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedItemStatus($item, $status)
		{
			switch (get_class($item))
			{
				case 'BUGSedition':
					return $this->setAffectedEditionStatus($item, $status);
					break;
				case 'BUGSbuild':
					return $this->setAffectedBuildStatus($item, $status);
					break;
				case 'BUGScomponent':
					return $this->setAffectedComponentStatus($item, $status);
					break;
			}
			return false;
		}

		/**
		 * Set status for affected edition
		 * 
		 * @see setAffectedItemStatus()
		 * @see setAffectedBuildStatus()
		 * @see setAffectedComponentStatus()
		 * 
		 * @param BUGSedition $item The edition to set status for
		 * @param BUGSdatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedEditionStatus($item, $status)
		{
			if (B2DB::getTable('B2tIssueAffectsEdition')->setStatusByIssueIDandEditionID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				$this->addSystemComment(__("Affected editions"), __("[b]%edition_name%[/b] has been set to status '%status_name% for this issue", array('%edition_name%' => $item->getName(), '%status_name%' => $status->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Set status for affected build
		 * 
		 * @see setAffectedItemStatus()
		 * @see setAffectedEditionStatus()
		 * @see setAffectedComponentStatus()
		 * 
		 * @param BUGSbuild $item The build to set status for
		 * @param BUGSdatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedBuildStatus($item, $status)
		{
			if (B2DB::getTable('B2tIssueAffectsBuild')->setStatusByIssueIDandBuildID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				$this->addSystemComment(__("Affected releases"), __("[b]%release_name%[/b] has been set to status '%status_name% for this issue", array('%release_name%' => $item->getName(), '%status_name%' => $status->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
				
		/**
		 * Set status for affected component
		 * 
		 * @see setAffectedItemStatus()
		 * @see setAffectedBuildStatus()
		 * @see setAffectedEditionStatus()
		 * 
		 * @param BUGScomponent $item The component to set status for
		 * @param BUGSdatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedComponentStatus($item, $status)
		{
			if (B2DB::getTable('B2tIssueAffectsComponent')->setStatusByIssueIDandComponentID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(B2tLog::LOG_AFF_DELETE, __("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				$this->addSystemComment(__("Affected components"), __("[b]%component_name%[/b] has been set to status '%status_name% for this issue", array('%component_name%' => $item->getName(), '%status_name%' => $status->getName())), BUGScontext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Adds a task and returns the task id
		 *
		 * @param string $title
		 * @param string $desc
		 * 
		 * @return BUGStask The task that was created
		 */
		public function addTask($title, $desc)
		{
			$task = BUGStask::createTask($title, $desc, $this->getID());
			
			$this->addLogEntry(B2tLog::LOG_TASK_ADD, __("Added task '%task_title%'", array('%task_title%' => $title)));
			$this->addSystemComment(__("Task added"), __("The task '%task_title%' has been added", array('%task_title%' => $title)), BUGScontext::getUser()->getUID());
	
			return $task;
		}
	
		/**
		 * Returns userids which have some kind of connection to this issue and should be notified on change
		 *
		 * @return array
		 */
		public function getRelatedUIDs()
		{
			if ($this->_related_users === null)
			{
				$this->_related_users = $this->_getRelatedUsers();
			}
			return $this->_related_users;
		}
		
		/**
		 * Retrieves all users related to this issue:
		 * 		- Users subscribing to this issue
		 * 		- Issue poster
		 * 		- Issue owner
		 * 		- Issue assignee
		 * 		- Project + edition owner
		 * 		- Project + edition qa
		 * 		- Project + edition + component developers
		 * 
		 * @return array
		 */
		protected function _getRelatedUsers()
		{
			$uids = array();
	
			// Add all users who's marked this issue as interesting
			$uids = array_merge($uids, B2DB::getTable('B2tUserIssues')->getUserIDsByIssueID($this->getID()));
	
			// Add all users from the team owning the issue if valid
			// or add the owning user if a user owns the issue
			if ($this->getOwnerType() == BUGSidentifiableclass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getOwner()->getMemberIDs());
			}
			elseif ($this->getOwnerType() == BUGSidentifiableclass::TYPE_USER)
			{
				$uids[] = $this->getOwner()->getID();
			}
	
			// Add all users from the team assigned to the issue if valid
			// or add the assigned user if a user is assigned to the issue
			if ($this->getAssigneeType() == BUGSidentifiableclass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getAssignee()->getMemberIDs());
			}
			elseif ($this->getAssigneeType() == BUGSidentifiableclass::TYPE_USER)
			{
				$uids[] = $this->getAssignee()->getID();
			}
			
			// Add all users assigned to a project
			$uids = array_merge($uids, $this->getProject()->getAssignees());
			
			// Add all users in the team who leads the project, if valid
			// or add the user who leads the project, if valid
			if ($this->getProject()->getLeaderType() == BUGSidentifiableclass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getProject()->getLeadBy()->getMembers());
			}
			elseif ($this->getProject()->getLeaderType() == BUGSidentifiableclass::TYPE_USER)
			{
				$uids[] = $this->getProject()->getLeaderID();
			}
	
			// Same for QA
			if ($this->getProject()->getQAType() == BUGSidentifiableclass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getProject()->getQA()->getMembers());
			}
			elseif ($this->getProject()->getQAType() == BUGSidentifiableclass::TYPE_USER)
			{
				$uids[] = $this->getProject()->getQA()->getID();
			}
			
			// Add all users relevant for all affected editions
			foreach ($this->getEditions() as $edition_list)
			{
				if ($edition_list['edition']->getLeadType() == BUGSidentifiableclass::TYPE_TEAM)
				{
					$uids = array_merge($uids, $edition_list['edition']->getLeadBy()->getMembers());
				}
				elseif ($edition_list['edition']->getLeadType() == BUGSidentifiableclass::TYPE_USER)
				{
					$uids[] = $edition_list['edition']->getLeadBy()->getID();
				}
				
				if ($edition_list['edition']->getQAType() == BUGSidentifiableclass::TYPE_TEAM)
				{
					$uids = array_merge($uids, $edition_list['edition']->getQA()->getMembers());
				}
				elseif ($edition_list['edition']->getQAType() == BUGSidentifiableclass::TYPE_USER)
				{
					$uids[] = $edition_list['edition']->getQA()->getID();
				}
				$uids = array_merge($uids, $edition_list['edition']->getAssigneeIDs());
			}
			
			// Add all users relevant for all affected components
			foreach ($this->getComponents() as $component_list)
			{
				$uids = array_merge($uids, $component_list['component']->getAssigneeIDs());
			}
			
			// Add the user who posted the issue
			$uids[] = $this->getPostedBy()->getID();
			
			return array_unique($uids);
		}
	
		/**
		 * Updates the issue's last_updated time to "now"
		 */
		public function updateTime()
		{
			$this->_addChangedProperty('_last_updated', $_SERVER["REQUEST_TIME"]);
		}

		/**
		 * Delete this issue
		 */
		public function deleteIssue()
		{
			$this->_deleted = true;
		}
	
		/**
		 * Adds a log entry
		 * 
		 * @param integer $change_type Type of log entry
		 * @param string $text The text to log
		 * @param boolean $system Whether this is a user entry or a system entry
		 */
		public function addLogEntry($change_type, $text = null, $system = false)
		{
			$uid = ($system) ? 0 : BUGScontext::getUser()->getUID();
			B2DB::getTable('B2tLog')->createNew($this->getID(), B2tLog::TYPE_ISSUE, $change_type, $text, $uid);
		}
	
		/**
		 * Adds a system comment
		 * 
		 * @param string $title Comment title
		 * @param string $text Comment text
		 * @param integer $uid The user ID that posted the comment
		 * 
		 * @return BUGScomment
		 */
		public function addSystemComment($title, $text, $uid)
		{
			if (!BUGSsettings::isCommentTrailClean())
			{
				$comment = BUGSComment::createNew($title, $text, $uid, $this->getID(), BUGSComment::TYPE_ISSUE, 'core', true, true, false);
				BUGScontext::trigger('core', 'issue_comment_posted', array($this, $comment));
				return $comment;
			}
			return false;
		}
	
		/**
		 * Return an array with all the links:
		 * 		'id' => array('url', 'description')
		 * 
		 * @return array
		 */
		public function getLinks()
		{
			$this->_populateLinks();
			return $this->_links;
		}
		
		/**
		 * Populate the internal links array
		 */
		protected function _populateLinks()
		{
			if ($this->_links === null)
			{
				$this->_links = B2DB::getTable('B2tLinks')->getByIssueID($this->getID());
			}
		}
	
		/**
		 * Remove a link
		 * 
		 * @param integer $link_id The link ID to remove
		 */
		public function removeLink($link_id)
		{
			if ($res = B2DB::getTable('B2tLinks')->removeByIssueIDandLinkID($this->getID(), $link_id))
			{
				if (is_array($this->_links) && array_key_exists($link_id, $this->_links))
				{
					unset($this->_links[$link_id]);
				}
			}
		}
		
		/**
		 * Return an array with all files attached to this issue
		 * 
		 * @return array
		 */
		public function getFiles()
		{
			$this->_populateFiles();
			return $this->_files;
		}
		
		/**
		 * Populate the files array
		 */
		protected function _populateFiles()
		{
			if ($this->_files === null)
			{
				$this->_files = B2DB::getTable('B2tFiles')->getByIssueID($this->getID());
			}
		}
		
		/**
		 * Remove a file
		 * 
		 * @param integer $file_id The file ID
		 * 
		 * @return boolean
		 */
		public function removeFile($file_id)
		{
			if ($res = B2DB::getTable('B2tFiles')->removeByIssueIDandFileID($this->getID(), $file_id))
			{
				if (is_array($this->_files) && array_key_exists($file_id, $this->_files))
				{
					unset($this->_files[$file_id]);
				}
				unlink(BUGScontext::getIncludePath() . 'files/' . $row->get(B2tFiles::FILENAME));
				return true;
			}
			return false;
		}
	
		/**
		 * Retrieve all log entries for this issue
		 * 
		 * @return array
		 */
		public function getLogEntries()
		{
			$this->_populateLogEntries();
			return $this->_log_entries;
		}
		
		/**
		 * Populate log entries array
		 */
		protected function _populateLogEntries()
		{
			if ($this->_log_entries === null)
			{
				$this->_log_entries = B2DB::getTable('B2tLog')->getByIssueID($this->getID()); 
			}
		}

		/**
		 * Mark issue as blocking or not blocking
		 * 
		 * @param boolean $blocking[optional] Whether it's blocking or not
		 */
		public function setBlocking($blocking = true)
		{
			$this->_addChangedProperty('_blocking', (bool) $blocking);
		}
		
		/**
		 * Return whether the issue is blocking the next release or not
		 * 
		 * @return boolean
		 */
		public function isBlocking()
		{
			return $this->_blocking;
		}
		
		/**
		 * Retrieve all comments for this issue
		 * 
		 * @return array
		 */
		public function getComments()
		{
			$this->_populateComments();
			return $this->_comments;
		}
		
		/**
		 * Populate comments array
		 */
		protected function _populateComments()
		{
			if ($this->_comments === null)
			{
				$this->_comments = BUGSComment::getComments($this->getID(), BUGSComment::TYPE_ISSUE);
			}
		}
		
		/**
		 * Return the number of comments
		 * 
		 * @return integer
		 */
		public function getCommentCount()
		{
			if ($this->_comments === null)
			{
				return BUGSComment::countComments($this->getID(), BUGSComment::TYPE_ISSUE);
			}
			else
			{
				return count($this->getComments());
			}
		}
		
		/**
		 * Return whether or not a specific field is visible
		 *  
		 * @param string $fieldname the fieldname key
		 * 
		 * @return boolean
		 */
		protected function isFieldVisible($fieldname)
		{
			$fields_array = $this->getProject()->getVisibleFieldsArray($this->getIssueType()->getID());
			return array_key_exists($fieldname, $fields_array);
		}
		
		/**
		 * Return whether or not the "description" field is visible
		 * 
		 * @return boolean
		 */
		public function isDescriptionVisible()
		{
			return (bool) ($this->isFieldVisible('description') || $this->getDescription() != '');
		} 

		/**
		 * Return whether or not the "reproduction steps" field is visible
		 * 
		 * @return boolean
		 */
		public function isReproductionStepsVisible()
		{
			return (bool) ($this->isFieldVisible('reproduction_steps') || $this->getReproductionSteps());
		} 
		
		/**
		 * Return whether or not the "category" field is visible
		 * 
		 * @return boolean
		 */
		public function isCategoryVisible()
		{
			return (bool) ($this->isFieldVisible('category') || $this->getCategory() instanceof BUGSdatatype);
		} 

		/**
		 * Return whether or not the "resolution" field is visible
		 * 
		 * @return boolean
		 */
		public function isResolutionVisible()
		{
			return (bool) ($this->isFieldVisible('resolution') || $this->getResolution() instanceof BUGSdatatype);
		} 

		/**
		 * Return whether or not the "editions" field is visible
		 * 
		 * @return boolean
		 */
		public function isEditionsVisible()
		{
			return (bool) ($this->isFieldVisible('edition') || count($this->getEditions()) > 0);
		} 

		/**
		 * Return whether or not the "builds" field is visible
		 * 
		 * @return boolean
		 */
		public function isBuildsVisible()
		{
			return (bool) ($this->isFieldVisible('build') || count($this->getBuilds()) > 0);
		} 
		
		/**
		 * Return whether or not the "components" field is visible
		 * 
		 * @return boolean
		 */
		public function isComponentsVisible()
		{
			return (bool) ($this->isFieldVisible('component') || count($this->getComponents()) > 0);
		} 
		
		/**
		 * Return whether or not the "reproducability" field is visible
		 * 
		 * @return boolean
		 */
		public function isReproducabilityVisible()
		{
			return (bool) ($this->isFieldVisible('reproducability') || $this->getReproducability() instanceof BUGSdatatype);
		} 
		
		/**
		 * Return whether or not the "severity" field is visible
		 * 
		 * @return boolean
		 */
		public function isSeverityVisible()
		{
			return (bool) ($this->isFieldVisible('severity') || $this->getSeverity() instanceof BUGSdatatype);
		} 
		
		/**
		 * Return whether or not the "priority" field is visible
		 * 
		 * @return boolean
		 */
		public function isPriorityVisible()
		{
			return (bool) ($this->isFieldVisible('priority') || $this->getPriority() instanceof  BUGSdatatype);
		} 
		
		/**
		 * Return whether or not the "estimated time" field is visible
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeVisible()
		{
			return (bool) ($this->isFieldVisible('estimated_time') || $this->getEstimatedTime() > 0);
		} 
		
		/**
		 * Return whether or not the "spent time" field is visible
		 * 
		 * @return boolean
		 */
		public function isSpentTimeVisible()
		{
			return (bool) ($this->isFieldVisible('spent_time') || $this->getSpentTime() > 0);
		} 
		
		/**
		 * Return whether or not the "milestone" field is visible
		 * 
		 * @return boolean
		 */
		public function isMilestoneVisible()
		{
			return (bool) ($this->isFieldVisible('milestone') || $this->getMilestone() instanceof BUGSmilestone);
		} 

		/**
		 * Return whether or not the "percent_complete" field is visible
		 * 
		 * @return boolean
		 */
		public function isPercentCompletedVisible()
		{
			return (bool) ($this->isFieldVisible('percent_complete') || $this->getPercentCompleted() > 0);
		}

		/**
		 * Return the time when the issue was closed
		 * 
		 * @return false if closed, otherwise a timestamp
		 */
		public function whenClosed()
		{
			if (!$this->isClosed()) return false;
			$crit = new B2DBCriteria();
			$crit->addSelectionColumn(B2tLog::TIME);
			$crit->addWhere(B2tLog::TARGET, $this->_issue_uniqueid);
			$crit->addWhere(B2tLog::TARGET_TYPE, 1);
			$crit->addWhere(B2tLog::CHANGE_TYPE, 14);
			$crit->addOrderBy(B2tLog::TIME, 'desc');
			$res = B2DB::getTable('B2tLog')->doSelect($crit);
			
			$ret_arr = array();

			$row = $res->getNextRow();
			return($row->get(B2tLog::TIME));
		}	

		/**
		 * Return the time when the issue was reopened
		 * 
		 * @return false if closed, otherwise a timestamp
		 */
		public function whenReopened()
		{
			if ($this->isClosed()) return false;
			$crit = new B2DBCriteria();
			$crit->addSelectionColumn(B2tLog::TIME);
			$crit->addWhere(B2tLog::TARGET, $this->_issue_uniqueid);
			$crit->addWhere(B2tLog::TARGET_TYPE, 1);
			$crit->addWhere(B2tLog::CHANGE_TYPE, 22);
			$crit->addOrderBy(B2tLog::TIME, 'desc');
			$res = B2DB::getTable('B2tLog')->doSelect($crit);
			
			$ret_arr = array();

			if ($res->getNumberOfRows() == 0)
			{
				return false;
			}
			
			$row = $res->getNextRow();
			return($row->get(B2tLog::TIME));
		}	
		
		/**
		 * Save changes made to the issue since last time
		 * 
		 * @return boolean
		 */
		public function save()
		{
			$comment_lines = array();
			$is_saved_estimated = false;
			$is_saved_spent = false;
			$is_saved_assignee = false;
			$is_saved_owner = false;
			foreach ($this->_getChangedProperties() as $property => $value)
			{
				if ($value['original_value'] != $this->$property)
				{
					switch ($property)
					{
						case '_title':
							$this->addLogEntry(B2tLog::LOG_ISSUE_UPDATE, __("Title updated"));
							$comment_lines[] = __("This issue's title has been changed");
							break;
						case '_description':
							$this->addLogEntry(B2tLog::LOG_ISSUE_UPDATE, __("Description updated"));
							$comment_lines[] = __("This issue's description has been changed");
							break;
						case '_reproduction_steps':
							$this->addLogEntry(B2tLog::LOG_ISSUE_REPRODUCABILITY, __("Reproduction steps updated"));
							$comment_lines[] = __("This issue's reproduction steps has been changed");
							break;
						case '_category':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGScategoryLab($value['original_value'])) ? $old_item->getName() : __('Not determined');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getCategory() instanceof BUGSdatatype) ? $this->getCategory()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_CATEGORY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The category has been updated, from <b>%previous_category%</b> to <b>%new_category%</b>.", array('%previous_category%' => $old_name, '%new_category%' => $new_name));
							break;
						case '_status':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGSstatusLab($value['original_value'])) ? $old_item->getName() : __('Unknown');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getStatus() instanceof BUGSdatatype) ? $this->getStatus()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_STATUS, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The status has been updated, from <b>%previous_status%</b> to <b>%new_status%</b>.", array('%previous_status%' => $old_name, '%new_status%' => $new_name));
							break;
						case '_reproducability':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGSreproducabilityLab($value['original_value'])) ? $old_item->getName() : __('Unknown');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getReproducability() instanceof BUGSdatatype) ? $this->getReproducability()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_REPRODUCABILITY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The reproducability has been updated, from <b>%previous_reproducability%</b> to <b>%new_reproducability%</b>.", array('%previous_reproducability%' => $old_name, '%new_reproducability%' => $new_name));
							
							break;
						case '_priority':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGSpriorityLab($value['original_value'])) ? $old_item->getName() : __('Unknown');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getPriority() instanceof BUGSdatatype) ? $this->getPriority()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_PRIORITY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The priority has been updated, from <b>%previous_priority%</b> to <b>%new_priority%</b>.", array('%previous_priority%' => $old_name, '%new_priority%' => $new_name));
							break;
						case '_assignedto':
						case '_assignedtype':
							if (!$is_saved_assignee)
							{
								if ($value['original_value'] != 0)
								{
									if ($this->getChangedPropertyOriginal('_assignedtype') == BUGSidentifiableclass::TYPE_USER)
										$old_identifiable = BUGSfactory::userLab($value['original_value']);
									elseif ($this->getChangedPropertyOriginal('_assignedtype') == BUGSidentifiableclass::TYPE_TEAM)
										$old_identifiable = BUGSfactory::teamLab($value['original_value']);
									$old_name = ($old_identifiable instanceof BUGSidentifiableclass) ? $old_identifiable->getName() : __('Unknown');
								}
								else
								{
									$old_name = __('Not assigned');
								}
								$new_name = ($this->getAssignee() instanceof BUGSidentifiableclass) ? $this->getAssignee()->getName() : __('Not assigned');
								
								$this->addLogEntry(B2tLog::LOG_ISSUE_ASSIGNED, $old_name . ' &rArr; ' . $new_name);
								$comment_lines[] = __("The assignee has been changed, from <b>%previous_name%</b> to <b>%new_name%</b>.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
								$is_saved_assignee = true;
							}
							break;
						case '_postedby':
							$old_identifiable = BUGSfactory::userLab($value['original_value']);
							$old_name = ($old_identifiable instanceof BUGSidentifiableclass) ? $old_identifiable->getName() : __('Unknown');
							$new_name = $this->getPostedBy()->getName();
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_POSTED, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The issue's poster has been changed, from <b>%previous_name%</b> to <b>%new_name%</b>.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
							break;
						case '_ownedby':
						case '_ownedtype':
							if (!$is_saved_owner)
							{
								if ($value['original_value'] != 0)
								{
									if ($this->getChangedPropertyOriginal('_ownedtype') == BUGSidentifiableclass::TYPE_USER)
										$old_identifiable = BUGSfactory::userLab($value['original_value']);
									elseif ($this->getChangedPropertyOriginal('_ownedtype') == BUGSidentifiableclass::TYPE_TEAM)
										$old_identifiable = BUGSfactory::teamLab($value['original_value']);
									$old_name = ($old_identifiable instanceof BUGSidentifiableclass) ? $old_identifiable->getName() : __('Unknown');
								}
								else
								{
									$old_name = __('Not owned by anyone');
								}
								$new_name = ($this->getOwner() instanceof BUGSidentifiableclass) ? $this->getOwner()->getName() : __('Not owned by anyone');
								
								$this->addLogEntry(B2tLog::LOG_ISSUE_OWNED, $old_name . ' &rArr; ' . $new_name);
								$comment_lines[] = __("The owner has been changed, from <b>%previous_name%</b> to <b>%new_name%</b>.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
								$is_saved_assignee = true;
							}
							break;
						case '_percentcompleted':
							$this->addLogEntry(B2tLog::LOG_ISSUE_PERCENT, $value['original_value'] . '% &rArr; ' . $this->getPercentCompleted() . '%');
							$comment_lines[] = __("This issue's progression has been updated to %percent_completed% percent completed.", array('%percent_completed%' => $this->getPercentCompleted()));
							break;
						case '_resolution':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGSresolutionLab($value['original_value'])) ? $old_item->getName() : __('Unknown');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getResolution() instanceof BUGSdatatype) ? $this->getResolution()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_RESOLUTION, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The resolution has been updated, from <b>%previous_resolution%</b> to <b>%new_resolution%</b>.", array('%previous_resolution%' => $old_name, '%new_resolution%' => $new_name));
							break;
						case '_severity':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGSseverityLab($value['original_value'])) ? $old_item->getName() : __('Unknown');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getSeverity() instanceof BUGSdatatype) ? $this->getSeverity()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_SEVERITY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The severity has been updated, from <b>%previous_severity%</b> to <b>%new_severity%</b>.", array('%previous_severity%' => $old_name, '%new_severity%' => $new_name));
							break;
						case '_milestone':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::milestoneLab($value['original_value'])) ? $old_item->getName() : __('Not determined');
							}
							else
							{
								$old_name = __('Not determined');
							}
							$new_name = ($this->getMilestone() instanceof BUGSmilestone) ? $this->getMilestone()->getName() : __('Not determined');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_MILESTONE, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The milestone has been updated, from <b>%previous_milestone%</b> to <b>%new_milestone%</b>.", array('%previous_milestone%' => $old_name, '%new_milestone%' => $new_name));
							break;
						case '_issuetype':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = BUGSfactory::BUGSissuetypeLab($value['original_value'])) ? $old_item->getName() : __('Unknown');
							}
							else
							{
								$old_name = __('Unknown');
							}
							$new_name = ($this->getIssuetype() instanceof BUGSissuetype) ? $this->getIssuetype()->getName() : __('Unknown');
							
							$this->addLogEntry(B2tLog::LOG_ISSUE_ISSUETYPE, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = __("The issue type has been updated, from <b>%previous_type%</b> to <b>%new_type%</b>.", array('%previous_type%' => $old_name, '%new_type%' => $new_name));
							break;
						case '_estimatedmonths':
						case '_estimatedweeks':
						case '_estimateddays':
						case '_estimatedhours':
						case '_estimatedpoints':
							if (!$is_saved_estimated)
							{
								$old_time = array('months' => $this->getChangedPropertyOriginal('_estimatedmonths'),
													'weeks' => $this->getChangedPropertyOriginal('_estimatedweeks'),
													'days' => $this->getChangedPropertyOriginal('_estimateddays'),
													'hours' => $this->getChangedPropertyOriginal('_estimatedhours'),
													'points' => $this->getChangedPropertyOriginal('_estimatedpoints'));

								$this->addLogEntry(B2tLog::LOG_ISSUE_TIME_ESTIMATED, $this->getFormattedTime($old_time) . ' &rArr; ' . $this->getFormattedTime($this->getEstimatedTime()));
								$comment_lines[] = __("The issue has been (re-)estimated, from <b>%previous_time%</b> to <b>%new_time%</b>.", array('%previous_time%' => $this->getFormattedTime($old_time), '%new_time%' => $this->getFormattedTime($this->getEstimatedTime())));
								$is_saved_estimated = true;
							}
							break;
						case '_spentmonths':
						case '_spentweeks':
						case '_spentdays':
						case '_spenthours':
						case '_spentpoints':
							if (!$is_saved_spent)
							{
								$old_time = array('months' => $this->getChangedPropertyOriginal('_spentmonths'),
													'weeks' => $this->getChangedPropertyOriginal('_spentweeks'),
													'days' => $this->getChangedPropertyOriginal('_spentdays'),
													'hours' => $this->getChangedPropertyOriginal('_spenthours'),
													'points' => $this->getChangedPropertyOriginal('_spentpoints'));

								$this->addLogEntry(B2tLog::LOG_ISSUE_TIME_SPENT, $this->getFormattedTime($old_time) . ' &rArr; ' . $this->getFormattedTime($this->getSpentTime()));
								$comment_lines[] = __("Time spent on this issue, from <b>%previous_time%</b> to <b>%new_time%</b>.", array('%previous_time%' => $this->getFormattedTime($old_time), '%new_time%' => $this->getFormattedTime($this->getSpentTime())));
								$is_saved_spent = true;
							}
							break;
						case '_state':
							if ($this->isClosed())
							{
								$this->addLogEntry(B2tLog::LOG_ISSUE_CLOSE);
								$comment_lines[] = __("This issue has been closed");
								if ($this->getMilestone() instanceof BUGSmilestone)
								{
									$this->getMilestone()->updateStatus();
								}
							}
							else
							{
								$this->addLogEntry(B2tLog::LOG_ISSUE_REOPEN);
								$comment_lines[] = __("This issue has been reopened");
							}
							break;
					}
				}
			}
			$this->addSystemComment(__('Issue updated'), join("\n", $comment_lines), BUGScontext::getUser()->getUID());
			$this->_clearChangedProperties();
			
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssues::TITLE, $this->_title);
			$crit->addUpdate(B2tIssues::LAST_UPDATED, $this->_last_updated);
			$crit->addUpdate(B2tIssues::LONG_DESCRIPTION, $this->_description);
			$crit->addUpdate(B2tIssues::REPRODUCTION, $this->_reproduction_steps);
			$crit->addUpdate(B2tIssues::ISSUE_TYPE, (is_object($this->_issuetype)) ? $this->_issuetype->getID() : $this->_issuetype);
			$crit->addUpdate(B2tIssues::RESOLUTION, (is_object($this->_resolution)) ? $this->_resolution->getID() : $this->_resolution);
			$crit->addUpdate(B2tIssues::STATE, $this->_state);
			$crit->addUpdate(B2tIssues::POSTED_BY, (is_object($this->_postedby)) ? $this->_postedby->getID() : $this->_postedby);
			$crit->addUpdate(B2tIssues::OWNED_BY, (is_object($this->_ownedby)) ? $this->_ownedby->getID() : $this->_ownedby);
			$crit->addUpdate(B2tIssues::OWNED_TYPE, $this->_ownedtype);
			$crit->addUpdate(B2tIssues::ASSIGNED_TO, (is_object($this->_assignedto)) ? $this->_assignedto->getID() : $this->_assignedto);
			$crit->addUpdate(B2tIssues::ASSIGNED_TYPE, $this->_assignedtype);
			$crit->addUpdate(B2tIssues::STATUS, (is_object($this->_status)) ? $this->_status->getID() : $this->_status);
			$crit->addUpdate(B2tIssues::PRIORITY, (is_object($this->_priority)) ? $this->_priority->getID() : $this->_priority);
			$crit->addUpdate(B2tIssues::CATEGORY, (is_object($this->_category)) ? $this->_category->getID() : $this->_category);
			$crit->addUpdate(B2tIssues::REPRODUCABILITY, (is_object($this->_reproducability)) ? $this->_reproducability->getID() : $this->_reproducability);
			$crit->addUpdate(B2tIssues::ESTIMATED_MONTHS, $this->_estimatedmonths);
			$crit->addUpdate(B2tIssues::ESTIMATED_WEEKS, $this->_estimatedweeks);
			$crit->addUpdate(B2tIssues::ESTIMATED_DAYS, $this->_estimateddays);
			$crit->addUpdate(B2tIssues::ESTIMATED_HOURS, $this->_estimatedhours);
			$crit->addUpdate(B2tIssues::ESTIMATED_POINTS, $this->_estimatedpoints);
			$crit->addUpdate(B2tIssues::SPENT_MONTHS, $this->_spentmonths);
			$crit->addUpdate(B2tIssues::SPENT_WEEKS, $this->_spentweeks);
			$crit->addUpdate(B2tIssues::SPENT_DAYS, $this->_spentdays);
			$crit->addUpdate(B2tIssues::SPENT_HOURS, $this->_spenthours);
			$crit->addUpdate(B2tIssues::SPENT_POINTS, $this->_spentpoints);
			$crit->addUpdate(B2tIssues::SCRUMCOLOR, $this->_scrumcolor);
			$crit->addUpdate(B2tIssues::PERCENT_COMPLETE, $this->_percentcompleted);
			$crit->addUpdate(B2tIssues::DUPLICATE, (is_object($this->_duplicateof)) ? $this->_duplicateof->getID() : $this->_duplicateof);
			$crit->addUpdate(B2tIssues::DELETED, $this->_deleted);
			$crit->addUpdate(B2tIssues::BLOCKING, $this->_blocking);
			$crit->addUpdate(B2tIssues::USER_WORKING_ON, (is_object($this->_being_worked_on_by)) ? $this->_being_worked_on_by->getID() : $this->_being_worked_on_by);
			$crit->addUpdate(B2tIssues::USER_WORKED_ON_SINCE, $this->_being_worked_on_since);
			$crit->addUpdate(B2tIssues::MILESTONE, (is_object($this->_milestone)) ? $this->_milestone->getID() : $this->_milestone);
			$res = B2DB::getTable('B2tIssues')->doUpdateById($crit, $this->getID());
			$this->getProject()->clearRecentActivities();

			return true;
		}

		/**
		 * Return the user working on this issue if any
		 *
		 * @return BUGSuser
		 */
		public function getUserWorkingOnIssue()
		{
			if (is_numeric($this->_being_worked_on_by))
			{
				try
				{
					$this->_being_worked_on_by = BUGSfactory::userLab($this->_being_worked_on_by);
				}
				catch (Exception $e)
				{
					$this->_being_worked_on_by = null;
				}
			}
	
			return $this->_being_worked_on_by;
		}
		
		/**
		 * Clear the user currently working on this issue
		 * 
		 * @return null
		 */
		public function clearUserWorkingOnIssue()
		{
			$this->_being_worked_on_by = null;
			$this->_being_worked_on_since = null;
		}
		
		/**
		 * Register a user as working on the issue
		 * 
		 * @param BUGSuser $user
		 */
		public function startWorkingOnIssue($user)
		{
			$this->_being_worked_on_by = $user;
			$this->_being_worked_on_since = $_SERVER['REQUEST_TIME'];
		}
		
		/**
		 * Stop working on the issue, and save time spent
		 * 
		 * @return null
		 */
		public function stopWorkingOnIssue()
		{
			$time_spent = $_SERVER['REQUEST_TIME'] - $this->_being_worked_on_since;
			$user_working_on_it = $this->getUserWorkingOnIssue();
			$this->clearUserWorkingOnIssue();
			if ($time_spent > 0)
			{
				$weeks_spent = 0;
				$days_spent = 0;
				$hours_spent = 0;
				$time_spent = ceil($time_spent / 3600);
				if ($time_spent >= $this->getProject()->getHoursPerDay())
				{
					$days_spent = 1 + (int) floor($time_spent / 24);
					if ($days_spent >= 7)
					{
						$weeks_spent = floor($days_spent / 7);
						$days_spent -= ($weeks_spent * 7);
					}
				}
				$hours_spent = $time_spent - ($days_spent * 24);
				if ($hours_spent < 0) $hours_spent = 0;
				$this->_addChangedProperty('_spenthours', $this->_spenthours + $hours_spent);
				$this->_addChangedProperty('_spentdays', $this->_spentdays + $days_spent);
				$this->_addChangedProperty('_spentweeks', $this->_spentweeks + $weeks_spent);
			}
		}
		
		/**
		 * Return whether or not this issue is being worked on by a user
		 * 
		 * @return boolean
		 */
		public function isBeingWorkedOn()
		{
			return ($this->getUserWorkingOnIssue() instanceof BUGSuser) ? true : false;
		}
		
		public function getWorkedOnSince()
		{
			return $this->_being_worked_on_since;
		}
		
	}
