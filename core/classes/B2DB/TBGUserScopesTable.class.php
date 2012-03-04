<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * User scopes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * User scopes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="userscopes")
	 */
	class TBGUserScopesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'userscopes';
		const ID = 'userscopes.id';
		const SCOPE = 'userscopes.scope';
		const USER_ID = 'userscopes.user_id';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function _setupIndexes()
		{
			$this->_addIndex('uid_scope', array(self::USER_ID, self::SCOPE));
		}

		public function addUserToScope($user_id, $scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::USER_ID, $user_id);
			$crit->addInsert(self::SCOPE, $scope_id);
			$this->doInsert($crit);
		}

		public function clearUserScopes($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGSettings::getDefaultScopeID(), Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::USER_ID, $user_id);
			$this->doDelete($crit);
		}

	}
