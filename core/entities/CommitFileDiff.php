<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\helpers\Diffable;

    /**
     * Commit file diff
     *
     * @package thebuggenie
     * @subpackage livelink
     *
     * @Table(name="\thebuggenie\core\entities\tables\CommitFileDiffs")
     */
    class CommitFileDiff extends common\IdentifiableScoped implements Diffable
    {

        /**
         * File path
         * @var string
         * @Column(type="text")
         */
        protected $_diff = null;

        /**
         * Start line (remove)
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_start_line_remove = 0;

        /**
         * Lines removed
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_lines_removed = 0;

        /**
         * Start line (add)
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_start_line_add = 0;

        /**
         * Lines added
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_lines_added = 0;

        /**
         * File path
         * @var string
         * @Column(type="string", length=300)
         */
        protected $_diff_header = null;

        /**
         * Associated commit file
         * @var CommitFile
         *
         * @Column(type="integer")
         * @Relates(class="\thebuggenie\core\entities\CommitFile")
         */
        protected $_commit_file_id = null;

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $header = '';
                $this->_diff = trim($this->_diff);
                preg_match('/^@@(.*)\n/m', $this->_diff, $header);
                if (isset($header[0])) {
                    $this->_diff_header = substr($header[0], 0, 299);
                    list ($remove_details, $add_details) = explode(' ', trim($header[1]));
                    $remove_details = trim($remove_details, ' -');
                    $remove_comma_pos = strpos($remove_details, ',');
                    $add_details = trim($add_details, ' +');
                    $add_comma_pos = strpos($add_details, ',');
                    $this->_start_line_remove = ($remove_comma_pos !== false) ? substr($remove_details, 0, $remove_comma_pos) : $remove_details;
                    $this->_start_line_add = ($add_comma_pos !== false) ? substr($add_details, 0, $add_comma_pos) : $add_details;
                    $this->_diff = substr($this->_diff, strpos($this->_diff, "\n"));
                    $lines = explode("\n", $this->_diff);
                    foreach ($lines as $line) {
                        $first_char = substr($line, 0, 1);
                        if ($first_char == '-') {
                            $this->_lines_removed += 1;
                        } elseif ($first_char == '+') {
                            $this->_lines_added += 1;
                        }
                    }
                }
            }
        }

        /**
         * Get the commit with this link
         * @return Commit
         */
        public function getCommitFile()
        {
            return $this->_b2dbLazyload('_commit_file_id');
        }

        /**
         * Set the commit file
         * @param CommitFile $commit_file
         */
        public function setCommitFile(CommitFile $commit_file)
        {
            $this->_commit_file_id = $commit_file;
        }

        /**
         * @return string
         */
        public function getDiff()
        {
            return $this->_diff;
        }

        /**
         * @param string $diff
         */
        public function setDiff($diff)
        {
            $this->_diff = $diff;
        }

        /**
         * @return string
         */
        public function getDiffHeader()
        {
            return $this->_diff_header;
        }

        /**
         * @return int
         */
        public function getStartLineRemove()
        {
            return $this->_start_line_remove;
        }

        /**
         * @return int
         */
        public function getStartLineAdd()
        {
            return $this->_start_line_add;
        }

        /**
         * @return int
         */
        public function getLinesRemoved()
        {
            return $this->_lines_removed;
        }

        /**
         * @return int
         */
        public function getLinesAdded()
        {
            return $this->_lines_added;
        }

    }
