<?php

	/**
	 * Workflow class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflow extends TBGIdentifiableClass
	{

		static protected $_b2dbtablename = 'TBGWorkflowsTable';
		
		protected static $_workflows = null;

		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		/**
		 * Whether the workflow is active or not
		 *
		 * @var boolean
		 */
		protected $_is_active = true;

		protected $_steps = null;

		protected $_num_steps = null;

		protected $_transitions = null;
		
		protected $_number_of_schemes = null;

		/**
		 * Return all workflows in the system
		 *
		 * @return array An array of TBGWorkflow objects
		 */
		public static function getAll()
		{
			if (self::$_workflows === null)
			{
				self::$_workflows = array();
				if ($res = TBGWorkflowsTable::getTable()->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_workflows[$row->get(TBGWorkflowsTable::ID)] = TBGContext::factory()->TBGWorkflow($row->get(TBGWorkflowsTable::ID), $row);
					}
				}
			}
			return self::$_workflows;
		}

		public static function loadFixtures(TBGScope $scope)
		{
			$workflow = new TBGWorkflow();
			$workflow->setName("Default workflow");
			$workflow->setDescription("This is the default workflow. It is used by all projects with no specific workflow selected, and for issue types with no specific workflow specified. This workflow cannot be edited or removed.");
			$workflow->save();
			
			TBGWorkflowStep::loadFixtures($scope, $workflow);
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
		 * Whether this is the builtin workflow that cannot be
		 * edited or removed
		 *
		 * @return boolean
		 */
		public function isCore()
		{
			return ($this->getID() == 1);
		}

		/**
		 * Whether this is the builtin workflow that cannot be edited or removed
		 *
		 * @return boolean
		 */
		public function isActive()
		{
			return (bool) $this->_is_active;
		}

		protected function _populateTransitions()
		{
			if ($this->_transitions === null)
			{
				$this->_transitions = TBGWorkflowTransitionsTable::getTable()->getByWorkflowID($this->getID());
			}
		}
		
		/**
		 * Get all transitions in this workflow
		 *
		 * @return array An array of TBGWorkflowTransition objects
		 */
		public function getTransitions()
		{
			$this->_populateTransitions();
			return $this->_transitions;
		}

		protected function _populateSteps()
		{
			if ($this->_steps === null)
			{
				$this->_steps = TBGWorkflowStepsTable::getTable()->getByWorkflowID($this->getID());
			}
		}

		/**
		 * Get all steps in this workflow
		 *
		 * @return array An array of TBGWorkflowStep objects
		 */
		public function getSteps()
		{
			$this->_populateSteps();
			return $this->_steps;
		}

		/**
		 * Get the first step in this workflow
		 *
		 * @return TBGWorkflowStep
		 */
		public function getFirstStep()
		{
			$steps = $this->getSteps();
			return (is_array($steps)) ? array_shift($steps) : null;
		}

		public function getNumberOfSteps()
		{
			if ($this->_num_steps === null && $this->_steps !== null)
			{
				$this->_num_steps = count($this->_steps);
			}
			elseif ($this->_num_steps === null)
			{
				$this->_num_steps = TBGWorkflowStepsTable::getTable()->countByWorkflowID($this->getID());
			}
			return $this->_num_steps;
		}

		public function isInUse()
		{
			if ($this->_number_of_schemes === null)
			{
				$this->_number_of_schemes = TBGWorkflowIssuetypeTable::getTable()->countSchemesByWorkflowID($this->getID());
			}
			return (bool) $this->_number_of_schemes;
		}
		
		public function getNumberOfSchemes()
		{
			return $this->_number_of_schemes;
		}
		
		public function copy($new_name)
		{
			$new_workflow = new TBGWorkflow();
			$new_workflow->setName($new_name);
			$new_workflow->save();
			$step_mapper = array();
			$transition_mapper = array();
			foreach ($this->getSteps() as $key => $step)
			{
				$this->_steps[$key] = $step->copy($new_workflow);
				$step_mapper[$key] = $this->_steps[$key]->getID();
			}
			foreach ($this->getTransitions() as $key => $transition)
			{
				$old_id = $transition->getID();
				$this->_transitions[$key] = $transition->copy($new_workflow);
				$transition_mapper[$old_id] = $this->_transitions[$key]->getID();
			}
			TBGWorkflowStepTransitionsTable::getTable()->copyByWorkflowIDs($this->getID(), $new_workflow->getID());
			TBGWorkflowStepTransitionsTable::getTable()->reMapStepIDsByWorkflowID($new_workflow->getID(), $step_mapper);
			TBGWorkflowTransitionsTable::getTable()->reMapByWorkflowID($new_workflow->getID(), $step_mapper);
			TBGWorkflowStepTransitionsTable::getTable()->reMapTransitionIDsByWorkflowID($new_workflow->getID(), $transition_mapper);
			
			return $new_workflow;
		}

		public function moveIssueToMatchingWorkflowStep(TBGIssue $issue)
		{
			$change_step = false;
			
			if ($issue->isStatusChanged() || $issue->isResolutionChanged())
			{
				$change_step = true;
			}
			
			if ($change_step)
			{
				foreach ($this->getSteps() as $step)
				{
					if ($step->hasLinkedStatus() && $issue->getStatus() instanceof TBGStatus && $step->getLinkedStatusID() == $issue->getStatus()->getID())
					{
						$step->applyToIssue($issue);
						return true;
					}
				}
				foreach ($this->getSteps() as $step)
				{
					if (!$step->hasLinkedStatus())
					{
						foreach ($step->getIncomingTransitions() as $transition)
						{
							if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID))
							{
								$rule = $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID);
								if ($rule->isValid($issue->getStatus()))
								{
									$step->applyToIssue($issue);
									return true;
								}
							}
							else
							{
								$step->applyToIssue($issue);
								return true;
							}
						}
					}
				}
				throw new TBGWorkflowException('Cannot find valid workflow step');
			}
		}
		
	}
