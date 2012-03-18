<?php

	/**
	 * Class used for components
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Class used for components
	 *
	 * @package thebuggenie
	 * @subpackage main
	 *
	 * @Table(name="TBGComponentsTable")
	 */
	class TBGComponent extends TBGQaLeadableItem
	{
		
		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * This components project
		 *
		 * @var unknown_type
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGProject")
		 */
		protected $_project = null;
		
		/**
		 * @Relates(class="TBGUser", collection=true, manytomany=true, joinclass="TBGComponentAssignedUsersTable")
		 */
		protected $_assigned_users;

		/**
		 * @Relates(class="TBGTeam", collection=true, manytomany=true, joinclass="TBGComponentAssignedTeamsTable")
		 */
		protected $_assigned_teams;
		
		protected function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseecomponent", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGComponent::createNew', $this)->trigger();
			}
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
		
		protected function _preDelete()
		{
			TBGIssueAffectsComponentTable::getTable()->deleteByComponentID($this->getID());
			TBGEditionComponentsTable::getTable()->deleteByComponentID($this->getID());
			TBGComponentAssignedUsersTable::getTable()->deleteByComponentID($this->getID());
			TBGComponentAssignedTeamsTable::getTable()->deleteByComponentID($this->getID());
		}
		
		/**
		 * Add an assignee to the component
		 *
		 * @param TBGIdentifiableClass $assignee
		 * @param integer $role
		 *
		 * @return boolean
		 */
		public function addAssignee(TBGIdentifiableClass $assignee, $role)
		{
			if ($assignee instanceof TBGUser)
				$retval = TBGComponentAssignedUsersTable::getTable()->addUserToComponent($this->getID(), $assignee, $role);
			elseif ($assignee instanceof TBGTeam)
				$retval = TBGComponentAssignedTeamsTable::getTable()->addTeamToComponent($this->getID(), $assignee, $role);

			return $retval;
		}

		/**
		 * Add an assignee to the component
		 *
		 * @param TBGIdentifiableClass $assignee
		 * @param integer $role
		 *
		 * @return boolean
		 */
		public function removeAssignee(TBGIdentifiableClass $assignee)
		{
			if ($assignee instanceof TBGUser)
				$retval = TBGComponentAssignedUsersTable::getTable()->removeUserFromComponent($this->getID(), $assignee, $role);
			elseif ($assignee instanceof TBGTeam)
				$retval = TBGComponentAssignedTeamsTable::getTable()->removeTeamFromComponent($this->getID(), $assignee, $role);

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
		 * Whether or not the current user can access the component
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return ($this->getProject()->canSeeAllComponents() || TBGContext::getUser()->hasPermission('canseecomponent', $this->getID()));
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
