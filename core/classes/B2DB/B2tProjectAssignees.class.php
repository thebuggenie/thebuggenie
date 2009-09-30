<?php

	/**
	 * Project assignees table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Project assignees table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tProjectAssignees extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_projectassignees';
		const ID = 'bugs2_projectassignees.id';
		const SCOPE = 'bugs2_projectassignees.scope';
		const UID = 'bugs2_projectassignees.uid';
		const CID = 'bugs2_projectassignees.cid';
		const TID = 'bugs2_projectassignees.tid';
		const PROJECT_ID = 'bugs2_projectassignees.project_id';
		const TARGET_TYPE = 'bugs2_projectassignees.target_type';

		const TYPE_DEVELOPER = 1;
		const TYPE_PROJECTMANAGER = 2;
		const TYPE_TESTER = 3;
		const TYPE_DOCUMENTOR = 4;
		const TYPE_CUSTOMER = 5;
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_TYPE, 5);
			parent::_addForeignKeyColumn(self::PROJECT_ID, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::CID, B2DB::getTable('B2tCustomers'), B2tCustomers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		static public function getTypes()
		{
			return array(self::TYPE_DEVELOPER => BUGScontext::getI18n()->__('Developer'), 
						self::TYPE_PROJECTMANAGER => BUGScontext::getI18n()->__('Project manager'),
						self::TYPE_DOCUMENTOR => BUGScontext::getI18n()->__('Documentation editor'),
						self::TYPE_TESTER => BUGScontext::getI18n()->__('Tester'),
						self::TYPE_CUSTOMER => BUGScontext::getI18n()->__('Customer'));
		}
		
		static public function getTypeName($type)
		{
			$types = self::getTypes();
			return $types[$type];
		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByProjectAndRoleAndUser($project_id, $role, $user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::TARGET_TYPE, $role);
			$crit->addWhere(self::UID, $user_id);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function addByProjectAndRoleAndUser($project_id, $role, $user_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::PROJECT_ID, $project_id);
			$crit->addInsert(self::TARGET_TYPE, $role);
			$crit->addInsert(self::UID, $user_id);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res;
		}
		
		public function getByProjectAndRoleAndTeam($project_id, $role, $team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::TARGET_TYPE, $role);
			$crit->addWhere(self::TID, $team_id);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function addByProjectAndRoleAndTeam($project_id, $role, $team_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::PROJECT_ID, $project_id);
			$crit->addInsert(self::TARGET_TYPE, $role);
			$crit->addInsert(self::TID, $team_id);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res;
		}
		
		public function getByProjectAndRoleAndCustomer($project_id, $role, $customer_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::TARGET_TYPE, $role);
			$crit->addWhere(self::CID, $customer_id);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function addByProjectAndRoleAndCustomer($project_id, $role, $customer_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::PROJECT_ID, $project_id);
			$crit->addInsert(self::TARGET_TYPE, $role);
			$crit->addInsert(self::CID, $customer_id);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res;
		}
		
		public function getProjectsByUserID($user_id)
		{
			$projects = array();
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$projects[$row->get(self::PROJECT_ID)] = BUGSfactory::projectLab($row->get(self::PROJECT_ID), $row); 
				}
			}
			return $projects;
		}
		
	}
