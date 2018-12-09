<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\IssueCommit;
    use \thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Issue commits table
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

        protected function setupIndexes()
        {
            $this->addIndex('commit', self::COMMIT_ID);
            $this->addIndex('issue', self::ISSUE_NO);
        }

        /**
         * Get all rows by commit ID
         * @param integer $id
         * @return IssueCommit[]
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::COMMIT_ID, $id);

            return $this->select($query);
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
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::ISSUE_NO, $id);
            $query->addOrderBy(Commits::DATE, \b2db\QueryColumnSort::SORT_DESC);

            if ($limit !== null)
                $query->setLimit($limit);

            if ($offset !== null)
                $query->setOffset($offset);

            return $this->select($query);
        }

        /**
         * Count all rows by issue ID
         * @param integer $id
         * @return integer
         */
        public function countByIssueID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::ISSUE_NO, $id);

            return $this->count($query);
        }

    }
