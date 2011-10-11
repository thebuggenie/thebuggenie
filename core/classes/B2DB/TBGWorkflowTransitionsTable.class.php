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

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowTransitionsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGWorkflowTransitionsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addForeignKeyColumn(self::OUTGOING_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
			parent::_addVarchar(self::TEMPLATE, 200);
		}

		public function createNew($workflow_id, $name, $description, $to_step_id, $template, $scope = null)
		{
			$scope = ($scope !== null) ? $scope : TBGContext::getScope()->getID();
			$crit = $this->getCriteria();
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::WORKFLOW_ID, $workflow_id);
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::DESCRIPTION, $description);
			$crit->addInsert(self::OUTGOING_STEP_ID, $to_step_id);
			$crit->addInsert(self::TEMPLATE, $template);

			$res = $this->doInsert($crit);

			return $res->getInsertID();
		}

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
		
		public function saveByID($name, $description, $outgoing_step_id, $template, $transition_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::NAME, $name);
			$crit->addUpdate(self::DESCRIPTION, $description);
			$crit->addUpdate(self::OUTGOING_STEP_ID, $outgoing_step_id);
			$crit->addUpdate(self::TEMPLATE, $template);
			$res = $this->doUpdateById($crit, $transition_id);
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