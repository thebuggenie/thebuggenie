<?php

	/**
	 * Project class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGProject extends TBGOwnableItem
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
		 * Whether or not the project uses scrum planning
		 *
		 * @var boolean
		 */
		protected $_usescrum = false;

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
		 * @var array|TBGBuild
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
		 * Project key
		 *
		 * @var string
		 */
		protected $_key = null;
		
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
		 * @var TBGDatatype
		 */
		protected $_defaultstatus = 0;
		
		/**
		 * List of milestones + sprints for this project
		 *
		 * @var array
		 */
		protected $_allmilestones = null;

		/**
		 * List of milestones for this project
		 *
		 * @var array
		 */
		protected $_milestones = null;

		/**
		 * List of sprints for this project
		 *
		 * @var array
		 */
		protected $_sprints = null;
		
		/**
		 * List of components for this project
		 *
		 * @var array
		 */
		protected $_components = null;
		
		/**
		 * Count of issues registered for this project
		 *
		 * @var integer
		 */
		protected $_issuecounts = null;

		/**
		 * Issues registered for this project with no milestone assigned
		 *
		 * @var array
		 */
		protected $_unassignedissues = null;

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
		 * Set to true if the project is set to be deleted, but not saved yet
		 *
		 * @var boolean
		 */
		protected $_dodelete = false;

		/**
		 * Recent log items
		 *
		 * @var array
		 */
		protected $_recentlogitems = null;

		/**
		 * Recent important log items
		 *
		 * @var array
		 */
		protected $_recentimportantlogitems = null;

		/**
		 * Recent issues reported
		 *
		 * @var array
		 */
		protected $_recentissues = null;

		/**
		 * Priority count
		 *
		 * @var array
		 */
		protected $_prioritycount = null;

		/**
		 * Recent new features / enhancements reported
		 *
		 * @var array
		 */
		protected $_recentfeatures = null;

		/**
		 * Recent ideas suggested
		 *
		 * @var array
		 */
		protected $_recentideas = null;

		/**
		 * Recent activities
		 *
		 * @var array
		 */
		protected $_recentactivities = null;
		
		/**
		 * Is the affected things box hidden in a tab in viewissue
		 * 
		 * @var boolean
		 */
		protected $_affectshidden = 0;

		/**
		 * Make a project default
		 * 
		 * @param $p_id integer The id for the default project
		 * 
		 * @return boolean
		 */
		public static function setDefault($p_id)
		{
			TBGProjectsTable::getTable()->clearDefaults();
			TBGProjectsTable::getTable()->setDefaultProject($p_id);
			return true;
		}

		/**
		 * Retrieve a project by its key
		 *
		 * @param string $key
		 *
		 * @return TBGProject
		 */
		public static function getByKey($key)
		{
			if ($project_row = TBGProjectsTable::getTable()->getByKey(strtolower($key)))
			{
				return TBGContext::factory()->TBGProject($project_row->get(TBGProjectsTable::ID), $project_row);
			}
			return null;
		}
		
		/**
		 * Populates the projects array
		 */
		static protected function _populateProjects()
		{
			if (self::$_projects === null)
			{
				self::$_projects = array();
				if ($res = TBGProjectsTable::getTable()->getAll())
				{
					while ($row = $res->getNextRow())
					{
						$project = TBGContext::factory()->TBGProject($row->get(TBGProjectsTable::ID), $row);
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
		public static function getAll()
		{
			self::_populateProjects();
			return self::$_projects;
		}
		
		/**
		 * Retrieve the default project
		 * 
		 * @return TBGProject
		 */
		static function getDefaultProject()
		{
			if ($res = TBGProjectsTable::getTable()->getAllSortedByIsDefault())
			{
				while ($row = $res->getNextRow())
				{
					$project = TBGContext::factory()->TBGProject($row->get(TBGProjectsTable::ID), $row);
					if ($project->hasAccess() && $project->isDeleted() == 0)
					{
						return $row->get(TBGProjectsTable::ID);
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
		 * @return TBGProject
		 */
		public static function createNew($name)
		{
			$project = TBGProjectsTable::getTable()->getByKey(strtolower(str_replace(' ', '', $name)));
			if ($project === null)
			{
				$p_id = TBGProjectsTable::getTable()->createNew($name);

				TBGContext::setPermission("canseeproject", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canmanageproject", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("page_project_allpages_access", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canvoteforissues", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canlockandeditlockedissues", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("cancreateandeditissues", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("caneditissue", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("caneditissuecustomfields", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canaddextrainformationtoissues", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canpostseeandeditallcomments", $p_id, "core", TBGContext::getUser()->getID(), 0, 0, true);

				$project = TBGContext::factory()->TBGProject($p_id);
				TBGEvent::createNew('core', 'TBGProject::createNew', $project)->trigger();

				return $project;
			}
			return null;
		}
		
		/**
		 * Returns the project for a specified prefix
		 * 
		 * @return TBGProject
		 */
		static function getByPrefix($prefix)
		{
			if ($row = TBGProjectsTable::getTable()->getByPrefix($prefix))
			{
				return TBGContext::factory()->TBGProject($row->get(TBGProjectsTable::ID), $row);
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
				$row = TBGProjectsTable::getTable()->getById($id);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_name 					= $row->get(TBGProjectsTable::NAME);
				$this->_key 					= $row->get(TBGProjectsTable::KEY);
				$this->_prefix 					= $row->get(TBGProjectsTable::PREFIX);
				$this->_locked 					= (bool) $row->get(TBGProjectsTable::LOCKED);
				$this->_useprefix 				= (bool) $row->get(TBGProjectsTable::USE_PREFIX);
				$this->_usescrum 				= (bool) $row->get(TBGProjectsTable::USE_SCRUM);
				$this->_enablebuilds 			= (bool) $row->get(TBGProjectsTable::ENABLE_BUILDS);
				$this->_enableeditions 			= (bool) $row->get(TBGProjectsTable::ENABLE_EDITIONS);
				$this->_enablecomponents 		= (bool) $row->get(TBGProjectsTable::ENABLE_COMPONENTS);
				$this->_enabletasks 			= (bool) $row->get(TBGProjectsTable::ENABLE_TASKS);
				$this->_enablevotes 			= (bool) $row->get(TBGProjectsTable::VOTES);
				$this->_isreleased 				= (bool) $row->get(TBGProjectsTable::RELEASED);
				$this->_isplannedreleased		= (bool) $row->get(TBGProjectsTable::PLANNED_RELEASE);
				$this->_isdefault 				= (bool) $row->get(TBGProjectsTable::IS_DEFAULT);
				$this->_release_date 			= $row->get(TBGProjectsTable::RELEASE_DATE);
				$this->_itemtype 				= TBGVersionItem::PROJECT;
				$this->_homepage 				= $row->get(TBGProjectsTable::HOMEPAGE);
				$this->_description 			= $row->get(TBGProjectsTable::DESCRIPTION);
				$this->_timeunit 				= ($row->get(TBGProjectsTable::TIME_UNIT)) ? $row->get(TBGProjectsTable::TIME_UNIT) : 5;
				$this->_itemid 					= $id;
				$this->_defaultstatus 			= ($row->get(TBGProjectsTable::DEFAULT_STATUS)) ? $row->get(TBGProjectsTable::DEFAULT_STATUS) : null;
				$this->_doc_url 				= $row->get(TBGProjectsTable::DOC_URL);
				$this->_owner_type				= $row->get(TBGProjectsTable::OWNED_TYPE);
				$this->_owner	 				= $row->get(TBGProjectsTable::OWNED_BY);
				$this->_leader_type				= $row->get(TBGProjectsTable::LEAD_TYPE);
				$this->_leader 					= $row->get(TBGProjectsTable::LEAD_BY);
				$this->_qa_responsible_type		= $row->get(TBGProjectsTable::QA_TYPE);
				$this->_qa_responsible			= $row->get(TBGProjectsTable::QA);
				$this->_hrsprday 				= $row->get(TBGProjectsTable::HRS_PR_DAY);
				$this->_show_in_summary			= (bool) $row->get(TBGProjectsTable::SHOW_IN_SUMMARY);
				$this->_can_change_wo_working	= (bool) $row->get(TBGProjectsTable::ALLOW_CHANGING_WITHOUT_WORKING);
				$this->_summary_display			= $row->get(TBGProjectsTable::SUMMARY_DISPLAY);
				$this->_deleted					= $row->get(TBGProjectsTable::DELETED);
				$this->_affectshidden			= $row->get(TBGProjectsTable::HIDDEN_AFFECTS_BOX);
				TBGEvent::createNew('core', 'TBGProject::__construct', $this)->trigger();
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
		 * Set whether the project uses scrum planning
		 *
		 * @param boolean $val
		 */
		public function setUsesScrum($val = true)
		{
			$this->_usescrum = $val;
		}

		/**
		 * Return whether the project uses scrum planning
		 *
		 * @return boolean
		 */
		public function usesScrum()
		{
			return (bool) $this->_usescrum;
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
			if (preg_match('/[^a-zA-Z0-9]+/', $prefix) > 0)
			{
				return false;
			}
			else
			{
				$this->_prefix = $prefix;
				return true;
			}
		}	
		
		/**
		 * Mark the project as deleted
		 *
		 * @return boolean
		 */
		public function delete()
		{
			$this->_deleted = true;
			$this->_dodelete = true;
			$this->_key = '';
			return true;
		}

		public function getStrippedProjectName()
		{
			return preg_replace("/[^0-9a-zA-Z]/i", '', $this->getName());
		}

		/**
		 * Set the project name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			parent::setName($name);
			$this->_key = strtolower($this->getStrippedProjectName());
		}
		
		/**
		 * Return project key
		 * 
		 * @return string
		 */
		public function getKey()
		{
			return $this->_key;
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
		 * @return TBGEdition
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
		 * Set whether the affected things box is hidden in a tab or not in viewissue
		 *
		 * @param boolean $affects_hidden
		 */
		public function setAffectsHidden($affects_hidden)
		{
			$this->_affectshidden = (bool) $affects_hidden;
		}
		
		/**
		 * Returns the default status for new issues
		 *
		 * @return TBGDatatype
		 */
		public function getDefaultStatus()
		{
			if (is_numeric($this->_defaultstatus))
			{
				try
				{
					$this->_defaultstatus = TBGContext::factory()->TBGStatus($this->_defaultstatus);
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
			return ($this->getDefaultStatus() instanceof TBGStatus) ? $this->getDefaultStatus()->getID() : null;
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
				foreach (TBGEdition::getAllByProjectID($this->getID()) as $edition)
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
		
		public function doesUsePrefix()
		{
			return $this->usePrefix();
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
		 * @return TBGEdition
		 */
		public function addEdition($e_name)
		{
			$this->_editions = null;
			return TBGEdition::createNew($e_name, $this->getID());
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
				$this->_components = TBGComponent::getAllByProjectID($this->getID());
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
		 * @return TBGComponent
		 */
		public function addComponent($c_name)
		{
			$this->_components = null;
			return TBGComponent::createNew($c_name, $this->getID());
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
				foreach (TBGMilestone::getMilestonesByProjectID($this->getID()) as $milestone)
				{
					$this->_milestones[$milestone->getID()] = $milestone;
				}
			}
		}

		/**
		 * Populates the milestones + sprints array
		 *
		 * @return void
		 */
		protected function _populateAllMilestones()
		{
			if ($this->_allmilestones === null)
			{
				$this->_allmilestones = array();
				foreach (TBGMilestone::getAllByProjectID($this->getID()) as $milestone)
				{
					$this->_allmilestones[$milestone->getID()] = $milestone;
				}
			}
		}

		/**
		 * Populates the sprints array
		 *
		 * @return void
		 */
		protected function _populateSprints()
		{
			if ($this->_sprints === null)
			{
				$this->_sprints = array();
				foreach (TBGMilestone::getSprintsByProjectID($this->getID()) as $sprint)
				{
					$this->_sprints[$sprint->getID()] = $sprint;
				}
			}
		}

		/**
		 * Adds a new milestone to the project
		 *
		 * @param string $m_name
		 * @return TBGMilestone
		 */
		public function addMilestone($m_name, $type)
		{
			$this->_milestones = null;
			return TBGMilestone::createNew($m_name, $type, $this->getID());
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
		 * Returns an array with all the milestones + sprints
		 *
		 * @return array
		 */
		public function getAllMilestones()
		{
			$this->_populateAllMilestones();
			return $this->_allmilestones;
		}

		/**
		 * Returns an array with all the sprints
		 *
		 * @return array
		 */
		public function getSprints()
		{
			$this->_populateSprints();
			return $this->_sprints;
		}

		/**
		 * Returns a list of upcoming milestones and sprints
		 * 
		 * @param integer $days[optional] Number of days, default 21 
		 * 
		 * @return array
		 */
		public function getUpcomingMilestonesAndSprints($days = 21)
		{
			$ret_arr = array();
			if ($allmilestones = $this->getAllMilestones())
			{
				$curr_day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				foreach ($allmilestones as $milestone)
				{
					if (($milestone->getScheduledDate() >= $curr_day || $milestone->isOverdue()) && (($milestone->getScheduledDate() <= ($curr_day + (86400 * $days))) || ($milestone->getType() == TBGMilestone::TYPE_SCRUMSPRINT && $milestone->isCurrent())))
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
		 * @param TBGIdentifiable $assignee The user, team or customer to add
		 * @param integer $role The role to add
		 *  
		 * @return null
		 */
		public function addAssignee($assignee, $role)
		{
			switch (true)
			{
				case ($assignee instanceof TBGUser):
					if (!$res = B2DB::getTable('TBGProjectAssigneesTable')->getByProjectAndRoleAndUser($this->getID(), $role, $assignee->getID()))
					{
						B2DB::getTable('TBGProjectAssigneesTable')->addByProjectAndRoleAndUser($this->getID(), $role, $assignee->getID());
					}
					break;
				case ($assignee instanceof TBGTeam):
					if ($res = B2DB::getTable('TBGProjectAssigneesTable')->getByProjectAndRoleAndTeam($this->getID(), $role, $assignee->getID()))
					{
						B2DB::getTable('TBGProjectAssigneesTable')->addByProjectAndRoleAndTeam($this->getID(), $role, $assignee->getID());
					}
					break;
				case ($assignee instanceof TBGCustomer):
					if ($res = B2DB::getTable('TBGProjectAssigneesTable')->getByProjectAndRoleAndCustomer($this->getID(), $role, $assignee->getID()))
					{
						B2DB::getTable('TBGProjectAssigneesTable')->addByProjectAndRoleAndCustomer($this->getID(), $role, $assignee->getID());
					}
					break;
			}
		}

		protected function _populateAssignees()
		{
			if ($this->_assignees === null)
			{
				$this->_assignees = array('uids' => array(), 'users' => array(), 'customers' => array(), 'teams' => array());
		
				if ($res = B2DB::getTable('TBGProjectAssigneesTable')->getByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						switch (true)
						{
							case ($row->get(TBGProjectAssigneesTable::UID) != 0):
								$this->_assignees['users'][$row->get(TBGProjectAssigneesTable::UID)]['projects'][$this->getID()][$row->get(TBGProjectAssigneesTable::TARGET_TYPE)] = true;
								$this->_assignees['uids'][$row->get(TBGProjectAssigneesTable::UID)] = $row->get(TBGProjectAssigneesTable::UID);
								break;
							case ($row->get(TBGProjectAssigneesTable::CID) != 0):
								$this->_assignees['customers'][$row->get(TBGProjectAssigneesTable::CID)]['projects'][$this->getID()][$row->get(TBGProjectAssigneesTable::TARGET_TYPE)] = true;
								break;
							case ($row->get(TBGProjectAssigneesTable::TID) != 0):
								$this->_assignees['teams'][$row->get(TBGProjectAssigneesTable::TID)]['projects'][$this->getID()][$row->get(TBGProjectAssigneesTable::TARGET_TYPE)] = true;
								foreach (B2DB::getTable('TBGTeamMembersTable')->getUIDsForTeamID($row->get(TBGProjectAssigneesTable::TID)) as $uid)
								{
									$this->_assignees['uids'][$uid] = $uid;
								}
								break;
						}
					}
				}
				
				if ($edition_ids = array_keys($this->getEditions()))
				{
					if ($res = B2DB::getTable('TBGEditionAssigneesTable')->getByEditionIDs($edition_ids))
					{
						while ($row = $res->getNextRow())
						{
							switch (true)
							{
								case ($row->get(TBGEditionAssigneesTable::UID) != 0):
									$this->_assignees['users'][$row->get(TBGEditionAssigneesTable::UID)]['editions'][$row->get(TBGEditionAssigneesTable::EDITION_ID)][$row->get(TBGEditionAssigneesTable::TARGET_TYPE)] = true;
									$this->_assignees['uids'][$row->get(TBGEditionAssigneesTable::UID)] = $row->get(TBGEditionAssigneesTable::UID);
									break;
								case ($row->get(TBGEditionAssigneesTable::CID) != 0):
									$this->_assignees['customers'][$row->get(TBGEditionAssigneesTable::CID)]['editions'][$row->get(TBGEditionAssigneesTable::EDITION_ID)][$row->get(TBGEditionAssigneesTable::TARGET_TYPE)] = true;
									break;
								case ($row->get(TBGEditionAssigneesTable::TID) != 0):
									$this->_assignees['teams'][$row->get(TBGEditionAssigneesTable::TID)]['editions'][$row->get(TBGEditionAssigneesTable::EDITION_ID)][$row->get(TBGEditionAssigneesTable::TARGET_TYPE)] = true;
									foreach (B2DB::getTable('TBGTeamMembersTable')->getUIDsForTeamID($row->get(TBGEditionAssigneesTable::TID)) as $uid)
									{
										$this->_assignees['uids'][$uid] = $uid;
									}
									break;
							}
						}
					}
				}
	
				if ($component_ids = array_keys($this->getComponents()))
				{
					if ($res = B2DB::getTable('TBGComponentAssigneesTable')->getByComponentIDs($component_ids))
					{
						while ($row = $res->getNextRow())
						{
							switch (true)
							{
								case ($row->get(TBGComponentAssigneesTable::UID) != 0):
									$this->_assignees['users'][$row->get(TBGComponentAssigneesTable::UID)]['components'][$row->get(TBGComponentAssigneesTable::COMPONENT_ID)][$row->get(TBGComponentAssigneesTable::TARGET_TYPE)] = true;
									$this->_assignees['uids'][$row->get(TBGComponentAssigneesTable::UID)] = $row->get(TBGComponentAssigneesTable::UID);
									break;
								case ($row->get(TBGComponentAssigneesTable::CID) != 0):
									$this->_assignees['customers'][$row->get(TBGComponentAssigneesTable::CID)]['components'][$row->get(TBGComponentAssigneesTable::COMPONENT_ID)][$row->get(TBGComponentAssigneesTable::TARGET_TYPE)] = true;
									break;
								case ($row->get(TBGComponentAssigneesTable::TID) != 0):
									$this->_assignees['teams'][$row->get(TBGComponentAssigneesTable::TID)]['components'][$row->get(TBGComponentAssigneesTable::COMPONENT_ID)][$row->get(TBGComponentAssigneesTable::TARGET_TYPE)] = true;
									foreach (B2DB::getTable('TBGTeamMembersTable')->getUIDsForTeamID($row->get(TBGComponentAssigneesTable::TID)) as $uid)
									{
										$this->_assignees['uids'][$uid] = $uid;
									}
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
		 * Return a list of user ids for all users assigned to this project
		 *
		 * @return array
		 */
		public function getAssignedUserIDs()
		{
			$this->_populateAssignees();
			return array_keys($this->_assignees['uids']);
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
			$crit->addUpdate(TBGProjectsTable::LOCKED, $this->_locked);
			$crit->addUpdate(TBGProjectsTable::PREFIX, $this->_prefix);
			$crit->addUpdate(TBGProjectsTable::USE_PREFIX, $this->_useprefix);
			$crit->addUpdate(TBGProjectsTable::USE_SCRUM, $this->_usescrum);
			$crit->addUpdate(TBGProjectsTable::ENABLE_BUILDS, $this->_enablebuilds);
			$crit->addUpdate(TBGProjectsTable::ENABLE_COMPONENTS, $this->_enablecomponents);
			$crit->addUpdate(TBGProjectsTable::ENABLE_EDITIONS, $this->_enableeditions);
			$crit->addUpdate(TBGProjectsTable::ENABLE_TASKS, $this->_enabletasks);
			$crit->addUpdate(TBGProjectsTable::VOTES, $this->_enablevotes);
			$crit->addUpdate(TBGProjectsTable::NAME, $this->_name);
			$crit->addUpdate(TBGProjectsTable::KEY, $this->_key);
			$crit->addUpdate(TBGProjectsTable::RELEASED, $this->_isreleased);
			$crit->addUpdate(TBGProjectsTable::PLANNED_RELEASE, $this->_isplannedreleased);
			$crit->addUpdate(TBGProjectsTable::IS_DEFAULT, $this->_isdefault);
			$crit->addUpdate(TBGProjectsTable::RELEASE_DATE, $this->_release_date);
			$crit->addUpdate(TBGProjectsTable::HOMEPAGE, $this->_homepage);
			$crit->addUpdate(TBGProjectsTable::DESCRIPTION, $this->_description);
			$crit->addUpdate(TBGProjectsTable::TIME_UNIT, $this->_timeunit);
			$crit->addUpdate(TBGProjectsTable::DEFAULT_STATUS, $this->_defaultstatus);
			$crit->addUpdate(TBGProjectsTable::DOC_URL, $this->_doc_url);
			$crit->addUpdate(TBGProjectsTable::OWNED_TYPE, $this->getOwnerType());
			$crit->addUpdate(TBGProjectsTable::OWNED_BY, $this->getOwnerID());
			$crit->addUpdate(TBGProjectsTable::LEAD_TYPE, $this->getLeaderType());
			$crit->addUpdate(TBGProjectsTable::LEAD_BY, $this->getLeaderID());
			$crit->addUpdate(TBGProjectsTable::QA_TYPE, $this->getQaResponsibleType());
			$crit->addUpdate(TBGProjectsTable::QA, $this->getQaResponsibleID());
			$crit->addUpdate(TBGProjectsTable::HRS_PR_DAY, $this->_hrsprday); 
			$crit->addUpdate(TBGProjectsTable::SHOW_IN_SUMMARY, $this->_show_in_summary);
			$crit->addUpdate(TBGProjectsTable::SUMMARY_DISPLAY, $this->_summary_display);
			$crit->addUpdate(TBGProjectsTable::ALLOW_CHANGING_WITHOUT_WORKING, $this->_can_change_wo_working);
			$crit->addUpdate(TBGProjectsTable::DELETED, $this->_deleted);
			$crit->addUpdate(TBGProjectsTable::HIDDEN_AFFECTS_BOX, $this->_affectshidden);
			$res = TBGProjectsTable::getTable()->doUpdateById($crit, $this->getID());

			if ($this->_dodelete)
			{
				TBGIssuesTable::getTable()->markIssuesDeletedByProjectID($this->getID());
				$this->_dodelete = false;
			}
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
				foreach (TBGBuild::getByProjectID($this->getID()) as $build)
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
				$this->_issuetypes = TBGIssuetype::getAllApplicableToProject($this->getID());
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
		 * Populates the internal array with unassigned issues
		 */
		protected function _populateUnassignedIssues()
		{
			if ($this->_unassignedissues == null)
			{
				$this->_unassignedissues = array();
				if ($res = TBGIssuesTable::getTable()->getByProjectIDandNoMilestone($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_unassignedissues[$row->get(TBGIssuesTable::ID)] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID));
					}
				}
			}
		}

		/**
		 * Returns an array with issues
		 *
		 * @return array
		 */
		public function getIssuesWithoutMilestone()
		{
			$this->_populateUnassignedIssues();
			return $this->_unassignedissues;
		}

		/**
		 * Returns whether the affected things box is hidden in a tab or not
		 *
		 * @return boolean
		 */
		public function isAffectsHidden()
		{
			return $this->_affectshidden;
		}
		
		/**
		 * Returns an array with unassigned user stories
		 *
		 * @return array
		 */
		public function getUnassignedStories()
		{
			$issues = $this->getIssuesWithoutMilestone();
			$unassigned_stories = array();
			foreach ($issues as $issue)
			{
				if ($issue->getIssueType() instanceof TBGIssuetype && $issue->getIssueType()->getIcon() == 'developer_report')
				{
					$unassigned_stories[$issue->getID()] = $issue;
				}
			}
			return $unassigned_stories;
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
				if ($res = B2DB::getTable('TBGVisibleMilestonesTable')->getAllByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$milestone = TBGContext::factory()->TBGMilestone($row->get(TBGMilestonesTable::ID), $row);
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
			B2DB::getTable('TBGVisibleMilestonesTable')->clearByProjectID($this->getID());
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
				B2DB::getTable('TBGVisibleMilestonesTable')->addByProjectIDAndMilestoneID($this->getID(), $milestone_id);
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
			if (!is_array($this->_issuecounts))
			{
				$this->_issuecounts = array();
			}
			if (!array_key_exists('all', $this->_issuecounts))
			{
				$this->_issuecounts['all'] = array();
			}
			if (empty($this->_issuecounts['all']))
			{
				list ($this->_issuecounts['all']['closed'], $this->_issuecounts['all']['open']) = TBGIssue::getIssueCountsByProjectID($this->getID());
			}
			if (empty($this->_issuecounts['last30']))
			{
				list ($closed, $open) = TBGLogTable::getTable()->getLast30IssueCountsByProjectID($this->getID());
				$this->_issuecounts['last30']['open'] = $open;
				$this->_issuecounts['last30']['closed'] = $closed;
			}
		}
		
		protected function _populateIssueCountsByIssueType($issuetype_id)
		{
			if ($this->_issuecounts === null)
			{
				$this->_issuecounts = array();
			}
			if (!array_key_exists('issuetype', $this->_issuecounts))
			{
				$this->_issuecounts['issuetype'] = array();
			}
			if (!array_key_exists($issuetype_id, $this->_issuecounts['issuetype']))
			{
				list ($this->_issuecounts['issuetype'][$issuetype_id]['closed'], $this->_issuecounts['issuetype'][$issuetype_id]['open']) = TBGIssue::getIssueCountsByProjectIDandIssuetype($this->getID(), $issuetype_id);
			}
		}

		protected function _populateIssueCountsByMilestone($milestone_id, $exclude_tasks = false)
		{
			if ($this->_issuecounts === null)
			{
				$this->_issuecounts = array();
			}
			if (!array_key_exists('milestone', $this->_issuecounts))
			{
				$this->_issuecounts['milestone'] = array();
			}
			if (!array_key_exists($milestone_id, $this->_issuecounts['milestone']))
			{
				list ($this->_issuecounts['milestone'][$milestone_id]['closed'], $this->_issuecounts['milestone'][$milestone_id]['open']) = TBGIssue::getIssueCountsByProjectIDandMilestone($this->getID(), $milestone_id, $exclude_tasks);
			}
		}

		protected function _populateVisibleIssuetypes()
		{
			if ($this->_visible_issuetypes === null)
			{
				$this->_visible_issuetypes = array();
				if ($res = B2DB::getTable('TBGVisibleIssueTypesTable')->getAllByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_visible_issuetypes[$row->get(TBGIssueTypesTable::ID)] = TBGContext::factory()->TBGIssuetype($row->get(TBGIssueTypesTable::ID), $row);
					}
				}
			}
		}
		
		/**
		 * Returns all issue types visible in the project summary block
		 * 
		 * @return array|TBGIssuetype
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
			B2DB::getTable('TBGVisibleIssueTypesTable')->clearByProjectID($this->getID());
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
				B2DB::getTable('TBGVisibleIssueTypesTable')->addByProjectIDAndIssuetypeID($this->getID(), $issuetype_id);
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
			return $this->_issuecounts['all']['closed'] + $this->_issuecounts['all']['open'];
		}

		public function getLast30Counts()
		{
			$this->_populateIssueCounts();
			return $this->_issuecounts['last30'];
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
			return $this->_issuecounts['issuetype'][$issuetype]['closed'] + $this->_issuecounts['issuetype'][$issuetype]['open'];
		}

		/**
		 * Returns the number of issues for this project with a specific milestone
		 * 
		 * @param integer $milestone ID of the milestone
		 * @param boolean $exclude_tasks Whether to exclude tasks
		 * 
		 * @return integer
		 */
		public function countIssuesByMilestone($milestone, $exclude_tasks = false)
		{
			$this->_populateIssueCountsByMilestone($milestone, $exclude_tasks);
			return $this->_issuecounts['milestone'][$milestone]['closed'] + $this->_issuecounts['milestone'][$milestone]['open'];
		}
		
		/**
		 * Returns the number of open issues for this project
		 * 
		 * @return integer
		 */
		public function countAllOpenIssues()
		{
			$this->_populateIssueCounts();
			return $this->_issuecounts['all']['open'];
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
			return $this->_issuecounts['issuetype'][$issue_type]['open'];
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
			return $this->_issuecounts['milestone'][$milestone]['open'];
		}
		
		/**
		 * Returns the number of closed issues for this project
		 * 
		 * @return integer
		 */
		public function countAllClosedIssues()
		{
			$this->_populateIssueCounts();
			return $this->_issuecounts['all']['closed'];
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
			return $this->_issuecounts['issuetype'][$issue_type]['closed'];
		}

		/**
		 * Returns the number of closed issues for this project with a specific milestone
		 * 
		 * @param integer $milestone ID of the milestone
		 * 
		 * @return integer
		 */
		public function countClosedIssuesByMilestone($milestone, $exclude_tasks = false)
		{
			$this->_populateIssueCountsByMilestone($milestone, $exclude_tasks);
			return $this->_issuecounts['milestone'][$milestone]['closed'];
		}
		
		/**
		 * Returns the percentage of a given number related to another given number
		 * 
		 * @param integer $num_1 percentage number
		 * @param integer $num_max total number
		 * 
		 * @return integer The percentage
		 */
		protected function _getPercentage($num_1, $num_max)
		{
			$pct = 0;
			
			if ($num_max > 0 && $num_1 > 0)
			{
				$multiplier = 100 / $num_max;
				$pct = $num_1 * $multiplier;
			}
			
			return (int) $pct;
		}
		
		/**
		 * Returns the percentage of closed issues for this project
		 * 
		 * @return integer
		 */
		public function getClosedPercentageForAllIssues()
		{
			return $this->_getPercentage($this->countAllClosedIssues(), $this->countAllIssues());
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
			return $this->_getPercentage($this->countClosedIssuesByType($issue_type), $this->countIssuesByType($issue_type));
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
			return $this->_getPercentage($this->countClosedIssuesByMilestone($milestone), $this->countIssuesByMilestone($milestone));
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
		 * Checks to see if a list of issues is shown in the frontpage summary
		 *
		 * @return boolean
		 */
		public function isIssuelistVisibleInFrontpageSummary()
		{
			return ($this->getFrontpageSummaryType() == 'issuelist') ? true : false;
		}

		public function getOpenIssuesForFrontpageSummary($merged = false)
		{
			$res = TBGIssuesTable::getTable()->getOpenIssuesByProjectIDAndIssueTypes($this->getID(), array_keys($this->getVisibleIssuetypes()));

			$retval = array();
			if (!$merged)
			{
				foreach ($this->getVisibleIssuetypes() as $issuetype_id => $issuetype)
				{
					$retval[$issuetype_id] = array('issuetype' => $issuetype, 'issues' => array());
				}
			}
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID));;
					if (!$merged)
					{
						$retval[$row->get(TBGIssuesTable::ISSUE_TYPE)]['issues'][] = $issue;
					}
					else
					{
						$retval[] = $issue;
					}
				}
			}

			return $retval;
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
			return $this->_getFieldsArray($issue_type, true);
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
			return $this->_getFieldsArray($issue_type, false);
		}
		
		/**
		 * Return an array specifying visibility, requirement and choices for fields in issues
		 * 
		 * @param integer $issue_type
		 * @param boolean $reportable[optional] Whether to only include fields that can be reported
		 * 
		 * @return array
		 */
		protected function _getFieldsArray($issue_type, $reportable = true)
		{
			if (!isset($this->_fieldsarrays[$issue_type][(int) $reportable]))
			{
				$retval = array();
				if (!($res = B2DB::getTable('TBGIssueFieldsTable')->getByProjectIDandIssuetypeID($this->getID(), $issue_type)))
				{
					$res = B2DB::getTable('TBGIssueFieldsTable')->getByIssuetypeID($issue_type);
				}
				if ($res)
				{
					$builtin_types = TBGDatatype::getAvailableFields(true);
					while ($row = $res->getNextRow())
					{
						if (!$reportable || (bool) $row->get(TBGIssueFieldsTable::REPORTABLE) == true)
						{
							if ($reportable)
							{
								if (in_array($row->get(TBGIssueFieldsTable::FIELD_KEY), $builtin_types) && (!$this->fieldPermissionCheck($row->get(TBGIssueFieldsTable::FIELD_KEY), $reportable) && !($row->get(TBGIssueFieldsTable::REQUIRED) && $reportable))) continue;
								elseif (!in_array($row->get(TBGIssueFieldsTable::FIELD_KEY), $builtin_types) && (!$this->fieldPermissionCheck($row->get(TBGIssueFieldsTable::FIELD_KEY), $reportable, true) && !($row->get(TBGIssueFieldsTable::REQUIRED) && $reportable))) continue;
							}
							$field_key = $row->get(TBGIssueFieldsTable::FIELD_KEY);
							$retval[$field_key] = array('required' => (bool) $row->get(TBGIssueFieldsTable::REQUIRED), 'additional' => (bool) $row->get(TBGIssueFieldsTable::ADDITIONAL));
							if (!in_array($field_key, $builtin_types))
							{
								$retval[$field_key]['custom'] = true;
								$custom_type = TBGCustomDatatype::getByKey($field_key);
								if ($custom_type instanceof TBGCustomDatatype)
								{
									$retval[$field_key]['custom_type'] = $custom_type->getType();
								}
								else
								{
									unset($retval[$field_key]);
								}
							}
						}
					}
					if (array_key_exists('user_pain', $retval))
					{
						$retval['pain_bug_type'] = array('required' => $retval['user_pain']['required']);
						$retval['pain_likelihood'] = array('required' => $retval['user_pain']['required']);
						$retval['pain_effect'] = array('required' => $retval['user_pain']['required']);
					}
					
					if ($reportable)
					{
						foreach ($retval as $key => $return_details)
						{
							if ($key == 'edition' || array_key_exists('custom', $return_details) && $return_details['custom'] && in_array($return_details['custom_type'], array(TBGCustomDatatype::EDITIONS_LIST, TBGCustomDatatype::EDITIONS_CHOICE)))
							{
								$retval[$key]['values'] = array();
								foreach ($this->getEditions() as $edition)
								{
									$retval[$key]['values'][$edition->getID()] = $edition->getName();
								}
								if (!$this->isEditionsEnabled() || empty($retval[$key]['values']))
								{
									if (!$retval[$key]['required'])
									{
										unset($retval[$key]);
									}
									else
									{
										unset($retval[$key]['values']);
									}
								}
							}
							elseif ($key == 'status' || array_key_exists('custom', $return_details) && $return_details['custom'] && in_array($return_details['custom_type'], array(TBGCustomDatatype::EDITIONS_LIST, TBGCustomDatatype::STATUS_CHOICE)))
							{
								$retval[$key]['values'] = array();
								foreach (TBGStatus::getAll() as $status)
								{
									$retval[$key]['values'][$status->getID()] = $status->getName();
								}
								if (empty($retval[$key]['values']))
								{
									if (!$retval[$key]['required'])
									{
										unset($retval[$key]);
									}
									else
									{
										unset($retval[$key]['values']);
									}
								}
							}
							elseif ($key == 'component' || array_key_exists('custom', $return_details) && $return_details['custom'] && in_array($return_details['custom_type'], array(TBGCustomDatatype::COMPONENTS_LIST, TBGCustomDatatype::COMPONENTS_CHOICE)))
							{
								$retval[$key]['values'] = array();
								foreach ($this->getComponents() as $component)
								{
									$retval[$key]['values'][$component->getID()] = $component->getName();
								}
								if (!$this->isComponentsEnabled() || empty($retval[$key]['values']))
								{
									if (!$retval[$key]['required'])
									{
										unset($retval[$key]);
									}
									else
									{
										unset($retval[$key]['values']);
									}
								}
							}
							elseif ($key == 'build' || array_key_exists('custom', $return_details) && $return_details['custom'] && in_array($return_details['custom_type'], array(TBGCustomDatatype::RELEASES_LIST, TBGCustomDatatype::RELEASES_CHOICE)))
							{
								$retval[$key]['values'] = array();
								foreach ($this->getBuilds() as $build)
								{
									$retval[$key]['values'][$build->getID()] = $build->getName();
								}
								if (!$this->isBuildsEnabled() || empty($retval[$key]['values']))
								{
									if (!$retval[$key]['required'])
									{
										unset($retval[$key]);
									}
									else
									{
										unset($retval[$key]['values']);
									}
								}
							}
						}
					}
				}
				$this->_fieldsarrays[$issue_type][(int) $reportable] = $retval;
			}
			
			return $this->_fieldsarrays[$issue_type][(int) $reportable];
		}
				
		/**
		 * Whether or not the current user can access the project
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return TBGContext::getUser()->hasPermission('canseeproject', $this->getID(), 'core');
		}
		
		public function hasIcon()
		{
			return (bool) (file_exists(TBGContext::getIncludePath() . 'thebuggenie/project_icons/' . $this->getKey() . '.png'));
		}
		
		public function getIcon()
		{
			return ($this->hasIcon()) ? 'project_icons/' . $this->getKey() . '.png' : 'icon_project.png';			
		}

		protected function _populateLogItems($limit = null, $important = true)
		{
			$varname = ($important) ? '_recentimportantlogitems' : '_recentlogitems';
			if ($this->$varname === null)
			{
				$this->$varname = array();
				if ($important)
				{
					$res = TBGLogTable::getTable()->getImportantByProjectID($this->getID(), $limit);
				}
				else
				{
					$res = TBGLogTable::getTable()->getByProjectID($this->getID(), $limit);
				}
				if ($res)
				{
					$this->$varname = $res;
				}
			}
		}

		/**
		 * Return this projects most recent log items
		 *
		 * @return array A list of log items
		 */
		public function getRecentLogItems($limit = null, $important = true)
		{
			$this->_populateLogItems($limit, $important);
			return ($important) ? $this->_recentimportantlogitems : $this->_recentlogitems;
		}

		protected function _populatePriorityCount()
		{
			if ($this->_prioritycount === null)
			{
				$this->_prioritycount = array();
				$this->_prioritycount[0] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				foreach (TBGPriority::getAll() as $priority_id => $priority)
				{
					$this->_prioritycount[$priority_id] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				}
				foreach (TBGIssuesTable::getTable()->getPriorityCountByProjectID($this->getID()) as $priority_id => $priority_count)
				{
					$this->_prioritycount[$priority_id] = $priority_count;
				}
			}
		}

		public function getPriorityCount()
		{
			$this->_populatePriorityCount();
			return $this->_prioritycount;
		}

		protected function _populateRecentIssues()
		{
			if ($this->_recentissues === null)
			{
				$this->_recentissues = array();
				if ($res = TBGIssuesTable::getTable()->getRecentByProjectIDandIssueType($this->getID(), array('bug_report'), 10))
				{
					while ($row = $res->getNextRow())
					{
						$this->_recentissues[] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					}
				}
			}
		}

		protected function _populateRecentFeatures()
		{
			if ($this->_recentfeatures === null)
			{
				$this->_recentfeatures = array();
				if ($res = TBGIssuesTable::getTable()->getRecentByProjectIDandIssueType($this->getID(), array('feature_request', 'enhancement')))
				{
					while ($row = $res->getNextRow())
					{
						$this->_recentfeatures[] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					}
				}
			}
		}

		protected function _populateRecentIdeas()
		{
			if ($this->_recentideas === null)
			{
				$this->_recentideas = array();
				if ($res = TBGIssuesTable::getTable()->getRecentByProjectIDandIssueType($this->getID(), array('idea')))
				{
					while ($row = $res->getNextRow())
					{
						$this->_recentideas[] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					}
				}
			}
		}

		/**
		 * Return this projects 10 most recent issues
		 *
		 * @return array A list of TBGIssues
		 */
		public function getRecentIssues()
		{
			$this->_populateRecentIssues();
			return $this->_recentissues;
		}

		/**
		 * Return this projects 5 most recent feature requests / enhancements
		 *
		 * @return array A list of TBGIssues
		 */
		public function getRecentFeatures()
		{
			$this->_populateRecentFeatures();
			return $this->_recentfeatures;
		}

		/**
		 * Return this projects 5 most recent ideas / suggestions
		 *
		 * @return array A list of TBGIssues
		 */
		public function getRecentIdeas()
		{
			$this->_populateRecentIdeas();
			return $this->_recentideas;
		}

		protected function _populateRecentActivities($limit = null, $important = true)
		{
			if ($this->_recentactivities === null)
			{
				$this->_recentactivities = array();
				foreach ($this->getBuilds() as $build)
				{
					if ($build->isReleased() && $build->getReleaseDate() <= time())
					{
						if ($build->getReleaseDate() > time()) continue;
						if (!array_key_exists($build->getReleaseDate(), $this->_recentactivities))
						{
							$this->_recentactivities[$build->getReleaseDate()] = array();
						}
						$this->_recentactivities[$build->getReleaseDate()][] = array('change_type' => 'build_release', 'info' => $build->getName());
					}
				}
				foreach ($this->getAllMilestones() as $milestone)
				{
					if ($milestone->isVisible() && $milestone->isStarting() && $milestone->isSprint())
					{
						if ($milestone->getStartingDate() > time()) continue;
						if (!array_key_exists($milestone->getStartingDate(), $this->_recentactivities))
						{
							$this->_recentactivities[$milestone->getStartingDate()] = array();
						}
						$this->_recentactivities[$milestone->getStartingDate()][] = array('change_type' => 'sprint_start', 'info' => $milestone->getName());
					}
					if ($milestone->isVisible() && $milestone->isScheduled() && $milestone->isReached())
					{
						if ($milestone->getReachedDate() > time()) continue;
						if (!array_key_exists($milestone->getReachedDate(), $this->_recentactivities))
						{
							$this->_recentactivities[$milestone->getReachedDate()] = array();
						}
						$this->_recentactivities[$milestone->getReachedDate()][] = array('change_type' => (($milestone->isSprint()) ? 'sprint_end' : 'milestone_release'), 'info' => $milestone->getName());
					}
				}
				
				foreach ($this->getRecentLogItems($limit, $important) as $log_item)
				{
					if (!array_key_exists($log_item['timestamp'], $this->_recentactivities))
					{
						$this->_recentactivities[$log_item['timestamp']] = array();
					}
					$this->_recentactivities[$log_item['timestamp']][] = $log_item;
				}
				
				/*if ($important)
				{
					if ($res = TBGCommentsTable::getTable()->getRecentCommentsByProjectID($this->getID()))
					{
						while ($row = $res->getNextRow())
						{
							//$this->_recentactivities[$row->get(TBGCommentsTable::POSTED)][] = 
						}
					}
				}*/
				krsort($this->_recentactivities, SORT_NUMERIC);
			}
		}

		/**
		 * Return a list of recent activity for the project
		 *
		 * @param integer $limit Limit number of activities
		 * @return array
		 */
		public function getRecentActivities($limit = null, $important = false)
		{
			$this->_populateRecentActivities($limit, $important);
			if ($limit !== null)
			{
				$recent_activities = array_slice($this->_recentactivities, 0, $limit, true);
			}
			else
			{
				$recent_activities = $this->_recentactivities;
			}
			
			return $recent_activities;
		}
		
		public function getRecentImportantActivities($limit = null)
		{
			return $this->getRecentActivities($limit, true);
		}

		public function clearRecentActivities()
		{
			$this->_recentactivities = null;
			$this->_recentissues = null;
			$this->_recentfeatures = null;
			$this->_recentlogitems = null;
		}

		/**
		 * Perform a permission check based on a key, and whether or not to
		 * check if the permission is explicitly set
		 *
		 * @param string $key The permission key to check for
		 * @param boolean $exclusive Whether to make sure the permission is explicitly set
		 *
		 * @return boolean
		 */
		public function permissionCheck($key, $explicit = false)
		{
			$retval = TBGContext::getUser()->hasPermission($key, $this->getID(), 'core', true, null);
			if ($explicit)
			{
				$retval = ($retval !== null) ? $retval : TBGContext::getUser()->hasPermission($key, 0, 'core', true, null);
			}
			else
			{
				$retval = ($retval !== null) ? $retval : TBGContext::getUser()->hasPermission($key);
			}
			
			return $retval;
		}

		public function fieldPermissionCheck($field, $reportable, $custom = false)
		{
			if ($custom)
			{
				return (bool) ($this->permissionCheck('caneditcustomfields'.$field) || $this->permissionCheck('caneditissuecustomfields'));
			}
			elseif (in_array($field, array('title', 'description', 'reproduction_steps')))
			{
				return (bool) ($this->permissionCheck('caneditissue'.$field) || $this->permissionCheck('caneditissuebasic') || $this->permissionCheck('cancreateissues') || $this->permissionCheck('cancreateandeditissues'));
			}
			elseif (in_array($field, array('builds', 'editions', 'components', 'links', 'files')))
			{
				return (bool) ($this->permissionCheck('canadd'.$field) || $this->permissionCheck('canaddextrainformationtoissues'));
			}
			else
			{
				return (bool) ($this->permissionCheck('caneditissue'.$field) || $this->permissionCheck('caneditissue'));
			}
			return false;
		}
		
	}
