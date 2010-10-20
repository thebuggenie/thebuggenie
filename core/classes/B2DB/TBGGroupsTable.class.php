<?php

	/**
	 * Groups table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Groups table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGGroupsTable extends B2DBTable 
	{

		const B2DBNAME = 'groups';
		const ID = 'groups.id';
		const GNAME = 'groups.gname';
		const SCOPE = 'groups.scope';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGGroupsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGGroupsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::GNAME, 50);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function loadFixtures($scope)
		{
			$i18n = TBGContext::getI18n();

			$crit = $this->getCriteria();
			$crit->addInsert(self::GNAME, $i18n->__('Administrators'));
			$crit->addInsert(self::SCOPE, $scope);
			$admin_group_id = $this->doInsert($crit)->getInsertID();
			TBGSettings::saveSetting('admingroup', $admin_group_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::GNAME, $i18n->__('Regular users'));
			$crit->addInsert(self::SCOPE, $scope);
			$users_group_id = $this->doInsert($crit)->getInsertID();

			$crit = $this->getCriteria();
			$crit->addInsert(self::GNAME, $i18n->__('Guests'));
			$crit->addInsert(self::SCOPE, $scope);
			$guest_group_id = $this->doInsert($crit)->getInsertID();

			return array($admin_group_id, $users_group_id, $guest_group_id);
		}
		
		public function getAll($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function doesGroupNameExist($group_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::GNAME, $group_name);
			
			return (bool) $this->doCount($crit);
		}
		
	}
