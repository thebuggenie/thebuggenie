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
	 *
	 * @Table(name="TBGWorkflowStepsTable")
	 */
	class TBGWorkflowStep extends TBGIdentifiableScopedClass
	{

		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * The workflow description
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_description = null;

		/**
		 * @Column(type="boolean")
		 */
		protected $_editable = null;

		/**
		 * @Column(type="boolean")
		 */
		protected $_closed = null;

		/**
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGStatus")
		 */
		protected $_status_id = null;

		protected $_incoming_transitions = null;

		protected $_num_incoming_transitions = null;

		protected $_outgoing_transitions = null;

		protected $_num_outgoing_transitions = null;

		/**
		 * The associated workflow object
		 *
		 * @var TBGWorkflow
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGWorkflow")
		 */
		protected $_workflow_id = null;

		public static function loadFixtures(TBGScope $scope, TBGWorkflow $workflow)
		{
			$steps = array();
			$steps['new'] = array('name' => 'New', 'description' => 'A new issue, not yet handled', 'status_id' => TBGStatus::getStatusByKeyish('new')->getID(), 'transitions' => array('investigateissue', 'confirmissue', 'rejectissue', 'acceptissue', 'resolveissue'), 'editable' => true, 'is_closed' => false);
			$steps['investigating'] = array('name' => 'Investigating', 'description' => 'An issue that is being investigated, looked into or is by other means between new and unconfirmed state', 'status_id' => TBGStatus::getStatusByKeyish('investigating')->getID(), 'transitions' => array('requestmoreinformation', 'confirmissue', 'rejectissue', 'acceptissue'), 'editable' => true, 'is_closed' => false);
			$steps['confirmed'] = array('name' => 'Confirmed', 'description' => 'An issue that has been confirmed', 'status_id' => TBGStatus::getStatusByKeyish('confirmed')->getID(), 'transitions' => array('acceptissue', 'assignissue', 'resolveissue'), 'editable' => false, 'is_closed' => false);
			$steps['inprogress'] = array('name' => 'In progress', 'description' => 'An issue that is being adressed', 'status_id' => TBGStatus::getStatusByKeyish('beingworkedon')->getID(), 'transitions' => array('rejectissue', 'markreadyfortesting', 'resolveissue'), 'editable' => false, 'is_closed' => false);
			$steps['readyfortesting'] = array('name' => 'Ready for testing', 'description' => 'An issue that has been marked fixed and is ready for testing', 'status_id' => TBGStatus::getStatusByKeyish('readyfortesting/qa')->getID(), 'transitions' => array('resolveissue', 'testissuesolution'), 'editable' => false, 'is_closed' => false);
			$steps['testing'] = array('name' => 'Testing', 'description' => 'An issue where the proposed or implemented solution is currently being tested or approved', 'status_id' => TBGStatus::getStatusByKeyish('testing/qa')->getID(), 'transitions' => array('acceptissuesolution', 'rejectissuesolution'), 'editable' => false, 'is_closed' => false);
			$steps['rejected'] = array('name' => 'Rejected', 'description' => 'A closed issue that has been rejected', 'status_id' => TBGStatus::getStatusByKeyish('notabug')->getID(), 'transitions' => array('reopenissue'), 'editable' => false, 'is_closed' => true);
			$steps['closed'] = array('name' => 'Closed', 'description' => 'A closed issue', 'status_id' => null, 'transitions' => array('reopenissue'), 'editable' => false, 'is_closed' => true);

			foreach ($steps as $key => $step)
			{
				$step_object = new TBGWorkflowStep();
				$step_object->setWorkflow($workflow);
				$step_object->setName($step['name']);
				$step_object->setDescription($step['description']);
				$step_object->setLinkedStatusID($step['status_id']);
				$step_object->setIsClosed($step['is_closed']);
				$step_object->setIsEditable($step['editable']);
				$step_object->save();
				$steps[$key]['step'] = $step_object;
			}
			
			$transitions = TBGWorkflowTransition::loadFixtures($scope, $workflow, $steps);

			$transition = new TBGWorkflowTransition();
			$step = $steps['new']['step'];
			$transition->setOutgoingStep($step);
			$transition->setName('Issue created');
			$transition->setWorkflow($workflow);
			$transition->setDescription('This is the initial transition for issues using this workflow');
			$transition->save();
			$workflow->setInitialTransition($transition);
			$workflow->save();

			foreach ($steps as $step)
			{
				foreach ($step['transitions'] as $transition)
				{
					$step['step']->addOutgoingTransition($transitions[$transition]);
				}
			}
			
		}

		public static function getAllByWorkflowSchemeID($scheme_id)
		{
			$ids = TBGWorkflowStepsTable::getTable()->getAllByWorkflowSchemeID($scheme_id);
			$steps = array();
			foreach ($ids as $step_id)
			{
				$steps[$step_id] = new TBGWorkflowStep((int) $step_id);
			}

			return $steps;
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
			return $this->_b2dbLazyload('_workflow_id');
		}

		public function setWorkflow(TBGWorkflow $workflow)
		{
			$this->_workflow_id = $workflow;
		}
		
		/**
		 * Whether this is a step in the builtin workflow that cannot be
		 * edited or removed
		 *
		 * @return boolean
		 */
		public function isCore()
		{
			return $this->getWorkflow()->isCore();
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
		 * @return array|TBGWorkflowTransition An array of TBGWorkflowTransition objects
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
					$return_array[$transition->getID()] = $transition;
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
		
		public function copy(TBGWorkflow $new_workflow)
		{
			$new_step = clone $this;
			$new_step->setWorkflow($new_workflow);
			$new_step->save();
			return $new_step;
		}
		
		/**
		 * Return the items name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

	}
