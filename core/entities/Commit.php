<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\helpers\TextParser,
        thebuggenie\modules\vcs_integration\Vcs_integration;
    use thebuggenie\core\modules\livelink\Livelink;

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
     * @Table(name="\thebuggenie\core\entities\tables\Commits")
     */
    class Commit extends common\IdentifiableScoped
    {

        /**
         * Commit log.
         * @var string
         * @Column(type="text")
         */
        protected $_log;

        /**
         * Revision number/hash of previous commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_old_rev;

        /**
         * Revision number/hash of this commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_new_rev;

        /**
         * Short revision number/hash of this commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_new_rev_short;

        /**
         * Commit author
         * @var User
         * @Relates(class="\thebuggenie\core\entities\User")
         * @Column(type="integer")
         */
        protected $_author;

        /**
         * POSIX timestamp of commit
         * @var integer
         * @Column(type="integer")
         */
        protected $_date;

        /**
         * Misc data
         * @var array
         * @Column(type="serializable", length=500)
         */
        protected $_data = [];

        /**
         * Affected files
         * @var CommitFile[]
         * @Relates(class="\thebuggenie\core\entities\CommitFile", collection=true, foreign_column="commit_id")
         */
        protected $_files;

        /**
         * Affected issues
         * @var array
         */
        protected $_issues;

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
                if (!$this->getAuthor() || ($this->getAuthor() instanceof User && $user->getID() == $this->getAuthor()->getID()) || (!$this->getAuthor() instanceof User && $user->getID() == $this->getAuthor()))
                    continue;

                $notification = new Notification();
                $notification->setTarget($this);
                $notification->setTriggeredByUser($this->getAuthor());
                $notification->setUser($user);
                $notification->setNotificationType(Livelink::NOTIFICATION_COMMIT_MENTIONED);
                $notification->setModuleName('livelink');
                $notification->save();
            }
        }

        protected function _preSave($is_new)
        {
            if ($is_new)
            {
                $this->_date = NOW;
            }
        }

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                $this->_addNotifications();
            }
        }

        protected function _parseMiscDataToArray() {
            if (is_null($this->_data)) return array();

            $array = array();
            $misc_data = explode('|', $this->_data);

            foreach ($misc_data as $data)
            {
                $key_value = explode(':', $data);

                if (count($key_value) == 2)
                {
                    $array[$key_value[0]] = $key_value[1];
                }
            }

            return $array;
        }

        protected function _parseMiscDataFromArray() {
            if (is_null($this->_data_array) || ! count($this->_data_array)) return null;

            $string = '';

            foreach ($this->_data_array as $key => $value)
            {
                $string .= "{$key}:{$value}|";
            }

            return rtrim($string, '|');
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
         * @return User
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
         * @return array
         */
        public function getMiscData()
        {
            return $this->_data;
        }

        /**
         * Get any other data for this comment, is parsed to array
         *
         * @return array
         */
        public function getMiscDataArray()
        {
            if (is_null($this->_data_array))
            {
                $this->_data_array = $this->_parseMiscDataToArray();
            }

            return $this->_data_array;
        }

        /**
         * Get an array of CommitFiles affected by this commit
         *
         * @return CommitFile[]
         */
        public function getFiles()
        {
            $this->_populateAffectedFiles();
            return $this->_files;
        }

        /**
         * Get an array of \thebuggenie\core\entities\Issues affected by this commit
         *
         * @return Issue[]
         */
        public function getIssues()
        {
            $this->_populateAffectedIssues();
            return $this->_issues;
        }

        /**
         * Set a new commit author
         *
         * @param User $user
         */
        public function setAuthor(User $user)
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
            if (!is_numeric($revno) && strlen($revno) > 7) {
                $this->_new_rev_short = substr($revno, 0, 7);
            } else {
                $this->_new_rev_short = $revno;
            }
        }

        public function getShortRevision()
        {
            return $this->_new_rev_short;
        }

        /**
         * Set misc data for this commit (see other docs)
         *
         * @param array $data
         */
        public function setMiscData($data)
        {
            $this->_data = $data;
        }

        /**
         * Set misc data array for this commit (see other docs)
         *
         * @param array $data
         */
        public function setMiscDataArray(array $data)
        {
            $this->_data_array = $data;
            $this->_data = $this->_parseMiscDataFromArray();
        }

        /**
         * Set the project this commit applies to
         *
         * @param Project $project
         */
        public function setProject(Project $project)
        {
            $this->_project = $project;
        }

        /**
         * Get the project this commit applies to
         *
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }

        private function _populateAffectedFiles()
        {
            if ($this->_files === null)
            {
                $this->_files = $this->_b2dbLazyload('_files');
            }
        }

        private function _populateAffectedIssues()
        {
            if ($this->_issues === null)
            {
                $issue_commits = tables\IssueCommits::getTable()->getByCommitID($this->_id);
                $issues = array();
                foreach ($issue_commits as $issue_commit) {
                    if ($issue_commit->getIssue() instanceof Issue) {
                        $issues[$issue_commit->getIssue()->getId()] = $issue_commit->getIssue();
                    }
                }
                $this->_issues = $issues;
            }
        }

        /**
         * Get Gitlab url for merge request bz provided id
         *
         * @param  integer $merge_request_id
         * @return string
         *
         * @throws \Exception
         */
        public function getGitlabUrlForMergeRequestID($merge_request_id)
        {
            $base_url = \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('browser_url_' . $this->getProject()->getID());
            $misc_data_array = $this->getMiscDataArray();
            $reposname = null;

            if (array_key_exists('gitlab_repos_ns', $misc_data_array))
            {
                $reposname = $misc_data_array['gitlab_repos_ns'];
                $base_url = rtrim($base_url, '/').'/'.$reposname;
            }

            return $base_url.'/merge_requests/'.$merge_request_id;
        }

    }
