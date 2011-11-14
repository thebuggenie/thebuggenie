<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Workflows table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflows table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="workflows")
	 * @Entity(class="TBGWorkflow")
	 */
	class TBGWorkflowsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'workflows';
		const ID = 'workflows.id';
		const SCOPE = 'workflows.scope';
		const NAME = 'workflows.name';
		const DESCRIPTION = 'workflows.description';
		const IS_ACTIVE = 'workflows.is_active';

//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//			parent::_addVarchar(self::NAME, 200);
//			parent::_addText(self::DESCRIPTION, false);
//			parent::_addBoolean(self::IS_ACTIVE);
//		}

		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ID, Criteria::SORT_ASC);

			return $this->select($crit);
		}

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit, false);
			return $row;
		}

		public function countWorkflows($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);

			return $this->doCount($crit);
		}

	}