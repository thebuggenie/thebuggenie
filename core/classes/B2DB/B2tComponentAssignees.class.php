<?php

	/**
	 * Component assignees table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Component assignees table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tComponentAssignees extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_componentassignees';
		const ID = 'bugs2_componentassignees.id';
		const SCOPE = 'bugs2_componentassignees.scope';
		const UID = 'bugs2_componentassignees.uid';
		const CID = 'bugs2_componentassignees.cid';
		const TID = 'bugs2_componentassignees.tid';
		const COMPONENT_ID = 'bugs2_componentassignees.component_id';
		const TARGET_TYPE = 'bugs2_componentassignees.target_type';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_TYPE, 5);
			parent::_addForeignKeyColumn(self::COMPONENT_ID, B2DB::getTable('B2tComponents'), B2tComponents::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::CID, B2DB::getTable('B2tCustomers'), B2tCustomers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getByComponentIDs($component_ids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::COMPONENT_ID, $component_ids, B2DBCriteria::DB_IN);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getProjectsByUserID($user_id)
		{
			$projects = array();
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			if ($res = $this->doSelect($crit))
			{
				foreach ($res->getNextRow() as $row)
				{
					$projects[$row->get(B2tComponents::PROJECT)] = BUGSfactory::projectLab($row->get(B2tComponents::PROJECT)); 
				}
			}
			return $projects;
		}
		
	}
