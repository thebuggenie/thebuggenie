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
			$templates = array('main/updateissueproperties' => 'Set issue properties or add comment');
			$event = TBGEvent::createNew('core', 'workflow_templates', null, array(), $templates)->trigger();
			
			return $event->getReturnList();
		}
		
		public static function loadFixtures(TBGScope $scope, TBGWorkflow $workflow, $steps)
		{
			$rejected_resolutions = array();
			$rejected_resolutions[] = TBGResolution::getResolutionByKeyish('notanissue')->getID();
			$rejected_resolutions[] = TBGResolution::getResolutionByKeyish('wontfix')->getID();
			$rejected_resolutions[] = TBGResolution::getResolutionByKeyish('cantfix')->getID();
			$rejected_resolutions[] = TBGResolution::getResolutionByKeyish('cantreproduce')->getID();
			$rejected_resolutions[] = TBGResolution::getResolutionByKeyish('duplicate')->getID();
			$resolved_resolutions = array();
			$resolved_resolutions[] = TBGResolution::getResolutionByKeyish('resolved')->getID();
			$resolved_resolutions[] = TBGResolution::getResolutionByKeyish('wontfix')->getID();
			$resolved_resolutions[] = TBGResolution::getResolutionByKeyish('postponed')->getID();
			$resolved_resolutions[] = TBGResolution::getResolutionByKeyish('duplicate')->getID();
			$closed_statuses = array();
			$closed_statuses[] = TBGStatus::getStatusByKeyish('closed')->getID();
			$closed_statuses[] = TBGStatus::getStatusByKeyish('postponed')->getID();
			$closed_statuses[] = TBGStatus::getStatusByKeyish('done')->getID();
			$closed_statuses[] = TBGStatus::getStatusByKeyish('fixed')->getID();
			$transitions = array();
			$transitions['investigateissue'] = array('name' => 'Investigate issue', 'description' => 'Assign the issue to yourself and start investigating it', 'outgoing_step' => 'investigating', 'template' => null, 'pre_validations' => array(TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES => 5), 'actions' => array(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0));
			$transitions['requestmoreinformation'] = array('name' => 'Request more information', 'description' => 'Move issue back to new state for more details', 'outgoing_step' => 'new', 'template' => 'main/updateissueproperties', 'actions' => array(TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0));
			$transitions['confirmissue'] = array('name' => 'Confirm issue', 'description' => 'Confirm that the issue is valid', 'outgoing_step' => 'confirmed', 'template' => null, 'actions' => array(TBGWorkflowTransitionAction::ACTION_SET_PERCENT => 10, TBGWorkflowTransitionAction::ACTION_SET_PRIORITY));
			$transitions['rejectissue'] = array('name' => 'Reject issue', 'description' => 'Reject the issue as invalid', 'outgoing_step' => 'rejected', 'template' => 'main/updateissueproperties', 'post_validations' => array(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID => join(',', $rejected_resolutions)), 'actions' => array(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION => 0, TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE => 0, TBGWorkflowTransitionAction::ACTION_SET_PERCENT => 100, TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0));
			$transitions['acceptissue'] = array('name' => 'Accept issue', 'description' => 'Accept the issue and assign it to yourself', 'outgoing_step' => 'inprogress', 'template' => null, 'pre_validations' => array(TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES => 5), 'actions' => array(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0, TBGWorkflowTransitionAction::ACTION_USER_START_WORKING => 0));
			$transitions['reopenissue'] = array('name' => 'Reopen issue', 'description' => 'Reopen the issue', 'outgoing_step' => 'new', 'template' => null, 'actions' => array(TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION => 0, TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE => 0, TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT => 0));
			$transitions['assignissue'] = array('name' => 'Assign issue', 'description' => 'Accept the issue and assign it to someone', 'outgoing_step' => 'inprogress', 'template' => 'main/updateissueproperties', 'actions' => array(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE => 0, TBGWorkflowTransitionAction::ACTION_USER_START_WORKING => 0));
			$transitions['markreadyfortesting'] = array('name' => 'Mark ready for testing', 'description' => 'Mark the issue as ready to be tested', 'outgoing_step' => 'readyfortesting', 'template' => null, 'actions' => array(TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0, TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0));
			$transitions['resolveissue'] = array('name' => 'Resolve issue', 'description' => 'Resolve the issue', 'outgoing_step' => 'closed', 'template' => 'main/updateissueproperties', 'post_validations' => array(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID => join(',', $closed_statuses), TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID => join(',', $resolved_resolutions)), 'actions' => array(TBGWorkflowTransitionAction::ACTION_SET_STATUS => 0, TBGWorkflowTransitionAction::ACTION_SET_PERCENT => 100, TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION => 0, TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0));
			$transitions['testissuesolution'] = array('name' => 'Test issue solution', 'description' => 'Check whether the solution is valid', 'outgoing_step' => 'testing', 'template' => null, 'actions' => array(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF => 0, TBGWorkflowTransitionAction::ACTION_USER_START_WORKING => 0));
			$transitions['acceptissuesolution'] = array('name' => 'Accept issue solution', 'description' => 'Mark the issue as resolved', 'outgoing_step' => 'closed', 'template' => 'main/updateissueproperties', 'actions' => array(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION => 0, TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0, TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0));
			$transitions['rejectissuesolution'] = array('name' => 'Reject issue solution', 'description' => 'Reject the proposed solution and mark the issue as in progress', 'outgoing_step' => 'inprogress', 'template' => null, 'actions' => array(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION => 0, TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE => 0, TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING => 0));

			foreach ($transitions as $key => $transition)
			{
				$transition_object = new TBGWorkflowTransition();
				$transition_object->setName($transition['name']);
				$transition_object->setDescription($transition['description']);
				$transition_object->setOutgoingStep($steps[$transition['outgoing_step']]['step']);
				$transition_object->setTemplate($transition['template']);
				$transition_object->setWorkflow($workflow);
				$transition_object->save();
				$transitions[$key] = $transition_object;
				
				if (array_key_exists('pre_validations', $transition) && is_array($transition['pre_validations']))
				{
					foreach ($transition['pre_validations'] as $type => $validation)
					{
						$rule = new TBGWorkflowTransitionValidationRule();
						$rule->setTransition($transition_object);
						$rule->setPre();
						$rule->setRule($type);
						$rule->setRuleValue($validation);
						$rule->setWorkflow($workflow);
						$rule->save();
					}
				}
				if (array_key_exists('post_validations', $transition) && is_array($transition['post_validations']))
				{
					foreach ($transition['post_validations'] as $type => $validation)
					{
						$rule = new TBGWorkflowTransitionValidationRule();
						$rule->setTransition($transition_object);
						$rule->setPost();
						$rule->setRule($type);
						$rule->setRuleValue($validation);
						$rule->setWorkflow($workflow);
						$rule->save();
					}
				}
				if (array_key_exists('actions', $transition) && is_array($transition['actions']))
				{
					foreach ($transition['actions'] as $type => $action)
					{
						$action_object = new TBGWorkflowTransitionAction();
						$action_object->setActionType($type);
						$action_object->setTransition($transition_object);
						$action_object->setWorkflow($workflow);
						if (!is_null($action)) $action_object->setTargetValue($action);
						$action_object->save();
					}
				}
			}
			
			return $transitions;
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
			return false;
			return $this->getWorkflow()->isCore();
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
			if (array_key_exists($template, $this->getTemplates()) || !$template)
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
			$actions = $this->getActions();
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
			foreach ($this->getActions() as $action)
			{
				if (!$action->isValid($request))
				{
					$this->_validation_errors[$action->getActionType()] = true;
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
			$comment->setSystemComment(false);
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
			if ($request->hasParameter('comment_body') && trim($request->getParameter('comment_body') != '')) {
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
			
			foreach ($this->getPreValidationRules() as $rule)
			{
				$new_rule = clone $rule;
				$new_rule->setTransition($new_transition);
				$new_rule->setWorkflow($new_workflow);
				$new_rule->save();
			}
			
			foreach ($this->getPostValidationRules() as $rule)
			{
				$new_rule = clone $rule;
				$new_rule->setTransition($new_transition);
				$new_rule->setWorkflow($new_workflow);
				$new_rule->save();
			}
			
			foreach ($this->getActions() as $action)
			{
				$new_action = clone $action;
				$new_action->setTransition($new_transition);
				$new_action->setWorkflow($new_workflow);
				$new_action->save();
			}
			
			return $new_transition;
		}

	}
