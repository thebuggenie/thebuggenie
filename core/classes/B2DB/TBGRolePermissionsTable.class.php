<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Roles <- permissions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Roles <- permissions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="rolepermissions")
	 */
	class TBGRolePermissionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'rolepermissions';
		const ID = 'rolepermissions.id';
		const SCOPE = 'rolepermissions.scope';
		const ROLE_ID = 'rolepermissions.role_id';
		const PERMISSION = 'rolepermissions.permission';

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ROLE_ID, TBGListTypesTable::getTable());
			parent::_addVarchar(self::PERMISSION, 100);
		}

		protected function _setupIndexes()
		{
			$this->_addIndex('role_id', self::ROLE_ID);
		}

		public function clearPermissionsForRole($role_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ROLE_ID, $role_id);
			$this->doDelete($crit);
		}

		public function addPermissionForRole($role_id, $permission)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ROLE_ID, $role_id);
			$crit->addInsert(self::PERMISSION, $permission);
			$this->doInsert($crit);
		}

	}
