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

		static protected $_b2dbtablename = 'TBGWorkflowStepsTable';
		
		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		protected $_editable = null;

		protected $_closed = null;

		protected $_status_id = null;

		protected $_incoming_transitions = null;

		protected $_num_incoming_transitions = null;

		protected $_outgoing_transitions = null;

		protected $_num_outgoing_transitions = null;

		/**
		 * The associated workflow object
		 *
		 * @var TBGWorkflow
		 */
		protected $_workflow_id = null;

		public function _construct(B2DBRow $row, $foreign_key = null)
		{
			$this->_workflow_id = TBGContext::factory()->TBGWorkflow($this->_workflow_id);
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
			return $this->_workflow_id;
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
			if (is_numeric($this->_status_id))
			{
				try
				{
					$this->_status_id = TBGContext::factory()->TBGStatus($this->_status_id);
				}
				catch (Exception $e)
				{
					$this->_status_id = null;
				}
			}
			return $this->_status_id;
		}

		public function setLinkedStatusID($status_id)
		{
			$this->_status_id = $status_id;
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

		public function getLinkedStatusID()
		{
			return ($this->hasLinkedStatus()) ? $this->getLinkedStatus()->getID() : null;
		}

		public function isEditable()
		{
			return (bool) $this->_editable;
		}

		public function setIsEditable($is_editable = true)
		{
			$this->_editable = $is_editable;
		}

		public function isClosed()
		{
			return (bool) $this->_closed;
		}

		public function setIsClosed($is_closed = true)
		{
			$this->_closed = $is_closed;
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

		public function deleteOutgoingTransitions()
		{
			TBGWorkflowStepTransitionsTable::getTable()->deleteByStepID($this->getID());
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
		
		public function getAvailableTransitionsForIssue(TBGIssue $issue)
		{
			$return_array = array();
			foreach ($this->getOutgoingTransitions() as $transition)
			{
				if ($transition->isAvailableForIssue($issue))
					$return_array[] = $transition;
			}
			
			return $return_array;
		}
		
		public function applyToIssue(TBGIssue $issue)
		{
			$issue->setWorkflowStep($this);
			if ($this->hasLinkedStatus())
			{
				$issue->setStatus($this->getLinkedStatusID());
			}
			if ($this->isClosed())
			{
				$issue->close();
			}
			else
			{
				$issue->open();
			}
		}

	}
