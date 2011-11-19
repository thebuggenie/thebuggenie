<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Workflow schemes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow schemes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="workflow_schemes")
	 * @Entity(class="TBGWorkflowScheme")
	 */
	class TBGWorkflowSchemesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'workflow_schemes';
		const ID = 'workflow_schemes.id';
		const SCOPE = 'workflow_schemes.scope';
		const NAME = 'workflow_schemes.name';
		const DESCRIPTION = 'workflow_schemes.description';

//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//			parent::_addVarchar(self::NAME, 200);
//			parent::_addText(self::DESCRIPTION, false);
//		}

		public function getAll($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			$crit->addOrderBy(self::ID, Criteria::SORT_ASC);

			$res = $this->select($crit);

			return $res;
		}

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit, false);
			return $row;
		}

	}