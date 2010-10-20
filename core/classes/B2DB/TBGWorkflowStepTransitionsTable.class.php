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
		const WORKFLOW_ID = 'workflow_step_transitions.workflow_id';

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
			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
			parent::_addForeignKeyColumn(self::FROM_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
			parent::_addForeignKeyColumn(self::TRANSITION_ID, TBGWorkflowTransitionsTable::getTable(), TBGWorkflowTransitionsTable::ID);
		}

		public function loadFixtures($scope)
		{
			$transitions = array();
			$transitions[] = array('from_step_id' => 1, 'transition_id' => 1);
			$transitions[] = array('from_step_id' => 1, 'transition_id' => 2);
			$transitions[] = array('from_step_id' => 1, 'transition_id' => 3);
			$transitions[] = array('from_step_id' => 1, 'transition_id' => 4);
			$transitions[] = array('from_step_id' => 2, 'transition_id' => 2);
			$transitions[] = array('from_step_id' => 2, 'transition_id' => 3);
			$transitions[] = array('from_step_id' => 2, 'transition_id' => 4);
			$transitions[] = array('from_step_id' => 3, 'transition_id' => 4);
			$transitions[] = array('from_step_id' => 3, 'transition_id' => 6);
			$transitions[] = array('from_step_id' => 4, 'transition_id' => 3);
			$transitions[] = array('from_step_id' => 4, 'transition_id' => 7);
			$transitions[] = array('from_step_id' => 4, 'transition_id' => 8);
			$transitions[] = array('from_step_id' => 5, 'transition_id' => 9);
			$transitions[] = array('from_step_id' => 5, 'transition_id' => 8);
			$transitions[] = array('from_step_id' => 6, 'transition_id' => 10);
			$transitions[] = array('from_step_id' => 6, 'transition_id' => 11);
			$transitions[] = array('from_step_id' => 7, 'transition_id' => 5);
			$transitions[] = array('from_step_id' => 8, 'transition_id' => 5);

			foreach ($transitions as $transition)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::WORKFLOW_ID, 1);
				$crit->addInsert(self::SCOPE, $scope);
				$crit->addInsert(self::FROM_STEP_ID, $transition['from_step_id']);
				$crit->addInsert(self::TRANSITION_ID, $transition['transition_id']);
				$this->doInsert($crit);
			}
		}

		public function countByStepID($step_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::FROM_STEP_ID, $step_id);
			return $this->doCount($crit);
		}

		public function getByStepID($step_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::FROM_STEP_ID, $step_id);

			$return_array = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$return_array[$row->get(self::ID)] = TBGContext::factory()->TBGWorkflowTransition($row->get(self::ID), $row);
				}
			}

			return $return_array;
		}

	}