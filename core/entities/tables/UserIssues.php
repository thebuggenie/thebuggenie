<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * User issues table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * User issues table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="userissues")
     */
    class UserIssues extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'userissues';
        const ID = 'userissues.id';
        const SCOPE = 'userissues.scope';
        const ISSUE = 'userissues.issue';
        const UID = 'userissues.uid';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::ISSUE, Issues::getTable(), Issues::ID);
            parent::_addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
        }

        public function _setupIndexes()
        {
            $this->_addIndex('uid_scope', array(self::UID, self::SCOPE));
        }

        public function getUserIDsByIssueID($issue_id)
        {
            $uids = array();
            $crit = $this->getCriteria();
            
            $crit->addWhere(self::ISSUE, $issue_id);
            
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $uid = $row->get(UserIssues::UID);
                    $uids[$uid] = $uid;
                }
            }
            
            return $uids;
        }

        public function copyStarrers($from_issue_id, $to_issue_id)
        {
            $old_watchers = $this->getUserIDsByIssueID($from_issue_id);
            $new_watchers = $this->getUserIDsByIssueID($to_issue_id);

            if (count($old_watchers))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::ISSUE, $to_issue_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                foreach ($old_watchers as $uid)
                {
                    if (!in_array($uid, $new_watchers))
                    {
                        $crit->addInsert(self::UID, $uid);
                        $this->doInsert($crit);
                    }
                }
            }
        }
        
        public function getUserStarredIssues($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $user_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addJoin(Issues::getTable(), Issues::ID, self::ISSUE);
            $crit->addWhere(Issues::DELETED, 0);
            $crit->addSelectionColumn(Issues::ID, 'issue_id');

            $res = $this->doSelect($crit);
            $issues = array();
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $issue_id = $row['issue_id'];
                    $issues[$issue_id] = $issue_id;
                }
            }
            return $issues;
        }
        
        public function addStarredIssue($user_id, $issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUE, $issue_id);
            $crit->addInsert(self::UID, $user_id);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());

            $this->doInsert($crit);
        }
        
        public function removeStarredIssue($user_id, $issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE, $issue_id);
            $crit->addWhere(self::UID, $user_id);
                
            $this->doDelete($crit);
            return true;
        }
    }
