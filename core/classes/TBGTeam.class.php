<?php

	/**
	 * Team class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Team class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGTeam extends TBGIdentifiableClass 
	{
		
		static protected $_b2dbtablename = 'TBGTeamsTable';
		
		protected $_members = null;

		protected $_num_members = null;
		
		protected static $_teams = null;
		
		protected $_associated_projects = null;
		
		public static function doesTeamNameExist($team_name)
		{
			return TBGTeamsTable::getTable()->doesTeamNameExist($team_name);
		}

		public static function getAll()
		{
			if (self::$_teams === null)
			{
				self::$_teams = array();
				if ($res = B2DB::getTable('TBGTeamsTable')->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_teams[$row->get(TBGTeamsTable::ID)] = TBGContext::factory()->TBGTeam($row->get(TBGTeamsTable::ID), $row);
					}
				}
			}
			return self::$_teams;
		}
		
		public static function loadFixtures(TBGScope $scope)
		{
			$staff_members = new TBGTeam();
			$staff_members->setName('Staff members');
			$staff_members->save();
			
			$developers = new TBGTeam();
			$developers->setName('Developers');
			$developers->save();
			
			$team_leaders = new TBGTeam();
			$team_leaders->setName('Team leaders');
			$team_leaders->save();
			
			$testers = new TBGTeam();
			$testers->setName('Testers');
			$testers->save();
			
			$translators = new TBGTeam();
			$translators->setName('Translators');
			$translators->save();
		}
		
		public function __toString()
		{
			return "" . $this->_name;
		}
		
		public function getType()
		{
			return self::TYPE_TEAM;
		}
		
		/**
		 * Creates a team
		 *
		 * @param unknown_type $groupname
		 * @return TBGTeam
		 */
		public static function createNew($teamname)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGTeamsTable::NAME, $teamname);
			$crit->addInsert(TBGTeamsTable::SCOPE, TBGContext::getScope()->getID());
			$res = B2DB::getTable('TBGTeamsTable')->doInsert($crit);
			return TBGContext::factory()->TBGTeam($res->getInsertID());
		}
		
		/**
		 * Adds a user to the team
		 *
		 * @param TBGUser $user
		 */
		public function addMember(TBGUser $user)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGTeamMembersTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(TBGTeamMembersTable::TID, $this->_id);
			$crit->addInsert(TBGTeamMembersTable::UID, $user->getID());
			B2DB::getTable('TBGTeamMembersTable')->doInsert($crit);
			if ($this->_members === null)
			{
				$this->_members = array();
			}
			$this->_members[] = $user->getID();
			array_unique($this->_members);
		}
		
		public function getMembers()
		{
			if ($this->_members === null)
			{
				$this->_members = array();
				foreach (TBGTeamMembersTable::getTable()->getUIDsForTeamID($this->getID()) as $uid)
				{
					$this->_members[$uid] = TBGContext::factory()->TBGUser($uid);
				}
			}
			return $this->_members;
		}

		/**
		 * Removes a user from the team
		 *
		 * @param integer $uid
		 */
		public function removeMember($uid)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGTeamMembersTable::UID, $uid);
			$crit->addWhere(TBGTeamMembersTable::TID, $this->_id);
			B2DB::getTable('TBGTeamMembersTable')->doDelete($crit);
		}
		
		public function _preDelete()
		{
			$crit = TBGTeamMembersTable::getTable()->getCriteria();
			$crit->addWhere(TBGTeamMembersTable::TID, $this->getID());
			$res = TBGTeamMembersTable::getTable()->doDelete($crit);
		}
		
		public static function findTeams($details)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGTeamsTable::NAME, "%$details%", B2DBCriteria::DB_LIKE);
			$teams = array();
			if ($res = B2DB::getTable('TBGTeamsTable')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$teams[$row->get(TBGTeamsTable::ID)] = TBGContext::factory()->TBGTeam($row->get(TBGTeamsTable::ID), $row);
				}
			}
			return $teams;
		}

		public function getNumberOfMembers()
		{
			if ($this->_members !== null)
			{
				return count($this->_members);
			}
			elseif ($this->_num_members === null)
			{
				$this->_num_members = TBGTeamMembersTable::getTable()->getNumberOfMembersByTeamID($this->getID());
			}

			return $this->_num_members;
		}
		
		/**
		 * Get all the projects a team is associated with
		 * 
		 * @return array
		 */
		public function getAssociatedProjects()
		{
			if ($this->_associated_projects === null)
			{
				$this->_associated_projects = array();
				
				$projects = B2DB::getTable('TBGProjectAssigneesTable')->getProjectsByTeamID($this->getID());
				$edition_projects = B2DB::getTable('TBGEditionAssigneesTable')->getProjectsByTeamID($this->getID());
				$component_projects = B2DB::getTable('TBGComponentAssigneesTable')->getProjectsByTeamID($this->getID());

				$project_ids = array_merge(array_keys($projects), array_keys($edition_projects), array_keys($component_projects));
				foreach ($project_ids as $project_id)
				{
					$this->_associated_projects[$project_id] = TBGContext::factory()->TBGProject($project_id);
				}
			}
			
			return $this->_associated_projects;
		}
	}
