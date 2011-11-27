<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Project assigned teams table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Project assigned teams table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="projectassignedteams")
	 */
	class TBGProjectAssignedTeamsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'projectassignedteams';
		const ID = 'projectassignedteams.id';
		const SCOPE = 'projectassignedteams.scope';
		const TEAM_ID = 'projectassignedteams.uid';
		const PROJECT_ID = 'projectassignedteams.project_id';
		const ROLE_ID = 'projectassignedteams.role_id';
		
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable());
			parent::_addForeignKeyColumn(self::ROLE_ID, TBGListTypesTable::getTable());
			parent::_addForeignKeyColumn(self::TEAM_ID, TBGTeamsTable::getTable());
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable());
		}
		
		public function deleteByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doDelete($crit);
			return $res;
		}

		public function deleteByRoleID($role_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ROLE_ID, $role_id);
			$res = $this->doDelete($crit);
			return $res;
		}

		public function addTeamToProject($project_id, $team_id, $role_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::TEAM_ID, $team_id);
			$crit->addWhere(self::ROLE_ID, $role_id);
			if (!$this->doCount($crit))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::PROJECT_ID, $project_id);
				$crit->addInsert(self::TEAM_ID, $team_id);
				$crit->addInsert(self::ROLE_ID, $role_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
				return true;
			}
			return false;
		}

		public function removeTeamFromProject($team, $project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::TEAM_ID, $team);
			$this->doDelete($crit);
		}
		
		public function getProjectsByTeamID($team)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TEAM_ID, $team);
			$res = $this->doSelect($crit);
			
			$projects = array();
			if ($res)
			{
				$pid = $row->get(self::PROJECT_ID);
				$projects[$pid] = $pid;
			}
			
			return $projects;
		}

		public function getRolesForProject($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doSelect($crit);

			$roles = array();
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$roles[$row->get(self::TEAM_ID)][] = new TBGRole($row->get(self::ROLE_ID));
				}
			}

			return $roles;
		}

	}
