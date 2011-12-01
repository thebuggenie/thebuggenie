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
	 * @Entity(class="TBGRolePermission")
	 */
	class TBGRolePermissionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'rolepermissions';
		const ID = 'rolepermissions.id';
		const SCOPE = 'rolepermissions.scope';
		const ROLE_ID = 'rolepermissions.role_id';
		const PERMISSION = 'rolepermissions.permission';
		const MODULE = 'rolepermissions.module';
		const TARGET_ID = 'rolepermissions.target_id';

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

		public function addPermissionForRole($role_id, $permission, $module, $target_id = null)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ROLE_ID, $role_id);
			$crit->addInsert(self::PERMISSION, $permission);
			$crit->addInsert(self::MODULE, $module);
			$crit->addInsert(self::TARGET_ID, $target_id);

			$this->doInsert($crit);
		}

	}
