<?php

	/**
	 * Workflow steps table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow steps table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowStepsTable extends B2DBTable
	{

		const B2DBNAME = 'workflow_steps';
		const ID = 'workflow_steps.id';
		const SCOPE = 'workflow_steps.scope';
		const NAME = 'workflow_steps.name';
		const STATUS_ID = 'workflow_steps.status_id';
		const IS_CLOSED = 'workflow_steps.is_closed';
		const DESCRIPTION = 'workflow_steps.description';
		const EDITABLE = 'workflow_steps.editable';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowStepsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowStepsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS_ID, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::EDITABLE);
			parent::_addBoolean(self::IS_CLOSED);
		}

	}