<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Workflow steps table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Workflow steps table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="workflow_steps")
     * @Entity(class="\thebuggenie\core\entities\WorkflowStep")
     */
    class WorkflowSteps extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'workflow_steps';
        const ID = 'workflow_steps.id';
        const SCOPE = 'workflow_steps.scope';
        const NAME = 'workflow_steps.name';
        const STATUS_ID = 'workflow_steps.status_id';
        const WORKFLOW_ID = 'workflow_steps.workflow_id';
        const CLOSED = 'workflow_steps.closed';
        const DESCRIPTION = 'workflow_steps.description';
        const EDITABLE = 'workflow_steps.editable';

        public function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $steps = array();
            $steps[] = array('name' => 'New', 'description' => 'A new issue, not yet handled', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('new')->getID(), 'editable' => true, 'is_closed' => false);
            $steps[] = array('name' => 'Investigating', 'description' => 'An issue that is being investigated, looked into or is by other means between new and unconfirmed state', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('investigating')->getID(), 'editable' => true, 'is_closed' => false);
            $steps[] = array('name' => 'Confirmed', 'description' => 'An issue that has been confirmed', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('confirmed')->getID(), 'editable' => false, 'is_closed' => false);
            $steps[] = array('name' => 'In progress', 'description' => 'An issue that is being adressed', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('beingworkedon')->getID(), 'editable' => false, 'is_closed' => false);
            $steps[] = array('name' => 'Ready for testing', 'description' => 'An issue that has been marked fixed and is ready for testing', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('readyfortesting/qa')->getID(), 'editable' => false, 'is_closed' => false);
            $steps[] = array('name' => 'Testing', 'description' => 'An issue where the proposed or implemented solution is currently being tested or approved', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('testing/qa')->getID(), 'editable' => false, 'is_closed' => false);
            $steps[] = array('name' => 'Rejected', 'description' => 'A closed issue that has been rejected', 'status_id' => \thebuggenie\core\entities\Status::getByKeyish('notabug')->getID(), 'editable' => false, 'is_closed' => true);
            $steps[] = array('name' => 'Closed', 'description' => 'A closed issue', 'status_id' => null, 'editable' => false, 'is_closed' => true);

            foreach ($steps as $step)
            {
                $insertion = new Insertion();
                $insertion->add(self::WORKFLOW_ID, 1);
                $insertion->add(self::SCOPE, $scope->getID());
                $insertion->add(self::NAME, $step['name']);
                $insertion->add(self::DESCRIPTION, $step['description']);
                $insertion->add(self::STATUS_ID, $step['status_id']);
                $insertion->add(self::CLOSED, $step['is_closed']);
                $insertion->add(self::EDITABLE, $step['editable']);
                $this->rawInsert($insertion);
            }
        }

        public function countByWorkflowID($workflow_id)
        {
            $query = $this->getQuery();
            $query->where(self::WORKFLOW_ID, $workflow_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query, false);
            return $row;
        }
        
        public function countByStatusID($status_id)
        {
            $query = $this->getQuery();
            $query->where(self::STATUS_ID, $status_id);
            
            return $this->count($query);
        }

        public function getAllByWorkflowSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->join(Workflows::getTable(), Workflows::ID, self::WORKFLOW_ID, array(), \b2db\Join::INNER);
            $query->join(WorkflowIssuetype::getTable(), WorkflowIssuetype::WORKFLOW_ID, self::WORKFLOW_ID, array(), \b2db\Join::INNER);
            $query->where(WorkflowIssuetype::WORKFLOW_SCHEME_ID, $scheme_id);
            $res = $this->rawSelect($query);

            $steps = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $step_id = $row->get(self::ID);
                    $steps[$step_id] = $step_id;
                }
            }

            return $steps;
        }

    }