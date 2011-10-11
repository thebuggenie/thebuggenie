<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Modules table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Modules table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGModulesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'modules';
		const ID = 'modules.id';
		const MODULE_NAME = 'modules.name';
		const MODULE_LONGNAME = 'modules.module_longname';
		const ENABLED = 'modules.enabled';
		const VERSION = 'modules.version';
		const CLASSNAME = 'modules.classname';
		const SCOPE = 'modules.scope';

		/**
		 * Return an instance of TBGModulesTable
		 *
		 * @return TBGModulesTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGModulesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::MODULE_NAME, 50);
			parent::_addBoolean(self::ENABLED);
			parent::_addVarchar(self::VERSION, 10);
			parent::_addVarchar(self::CLASSNAME, 50);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit, 'none');
			return $res;
		}

		public function disableModuleByID($module_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::ENABLED, 0);
			return $this->doUpdateById($crit, $module_id);
		}

		public function removeModuleByID($module_id)
		{
			return $this->doDeleteById($module_id);
		}

		public function disableModuleByName($module_name)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::ENABLED, 0);
			$crit->addWhere(self::MODULE_NAME, $module_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			return $this->doUpdate($crit);
		}

		public function installModule($identifier, $classname, $version, $scope)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CLASSNAME, $classname);
  			$crit->addWhere(self::MODULE_NAME, $identifier);
  			$crit->addWhere(self::SCOPE, $scope);
  			if (!$res = $this->doSelectOne($crit))
  			{
				$crit = $this->getCriteria();
	  			$crit->addInsert(self::CLASSNAME, $classname);
	  			$crit->addInsert(self::ENABLED, true);
	  			$crit->addInsert(self::MODULE_NAME, $identifier);
	  			$crit->addInsert(self::VERSION, $version);
	  			$crit->addInsert(self::SCOPE, $scope);
	  			$module_id = $this->doInsert($crit)->getInsertID();
  			}
  			else
  			{
	  			$module_id = $res->get(self::ID);
  			}
			return $module_id;
		}

		public function getModulesForScope($scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope_id);

			$return_array = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$return_array[$row->get(self::MODULE_NAME)] = (bool) $row->get(self::ENABLED);
				}
			}

			return $return_array;
		}

		public function getModuleForScope($module_name, $scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::MODULE_NAME, $module_name);
			$crit->addWhere(self::SCOPE, $scope_id);

			$module = null;
			if ($row = $this->doSelectOne($crit))
			{
				$classname = $row->get(self::CLASSNAME);
				$module = new $classname($row->get(self::ID), $row);
			}

			return $module;
		}

	}
