<?php

	/**
	 * Project class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Project class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class BUGSproject extends BUGSversionitem 
	{
		
		const TIME_UNIT_HOURS = 0;
		const TIME_UNIT_HOURS_DAYS = 1;
		const TIME_UNIT_HOURS_DAYS_WEEKS = 2;
		const TIME_UNIT_DAYS = 3;
		const TIME_UNIT_DAYS_WEEKS = 4;
		const TIME_UNIT_WEEKS = 5;
		const TIME_UNIT_POINTS = 6;
		const TIME_UNIT_POINTS_HOURS = 7;
		
		/**
		 * The project prefix
		 *
		 * @var string
		 * @access protected
		 */
		protected $_prefix = '';
		
		/**
		 * Whether or not the project uses prefix
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_useprefix = false;
		
		/**
		 * Hours per day for this project
		 *
		 * @var integer
		 */
		protected $_hrsprday = 7;

		/**
		 * Time unit for this project
		 *
		 * @var integer
		 */
		protected $_timeunit;
		
		/**
		 * Whether or not you can vote for issues for this project
		 *
		 * @var boolean
		 */
		protected $_enablevotes = null;
		
		/**
		 * Whether or not the project uses tasks for its issues
		 *
		 * @var boolean
		 */
		protected $_enabletasks = null;

		/**
		 * Whether or not the project uses builds
		 *
		 * @var boolean
		 */
		protected $_enablebuilds = null;
		
		/**
		 * Edition builds
		 *
		 * @var array|BUGSbuild
		 */
		protected $_builds = null;
		
		/**
		 * Whether or not the project uses editions
		 *
		 * @var boolean
		 */
		protected $_enableeditions = null;
		
		/**
		 * Whether or not the project uses components
		 *
		 * @var boolean
		 */
		protected $_enablecomponents = null;
		
		
		/**
		 * List of editions for this project
		 *
		 * @var array
		 */
		protected $_editions = null;
		
		/**
		 * The projects homepage 
		 * 
		 * @var string
		 */
		protected $_homepage = '';
		
		/**
		 * The projects default status
		 * 
		 * @var BUGSdatatype
		 */
		protected $_defaultstatus = 0;
		
		/**
		 * List of milestones for this project
		 *
		 * @var array
		 */
		protected $_milestones = null;
		
		/**
		 * List of components for this project
		 *
		 * @var array
		 */
		protected $_components = null;
		
		/**
		 * List of issues registered for this project
		 * 
		 * @var integer
		 */
		protected $_issues = null;
		
		/**
		 * The lead type for the project, BUGSidentifiableclass::TYPE_USER or BUGSidentifiableclass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_leadtype = 0;

		/**
		 * The lead for the project
		 *  
		 * @var BUGSidentifiable
		 */
		protected $_leadby = 0;
		
		/**
		 * The QA type for the project, BUGSidentifiableclass::TYPE_USER or BUGSidentifiableclass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_qatype = 0;
		
		/**
		 * The QA for the project
		 *  
		 * @var BUGSidentifiable
		 */
		protected $_qaby = 0;
		
		/**
		 * The owner type for the project, BUGSidentifiableclass::TYPE_USER or BUGSidentifiableclass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_ownedtype = 0;
		
		/**
		 * The owner of the project
		 *  
		 * @var BUGSidentifiable
		 */
		protected $_ownedby = 0;
		
		/**
		 * The projects documentation URL
		 * 
		 * @var string
		 */
		protected $_doc_url = '';
		
		/**
		 * The project description
		 * 
		 * @var string
		 */
		protected $_description = '';
		
		/**
		 * Available / applicable issue types for this project
		 * 
		 * @var array
		 */
		protected $_issuetypes = null;
		
		/**
		 * Issue types visible in the frontpage summary
		 * 
		 * @var array
		 */
		protected $_visible_issuetypes = null;

		/**
		 * Milestones visible in the frontpage summary
		 * 
		 * @var array
		 */
		protected $_visible_milestones = null;
		
		/**
		 * Whether or not this project is visible in the frontpage summary
		 * 
		 * @var boolean
		 */
		protected $_show_in_summary = null;
		
		/**
		 * What to show on the frontpage summary
		 * 
		 * @var string
		 */
		protected $_summary_display = null;
		
		/**
		 * List of assigned users, teams and customers
		 * 
		 * @var array
		 */
		protected $_assignees = null;

		/**
		 * List of issue fields per issue type
		 * 
		 * @var array
		 */
		protected $_fieldsarrays = array();
		
		/**
		 * Whether a user can change details about an issue without working on the issue
		 * 
		 * @var boolean
		 */
		protected $_can_change_wo_working = null;
		
		/**
		 * Project list cache
		 * 
		 * @var array
		 */
		static protected $_projects = null;

		/**
		 * Is project deleted
		 * 
		 * @var boolean
		 */
		protected $_deleted = 0;

		/**
		 * Make a project default
		 * 
		 * @param $p_id integer The id for the default project
		 * 
		 * @return boolean
		 */
		static public function setDefault($p_id)
		{
			B2DB::getTable('B2tProjects')->clearDefaults();
			B2DB::getTable('B2tProjects')->setDefaultProject($p_id);
			return true;
		}
		
		/**
		 * Populates the projects array
		 */
		static protected function _populateProjects()
		{
			if (self::$_projects === null)
			{
				self::$_projects = array();
				if ($res = B2DB::getTable('B2tProjects')->getAll())
				{
					while ($row = $res->getNextRow())
					{
						$project = BUGSfactory::projectLab($row->get(B2tProjects::ID), $row);
						if ($project->hasAccess() && $project->isDeleted() == 0)
						{
							self::$_projects[$project->getID()] = $project;
						}
					}
				}
			}
		}
		
		/**
		 * Retrieve all projects
		 * 
		 * @return array
		 */
		static public function getAll()
		{
			self::_populateProjects();
			return self::$_projects;
		}
		
		/**
		 * Retrieve the default project
		 * 
		 * @return BUGSproject
		 */
		static function getDefaultProject()
		{
			if ($res = B2DB::getTable('B2tProjects')->getAllSortedByIsDefault())
			{
				while ($row = $res->getNextRow())
				{
					$project = BUGSfactory::projectLab($row->get(B2tProjects::ID), $row);
					if ($project->hasAccess() && $project->isDeleted() == 0)
					{
						return $row->get(B2tProjects::ID);
					}
				}
			}
			return null;
		}
		
		
		/**
		 * Create a new project and return it
		 *
		 * @param string $name
		 * 
		 * @return BUGSproject
		 */
		static public function createNew($name)
		{
			$p_id = B2DB::getTable('B2tProjects')->createNew($name);
			
			BUGScontext::setPermission("b2projectaccess", $p_id, "core", 0, BUGScontext::getUser()->getGroup()->getID(), 0, true);
			
			if ($res = B2DB::getTable('B2tPermissionsList')->getByAppliesTo('projects'))
			{
				while ($row = $res->getNextRow())
				{
					$target_id = ($row->get(B2tPermissionsList::LEVELS) == 4) ? '1:'.$p_id : $p_id;
					BUGScontext::setPermission($row->get(B2tPermissionsList::PERMISSION_NAME), $target_id, "core", 0, BUGScontext::getUser()->getGroup()->getID(), 0, true);
				}
			}
			
			$theProject = BUGSfactory::projectLab($p_id);
			BUGScontext::trigger('core', 'BUGSProject::createNew', $theProject);
			
			return $theProject;
		}
		
		/**
		 * Returns the project for a specified prefix
		 * 
		 * @return BUGSproject
		 */
		static function getByPrefix($prefix)
		{
			if ($row = B2DB::getTable('B2tProjects')->getByPrefix($prefix))
			{
				return BUGSfactory::projectLab($row->get(B2tProjects::ID), $row);
			}
			return null;
		}
		
		/**
		 * Constructor function
		 *
		 * @param integer $id
		 * @param B2DBRow $row
 		 */
		public function __construct($id, $row = null)
		{
			if (!$row instanceof B2DBRow)
			{
				$row = B2DB::getTable('B2tProjects')->getById($id);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_name 					= $row->get(B2tProjects::NAME);
				$this->_prefix 					= $row->get(B2tProjects::PREFIX);
				$this->_locked 					= (bool) $row->get(B2tProjects::LOCKED);
				$this->_useprefix 				= (bool) $row->get(B2tProjects::USE_PREFIX);
				$this->_enablebuilds 			= (bool) $row->get(B2tProjects::ENABLE_BUILDS);
				$this->_enableeditions 			= (bool) $row->get(B2tProjects::ENABLE_EDITIONS);
				$this->_enablecomponents 		= (bool) $row->get(B2tProjects::ENABLE_COMPONENTS);
				$this->_enabletasks 			= (bool) $row->get(B2tProjects::ENABLE_TASKS);
				$this->_enablevotes 			= (bool) $row->get(B2tProjects::VOTES);
				$this->_isreleased 				= (bool) $row->get(B2tProjects::RELEASED);
				$this->_isplannedreleased		= (bool) $row->get(B2tProjects::PLANNED_RELEASE);
				$this->_isdefault 				= (bool) $row->get(B2tProjects::IS_DEFAULT);
				$this->_release_date 			= $row->get(B2tProjects::RELEASE_DATE);
				$this->_itemtype 				= BUGSversionitem::PROJECT;
				$this->_homepage 				= $row->get(B2tProjects::HOMEPAGE);
				$this->_description 			= $row->get(B2tProjects::DESCRIPTION);
				$this->_timeunit 				= ($row->get(B2tProjects::TIME_UNIT)) ? $row->get(B2tProjects::TIME_UNIT) : 5;
				$this->_itemid 					= $id;
				$this->_defaultstatus 			= ($row->get(B2tProjects::DEFAULT_STATUS)) ? $row->get(B2tProjects::DEFAULT_STATUS) : null;
				$this->_doc_url 				= $row->get(B2tProjects::DOC_URL);
				$this->_ownedtype				= $row->get(B2tProjects::OWNED_TYPE);
				$this->_ownedby 				= $row->get(B2tProjects::OWNED_BY);
				$this->_leadtype 				= $row->get(B2tProjects::LEAD_TYPE);
				$this->_leadby 					= $row->get(B2tProjects::LEAD_BY);
				$this->_qatype	 				= $row->get(B2tProjects::QA_TYPE);
				$this->_qaby 					= $row->get(B2tProjects::QA);
				$this->_hrsprday 				= $row->get(B2tProjects::HRS_PR_DAY);
				$this->_show_in_summary			= (bool) $row->get(B2tProjects::SHOW_IN_SUMMARY);
				$this->_can_change_wo_working	= (bool) $row->get(B2tProjects::ALLOW_CHANGING_WITHOUT_WORKING);
				$this->_summary_display			= $row->get(B2tProjects::SUMMARY_DISPLAY);
				$this->_deleted					= $row->get(B2tProjects::DELETED);
			}
			else
			{
				throw new Exception('This project does not exist. It might have been deleted.');
			}
		}
		
		/**
		 * Returns the prefix for this project
		 *
		 * @return string
		 */
		public function getPrefix()
		{
			return $this->_prefix;
		}
		
		/**
		 * Returns whether or not the project has a prefix set (regardless of whether it uses prefix or not)
		 * 
		 * @return boolean
		 */
		public function hasPrefix()
		{
			return ($this->_prefix != '') ? true : false;
		}
		
		/**
		 * Set the project prefix
		 *
		 * @param string $prefix
		 * 
		 * @return boolean
		 */
		public function setPrefix($prefix)
		{
			if (preg_match('/^[a-zA-Z0-9]+$/', $prefix) > 0)
			{
				$this->_prefix = $prefix;
				return true;
			}
			else
			{
				return false;
			}
		}	
		
		/**
		 * Mark the project as deleted
		 *
		 * @return boolean
		 */
		public function delete()
		{
			$this->_deleted = 1;
			return true;
		}


		/**
		 * Set the project name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}
		
		/**
		 * Returns homepage
		 *
		 * @return string
		 */
		public function getHomepage()
		{
			return $this->_homepage;
		}
		
		/**
		 * Returns whether or not this project has a homepage set
		 * 
		 * @return boolean
		 */
		public function hasHomepage()
		{
			return ($this->_homepage != '') ? true : false;
		}
		
		/**
		 * Whether or not this project has any editions
		 * 
		 * @return bool
		 */
		public function hasEditions()
		{
			return (bool) count($this->getEditions());
		}

		/**
		 * Set the project homepage
		 *
		 * @param string $homepage
		 */
		public function setHomepage($homepage)
		{
			$this->_homepage = $homepage;
		}
		
		
		/**
		 * Returns hours per day for this project
		 *
		 * @return integer
		 */
		public function getHoursPerDay()
		{
			if ((int) $this->_hrsprday == 0)
			{
				$this->_hrsprday = 7;
			}
			return $this->_hrsprday;
		}
		
		/**
		 * Set the project hours per day
		 *
		 * @param integer $hrs_pr_day
		 */
		public function setHoursPerDay($hrs_pr_day)
		{
			$hrs_pr_day = ($hrs_pr_day > 0) ? (int) $hrs_pr_day : 8; 
			
			$this->_hrsprday = $hrs_pr_day;
		}
		
		/**
		 * Returns the time unit
		 *
		 * @return integer
		 */
		public function getTimeUnit()
		{
			return $this->_timeunit;
		}
		
		/**
		 * Set the project time unit
		 *
		 * @param integer $time_unit
		 */
		public function setTimeUnit($time_unit)
		{
			$this->_timeunit = (int) $time_unit;
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
		 * Returns whether or not this project has any description set
		 * 
		 * @return boolean
		 */
		public function hasDescription()
		{
			return ($this->_description != '') ? true : false;
		}
		
		/**
		 * Set the project description
		 *
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		/**
		 * Returns the documentation url
		 *
		 * @return string
		 */
		public function getDocumentationURL()
		{
			return $this->_doc_url;
		}
		
		/**
		 * Returns whether or not this project has a homepage set
		 * 
		 * @return boolean
		 */
		public function hasDocumentationURL()
		{
			return ($this->_doc_url != '') ? true : false;
		}
		
		/**
		 * Set the projects documentation url
		 *
		 * @param string $doc_url
		 */
		public function setDocumentationURL($doc_url)
		{
			$this->_doc_url = $doc_url;
		}
		
		/**
		 * Return the default edition
		 *
		 * @return BUGSedition
		 */
		public function getDefaultEdition()
		{
			foreach ($this->getEditions() as $edition)
			{
				if ($edition->isDefault() && !$edition->isLocked())
				{
					return $edition;
				}
			}
			foreach ($this->getEditions() as $edition)
			{
				if ($edition->isLocked() == false)
				{
					return $edition;
				}
			}
			return 0;
		}
		
		/**
		 * Is tasks enabled?
		 * 
		 * @return bool
		 */
		public function isTasksEnabled()
		{
			return $this->_enabletasks;
		}
		
		/**
		 * Set whether tasks are enabled or not
		 *
		 * @param boolean $tasks_enabled
		 */
		public function setTasksEnabled($tasks_enabled)
		{
			$this->_enabletasks = (bool) $tasks_enabled;
		}
		
		/**
		 * Is votes enabled?
		 *
		 * @return boolean
		 */
		public function isVotesEnabled()
		{
			return $this->_enablevotes;
		}
		
		/**
		 * Set whether votes are enabled or not
		 *
		 * @param boolean $votes_enabled
		 */
		public function setVotesEnabled($votes_enabled)
		{
			$this->_enablevotes = (bool) $votes_enabled;
		}
		
		/**
		 * Set if the project is locked
		 *
		 * @param boolean $locked
		 */
		public function setLocked($locked)
		{
			$this->_locked = (bool) $locked;
		}
		
		/**
		 * Set the release date
		 *
		 * @param integer $release_date
		 */
		public function setReleaseDate($release_date)
		{
			$this->_release_date = (int) $release_date;
		}
		
		/**
		 * Returns the default status for new issues
		 *
		 * @return BUGSdatatype
		 */
		public function getDefaultStatus()
		{
			if (is_numeric($this->_defaultstatus))
			{
				try
				{
					$this->_defaultstatus = BUGSfactory::BUGSstatusLab($this->_defaultstatus);
				}
				catch (Exception $e)
				{
					$this->_defaultstatus = nul;
				}
			}
			return $this->_defaultstatus;
		}
		
		/**
		 * Returns the id for the default status
		 * 
		 * @return integer
		 */
		public function getDefaultStatusID()
		{
			return ($this->getDefaultStatus() instanceof BUGSstatus) ? $this->getDefaultStatus()->getID() : null;
		}
		
		/**
		 * Set the default status
		 *
		 * @param integer $status
		 */
		public function setDefaultStatus($status)
		{
			$this->_defaultstatus = (int) $status;
		}
		
		/**
		 * Is builds enabled
		 *
		 * @return boolean
		 */
		public function isBuildsEnabled()
		{
			return $this->_enablebuilds;
		}
		
		/**
		 * Set if the project uses builds
		 *
		 * @param boolean $builds_enabled
		 */
		public function setBuildsEnabled($builds_enabled)
		{
			$this->_enablebuilds = (bool) $builds_enabled;
		}

		/**
		 * Is editions enabled
		 *
		 * @return boolean
		 */
		public function isEditionsEnabled()
		{
			return $this->_enableeditions;
		}
		
		/**
		 * Set if the project uses editions
		 *
		 * @param boolean $editions_enabled
		 */
		public function setEditionsEnabled($editions_enabled)
		{
			$this->_enableeditions = (bool) $editions_enabled;
		}
		
		/**
		 * Is components enabled
		 *
		 * @return boolean
		 */
		public function isComponentsEnabled()
		{
			return $this->_enablecomponents;
		}
		
		/**
		 * Set if the project uses components
		 *
		 * @param boolean $components_enabled
		 */
		public function setComponentsEnabled($components_enabled)
		{
			$this->_enablecomponents = (bool) $components_enabled;
		}
		
		/**
		 * Populates editions inside the project
		 *
		 * @return void
		 */
		protected function _populateEditions()
		{
			if ($this->_editions === null)
			{
				$this->_editions = array();
				foreach (BUGSedition::getAllByProjectID($this->getID()) as $edition)
				{
					if ($edition->hasAccess())
					{
						$this->_editions[$edition->getID()] = $edition;
					}
				}
			}
		}

		/**
		 * Returns whether or not the project uses prefix
		 *
		 * @return boolean
		 */
		public function usePrefix()
		{
			return $this->_useprefix;
		}
		
		/**
		 * Returns whether or not the project has been deleted
		 *
		 * @return boolean
		 */
		public function isDeleted()
		{
			return $this->_deleted;
		}

		/**
		 * Set whether or not the project uses prefix
		 *
		 * @param boolean $use_prefix
		 */
		public function setUsePrefix($use_prefix)
		{
			$this->_useprefix = (bool) $use_prefix;
		}
		
		/**
		 * Return the leader
		 *
		 * @return BUGSidentifiable
		 */
		public function getLeadBy()
		{
			if (is_numeric($this->_leadby))
			{
				try
				{
					if ($this->_leadtype == BUGSidentifiableclass::TYPE_USER)
					{
						$this->_leadby = BUGSfactory::userLab($this->_leadby);
					}
					elseif ($this->_leadtype == BUGSidentifiableclass::TYPE_TEAM)
					{
						$this->_leadby = BUGSfactory::teamLab($this->_leadby);
					}
				}
				catch (Exception $e)
				{
					$this->_leadby = null;
					$this->_leadtype = null;
				}
			}
	
			return $this->_leadby;
		}
		
		/**
		 * Return the leader
		 *
		 * @see getLeadBy
		 * 
		 * @return BUGSidentifiable
		 */
		public function getLeader()
		{
			return $this->getLeadBy();
		}
		
		/**
		 * Returns the leader type
		 *
		 * @return integer
		 */
		public function getLeaderType()
		{
			$leader = $this->getLeader();
			return ($leader instanceof BUGSidentifiableclass) ? $leader->getType() : null;
		}
		
		/**
		 * Return the leader id
		 *
		 * @return integer
		 */
		public function getLeaderID()
		{
			$leader = $this->getLeader();
			return ($leader instanceof BUGSidentifiableclass) ? $leader->getID() : null;
		}
		
		/**
		 * Returns whether or not this project has a leader set
		 * 
		 * @return boolean
		 */
		public function hasLeader()
		{
			return (bool) ($this->getLeadBy() instanceof BUGSidentifiable);
		}
		
		/**
		 * Return the owner
		 *
		 * @return BUGSidentifiable
		 */
		public function getOwnedBy()
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
		 * Alias for getOwnedBy
		 * 
		 * @see getOwnedBy
		 * 
		 * @return BUGSidentifiable
		 */
		public function getOwner()
		{
			return $this->getOwnedBy();
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
		 * Returns whether or not this project has an owner set
		 * 
		 * @return boolean
		 */
		public function hasOwner()
		{
			return (bool) ($this->getOwnedBy() instanceof BUGSidentifiable);
		}
		
		/**
		 * Return the assignee
		 *
		 * @return BUGSidentifiable
		 */
		public function getQA()
		{
			if (is_numeric($this->_qaby))
			{
				try
				{
					if ($this->_qatype == BUGSidentifiableclass::TYPE_USER)
					{
						$this->_qaby = BUGSfactory::userLab($this->_qaby);
					}
					elseif ($this->_qatype == BUGSidentifiableclass::TYPE_TEAM)
					{
						$this->_qaby = BUGSfactory::teamLab($this->_qaby);
					}
				}
				catch (Exception $e)
				{
					$this->_qaby = null;
					$this->_qatype = null;
				}
			}
	
			return $this->_qaby;
		}

		/**
		 * Returns the qa type
		 *
		 * @return integer
		 */
		public function getQAType()
		{
			$qa = $this->getQA();
			return ($qa instanceof BUGSidentifiableclass) ? $qa->getType() : null;
		}
		
		/**
		 * Return the qa id
		 *
		 * @return integer
		 */
		public function getQAID()
		{
			$qa = $this->getQA();
			return ($qa instanceof BUGSidentifiableclass) ? $qa->getID() : null;
		}
		
		/**
		 * Returns whether or not this project has a QA set
		 * 
		 * @return boolean
		 */
		public function hasQA()
		{
			return (bool) ($this->getQA() instanceof BUGSidentifiable);
		}
		
		/**
		 * Set project Leader
		 * 
		 * @param BUGSidentifiableclass $leader The user/team you want to lead the project
		 */
		public function setLeadBy(BUGSidentifiableclass $leader)
		{
			$this->_leadby = $leader->getID();
			$this->_leadtype = $leader->getType();
		}

		/**
		 * Unset project Leader
		 */
		public function unsetLeadBy()
		{
			$this->_leadby = 0;
			$this->_leadtype = 0;
		}
		
		/**
		 * Set project QA
		 * 
		 * @param BUGSidentifiableclass $qaby The user/team you want to QA the project
		 */
		public function setQA(BUGSidentifiableclass $qaby)
		{
			$this->_qaby = $qaby->getID();
			$this->_qatype = $qaby->getType();
		}

		/**
		 * Unset project QA
		 */
		public function unsetQA()
		{
			$this->_qaby = 0;
			$this->_qatype = 0;
		}
		
		/**
		 * Set project owner
		 * 
		 * @param BUGSidentifiableclass $owner The user/team you want to own the project
		 */
		public function setOwner(BUGSidentifiableclass $owner)
		{
			$this->_ownedby = $owner->getID();
			$this->_ownedtype = $owner->getType();
			BUGSlogging::log('set owner');
		}
				
		/**
		 * Unset project Owner
		 */
		public function unsetOwner()
		{
			$this->_ownedby = 0;
			$this->_ownedtype = 0;
		}
		
		/**
		 * Returns an array of all the projects editions
		 *
		 * @return array
		 */
		public function getEditions()
		{
			$this->_populateEditions();
			return $this->_editions;
		}
		
		/**
		 * Adds an edition to the project
		 *
		 * @param string $e_name
		 * 
		 * @return BUGSedition
		 */
		public function addEdition($e_name)
		{
			$this->_editions = null;
			return BUGSedition::createNew($e_name, $this->getID());
		}
		
		/**
		 * Populates components inside the project
		 *
		 * @return void
		 */
		protected function _populateComponents()
		{
			if ($this->_components === null)
			{
				$this->_components = BUGScomponent::getAllByProjectID($this->getID());
			}
		}
		
		/**
		 * Returns an array with all components
		 *
		 * @return array
		 */
		public function getComponents()
		{
			$this->_populateComponents();
			return $this->_components;
		}
		
		/**
		 * Adds a new component to the project
		 *
		 * @param string $c_name
		 * @return BUGScomponent
		 */
		public function addComponent($c_name)
		{
			$this->_components = null;
			return BUGScomponent::createNew($c_name, $this->getID());
		}
		
		/**
		 * Populates the milestones array
		 *
		 * @return void
		 */
		protected function _populateMilestones()
		{
			if ($this->_milestones === null)
			{
				$this->_milestones = array();
				foreach (BUGSmilestone::getAllByProjectID($this->getID()) as $milestone)
				{
					$this->_milestones[$milestone->getID()] = $milestone;
				}
			}
		}

		/**
		 * Adds a new milestone to the project
		 *
		 * @param string $m_name
		 * @return BUGSmilestone
		 */
		public function addMilestone($m_name)
		{
			$this->_milestones = null;
			return BUGSmilestone::createNew($m_name, $this->getID());
		}
		
		/**
		 * Returns an array with all the milestones
		 *
		 * @return array
		 */
		public function getMilestones()
		{
			$this->_populateMilestones();
			return $this->_milestones;
		}
		
		/**
		 * Returns a list of upcoming milestones
		 * 
		 * @param integer $days[optional] Number of days, default 21 
		 * 
		 * @return array
		 */
		public function getUpcomingMilestones($days = 21)
		{
			$ret_arr = array();
			if ($milestones = $this->getMilestones())
			{
				$curr_day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				foreach ($milestones as $milestone)
				{
					if (($milestone->getScheduledDate() >= $curr_day || $milestone->isOverdue()) && $milestone->getScheduledDate() <= ($curr_day + (86400 * $days)))
					{
						$ret_arr[$milestone->getID()] = $milestone;
					}
				}
			}
			return $ret_arr;
		}
		
		/**
		 * Adds an assignee with a given role
		 * 
		 * @param BUGSidentifiable $assignee The user, team or customer to add
		 * @param integer $role The role to add
		 *  
		 * @return null
		 */
		public function addAssignee($assignee, $role)
		{
			switch (true)
			{
				case ($assignee instanceof BUGSuser):
					if (!$res = B2DB::getTable('B2tProjectAssignees')->getByProjectAndRoleAndUser($this->getID(), $role, $assignee->getID()))
					{
						B2DB::getTable('B2tProjectAssignees')->addByProjectAndRoleAndUser($this->getID(), $role, $assignee->getID());
					}
					break;
				case ($assignee instanceof BUGSteam):
					if ($res = B2DB::getTable('B2tProjectAssignees')->getByProjectAndRoleAndTeam($this->getID(), $role, $assignee->getID()))
					{
						B2DB::getTable('B2tProjectAssignees')->addByProjectAndRoleAndTeam($this->getID(), $role, $assignee->getID());
					}
					break;
				case ($assignee instanceof BUGScustomer):
					if ($res = B2DB::getTable('B2tProjectAssignees')->getByProjectAndRoleAndCustomer($this->getID(), $role, $assignee->getID()))
					{
						B2DB::getTable('B2tProjectAssignees')->addByProjectAndRoleAndCustomer($this->getID(), $role, $assignee->getID());
					}
					break;
			}
		}

		protected function _populateAssignees()
		{
			if ($this->_assignees === null)
			{
				$this->_assignees = array('users' => array(), 'customers' => array(), 'teams' => array());
		
				if ($res = B2DB::getTable('B2tProjectAssignees')->getByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						switch (true)
						{
							case ($row->get(B2tProjectAssignees::UID) != 0):
								$this->_assignees['users'][$row->get(B2tProjectAssignees::UID)]['projects'][$this->getID()][$row->get(B2tProjectAssignees::TARGET_TYPE)] = true;
								break;
							case ($row->get(B2tProjectAssignees::CID) != 0):
								$this->_assignees['customers'][$row->get(B2tProjectAssignees::CID)]['projects'][$this->getID()][$row->get(B2tProjectAssignees::TARGET_TYPE)] = true;
								break;
							case ($row->get(B2tProjectAssignees::TID) != 0):
								$this->_assignees['teams'][$row->get(B2tProjectAssignees::TID)]['projects'][$this->getID()][$row->get(B2tProjectAssignees::TARGET_TYPE)] = true;
								break;
						}
					}
				}
				
				if ($edition_ids = array_keys($this->getEditions()))
				{
					if ($res = B2DB::getTable('B2tEditionAssignees')->getByEditionIDs($edition_ids))
					{
						while ($row = $res->getNextRow())
						{
							switch (true)
							{
								case ($row->get(B2tEditionAssignees::UID) != 0):
									$this->_assignees['users'][$row->get(B2tEditionAssignees::UID)]['editions'][$row->get(B2tEditionAssignees::EDITION_ID)][$row->get(B2tEditionAssignees::TARGET_TYPE)] = true;
									break;
								case ($row->get(B2tEditionAssignees::CID) != 0):
									$this->_assignees['customers'][$row->get(B2tEditionAssignees::CID)]['editions'][$row->get(B2tEditionAssignees::EDITION_ID)][$row->get(B2tEditionAssignees::TARGET_TYPE)] = true;
									break;
								case ($row->get(B2tEditionAssignees::TID) != 0):
									$this->_assignees['teams'][$row->get(B2tEditionAssignees::TID)]['editions'][$row->get(B2tEditionAssignees::EDITION_ID)][$row->get(B2tEditionAssignees::TARGET_TYPE)] = true;
									break;
							}
						}
					}
				}
	
				if ($component_ids = array_keys($this->getComponents()))
				{
					if ($res = B2DB::getTable('B2tComponentAssignees')->getByComponentIDs($component_ids))
					{
						while ($row = $res->getNextRow())
						{
							switch (true)
							{
								case ($row->get(B2tComponentAssignees::UID) != 0):
									$this->_assignees['users'][$row->get(B2tComponentAssignees::UID)]['components'][$row->get(B2tComponentAssignees::COMPONENT_ID)][$row->get(B2tComponentAssignees::TARGET_TYPE)] = true;
									break;
								case ($row->get(B2tComponentAssignees::CID) != 0):
									$this->_assignees['customers'][$row->get(B2tComponentAssignees::CID)]['components'][$row->get(B2tComponentAssignees::COMPONENT_ID)][$row->get(B2tComponentAssignees::TARGET_TYPE)] = true;
									break;
								case ($row->get(B2tComponentAssignees::TID) != 0):
									$this->_assignees['teams'][$row->get(B2tComponentAssignees::TID)]['components'][$row->get(B2tComponentAssignees::COMPONENT_ID)][$row->get(B2tComponentAssignees::TARGET_TYPE)] = true;
									break;
							}
						}
					}
				}
			}
		}
		
		/**
		 * Get assignees for this project, including components and editions
		 * 
		 * @return array
		 */
		public function getAssignees()
		{
			$this->_populateAssignees();
			return $this->_assignees;
		}
		
		/**
		 * Return whether a user can change details about an issue without working on the issue
		 *  
		 * @return boolean
		 */
		public function canChangeIssuesWithoutWorkingOnThem()
		{
			return (bool) $this->_can_change_wo_working;
		}
		
		/**
		 * Set whether a user can change details about an issue without working on the issue
		 * 
		 * @param boolean $val
		 */
		public function setChangeIssuesWithoutWorkingOnThem($val)
		{
			$this->_can_change_wo_working = (bool) $val;
		}
		
		/**
		 * Save changes made to the project
		 * 
		 * @return bool
		 */
		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tProjects::LOCKED, $this->_locked);
			$crit->addUpdate(B2tProjects::PREFIX, $this->_prefix);
			$crit->addUpdate(B2tProjects::USE_PREFIX, $this->_useprefix);
			$crit->addUpdate(B2tProjects::ENABLE_BUILDS, $this->_enablebuilds);
			$crit->addUpdate(B2tProjects::ENABLE_COMPONENTS, $this->_enablecomponents);
			$crit->addUpdate(B2tProjects::ENABLE_EDITIONS, $this->_enableeditions);
			$crit->addUpdate(B2tProjects::ENABLE_TASKS, $this->_enabletasks);
			$crit->addUpdate(B2tProjects::VOTES, $this->_enablevotes);
			$crit->addUpdate(B2tProjects::NAME, $this->_name);
			$crit->addUpdate(B2tProjects::RELEASED, $this->_isreleased);
			$crit->addUpdate(B2tProjects::PLANNED_RELEASE, $this->_isplannedreleased);
			$crit->addUpdate(B2tProjects::IS_DEFAULT, $this->_isdefault);
			$crit->addUpdate(B2tProjects::RELEASE_DATE, $this->_release_date);
			$crit->addUpdate(B2tProjects::HOMEPAGE, $this->_homepage);
			$crit->addUpdate(B2tProjects::DESCRIPTION, $this->_description);
			$crit->addUpdate(B2tProjects::TIME_UNIT, $this->_timeunit);
			$crit->addUpdate(B2tProjects::DEFAULT_STATUS, $this->_defaultstatus);
			$crit->addUpdate(B2tProjects::DOC_URL, $this->_doc_url);
			$crit->addUpdate(B2tProjects::OWNED_TYPE, $this->_ownedtype);
			$crit->addUpdate(B2tProjects::OWNED_BY, $this->_ownedby);
			$crit->addUpdate(B2tProjects::LEAD_TYPE, $this->_leadtype);
			$crit->addUpdate(B2tProjects::LEAD_BY, $this->_leadby);
			$crit->addUpdate(B2tProjects::QA_TYPE, $this->_qatype);
			$crit->addUpdate(B2tProjects::QA, $this->_qaby);
			$crit->addUpdate(B2tProjects::HRS_PR_DAY, $this->_hrsprday); 
			$crit->addUpdate(B2tProjects::SHOW_IN_SUMMARY, $this->_show_in_summary);
			$crit->addUpdate(B2tProjects::SUMMARY_DISPLAY, $this->_summary_display);
			$crit->addUpdate(B2tProjects::ALLOW_CHANGING_WITHOUT_WORKING, $this->_can_change_wo_working);
			$crit->addUpdate(B2tProjects::DELETED, $this->_deleted);
			$res = B2DB::getTable('B2tProjects')->doUpdateById($crit, $this->getID());

			return true;
		}

		/**
		 * Populates builds inside the project
		 *
		 * @return void
		 */
		protected function _populateBuilds()
		{
			if ($this->_builds === null)
			{
				$this->_builds = array();
				foreach (BUGSbuild::getByProjectID($this->getID()) as $build)
				{
					if ($build->hasAccess())
					{
						$this->_builds[$build->getID()] = $build;
					}
				}
			}
		}
		
		/**
		 * Returns an array with all builds
		 *
		 * @return array
		 */
		public function getBuilds()
		{
			$this->_populateBuilds();
			return $this->_builds;
		}

		/**
		 * Populates issue types inside the project
		 *
		 * @return void
		 */
		protected function _populateIssuetypes()
		{
			if ($this->_issuetypes === null)
			{
				$this->_issuetypes = BUGSissuetype::getAllApplicableToProject($this->getID());
			}
		}
		
		/**
		 * Returns all issue types available for / applicable to this project
		 * 
		 * @return array
		 */
		public function getIssuetypes()
		{
			$this->_populateIssuetypes();
			return $this->_issuetypes;
		}
		
		/**
		 * Populates visible milestones inside the project
		 *
		 * @return void
		 */
		protected function _populateVisibleMilestones()
		{
			if ($this->_visible_milestones === null)
			{
				$this->_visible_milestones = array();
				if ($res = B2DB::getTable('B2tVisibleMilestones')->getAllByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$milestone = BUGSfactory::milestoneLab($row->get(B2tMilestones::ID), $row);
						if ($milestone->hasAccess())
						{
							$this->_visible_milestones[$milestone->getID()] = $milestone;
						}
					}
				}
			}
		}

		/**
		 * Returns all milestones visible in the project summary block
		 * 
		 * @return array
		 */
		public function getVisibleMilestones()
		{
			$this->_populateVisibleMilestones();
			return $this->_visible_milestones;
		}
		
		/**
		 * Removes all milestones from being visible in the project summary block
		 * 
		 * @return null
		 */
		public function clearVisibleMilestones()
		{
			$this->_visible_milestones = null;
			B2DB::getTable('B2tVisibleMilestones')->clearByProjectID($this->getID());
		}
		
		/**
		 * Adds a milestone to list of visible milestones in project summary block
		 * 
		 * @param integer $milestone_id The ID of the added milestone
		 * 
		 * @return boolean
		 */
		public function addVisibleMilestone($milestone_id)
		{
			try
			{
				$this->_visible_milestones = null;
				B2DB::getTable('B2tVisibleMilestones')->addByProjectIDAndMilestoneID($this->getID(), $milestone_id);
				return true;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		
		/**
		 * Returns whether or not a milestone is visible in the project summary block
		 * 
		 * @param integer $milestone_id The ID of the milestone
		 * 
		 * @return boolean
		 */
		public function isMilestoneVisible($milestone_id)
		{
			$milestones = $this->getVisibleMilestones();
			return array_key_exists($milestone_id, $milestones);
		}
		
		protected function _populateIssueCounts()
		{
			if (!is_array($this->_issues))
			{
				$this->_issues = array();
			}
			if (!array_key_exists('all', $this->_issues))
			{
				$this->_issues['all'] = array();
			}
			if (empty($this->_issues['all']))
			{
				list ($this->_issues['all']['closed'], $this->_issues['all']['open']) = BUGSissue::getIssueCountsByProjectID($this->getID());
			}
		}
		
		protected function _populateIssueCountsByIssueType($issuetype_id)
		{
			if ($this->_issues === null)
			{
				$this->_issues = array();
			}
			if (!array_key_exists('issuetype', $this->_issues))
			{
				$this->_issues['issuetype'] = array();
			}
			if (!array_key_exists($issuetype_id, $this->_issues['issuetype']))
			{
				list ($this->_issues['issuetype'][$issuetype_id]['closed'], $this->_issues['issuetype'][$issuetype_id]['open']) = BUGSissue::getIssueCountsByProjectIDandIssuetype($this->getID(), $issuetype_id);
			}
		}

		protected function _populateIssueCountsByMilestone($milestone_id)
		{
			if ($this->_issues === null)
			{
				$this->_issues = array();
			}
			if (!array_key_exists('milestone', $this->_issues))
			{
				$this->_issues['milestone'] = array();
			}
			if (!array_key_exists($milestone_id, $this->_issues['milestone']))
			{
				list ($this->_issues['milestone'][$milestone_id]['closed'], $this->_issues['milestone'][$milestone_id]['open']) = BUGSissue::getIssueCountsByProjectIDandMilestone($this->getID(), $milestone_id);
			}
		}

		protected function _populateVisibleIssuetypes()
		{
			if ($this->_visible_issuetypes === null)
			{
				$this->_visible_issuetypes = array();
				if ($res = B2DB::getTable('B2tVisibleIssueTypes')->getAllByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_visible_issuetypes[$row->get(B2tIssueTypes::ID)] = BUGSfactory::BUGSissuetypeLab($row->get(B2tIssueTypes::ID), $row);
					}
				}
			}
		}
		
		/**
		 * Returns all issue types visible in the project summary block
		 * 
		 * @return array|BUGSissuetype
		 */
		public function getVisibleIssuetypes()
		{
			$this->_populateVisibleIssuetypes();
			return $this->_visible_issuetypes;
		}
		
		/**
		 * Removes all issue types from being visible in the project summary block
		 * 
		 * @return null
		 */
		public function clearVisibleIssuetypes()
		{
			$this->_visible_issuetypes = null;
			B2DB::getTable('B2tVisibleIssueTypes')->clearByProjectID($this->getID());
		}
		
		/**
		 * Adds an issue type to list of visible issue types in project summary block
		 * 
		 * @param integer $issuetype_id The ID of the added issue type
		 * 
		 * @return bool
		 */
		public function addVisibleIssuetype($issuetype_id)
		{
			try
			{
				B2DB::getTable('B2tVisibleIssueTypes')->addByProjectIDAndIssuetypeID($this->getID(), $issuetype_id);
				return true;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		
		/**
		 * Returns whether or not an issue type is visible in the project summary block
		 * 
		 * @param integer $issuetype_id The ID of the issue type
		 * 
		 * @return bool
		 */
		public function isIssuetypeVisible($issuetype_id)
		{
			$issuetypes = $this->getVisibleIssuetypes();
			return array_key_exists($issuetype_id, $issuetypes);
		}
		
		/**
		 * Returns the number of issues for this project
		 * 
		 * @return integer
		 */
		public function countAllIssues()
		{
			$this->_populateIssueCounts();
			return $this->_issues['all']['closed'] + $this->_issues['all']['open'];
		}
		
		/**
		 * Returns the number of issues for this project with a specific issue type
		 * 
		 * @param integer $issue_type ID of the issue type
		 * 
		 * @return integer
		 */
		public function countIssuesByType($issuetype)
		{
			$this->_populateIssueCountsByIssueType($issuetype);
			return $this->_issues['issuetype'][$issuetype]['closed'] + $this->_issues['issuetype'][$issuetype]['open'];
		}

		/**
		 * Returns the number of issues for this project with a specific milestone
		 * 
		 * @param integer $milestone ID of the milestone
		 * 
		 * @return integer
		 */
		public function countIssuesByMilestone($milestone)
		{
			$this->_populateIssueCountsByMilestone($milestone);
			return $this->_issues['milestone'][$milestone]['closed'] + $this->_issues['milestone'][$milestone]['open'];
		}
		
		/**
		 * Returns the number of open issues for this project
		 * 
		 * @return integer
		 */
		public function countAllOpenIssues()
		{
			$this->_populateIssueCounts();
			return $this->_issues['all']['open'];
		}
		
		/**
		 * Returns the number of open issues for this project with a specific issue type
		 * 
		 * @param integer $issue_type ID of the issue type
		 * 
		 * @return integer
		 */
		public function countOpenIssuesByType($issue_type)
		{
			$this->_populateIssueCountsByIssueType($issue_type);
			return $this->_issues['issuetype'][$issue_type]['open'];
		}

		/**
		 * Returns the number of open issues for this project with a specific milestone
		 * 
		 * @param integer $milestone ID of the milestone
		 * 
		 * @return integer
		 */
		public function countOpenIssuesByMilestone($milestone)
		{
			$this->_populateIssueCountsByMilestone($milestone);
			return $this->_issues['milestone'][$milestone]['open'];
		}
		
		/**
		 * Returns the number of closed issues for this project
		 * 
		 * @return integer
		 */
		public function countAllClosedIssues()
		{
			$this->_populateIssueCounts();
			return $this->_issues['all']['closed'];
		}
		
		/**
		 * Returns the number of closed issues for this project with a specific issue type
		 * 
		 * @param integer $issue_type ID of the issue type
		 * 
		 * @return integer
		 */
		public function countClosedIssuesByType($issue_type)
		{
			$this->_populateIssueCountsByIssueType($issue_type);
			return $this->_issues['issuetype'][$issue_type]['closed'];
		}

		/**
		 * Returns the number of closed issues for this project with a specific milestone
		 * 
		 * @param integer $milestone ID of the milestone
		 * 
		 * @return integer
		 */
		public function countClosedIssuesByMilestone($milestone)
		{
			$this->_populateIssueCountsByMilestone($milestone);
			return $this->_issues['milestone'][$milestone]['closed'];
		}
		
		/**
		 * Returns the percentage of a given number related to another given number
		 * 
		 * @param integer $num_1 percentage number
		 * @param integer $num_max total number
		 * 
		 * @return integer The percentage
		 */
		protected function getPercentage($num_1, $num_max)
		{
			$pct = 0;
			
			if ($num_max > 0 && $num_1 > 0)
			{
				$multiplier = 100 / $num_max;
				$pct = $num_1 * $multiplier;
			}
			
			return $pct;			
		}
		
		/**
		 * Returns the percentage of closed issues for this project
		 * 
		 * @return integer
		 */
		public function getClosedPercentageForAllIssues()
		{
			return $this->getPercentage($this->countAllClosedIssues(), $this->countAllIssues());
		}
		
		/**
		 * Returns the percentage of closed issues for this project with a specific issue type
		 * 
		 * @param integer $issue_type ID of the issue type
		 * 
		 * @return integer
		 */
		public function getClosedPercentageByType($issue_type)
		{
			return $this->getPercentage($this->countClosedIssuesByType($issue_type), $this->countIssuesByType($issue_type));
		}

		/**
		 * Returns the percentage of closed issues for this project with a specific milestone
		 * 
		 * @param integer $milestone ID of the milestone
		 * 
		 * @return integer
		 */
		public function getClosedPercentageByMilestone($milestone)
		{
			return $this->getPercentage($this->countClosedIssuesByMilestone($milestone), $this->countIssuesByMilestone($milestone));
		}
		
		/**
		 * Whether or not this project is visible in the frontpage summary
		 * 
		 * @return boolean
		 */
		public function isShownInFrontpageSummary()
		{
			return $this->_show_in_summary;
		}
		
		/**
		 * Set whether or not this project is visible in the frontpage summary
		 * 
		 * @param boolean $visibility Visible or not
		 * 
		 * @return null
		 */
		public function setFrontpageSummaryVisibility($visibility)
		{
			$this->_show_in_summary = (bool) $visibility;
		}
		
		/**
		 * Set what to display in the frontpage summary
		 * 
		 * @param string $summary_type "milestones" or "issuetypes"
		 * 
		 * @return null
		 */
		public function setFrontpageSummaryType($summary_type)
		{
			$this->_summary_display = $summary_type; 
		}
		
		/**
		 * Returns what is displayed in the frontpage summary
		 * 
		 * @return string "milestones" or "issuetypes"
		 */
		public function getFrontpageSummaryType()
		{
			return $this->_summary_display;
		}
		
		/**
		 * Checks to see if milestones are shown in the frontpage summary
		 * 
		 * @return boolean
		 */
		public function isMilestonesVisibleInFrontpageSummary()
		{
			return ($this->getFrontpageSummaryType() == 'milestones') ? true : false;
		}

		/**
		 * Checks to see if issue types are shown in the frontpage summary
		 * 
		 * @return boolean
		 */
		public function isIssuetypesVisibleInFrontpageSummary()
		{
			return ($this->getFrontpageSummaryType() == 'issuetypes') ? true : false;
		}

		/**
		 * Checks to see if anything is shown in the frontpage summary
		 * 
		 * @return boolean
		 */
		public function isAnythingVisibleInFrontpageSummary()
		{
			return ($this->getFrontpageSummaryType() == '') ? false : true;
		}

		/**
		 * Return an array specifying visibility, requirement and choices for fields in reporting wizard
		 * 
		 * @param integer $issue_type
		 * 
		 * @return array
		 */
		public function getReportableFieldsArray($issue_type)
		{
			return $this->getFieldsArray($issue_type, true);
		}

		/**
		 * Return an array specifying visibility, requirement and choices for fields in the "View issue" page
		 * 
		 * @param integer $issue_type
		 * 
		 * @return array
		 */
		public function getVisibleFieldsArray($issue_type)
		{
			return $this->getFieldsArray($issue_type, false);
		}
		
		/**
		 * Return an array specifying visibility, requirement and choices for fields in issues
		 * 
		 * @param integer $issue_type
		 * @param boolean $reportable[optional] Whether to only include fields that can be reported
		 * 
		 * @return array
		 */
		protected function getFieldsArray($issue_type, $reportable = true)
		{
			if (!isset($this->_fieldsarrays[$issue_type][(int) $reportable]))
			{
				$retval = array();
				if (!($res = B2DB::getTable('B2tIssueFields')->getByProjectIDandIssuetypeID($this->getID(), $issue_type)))
				{
					$res = B2DB::getTable('B2tIssueFields')->getByIssuetypeID($issue_type);
				}
				if ($res)
				{
					while ($row = $res->getNextRow())
					{
						if (!$reportable || (bool) $row->get(B2tIssueFields::REPORTABLE) == true)
						{
							$retval[$row->get(B2tIssueFields::FIELD_KEY)] = array('required' => (bool) $row->get(B2tIssueFields::REQUIRED), 'additional' => (bool) $row->get(B2tIssueFields::ADDITIONAL));
						}
					}
					
					if ($reportable)
					{
						if ($this->isEditionsEnabled() && array_key_exists('edition', $retval))
						{
							$retval['edition']['editions'] = array();
							foreach ($this->getEditions() as $edition)
							{
								$retval['edition']['editions'][$edition->getID()] = $edition->getName();
							}
						}
					}
					if (!$this->isEditionsEnabled() || empty($retval['edition']['editions'])) unset($retval['edition']);
		
					if ($reportable)
					{
						if ($this->isBuildsEnabled() && array_key_exists('build', $retval))
						{
							$retval['build']['builds'] = array();
							foreach ($this->getBuilds() as $build)
							{
								$retval['build']['builds'][$build->getID()] = $build->getName();
							}
						}
					}
					if (!$this->isBuildsEnabled() || empty($retval['build']['builds'])) unset($retval['build']);
					
					if ($reportable)
					{
						if ($this->isComponentsEnabled() && array_key_exists('component', $retval))
						{
							$retval['component']['components'] = array();
							foreach ($this->getComponents() as $component)
							{
								$retval['component']['components'][$component->getID()] = $component->getName();
							}
						}
					}
					if (!$this->isComponentsEnabled() || empty($retval['component']['components'])) unset($retval['component']);
				}
				$this->_fieldsarrays[$issue_type][(int) $reportable] = $retval;
			}
			
			return $this->_fieldsarrays[$issue_type][(int) $reportable];
		}
				
		/**
		 * Whether or not the current user can access the edition
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return BUGScontext::getUser()->hasPermission('b2projectaccess', $this->getID(), 'core');			
		}
		
	}
