<?php

	/**
	 * Workflow transitions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGWorkflowTransitionsTable extends B2DBTable
	{

		const B2DBNAME = 'workflow_transitions';
		const ID = 'workflow_transitions.id';
		const SCOPE = 'workflow_transitions.scope';
		const WORKFLOW_ID = 'workflow_transitions.workflow_id';
		const NAME = 'workflow_transitions.name';
		const DESCRIPTION = 'workflow_transitions.description';
		const TO_STEP_ID = 'workflow_transitions.to_step_id';
		const TEMPLATE = 'workflow_transitions.template';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowTransitionsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowTransitionsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_ID, TBGWorkflowsTable::getTable(), TBGWorkflowsTable::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addForeignKeyColumn(self::TO_STEP_ID, TBGWorkflowStepsTable::getTable(), TBGWorkflowStepsTable::ID);
			parent::_addVarchar(self::TEMPLATE, 200);
		}

		public function loadFixtures($scope)
		{
			$transitions = array();
			$transitions[] = array('name' => 'Investigate issue', 'description' => 'Assign the issue to yourself and start investigating it', 'to_step_id' => 2, 'template' => null);
			$transitions[] = array('name' => 'Confirm issue', 'description' => 'Confirm that the issue is valid', 'to_step_id' => 3, 'template' => null);
			$transitions[] = array('name' => 'Reject issue', 'description' => 'Reject the issue as invalid', 'to_step_id' => 7, 'template' => null);
			$transitions[] = array('name' => 'Accept issue', 'description' => 'Accept the issue and assign it to yourself', 'to_step_id' => 4, 'template' => null);
			$transitions[] = array('name' => 'Reopen issue', 'description' => 'Reopen the issue', 'to_step_id' => 1, 'template' => null);

			foreach ($transitions as $transition)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::WORKFLOW_ID, 1);
				$crit->addInsert(self::SCOPE, $scope);
				$crit->addInsert(self::NAME, $transition['name']);
				$crit->addInsert(self::DESCRIPTION, $transition['description']);
				$crit->addInsert(self::TO_STEP_ID, $transition['to_step_id']);
				$crit->addInsert(self::TEMPLATE, $transition['template']);
				$this->doInsert($crit);
			}
		}

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit, false);
			return $row;
		}

		public function countByStepID($step_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::TO_STEP_ID, $step_id);
			return $this->doCount($crit);
		}

		public function getByStepID($step_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::TO_STEP_ID, $step_id);

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