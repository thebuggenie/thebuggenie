<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria,
        thebuggenie\core\framework;
    use b2db\Criterion;
    use b2db\Update;
    use thebuggenie\core\entities\Comment;

    /**
     * Comments table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method static Comments getTable()
     *
     * @Table(name="comments")
     * @Entity(class="\thebuggenie\core\entities\Comment")
     * @Discriminator(column="target_type")
     * @Discriminators(\thebuggenie\core\entities\Issue=1, \thebuggenie\core\entities\Article=2, \thebuggenie\core\entities\Commit=3)
     */
    class Comments extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 4;
        const B2DBNAME = 'comments';
        const ID = 'comments.id';
        const SCOPE = 'comments.scope';
        const TARGET_ID = 'comments.target_id';
        const TARGET_TYPE = 'comments.target_type';
        const CONTENT = 'comments.content';
        const IS_PUBLIC = 'comments.is_public';
        const POSTED_BY = 'comments.posted_by';
        const POSTED = 'comments.posted';
        const UPDATED_BY = 'comments.updated_by';
        const UPDATED = 'comments.updated';
        const DELETED = 'comments.deleted';
        const MODULE = 'comments.module';
        const COMMENT_NUMBER = 'comments.comment_number';
        const SYSTEM_COMMENT = 'comments.system_comment';
        const HAS_ASSOCIATED_CHANGES = 'comments.has_associated_changes';

        protected $_preloaded_counts = [];

        protected function setupIndexes()
        {
            $this->addIndex('type_target', array(self::TARGET_TYPE, self::TARGET_ID));
            $this->addIndex('type_target_deleted_system', array(self::TARGET_TYPE, self::TARGET_ID, self::DELETED, self::SYSTEM_COMMENT));
        }

        protected function migrateData(\b2db\Table $old_table)
        {
            switch ($old_table::B2DB_TABLE_VERSION)
            {
                case 3:
                    $ids = [];
                    $query = $this->getQuery();
                    $query->addSelectionColumn(self::ID, 'id');
                    $res = $this->rawSelect($query);
                    if ($res) {
                        while ($row = $res->getNextRow()) {
                            $ids[$row['id']] = $row['id'];
                        }
                    }

                    $log_table = LogItems::getTable();
                    $ids_count = count($ids);
                    if ($ids_count > 0) {
                        $step = ceil($ids_count / 100);
                        $cc = 0;
                        $pct = 0;
                        foreach ($ids as $id) {
                            $log_query = $log_table->getQuery();
                            $log_query->where(LogItems::COMMENT_ID, $id);
                            if ($log_table->count($log_query)) {
                                $update = new Update();
                                $update->add(self::HAS_ASSOCIATED_CHANGES, true);
                                $this->rawUpdateById($update, $id);
                            }
                            $cc++;

                            if (defined('TBG_CLI') && $step > 10 && $cc % $step == 0) {
                                $pct += 1;
                                framework\cli\Command::cli_echo("{$cc} / {$ids_count} ({$pct}%)\n");
                            }
                        }
                    }
                    break;
            }
        }

        public function getComments($target_id, $target_type, $sort_order = \b2db\QueryColumnSort::SORT_ASC)
        {
            $query = $this->getQuery();
            if($target_id != 0)
            {
                $query->where(self::TARGET_ID, $target_id);
            }
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::DELETED, 0);
            $query->addOrderBy(self::COMMENT_NUMBER, $sort_order);
            $res = $this->select($query, false);

            return $res;
        }

        public function getCommentIDs($target_id, $target_type, $sort_order = \b2db\QueryColumnSort::SORT_ASC)
        {
            $query = $this->getQuery();
            if($target_id != 0)
            {
                $query->where(self::TARGET_ID, $target_id);
            }
            $query->where(self::TARGET_TYPE, $target_type);
            $query->addSelectionColumn(self::ID);
            $query->addOrderBy(self::POSTED, $sort_order);
            $res = $this->rawSelect($query, false);

            $ids = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $ids[] = $row[self::ID];
                }
            }
            return $ids;
        }

        public function countComments($target_id, $target_type, $include_system_comments = true)
        {
            $query = $this->getQuery();
            if($target_id != 0)
            {
                $query->where(self::TARGET_ID, $target_id);
            }
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::DELETED, 0);
            if (!$include_system_comments)
                $query->where(self::SYSTEM_COMMENT, false);

            return $this->count($query);
        }

        public function preloadCommentCounts($target_type, $target_ids)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'num_comments', \b2db\Query::DB_COUNT);
            $query->addSelectionColumn(self::TARGET_ID, 'identifier');
            $query->where(self::TARGET_ID, $target_ids, \b2db\Criterion::IN);
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::DELETED, 0);
            $query->where(self::SYSTEM_COMMENT, false);
            $query->addGroupBy(self::TARGET_ID);

            $res = $this->rawSelect($query, false);
            $this->_preloaded_counts[$target_type] = [];
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $this->_preloaded_counts[$target_type][$row->get('identifier')] = $row->get('num_comments');
                }
            }
        }

        public function clearPreloadedCommentCounts($target_type)
        {
            unset($this->_preloaded_counts[$target_type]);
        }

        public function getPreloadedCommentCount($target_type, $target_id)
        {
            if (!array_key_exists($target_type, $this->_preloaded_counts) || !is_array($this->_preloaded_counts[$target_type])) return null;

            if (isset($this->_preloaded_counts[$target_type][$target_id]))
            {
                $val = $this->_preloaded_counts[$target_type][$target_id];
                unset($this->_preloaded_counts[$target_type][$target_id]);
                return $val;
            }

            return 0;
        }

        public function getNextCommentNumber($target_id, $target_type)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::COMMENT_NUMBER, 'max_no', \b2db\Query::DB_MAX, '', '+1');
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::TARGET_TYPE, $target_type);

            $row = $this->rawSelectOne($query);
            return ($row->get('max_no')) ? $row->get('max_no') : 1;
        }

        public function getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit = 10)
        {
            $query = $this->getQuery();
            $query->where(self::POSTED_BY, $user_id);
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::SYSTEM_COMMENT, false);
            $query->addOrderBy(self::POSTED, \b2db\QueryColumnSort::SORT_DESC);
            $query->setLimit($limit);

            return $this->select($query);
        }

        public function fixFileComments()
        {
            $query = $this->getQuery();
            $query->where(self::CONTENT, 'A file was uploaded%', Criterion::LIKE);

            $update = new Update();
            $update->add(self::SYSTEM_COMMENT, true);

            $this->rawUpdate($update, $query);
        }

    }
