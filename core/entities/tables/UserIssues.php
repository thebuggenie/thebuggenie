<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUE, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
        }

        protected function setupIndexes()
        {
            $this->addIndex('uid_scope', array(self::UID, self::SCOPE));
        }

        public function getUserIDsByIssueID($issue_id)
        {
            $uids = array();
            $query = $this->getQuery();
            
            $query->where(self::ISSUE, $issue_id);
            
            if ($res = $this->rawSelect($query))
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
                $insertion = new Insertion();
                $insertion->add(self::ISSUE, $to_issue_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                foreach ($old_watchers as $uid)
                {
                    if (!in_array($uid, $new_watchers))
                    {
                        $insertion->add(self::UID, $uid);
                        $this->rawInsert($insertion);
                    }
                }
            }
        }
        
        public function getUserStarredIssues($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->join(Issues::getTable(), Issues::ID, self::ISSUE);
            $query->where(Issues::DELETED, 0);
            $query->addSelectionColumn(Issues::ID, 'issue_id');

            $res = $this->rawSelect($query);
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
            $insertion = new Insertion();
            $insertion->add(self::ISSUE, $issue_id);
            $insertion->add(self::UID, $user_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawInsert($insertion);
        }
        
        public function removeStarredIssue($user_id, $issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE, $issue_id);
            $query->where(self::UID, $user_id);
                
            $this->rawDelete($query);
            return true;
        }
    }
