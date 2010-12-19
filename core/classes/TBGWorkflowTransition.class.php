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

		static protected $_b2dbtablename = 'TBGWorkflowTransitionsTable';
		
		/**
		 * The workflow description
		 *
		 * @var string
		 */
		protected $_description = null;

		protected $_incoming_steps = null;

		protected $_actions = null;

		protected $_num_incoming_steps = null;

		/**
		 * The outgoing step from this transition
		 *
		 * @var TBGWorkflowStep
		 * @Class TBGWorkflowStep
		 */
		protected $_outgoing_step_id = null;

		protected $_template = null;
		
		/**
		 * The originating request
		 * 
		 * @var TBGRequest
		 */
		protected $_request = null;
		
		protected $_pre_validation_rules = null;
		
		protected $_validation_errors = array();

		/**
		 * The associated workflow object
		 *
		 * @var TBGWorkflow
		 * @Class TBGWorkflow
		 */
		protected $_workflow_id = null;

		public static function getTemplates()
		{
			$templates = array('main/updateissueproperties' => 'Set issue properties', 'template_2' => 'Template 2', 'template_3' => 'Template 3');
			$event = TBGEvent::createNew('core', 'workflow_templates', null, array(), $templates)->trigger();
			
			return $event->getReturnList();
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
			return $this->_getPopulatedObjectFromProperty('_workflow_id');
		}

		public function setWorkflow(TBGWorkflow $workflow)
		{
			$this->_workflow_id = $workflow;
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

		public function getTemplate()
		{
			return $this->_template;
		}
		
		/**
		 * Set the template to be used
		 * 
		 * @param string $template 
		 */
		public function setTemplate($template)
		{
			if (array_key_exists($template, $this->getTemplates()))
			{
				$this->_template = $template;
			}
		}

		public function getTemplateName()
		{
			$templates = self::getTemplates();
			return $templates[$this->getTemplate()];
		}

		public function hasTemplate()
		{
			return (bool) ($this->getTemplate() != '');
		}

		protected function _populateIncomingSteps()
		{
			if ($this->_incoming_steps === null)
			{
				$this->_incoming_steps = TBGWorkflowStepTransitionsTable::getTable()->getByTransitionID($this->getID());
			}
		}

		public function getIncomingSteps()
		{
			$this->_populateIncomingSteps();
			return $this->_incoming_steps;
		}

		public function getNumberOfIncomingSteps()
		{
			if ($this->_num_incoming_steps === null && $this->_incoming_steps !== null)
			{
				$this->_num_incoming_steps = count($this->_incoming_steps);
			}
			elseif ($this->_num_incoming_steps === null)
			{
				$this->_num_incoming_steps = TBGWorkflowStepTransitionsTable::getTable()->countByTransitionID($this->getID());
			}
			return $this->_num_incoming_steps;
		}

		/**
		 * Return the outgoing step
		 *
		 * @return TBGWorkflowStep
		 */
		public function getOutgoingStep()
		{
			return $this->_getPopulatedObjectFromProperty('_outgoing_step_id');
		}
		
		/**
		 * Set the outgoing step
		 * 
		 * @param TBGWorkflowStep $step A workflow step
		 */
		public function setOutgoingStep(TBGWorkflowStep $step)
		{
			$this->_outgoing_step_id = $step;
		}
		
		public function deleteTransition($direction)
		{
			if ($direction == 'incoming')
			{
				$this->delete();
			}
			else
			{
				$this->_preDelete();
			}
		}
		
		public function _preDelete()
		{
			TBGWorkflowStepTransitionsTable::getTable()->deleteByTransitionID($this->getID());
		}

		protected function _populateValidationRules()
		{
			if ($this->_pre_validation_rules === null)
			{
				$rules = TBGWorkflowTransitionValidationRulesTable::getTable()->getByTransitionID($this->getID());
				$this->_pre_validation_rules = $rules['pre'];
				$this->_post_validation_rules = $rules['post'];
			}
		}
		
		public function getPreValidationRules()
		{
			$this->_populateValidationRules();
			return $this->_pre_validation_rules;
		}

		public function hasPreValidationRules()
		{
			return (bool) count($this->getPreValidationRules());
		}
		
		public function hasPreValidationRule($rule)
		{
			$rules = $this->getPreValidationRules();
			return (array_key_exists($rule, $rules));
		}
		
		public function getPreValidationRule($rule)
		{
			$rules = $this->getPreValidationRules();
			return (array_key_exists($rule, $rules)) ? $rules[$rule] : null;
		}
		
		public function getPostValidationRules()
		{
			$this->_populateValidationRules();
			return $this->_post_validation_rules;
		}

		public function hasPostValidationRules()
		{
			return (bool) count($this->getPostValidationRules());
		}
		
		public function hasPostValidationRule($rule)
		{
			$rules = $this->getPostValidationRules();
			return (array_key_exists($rule, $rules));
		}
		
		public function getPostValidationRule($rule)
		{
			$rules = $this->getPostValidationRules();
			return (array_key_exists($rule, $rules)) ? $rules[$rule] : null;
		}
		
		public function isAvailableForIssue(TBGIssue $issue)
		{
			foreach ($this->getPreValidationRules() as $validation_rule)
			{
				if ($validation_rule instanceof TBGWorkflowTransitionValidationRule)
				{
					if (!$validation_rule->isValid($issue)) return false;
				}
			}
			return true;
		}
		
		public function getProperties()
		{
			return ($this->getOutgoingStep()->isClosed()) ? array('resolution', 'status') : array();
		}

		protected function _populateActions()
		{
			if ($this->_actions === null)
			{
				$this->_actions = TBGWorkflowTransitionAction::getByTransitionID($this->getID());
			}
		}
		
		public function getActions()
		{
			$this->_populateActions();
			return $this->_actions;
		}
		
		public function hasActions()
		{
			return (bool) count($this->getActions());
		}
		
		public function hasAction($action_type)
		{
			$actions = $this->getActions();
			return array_key_exists($action_type, $actions);
		}
		
		public function getAction($action_type)
		{
			return (array_key_exists($action_type, $actions)) ? $actions[$action_type] : null;
		}
		
		public function validateFromRequest(TBGRequest $request)
		{
			$this->_request = $request;
			foreach ($this->getPostValidationRules() as $rule)
			{
				if (!$rule->isValid($request))
				{
					$this->_validation_errors[$rule->getRule()] = true;
				}
			}
			return empty($this->_validation_errors);
		}
		
		public function getValidationErrors()
		{
			return array_keys($this->_validation_errors);
		}
		
		public function listenIssueSaveAddComment(TBGEvent $event)
		{
			$comment = $event->getParameter('comment');
			$comment->setContent($this->_request->getParameter('comment_body', null, false) . "\n\n" . $comment->getContent());
			$comment->save();
		}

		/**
		 * Transition an issue to the outgoing step, based on request data if available
		 * 
		 * @param TBGIssue $issue
		 * @param TBGRequest $request 
		 */
		public function transitionIssueToOutgoingStepFromRequest(TBGIssue $issue, $request = null)
		{
			$request = ($request !== null) ? $request : $this->_request;
			$this->getOutgoingStep()->applyToIssue($issue);
			if ($request->hasParameter('comment_body')) {
				$this->_request = $request;
				TBGEvent::listen('core', 'TBGIssue::save', array($this, 'listenIssueSaveAddComment'));
			}
			
			if (!empty($this->_validation_errors)) return false;
			
			foreach ($this->getActions() as $action)
			{
				$action->perform($issue, $request);
			}
			
			$issue->save();
		}

		public function copy(TBGWorkflow $new_workflow)
		{
			$new_transition = clone $this;
			$new_transition->setWorkflow($new_workflow);
			$new_transition->save();
			return $new_transition;
		}

	}
