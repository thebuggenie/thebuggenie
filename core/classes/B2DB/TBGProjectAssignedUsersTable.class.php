<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Project assigned users table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Project assigned users table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="projectassignedusers")
	 */
	class TBGProjectAssignedUsersTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'projectassignedusers';
		const ID = 'projectassignedusers.id';
		const SCOPE = 'projectassignedusers.scope';
		const USER_ID = 'projectassignedusers.uid';
		const PROJECT_ID = 'projectassignedusers.project_id';
		const ROLE_ID = 'projectassignedusers.role_id';
		
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable());
			parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable());
			parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable());
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable());
		}
		
		public function deleteByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$res = $this->doDelete($crit);
			return $res;
		}

		public function addUserToProject($project_id, $user, $role)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::USER_ID, $user->getID());
			if (!$this->doCount($crit))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::USER_ID, $user->getID());
				$crit->addInsert(self::PROJECT_ID, $project_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
				return true;
			}
			return false;
		}

		public function removeUserFromProject($project_id, $user)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addWhere(self::USER_ID, $user);
			$this->doDelete($crit);
		}

	}
