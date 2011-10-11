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
		const ACTION_SET_DUPLICATE = 'set_duplicate';
		const ACTION_CLEAR_DUPLICATE = 'clear_duplicate';
		const ACTION_SET_RESOLUTION = 'set_resolution';
		const ACTION_CLEAR_RESOLUTION = 'clear_resolution';
		const ACTION_SET_STATUS = 'set_status';
		const ACTION_SET_MILESTONE = 'set_milestone';
		const ACTION_SET_PRIORITY = 'set_priority';
		const ACTION_CLEAR_PRIORITY = 'clear_priority';
		const ACTION_SET_PERCENT = 'set_percent';
		const ACTION_CLEAR_PERCENT = 'clear_percent';
		const ACTION_SET_REPRODUCABILITY = 'set_reproducability';
		const ACTION_CLEAR_REPRODUCABILITY = 'clear_reproducability';
		const ACTION_USER_START_WORKING = 'user_start_working';
		const ACTION_USER_STOP_WORKING = 'user_stop_working';
		
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
		
		public function hasTargetValue()
		{
			return (bool) $this->_target_value;
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
				case self::ACTION_SET_MILESTONE:
					if ($this->getTargetValue())
						$issue->setMilestone(TBGContext::factory()->TBGMilestone((int) $this->getTargetValue()));
					else
						$issue->setMilestone($request->getParameter('milestone_id'));
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
				case self::ACTION_CLEAR_PERCENT:
					$issue->setPercentCompleted(0);
					break;
				case self::ACTION_SET_PERCENT:
					if ($this->getTargetValue())
						$issue->setPercentCompleted((int) $this->getTargetValue());
					else
						$issue->setPercentCompleted((int) $request->getParameter('percent_complete_id'));
					break;
				case self::ACTION_CLEAR_DUPLICATE:
					$issue->setDuplicateOf(null);
					break;
				case self::ACTION_SET_DUPLICATE:
					$issue->setDuplicateOf($request->getParameter('duplicate_issue_id'));
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
					{
						$issue->setAssignee(TBGContext::factory()->TBGUser((int) $this->getTargetValue()));
					}
					else
					{
						$assignee = null;
						switch ($request->getParameter('assignee_type'))
						{
							case TBGIdentifiableClass::TYPE_USER:
								$assignee = TBGContext::factory()->TBGUser($request->getParameter('assignee_id'));
								break;
							case TBGIdentifiableClass::TYPE_TEAM:
								$assignee = TBGContext::factory()->TBGTeam($request->getParameter('assignee_id'));
								break;
						}
						if ((bool) $request->getParameter('assignee_teamup', false))
						{
							$team = new TBGTeam();
							$team->setName($assignee->getBuddyname() . ' & ' . TBGContext::getUser()->getBuddyname());
							$team->setOndemand(true);
							$team->save();
							$team->addMember($assignee);
							$team->addMember(TBGContext::getUser());
							$assignee = $team;
						}
						$issue->setAssignee($assignee);
					}
					break;
				case self::ACTION_USER_START_WORKING:
					$issue->clearUserWorkingOnIssue();
					if ($issue->getAssignee() instanceof TBGTeam && $issue->getAssignee()->isOndemand())
					{
						$members = $issue->getAssignee()->getMembers();
						$issue->startWorkingOnIssue(array_shift($members));
					}
					else
					{
						$issue->startWorkingOnIssue($issue->getAssignee());
					}
					break;
				case self::ACTION_USER_STOP_WORKING:
					if ($request->getParameter('did', 'nothing') == 'nothing')
					{
						$issue->clearUserWorkingOnIssue();
					}
					else
					{
						$issue->stopWorkingOnIssue();
					}
					break;
			}
		}
		
		public function isValid(TBGRequest $request)
		{
			if ($this->_target_value) return true;
			
			switch ($this->_action_type)
			{
				case self::ACTION_ASSIGN_ISSUE:
					return (bool) $request->getParameter('assignee_type') && $request->getParameter('assignee_id');
					break;
				case self::ACTION_SET_MILESTONE:
					return (bool) $request->hasParameter('milestone_id');
					break;
				case self::ACTION_SET_PRIORITY:
					return (bool) $request->hasParameter('priority_id');
					break;
				case self::ACTION_SET_STATUS:
					return (bool) $request->hasParameter('status_id');
					break;
				case self::ACTION_SET_REPRODUCABILITY:
					return (bool) $request->hasParameter('reproducability_id');
					break;
				case self::ACTION_SET_RESOLUTION:
					return (bool) $request->hasParameter('resolution_id');
					break;
				default:
					return true;
			}
		}

	}
