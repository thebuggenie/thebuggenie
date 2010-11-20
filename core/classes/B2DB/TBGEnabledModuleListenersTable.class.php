<?php

	/**
	 * Module sections table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Module sections table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGEnabledModuleListenersTable extends TBGB2DBTable 
	{
		const B2DBNAME = 'enabledmodulelisteners';
		const ID = 'enabledmodulelisteners.id';
		const IDENTIFIER = 'enabledmodulelisteners.identifier';
		const MODULE = 'enabledmodulelisteners.module';
		const MODULE_NAME = 'enabledmodulelisteners.module_name';
		const ORDER = 'enabledmodulelisteners.order';
		const SCOPE = 'enabledmodulelisteners.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::MODULE, 50);
			parent::_addVarchar(self::IDENTIFIER, 50);
			parent::_addInteger(self::ORDER, 3);
			parent::_addForeignKeyColumn(self::MODULE_NAME, B2DB::getTable('TBGModulesTable'), TBGModulesTable::MODULE_NAME);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getAll($module_names)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(B2DB::getTable('TBGModulesTable'), TBGModulesTable::MODULE_NAME, self::MODULE_NAME);
			$crit->addWhere(self::MODULE_NAME, $module_names, B2DBCriteria::DB_IN);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(TBGModulesTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ORDER, 'asc');
			$res = $this->doSelect($crit);

			return $res;
		}

		public function savePermanentListener($module, $identifier, $module_name, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::IDENTIFIER, $identifier);
			$crit->addWhere(self::MODULE, $module);
			$crit->addWhere(self::MODULE_NAME, $module_name);
			$crit->addWhere(self::SCOPE, $scope);
			if (!($res = $this->doSelectOne($crit)))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::IDENTIFIER, $identifier);
				$crit->addInsert(self::MODULE, $module);
				$crit->addInsert(self::MODULE_NAME, $module_name);
				$crit->addInsert(self::SCOPE, $scope);
				$res = $this->doInsert($crit);
			}
		}

		public function removePermanentListener($module, $identifier, $module_name, $scope)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::IDENTIFIER, $identifier);
			$crit->addWhere(self::MODULE, $module);
			$crit->addWhere(self::MODULE_NAME, $module_name);
			$crit->addWhere(self::SCOPE, $scope);
			$res = $this->doDelete($crit);
		}

		public function removeAllModuleListeners($module_name, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MODULE_NAME, $module_name);
			$crit->addWhere(self::SCOPE, $scope);
			$res = $this->doDelete($crit);
		}

	}
