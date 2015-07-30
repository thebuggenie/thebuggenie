<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Workflow transition action class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Workflow transition action class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\WorkflowTransitionActions")
     */
    class WorkflowTransitionAction extends IdentifiableScoped
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
        const ACTION_CLEAR_MILESTONE = 'clear_milestone';
        const ACTION_SET_PRIORITY = 'set_priority';
        const ACTION_CLEAR_PRIORITY = 'clear_priority';
        const ACTION_SET_PERCENT = 'set_percent';
        const ACTION_CLEAR_PERCENT = 'clear_percent';
        const ACTION_SET_REPRODUCABILITY = 'set_reproducability';
        const ACTION_CLEAR_REPRODUCABILITY = 'clear_reproducability';
        const ACTION_USER_START_WORKING = 'user_start_working';
        const ACTION_USER_STOP_WORKING = 'user_stop_working';

        const CUSTOMFIELD_CLEAR_PREFIX = 'customfield_clear_';
        const CUSTOMFIELD_SET_PREFIX = 'customfield_set_';

        /**
         * @Column(type="string", length=200)
         */
        protected $_action_type;

        /**
         * @Column(type="string", length=200)
         */
        protected $_target_value = null;

        /**
         * The connected transition
         *
         * @var \thebuggenie\core\entities\WorkflowTransition
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\WorkflowTransition")
         */
        protected $_transition_id = null;

        /**
         * The associated workflow object
         *
         * @var \thebuggenie\core\entities\Workflow
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Workflow")
         */
        protected $_workflow_id = null;

        protected static $_available_actions = null;

        protected static function _populateAvailableActions()
        {
            if (self::$_available_actions === null)
            {
                $initial_list = array('special' => array(), 'set' => array(), 'clear' => array());
                $i18n = framework\Context::getI18n();
                $initial_list['special'][self::ACTION_ASSIGN_ISSUE] = $i18n->__('Assign the issue to a user');
                $initial_list['special'][self::ACTION_ASSIGN_ISSUE_SELF] = $i18n->__('Assign the issue to the current user');
                $initial_list['special'][self::ACTION_CLEAR_DUPLICATE] = $i18n->__('Mark as not duplicate');
                $initial_list['special'][self::ACTION_SET_DUPLICATE] = $i18n->__('Possibly mark as duplicate');
                $initial_list['special'][self::ACTION_USER_START_WORKING] = $i18n->__('Start logging time');
                $initial_list['special'][self::ACTION_USER_STOP_WORKING] = $i18n->__('Stop logging time and optionally add time spent');
                $initial_list['clear'][self::ACTION_CLEAR_ASSIGNEE] = $i18n->__('Clear issue assignee');
                $initial_list['clear'][self::ACTION_CLEAR_PRIORITY] = $i18n->__('Clear issue priority');
                $initial_list['clear'][self::ACTION_CLEAR_PERCENT] = $i18n->__('Clear issue percent');
                $initial_list['clear'][self::ACTION_CLEAR_REPRODUCABILITY] = $i18n->__('Clear issue reproducability');
                $initial_list['clear'][self::ACTION_CLEAR_RESOLUTION] = $i18n->__('Clear issue resolution');
                $initial_list['clear'][self::ACTION_CLEAR_MILESTONE] = $i18n->__('Clear issue milestone');
                $initial_list['set'][self::ACTION_SET_PRIORITY] = $i18n->__('Set issue priority');
                $initial_list['set'][self::ACTION_SET_PERCENT] = $i18n->__('Set issue percent');
                $initial_list['set'][self::ACTION_SET_REPRODUCABILITY] = $i18n->__('Set issue reproducability');
                $initial_list['set'][self::ACTION_SET_RESOLUTION] = $i18n->__('Set issue resolution');
                $initial_list['set'][self::ACTION_SET_STATUS] = $i18n->__('Set issue status');
                $initial_list['set'][self::ACTION_SET_MILESTONE] = $i18n->__('Set issue milestone');
                foreach (CustomDatatype::getAll() as $key => $details)
                {
                    $initial_list['clear'][self::CUSTOMFIELD_CLEAR_PREFIX . $key] = $i18n->__('Clear issue field %key', array('%key' => $key));
                    $initial_list['set'][self::CUSTOMFIELD_SET_PREFIX . $key] = $i18n->__('Set issue field %key', array('%key' => $key));
                }

                $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionAction::getAvailableTransitionActions', null, array(), $initial_list);
                $event->trigger();

                self::$_available_actions = $event->getReturnList();
            }
        }

        public static function getAvailableTransitionActions($key)
        {
            self::_populateAvailableActions();
            return self::$_available_actions[$key];
        }

        public static function getByTransitionID($transition_id)
        {
            $actions = array();
            if ($actions_array = tables\WorkflowTransitionActions::getTable()->getByTransitionID($transition_id))
            {
                foreach ($actions_array as $action)
                {
                    $actions[$action->getActionType()] = $action;
                }
            }

            return $actions;
        }

        /**
         * Return the workflow
         *
         * @return \thebuggenie\core\entities\Workflow
         */
        public function getWorkflow()
        {
            return $this->_b2dbLazyload('_workflow_id');
        }

        public function setWorkflow(\thebuggenie\core\entities\Workflow $workflow)
        {
            $this->_workflow_id = $workflow;
        }

        public function setTransition(\thebuggenie\core\entities\WorkflowTransition $transition)
        {
            $this->_transition_id = $transition;
        }

        public function getTransition()
        {
            return $this->_b2dbLazyload('_transition_id');
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

        public function perform(\thebuggenie\core\entities\Issue $issue, $request = null)
        {
            switch ($this->_action_type)
            {
                case self::ACTION_ASSIGN_ISSUE_SELF:
                    $issue->setAssignee(framework\Context::getUser());
                    break;
                case self::ACTION_SET_STATUS:
                    if ($this->getTargetValue())
                        $issue->setStatus(Status::getB2DBTable()->selectById((int) $this->getTargetValue()));
                    else
                        $issue->setStatus($request['status_id']);
                    break;
                case self::ACTION_CLEAR_MILESTONE:
                    $issue->setMilestone(null);
                    break;
                case self::ACTION_SET_MILESTONE:
                    if ($this->getTargetValue())
                        $issue->setMilestone(Milestone::getB2DBTable()->selectById((int) $this->getTargetValue()));
                    else
                        $issue->setMilestone($request['milestone_id']);
                    break;
                case self::ACTION_CLEAR_PRIORITY:
                    $issue->setPriority(null);
                    break;
                case self::ACTION_SET_PRIORITY:
                    if ($this->getTargetValue())
                        $issue->setPriority(Priority::getB2DBTable()->selectById((int) $this->getTargetValue()));
                    else
                        $issue->setPriority($request['priority_id']);
                    break;
                case self::ACTION_CLEAR_PERCENT:
                    $issue->setPercentCompleted(0);
                    break;
                case self::ACTION_SET_PERCENT:
                    if ($this->getTargetValue())
                        $issue->setPercentCompleted((int) $this->getTargetValue());
                    else
                        $issue->setPercentCompleted((int) $request['percent_complete_id']);
                    break;
                case self::ACTION_CLEAR_DUPLICATE:
                    $issue->setDuplicateOf(null);
                    break;
                case self::ACTION_SET_DUPLICATE:
                    $issue->setDuplicateOf($request['duplicate_issue_id']);
                    break;
                case self::ACTION_CLEAR_RESOLUTION:
                    $issue->setResolution(null);
                    break;
                case self::ACTION_SET_RESOLUTION:
                    if ($this->getTargetValue())
                        $issue->setResolution(Resolution::getB2DBTable()->selectById((int) $this->getTargetValue()));
                    else
                        $issue->setResolution($request['resolution_id']);
                    break;
                case self::ACTION_CLEAR_REPRODUCABILITY:
                    $issue->setReproducability(null);
                    break;
                case self::ACTION_SET_REPRODUCABILITY:
                    if ($this->getTargetValue())
                        $issue->setReproducability(Reproducability::getB2DBTable()->selectById((int) $this->getTargetValue()));
                    else
                        $issue->setReproducability($request['reproducability_id']);
                    break;
                case self::ACTION_CLEAR_ASSIGNEE:
                    $issue->clearAssignee();
                    break;
                case self::ACTION_ASSIGN_ISSUE:
                    if ($this->getTargetValue())
                    {
                        $target_details = explode('_', $this->_target_value);
                        if ($target_details[0] == 'user')
                        {
                            $assignee = \thebuggenie\core\entities\User::getB2DBTable()->selectById((int) $target_details[1]);
                        }
                        else
                        {
                            $assignee = Team::getB2DBTable()->selectById((int) $target_details[1]);
                        }
                        $issue->setAssignee($assignee);
                    }
                    else
                    {
                        $assignee = null;
                        switch ($request['assignee_type'])
                        {
                            case 'user':
                                $assignee = \thebuggenie\core\entities\User::getB2DBTable()->selectById((int) $request['assignee_id']);
                                break;
                            case 'team':
                                $assignee = Team::getB2DBTable()->selectById((int) $request['assignee_id']);
                                break;
                        }
                        if ((bool) $request->getParameter('assignee_teamup', false) && $assignee instanceof \thebuggenie\core\entities\User && $assignee->getID() != framework\Context::getUser()->getID())
                        {
                            $team = new \thebuggenie\core\entities\Team();
                            $team->setName($assignee->getBuddyname() . ' & ' . framework\Context::getUser()->getBuddyname());
                            $team->setOndemand(true);
                            $team->save();
                            $team->addMember($assignee);
                            $team->addMember(framework\Context::getUser());
                            $assignee = $team;
                        }
                        $issue->setAssignee($assignee);
                    }
                    break;
                case self::ACTION_USER_START_WORKING:
                    $issue->clearUserWorkingOnIssue();
                    if ($issue->getAssignee() instanceof \thebuggenie\core\entities\Team && $issue->getAssignee()->isOndemand())
                    {
                        $members = $issue->getAssignee()->getMembers();
                        $issue->startWorkingOnIssue(array_shift($members));
                    }
                    elseif ($issue->getAssignee() instanceof \thebuggenie\core\entities\User)
                    {
                        $issue->startWorkingOnIssue($issue->getAssignee());
                    }
                    break;
                case self::ACTION_USER_STOP_WORKING:
                    if ($request->getParameter('did', 'nothing') == 'nothing')
                    {
                        $issue->clearUserWorkingOnIssue();
                    }
                    elseif ($request->getParameter('did', 'nothing') == 'this')
                    {
                        $times = array();
                        if ($request['timespent_manual'])
                        {
                            $times = Issue::convertFancyStringToTime($request['timespent_manual']);
                        }
                        elseif ($request['timespent_specified_type'])
                        {
                            $times = array('points' => 0, 'hours' => 0, 'days' => 0, 'weeks' => 0, 'months' => 0);
                            $times[$request['timespent_specified_type']] = $request['timespent_specified_value'];
                        }
                        if (array_sum($times) > 0)
                        {
                            $times['hours'] *= 100;
                            $spenttime = new \thebuggenie\core\entities\IssueSpentTime();
                            $spenttime->setIssue($issue);
                            $spenttime->setUser(framework\Context::getUser());
                            $spenttime->setSpentPoints($times['points']);
                            $spenttime->setSpentHours($times['hours']);
                            $spenttime->setSpentDays($times['days']);
                            $spenttime->setSpentWeeks($times['weeks']);
                            $spenttime->setSpentMonths($times['months']);
                            $spenttime->setActivityType($request['timespent_activitytype']);
                            $spenttime->setComment($request['timespent_comment']);
                            $spenttime->save();
                        }
                        $issue->clearUserWorkingOnIssue();
                    }
                    else
                    {
                        $issue->stopWorkingOnIssue();
                    }
                    break;
                default:
                    if (strpos($this->_action_type, self::CUSTOMFIELD_CLEAR_PREFIX) === 0)
                    {
                        $customkey = substr($this->_action_type, strlen(self::CUSTOMFIELD_CLEAR_PREFIX));
                        $issue->setCustomField($customkey, null);
                    }
                    elseif (strpos($this->_action_type, self::CUSTOMFIELD_SET_PREFIX) === 0)
                    {
                        $customkey = substr($this->_action_type, strlen(self::CUSTOMFIELD_SET_PREFIX));

                        if ($this->getTargetValue())
                            $issue->setCustomField($customkey, $this->getTargetValue());
                        else
                            $issue->setCustomField($customkey, $request[$customkey . '_id']);
                    }
                    else
                    {
                        $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionAction::perform', $issue, array('request' => $request));
                        $event->triggerUntilProcessed();
                    }
            }
        }

        public function hasValidTarget()
        {
            if (!$this->_target_value) return true;

            switch ($this->_action_type)
            {
                case self::ACTION_ASSIGN_ISSUE:
                    $target_details = explode('_', $this->_target_value);
                    return (bool) ($target_details[0] == 'user') ? \thebuggenie\core\entities\User::doesIDExist($target_details[1]) : Team::doesIDExist($target_details[1]);
                    break;
                case self::ACTION_SET_PERCENT:
                    return (bool) ($this->_target_value > -1);
                    break;
                case self::ACTION_SET_MILESTONE:
                    return (bool) Milestone::doesIDExist($this->_target_value);
                    break;
                case self::ACTION_SET_PRIORITY:
                    return (bool) Priority::has($this->_target_value);
                    break;
                case self::ACTION_SET_STATUS:
                    return (bool) Status::has($this->_target_value);
                    break;
                case self::ACTION_SET_REPRODUCABILITY:
                    return (bool) Reproducability::has($this->_target_value);
                    break;
                case self::ACTION_SET_RESOLUTION:
                    return (bool) Resolution::has($this->_target_value);
                    break;
                default:
                    return true;
            }
        }

        public function getCustomActionType()
        {
            $prefix = $this->isCustomAction(true);

            if (is_null($prefix)) return null;

            return substr($this->_action_type, strlen($prefix));
        }

        public function isCustomClearAction($only_prefix = false)
        {
            return $this->isCustomAction($only_prefix, self::CUSTOMFIELD_CLEAR_PREFIX);
        }

        public function isCustomSetAction($only_prefix = false)
        {
            return $this->isCustomAction($only_prefix, self::CUSTOMFIELD_SET_PREFIX);
        }

        public function isCustomAction($only_prefix = false, $prefixes = array())
        {
            $prefixes = count((array) $prefixes)
                ? (array) $prefixes
                : array(self::CUSTOMFIELD_CLEAR_PREFIX, self::CUSTOMFIELD_SET_PREFIX);

            foreach ($prefixes as $prefix) {
                if (substr(
                    $this->_action_type,
                    0,
                    strlen($prefix)
                ) == $prefix)
                {
                    return $only_prefix ? $prefix : true;
                }
            }

            return $only_prefix ? null : false;
        }

        public function isValid(\thebuggenie\core\framework\Request $request)
        {
            if ($this->_target_value) return true;

            switch ($this->_action_type)
            {
                case self::ACTION_ASSIGN_ISSUE:
                    return (bool) $request['assignee_type'] && $request['assignee_id'];
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
