<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Query;
    use b2db\Update;
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
            $query = $this->getQuery();
            $query->where(self::TARGET, $issue_id);
            $query->where(self::TARGET_TYPE, LogItem::TYPE_ISSUE);
            $query->addOrderBy(self::TIME, \b2db\QueryColumnSort::SORT_ASC);
            return $this->select($query);
        }

        /**
         * @param int $limit
         * @param int $offset
         * @param int $project_id
         * @param int $user_id
         *
         * @return Query
         */
        protected function getQueryWithCriteriaForProjectOrUser($limit, $offset, $project_id = null, $user_id = null)
        {
            $criteria = new Criteria();
            if ($project_id !== null) {
                $criteria->where('log.project_id', $project_id);
            }
            if ($user_id !== null) {
                $criteria->where(self::UID, $user_id);
            }

            $criteria->where(self::TIME, NOW, Criterion::LESS_THAN_EQUAL);

            $query = $this->getQuery();
            $query->where($criteria);
            if ($limit !== null) {
                $query->setLimit($limit);
            }
            if ($offset !== null) {
                $query->setOffset($offset);
            }

            $query->addOrderBy(self::TIME, \b2db\QueryColumnSort::SORT_DESC);

            return $query;
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
            $query = $this->getQueryWithCriteriaForProjectOrUser($limit, $offset, null, $user_id);

            return $this->select($query);
        }

        /**
         * @param int $project_id
         * @param int $limit
         * @param int $offset
         *
         * @return LogItem[]
         */
        public function getByProjectID($project_id, $limit = 50, $offset = null)
        {
            $query = $this->getQueryWithCriteriaForProjectOrUser($limit, $offset, $project_id);
            return $this->select($query);
        }

        public function getImportantByProjectID($project_id, $limit = 50, $offset = null)
        {
            $query = $this->getQueryWithCriteriaForProjectOrUser($limit, $offset, $project_id);
            $query->where(self::CHANGE_TYPE, array(LogItem::ACTION_ISSUE_CREATED, LogItem::ACTION_ISSUE_CLOSE), Criterion::IN);
            return $this->select($query);
        }

        public function getLast15IssueCountsByProjectID($project_id)
        {
            $retarr = array();

            for ($cc = 15; $cc >= 0; $cc--)
            {
                $query = $this->getQuery();
                $query->join(Issues::getTable(), Issues::ID, self::TARGET, array(array(Issues::PROJECT_ID, $project_id), array(Issues::DELETED, false)));
                $query->where(self::CHANGE_TYPE, array(LogItem::ACTION_ISSUE_CREATED, LogItem::ACTION_ISSUE_CLOSE), Criterion::IN);
                $query->where(self::TARGET_TYPE, LogItem::TYPE_ISSUE);
                $query->where(Issues::DELETED, false);
                $query->where('log.project_id', $project_id);
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->where(self::TIME, NOW - (86400 * ($cc + 1)), Criterion::GREATER_THAN_EQUAL);
                $query->where(self::TIME, NOW - (86400 * $cc), Criterion::LESS_THAN_EQUAL);

                $closed_count = array();
                $open_count = array();
                if ($res = $this->rawSelect($query)) {
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

        protected function setupIndexes()
        {
            $this->addIndex('commentid', array(self::COMMENT_ID));
            $this->addIndex('targettype_time', array(self::TARGET_TYPE, self::TIME));
            $this->addIndex('targettype_changetype', array(self::TARGET_TYPE, self::CHANGE_TYPE));
            $this->addIndex('target_uid_commentid_scope', array(self::TARGET, self::UID, self::COMMENT_ID, self::SCOPE));
        }

        protected function migrateData(\b2db\Table $old_table)
        {
            switch ($old_table::B2DB_TABLE_VERSION)
            {
                case 2:
                    $query = $this->getQuery();
                    $query->setIsDistinct();
                    $query->addSelectionColumn(self::TARGET);
                    $query->join(Issues::getTable(), Issues::ID, self::TARGET, [[Issues::DELETED, false]]);
                    $query->addSelectionColumn(Issues::PROJECT_ID);
                    $query->where(self::TARGET_TYPE, LogItem::TYPE_ISSUE);

                    $issue_ids = [];
                    if ($res = $this->rawSelect($query)) {
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
                            $query = $this->getQuery();
                            $update = new Update();

                            $update->add('log.project_id', $project_id);

                            $query->where(self::TARGET, $issues, Criterion::IN);

                            $this->rawUpdate($update, $query);
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
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TARGET, $target);
            if ($target_type !== null) {
                $query->where(self::TARGET_TYPE, $target_type);
            }
            $query->where(self::CHANGE_TYPE, $change);

            return $this->selectOne($query);
        }

    }
