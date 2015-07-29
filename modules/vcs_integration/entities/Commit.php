<?php

    namespace thebuggenie\modules\vcs_integration\entities;

    use thebuggenie\core\entities\Issue;
    use thebuggenie\core\helpers\TextParser,
        \thebuggenie\core\entities\Notification,
        \thebuggenie\core\entities\Project,
        \thebuggenie\core\entities\User,
        thebuggenie\modules\vcs_integration\entities\tables\Commits,
        thebuggenie\modules\vcs_integration\entities\tables\Files,
        thebuggenie\modules\vcs_integration\entities\tables\IssueLinks,
        thebuggenie\modules\vcs_integration\Vcs_integration;

    /**
     * Commit class, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * Commit class, vcs_integration
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Table(name="\thebuggenie\modules\vcs_integration\entities\tables\Commits")
     */
    class Commit extends \thebuggenie\core\entities\common\IdentifiableScoped
    {

        /**
         * Commit log.
         * @var string
         * @Column(type="text")
         */
        protected $_log = null;

        /**
         * Revision number/hash of previous commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_old_rev = null;

        /**
         * Revision number/hash of this commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_new_rev = null;

        /**
         * Commit author
         * @var \thebuggenie\core\entities\User
         * @Relates(class="\thebuggenie\core\entities\User")
         * @Column(type="integer")
         */
        protected $_author = null;

        /**
         * POSIX timestamp of commit
         * @var integer
         * @Column(type="integer")
         */
        protected $_date = null;

        /**
         * Misc data
         * @var string
         * @Column(type="text")
         */
        protected $_data = null;

        /**
         * Affected files
         * @var array
         */
        protected $_files = null;

        /**
         * Affected issues
         * @var array
         */
        protected $_issues = null;

        /**
         * Project
         * @var \thebuggenie\core\entities\Project
         * @Relates(class="\thebuggenie\core\entities\Project")
         *  @Column(type="integer", name="project_id")
         */
        protected $_project = null;

        public function _addNotifications()
        {
            $parser = new TextParser($this->_log);
            $parser->setOption('plain', true);
            $parser->doParse();
            foreach ($parser->getMentions() as $user)
            {
                if (!$this->getAuthor() || $user->getID() == $this->getAuthor())
                    continue;

                $notification = new \thebuggenie\core\entities\Notification();
                $notification->setTarget($this);
                $notification->setTriggeredByUser($this->getAuthor());
                $notification->setUser($user);
                $notification->setNotificationType(Vcs_integration::NOTIFICATION_COMMIT_MENTIONED);
                $notification->setModuleName('vcs_integration');
                $notification->save();
            }
        }

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                $this->_addNotifications();
            }
        }

        /**
         * Get the commit log for this commit
         * 
         * @return string
         */
        public function getLog()
        {
            return $this->_log;
        }

        /**
         * Get this commit's revision number or hash
         * 
         * @return mixed
         */
        public function getRevision()
        {
            return $this->_new_rev;
        }

        public function getRevisionString()
        {
            return (!is_numeric($this->getRevision())) ? mb_substr($this->getRevision(), 0, 7) : $this->getRevision();
        }

        /**
         * Get the preceeding commit's revision number or hash
         *
         * @return mixed
         */
        public function getPreviousRevision()
        {
            return $this->_old_rev;
        }

        public function getPreviousRevisionString()
        {
            return (!is_numeric($this->getPreviousRevision())) ? mb_substr($this->getPreviousRevision(), 0, 7) : $this->getPreviousRevision();
        }

        /**
         * Get the previous commit
         *
         * @return Commit
         */
        public function getPreviousCommit()
        {
            return tables\Commits::getTable()->getCommitByCommitId($this->_old_rev, $this->getProject()->getID());
        }

        /**
         * Get the author of this commit
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getAuthor()
        {
            return $this->_author;
        }

        /**
         * Get the POSIX timestamp of this comment
         *
         * @return integer
         */
        public function getDate()
        {
            return $this->_date;
        }

        /**
         * Get any other data for this comment, will need parsing
         *
         * @return string
         */
        public function getMiscData()
        {
            return $this->_data;
        }

        /**
         * Get an array of Vcs_integrationFiles affected by this commit
         *
         * @return array
         */
        public function getFiles()
        {
            $this->_populateAffectedFiles();
            return $this->_files;
        }

        /**
         * Get an array of \thebuggenie\core\entities\Issues affected by this commit
         *
         * @return array|\thebuggenie\core\entities\Issue
         */
        public function getIssues()
        {
            $this->_populateAffectedIssues();
            return $this->_issues;
        }

        /**
         * Get the project this commit applies to
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }

        /**
         * Set a new commit author
         *
         * @param \thebuggenie\core\entities\User $user
         */
        public function setAuthor(\thebuggenie\core\entities\User $user)
        {
            $this->_author = $user;
        }

        /**
         * Set a new date for the commit
         *
         * @param integer $date
         */
        public function setDate($date)
        {
            $this->_date = $date;
        }

        /**
         * Set a new log for the commit. This will not affect the issues which are affected
         *
         * @param string $log
         */
        public function setLog($log)
        {
            $this->_log = $log;
        }

        /**
         * Set a new parent revision
         *
         * @param integer $revno
         */
        public function setPreviousRevision($revno)
        {
            $this->_old_rev = $revno;
        }

        /**
         * Set THIS revisions revno
         *
         * @param integer $revno
         */
        public function setRevision($revno)
        {
            $this->_new_rev = $revno;
        }

        /**
         * Set misc data for this commit (see other docs)
         *
         * @param string $data
         */
        public function setMiscData($data)
        {
            $this->_data = $data;
        }

        /**
         * Set the project this commit applies to
         *
         * @param \thebuggenie\core\entities\Project $project
         */
        public function setProject(\thebuggenie\core\entities\Project $project)
        {
            $this->_project = $project;
        }

        private function _populateAffectedFiles()
        {
            if ($this->_files === null)
            {
                $this->_files = tables\Files::getTable()->getByCommitID($this->_id);
            }
        }

        private function _populateAffectedIssues()
        {
            if ($this->_issues === null)
            {
                $issuelinks = tables\IssueLinks::getTable()->getByCommitID($this->_id);
                $issues = array();
                foreach ($issuelinks as $issuelink) {
                    if ($issuelink->getIssue() instanceof Issue) {
                        $issues[$issuelink->getIssue()->getId()] = $issuelink->getIssue();
                    }
                }
                $this->_issues = $issues;
            }
        }

        /**
         * Get all commits relating to issues inside a project
         *
         * @param integer $id
         * @param integer $limit
         * @param integer $offset
         *
         * @return mixed
         */
        public static function getByProject($id, $limit = 40, $offset = null)
        {
            $commits = tables\Commits::getTable()->getCommitsByProject($id, $limit, $offset);
            return $commits;
        }

    }
