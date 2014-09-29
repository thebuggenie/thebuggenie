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
     *
     * @Table(name="TBGWorkflowTransitionValidationRulesTable")
     */
    class TBGWorkflowTransitionValidationRule extends TBGIdentifiableScopedClass
    {

        const RULE_MAX_ASSIGNED_ISSUES = 'max_assigned_issues';
        const RULE_STATUS_VALID = 'valid_status';
        const RULE_RESOLUTION_VALID = 'valid_resolution';
        const RULE_REPRODUCABILITY_VALID = 'valid_reproducability';
        const RULE_PRIORITY_VALID = 'valid_priority';
        const RULE_TEAM_MEMBERSHIP_VALID = 'valid_team';

        const CUSTOMFIELD_VALIDATE_PREFIX = 'customfield.validate_';

        /**
         * @Column(type="string", length=100, name="rule")
         */
        protected $_name = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_rule_value = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_pre_or_post;

        /**
         * The connected transition
         *
         * @var TBGWorkflowTransition
         * @Column(type="integer", length=10)
         * @Relates(class="TBGWorkflowTransition")
         */
        protected $_transition_id = null;

        /**
         * The associated workflow object
         *
         * @var TBGWorkflow
         * @Column(type="integer", length=10)
         * @Relates(class="TBGWorkflow")
         */
        protected $_workflow_id = null;

        public static function getAvailablePreValidationRules()
        {
            $initial_list = array();
            $i18n = TBGContext::getI18n();
            $initial_list[self::RULE_MAX_ASSIGNED_ISSUES] = $i18n->__('Max number of assigned issues');
            $initial_list[self::RULE_TEAM_MEMBERSHIP_VALID] = $i18n->__('User must be member of a certain team');

            $event = new TBGEvent('core', 'TBGWorkflowTransitionValidationRule::getAvailablePreValidationRules', null, array(), $initial_list);
            $event->trigger();

            return $event->getReturnList();
        }

        public static function getAvailablePostValidationRules()
        {
            $initial_list = array();
            $i18n = TBGContext::getI18n();
            $initial_list[self::RULE_PRIORITY_VALID] = $i18n->__('Validate specified priority');
            $initial_list[self::RULE_REPRODUCABILITY_VALID] = $i18n->__('Validate specified reproducability');
            $initial_list[self::RULE_RESOLUTION_VALID] = $i18n->__('Validate specified resolution');
            $initial_list[self::RULE_STATUS_VALID] = $i18n->__('Validate specified status');
            foreach (TBGCustomDatatype::getAll() as $key => $details)
            {
                $initial_list[self::CUSTOMFIELD_VALIDATE_PREFIX . $key] = $i18n->__('Validate specified %key', array('%key' => $key));
            }
            $initial_list[self::RULE_TEAM_MEMBERSHIP_VALID] = $i18n->__('Validate team membership of assignee');

            $event = new TBGEvent('core', 'TBGWorkflowTransitionValidationRule::getAvailablePostValidationRules', null, array(), $initial_list);
            $event->trigger();

            return $event->getReturnList();
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

        public function setTransition(TBGWorkflowTransition $transition)
        {
            $this->_transition_id = $transition;
        }

        public function getTransition()
        {
            return $this->_b2dbLazyload('_transition_id');
        }

        public function setPost()
        {
            $this->_pre_or_post = 'post';
        }

        public function setPre()
        {
            $this->_pre_or_post = 'pre';
        }

        public function isPreOrPost()
        {
            return $this->_pre_or_post;
        }

        public function isPre()
        {
            return (bool) ($this->_pre_or_post == 'pre');
        }

        public function isPost()
        {
            return (bool) ($this->_pre_or_post == 'post');
        }

        public function setRule($rule)
        {
            $this->_name = $rule;
        }

        public function getRule()
        {
            return $this->_name;
        }

        public function setRuleValue($rule_value)
        {
            $this->_rule_value = $rule_value;
        }

        public function getRuleValue()
        {
            return $this->_rule_value;
        }

        public function getRuleValueAsJoinedString()
        {
            $is_core = true;
            $is_custom = false;
            if ($this->_name == self::RULE_STATUS_VALID)
            {
                $fieldname = 'TBGStatus';
            }
            elseif ($this->_name == self::RULE_RESOLUTION_VALID)
            {
                $fieldname = 'TBGResolution';
            }
            elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID)
            {
                $fieldname = 'TBGReproducability';
            }
            elseif ($this->_name == self::RULE_PRIORITY_VALID)
            {
                $fieldname = 'TBGPriority';
            }
            elseif ($this->_name == self::RULE_TEAM_MEMBERSHIP_VALID)
            {
                $fieldname = 'TBGTeam';
            }
            else
            {
                $is_core = false;
                $is_custom = (bool) (strpos($this->_name, self::CUSTOMFIELD_VALIDATE_PREFIX) !== false);
            }
            if ($is_core || $is_custom)
            {
                $values = explode(',', $this->getRuleValue());
                if ($is_custom)
                {
                    $custom_field_key = substr($this->_name, strlen(self::CUSTOMFIELD_VALIDATE_PREFIX) - 1);
                    $custom_field = TBGCustomFieldsTable::getTable()->getByKey($custom_field_key);
                }
                $return_values = array();
                foreach ($values as $value)
                {
                    try
                    {
                        if ($is_core)
                        {
                            $field = $fieldname::getB2DBTable()->selectByID((int) $value);
                        }
                        elseif ($is_custom)
                        {
                            $field = TBGCustomFieldOptionsTable::getTable()->selectById((int) $value);
                        }
                        if ($field instanceof TBGIdentifiable)
                        {
                            $return_values[] = $field->getName();
                        }
                    }
                    catch (Exception $e) {}
                }
                return join(' / ', $return_values);
            }
            else
            {
                $event = new TBGEvent('core', 'TBGWorkflowTransitionValidationRule::getRuleValueAsJoinedString', $this);
                $event->triggerUntilProcessed();

                return $event->getReturnValue();
            }
        }

        public function isValueValid($value)
        {
            $is_core = true;
            $is_custom = false;
            if ($this->_name == self::RULE_STATUS_VALID)
            {
                $fieldname = 'TBGStatus';
            }
            elseif ($this->_name == self::RULE_RESOLUTION_VALID)
            {
                $fieldname = 'TBGResolution';
            }
            elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID)
            {
                $fieldname = 'TBGReproducability';
            }
            elseif ($this->_name == self::RULE_PRIORITY_VALID)
            {
                $fieldname = 'TBGPriority';
            }
            elseif ($this->_name == self::RULE_TEAM_MEMBERSHIP_VALID)
            {
                $fieldname = 'TBGTeam';
            }
            else
            {
                $is_core = false;
                $is_custom = (bool) (strpos($this->_name, self::CUSTOMFIELD_VALIDATE_PREFIX) !== false);
            }
            if ($is_core || $is_custom)
            {
                switch ($this->_name)
                {
                    case self::RULE_STATUS_VALID:
                    case self::RULE_RESOLUTION_VALID:
                    case self::RULE_REPRODUCABILITY_VALID:
                    case self::RULE_PRIORITY_VALID:
                    case self::RULE_TEAM_MEMBERSHIP_VALID:
                        $value = ($value instanceof $fieldname) ? $value->getID() : $value;
                        return ($this->getRuleValue()) ? in_array($value, explode(',', $this->getRuleValue())) : (bool) $value;
                        break;
                }
                return true;
            }
            else
            {
                $event = new TBGEvent('core', 'TBGWorkflowTransitionValidationRule::isValueValid', $this);
                $event->setReturnValue(false);
                $event->triggerUntilProcessed(array('value' => $value));

                return $event->getReturnValue();
            }
        }

        public function isValid($input)
        {
            switch ($this->_name)
            {
                case self::RULE_MAX_ASSIGNED_ISSUES:
                    $num_issues = (int) $this->getRuleValue();
                    return ($num_issues) ? (bool) (count(TBGContext::getUser()->getUserAssignedIssues()) < $num_issues) : true;
                    break;
                case self::RULE_TEAM_MEMBERSHIP_VALID:
                    $valid_items = explode(',', $this->getRuleValue());
                    $teams = TBGTeam::getAll();
                    if ($this->isPost())
                    {
                        if ($input instanceof TBGIssue)
                        {
                            $assignee = $input->getAssignee();
                        }
                    }
                    else
                    {
                        $assignee = TBGContext::getUser();
                    }
                    if ($assignee instanceof TBGUser)
                    {
                        foreach ($valid_items as $team_id)
                        {
                            if ($assignee->isMemberOfTeam($teams[$team_id]))
                                return true;
                        }
                    }
                    elseif ($assignee instanceof TBGTeam)
                    {
                        foreach ($valid_items as $team_id)
                        {
                            if ($assignee->getID() == $team_id)
                                return true;
                        }
                    }
                    return false;
                case self::RULE_STATUS_VALID:
                case self::RULE_PRIORITY_VALID:
                case self::RULE_RESOLUTION_VALID:
                case self::RULE_REPRODUCABILITY_VALID:
                    $valid_items = explode(',', $this->getRuleValue());
                    $valid = false;
                    foreach ($valid_items as $item)
                    {
                        if ($this->_name == self::RULE_STATUS_VALID)
                        {
                            $fieldname = 'Status';
                            $fieldname_small = 'status';
                        }
                        elseif ($this->_name == self::RULE_RESOLUTION_VALID)
                        {
                            $fieldname = 'Resolution';
                            $fieldname_small = 'resolution';
                        }
                        elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID)
                        {
                            $fieldname = 'Reproducability';
                            $fieldname_small = 'reproducability';
                        }
                        elseif ($this->_name == self::RULE_PRIORITY_VALID)
                        {
                            $fieldname = 'Priority';
                            $fieldname_small = 'priority';
                        }

                        if ($input instanceof TBGIssue)
                        {
                            $type = "TBG{$fieldname}";
                            $getter = "get{$fieldname}";
                            if (TBGContext::factory()->$type((int) $item)->getID() == $input->$getter()->getID())
                            {
                                $valid = true;
                                break;
                            }
                        }
                        elseif ($input instanceof TBGRequest)
                        {
                            if ($input->getParameter("{$fieldname_small}_id") == $item)
                            {
                                $valid = true;
                                break;
                            }
                        }
                    }
                    return $valid;
                    break;
                default:
                    if (strpos($this->_name, self::CUSTOMFIELD_VALIDATE_PREFIX) !== false)
                    {

                    }
                    else
                    {
                        $event = new TBGEvent('core', 'TBGWorkflowTransitionValidationRule::isValid', $this);
                        $event->setReturnValue(false);
                        $event->triggerUntilProcessed(array('input' => $input));

                        return $event->getReturnValue();
                    }
            }
        }

    }
