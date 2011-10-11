<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Teams table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Teams table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGTeamsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'teams';
		const ID = 'teams.id';
		const SCOPE = 'teams.scope';
		const NAME = 'teams.name';
		const ONDEMAND = 'teams.ondemand';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addBoolean(self::ONDEMAND);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getAll($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			$crit->addWhere(self::ONDEMAND, false);
			
			$res = $this->doSelect($crit, 'none');
			
			return $res;
		}

		public function doesTeamNameExist($team_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $team_name);

			return (bool) $this->doCount($crit);
		}

		public function countTeams($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			$crit->addWhere(self::ONDEMAND, false);

			return $this->doCount($crit);
		}

	}
