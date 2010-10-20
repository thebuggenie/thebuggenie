<?php

	/**
	 * Workflow step transitions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow step transitions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowStepTransitionsTable extends B2DBTable
	{

		const B2DBNAME = 'workflow_step_transitions';
		const ID = 'workflow_step_transitions.id';
		const SCOPE = 'workflow_step_transitions.scope';
		const FROM_STEP_ID = 'workflow_step_transitions.from_step_id';
		const TRANSITION_ID = 'workflow_step_transitions.transition_id';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowStepTransitionsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowStepTransitionsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::FROM_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
			parent::_addForeignKeyColumn(self::TRANSITION_ID, TBGWorkflowTransitionsTable::getTable(), TBGWorkflowTransitionsTable::ID);
		}

	}