<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Votes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Votes table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="votes")
     */
    class Votes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'votes';
        const ID = 'votes.id';
        const SCOPE = 'votes.scope';
        const TARGET = 'votes.target';
        const VOTE = 'votes.vote';
        const UID = 'votes.uid';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addInteger(self::TARGET, 10);
            parent::_addInteger(self::VOTE, 2);
            parent::_addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
        }
        
        public function getVoteSumForIssue($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::VOTE, 'votes_total', Criteria::DB_SUM);
            $crit->addWhere(self::TARGET, $issue_id);
            $res = $this->doSelectOne($crit, false);

            return ($res) ? $res->get('votes_total') : 0;
        }
        
        public function getByIssueId($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::TARGET, $issue_id);
            $res = $this->doSelect($crit, false);
            return $res;
        }
        
        public function addByUserIdAndIssueId($user_id, $issue_id, $up = true)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::TARGET, $issue_id);
            $crit->addWhere(self::UID, $user_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
            
            $crit = $this->getCriteria();
            $crit->addInsert(self::TARGET, $issue_id);
            $crit->addInsert(self::UID, $user_id);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addInsert(self::VOTE, (($up) ? 1 : -1));
            $res = $this->doInsert($crit);
            return $res->getInsertID();
        }
        
    }
