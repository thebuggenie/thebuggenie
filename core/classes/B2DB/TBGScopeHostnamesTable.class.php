<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Scopes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Scopes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGScopeHostnamesTable extends TBGB2DBTable
	{
		
		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'scopehostnames';
		const ID = 'scopehostnames.id';
		const SCOPE_ID = 'scopehostnames.scope_id';
		const HOSTNAME = 'scopehostnames.hostname';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::HOSTNAME, 200, '');
			parent::_addForeignKeyColumn(self::SCOPE_ID, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function addHostnameToScope($hostname, $scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::HOSTNAME, $hostname);
			$crit->addInsert(self::SCOPE_ID, $scope_id);
			$res = $this->doInsert($crit);
		}

		public function removeHostnameFromScope($hostname, $scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::HOSTNAME, $hostname);
			$crit->addWhere(self::SCOPE_ID, $scope_id);
			$res = $this->doDelete($crit);
		}

		public function saveScopeHostnames($hostnames, $scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE_ID, $scope_id);
			$res = $this->doDelete($crit);
			foreach ($hostnames as $hostname)
			{
				$this->addHostnameToScope($hostname, $scope_id);
			}
		}

		public function getHostnamesForScope($scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE_ID, $scope_id);

			$hostnames = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$hostnames[$row->get(self::ID)] = $row->get(self::HOSTNAME);
				}
			}

			return $hostnames;
		}

	}
