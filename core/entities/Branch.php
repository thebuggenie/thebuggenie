<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\modules\livelink\Livelink;

    /**
     * Branch class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Branches")
     */
    class Branch extends common\IdentifiableScoped
    {

        /**
         * Branch name
         * @var string
         * @Column(type="string", length="200")
         */
        protected $_name;

        /**
         * Whether the branch is deleted
         *
         * @var boolean
         * @Column(type="boolean", default="false")
         */
        protected $_is_deleted = false;

        /**
         * Project
         * @var \thebuggenie\core\entities\Commit
         * @Relates(class="\thebuggenie\core\entities\Commit")
         * @Column(type="integer")
         */
        protected $_latest_commit_id = null;

        /**
         * Project
         * @var \thebuggenie\core\entities\Project
         * @Relates(class="\thebuggenie\core\entities\Project")
         * @Column(type="integer", name="project_id")
         */
        protected $_project = null;

        /**
         * @var Commit[]
         */
        protected $_commits;

        public static function getBranchNameFromRef($ref)
        {
            if (strpos($ref, 'refs/heads/') === 0) {
                return substr($ref, strrpos($ref, '/'));
            }

            return '';
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
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
         * Get the latest commit for this branch
         *
         * @return Commit
         */
        public function getLatestCommit()
        {
            return $this->_b2dbLazyload('_latest_commit_id');
        }

        /**
         * Get the latest commit for this branch
         *
         * @return int
         */
        public function getLatestCommitId()
        {
            if ($this->_latest_commit_id instanceof Commit) {
                return $this->_latest_commit_id->getID();
            } else {
                return $this->_latest_commit_id;
            }
        }

        /**
         * Set the latest commit on this branch
         *
         * @param Commit $commit
         */
        public function setLatestCommit(Commit $commit)
        {
            $this->_latest_commit_id = $commit;
        }

        public function getCommits(Commit $from_commit = null, $limit = 40)
        {
            if (!$from_commit instanceof Commit) {
                $from_commit = $this->getLatestCommit();
            }

            $this->_commits[] = $from_commit;
            $last_commit = $from_commit;
            while (count($this->_commits) < $limit) {
                if (!$last_commit->getPreviousCommit() instanceof Commit) {
                    break;
                }

                $this->_commits[] = $last_commit->getPreviousCommit();
                $last_commit = $last_commit->getPreviousCommit();
            }

            return $this->_commits;
        }

        /**
         * @return bool
         */
        public function isDeleted()
        {
            return $this->_is_deleted;
        }

        /**
         * @param bool $is_deleted
         */
        public function setIsDeleted($is_deleted = true)
        {
            $this->_is_deleted = $is_deleted;
        }

    }
