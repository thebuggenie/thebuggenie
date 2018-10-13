<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\IssueCommit;
    use \thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * B2DB Table, vcs_integration -> VCSIntegrationIssueLinksTable
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * B2DB Table, vcs_integration -> VCSIntegrationIssueLinksTable
     *
     * @method static IssueCommits getTable()
     *
     * @Entity(class="\thebuggenie\core\entities\IssueCommit")
     * @Table(name="issuecommits")
     */
    class IssueCommits extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'issuecommits';
        const ID = 'issuecommits.id';
        const SCOPE = 'issuecommits.scope';
        const ISSUE_NO = 'issuecommits.issue_no';
        const COMMIT_ID = 'issuecommits.commit_id';

        protected function _setupIndexes()
        {
            $this->_addIndex('commit', self::COMMIT_ID);
            $this->_addIndex('issue', self::ISSUE_NO);
        }

        /**
         * Get all rows by commit ID
         * @param integer $id
         * @return IssueCommit[]
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::COMMIT_ID, $id);

            return $this->select($crit);
        }

        /**
         * Get all rows by issue ID
         * @param integer $id
         * @param integer $scope
         * @param integer $limit
         * @param integer $offset
         * @return IssueCommit
         */
        public function getByIssueID($id, $limit = null, $offset = null, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::ISSUE_NO, $id);
            $crit->addOrderBy(Commits::DATE, Criteria::SORT_DESC);

            if ($limit !== null)
                $crit->setLimit($limit);

            if ($offset !== null)
                $crit->setOffset($offset);

            return $this->select($crit);
        }

        /**
         * Count all rows by issue ID
         * @param integer $id
         * @return integer
         */
        public function countByIssueID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::ISSUE_NO, $id);

            return $this->doCount($crit);
        }

    }
