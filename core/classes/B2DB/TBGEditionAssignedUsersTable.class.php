<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Edition assigned users table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Edition assigned users table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="editionassignedusers")
	 */
	class TBGEditionAssignedUsersTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'editionassignedusers';
		const ID = 'editionassignedusers.id';
		const SCOPE = 'editionassignedusers.scope';
		const USER_ID = 'editionassignedusers.uid';
		const ROLE_ID = 'editionassignedusers.role_id';
		const EDITION_ID = 'editionassignedusers.edition_id';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::EDITION_ID, TBGEditionsTable::getTable());
			parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable());
			parent::_addForeignKeyColumn(self::ROLE_ID, TBGListTypesTable::getTable());
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable());
		}
		
		public function deleteByEditionID($edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_id);
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

		public function addUserToEdition($edition_id, $user, $role)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_id);
			$crit->addWhere(self::USER_ID, $user->getID());
			if (!$this->doCount($crit))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::USER_ID, $user->getID());
				$crit->addInsert(self::EDITION_ID, $edition_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
				return true;
			}
			return false;
		}

		public function removeUserFromEdition($user, $edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_id);
			$crit->addWhere(self::USER_ID, $user);
			$this->doDelete($crit);
		}

	}
