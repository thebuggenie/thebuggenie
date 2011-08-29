<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Workflow transition validation rules table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow transition validation rules table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowTransitionValidationRulesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'workflow_transition_validation_rules';
		const ID = 'workflow_transition_validation_rules.id';
		const SCOPE = 'workflow_transition_validation_rules.scope';
		const RULE = 'workflow_transition_validation_rules.rule';
		const TRANSITION_ID = 'workflow_transition_validation_rules.transition_id';
		const WORKFLOW_ID = 'workflow_transition_validation_rules.workflow_id';
		const RULE_VALUE = 'workflow_transition_validation_rules.rule_value';
		const PRE_OR_POST = 'workflow_transition_validation_rules.pre_or_post';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowTransitionValidationRulesTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGWorkflowTransitionValidationRulesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addVarchar(self::PRE_OR_POST, 4);
			parent::_addVarchar(self::RULE, 100);
			parent::_addVarchar(self::RULE_VALUE, 200);
			parent::_addForeignKeyColumn(self::TRANSITION_ID, TBGWorkflowTransitionsTable::getTable(), TBGWorkflowTransitionsTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
		}
		
		public function getByTransitionID($transition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::TRANSITION_ID, $transition_id);
			
			$actions = array('pre' => array(), 'post' => array());
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$actions[$row->get(self::PRE_OR_POST)][$row->get(self::RULE)] = TBGContext::factory()->TBGWorkflowTransitionValidationRule($row->get(self::ID), $row);
				}
			}
			
			return $actions;
		}

	}