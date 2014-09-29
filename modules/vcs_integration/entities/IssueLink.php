<?php

    namespace thebuggenie\modules\vcs_integration\entities;

    use TBGIssue,
        thebuggenie\modules\vcs_integration\entities\b2db\IssueLinks;

    /**
     * Issue to Commit link class, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * Issue to Commit link class, vcs_integration
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Table(name="\thebuggenie\modules\vcs_integration\entities\b2db\IssueLinks")
     */
    class IssueLink extends \TBGIdentifiableScopedClass
    {

        /**
         * Affected issue
         * @var \TBGIssue
         * @Column(type="integer", name="issue_no")
         * @Relates(class="\TBGIssue")
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
         * @return \TBGIssue
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
         * @param \TBGIssue $issue
         */
        public function setIssue(TBGIssue $issue)
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
         * @param \TBGIssue $issue
         * @return array|\TBGIssue
         */
        public static function getCommitsByIssue(TBGIssue $issue)
        {
            return IssueLinks::getTable()->getByIssueID($issue->getID());
        }

        /**
         * Return all issues for a given commit
         * @param \thebuggenie\modules\vcs_integration\entities\Commit $commit
         * @return array
         */
        public static function getIssuesByCommit(Commit $commit)
        {
            return IssueLinks::getTable()->getByCommitID($commit->getID());
        }

    }
