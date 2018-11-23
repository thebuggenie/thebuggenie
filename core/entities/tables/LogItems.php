<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\LogItem;
    use thebuggenie\core\entities\Scope;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Log table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method static LogItems getTable()
     *
     * @Entity(class="\thebuggenie\core\entities\LogItem")
     * @Table(name="log")
     */
    class LogItems extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 3;

        const B2DBNAME = 'log';
        const ID = 'log.id';
        const SCOPE = 'log.scope';
        const TARGET = 'log.target';
        const TARGET_TYPE = 'log.target_type';
        const CHANGE_TYPE = 'log.change_type';
        const PREVIOUS_VALUE = 'log.previous_value';
        const CURRENT_VALUE = 'log.current_value';
        const TEXT = 'log.text';
        const TIME = 'log.time';
        const UID = 'log.uid';
        const COMMENT_ID = 'log.comment_id';

        /**
         * @param $issue_id
         *
         * @return LogItem[]
         */
        public function getByIssueID($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::TARGET, $issue_id);
            $crit->addWhere(self::TARGET_TYPE, LogItem::TYPE_ISSUE);
            $crit->addOrderBy(self::TIME, Criteria::SORT_ASC);
            return $this->select($crit);
        }

        /**
         * @param int $limit
         * @param int $offset
         * @param int $project_id
         * @param int $user_id
         *
         * @return Criteria
         */
        protected function getCriteriaForProjectOrUser($limit, $offset, $project_id = null, $user_id = null)
        {
            $crit = $this->getCriteria();
            if ($project_id !== null) {
                $crit->addWhere('log.project_id', $project_id);
            }
            if ($user_id !== null) {
                $crit->addWhere(self::UID, $user_id);
            }

            $crit->addWhere(self::TIME, NOW, Criteria::DB_LESS_THAN_EQUAL);

            if ($limit !== null) {
                $crit->setLimit($limit);
            }
            if ($offset !== null) {
                $crit->setOffset($offset);
            }

            $crit->addOrderBy(self::TIME, Criteria::SORT_DESC);

            return $crit;
        }

        /**
         * @param $user_id
         * @param int $limit
         * @param int $offset
         *
         * @return LogItem[]
         */
        public function getByUserID($user_id, $limit = null, $offset = null)
        {
            $crit = $this->getCriteriaForProjectOrUser($limit, $offset, null, $user_id);
            return $this->select($crit);
        }

        /**
         * @param int $project_id
         * @param int $limit
         * @param int $offset
         *
         * @return LogItem[]
         */
        public function getByProjectID($project_id, $limit = 20, $offset = null)
        {
            $crit = $this->getCriteriaForProjectOrUser($limit, $offset, $project_id);
            return $this->select($crit);
        }

        public function getImportantByProjectID($project_id, $limit = 20, $offset = null)
        {
            $crit = $this->getCriteriaForProjectOrUser($limit, $offset, $project_id);
            $crit->addWhere(self::CHANGE_TYPE, array(LogItem::ACTION_ISSUE_CREATED, LogItem::ACTION_ISSUE_CLOSE), Criteria::DB_IN);
            return $this->select($crit);
        }

        public function getLast15IssueCountsByProjectID($project_id)
        {
            $retarr = array();

            for ($cc = 15; $cc >= 0; $cc--)
            {
                $crit = $this->getCriteria();
                $crit->addJoin(Issues::getTable(), Issues::ID, self::TARGET, array(array(Issues::PROJECT_ID, $project_id), array(Issues::DELETED, false)));
                $crit->addWhere(self::CHANGE_TYPE, array(LogItem::ACTION_ISSUE_CREATED, LogItem::ACTION_ISSUE_CLOSE), Criteria::DB_IN);
                $crit->addWhere(self::TARGET_TYPE, LogItem::TYPE_ISSUE);
                $crit->addWhere(Issues::DELETED, false);
                $crit->addWhere('log.project_id', $project_id);
                $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
                $ctn = $crit->returnCriterion(self::TIME, NOW - (86400 * ($cc + 1)), Criteria::DB_GREATER_THAN_EQUAL);
                $ctn->addWhere(self::TIME, NOW - (86400 * $cc), Criteria::DB_LESS_THAN_EQUAL);
                $crit->addWhere($ctn);

                $closed_count = array();
                $open_count = array();
                if ($res = $this->doSelect($crit)) {
                    while ($row = $res->getNextRow()) {
                        if ($row[self::CHANGE_TYPE] == LogItem::ACTION_ISSUE_CLOSE) {
                            $closed_count[$row->get(self::TARGET)] = true;
                        } else {
                            $open_count[$row->get(self::TARGET)] = true;
                        }
                    }
                }
                $retarr[0][$cc] = count($closed_count);
                $retarr[1][$cc] = count($open_count);
            }
            return $retarr;
        }

        protected function _setupIndexes()
        {
            $this->_addIndex('commentid', array(self::COMMENT_ID));
            $this->_addIndex('targettype_time', array(self::TARGET_TYPE, self::TIME));
            $this->_addIndex('targettype_changetype', array(self::TARGET_TYPE, self::CHANGE_TYPE));
            $this->_addIndex('target_uid_commentid_scope', array(self::TARGET, self::UID, self::COMMENT_ID, self::SCOPE));
        }

        public function _migrateData(\b2db\Table $old_table)
        {
            switch ($old_table::B2DB_TABLE_VERSION)
            {
                case 2:
                    $crit = $this->getCriteria();
                    $crit->setDistinct();
                    $crit->addSelectionColumn(self::TARGET);
                    $crit->addJoin(Issues::getTable(), Issues::ID, self::TARGET, [[Issues::DELETED, false]]);
                    $crit->addSelectionColumn(Issues::PROJECT_ID);
                    $crit->addWhere(self::TARGET_TYPE, LogItem::TYPE_ISSUE);

                    $issue_ids = [];
                    if ($res = $this->doSelect($crit)) {
                        while ($row = $res->getNextRow()) {
                            $project_id = $row->get(Issues::PROJECT_ID);

                            if (!$project_id) continue;
                            if (!isset($issue_ids[$project_id])) {
                                $issue_ids[$project_id] = [];
                            }
                            $issue_id = $row->get(self::TARGET);
                            $issue_ids[$project_id][$issue_id] = $issue_id;
                        }
                    }

                    if (count($issue_ids)) {
                        foreach ($issue_ids as $project_id => $issues) {
                            $crit = $this->getCriteria();
                            $crit->addWhere(self::TARGET, $issues, Criteria::DB_IN);
                            $crit->addUpdate('log.project_id', $project_id);

                            $this->doUpdate($crit);
                        }
                    }

                    $current_scope = framework\Context::getScope();
                    foreach (Scope::getAll() as $scope) {
                        framework\Context::setScope($scope);
                        foreach (Milestones::getTable()->selectAll() as $milestone) {
                            $milestone->generateLogItems();
                        }
                        foreach (Builds::getTable()->selectAll() as $build) {
                            $build->generateLogItems();
                        }
                    }
                    framework\Context::setScope($current_scope);
                    break;
            }
        }

        /**
         * @param $target
         * @param $change
         * @param $target_type
         *
         * @return LogItem
         */
        public function getByTargetAndChangeAndType($target, $change, $target_type = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::TARGET, $target);
            if ($target_type !== null) {
                $crit->addWhere(self::TARGET_TYPE, $target_type);
            }
            $crit->addWhere(self::CHANGE_TYPE, $change);

            return $this->selectOne($crit);
        }

    }
