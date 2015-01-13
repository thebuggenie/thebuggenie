<?php

    namespace thebuggenie\modules\vcs_integration\entities;

    use TBGTextParser,
        TBGNotification,
        TBGProject,
        TBGUser,
        thebuggenie\modules\vcs_integration\entities\b2db\Commits,
        thebuggenie\modules\vcs_integration\entities\b2db\Files,
        thebuggenie\modules\vcs_integration\entities\b2db\IssueLinks,
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
     * @Table(name="\thebuggenie\modules\vcs_integration\entities\b2db\Commits")
     */
    class Commit extends \TBGIdentifiableScopedClass
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
         * @var \TBGUser
         * @Relates(class="\TBGUser")
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
         * @var TBGProject
         * @Relates(class="\TBGProject")
         *  @Column(type="integer", name="project_id")
         */
        protected $_project = null;

        public function _addNotifications()
        {
            $parser = new TBGTextParser($this->_log);
            $parser->setOption('plain', true);
            $parser->doParse();
            foreach ($parser->getMentions() as $user)
            {
                if (!$this->getAuthor() || $user->getID() == $this->getAuthor())
                    continue;

                $notification = new TBGNotification();
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
         * @return string
         */
        public function getLog()
        {
            return $this->_log;
        }

        /**
         * Get this commit's revision number or hash
         * @return string/integer
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
         * @return string/integer
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
         * @return Vcs_integrationCommit
         */
        public function getPreviousCommit()
        {
            return Commits::getTable()->getCommitByCommitId($this->_old_rev, $this->getProject()->getID());
        }

        /**
         * Get the author of this commit
         * @return \TBGUser
         */
        public function getAuthor()
        {
            return $this->_author;
        }

        /**
         * Get the POSIX timestamp of this comment
         * @return integer
         */
        public function getDate()
        {
            return $this->_date;
        }

        /**
         * Get any other data for this comment, will need parsing
         * @return string
         */
        public function getMiscData()
        {
            return $this->_data;
        }

        /**
         * Get an array of Vcs_integrationFiles affected by this commit
         * @return array
         */
        public function getFiles()
        {
            $this->_populateAffectedFiles();
            return $this->_files;
        }

        /**
         * Get an array of TBGIssues affected by this commit
         * @return array|\TBGIssue
         */
        public function getIssues()
        {
            $this->_populateAffectedIssues();
            return $this->_issues;
        }

        /**
         * Get the project this commit applies to
         * @return \TBGProject
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }

        /**
         * Set a new commit author
         * @param \TBGUser $user
         */
        public function setAuthor(TBGUser $user)
        {
            $this->_author = $user;
        }

        /**
         * Set a new date for the commit
         * @param integer $date
         */
        public function setDate($date)
        {
            $this->_date = $date;
        }

        /**
         * Set a new log for the commit. This will not affect the issues which are affected
         * @param string $log
         */
        public function setLog($log)
        {
            $this->_log = $log;
        }

        /**
         * Set a new parent revision
         * @param integer $revno
         */
        public function setPreviousRevision($revno)
        {
            $this->_old_rev = $revno;
        }

        /**
         * Set THIS revisions revno
         * @param integer $revno
         */
        public function setRevision($revno)
        {
            $this->_new_rev = $revno;
        }

        /**
         * Set misc data for this commit (see other docs)
         * @param string $data
         */
        public function setMiscData($data)
        {
            $this->_data = $data;
        }

        /**
         * Set the project this commit applies to
         * @param \TBGProject $project
         */
        public function setProject(TBGProject $project)
        {
            $this->_project = $project;
        }

        private function _populateAffectedFiles()
        {
            if ($this->_files == null)
            {
                $this->_files = Files::getTable()->getByCommitID($this->_id);
            }
        }

        private function _populateAffectedIssues()
        {
            if ($this->_issues == null)
            {
                $this->_issues = IssueLinks::getTable()->getByCommitID($this->_id);
            }
        }

        /**
         * Get all commits relating to issues inside a project
         * @param integer $id
         * @param integer $limit
         * @param integer $offset
         *
         * @return array/false
         */
        public static function getByProject($id, $limit = 40, $offset = null)
        {
            $commits = Commits::getTable()->getCommitsByProject($id, $limit, $offset);
            return $commits;
        }

    }
