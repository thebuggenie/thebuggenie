<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Workflow step transitions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Workflow step transitions table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="workflow_step_transitions")
     */
    class WorkflowStepTransitions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'workflow_step_transitions';
        const ID = 'workflow_step_transitions.id';
        const SCOPE = 'workflow_step_transitions.scope';
        const FROM_STEP_ID = 'workflow_step_transitions.from_step_id';
        const TRANSITION_ID = 'workflow_step_transitions.transition_id';
        const WORKFLOW_ID = 'workflow_step_transitions.workflow_id';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::WORKFLOW_ID, Workflows::getTable(), Workflows::ID);
            parent::addForeignKeyColumn(self::FROM_STEP_ID, WorkflowSteps::getTable(), WorkflowSteps::ID);
            parent::addForeignKeyColumn(self::TRANSITION_ID, WorkflowTransitions::getTable(), WorkflowTransitions::ID);
        }

        protected function _deleteByTypeID($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where((($type == 'step') ? self::FROM_STEP_ID : self::TRANSITION_ID), $id);
            return $this->rawDelete($query);
        }

        protected function _countByTypeID($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where((($type == 'step') ? self::FROM_STEP_ID : self::TRANSITION_ID), $id);
            return $this->count($query);
        }

        protected function _getByTypeID($type, $id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where((($type == 'step') ? self::FROM_STEP_ID : self::TRANSITION_ID), $id);
            $query->join(WorkflowTransitions::getTable(), WorkflowTransitions::ID, self::TRANSITION_ID);

            $return_array = array();
            if ($res = $this->rawSelect($query, false))
            {
                while ($row = $res->getNextRow())
                {
                    if ($type == 'step')
                    {
                        $return_array[$row->get(self::TRANSITION_ID)] = new \thebuggenie\core\entities\WorkflowTransition($row->get(self::TRANSITION_ID));
                    }
                    else
                    {
                        $return_array[$row->get(self::FROM_STEP_ID)] = new \thebuggenie\core\entities\WorkflowStep($row->get(self::FROM_STEP_ID));
                    }
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

        public function countByTransitionID($transition_id)
        {
            return $this->_countByTypeID('transition', $transition_id);
        }

        public function getByTransitionID($transition_id)
        {
            return $this->_getByTypeID('transition', $transition_id);
        }

        public function addNew($from_step_id, $transition_id, $workflow_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::FROM_STEP_ID, $from_step_id);
            $insertion->add(self::TRANSITION_ID, $transition_id);
            $insertion->add(self::WORKFLOW_ID, $workflow_id);
            $this->rawInsert($insertion);
        }
        
        public function copyByWorkflowIDs($old_workflow_id, $new_workflow_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::WORKFLOW_ID, $old_workflow_id);
            
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $insertion = new Insertion();
                    $insertion->add(self::FROM_STEP_ID, $row->get(self::FROM_STEP_ID));
                    $insertion->add(self::SCOPE, $row->get(self::SCOPE));
                    $insertion->add(self::TRANSITION_ID, $row->get(self::TRANSITION_ID));
                    $insertion->add(self::WORKFLOW_ID, $new_workflow_id);
                    $this->rawInsert($insertion);
                }
            }
        }

        public function deleteByTransitionID($transition_id)
        {
            $this->_deleteByTypeID('transition', $transition_id);
        }

        public function deleteByStepID($step_id)
        {
            $this->_deleteByTypeID('step', $step_id);
        }

        public function deleteByTransitionAndStepID($transition_id, $step_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TRANSITION_ID, $transition_id);
            $query->where(self::FROM_STEP_ID, $step_id);
            return $this->rawDelete($query);
        }

        public function reMapStepIDsByWorkflowID($workflow_id, $mapper_array)
        {
            foreach ($mapper_array as $old_step_id => $new_step_id)
            {
                $query = $this->getQuery();
                $update = new Update();

                $update->add(self::FROM_STEP_ID, $new_step_id);

                $query->where(self::FROM_STEP_ID, $old_step_id);
                $query->where(self::WORKFLOW_ID, $workflow_id);

                $this->rawUpdate($update, $query);
            }
        }
        
        public function reMapTransitionIDsByWorkflowID($workflow_id, $mapper_array)
        {
            foreach ($mapper_array as $old_transition_id => $new_transition_id)
            {
                $query = $this->getQuery();
                $update = new Update();

                $update->add(self::TRANSITION_ID, $new_transition_id);

                $query->where(self::TRANSITION_ID, $old_transition_id);
                $query->where(self::WORKFLOW_ID, $workflow_id);

                $this->rawUpdate($update, $query);
            }
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope_fromstepid', array(self::SCOPE, self::FROM_STEP_ID));
        }

    }