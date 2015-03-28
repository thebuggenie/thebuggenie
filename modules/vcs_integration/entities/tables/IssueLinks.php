<?php

    namespace thebuggenie\modules\vcs_integration\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;
    use \thebuggenie\core\entities\Context;

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
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::COMMIT_ID, $id);

            return $this->select($crit);
        }

        /**
         * Get all rows by issue ID
         * @param integer $id
         * @return \b2db\Row
         */
        public function getByIssueID($id, $scope = null)
        {
            $scope = ($scope === null) ? \thebuggenie\core\framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::ISSUE_NO, $id);

            return $this->select($crit);
        }

    }
