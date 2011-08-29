<?php

	/**
	 * Edition class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Edition class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGEdition extends TBGOwnableItem 
	{
		
		protected static $_b2dbtablename = 'TBGEditionsTable';
		
		/**
		 * The project
		 *
		 * @var TBGProject
		 * @Class TBGProject
		 */
		protected $_project = null;
		
		/**
		 * Editions components
		 *
		 * @var array|TBGComponent
		 */
		protected $_components = null;
		
		/**
		 * Edition builds
		 *
		 * @var array|TBGBuild
		 */
		protected $_builds = null;
		
		protected $_description = '';
		
		protected $_assignees = null;
		
		/**
		 * The editions documentation URL
		 * 
		 * @var string
		 */
		protected $_doc_url = '';
						
		static protected $_editions = null;
		
		public function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseeedition", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGEdition::createNew', $this)->trigger();
			}
		}

		/**
		 * Retrieve all editions for a specific project
		 *
		 * @param integer $project_id
		 * 
		 * @return array
		 */
		public static function getAllByProjectID($project_id)
		{
			if (self::$_editions === null)
			{
				self::$_editions = array();
			}
			if (!array_key_exists($project_id, self::$_editions))
			{
				self::$_editions[$project_id] = array();
				if ($res = \b2db\Core::getTable('TBGEditionsTable')->getByProjectID($project_id))
				{
					while ($row = $res->getNextRow())
					{
						$edition = TBGContext::factory()->TBGEdition($row->get(TBGEditionsTable::ID), $row);
						self::$_editions[$project_id][$edition->getID()] = $edition;
					}
				}
			}
			return self::$_editions[$project_id];
		}
		
		/**
		 * Constructor function
		 *
		 * @param \b2db\Row $row
		 */
		public function _construct(\b2db\Row $row, $foreign_key = null)
		{
			TBGEvent::createNew('core', 'TBGEdition::__construct', $this)->trigger();
		}
		
		/**
		 * Populates components inside the edition
		 *
		 * @return void
		 */
		protected function _populateComponents()
		{
			if ($this->_components === null)
			{
				$this->_components = array();
				if ($res = \b2db\Core::getTable('TBGEditionComponentsTable')->getByEditionID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_components[$row->get(TBGEditionComponentsTable::COMPONENT)] = TBGContext::factory()->TBGComponent($row->get(TBGEditionComponentsTable::COMPONENT));
					}
				}
			}
		}
		
		/**
		 * Returns an array with all components
		 *
		 * @return array|TBGComponent
		 */
		public function getComponents()
		{
			$this->_populateComponents();
			return $this->_components;
		}
		
		/**
		 * Whether or not this edition has a component enabled
		 * 
		 * @param TBGComponent|integer $component The component to check for
		 * 
		 * @return boolean
		 */
		public function hasComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			return array_key_exists($c_id, $this->getComponents());
		}

		/**
		 * Whether this edition has a description set
		 *
		 * @return string
		 */
		public function hasDescription()
		{
			return (bool) $this->getDescription();
		}

		/**
		 * Adds an existing component to the edition
		 *
		 * @param TBGComponent|integer $component
		 */
		public function addComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			return \b2db\Core::getTable('TBGEditionComponentsTable')->addEditionComponent($this->getID(), $c_id);
		}
		
		/**
		 * Removes an existing component from the edition
		 *
		 * @param TBGComponent|integer $c_id
		 */
		public function removeComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			\b2db\Core::getTable('TBGEditionComponentsTable')->removeEditionComponent($this->getID(), $c_id);
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
		 * Returns the documentation url
		 *
		 * @return string
		 */
		public function getDocumentationURL()
		{
			return $this->_doc_url;
		}
		
		/**
		 * Returns the component specified
		 *
		 * @param integer $c_id
		 * 
		 * @return TBGComponent
		 */
		public function getComponent($c_id)
		{
			$this->_populateComponents();
			if (array_key_exists($c_id, $this->_components))
			{
				return $this->_components[$c_id];
			}

			return null;
		}
		
		/**
		 * Populates builds inside the edition
		 *
		 * @return void
		 */
		protected function _populateBuilds()
		{
			if ($this->_builds === null)
			{
				$this->_builds = array();
				if ($res = \b2db\Core::getTable('TBGBuildsTable')->getByEditionID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_builds[$row->get(TBGBuildsTable::ID)] = TBGContext::factory()->TBGBuild($row->get(TBGBuildsTable::ID), $row);
					}
				}
			}
		}

		/**
		 * Returns an array with all builds
		 *
		 * @return array|TBGBuild
		 */
		public function getBuilds()
		{
			$this->_populateBuilds();
			return $this->_builds;
		}

		/**
		 * Returns the default build
		 *
		 * @return TBGBuild
		 */
		public function getDefaultBuild()
		{
			$this->_populateBuilds();
			if (count($this->_builds) > 0)
			{
				foreach ($this->_builds as $build)
				{
					if ($build->isDefault() && $build->isLocked() == false)
					{
						return $build;
					}
				}
				return array_slice($this->_builds, 0, 1);
			}
			return 0;
		}

		public function _sortBuildsByReleaseDate(TBGBuild $build1, TBGBuild $build2)
		{
			if ($build1->getReleaseDate() == $build2->getReleaseDate())
			{
				return 0;
			}
			return ($build1->getReleaseDate() < $build2->getReleaseDate()) ? -1 : 1;
		}

		/**
		 * Returns the latest build
		 *
		 * @return TBGBuild
		 */
		public function getLatestBuild()
		{
			$this->_populateBuilds();
			if (count($this->getBuilds()) > 0)
			{
				$builds = usort($this->getBuilds(), array($this, '_sortBuildsByReleaseDate'));
				return array_slice($builds, 0, 1);
			}
			return null;
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
		 * Add an assignee to the edition
		 *
		 * @param TBGIdentifiableClass $assignee
		 * @param integer $role
		 * 
		 * @return boolean
		 */
		public function addAssignee(TBGIdentifiableClass $assignee, $role)
		{
			$retval = TBGEditionAssigneesTable::getTable()->addAssigneeToEdition($this->getID(), $assignee, $role);
			$this->applyInitialPermissionSet($assignee, $role);
			
			return $retval;
		}

		protected function _populateAssignees()
		{
			if ($this->_assignees === null)
			{
				$this->_assignees = TBGEditionAssigneesTable::getTable()->getByEditionID($this->getID());
			}
		}
		
		/**
		 * Get assignees for this edition
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
		 * Set the edition description
		 *
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		/**
		 * Set the editions documentation url
		 *
		 * @param string $doc_url
		 */
		public function setDocumentationURL($doc_url)
		{
			$this->_doc_url = $doc_url;
		}

		public function _preDelete()
		{
			\b2db\Core::getTable('TBGEditionAssigneesTable')->deleteByEditionID($this->getID());
		}
		
		/**
		 * Whether or not the current user can access the edition
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return ($this->getProject()->canSeeAllEditions() || TBGContext::getUser()->hasPermission('canseeedition', $this->getID()));
		}
		
	}
