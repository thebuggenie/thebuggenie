<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Workflow transitions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow transitions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="workflow_transitions")
	 * @Entity(class="TBGWorkflowTransition")
	 */
	class TBGWorkflowTransitionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'workflow_transitions';
		const ID = 'workflow_transitions.id';
		const SCOPE = 'workflow_transitions.scope';
		const WORKFLOW_ID = 'workflow_transitions.workflow_id';
		const NAME = 'workflow_transitions.name';
		const DESCRIPTION = 'workflow_transitions.description';
		const OUTGOING_STEP_ID = 'workflow_transitions.outgoing_step_id';
		const TEMPLATE = 'workflow_transitions.template';

//		public function _initialize()
//		{
//			parent::_setup(self::B2DBNAME, self::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
//			parent::_addVarchar(self::NAME, 200);
//			parent::_addText(self::DESCRIPTION, false);
//			parent::_addForeignKeyColumn(self::OUTGOING_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
//			parent::_addVarchar(self::TEMPLATE, 200);
//		}

		protected function _deleteByTypeID($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);
			return $this->doDelete($crit);
		}

		protected function _countByTypeID($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);
			return $this->doCount($crit);
		}

		protected function _getByTypeID($type, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);

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

		public function countByStepID($step_id)
		{
			return $this->_countByTypeID('step', $step_id);
		}

		public function getByStepID($step_id)
		{
			return $this->_getByTypeID('step', $step_id);
		}

		public function countByWorkflowID($workflow_id)
		{
			return $this->_countByTypeID('workflow', $workflow_id);
		}

		public function getByWorkflowID($workflow_id)
		{
			return $this->_getByTypeID('workflow', $workflow_id);
		}
		
		public function reMapByWorkflowID($workflow_id, $mapper_array)
		{
			foreach ($mapper_array as $old_step_id => $new_step_id)
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::OUTGOING_STEP_ID, $new_step_id);
				$crit->addWhere(self::OUTGOING_STEP_ID, $old_step_id);
				$crit->addWhere(self::WORKFLOW_ID, $workflow_id);
				$this->doUpdate($crit);
			}
		}

	}