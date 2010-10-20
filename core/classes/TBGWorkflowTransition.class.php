<?php

	/**
	 * Workflow transition class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow transition class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflowTransition extends TBGIdentifiableClass
	{

		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		/**
		 * The outgoing step from this transition
		 *
		 * @var TBGWorkflowStep
		 */
		protected $_outgoing_step = null;

		public function __construct($id, $row)
		{
			if (!is_numeric($id))
			{
				throw new Exception('Please specify a valid workflow step id');
			}
			if ($row === null)
			{
				$row = TBGWorkflowTransitionsTable::getTable()->getByID($id);
			}

			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified workflow step transition id does not exist');
			}

			$this->_itemid = $row->get(TBGWorkflowTransitionsTable::ID);
			$this->_name = $row->get(TBGWorkflowTransitionsTable::NAME);
			$this->_description = $row->get(TBGWorkflowTransitionsTable::DESCRIPTION);
			$this->_outgoing_step = TBGContext::factory()->TBGWorkflowStep($row->get(TBGWorkflowTransitionsTable::TO_STEP_ID));
			$this->_workflow = TBGContext::factory()->TBGWorkflow($row->get(TBGWorkflowStepsTable::WORKFLOW_ID));
		}

		/**
		 * Returns the workflows description
		 *
		 * @return string
		 */
		public function getDescription()
		{
			return $this->_description;
		}

		/**
		 * Set the workflows description
		 *
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_description = $description;
		}

		/**
		 * Return the workflow
		 *
		 * @return TBGWorkflow
		 */
		public function getWorkflow()
		{
			return $this->_workflow;
		}

		/**
		 * Whether this is a transition in the builtin workflow that cannot be
		 * edited or removed
		 *
		 * @return boolean
		 */
		public function isCore()
		{
			return ($this->getWorkflow()->getID() == 1);
		}

		/**
		 * Return the outgoing step
		 *
		 * @return TBGWorkflowStep
		 */
		public function getOutgoingStep()
		{
			return $this->_outgoing_step;
		}

	}
