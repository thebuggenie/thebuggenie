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
	 *
	 * @Table(name="TBGWorkflowsTable")
	 */
	class TBGWorkflow extends TBGIdentifiableScopedClass
	{

		protected static $_workflows = null;
		
		protected static $_num_workflows = null;

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
		 * Whether the workflow is active or not
		 *
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_is_active = true;

		/**
		 * This workflow's steps
		 *
		 * @var array|TBGWorkflowStep
		 * @Relates(class="TBGWorkflowStep", collection=true, foreign_column="workflow_id")
		 */
		protected $_steps = null;

		protected $_num_steps = null;

		/**
		 * This workflow's transitions
		 *
		 * @var array|TBGWorkflowTransition
		 * @Relates(class="TBGWorkflowTransition", collection=true, foreign_column="workflow_id")
		 */
		protected $_transitions = null;
		
		/**
		 * This workflow's schemes
		 *
		 * @var array|TBGWorkflowTransition
		 * @Relates(class="TBGWorkflowScheme", collection=true, manytomany=true, joinclass="TBGWorkflowIssuetypeTable")
		 */
		protected $_schemes = null;

		protected $_num_schemes = null;
		
		protected static function _populateWorkflows()
		{
			if (self::$_workflows === null)
			{
				self::$_workflows = TBGWorkflowsTable::getTable()->getAll();
			}
		}
		
		/**
		 * Return all workflows in the system
		 *
		 * @return array An array of TBGWorkflow objects
		 */
		public static function getAll()
		{
			self::_populateWorkflows();
			return self::$_workflows;
		}
		
		public static function loadFixtures(TBGScope $scope)
		{
			$workflow = new TBGWorkflow();
			$workflow->setName("Default workflow");
			$workflow->setDescription("This is the default workflow. It is used by all projects with no specific workflow selected, and for issue types with no specific workflow specified. This workflow cannot be edited or removed.");
			$workflow->setScope($scope->getID());
			$workflow->save();

			TBGSettings::saveSetting(TBGSettings::SETTING_DEFAULT_WORKFLOW, $workflow->getID());
			TBGWorkflowStep::loadFixtures($scope, $workflow);
		}

		public static function getWorkflowsCount()
		{
			if (self::$_num_workflows === null)
			{
				if (self::$_workflows !== null)
					self::$_num_workflows = count(self::$_workflows);
				else
					self::$_num_workflows = TBGWorkflowsTable::getTable()->countWorkflows();
			}

			return self::$_num_workflows;
		}

		public static function getCustomWorkflowsCount()
		{
			return self::getWorkflowsCount() - 1;
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
			return ($this->getID() == TBGSettings::getCoreWorkflow()->getID());
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
				$this->_b2dbLazyload('_transitions');
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
				$this->_b2dbLazyload('_steps');
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
				$this->_b2dbLazycount('_steps');
			}
			return $this->_num_steps;
		}

		public function isInUse()
		{
			return (bool) $this->getNumberOfSchemes();
		}
		
		public function getNumberOfSchemes()
		{
			if ($this->_num_schemes === null && $this->_schemes !== null)
			{
				$this->_num_schemes = count($this->_schemes);
			}
			elseif ($this->_num_schemes === null)
			{
				$this->_num_schemes = $this->_b2dbLazycount('_schemes');
			}
			return $this->_num_schemes;
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
						}
					}
				}
			}
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
