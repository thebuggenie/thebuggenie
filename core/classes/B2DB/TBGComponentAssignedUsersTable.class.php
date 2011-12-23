<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Component assigned users table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Component assigned users table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="componentassignedusers")
	 */
	class TBGComponentAssignedUsersTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'componentassignedusers';
		const ID = 'componentassignedusers.id';
		const SCOPE = 'componentassignedusers.scope';
		const USER_ID = 'componentassignedusers.uid';
		const ROLE_ID = 'componentassignedusers.role_id';
		const COMPONENT_ID = 'componentassignedusers.component_id';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::COMPONENT_ID, TBGComponentsTable::getTable());
			parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable());
			parent::_addForeignKeyColumn(self::ROLE_ID, TBGListTypesTable::getTable());
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable());
		}
		
		public function deleteByComponentID($component_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::COMPONENT_ID, $component_id);
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

		public function addUserToComponent($component_id, $user, $role)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::COMPONENT_ID, $component_id);
			$crit->addWhere(self::USER_ID, $user->getID());
			if (!$this->doCount($crit))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::USER_ID, $user->getID());
				$crit->addInsert(self::COMPONENT_ID, $component_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
				return true;
			}
			return false;
		}

		public function removeUserFromComponent($user, $component_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::COMPONENT_ID, $component_id);
			$crit->addWhere(self::USER_ID, $user);
			$this->doDelete($crit);
		}

	}
