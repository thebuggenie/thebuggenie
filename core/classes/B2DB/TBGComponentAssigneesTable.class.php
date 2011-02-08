<?php

	/**
	 * Component assignees table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGComponentAssigneesTable extends TBGB2DBTable 
	{

		const B2DBNAME = 'componentassignees';
		const ID = 'componentassignees.id';
		const SCOPE = 'componentassignees.scope';
		const UID = 'componentassignees.uid';
		const TID = 'componentassignees.tid';
		const COMPONENT_ID = 'componentassignees.component_id';
		const TARGET_TYPE = 'componentassignees.target_type';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_TYPE, 5);
			parent::_addForeignKeyColumn(self::COMPONENT_ID, B2DB::getTable('TBGComponentsTable'), TBGComponentsTable::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
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
					$projects[$row->get(TBGComponentsTable::PROJECT)] = TBGContext::factory()->TBGProject($row->get(TBGComponentsTable::PROJECT)); 
				}
			}
			return $projects;
		}
		
		public function getProjectsByTeamID($team_id)
		{
			$projects = array();
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $team_id);
			if ($res = $this->doSelect($crit))
			{
				foreach ($res->getNextRow() as $row)
				{
					$projects[$row->get(TBGComponentsTable::PROJECT)] = TBGContext::factory()->TBGProject($row->get(TBGComponentsTable::PROJECT)); 
				}
			}
			return $projects;
		}
		
		public function addAssigneeToComponent($component_id, $assignee, $role)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::COMPONENT_ID, $component_id);
			$crit->addWhere(self::TARGET_TYPE, $role);
			switch (true)
			{
				case ($assignee instanceof TBGUser):
					$crit->addWhere(self::UID, $assignee->getID());
					break;
				case ($assignee instanceof TBGTeam):
					$crit->addWhere(self::TID, $assignee->getID());
					break;
			}
			$res = $this->doSelectOne($crit);
			
			if (!$res instanceof B2DBRow)
			{
				$crit = $this->getCriteria();
				switch (true)
				{
					case ($assignee instanceof TBGUser):
						$crit->addInsert(self::UID, $assignee->getID());
						break;
					case ($assignee instanceof TBGTeam):
						$crit->addInsert(self::TID, $assignee->getID());
						break;
				}
				$crit->addInsert(self::COMPONENT_ID, $component_id);
				$crit->addInsert(self::TARGET_TYPE, $role);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				try
				{
					$res = $this->doInsert($crit);
				}
				catch (Exception $e)
				{
					throw $e;
				}
				return true;
			}
			return false;
		}
		
	}
