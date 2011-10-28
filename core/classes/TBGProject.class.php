<?php

	/**
	 * Project class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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

		static protected $_b2dbtablename = 'TBGProjectsTable';
		
		/**
		 * Project list cache
		 *
		 * @var array
		 */
		static protected $_projects = null;

		static protected $_num_projects = null;

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
		protected $_use_prefix = false;

		/**
		 * Whether or not the project uses sprint planning
		 *
		 * @var boolean
		 */
		protected $_use_scrum = true;

		/**
		 * Time unit for this project
		 *
		 * @var integer
		 */
		protected $_timeunit;
		
		/**
		 * Whether or not the project uses builds
		 *
		 * @var boolean
		 */
		protected $_enable_builds = true;

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
		protected $_enable_editions = null;
		
		/**
		 * Whether or not the project uses components
		 *
		 * @var boolean
		 */
		protected $_enable_components = null;
		
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
		 * The small project icon, if set
		 * 
		 * @Class TBGFile
		 * @var TBGFile
		 */
		protected $_small_icon = null;
		
		/**
		 * The large project icon, if set
		 * 
		 * @Class TBGFile
		 * @var TBGFile
		 */
		protected $_large_icon = null;

		/**
		 * Issues registered for this project with no milestone assigned
		 *
		 * @var array
		 */
		protected $_unassignedissues = null;
		
		/**
		 * Developer reports registered for this project with no milestone assigned
		 *
		 * @var array
		 */
		protected $_unassignedstories = null;

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
		 * List of assigned users and teams
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
		protected $_allow_freelancing = false;
		
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
		protected $_recentissues = array();

		/**
		 * Priority count
		 *
		 * @var array
		 */
		protected $_prioritycount = null;

		/**
		 * Status count
		 *
		 * @var array
		 */
		protected $_statuscount = null;

		/**
		 * Category count
		 *
		 * @var array
		 */
		protected $_categorycount = null;

		/**
		 * Resolution count
		 *
		 * @var array
		 */
		protected $_resolutioncount = null;

		/**
		 * State count
		 *
		 * @var array
		 */
		protected $_statecount = null;

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
		 * Recent documentation requests
		 *
		 * @var array
		 */
		protected $_recentdocumentationrequests = null;
		
		/**
		 * Recent new enhancements
		 *
		 * @var array
		 */
		protected $_recentenhancements = null;

		/**
		 * Recent support requests
		 *
		 * @var array
		 */
		protected $_recentsupportrequests = null;
		
		/**
		 * Recent tasks
		 *
		 * @var array
		 */
		protected $_recenttasks = null;

		/**
		 * Recent developer reports
		 *
		 * @var array
		 */
		protected $_recentdeveloperreports = null;
		
		/**
		 * The selected workflow scheme
		 * 
		 * @var TBGWorkflowScheme
		 * @Class TBGWorkflowScheme
		 */
		protected $_workflow_scheme_id = 1;
		
		/**
		 * The selected workflow scheme
		 * 
		 * @var TBGIssuetypeScheme
		 * @Class TBGIssuetypeScheme
		 */
		protected $_issuetype_scheme_id = 1;
		
		/**
		 * Assigned client
		 * 
		 * @var TBGClient
		 * @Class TBGClient
		 */
		protected $_client = null;
		
		/**
		 * Autoassignment
		 * 
		 * @var boolean
		 */
		protected $_autoassign = null;
		
		/**
		 * Parent project
		 * 
		 * @var TBGProject
		 * @Class TBGProject
		 */
		protected $_parent = null;
		
		/**
		 * Child projects
		 * 
		 * @var Array
		 */
		protected $_children = null;
		
		/**
		 * Recent activities
		 * 
		 * @var Array
		 */
		protected $_recentactivities = null;
		
		/**
		 * Whether to show a "Download" link and corresponding section
		 * 
		 * @var boolean
		 */
		protected $_has_downloads = true;
		
		/**
		 * Whether a project is archived (read-only mode)
		 * 
		 * @var boolean
		 */
		protected $_archived = false;
		
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
			self::_populateProjects();
			return (array_key_exists($key, self::$_projects)) ? self::$_projects[$key] : null;
		}
		
		public static function getValidSubprojects(TBGProject $project)
		{
			$valid_subproject_targets = array();
			foreach (self::getAll() as $aproject)
			{
				if ($aproject->getId() == $project->getId()) continue;
				$valid_subproject_targets[$aproject->getKey()] = $aproject;
			}

			// if this project has no children, life is made easy
			if ($project->hasChildren())
			{
				foreach ($project->getChildren() as $child)
				{
					unset($valid_subproject_targets[$child->getKey()]);
				}
			}
			
			return $valid_subproject_targets;
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
						if (TBGContext::isUpgrademode() || ($project->hasAccess() && !$project->isDeleted()))
						{
							self::$_projects[$project->getKey()] = $project;
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
		 * Retrieve all projects by parent ID
		 * 
		 * @return array
		 */
		public static function getAllByParentID($id)
		{
			self::_populateProjects();
			$final = array();
			foreach (self::$_projects as $project)
			{
				if (($project->getParent() instanceof TBGProject) && $project->getParent()->getID() == $id)
				{
					$final[] = $project;
				}
			}
			return $final;
		}
		
		/**
		 * Retrieve all projects with no parent. If the parent is archived, the project will not be shown
		 * 
		 * @param bool $archived[optional] Show archived projects instead
		 * 
		 * @return array
		 */
		public static function getAllRootProjects($archived = false)
		{
			self::_populateProjects();
			$final = array();
			foreach (self::$_projects as $project)
			{
				if ($archived)
				{
					if (!($project->getParent() instanceof TBGProject) && $project->isArchived())
					{
						$final[] = $project;
					}
				}
				else
				{
					if (!($project->getParent() instanceof TBGProject) && !$project->isArchived())
					{
						$final[] = $project;
					}
				}
			}
			return $final;
		}

		// Archived projects do not count
		public static function getProjectsCount()
		{
			if (self::$_num_projects === null)
			{
				if (self::$_projects !== null)
					self::$_num_projects = count(self::$_projects);
				else
					self::$_num_projects = TBGProjectsTable::getTable()->countProjects();
			}

			return self::$_num_projects;
		}
		
		/**
		 * Retrieve all projects by client ID
		 * 
		 * @return array
		 */
		public static function getAllByClientID($id)
		{
			self::_populateProjects();
			$final = array();
			foreach (self::$_projects as $project)
			{
				if (($project->getClient() instanceof TBGClient) && $project->getClient()->getID() == $id)
				{
					$final[] = $project;
				}
			}
			return $final;
		}
		
		/**
		 * Retrieve all projects by leader
		 * 
		 * @param TBGUser or TBGTeam
		 * @return array
		 */
		public static function getAllByLeader($leader)
		{
			self::_populateProjects();
			$final = array();
			
			if (!($leader instanceof TBGUser) && !($leader instanceof TBGTeam))
			{
				return false;
			}
			
			if ($leader instanceof TBGUser)
			{
				foreach (self::$_projects as $project)
				{
					if (($project->getLeaderID() == $leader->getID()) && ($project->getLeaderType() == TBGIdentifiableClass::TYPE_USER))
					{
						$final[] = $project;
					}
				}
				return $final;
			}
			else
			{
				foreach (self::$_projects as $project)
				{
					if (($project->getLeaderID() == $leader->getID()) && ($project->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM))
					{
						$final[] = $project;
					}
				}
				return $final;
			}
		}
		
		/**
		 * Retrieve all projects by owner
		 * 
		 * @param TBGUser or TBGTeam
		 * @return array
		 */
		public static function getAllByOwner($owner)
		{
			self::_populateProjects();
			$final = array();
			
			if (!($owner instanceof TBGUser) && !($owner instanceof TBGTeam))
			{
				return false;
			}
			
			if ($owner instanceof TBGUser)
			{
				foreach (self::$_projects as $project)
				{
					if (($project->getOwnerID() == $owner->getID()) && ($project->getOwnerType() == TBGIdentifiableClass::TYPE_USER))
					{
						$final[] = $project;
					}
				}
				return $final;
			}
			else
			{
				foreach (self::$_projects as $project)
				{
					if (($project->getOwnerID() == $owner->getID()) && ($project->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM))
					{
						$final[] = $project;
					}
				}
				return $final;
			}
		}
		
		/**
		 * Retrieve all projects by qa
		 * 
		 * @param TBGUser or TBGTeam
		 * @return array
		 */
		public static function getAllByQaResponsible($qa)
		{
			self::_populateProjects();
			$final = array();
			
			if (!($qa instanceof TBGUser) && !($qa instanceof TBGTeam))
			{
				return false;
			}
			
			if ($qa instanceof TBGUser)
			{
				foreach (self::$_projects as $project)
				{
					if (($project->getQaResponsibleID() == $qa->getID()) && ($project->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER))
					{
						$final[] = $project;
					}
				}
				return $final;
			}
			else
			{
				foreach (self::$_projects as $project)
				{
					if (($project->getQaResponsibleID() == $qa->getID()) && ($project->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM))
					{
						$final[] = $project;
					}
				}
				return $final;
			}
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
		public function _preSave($is_new)
		{
			$project = self::getByKey($this->getKey()); // TBGProjectsTable::getTable()->getByKey($this->getKey());
			if ($project instanceof TBGProject && $project->getID() != $this->getID())
			{
				throw new InvalidArgumentException("A project with this key already exists");
			}
			if ($is_new)
			{
				$this->setIssuetypeScheme(TBGIssuetypeScheme::getCoreScheme());
				$this->setWorkflowScheme(TBGWorkflowScheme::getCoreScheme());
			}
		}

		public function _postSave($is_new)
		{
			if ($is_new)
			{
				self::$_num_projects = null;
				self::$_projects = null;

				TBGDashboardViewsTable::getTable()->setDefaultViews($this->getID(), TBGDashboardViewsTable::TYPE_PROJECT);

				TBGContext::setPermission("canseeproject", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canseeprojecthierarchy", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canmanageproject", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("page_project_allpages_access", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canvoteforissues", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canlockandeditlockedissues", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("cancreateandeditissues", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("caneditissue", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("caneditissuecustomfields", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canaddextrainformationtoissues", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);
				TBGContext::setPermission("canpostseeandeditallcomments", $this->getID(), "core", TBGContext::getUser()->getID(), 0, 0, true);

				TBGEvent::createNew('core', 'TBGProject::createNew', $this)->trigger();
			}
			if ($this->_dodelete)
			{
				TBGIssuesTable::getTable()->markIssuesDeletedByProjectID($this->getID());
				$this->_dodelete = false;
			}
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
		 * @param \b2db\Row $row
 		 */
		public function _construct(\b2db\Row $row, $foreign_key = null)
		{
			TBGEvent::createNew('core', 'TBGProject::__construct', $this)->trigger();
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
		 * Set whether the project uses sprint planning
		 *
		 * @param boolean $val
		 */
		public function setUsesScrum($val = true)
		{
			$this->_use_scrum = $val;
		}

		/**
		 * Return whether the project uses sprint planning
		 *
		 * @return boolean
		 */
		public function usesScrum()
		{
			return (bool) $this->_use_scrum;
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
		 * Set autoassign setting
		 *
		 * @param boolean $autoassign
		 */
		public function setAutoassign($autoassign)
		{
			$this->_autoassign = $autoassign;
		}
		
		/**
		 * Mark the project as deleted
		 *
		 * @return boolean
		 */
		public function setDeleted()
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
			$this->_key = mb_strtolower($this->getStrippedProjectName());
			if ($this->_key == '') $this->_key = 'project'.$this->getID();
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
		
		public function setKey($key)
		{
			$this->_key = $key;
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
		 * Is builds enabled
		 *
		 * @return boolean
		 */
		public function isBuildsEnabled()
		{
			return $this->_enable_builds;
		}

		/**
		 * Set if the project uses builds
		 *
		 * @param boolean $builds_enabled
		 */
		public function setBuildsEnabled($builds_enabled)
		{
			$this->_enable_builds = (bool) $builds_enabled;
		}

		/**
		 * Is editions enabled
		 *
		 * @return boolean
		 */
		public function isEditionsEnabled()
		{
			return $this->_enable_editions;
		}
		
		/**
		 * Set if the project uses editions
		 *
		 * @param boolean $editions_enabled
		 */
		public function setEditionsEnabled($editions_enabled)
		{
			$this->_enable_editions = (bool) $editions_enabled;
		}
		
		/**
		 * Is components enabled
		 *
		 * @return boolean
		 */
		public function isComponentsEnabled()
		{
			return $this->_enable_components;
		}
		
		/**
		 * Set if the project uses components
		 *
		 * @param boolean $components_enabled
		 */
		public function setComponentsEnabled($components_enabled)
		{
			$this->_enable_components = (bool) $components_enabled;
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
			return $this->_use_prefix;
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
		 * Returns whether or not the project has been archived
		 *
		 * @return boolean
		 */
		public function isArchived()
		{
			return $this->_archived;
		}

		/**
		 * Set the archived state
		 * 
		 * @var boolean $archived
		 */
		public function setArchived($archived)
		{
			$this->_archived = $archived;
		}

		/**
		 * Set whether or not the project uses prefix
		 *
		 * @param boolean $use_prefix
		 */
		public function setUsePrefix($use_prefix)
		{
			$this->_use_prefix = (bool) $use_prefix;
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
			$edition = new TBGEdition();
			$edition->setName($e_name);
			$edition->setProject($this);
			$edition->save();
			
			return $edition;
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
			$component = new TBGComponent();
			$component->setName($c_name);
			$component->setProject($this);
			$component->save();
			
			return $component;
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
				uasort($this->_allmilestones, function($milestone_a, $milestone_b) {
					if (!$milestone_a->isScheduled() && !$milestone_a->isStarting() && !$milestone_b->isScheduled() && !$milestone_b->isStarting()) return 1;
					if ($milestone_a->isStarting() && $milestone_b->isStarting())
						return ($milestone_a->getStartingDate() < $milestone_b->getStartingDate()) ? -1 : 1;
					
					if ($milestone_a->isScheduled() && $milestone_b->isScheduled())
						return ($milestone_a->getScheduledDate() < $milestone_b->getScheduledDate()) ? -1 : 1;
					
					if ($milestone_a->isStarting() && $milestone_b->isScheduled())
						return ($milestone_a->getStartingDate() < $milestone_b->getScheduledDate()) ? -1 : 1;

					if ($milestone_a->isScheduled() && $milestone_b->isStarting())
						return ($milestone_a->getScheduledDate() < $milestone_b->getStartingDate()) ? -1 : 1;

					if ($milestone_a->isStarting()) return -1;
					if ($milestone_b->isStarting()) return 1;
					
					if ($milestone_a->isScheduled()) return -1;
					if ($milestone_b->isScheduled()) return 1;
					
					if ($milestone_a->isOverdue()) return -1;
					if ($milestone_b->isOverdue()) return 1;
					
					if (!$milestone_b->isStarting() && !$milestone_b->isScheduled()) return -1;
					
					return 0;
				});
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
			$milestone = new TBGMilestone();
			$milestone->setName($m_name);
			$milestone->setType($type);
			$milestone->setProject($this);
			$milestone->save();
			
			return $milestone;
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
				$curr_day = time();
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
		 * Returns a list of milestones and sprints starting soon
		 * 
		 * @param integer $days[optional] Number of days, default 21 
		 * 
		 * @return array
		 */
		public function getStartingMilestonesAndSprints($days = 21)
		{
			$ret_arr = array();
			if ($allmilestones = $this->getAllMilestones())
			{
				$curr_day = time();
				foreach ($allmilestones as $milestone)
				{
					if (($milestone->getStartingDate() > $curr_day) && ($milestone->getStartingDate() < ($curr_day + (86400 * $days))))
					{
						$ret_arr[$milestone->getID()] = $milestone;
					}
				}
			}
			return $ret_arr;
		}
		
		public function removeAssignee($assignee_type, $assignee_id)
		{
			TBGProjectAssigneesTable::getTable()->removeAssigneeFromProject($assignee_type, $assignee_id, $this->getID());
			foreach ($this->getEditions() as $edition)
			{
				TBGEditionAssigneesTable::getTable()->removeAssigneeFromEdition($assignee_type, $assignee_id, $edition->getID());
			}
			foreach ($this->getComponents() as $component)
			{
				TBGComponentAssigneesTable::getTable()->removeAssigneeFromComponent($assignee_type, $assignee_id, $component->getID());
			}
		}

		/**
		 * Adds an assignee with a given role
		 * 
		 * @param TBGIdentifiable $assignee The user or team to add
		 * @param integer $role The role to add
		 *  
		 * @return null
		 */
		public function addAssignee($assignee, $role)
		{
			switch (true)
			{
				case ($assignee instanceof TBGUser):
					if (!$res = \b2db\Core::getTable('TBGProjectAssigneesTable')->getByProjectAndRoleAndUser($this->getID(), $role, $assignee->getID()))
					{
						\b2db\Core::getTable('TBGProjectAssigneesTable')->addByProjectAndRoleAndUser($this->getID(), $role, $assignee->getID());
					}
					break;
				case ($assignee instanceof TBGTeam):
					if (!($res = \b2db\Core::getTable('TBGProjectAssigneesTable')->getByProjectAndRoleAndTeam($this->getID(), $role, $assignee->getID())))
					{
						\b2db\Core::getTable('TBGProjectAssigneesTable')->addByProjectAndRoleAndTeam($this->getID(), $role, $assignee->getID());
					}
					break;
			}
			$this->applyInitialPermissionSet($assignee, $role);
		}

		protected function _populateAssignees()
		{
			if ($this->_assignees === null)
			{
				$this->_assignees = TBGProjectAssigneesTable::getTable()->getByProjectID($this->getID());
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
		
		public function getAssignedUsers()
		{
			$this->_populateAssignees();
			$users = array();
			foreach (array_keys($this->_assignees['users']) as $user_id)
			{
				$users[$user_id] = TBGContext::factory()->TBGUser($user_id);
			}
			return $users;
		}
		
		public function getAssignedTeams()
		{
			$this->_populateAssignees();
			$teams = array();
			foreach (array_keys($this->_assignees['teams']) as $team_id)
			{
				$teams[$team_id] = TBGContext::factory()->TBGTeam($team_id);
			}
			return $teams;
		}

		/**
		 * Return whether a user can change details about an issue without working on the issue
		 *  
		 * @return boolean
		 */
		public function canChangeIssuesWithoutWorkingOnThem()
		{
			return (bool) $this->_allow_freelancing;
		}
		
		/**
		 * Set whether a user can change details about an issue without working on the issue
		 * 
		 * @param boolean $val
		 */
		public function setChangeIssuesWithoutWorkingOnThem($val)
		{
			$this->_allow_freelancing = (bool) $val;
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
		
		public function getActiveBuilds()
		{
			$builds = $this->getBuilds();
			foreach ($builds as $id => $build)
			{
				if ($build->isLocked()) unset($builds[$id]);
			}
			
			return $builds;
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
				$this->_issuetypes = $this->getIssuetypeScheme()->getIssuetypes();
			}
		}

		/**
		 * Populates the internal array with unassigned issues
		 */
		protected function _populateUnassignedIssues()
		{
			if ($this->_unassignedissues === null)
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
		 * Populates the internal array with unassigned user stories for the scrum page
		 */
		protected function _populateUnassignedStories()
		{
			if ($this->_unassignedstories === null)
			{
				$this->_unassignedstories = array();
				$issuetypes = array();
				
				foreach (TBGIssuetype::getAll() as $issuetype)
				{
					if ($issuetype->getIcon() == 'developer_report')
					{
						$issuetypes[] = $issuetype->getID();
					}
				}
				
				if (count($issuetypes) > 0)
				{
					if ($res = TBGIssuesTable::getTable()->getByProjectIDandNoMilestoneandTypesAndState($this->getID(), $issuetypes, TBGIssue::STATE_OPEN))
					{
						while ($row = $res->getNextRow())
						{
							$this->_unassignedstories[$row->get(TBGIssuesTable::ID)] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID));
						}
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
		 * Returns an array with unassigned user stories
		 *
		 * @return array
		 */
		public function getUnassignedStories()
		{
			$this->_populateUnassignedStories();
			return $this->_unassignedstories;
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
				if ($res = \b2db\Core::getTable('TBGVisibleMilestonesTable')->getAllByProjectID($this->getID()))
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
			\b2db\Core::getTable('TBGVisibleMilestonesTable')->clearByProjectID($this->getID());
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
				\b2db\Core::getTable('TBGVisibleMilestonesTable')->addByProjectIDAndMilestoneID($this->getID(), $milestone_id);
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
			if (empty($this->_issuecounts['last15']))
			{
				list ($closed, $open) = TBGLogTable::getTable()->getLast15IssueCountsByProjectID($this->getID());
				$this->_issuecounts['last15']['open'] = $open;
				$this->_issuecounts['last15']['closed'] = $closed;
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
				if ($res = TBGVisibleIssueTypesTable::getTable()->getAllByProjectID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$i_id = $row->get(TBGVisibleIssueTypesTable::ISSUETYPE_ID);
							$this->_visible_issuetypes[$i_id] = TBGContext::factory()->TBGIssuetype($i_id);
						}
						catch (Exception $e)
						{
							TBGVisibleIssueTypesTable::getTable()->deleteByIssuetypeID($i_id);
						}
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
			\b2db\Core::getTable('TBGVisibleIssueTypesTable')->clearByProjectID($this->getID());
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
				\b2db\Core::getTable('TBGVisibleIssueTypesTable')->addByProjectIDAndIssuetypeID($this->getID(), $issuetype_id);
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

		public function getLast15Counts()
		{
			$this->_populateIssueCounts();
			return $this->_issuecounts['last15'];
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
			$res = TBGIssuesTable::getTable()->getOpenIssuesByProjectIDAndIssueTypes($this->getID(), array_keys($this->getVisibleIssuetypes()), TBGIssuesTable::ISSUE_TYPE);

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
			$issue_type = (is_object($issue_type)) ? $issue_type->getID() : $issue_type;
			if (!isset($this->_fieldsarrays[$issue_type][(int) $reportable]))
			{
				$retval = array();
				$res = \b2db\Core::getTable('TBGIssueFieldsTable')->getBySchemeIDandIssuetypeID($this->getIssuetypeScheme()->getID(), $issue_type);
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
								$retval[$key]['values'][''] = TBGContext::getI18n()->__('None');
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
								if (array_key_exists($key, $retval) && array_key_exists('values', $retval[$key]))
								{
									asort($retval[$key]['values'], SORT_STRING);
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
								if (array_key_exists($key, $retval) && array_key_exists('values', $retval[$key]))
								{
									asort($retval[$key]['values'], SORT_STRING);
								}
							}
							elseif ($key == 'component' || array_key_exists('custom', $return_details) && $return_details['custom'] && in_array($return_details['custom_type'], array(TBGCustomDatatype::COMPONENTS_LIST, TBGCustomDatatype::COMPONENTS_CHOICE)))
							{
								$retval[$key]['values'] = array();
								$retval[$key]['values'][''] = TBGContext::getI18n()->__('None');
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
								if (array_key_exists($key, $retval) && array_key_exists('values', $retval[$key]))
								{
									asort($retval[$key]['values'], SORT_STRING);
								}
							}
							elseif ($key == 'build' || array_key_exists('custom', $return_details) && $return_details['custom'] && in_array($return_details['custom_type'], array(TBGCustomDatatype::RELEASES_LIST, TBGCustomDatatype::RELEASES_CHOICE)))
							{
								$retval[$key]['values'] = array();
								$retval[$key]['values'][''] = TBGContext::getI18n()->__('None');
								foreach ($this->getBuilds() as $build)
								{
									if ($build->isLocked()) continue;
									$retval[$key]['values'][$build->getID()] = $build->getName().' ('.$build->getVersion().')';
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
			return TBGContext::getUser()->hasPermission('canseeproject', $this->getID());
		}
		
		protected function _populateLogItems($limit = null, $important = true, $offset = null)
		{
			$varname = ($important) ? '_recentimportantlogitems' : '_recentlogitems';
			if ($this->$varname === null)
			{
				$this->$varname = array();
				if ($important)
				{
					$res = TBGLogTable::getTable()->getImportantByProjectID($this->getID(), $limit, $offset);
				}
				else
				{
					$res = TBGLogTable::getTable()->getByProjectID($this->getID(), $limit, $offset);
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
		public function getRecentLogItems($limit = null, $important = true, $offset = null)
		{
			$this->_populateLogItems($limit, $important, $offset);
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

		protected function _populateStatusCount()
		{
			if ($this->_statuscount === null)
			{
				$this->_statuscount = array();
				$this->_statuscount[0] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				foreach (TBGStatus::getAll() as $status_id => $status)
				{
					$this->_statuscount[$status_id] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				}
				foreach (TBGIssuesTable::getTable()->getStatusCountByProjectID($this->getID()) as $status_id => $status_count)
				{
					$this->_statuscount[$status_id] = $status_count;
				}
			}
		}

		public function getStatusCount()
		{
			$this->_populateStatusCount();
			return $this->_statuscount;
		}

		protected function _populateResolutionCount()
		{
			if ($this->_resolutioncount === null)
			{
				$this->_resolutioncount = array();
				$this->_resolutioncount[0] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				foreach (TBGResolution::getAll() as $resolution_id => $resolution)
				{
					$this->_resolutioncount[$resolution_id] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				}
				foreach (TBGIssuesTable::getTable()->getResolutionCountByProjectID($this->getID()) as $resolution_id => $resolution_count)
				{
					$this->_resolutioncount[$resolution_id] = $resolution_count;
				}
			}
		}

		public function getResolutionCount()
		{
			$this->_populateResolutionCount();
			return $this->_resolutioncount;
		}

		protected function _populateCategoryCount()
		{
			if ($this->_categorycount === null)
			{
				$this->_categorycount = array();
				$this->_categorycount[0] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				foreach (TBGCategory::getAll() as $category_id => $category)
				{
					$this->_categorycount[$category_id] = array('open' => 0, 'closed' => 0, 'percentage' => 0);
				}
				foreach (TBGIssuesTable::getTable()->getCategoryCountByProjectID($this->getID()) as $category_id => $category_count)
				{
					$this->_categorycount[$category_id] = $category_count;
				}
			}
		}

		public function getCategoryCount()
		{
			$this->_populateCategoryCount();
			return $this->_categorycount;
		}

		protected function _populateStateCount()
		{
			if ($this->_statecount === null)
			{
				$this->_statecount = array();
				foreach (TBGIssuesTable::getTable()->getStateCountByProjectID($this->getID()) as $state_id => $state_count)
				{
					$this->_statecount[$state_id] = $state_count;
				}
			}
		}

		public function getStateCount()
		{
			$this->_populateStateCount();
			return $this->_statecount;
		}

		protected function _populateRecentIssues($issuetype)
		{
			$issuetype_id = (is_object($issuetype)) ? $issuetype->getID() : $issuetype;

			if (!array_key_exists($issuetype_id, $this->_recentissues))
			{
				$this->_recentissues[$issuetype_id] = array();
				if ($res = TBGIssuesTable::getTable()->getRecentByProjectIDandIssueType($this->getID(), $issuetype_id))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
							if ($issue->hasAccess()) $this->_recentissues[$issuetype_id][$issue->getID()] = $issue;
						}
						catch (Exception $e) {}
					}
				}
			}
		}

		/**
		 * Return this projects 10 most recent issues
		 *
		 * @return array A list of TBGIssues
		 */
		public function getRecentIssues($issuetype)
		{
			$issuetype_id = (is_object($issuetype)) ? $issuetype->getID() : $issuetype;
			$this->_populateRecentIssues($issuetype_id);
			return $this->_recentissues[$issuetype_id];
		}

		protected function _populateRecentActivities($limit = null, $important = true, $offset = null)
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
					if ($milestone->isStarting() && $milestone->isSprint())
					{
						if ($milestone->getStartingDate() > time()) continue;
						if (!array_key_exists($milestone->getStartingDate(), $this->_recentactivities))
						{
							$this->_recentactivities[$milestone->getStartingDate()] = array();
						}
						$this->_recentactivities[$milestone->getStartingDate()][] = array('change_type' => 'sprint_start', 'info' => $milestone->getName());
					}
					if ($milestone->isScheduled() && $milestone->isReached())
					{
						if ($milestone->getReachedDate() > time()) continue;
						if (!array_key_exists($milestone->getReachedDate(), $this->_recentactivities))
						{
							$this->_recentactivities[$milestone->getReachedDate()] = array();
						}
						$this->_recentactivities[$milestone->getReachedDate()][] = array('change_type' => (($milestone->isSprint()) ? 'sprint_end' : 'milestone_release'), 'info' => $milestone->getName());
					}
				}
				
				foreach ($this->getRecentLogItems($limit, $important, $offset) as $log_item)
				{
					if (!array_key_exists($log_item['timestamp'], $this->_recentactivities))
					{
						$this->_recentactivities[$log_item['timestamp']] = array();
					}
					$this->_recentactivities[$log_item['timestamp']][] = $log_item;
				}
				
				krsort($this->_recentactivities, SORT_NUMERIC);
			}
		}

		/**
		 * Return a list of recent activity for the project
		 *
		 * @param integer $limit Limit number of activities
		 * @return array
		 */
		public function getRecentActivities($limit = null, $important = false, $offset = null)
		{
			$this->_populateRecentActivities($limit, $important, $offset);
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
		 * Return the projects' associated workflow scheme
		 * 
		 * @return TBGWorkflowScheme 
		 */
		public function getWorkflowScheme()
		{
			return $this->_getPopulatedObjectFromProperty('_workflow_scheme_id');
		}
		
		public function setWorkflowScheme(TBGWorkflowScheme $scheme)
		{
			$this->_workflow_scheme_id = $scheme;
		}
		
		public function hasWorkflowScheme()
		{
			return (bool) ($this->getWorkflowScheme() instanceof TBGWorkflowScheme);
		}
		
		/**
		 * Return the projects' associated issuetype scheme
		 * 
		 * @return TBGIssuetypeScheme
		 */
		public function getIssuetypeScheme()
		{
			return $this->_getPopulatedObjectFromProperty('_issuetype_scheme_id');
		}
		
		public function setIssuetypeScheme(TBGIssuetypeScheme $scheme)
		{
			$this->_issuetype_scheme_id = $scheme;
		}
		
		/**
		 * Return the client assigned to the project, or null if there is none
		 * 
		 * @return TBGClient
		 */
		public function getClient()
		{
			return $this->_getPopulatedObjectFromProperty('_client');
		}
		
		/**
		 * Return whether or not this project has a client associated
		 * 
		 * @return boolean
		 */
		public function hasClient()
		{
			return (bool) ($this->getClient() instanceof TBGClient);
		}
		
		/**
		 * Set the client
		 */
		public function setClient($client)
		{
			$this->_client = $client;
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

		public function canSeeAllEditions()
		{
			return (bool) ($this->permissionCheck('canseeprojecthierarchy') || $this->permissionCheck('canseeallprojecteditions'));
		}
		
		public function canSeeAllComponents()
		{
			return (bool) ($this->permissionCheck('canseeprojecthierarchy') || $this->permissionCheck('canseeallprojectcomponents'));
		}
		
		public function canSeeAllBuilds()
		{
			return (bool) ($this->permissionCheck('canseeprojecthierarchy') || $this->permissionCheck('canseeallprojectbuilds'));
		}
		
		public function canSeeAllMilestones()
		{
			return (bool) ($this->permissionCheck('canseeprojecthierarchy') || $this->permissionCheck('canseeallprojectmilestones'));
		}
		
		public function canAutoassign()
		{
			return (bool) ($this->_autoassign);
		}
		
		public function hasParent()
		{
			return ($this->getParent() instanceof TBGProject);
		}
		
		public function hasChildren()
		{
			return (bool) count($this->getChildren());
		}
		
		public function getParent()
		{
//			if ($this->getKey() == 'sampleproject2'): return TBGProject::getByKey('sampleproject1'); endif;
			return $this->_getPopulatedObjectFromProperty('_parent');
		}
		
		public function setParent(TBGProject $project)
		{
			$this->_parent = $project;
		}
		
		/**
		 * Get all children
		 * @param bool $archived[optional] Show archived projects
		 */
		public function getChildren($archived = false)
		{
			$this->_populateChildren();
			$f_projects = array();
			
			foreach ($this->_children as $project)
			{
				if ($archived)
				{
					if ($project->isArchived()): $f_projects[] = $project; endif;
				}
				else
				{
					if (!$project->isArchived()): $f_projects[] = $project; endif;
				}
			}
			
			return $f_projects;
		}
		
		protected function _populateChildren()
		{
			if ($this->_children === null)
			{
				$this->_children = array();
				$res = TBGProjectsTable::getTable()->getByParentID($this->getID());

				if ($res == false): return; endif;

				foreach ($res->getAllRows() as $row)
				{
					$this->_children[] = TBGContext::factory()->TBGProject($row->get(TBGProjectsTable::ID), $row);
				}
			}
		}
		
		/**
		 * Whether or not this project has downloads enabled
		 * 
		 * @return boolean
		 */
		public function hasDownloads() 
		{
			return (bool) $this->_has_downloads;
		}
		
		/**
		 * Set whether this project has downloads enabled
		 * 
		 * @param boolean $value
		 */
		public function setDownloadsEnabled($value = true)
		{
			$this->_has_downloads = $value;
		}
		
		public function setSmallIcon(TBGFile $icon)
		{
			$this->_small_icon = $icon;
		}
		
		public function clearSmallIcon()
		{
			$this->_small_icon = null;
		}
		
		/**
		 * Return the small icon file object
		 * 
		 * @return TBGFile
		 */
		public function getSmallIcon()
		{
			return $this->_getPopulatedObjectFromProperty('_small_icon');
		}
		
		public function getSmallIconName()
		{
			return ($this->hasSmallIcon()) ? TBGContext::getRouting()->generate('showfile', array('id' => $this->getSmallIcon()->getID())) : 'icon_project.png';
		}

		public function hasSmallIcon()
		{
			return ($this->getSmallIcon() instanceof TBGFile);
		}
		
		public function setLargeIcon(TBGFile $icon)
		{
			$this->_large_icon = $icon;
		}
		
		public function clearLargeIcon()
		{
			$this->_large_icon = null;
		}
		
		public function getLargeIcon()
		{
			return $this->_getPopulatedObjectFromProperty('_large_icon');
		}
		
		public function getLargeIconName()
		{
			return ($this->hasLargeIcon()) ? TBGContext::getRouting()->generate('showfile', array('id' => $this->getLargeIcon()->getID())) : 'icon_project_large.png';
		}

		public function hasLargeIcon()
		{
			return ($this->getLargeIcon() instanceof TBGFile);
		}
		
		/**
		 * Move issues from one step to another for a given issue type and conversions
		 * @param TBGIssuetype $issuetype
		 * @param array $conversions
		 * 
		 * $conversions should be an array containing arrays:
		 * array (
		 * 		array(oldstep, newstep)
		 * 		...
		 * )
		 */
		public function convertIssueStepPerIssuetype(TBGIssuetype $issuetype, array $conversions)
		{
			TBGIssuesTable::getTable()->convertIssueStepByIssuetype($this, $issuetype, $conversions);
		}
	}
