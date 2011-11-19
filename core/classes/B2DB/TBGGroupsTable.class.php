<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Groups table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Groups table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="groups")
	 * @Entity(class="TBGGroup")
	 */
	class TBGGroupsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'groups';
		const ID = 'groups.id';
		const NAME = 'groups.name';
		const SCOPE = 'groups.scope';

//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//
//			parent::_addVarchar(self::NAME, 50);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}

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
			$crit->addWhere(self::NAME, $group_name);
			
			return (bool) $this->doCount($crit);
		}
		
	}
