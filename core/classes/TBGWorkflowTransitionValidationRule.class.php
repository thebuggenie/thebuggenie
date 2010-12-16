<?php

	/**
	 * Workflow transition validation rule class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow transition validation rule class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflowTransitionValidationRule extends TBGIdentifiableClass
	{
		
		const RULE_MAX_ASSIGNED_ISSUES = 'max_assigned_issues';

		static protected $_b2dbtablename = 'TBGWorkflowTransitionValidationRulesTable';
		
		protected $_rule = null;

		protected $_rule_value = null;

		/**
		 * The connected transition
		 *
		 * @var TBGWorkflowTransition
		 * @Class TBGWorkflowTransition
		 */
		protected $_transition_id = null;

		/**
		 * The associated workflow object
		 *
		 * @var TBGWorkflow
		 * @Class TBGWorkflow
		 */
		protected $_workflow_id = null;

		/**
		 * Return the workflow
		 *
		 * @return TBGWorkflow
		 */
		public function getWorkflow()
		{
			return $this->_getPopulatedObjectFromProperty('_workflow_id');
		}

		public function setWorkflow(TBGWorkflow $workflow)
		{
			$this->_workflow_id = $workflow;
		}

		public function setTransition(TBGWorkflowTransition $transition)
		{
			$this->_transition_id = $transition;
		}
		
		public function getTransition()
		{
			return $this->_getPopulatedObjectFromProperty('_transition_id');
		}
		
		public function setRule($rule)
		{
			$this->_rule = $rule;
		}
		
		public function getRule()
		{
			return $this->_rule;
		}
		
		public function setRuleValue($rule_value)
		{
			$this->_rule_value = $rule_value;
		}
		
		public function getRuleValue()
		{
			return $this->_rule_value;
		}
		
		public function isValid()
		{
			switch ($this->_rule)
			{
				case self::RULE_MAX_ASSIGNED_ISSUES:
					$num_issues = (int) $this->getRuleValue();
					return ($num_issues) ? (bool) (count(TBGContext::getUser()->getUserAssignedIssues()) < $num_issues) : true;
					break;
			}
		}

	}
