<?php

	/**
	 * Team class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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

		static protected $_teams = null;
		
		static protected $_num_teams = null;

		protected $_members = null;

		protected $_num_members = null;
		
		protected $_ondemand = false;
		
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
				if ($res = \b2db\Core::getTable('TBGTeamsTable')->getAll())
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

		public static function getTeamsCount()
		{
			if (self::$_num_teams === null)
			{
				if (self::$_teams !== null)
					self::$_num_teams = count(self::$_teams);
				else
					self::$_num_teams = TBGTeamsTable::getTable()->countTeams();
			}

			return self::$_num_teams;
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
		 * Adds a user to the team
		 *
		 * @param TBGUser $user
		 */
		public function addMember(TBGUser $user)
		{
			TBGTeamMembersTable::getTable()->addUserToTeam($user->getID(), $this->getID());
			
			if (is_array($this->_members))
				$this->_members[$user->getID()] = $user->getID();
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

		public function removeMember(TBGUser $user)
		{
			if ($this->_members !== null)
			{
				unset($this->_members[$user->getID()]);
			}
			if ($this->_num_members !== null)
			{
				$this->_num_members--;
			}
		}
		
		public function _preDelete()
		{
			$crit = TBGTeamMembersTable::getTable()->getCriteria();
			$crit->addWhere(TBGTeamMembersTable::TID, $this->getID());
			$res = TBGTeamMembersTable::getTable()->doDelete($crit);
		}
		
		public static function findTeams($details)
		{
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGTeamsTable::NAME, "%$details%", \b2db\Criteria::DB_LIKE);
			$teams = array();
			if ($res = \b2db\Core::getTable('TBGTeamsTable')->doSelect($crit))
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
				
				$projects = \b2db\Core::getTable('TBGProjectAssigneesTable')->getProjectsByTeamID($this->getID());
				$edition_projects = \b2db\Core::getTable('TBGEditionAssigneesTable')->getProjectsByTeamID($this->getID());
				$component_projects = \b2db\Core::getTable('TBGComponentAssigneesTable')->getProjectsByTeamID($this->getID());

				$project_ids = array_merge(array_keys($projects), array_keys($edition_projects), array_keys($component_projects));
				foreach ($project_ids as $project_id)
				{
					$this->_associated_projects[$project_id] = TBGContext::factory()->TBGProject($project_id);
				}
			}
			
			return $this->_associated_projects;
		}
		
		public function isOndemand()
		{
			return $this->_ondemand;
		}
		
		public function setOndemand($val = true)
		{
			$this->_ondemand = $val;
		}

		public function hasAccess()
		{
			return (bool) (TBGContext::getUser()->hasPageAccess('teamlist') || TBGContext::getUser()->isMemberOfTeam($this));
		}

	}
