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
	 *
	 * @Table(name="TBGTeamsTable")
	 */
	class TBGTeam extends TBGIdentifiableScopedClass
	{
		
		protected static $_teams = null;
		
		protected static $_num_teams = null;

		protected $_members = null;

		protected $_num_members = null;

		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * @Column(type="boolean")
		 */
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
				self::$_teams = TBGTeamsTable::getTable()->getAll();
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

		public static function countAll()
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
		
		protected function _preDelete()
		{
			$crit = TBGTeamMembersTable::getTable()->getCriteria();
			$crit->addWhere(TBGTeamMembersTable::TID, $this->getID());
			$res = TBGTeamMembersTable::getTable()->doDelete($crit);
		}
		
		public static function findTeams($details)
		{
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGTeamsTable::NAME, "%$details%", \b2db\Criteria::DB_LIKE);
			$crit->addWhere(TBGTeamsTable::ONDEMAND, false);

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
				
				$project_ids = TBGProjectAssignedTeamsTable::getTable()->getProjectsByTeamID($this->getID());
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

		/**
		 * Return the items name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

	}
