<?php

    namespace thebuggenie\modules\vcs_integration\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;
    use \thebuggenie\core\entities\Context,
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
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Entity(class="\thebuggenie\modules\vcs_integration\entities\IssueLink")
     * @Table(name="vcsintegration_issuelinks")
     */
    class IssueLinks extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'vcsintegration_issuelinks';
        const ID = 'vcsintegration_issuelinks.id';
        const SCOPE = 'vcsintegration_issuelinks.scope';
        const ISSUE_NO = 'vcsintegration_issuelinks.issue_no';
        const COMMIT_ID = 'vcsintegration_issuelinks.commit_id';

        protected function _setupIndexes()
        {
            $this->_addIndex('commit', self::COMMIT_ID);
            $this->_addIndex('issue', self::ISSUE_NO);
        }

        /**
         * Get all rows by commit ID
         * @param integer $id
         * @return \b2db\Row
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? \thebuggenie\core\framework\Context::getScope()->getID() : $scope;
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
         * @return \b2db\Row
         */
        public function getByIssueID($id, $scope = null, $limit = null, $offset = null)
        {
            $scope = ($scope === null) ? \thebuggenie\core\framework\Context::getScope()->getID() : $scope;
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
            $scope = ($scope === null) ? \thebuggenie\core\framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::ISSUE_NO, $id);

            return $this->count($query);
        }

    }
