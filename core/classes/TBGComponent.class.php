<?php

	/**
	 * Class used for components
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Class used for components
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGComponent extends TBGOwnableItem 
	{
		
		protected static $_b2dbtablename = 'TBGComponentsTable';
		
		/**
		 * This components project
		 *
		 * @var unknown_type
		 * @Class TBGProject
		 */
		protected $_project = null;
		
		public static function getAllByProjectID($project_id)
		{
			$retval = array();
			if ($res = B2DB::getTable('TBGComponentsTable')->getByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$component = TBGContext::factory()->TBGComponent($row->get(TBGComponentsTable::ID), $row);
					if ($component->hasAccess())
					{
						$retval[$component->getID()] = $component;
					}
				}
			}
			return $retval;
		}
		
		public function _postSave($is_new)
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
			return $this->_getPopulatedObjectFromProperty('_project');
		}
		
		public function setProject($project)
		{
			$this->_project = $project;
		}
		
		public function addAssignee($assignee, $role)
		{
			$retval = TBGComponentAssigneesTable::getTable()->addAssigneeToComponent($this->getID(), $assignee, $role);
			$this->applyInitialPermissionSet($assignee, $role);
			
			return $retval;
		}
		
		public function setName($name)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGComponentsTable::NAME, $name);
			$res = B2DB::getTable('TBGComponentsTable')->doUpdateById($crit, $this->getID());
			
			$this->_name = $name;
		}

		public function _preDelete()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGIssueAffectsComponentTable::COMPONENT, $this->getID());
			B2DB::getTable('TBGIssueAffectsComponentTable')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGEditionComponentsTable::COMPONENT, $this->getID());
			B2DB::getTable('TBGEditionComponentsTable')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGComponentAssigneesTable::COMPONENT_ID, $this->getID());
			$crit->addWhere(TBGComponentAssigneesTable::SCOPE, TBGContext::getScope()->getID());
			B2DB::getTable('TBGComponentAssigneesTable')->doDelete($crit);
		}
		
		public function getAssignees()
		{
			$uids = array();
	
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGComponentAssigneesTable::COMPONENT_ID, $this->getID());
			
			$res = B2DB::getTable('TBGComponentAssigneesTable')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$uids[] = $row->get(TBGComponentAssigneesTable::UID);
			}
			return $uids;
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
		
	}
