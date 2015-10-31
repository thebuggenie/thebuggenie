<?php

    namespace thebuggenie\modules\vcs_integration\entities;

    use \thebuggenie\core\entities\Issue,
        thebuggenie\modules\vcs_integration\entities\tables\IssueLinks;

    /**
     * Issue to Commit link class, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * Issue to Commit link class, vcs_integration
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Table(name="\thebuggenie\modules\vcs_integration\entities\tables\IssueLinks")
     */
    class IssueLink extends \thebuggenie\core\entities\common\IdentifiableScoped
    {

        /**
         * Affected issue
         * @var \thebuggenie\core\entities\Issue
         * @Column(type="integer", name="issue_no")
         * @Relates(class="\thebuggenie\core\entities\Issue")
         */
        protected $_issue = null;

        /**
         * Associated commit
         * @var \thebuggenie\modules\vcs_integration\entities\Commit
         * @Column(type="integer", name="commit_id")
         * @Relates(class="\thebuggenie\modules\vcs_integration\entities\Commit")
         */
        protected $_commit = null;

        /**
         * Get the issue for this link
         * @return \thebuggenie\core\entities\Issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyload('_issue');
        }

        /**
         * Get the commit with this link
         * @return \thebuggenie\modules\vcs_integration\entities\Commit
         */
        public function getCommit()
        {
            return $this->_b2dbLazyload('_commit');
        }

        /**
         * Set the issue in this link
         * @param \thebuggenie\core\entities\Issue $issue
         */
        public function setIssue(\thebuggenie\core\entities\Issue $issue)
        {
            $this->_issue = $issue;
        }

        /**
         * Set the commit in this link
         * @param Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit = $commit;
        }

        /**
         * Return all commits for a given issue
         * @param \thebuggenie\core\entities\Issue $issue
         * @return array|\thebuggenie\core\entities\Issue
         */
        public static function getCommitsByIssue(\thebuggenie\core\entities\Issue $issue)
        {
            return tables\IssueLinks::getTable()->getByIssueID($issue->getID());
        }

        /**
         * Return all issues for a given commit
         * @param \thebuggenie\modules\vcs_integration\entities\Commit $commit
         * @return array
         */
        public static function getIssuesByCommit(Commit $commit)
        {
            return tables\IssueLinks::getTable()->getByCommitID($commit->getID());
        }

    }
