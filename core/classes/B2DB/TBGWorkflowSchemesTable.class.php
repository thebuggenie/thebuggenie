<?php

	/**
	 * Workflow schemes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow schemes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowSchemesTable extends TBGB2DBTable
	{

		const B2DBNAME = 'workflow_schemes';
		const ID = 'workflow_schemes.id';
		const SCOPE = 'workflow_schemes.scope';
		const NAME = 'workflow_schemes.name';
		const DESCRIPTION = 'workflow_schemes.description';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowSchemesTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowSchemesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
		}

		public function loadFixtures(TBGScope $scope)
		{
			$i18n = TBGContext::getI18n();

			$crit = $this->getCriteria();
			$crit->addInsert(self::ID, 1);
			$crit->addInsert(self::SCOPE, $scope->getID());
			$crit->addInsert(self::NAME, "Default workflow scheme");
			$crit->addInsert(self::DESCRIPTION, "This is the default workflow scheme. It is used by all projects with no specific workflow scheme selected. This scheme cannot be edited or removed.");
			$this->doInsert($crit);

		}

		public function getAll($scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);

			$res = $this->doSelect($crit);

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