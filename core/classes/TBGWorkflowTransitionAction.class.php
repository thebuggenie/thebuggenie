<?php

	/**
	 * Workflow transition action class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow transition action class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflowTransitionAction extends TBGIdentifiableClass
	{
		
		const ACTION_ASSIGN_ISSUE_SELF = 'assign_self';
		const ACTION_ASSIGN_ISSUE = 'assign_user';
		const ACTION_CLEAR_ASSIGNEE = 'clear_assignee';
		const ACTION_SET_RESOLUTION = 'set_resolution';
		const ACTION_CLEAR_RESOLUTION = 'clear_resolution';
		const ACTION_SET_STATUS = 'set_status';
		const ACTION_SET_PRIORITY = 'set_priority';
		const ACTION_CLEAR_PRIORITY = 'clear_priority';
		const ACTION_SET_REPRODUCABILITY = 'set_reproducability';
		const ACTION_CLEAR_REPRODUCABILITY = 'clear_reproducability';
		
		static protected $_b2dbtablename = 'TBGWorkflowTransitionActionsTable';
		
		protected $_action_type = null;

		protected $_target_value = null;
		
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

		public static function getByTransitionID($transition_id)
		{
			$actions = array();
			if ($res = TBGWorkflowTransitionActionsTable::getTable()->getByTransitionID($transition_id))
			{
				while ($row = $res->getNextRow())
				{
					$action = TBGContext::factory()->TBGWorkflowTransitionAction($row->get(TBGWorkflowTransitionActionsTable::ID), $row);
					$actions[$action->getActionType()] = $action;
				}
			}
			
			return $actions;
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

		public function setTransition(TBGWorkflowTransition $transition)
		{
			$this->_transition_id = $transition;
		}
		
		public function getTransition()
		{
			return $this->_getPopulatedObjectFromProperty('_transition_id');
		}

		public function setActionType($action_type)
		{
			$this->_action_type = $action_type;
		}
		
		public function getActionType()
		{
			return $this->_action_type;
		}
		
		public function setTargetValue($target_value)
		{
			$this->_target_value = $target_value;
		}
		
		public function getTargetValue()
		{
			return $this->_target_value;
		}
		
		public function perform(TBGIssue $issue, $request = null)
		{
			switch ($this->_action_type)
			{
				case self::ACTION_ASSIGN_ISSUE_SELF:
					$issue->setAssignee(TBGContext::getUser());
					break;
				case self::ACTION_SET_STATUS:
					if ($this->getTargetValue())
						$issue->setStatus(TBGContext::factory()->TBGStatus((int) $this->getTargetValue()));
					else
						$issue->setStatus($request->getParameter('status_id'));
					break;
				case self::ACTION_CLEAR_PRIORITY:
					$issue->setPriority(null);
					break;
				case self::ACTION_SET_PRIORITY:
					if ($this->getTargetValue())
						$issue->setPriority(TBGContext::factory()->TBGPriority((int) $this->getTargetValue()));
					else
						$issue->setPriority($request->getParameter('priority_id'));
					break;
				case self::ACTION_CLEAR_RESOLUTION:
					$issue->setResolution(null);
					break;
				case self::ACTION_SET_RESOLUTION:
					if ($this->getTargetValue())
						$issue->setResolution(TBGContext::factory()->TBGResolution((int) $this->getTargetValue()));
					else
						$issue->setResolution($request->getParameter('resolution_id'));
					break;
				case self::ACTION_CLEAR_REPRODUCABILITY:
					$issue->setReproducability(null);
					break;
				case self::ACTION_SET_REPRODUCABILITY:
					if ($this->getTargetValue())
						$issue->setReproducability(TBGContext::factory()->TBGReproducability((int) $this->getTargetValue()));
					else
						$issue->setReproducability($request->getParameter('reproducability_id'));
					break;
				case self::ACTION_CLEAR_ASSIGNEE:
					$issue->unsetAssignee();
					break;
				case self::ACTION_ASSIGN_ISSUE:
					if ($this->getTargetValue())
						$issue->setAssignee(TBGContext::factory()->TBGUser((int) $this->getTargetValue()));
					else
						$issue->setAssignee(TBGContext::factory()->TBGUser((int) $request->getParameter('assignee_id')));
					break;
			}
		}

	}
