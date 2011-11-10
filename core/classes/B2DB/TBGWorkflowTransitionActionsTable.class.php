<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Workflow transition actions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow transition actions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="workflow_transition_actions")
	 * @Entity(class="TBGWorkflowTransitionAction")
	 */
	class TBGWorkflowTransitionActionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'workflow_transition_actions';
		const ID = 'workflow_transition_actions.id';
		const SCOPE = 'workflow_transition_actions.scope';
		const ACTION_TYPE = 'workflow_transition_actions.action_type';
		const TRANSITION_ID = 'workflow_transition_actions.transition_id';
		const WORKFLOW_ID = 'workflow_transition_actions.workflow_id';
		const TARGET_VALUE = 'workflow_transition_actions.target_value';

//		public function _initialize()
//		{
//			parent::_setup(self::B2DBNAME, self::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//			parent::_addVarchar(self::ACTION_TYPE, 100);
//			parent::_addVarchar(self::TARGET_VALUE, 200);
//			parent::_addForeignKeyColumn(self::TRANSITION_ID, TBGWorkflowTransitionsTable::getTable(), TBGWorkflowTransitionsTable::ID);
//			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
//		}
		
		public function getByTransitionID($transition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::TRANSITION_ID, $transition_id);
			return $this->doSelect($crit);
		}

	}