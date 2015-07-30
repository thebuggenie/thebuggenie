<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\Identifiable;
    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\entities\tables\Builds;
    use thebuggenie\core\entities\tables\Clients;
    use thebuggenie\core\entities\tables\Components;
    use thebuggenie\core\entities\tables\Editions;
    use thebuggenie\core\entities\tables\ListTypes;
    use thebuggenie\core\entities\tables\Milestones;
    use thebuggenie\core\entities\tables\Teams;
    use thebuggenie\core\framework;

    /**
     * Workflow transition validation rule class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Workflow transition validation rule class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\WorkflowTransitionValidationRules")
     */
    class WorkflowTransitionValidationRule extends IdentifiableScoped
    {

        const RULE_MAX_ASSIGNED_ISSUES = 'max_assigned_issues';
        const RULE_STATUS_VALID = 'valid_status';
        const RULE_RESOLUTION_VALID = 'valid_resolution';
        const RULE_REPRODUCABILITY_VALID = 'valid_reproducability';
        const RULE_PRIORITY_VALID = 'valid_priority';
        const RULE_TEAM_MEMBERSHIP_VALID = 'valid_team';

        const CUSTOMFIELD_VALIDATE_PREFIX = 'customfield_validate_';

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

        public static function getAvailablePreValidationRules()
        {
            $initial_list = array();
            $i18n = framework\Context::getI18n();
            $initial_list[self::RULE_MAX_ASSIGNED_ISSUES] = $i18n->__('Max number of assigned issues');
            $initial_list[self::RULE_TEAM_MEMBERSHIP_VALID] = $i18n->__('User must be member of a certain team');

            $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionValidationRule::getAvailablePreValidationRules', null, array(), $initial_list);
            $event->trigger();

            return $event->getReturnList();
        }

        public static function getAvailablePostValidationRules()
        {
            $initial_list = array();
            $i18n = framework\Context::getI18n();
            $initial_list[self::RULE_PRIORITY_VALID] = $i18n->__('Validate specified priority');
            $initial_list[self::RULE_REPRODUCABILITY_VALID] = $i18n->__('Validate specified reproducability');
            $initial_list[self::RULE_RESOLUTION_VALID] = $i18n->__('Validate specified resolution');
            $initial_list[self::RULE_STATUS_VALID] = $i18n->__('Validate specified status');
            foreach (CustomDatatype::getAll() as $key => $details)
            {
                $initial_list[self::CUSTOMFIELD_VALIDATE_PREFIX . $key] = $i18n->__('Validate specified %key', array('%key' => $key));
            }
            $initial_list[self::RULE_TEAM_MEMBERSHIP_VALID] = $i18n->__('Validate team membership of assignee');

            $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionValidationRule::getAvailablePostValidationRules', null, array(), $initial_list);
            $event->trigger();

            return $event->getReturnList();
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
            $is_core = in_array($this->_name, array(self::RULE_STATUS_VALID, self::RULE_RESOLUTION_VALID, self::RULE_REPRODUCABILITY_VALID, self::RULE_PRIORITY_VALID, self::RULE_TEAM_MEMBERSHIP_VALID));
            $is_custom = $this->isCustom();
            $customtype = $this->getCustomType();

            if ($this->_name == self::RULE_STATUS_VALID)
            {
                $fieldname = '\thebuggenie\core\entities\Status';
            }
            elseif ($this->_name == self::RULE_RESOLUTION_VALID)
            {
                $fieldname = '\thebuggenie\core\entities\Resolution';
            }
            elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID)
            {
                $fieldname = '\thebuggenie\core\entities\Reproducability';
            }
            elseif ($this->_name == self::RULE_PRIORITY_VALID)
            {
                $fieldname = '\thebuggenie\core\entities\Priority';
            }
            elseif ($this->_name == self::RULE_TEAM_MEMBERSHIP_VALID)
            {
                $fieldname = '\thebuggenie\core\entities\Team';
            }

            if ($is_core || $is_custom)
            {
                $values = explode(',', $this->getRuleValue());
                if ($is_custom)
                {
                    $custom_field_key = substr($this->_name, strlen(self::CUSTOMFIELD_VALIDATE_PREFIX) - 1);
                    $custom_field = tables\CustomFields::getTable()->getByKey($custom_field_key);
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
                            switch ($customtype) {
                                case CustomDatatype::RADIO_CHOICE:
                                case CustomDatatype::DROPDOWN_CHOICE_TEXT:
                                    $field = tables\CustomFieldOptions::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::TEAM_CHOICE:
                                    $field = Teams::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::STATUS_CHOICE:
                                    $field = ListTypes::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::MILESTONE_CHOICE:
                                    $field = Milestones::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::CLIENT_CHOICE:
                                    $field = Clients::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::COMPONENTS_CHOICE:
                                    $field = Components::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::EDITIONS_CHOICE:
                                    $field = Editions::getTable()->selectById((int) $value);
                                    break;
                                case CustomDatatype::RELEASES_CHOICE:
                                    $field = Builds::getTable()->selectById((int) $value);
                                    break;
                            }
                        }
                        if ($field instanceof \thebuggenie\core\entities\common\Identifiable)
                        {
                            if ($field instanceof Milestone || $field instanceof Component || $field instanceof Edition || $field instanceof Build) {
                                $return_values[] = $field->getProject()->getName() . ' - ' . $field->getName();
                            } elseif ($field instanceof Status) {
                                $return_values[] = '<span class="status_badge" style="background-color: '.$field->getColor().'; color: '.$field->getTextColor().';">'.$field->getName().'</span>';
                            } else {
                                $return_values[] = $field->getName();
                            }
                        }
                    }
                    catch (\Exception $e) {}
                }
                return join(' / ', $return_values);
            }
            else
            {
                $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionValidationRule::getRuleValueAsJoinedString', $this);
                $event->triggerUntilProcessed();

                return $event->getReturnValue();
            }
        }

        public function isCustom()
        {
            return (bool) (strpos($this->_name, self::CUSTOMFIELD_VALIDATE_PREFIX) !== false);
        }

        /**
         * Returns the identifier key for the customfield used in the validation rule
         *
         * @return string
         */
        public function getCustomFieldname()
        {
            return substr($this->_name, strlen(self::CUSTOMFIELD_VALIDATE_PREFIX));
        }

        /**
         * Returns the custom field object used in the validation rule
         *
         * @return CustomDatatype
         */
        public function getCustomField()
        {
            return CustomDatatype::getByKey($this->getCustomFieldname());
        }

        /**
         * Returns the custom type for the custom field object used in the validation rule
         * 
         * @return string
         */
        public function getCustomType()
        {
            return ($this->isCustom()) ? $this->getCustomField()->getType() : '';
        }

        public function getRuleOptions()
        {
            if ($this->getRule() == \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID) {
                $options = \thebuggenie\core\entities\Status::getAll();
            } elseif ($this->getRule() == \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID) {
                $options = \thebuggenie\core\entities\Priority::getAll();
            } elseif ($this->getRule() == \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID) {
                $options = \thebuggenie\core\entities\Resolution::getAll();
            } elseif ($this->getRule() == \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID) {
                $options = \thebuggenie\core\entities\Reproducability::getAll();
            } elseif ($this->getRule() == \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID) {
                $options = \thebuggenie\core\entities\Team::getAll();
            } elseif ($this->isCustom()) {
                $options = $this->getCustomField()->getOptions();
            }

            return $options;
        }

        public function isValueValid($value)
        {
            $is_core = in_array($this->_name, array(self::RULE_STATUS_VALID, self::RULE_RESOLUTION_VALID, self::RULE_REPRODUCABILITY_VALID, self::RULE_PRIORITY_VALID, self::RULE_TEAM_MEMBERSHIP_VALID));
            $is_custom = $this->isCustom();

            if ($is_core || $is_custom)
            {
                $value = (is_object($value)) ? $value->getID() : $value;
                return ($this->getRuleValue()) ? in_array($value, explode(',', $this->getRuleValue())) : (bool) $value;
            }
            else
            {
                $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionValidationRule::isValueValid', $this);
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
                    return ($num_issues) ? (bool) (count(framework\Context::getUser()->getUserAssignedIssues()) < $num_issues) : true;
                    break;
                case self::RULE_TEAM_MEMBERSHIP_VALID:
                    $valid_items = explode(',', $this->getRuleValue());
                    $teams = \thebuggenie\core\entities\Team::getAll();
                    if ($this->isPost())
                    {
                        if ($input instanceof \thebuggenie\core\entities\Issue)
                        {
                            $assignee = $input->getAssignee();
                        }
                    }
                    else
                    {
                        $assignee = framework\Context::getUser();
                    }
                    if ($assignee instanceof \thebuggenie\core\entities\User)
                    {
                        foreach ($valid_items as $team_id)
                        {
                            if ($assignee->isMemberOfTeam($teams[$team_id]))
                                return true;
                        }
                    }
                    elseif ($assignee instanceof \thebuggenie\core\entities\Team)
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
                    if ($this->_name == self::RULE_STATUS_VALID) {
                        $fieldname = 'Status';
                        $fieldname_small = 'status';
                    } elseif ($this->_name == self::RULE_RESOLUTION_VALID) {
                        $fieldname = 'Resolution';
                        $fieldname_small = 'resolution';
                    } elseif ($this->_name == self::RULE_REPRODUCABILITY_VALID) {
                        $fieldname = 'Reproducability';
                        $fieldname_small = 'reproducability';
                    } elseif ($this->_name == self::RULE_PRIORITY_VALID) {
                        $fieldname = 'Priority';
                        $fieldname_small = 'priority';
                    } else {
                        throw new framework\exceptions\ConfigurationException(framework\Context::getI18n()->__('Invalid workflow validation rule: %rule_name', array('%rule_name' => $this->_name)));
                    }

                    if (!$this->getRuleValue()) {
                        if ($input instanceof \thebuggenie\core\entities\Issue) {
                            $getter = "get{$fieldname}";

                            if (is_object($input->$getter())) {
                                $valid = true;
                            }
                        } elseif ($input instanceof framework\Request) {
                            if ($input->getParameter("{$fieldname_small}_id") && Status::has($input->getParameter("{$fieldname_small}_id"))) {
                                $valid = true;
                            }
                        }
                    } else {
                        foreach ($valid_items as $item) {
                            if ($input instanceof \thebuggenie\core\entities\Issue) {
                                $type = "\\thebuggenie\\core\\entities\\{$fieldname}";
                                $getter = "get{$fieldname}";

                                if (is_object($input->$getter()) && $type::getB2DBTable()->selectByID((int) $item)->getID() == $input->$getter()->getID()) {
                                    $valid = true;
                                    break;
                                }
                            } elseif ($input instanceof framework\Request) {
                                if ($input->getParameter("{$fieldname_small}_id") == $item) {
                                    $valid = true;
                                    break;
                                }
                            }
                        }
                    }
                    return $valid;
                    break;
                default:
                    if ($this->isCustom()) {
                        switch ($this->getCustomType()) {
                            case CustomDatatype::RADIO_CHOICE:
                            case CustomDatatype::DROPDOWN_CHOICE_TEXT:
                            case CustomDatatype::TEAM_CHOICE:
                            case CustomDatatype::STATUS_CHOICE:
                            case CustomDatatype::MILESTONE_CHOICE:
                            case CustomDatatype::CLIENT_CHOICE:
                            case CustomDatatype::COMPONENTS_CHOICE:
                            case CustomDatatype::EDITIONS_CHOICE:
                            case CustomDatatype::RELEASES_CHOICE:
                                $valid_items = explode(',', $this->getRuleValue());
                                if ($input instanceof \thebuggenie\core\entities\Issue) {
                                    $value = $input->getCustomField($this->getCustomFieldname());
                                } elseif ($input instanceof framework\Request) {
                                    $value = $input->getParameter($this->getCustomFieldname() . "_id");
                                }

                                $valid = false;
                                if (!$this->getRuleValue()) {
                                    foreach ($this->getCustomField()->getOptions() as $item) {
                                        if ($item->getID() == $value) {
                                            $valid = true;
                                            break;
                                        }
                                    }
                                } else {
                                    foreach ($valid_items as $item) {
                                        if ($value instanceof Identifiable && $value->getID() == $item) {
                                            $valid = true;
                                            break;
                                        } elseif (is_numeric($value) && $value == $item) {
                                            $valid = true;
                                            break;
                                        }
                                    }
                                }

                                return $valid;
                                break;
                        }
                    } else {
                        $event = new \thebuggenie\core\framework\Event('core', 'WorkflowTransitionValidationRule::isValid', $this);
                        $event->setReturnValue(false);
                        $event->triggerUntilProcessed(array('input' => $input));

                        return $event->getReturnValue();
                    }
            }
        }

    }
