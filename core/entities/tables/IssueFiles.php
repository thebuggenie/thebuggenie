<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Issues <-> Files table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issues <-> Files table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuefiles")
     */
    class IssueFiles extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issuefiles';
        const ID = 'issuefiles.id';
        const SCOPE = 'issuefiles.scope';
        const UID = 'issuefiles.uid';
        const ATTACHED_AT = 'issuefiles.attached_at';
        const FILE_ID = 'issuefiles.file_id';
        const ISSUE_ID = 'issuefiles.issue_id';

        protected $_preloaded_issue_counts;

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
            parent::_addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::_addForeignKeyColumn(self::FILE_ID, Files::getTable(), Files::ID);
            parent::_addInteger(self::ATTACHED_AT, 10);
        }

        protected function _setupIndexes()
        {
            $this->_addIndex('issueid', self::ISSUE_ID);
        }

        public function addByIssueIDandFileID($issue_id, $file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            $crit->addWhere(self::FILE_ID, $file_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if ($this->doCount($crit) == 0)
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $crit->addInsert(self::ATTACHED_AT, NOW);
                $crit->addInsert(self::ISSUE_ID, $issue_id);
                $crit->addInsert(self::FILE_ID, $file_id);
                $this->doInsert($crit);

                return true;
            }

            return false;
        }

        public function getByIssueID($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            $crit->addJoin(Files::getTable(), Files::ID, self::FILE_ID);
            $res = $this->doSelect($crit, false);

            $ret_arr = array();

            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $file = new \thebuggenie\core\entities\File($row->get(Files::ID), $row);
                    $file->setUploadedAt($row->get(self::ATTACHED_AT));
                    $ret_arr[$row->get(Files::ID)] = $file;
                }
            }

            return $ret_arr;
        }

        public function preloadIssueFileCounts($target_ids)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID, 'num_files', Criteria::DB_COUNT);
            $crit->addSelectionColumn(self::ISSUE_ID);
            $crit->addWhere(self::ISSUE_ID, $target_ids, Criteria::DB_IN);
            $crit->addGroupBy(self::ISSUE_ID);

            $res = $this->doSelect($crit, false);
            $this->_preloaded_issue_counts = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $this->_preloaded_issue_counts[$row->get(self::ISSUE_ID)] = $row->get('num_files');
                }
            }
        }

        public function clearPreloadedIssueFileCounts()
        {
            $this->_preloaded_issue_counts = null;
        }

        public function getPreloadedIssueFileCount($target_id)
        {
            if (!is_array($this->_preloaded_issue_counts)) return null;

            if (isset($this->_preloaded_issue_counts[$target_id]))
            {
                $val = $this->_preloaded_issue_counts[$target_id];
                unset($this->_preloaded_issue_counts[$target_id]);
                return $val;
            }
            return 0;
        }

        public function countByIssueID($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            return $this->doCount($crit);
        }

        public function getIssuesByFileID($file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::FILE_ID, $file_id);

            $issue_ids = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $i_id = $row->get(self::ISSUE_ID);
                    $issue_ids[$i_id] = $i_id;
                }
            }
            return $issue_ids;
        }

        public function removeByIssueIDandFileID($issue_id, $file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            $crit->addWhere(self::FILE_ID, $file_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if ($res = $this->doSelectOne($crit))
            {
                $this->doDelete($crit);
            }
            return $res;
        }

    }
