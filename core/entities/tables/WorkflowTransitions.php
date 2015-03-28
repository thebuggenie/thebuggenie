<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Workflow transitions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Workflow transitions table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="workflow_transitions")
     * @Entity(class="\thebuggenie\core\entities\WorkflowTransition")
     */
    class WorkflowTransitions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'workflow_transitions';
        const ID = 'workflow_transitions.id';
        const SCOPE = 'workflow_transitions.scope';
        const WORKFLOW_ID = 'workflow_transitions.workflow_id';
        const NAME = 'workflow_transitions.name';
        const DESCRIPTION = 'workflow_transitions.description';
        const OUTGOING_STEP_ID = 'workflow_transitions.outgoing_step_id';
        const TEMPLATE = 'workflow_transitions.template';

        protected function _deleteByTypeID($type, $id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);
            return $this->doDelete($crit);
        }

        protected function _countByTypeID($type, $id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);
            return $this->doCount($crit);
        }

        protected function _getByTypeID($type, $id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere((($type == 'step') ? self::OUTGOING_STEP_ID : self::WORKFLOW_ID), $id);

            $return_array = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $return_array[$row->get(self::ID)] = new \thebuggenie\core\entities\WorkflowTransition($row->get(self::ID), $row);
                }
            }

            return $return_array;
        }

        public function countByStepID($step_id)
        {
            return $this->_countByTypeID('step', $step_id);
        }

        public function getByStepID($step_id)
        {
            return $this->_getByTypeID('step', $step_id);
        }

        public function countByWorkflowID($workflow_id)
        {
            return $this->_countByTypeID('workflow', $workflow_id);
        }

        public function getByWorkflowID($workflow_id)
        {
            return $this->_getByTypeID('workflow', $workflow_id);
        }
        
        public function reMapByWorkflowID($workflow_id, $mapper_array)
        {
            foreach ($mapper_array as $old_step_id => $new_step_id)
            {
                $crit = $this->getCriteria();
                $crit->addUpdate(self::OUTGOING_STEP_ID, $new_step_id);
                $crit->addWhere(self::OUTGOING_STEP_ID, $old_step_id);
                $crit->addWhere(self::WORKFLOW_ID, $workflow_id);
                $this->doUpdate($crit);
            }
        }

        public function upgradeFrom3dot1()
        {
            $wcrit = Settings::getTable()->getCriteria();
            $wcrit->addWhere(Settings::NAME, \thebuggenie\core\framework\Settings::SETTING_DEFAULT_WORKFLOW);

            $workflows = array();
            if ($res = Settings::getTable()->doSelect($wcrit))
            {
                while ($row = $res->getNextRow())
                {
                    $workflow_id = (int) $row->get(Settings::VALUE);
                    $workflows[$workflow_id] = $workflow_id;
                }
            }
            if (count($workflows))
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::NAME, '%reject%', \b2db\Criteria::DB_LIKE);
                $crit->addWhere(self::WORKFLOW_ID, $workflows, \b2db\Criteria::DB_IN);
                $crit->addUpdate(self::TEMPLATE, 'main/updateissueproperties');
                $this->doUpdate($crit);
            }
        }

    }