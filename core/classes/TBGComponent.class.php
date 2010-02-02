<?php

	/**
	 * Class used for components
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class TBGComponent extends TBGVersionItem 
	{
		/**
		 * This components project
		 *
		 * @var unknown_type
		 */
		protected $_project;
		
		public static function createNew($name, $project_id)
		{
			$c_id = B2DB::getTable('TBGComponentsTable')->createNew($name, $project_id);
			return TBGFactory::componentLab($c_id);
		}
		
		public static function getAllByProjectID($project_id)
		{
			$retval = array();
			if ($res = B2DB::getTable('TBGComponentsTable')->getByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$component = TBGFactory::componentLab($row->get(TBGComponentsTable::ID), $row);
					$retval[$component->getID()] = $component;
				}
			}
			return $retval;
		}
		
		/**
		 * Constructor function
		 *
		 * @param integer $b_id
		 * @param TBGProject $project
		 */
		public function __construct($c_id, $row = null)
		{
			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGComponentsTable::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('TBGComponentsTable')->doSelectById($c_id, $crit);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_name = $row->get(TBGComponentsTable::NAME);
				$this->_itemid = $c_id;
				$this->_isdefault = false;
				$this->_locked = false;
				$this->_version_major = $row->get(TBGComponentsTable::VERSION_MAJOR);
				$this->_version_minor = $row->get(TBGComponentsTable::VERSION_MINOR);
				$this->_version_revision = $row->get(TBGComponentsTable::VERSION_REVISION);
				$this->_project = $row->get(TBGComponentsTable::PROJECT);
			}
		}
		
		/**
		 * Returns the project 
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			if (!$this->_project instanceof TBGProject)
			{
				$this->_project = TBGFactory::projectLab($this->_project);
			}
			return $this->_project;
		}
		
		public function addAssignee($assignee, $role)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGComponentAssigneesTable::COMPONENT_ID, $this->getID());
			$crit->addWhere(TBGComponentAssigneesTable::TARGET_TYPE, $role);
			switch (true)
			{
				case ($assignee instanceof TBGUser):
					$crit->addWhere(TBGComponentAssigneesTable::UID, $assignee->getID());
					break;
				case ($assignee instanceof TBGTeam):
					$crit->addWhere(TBGComponentAssigneesTable::TID, $assignee->getID());
					break;
				case ($assignee instanceof TBGCustomer):
					$crit->addWhere(TBGComponentAssigneesTable::CID, $assignee->getID());
					break;
			}
			$res = B2DB::getTable('TBGComponentAssigneesTable')->doSelectOne($crit);
			
			if (!$res instanceof B2DBRow)
			{
				$crit = new B2DBCriteria();
				switch (true)
				{
					case ($assignee instanceof TBGUser):
						$crit->addInsert(TBGComponentAssigneesTable::UID, $assignee->getID());
						break;
					case ($assignee instanceof TBGTeam):
						$crit->addInsert(TBGComponentAssigneesTable::TID, $assignee->getID());
						break;
					case ($assignee instanceof TBGCustomer):
						$crit->addInsert(TBGComponentAssigneesTable::CID, $assignee->getID());
						break;
				}
				$crit->addInsert(TBGComponentAssigneesTable::COMPONENT_ID, $this->getID());
				$crit->addInsert(TBGComponentAssigneesTable::TARGET_TYPE, $role);
				$crit->addInsert(TBGComponentAssigneesTable::SCOPE, TBGContext::getScope()->getID());
				try
				{
					$res = B2DB::getTable('TBGComponentAssigneesTable')->doInsert($crit);
				}
				catch (Exception $e)
				{
					throw $e;
				}
				return true;
			}
			return false;
		}
		
		public function setName($name)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGComponentsTable::NAME, $name);
			$res = B2DB::getTable('TBGComponentsTable')->doUpdateById($crit, $this->getID());
			
			$this->_name = $name;
		}

		public function delete()
		{
			B2DB::getTable('TBGComponentsTable')->doDeleteById($this->getID());
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
		
	}
