<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Link table between workflow and issue type
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Link table between workflow and issue type
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="workflow_issuetype")
     */
    class WorkflowIssuetype extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'workflow_issuetype';
        const ID = 'workflow_issuetype.id';
        const SCOPE = 'workflow_issuetype.scope';
        const WORKFLOW_SCHEME_ID = 'workflow_issuetype.workflow_scheme_id';
        const WORKFLOW_ID = 'workflow_issuetype.workflow_id';
        const ISSUETYPE_ID = 'workflow_issuetype.issutype_id';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::WORKFLOW_ID, Workflows::getTable());
            parent::_addForeignKeyColumn(self::WORKFLOW_SCHEME_ID, WorkflowSchemes::getTable());
            parent::_addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable());
        }

        public function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            foreach (IssueTypes::getTable()->getAllIDsByScopeID($scope->getID()) as $issuetype_id)
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::SCOPE, $scope->getID());
                $crit->addInsert(self::WORKFLOW_ID, \thebuggenie\core\framework\Settings::getCoreWorkflow()->getID());
                $crit->addInsert(self::WORKFLOW_SCHEME_ID, \thebuggenie\core\framework\Settings::getCoreWorkflowScheme()->getID());
                $crit->addInsert(self::ISSUETYPE_ID, $issuetype_id);
                $this->doInsert($crit);
            }
        }
        
        public function setWorkflowIDforIssuetypeIDwithSchemeID($workflow_id, $issuetype_id, $scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::WORKFLOW_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
            if ($res = $this->doSelect($crit))
            {
                if ($workflow_id)
                {
                    $crit->addUpdate(self::WORKFLOW_ID, $workflow_id);
                    $this->doUpdate($crit);
                }
                else
                {
                    $this->doDelete($crit);
                }
            }
            elseif ($workflow_id)
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $crit->addInsert(self::WORKFLOW_ID, $workflow_id);
                $crit->addInsert(self::WORKFLOW_SCHEME_ID, $scheme_id);
                $crit->addInsert(self::ISSUETYPE_ID, $issuetype_id);
                $this->doInsert($crit);
            }
        }

        public function countSchemesByWorkflowID($workflow_id)
        {
            $crit = $this->getCriteria();
            $crit->setDistinct();
            $crit->addSelectionColumn(self::WORKFLOW_SCHEME_ID);
            $crit->addWhere(self::WORKFLOW_ID, $workflow_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->doCount($crit);
        }

        public function countByWorkflowSchemeID($workflow_scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::WORKFLOW_SCHEME_ID, $workflow_scheme_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->doCount($crit);
        }

        public function deleteByWorkflowSchemeID($workflow_scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::WORKFLOW_SCHEME_ID, $workflow_scheme_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->doDelete($crit);
        }

        public function getByWorkflowSchemeID($workflow_scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::WORKFLOW_SCHEME_ID, $workflow_scheme_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $return_array = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $return_array[$row->get(self::ISSUETYPE_ID)] = new \thebuggenie\core\entities\Workflow($row->get(self::WORKFLOW_ID), $row);
                }
            }

            return $return_array;
        }

    }
