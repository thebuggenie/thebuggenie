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
	class BUGScomponent extends BUGSversionitem 
	{
		/**
		 * This components project
		 *
		 * @var unknown_type
		 */
		protected $_project;
		
		public static function createNew($name, $project_id)
		{
			$c_id = B2DB::getTable('B2tComponents')->createNew($name, $project_id);
			return BUGSfactory::componentLab($c_id);
		}
		
		public static function getAllByProjectID($project_id)
		{
			$retval = array();
			if ($res = B2DB::getTable('B2tComponents')->getByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$component = BUGSfactory::componentLab($row->get(B2tComponents::ID), $row);
					$retval[$component->getID()] = $component;
				}
			}
			return $retval;
		}
		
		/**
		 * Constructor function
		 *
		 * @param integer $b_id
		 * @param BUGSproject $project
		 */
		public function __construct($c_id, $row = null)
		{
			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tComponents::SCOPE, BUGScontext::getScope()->getID());
				$row = B2DB::getTable('B2tComponents')->doSelectById($c_id, $crit);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_name = $row->get(B2tComponents::NAME);
				$this->_itemid = $c_id;
				$this->_isdefault = false;
				$this->_locked = false;
				$this->_version_major = $row->get(B2tComponents::VERSION_MAJOR);
				$this->_version_minor = $row->get(B2tComponents::VERSION_MINOR);
				$this->_version_revision = $row->get(B2tComponents::VERSION_REVISION);
				$this->_project = $row->get(B2tComponents::PROJECT);
			}
		}
		
		/**
		 * Returns the project 
		 *
		 * @return BUGSproject
		 */
		public function getProject()
		{
			if (!$this->_project instanceof BUGSproject)
			{
				$this->_project = BUGSfactory::projectLab($this->_project);
			}
			return $this->_project;
		}
		
		public function addAssignee($assignee, $role)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tComponentAssignees::COMPONENT_ID, $this->getID());
			$crit->addWhere(B2tComponentAssignees::TARGET_TYPE, $role);
			switch (true)
			{
				case ($assignee instanceof BUGSuser):
					$crit->addWhere(B2tComponentAssignees::UID, $assignee->getID());
					break;
				case ($assignee instanceof BUGSteam):
					$crit->addWhere(B2tComponentAssignees::TID, $assignee->getID());
					break;
				case ($assignee instanceof BUGScustomer):
					$crit->addWhere(B2tComponentAssignees::CID, $assignee->getID());
					break;
			}
			$res = B2DB::getTable('B2tComponentAssignees')->doSelectOne($crit);
			
			if (!$res instanceof B2DBRow)
			{
				$crit = new B2DBCriteria();
				switch (true)
				{
					case ($assignee instanceof BUGSuser):
						$crit->addInsert(B2tComponentAssignees::UID, $assignee->getID());
						break;
					case ($assignee instanceof BUGSteam):
						$crit->addInsert(B2tComponentAssignees::TID, $assignee->getID());
						break;
					case ($assignee instanceof BUGScustomer):
						$crit->addInsert(B2tComponentAssignees::CID, $assignee->getID());
						break;
				}
				$crit->addInsert(B2tComponentAssignees::COMPONENT_ID, $this->getID());
				$crit->addInsert(B2tComponentAssignees::TARGET_TYPE, $role);
				$crit->addInsert(B2tComponentAssignees::SCOPE, BUGScontext::getScope()->getID());
				try
				{
					$res = B2DB::getTable('B2tComponentAssignees')->doInsert($crit);
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
			$crit->addUpdate(B2tComponents::NAME, $name);
			$res = B2DB::getTable('B2tComponents')->doUpdateById($crit, $this->getID());
			
			$this->_name = $name;
		}

		public function delete()
		{
			B2DB::getTable('B2tComponents')->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tIssueAffectsComponent::COMPONENT, $this->getID());
			B2DB::getTable('B2tIssueAffectsComponent')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tEditionComponents::COMPONENT, $this->getID());
			B2DB::getTable('B2tEditionComponents')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tComponentAssignees::COMPONENT_ID, $this->getID());
			$crit->addWhere(B2tComponentAssignees::SCOPE, BUGScontext::getScope()->getID());
			B2DB::getTable('B2tComponentAssignees')->doDelete($crit);
		}
		
		public function getAssignees()
		{
			$uids = array();
	
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tComponentAssignees::COMPONENT_ID, $this->getID());
			
			$res = B2DB::getTable('B2tComponentAssignees')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$uids[] = $row->get(B2tComponentAssignees::UID);
			}
			return $uids;
		}
		
	}
