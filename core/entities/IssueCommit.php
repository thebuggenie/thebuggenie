<?php

    namespace thebuggenie\core\entities;

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
     * @Table(name="\thebuggenie\core\entities\tables\IssueCommits")
     */
    class IssueCommit extends common\IdentifiableScoped
    {

        /**
         * Affected issue
         * @var Issue
         * @Column(type="integer", name="issue_no")
         * @Relates(class="\thebuggenie\core\entities\Issue")
         */
        protected $_issue = null;

        /**
         * Associated commit
         * @var Commit
         * @Column(type="integer", name="commit_id")
         * @Relates(class="\thebuggenie\core\entities\Commit")
         */
        protected $_commit = null;

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);

            if ($is_new) {
                $log_item = new LogItem();
                $log_item->setChangeType(LogItem::ACTION_ISSUE_UPDATE_COMMIT);
                $log_item->setTarget($this->getID());
                $log_item->setTargetType(LogItem::TYPE_ISSUE_COMMIT);
                $log_item->setProject($this->getCommit()->getProject());
                $log_item->setUser($this->getCommit()->getAuthor()->getID());
                $log_item->save();
            }
        }

        /**
         * Get the issue for this link
         * @return Issue
         */
        public function getIssue()
        {
            return $this->_b2dbLazyload('_issue');
        }

        /**
         * Get the commit with this link
         * @return Commit
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

    }
