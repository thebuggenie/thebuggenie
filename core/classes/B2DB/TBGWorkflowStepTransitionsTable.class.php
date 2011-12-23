<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Workflow step transitions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow step transitions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="workflow_step_transitions")
	 */
	class TBGWorkflowStepTransitionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'workflow_step_transitions';
		const ID = 'workflow_step_transitions.id';
		const SCOPE = 'workflow_step_transitions.scope';
		const FROM_STEP_ID = 'workflow_step_transitions.from_step_id';
		const TRANSITION_ID = 'workflow_step_transitions.transition_id';
		const WORKFLOW_ID = 'workflow_step_transitions.workflow_id';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
			parent::_addForeignKeyColumn(self::FROM_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
			parent::_addForeignKeyColumn(self::TRANSITION_ID, TBGWorkflowTransitionsTable::getTable(), TBGWorkflowTransitionsTable::ID);
		}

		protected function _deleteByTypeID($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere((($type == 'step') ? self::FROM_STEP_ID : self::TRANSITION_ID), $id);
			return $this->doDelete($crit);
		}

		protected function _countByTypeID($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere((($type == 'step') ? self::FROM_STEP_ID : self::TRANSITION_ID), $id);
			return $this->doCount($crit);
		}

		protected function _getByTypeID($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere((($type == 'step') ? self::FROM_STEP_ID : self::TRANSITION_ID), $id);
		//	$crit->addJoin(self::TRANSITION_ID, TBGWorkflowTransitionsTable::getTable());

			$return_array = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					if ($type == 'step')
					{
						$return_array[$row->get(self::TRANSITION_ID)] = TBGContext::factory()->TBGWorkflowTransition($row->get(self::TRANSITION_ID), $row);
					}
					else
					{
						$return_array[$row->get(self::FROM_STEP_ID)] = TBGContext::factory()->TBGWorkflowStep($row->get(self::FROM_STEP_ID), $row);
					}
				}
			}

			return $return_array;
		}

		public function countByStepID($step_id)
		{
			return $this->_countByTypeID('step', $step_id);
		}

		public function getByStepID($step_id)
		{
			return $this->_getByTypeID('step', $step_id);
		}

		public function countByTransitionID($transition_id)
		{
			return $this->_countByTypeID('transition', $transition_id);
		}

		public function getByTransitionID($transition_id)
		{
			return $this->_getByTypeID('transition', $transition_id);
		}

		public function addNew($from_step_id, $transition_id, $workflow_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::FROM_STEP_ID, $from_step_id);
			$crit->addInsert(self::TRANSITION_ID, $transition_id);
			$crit->addInsert(self::WORKFLOW_ID, $workflow_id);
			$this->doInsert($crit);
		}
		
		public function copyByWorkflowIDs($old_workflow_id, $new_workflow_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::WORKFLOW_ID, $old_workflow_id);
			
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$crit2 = $this->getCriteria();
					$crit2->addInsert(self::FROM_STEP_ID, $row->get(self::FROM_STEP_ID));
					$crit2->addInsert(self::SCOPE, $row->get(self::SCOPE));
					$crit2->addInsert(self::TRANSITION_ID, $row->get(self::TRANSITION_ID));
					$crit2->addInsert(self::WORKFLOW_ID, $new_workflow_id);
					$this->doInsert($crit2);
				}
			}
		}

		public function deleteByTransitionID($transition_id)
		{
			$this->_deleteByTypeID('transition', $transition_id);
		}

		public function deleteByStepID($step_id)
		{
			$this->_deleteByTypeID('step', $step_id);
		}

		public function reMapStepIDsByWorkflowID($workflow_id, $mapper_array)
		{
			foreach ($mapper_array as $old_step_id => $new_step_id)
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::FROM_STEP_ID, $new_step_id);
				$crit->addWhere(self::FROM_STEP_ID, $old_step_id);
				$crit->addWhere(self::WORKFLOW_ID, $workflow_id);
				$this->doUpdate($crit);
			}
		}
		
		public function reMapTransitionIDsByWorkflowID($workflow_id, $mapper_array)
		{
			foreach ($mapper_array as $old_transition_id => $new_transition_id)
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::TRANSITION_ID, $new_transition_id);
				$crit->addWhere(self::TRANSITION_ID, $old_transition_id);
				$crit->addWhere(self::WORKFLOW_ID, $workflow_id);
				$this->doUpdate($crit);
			}
		}
		
	}