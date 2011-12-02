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
	 *
	 * @Table(name="TBGEditionsTable")
	 */
	class TBGEdition extends TBGQaLeadableItem
	{
		
		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * The project
		 *
		 * @var TBGProject
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGProject")
		 */
		protected $_project;
		
		/**
		 * Editions components
		 *
		 * @var array|TBGComponent
		 * @Relates(class="TBGComponent", collection=true, manytomany=true, joinclass="TBGEditionComponentsTable")
		 */
		protected $_components;
		
		/**
		 * Edition builds
		 *
		 * @var array|TBGBuild
		 * @Relates(class="TBGBuild", collection=true, foreign_column="edition_id")
		 */
		protected $_builds;

		/**
		 * @Column(type="string", length=200)
		 */
		protected $_description;

		/**
		 * @Relates(class="TBGUser", collection=true, manytomany=true, joinclass="TBGEditionAssignedUsersTable")
		 */
		protected $_assigned_users;
		
		/**
		 * @Relates(class="TBGTeam", collection=true, manytomany=true, joinclass="TBGEditionAssignedTeamsTable")
		 */
		protected $_assigned_teams;

		/**
		 * The editions documentation URL
		 * 
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_doc_url;
						
		/**
		 * Whether the item is locked or not
		 *
		 * @var boolean
		 * @access protected
		 * @Column(type="boolean")
		 */
		protected $_locked;

		protected function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseeedition", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGEdition::createNew', $this)->trigger();
			}
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
				$this->_b2dbLazyload('_components');
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
				$this->_b2dbLazyload('_builds');
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
			return $this->_b2dbLazyload('_project');
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
			if ($assignee instanceof TBGUser)
				$retval = TBGEditionAssignedUsersTable::getTable()->addUserToEdition($this->getID(), $assignee, $role);
			elseif ($assignee instanceof TBGTeam)
				$retval = TBGEditionAssignedTeamsTable::getTable()->addTeamToEdition($this->getID(), $assignee, $role);

			return $retval;
		}

		/**
		 * Add an assignee to the edition
		 *
		 * @param TBGIdentifiableClass $assignee
		 * @param integer $role
		 *
		 * @return boolean
		 */
		public function removeAssignee(TBGIdentifiableClass $assignee)
		{
			if ($assignee instanceof TBGUser)
				$retval = TBGEditionAssignedUsersTable::getTable()->removeUserFromEdition($this->getID(), $assignee, $role);
			elseif ($assignee instanceof TBGTeam)
				$retval = TBGEditionAssignedTeamsTable::getTable()->removeTeamFromEdition($this->getID(), $assignee, $role);

			return $retval;
		}

		protected function _populateAssignees()
		{
			if ($this->_assigned_users === null)
				$this->_b2dbLazyload('_assigned_users');

			if ($this->_assigned_teams === null)
				$this->_b2dbLazyload('_assigned_teams');
		}
		
		public function getAssignedUsers()
		{
			$this->_populateAssignees();
			return $this->_assigned_users;
		}
		
		public function getAssignedTeams()
		{
			$this->_populateAssignees();
			return $this->_assigned_teams;
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

		protected function _preDelete()
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
		
		/**
		 * Returns whether or not this item is locked
		 *
		 * @return boolean
		 * @access public
		 */
		public function isLocked()
		{
			return $this->_locked;
		}

		/**
		 * Specify whether or not this item is locked
		 *
		 * @param boolean $locked[optional]
		 */
		public function setLocked($locked = true)
		{
			$this->_locked = (bool) $locked;
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
