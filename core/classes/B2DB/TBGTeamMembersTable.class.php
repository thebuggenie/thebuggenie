<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Team members table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Team members table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGTeamMembersTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'teammembers';
		const ID = 'teammembers.id';
		const SCOPE = 'teammembers.scope';
		const UID = 'teammembers.uid';
		const TID = 'teammembers.tid';
		
		/**
		 * Return an instance of this table
		 *
		 * @return TBGTeamMembersTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGTeamMembersTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::TID, Core::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getUIDsForTeamID($team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $team_id);

			$uids = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$uids[$row->get(self::UID)] = $row->get(self::UID);
				}
			}

			return $uids;
		}
		
		public function clearTeamsByUserID($user_id)
		{
			$team_ids = array();
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addJoin(TBGTeamsTable::getTable(), TBGTeamsTable::ID, self::TID);
			$crit->addWhere(TBGTeamsTable::ONDEMAND, false);
			
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$team_ids[$row->get(self::TID)] = true;
				}
			}
			
			if (!empty($team_ids))
			{
				$crit = $this->getCriteria();
				$crit->addWhere(self::UID, $user_id);
				$crit->addWhere(self::TID, array_keys($team_ids), Criteria::DB_IN);
				$res = $this->doDelete($crit);
			}
		}

		public function getNumberOfMembersByTeamID($team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $team_id);
			$count = $this->doCount($crit);

			return $count;
		}

		public function cloneTeamMemberships($cloned_team_id, $new_team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $cloned_team_id);
			$memberships_to_add = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$memberships_to_add[] = $row->get(self::UID);
				}
			}

			foreach ($memberships_to_add as $uid)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::UID, $uid);
				$crit->addInsert(self::TID, $new_team_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
			}
		}
		
		public function getTeamIDsForUserID($user_id, $ondemand = false)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			return $this->doSelect($crit, 'all');
		}
		
		public function addUserToTeam($user_id, $team_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::TID, $team_id);
			$crit->addInsert(self::UID, $user_id);
			$this->doInsert($crit);
		}
		
	}
