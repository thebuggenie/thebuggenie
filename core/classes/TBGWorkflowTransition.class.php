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

		protected $_incoming_steps = null;

		protected $_actions = null;

		protected $_num_incoming_steps = null;

		/**
		 * The outgoing step from this transition
		 *
		 * @var TBGWorkflowStep
		 */
		protected $_outgoing_step = null;

		protected $_template = null;
		
		/**
		 * The originating request
		 * 
		 * @var TBGRequest
		 */
		protected $_request = null;
		
		protected $_validation_errors = array();

		public static function getTemplates()
		{
			$templates = array('main/updateissueproperties' => 'Set issue properties', 'template_2' => 'Template 2', 'template_3' => 'Template 3');
			$event = TBGEvent::createNew('core', 'workflow_templates', null, array(), $templates)->trigger();
			
			return $event->getReturnList();
		}

		public static function createNew($workflow_id, $name, $description, $to_step_id, $template)
		{
			$id = TBGWorkflowTransitionsTable::getTable()->createNew($workflow_id, $name, $description, $to_step_id, $template);
			return TBGContext::factory()->TBGWorkflowTransition($id);
		}

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
			$this->_template = $row->get(TBGWorkflowTransitionsTable::TEMPLATE);
			$this->_outgoing_step = TBGContext::factory()->TBGWorkflowStep($row->get(TBGWorkflowTransitionsTable::TO_STEP_ID));
			$this->_workflow = TBGContext::factory()->TBGWorkflow($row->get(TBGWorkflowTransitionsTable::WORKFLOW_ID));
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
			return $this->_outgoing_step;
		}
		
		/**
		 * Set the outgoing step
		 * 
		 * @param TBGWorkflowStep $step A workflow step
		 */
		public function setOutgoingStep(TBGWorkflowStep $step)
		{
			$this->_outgoing_step = $step;
		}
		
		public function save()
		{
			TBGWorkflowTransitionsTable::getTable()->saveByID($this->getName(), $this->getDescription(), $this->getOutgoingStep()->getID(), $this->getTemplate(), $this->getID());
		}

		public function delete($direction)
		{
			TBGWorkflowStepTransitionsTable::getTable()->deleteByTransitionID($this->getID());
			if ($direction == 'incoming')
			{
				TBGWorkflowTransitionsTable::getTable()->doDeleteById($this->getID());
			}
		}
		
		public function isAvailableForIssue(TBGIssue $issue)
		{
			return true;
		}
		
		public function getProperties()
		{
			return ($this->getOutgoingStep()->isClosed()) ? array('resolution', 'status') : array();
		}

		public function getActions()
		{
			$this->_populateActions();
			return $this->_actions;
		}
		
		public function validateFromRequest(TBGRequest $request)
		{
			$this->_request = $request;
			foreach ($this->getProperties() as $property)
			{
				if (!$request->hasParameter('set_' . $property))
				{
					$this->_validation_errors[$property] = true;
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
			$comment_body = $event->getParameter('comment') . "\n\n" . $this->_request->getParameter('comment_body', null, false);

			$comment = TBGComment::createNew($title, $comment_body, TBGContext::getUser()->getID(), $request->getParameter('comment_applies_id'), $request->getParameter('comment_applies_type'), $request->getParameter('comment_module'), $request->getParameter('comment_visibility'), 0, false);
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
			
			foreach ($this->getProperties() as $property)
			{
				switch ($property)
				{
					case 'status':
						$issue->setStatus($request->getParameter("{$property}_id"));
						break;
					case 'resolution':
						$issue->setResolution($request->getParameter("{$property}_id"));
						break;
				}
			}
			
			$issue->save();
		}

	}
