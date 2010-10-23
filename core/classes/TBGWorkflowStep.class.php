<?php

	/**
	 * Workflow step class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow step class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflowStep extends TBGIdentifiableClass
	{

		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		protected $_is_editable = null;

		protected $_is_closed = null;

		protected $_linked_status = null;

		protected $_incoming_transitions = null;

		protected $_num_incoming_transitions = null;

		protected $_outgoing_transitions = null;

		protected $_num_outgoing_transitions = null;

		/**
		 * The associated workflow object
		 *
		 * @var TBGWorkflow
		 */
		protected $_workflow = null;

		public function __construct($id, $row)
		{
			if (!is_numeric($id))
			{
				throw new Exception('Please specify a valid workflow step id');
			}
			if ($row === null)
			{
				$row = TBGWorkflowStepsTable::getTable()->getByID($id);
			}

			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified workflow step id does not exist');
			}

			$this->_itemid = $row->get(TBGWorkflowStepsTable::ID);
			$this->_name = $row->get(TBGWorkflowStepsTable::NAME);
			$this->_description = $row->get(TBGWorkflowStepsTable::DESCRIPTION);
			$this->_is_editable = (bool) $row->get(TBGWorkflowStepsTable::EDITABLE);
			$this->_is_closed = (bool) $row->get(TBGWorkflowStepsTable::IS_CLOSED);
			$this->_workflow = TBGContext::factory()->TBGWorkflow($row->get(TBGWorkflowStepsTable::WORKFLOW_ID));
			$this->_linked_status = $row->get(TBGWorkflowStepsTable::STATUS_ID);
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
		 * Whether this is a step in the builtin workflow that cannot be
		 * edited or removed
		 *
		 * @return boolean
		 */
		public function isCore()
		{
			return ($this->getWorkflow()->getID() == 1);
		}

		/**
		 * Return this steps linked status if any
		 * 
		 * @return TBGStatus
		 */
		public function getLinkedStatus()
		{
			if (is_numeric($this->_linked_status))
			{
				try
				{
					$this->_linked_status = TBGContext::factory()->TBGStatus($this->_linked_status);
				}
				catch (Exception $e)
				{
					$this->_linked_status = null;
				}
			}
			return $this->_linked_status;
		}

		/**
		 * Whether or not this step is linked to a specific status
		 *
		 * @return boolean
		 */
		public function hasLinkedStatus()
		{
			return ($this->getLinkedStatus() instanceof TBGStatus);
		}

		public function isEditable()
		{
			return (bool) $this->_is_editable;
		}

		public function isClosed()
		{
			return (bool) $this->_is_closed;
		}

		protected function _populateOutgoingTransitions()
		{
			if ($this->_outgoing_transitions === null)
			{
				$this->_outgoing_transitions = TBGWorkflowStepTransitionsTable::getTable()->getByStepID($this->getID());
			}
		}

		/**
		 * Get all outgoing transitions from this step
		 *
		 * @return array An array of TBGWorkflowTransition objects
		 */
		public function getOutgoingTransitions()
		{
			$this->_populateOutgoingTransitions();
			return $this->_outgoing_transitions;
		}

		public function getNumberOfOutgoingTransitions()
		{
			if ($this->_num_outgoing_transitions === null && $this->_outgoing_transitions !== null)
			{
				$this->_num_outgoing_transitions = count($this->_outgoing_transitions);
			}
			elseif ($this->_num_outgoing_transitions === null)
			{
				$this->_num_outgoing_transitions = TBGWorkflowStepTransitionsTable::getTable()->countByStepID($this->getID());
			}
			return $this->_num_outgoing_transitions;
		}

		public function hasOutgoingTransition(TBGWorkflowTransition $transition)
		{
			$transitions = $this->getOutgoingTransitions();
			return array_key_exists($transition->getID(), $transitions);
		}

		public function addOutgoingTransition(TBGWorkflowTransition $transition)
		{
			TBGWorkflowStepTransitionsTable::getTable()->addNew($this->getID(), $transition->getID(), $this->getWorkflow()->getID());
			if ($this->_outgoing_transitions !== null)
			{
				$this->_outgoing_transitions[$transition->getID()] = $transition;
			}
			if ($this->_num_outgoing_transitions !== null)
			{
				$this->_num_outgoing_transitions++;
			}
		}

		protected function _populateIncomingTransitions()
		{
			if ($this->_incoming_transitions === null)
			{
				$this->_incoming_transitions = TBGWorkflowTransitionsTable::getTable()->getByStepID($this->getID());
			}
		}

		/**
		 * Get all incoming transitions from this step
		 *
		 * @return array An array of TBGWorkflowTransition objects
		 */
		public function getIncomingTransitions()
		{
			$this->_populateIncomingTransitions();
			return $this->_incoming_transitions;
		}

		public function getNumberOfIncomingTransitions()
		{
			if ($this->_num_incoming_transitions === null && $this->_incoming_transitions !== null)
			{
				$this->_num_incoming_transitions = count($this->_incoming_transitions);
			}
			elseif ($this->_num_incoming_transitions === null)
			{
				$this->_num_incoming_transitions = TBGWorkflowTransitionsTable::getTable()->countByStepID($this->getID());
			}
			return $this->_num_incoming_transitions;
		}

		public function hasIncomingTransitions()
		{
			return (bool) ($this->getNumberOfIncomingTransitions() > 0);
		}

	}
