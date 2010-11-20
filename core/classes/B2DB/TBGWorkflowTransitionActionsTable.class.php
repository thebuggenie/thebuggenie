<?php

	/**
	 * Workflow transition actions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow transition actions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowTransitionActionsTable extends TBGB2DBTable
	{

		const B2DBNAME = 'workflow_transition_actions';
		const ID = 'workflow_transition_actions.id';
		const SCOPE = 'workflow_transition_actions.scope';
		const ACTION_TYPE = 'workflow_transition_actions.action_type';
		const TARGET_VALUE = 'workflow_transition_actions.target_value';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowTransitionActionsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowTransitionActionsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addInteger(self::ACTION_TYPE, 10);
			parent::_addInteger(self::TARGET_VALUE, 10);
		}

	}