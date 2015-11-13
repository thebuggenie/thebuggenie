<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Workflow transition validation rules table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Workflow transition validation rules table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method WorkflowTransitionValidationRules getTable() Return an instance of this table
     * @method WorkflowTransitionValidationRule selectById() Return a WorkflowTransitionValidationRule object
     *
     * @Table(name="workflow_transition_validation_rules")
     * @Entity(class="\thebuggenie\core\entities\WorkflowTransitionValidationRule")
     */
    class WorkflowTransitionValidationRules extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'workflow_transition_validation_rules';
        const ID = 'workflow_transition_validation_rules.id';
        const SCOPE = 'workflow_transition_validation_rules.scope';
        const RULE = 'workflow_transition_validation_rules.rule';
        const TRANSITION_ID = 'workflow_transition_validation_rules.transition_id';
        const WORKFLOW_ID = 'workflow_transition_validation_rules.workflow_id';
        const RULE_VALUE = 'workflow_transition_validation_rules.rule_value';
        const PRE_OR_POST = 'workflow_transition_validation_rules.pre_or_post';

        public function getByTransitionID($transition_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::TRANSITION_ID, $transition_id);
            
            $actions = array('pre' => array(), 'post' => array());
            if ($res = $this->select($crit, false))
            {
                foreach ($res as $rule)
                {
                    $actions[$rule->isPreOrPost()][$rule->getRule()] = $rule;
                }
            }
            
            return $actions;
        }

    }