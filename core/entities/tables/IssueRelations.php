<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Issue relations table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issue relations table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuerelations")
     */
    class IssueRelations extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issuerelations';
        const ID = 'issuerelations.id';
        const SCOPE = 'issuerelations.scope';
        const PARENT_ID = 'issuerelations.parent_id';
        const CHILD_ID = 'issuerelations.child_id';
        const MUSTFIX = 'issuerelations.mustfix';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addBoolean(self::MUSTFIX);
            parent::_addForeignKeyColumn(self::PARENT_ID, Issues::getTable(), Issues::ID);
            parent::_addForeignKeyColumn(self::CHILD_ID, Issues::getTable(), Issues::ID);
        }

        public function getRelatedIssues($issue_id)
        {
            $crit = $this->getCriteria();
            $ctn = $crit->returnCriterion(self::PARENT_ID, $issue_id);
            $ctn->addOr(self::CHILD_ID, $issue_id);
            $crit->addWhere($ctn);
            $crit->addWhere(Issues::DELETED, 0);
            $res = $this->doSelect($crit);
            return $res;
        }

        public function getIssueRelation($this_issue_id, $related_issue_id)
        {
            $crit = $this->getCriteria();
            $ctn = $crit->returnCriterion(self::PARENT_ID, $this_issue_id);
            $ctn->addOr(self::CHILD_ID, $this_issue_id);
            $crit->addWhere($ctn);
            $ctn = $crit->returnCriterion(self::PARENT_ID, $related_issue_id);
            $ctn->addOr(self::CHILD_ID, $related_issue_id);
            $crit->addWhere($ctn);
            $crit->addWhere(Issues::DELETED, 0);
            $res = $this->doSelectOne($crit);
            return $res;
        }

        public function addParentIssue($issue_id, $parent_id)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::CHILD_ID, $issue_id);
            $crit->addInsert(self::PARENT_ID, $parent_id);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doInsert($crit);
            return $res;
        }

        public function removeParentIssue($issue_id, $parent_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::CHILD_ID, $issue_id);
            $crit->addWhere(self::PARENT_ID, $parent_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
            return $res;
        }

        public function addChildIssue($issue_id, $child_id)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::PARENT_ID, $issue_id);
            $crit->addInsert(self::CHILD_ID, $child_id);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doInsert($crit);
            return $res;
        }

        public function removeChildIssue($issue_id, $child_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PARENT_ID, $issue_id);
            $crit->addWhere(self::CHILD_ID, $child_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
            return $res;
        }

        public function countChildIssues($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PARENT_ID, $issue_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->count($crit);
        }

    }
