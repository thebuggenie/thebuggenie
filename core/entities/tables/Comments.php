<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria,
        thebuggenie\core\framework;
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

        protected function _setupIndexes()
        {
            $this->_addIndex('type_target', array(self::TARGET_TYPE, self::TARGET_ID));
            $this->_addIndex('type_target_deleted_system', array(self::TARGET_TYPE, self::TARGET_ID, self::DELETED, self::SYSTEM_COMMENT));
        }

        public function _migrateData(\b2db\Table $old_table)
        {
            switch ($old_table::B2DB_TABLE_VERSION)
            {
                case 3:
                    $ids = [];
                    $crit = $this->getCriteria();
                    $crit->addSelectionColumn(self::ID, 'id');
                    $res = $this->doSelect($crit);
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
                            $log_crit = $log_table->getCriteria();
                            $log_crit->addWhere(LogItems::COMMENT_ID, $id);
                            if ($log_table->count($log_crit)) {
                                $comment_crit = $this->getCriteria();
                                $comment_crit->addUpdate(self::HAS_ASSOCIATED_CHANGES, true);
                                $comment_crit->addWhere(self::ID, $id);
                                $this->doUpdate($comment_crit);
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

        public function getComments($target_id, $target_type, $sort_order = Criteria::SORT_ASC)
        {
            $crit = $this->getCriteria();
            if($target_id != 0)
            {
                $crit->addWhere(self::TARGET_ID, $target_id);
            }
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addWhere(self::DELETED, 0);
            $crit->addOrderBy(self::COMMENT_NUMBER, $sort_order);
            $res = $this->select($crit, false);

            return $res;
        }

        public function getCommentIDs($target_id, $target_type, $sort_order = Criteria::SORT_ASC)
        {
            $crit = $this->getCriteria();
            if($target_id != 0)
            {
                $crit->addWhere(self::TARGET_ID, $target_id);
            }
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addSelectionColumn(self::ID);
            $crit->addOrderBy(self::POSTED, $sort_order);
            $res = $this->doSelect($crit, false);

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
            $crit = $this->getCriteria();
            if($target_id != 0)
            {
                $crit->addWhere(self::TARGET_ID, $target_id);
            }
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addWhere(self::DELETED, 0);
            if (!$include_system_comments)
                $crit->addWhere(self::SYSTEM_COMMENT, false);

            return $this->doCount($crit);
        }

        public function preloadCommentCounts($target_type, $target_ids)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID, 'num_comments', Criteria::DB_COUNT);
            $crit->addSelectionColumn(self::TARGET_ID, 'identifier');
            $crit->addWhere(self::TARGET_ID, $target_ids, Criteria::DB_IN);
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addWhere(self::DELETED, 0);
            $crit->addWhere(self::SYSTEM_COMMENT, false);
            $crit->addGroupBy(self::TARGET_ID);

            $res = $this->doSelect($crit, false);
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
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::COMMENT_NUMBER, 'max_no', Criteria::DB_MAX, '', '+1');
            $crit->addWhere(self::TARGET_ID, $target_id);
            $crit->addWhere(self::TARGET_TYPE, $target_type);

            $row = $this->doSelectOne($crit);
            return ($row->get('max_no')) ? $row->get('max_no') : 1;
        }

        public function getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit = 10)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::POSTED_BY, $user_id);
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addWhere(self::SYSTEM_COMMENT, false);
            $crit->addOrderBy(self::POSTED, Criteria::SORT_DESC);
            $crit->setLimit($limit);

            return $this->select($crit);
        }

    }
