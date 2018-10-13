<?php

    namespace thebuggenie\core\entities;

    /**
     * File class, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * File class, vcs_integration
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\CommitFiles")
     */
    class CommitFile extends common\IdentifiableScoped
    {

        const ACTION_ADDED = 'A';
        const ACTION_UPDATED = 'U';
        const ACTION_MODIFIED = 'M';
        const ACTION_DELETED = 'D';

        /**
         * File path
         * @var string
         * @Column(type="text")
         */
        protected $_file_name = null;

        /**
         * Action applied to file (Added, Updated or Deleted)
         * @var string
         * @Column(type="string", length=1)
         */
        protected $_action = null;

        /**
         * Associated commit
         * @var Commit
         *
         * @Column(type="integer", name="commit_id")
         * @Relates(class="\thebuggenie\core\entities\Commit")
         */
        protected $_commit = null;

        /**
         * Get the file path
         * @return string
         */
        public function getFile()
        {
            return $this->_file_name;
        }

        /**
         * Get the file action
         * @return string
         */
        public function getAction()
        {
            return $this->_action;
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
         * Set the file path
         * @param string $file
         */
        public function setFile($file)
        {
            $this->_file_name = $file;
        }

        /**
         * Set the action applied (M/A/D)
         * @param string $action
         */
        public function setAction($action)
        {
            $this->_action = $action;
        }

        /**
         * Set the commit this change applies to
         * @param Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit = $commit;
        }

    }
