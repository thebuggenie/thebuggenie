<?php

	/**
	 * Edition assignees table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Edition assignees table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGEditionAssigneesTable extends TBGB2DBTable
	{

		const B2DBNAME = 'editionassignees';
		const ID = 'editionassignees.id';
		const SCOPE = 'editionassignees.scope';
		const UID = 'editionassignees.uid';
		const TID = 'editionassignees.tid';
		const EDITION_ID = 'editionassignees.edition_id';
		const TARGET_TYPE = 'editionassignees.target_type';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_TYPE, 5);
			parent::_addForeignKeyColumn(self::EDITION_ID, B2DB::getTable('TBGEditionsTable'), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function getByEditionIDs($edition_ids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_ids, B2DBCriteria::DB_IN);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function deleteByEditionID($edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_id);
			$res = $this->doDelete($crit);
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
					$projects[$row->get(TBGEditionsTable::PROJECT)] = TBGContext::factory()->TBGProject($row->get(TBGEditionsTable::PROJECT)); 
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
					$projects[$row->get(TBGEditionsTable::PROJECT)] = TBGContext::factory()->TBGProject($row->get(TBGEditionsTable::PROJECT)); 
				}
			}
			return $projects;
		}

		public function addAssigneeToEdition($edition_id, $assignee, $role)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_id);
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
				$crit->addInsert(self::EDITION_ID, $edition_id);
				$crit->addInsert(self::TARGET_TYPE, $role);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$res = $this->doInsert($crit);
				return true;
			}
			return false;
		}
		
	}
