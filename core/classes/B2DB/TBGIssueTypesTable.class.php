<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issuetypes")
	 * @Entity(class="TBGIssuetype")
	 */
	class TBGIssueTypesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'issuetypes';
		const ID = 'issuetypes.id';
		const SCOPE = 'issuetypes.scope';
		const NAME = 'issuetypes.name';
		const DESCRIPTION = 'issuetypes.description';
		const ICON = 'issuetypes.icon';
		const TASK = 'issuetypes.task';

		public function _migrateData(\b2db\Table $old_table)
		{
			$sqls = array();
			$tn = $this->_getTableNameSQL();
			$qc = $this->getQC();
			switch ($old_table->getVersion())
			{
				case 1:
					$sqls[] = "UPDATE {$tn} SET icon = itemdata";
					$sqls[] = "UPDATE {$tn} SET {$qc}key{$qc} = NULL";
					break;
			}
			foreach ($sqls as $sql)
			{
				$statement = \b2db\Statement::getPreparedStatement($sql);
				$res = $statement->performQuery('update');
			}

			switch ($old_table->getVersion())
			{
				case 1:
					foreach (self::getTable()->getAll() as $issuetype)
					{
						// Trigger issuetype key regeneration
						$issuetype->setKey(null);
						$issuetype->getKey();
						$issuetype->save();
					}
					break;
			}
		}

		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			return $this->select($crit);
		}

		public function getAllIDsByScopeID($scope_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope_id);
			$crit->addSelectionColumn(self::ID, 'id');
			$res = $this->doSelect($crit);

			$ids = array();
			if ($res) {
				while ($row = $res->getNextRow()) {
					$id = $row->get('id');
					$ids[$id] = $id;
				}
			}

			return $ids;
		}

		public function getBugReportTypeIDs()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ICON, 'bug_report');
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit);

			$retarr = array();
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$retarr[] = $row->get(self::ID);
				}
			}

			return $retarr;
		}

	}
