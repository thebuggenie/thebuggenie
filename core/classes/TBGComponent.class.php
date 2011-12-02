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
		
		protected $_assignees = null;
		
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
		
		public function addAssignee($assignee, $role)
		{
			$retval = TBGComponentAssigneesTable::getTable()->addAssigneeToComponent($this->getID(), $assignee, $role);
			
			return $retval;
		}
		
		protected function _preDelete()
		{
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGIssueAffectsComponentTable::COMPONENT, $this->getID());
			\b2db\Core::getTable('TBGIssueAffectsComponentTable')->doDelete($crit);
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGEditionComponentsTable::COMPONENT, $this->getID());
			\b2db\Core::getTable('TBGEditionComponentsTable')->doDelete($crit);
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGComponentAssigneesTable::COMPONENT_ID, $this->getID());
			$crit->addWhere(TBGComponentAssigneesTable::SCOPE, TBGContext::getScope()->getID());
			\b2db\Core::getTable('TBGComponentAssigneesTable')->doDelete($crit);
		}
		
		protected function _populateAssignees()
		{
			if ($this->_assignees === null)
			{
				$this->_assignees = TBGComponentAssigneesTable::getTable()->getByComponentID($this->getID());
			}
		}
		
		/**
		 * Get assignees for this component
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
