<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Notifications table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Notifications table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="notifications")
     * @Entity(class="\thebuggenie\core\entities\Notification")
     */
    class Notifications extends ScopedTable
    {
        
        const B2DB_TABLE_VERSION = 3;
        const B2DBNAME = 'notifications';
        const ID = 'notifications.id';
        const SCOPE = 'notifications.scope';
        const MODULE_NAME = 'notifications.module_name';
        const NOTIFICATION_TYPE = 'notifications.notification_type';
        const TARGET_ID = 'notifications.target_id';
        const TRIGGERED_BY_UID = 'notifications.triggered_by_user_id';
        const USER_ID = 'notifications.user_id';
        const IS_READ = 'notifications.is_read';
        const CREATED_AT = 'notifications.created_at';
        const SHOWN_AT = 'notifications.shown_at';

        public function getCountsByUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::IS_READ, false);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::TRIGGERED_BY_UID, $user_id, Criteria::DB_NOT_EQUALS);
            $unread_count = $this->count($crit);

            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::IS_READ, true);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::TRIGGERED_BY_UID, $user_id, Criteria::DB_NOT_EQUALS);
            $read_count = $this->count($crit);
            
            return array($unread_count, $read_count);
        }

        public function getCountsByUserIDAndGroupableMinutes($user_id, $minutes = 0)
        {
            if ($minutes <= 0 || !is_numeric($minutes)) return $this->getCountsByUserID($user_id);

            $notification_type_col = \b2db\Core::getTablePrefix() . self::NOTIFICATION_TYPE;
            $created_at_col = \b2db\Core::getTablePrefix() . self::CREATED_AT;
            $id_col = \b2db\Core::getTablePrefix() . self::ID;
            $notification_type_issue_updated_col = \thebuggenie\core\entities\Notification::TYPE_ISSUE_UPDATED;
            $b2dbname = \b2db\Core::getTablePrefix() . self::B2DBNAME;
            $user_id_col = \b2db\Core::getTablePrefix() . self::USER_ID;
            $triggered_by_uid_col = \b2db\Core::getTablePrefix() . self::TRIGGERED_BY_UID;
            $is_read_col = \b2db\Core::getTablePrefix() . self::IS_READ;
            $seconds = $minutes * 60;

            $custom_sql_unread = "SELECT SUM(subquery.custom_count) as custom_count FROM (SELECT {$notification_type_col}, {$created_at_col} DIV {$seconds}, COUNT({$id_col}) as real_count, (CASE WHEN {$notification_type_col} = '{$notification_type_issue_updated_col}' THEN 1 ELSE COUNT({$id_col}) END) as custom_count FROM {$b2dbname} WHERE {$user_id_col} = {$user_id} AND {$triggered_by_uid_col} != {$user_id} AND $is_read_col = 0 GROUP BY {$notification_type_col}, {$created_at_col} DIV {$seconds}, {$triggered_by_uid_col}) as subquery";
            $statement = \b2db\Statement::getPreparedStatement($custom_sql_unread);
            $res = $statement->statement->execute(array());
            if (!$res)
            {
                $unread_count = 0;
            }
            else
            {
                $resultset = $statement->statement->fetch();
                $unread_count = is_null($resultset['custom_count']) ? 0 : $resultset['custom_count'];
            }

            $custom_sql_unread = "SELECT SUM(subquery.custom_count) as custom_count FROM (SELECT {$notification_type_col}, {$created_at_col} DIV {$seconds}, COUNT({$id_col}) as real_count, (CASE WHEN {$notification_type_col} = '{$notification_type_issue_updated_col}' THEN 1 ELSE COUNT({$id_col}) END) as custom_count FROM {$b2dbname} WHERE {$user_id_col} = {$user_id} AND {$triggered_by_uid_col} != {$user_id} AND $is_read_col = 1 GROUP BY {$notification_type_col}, {$created_at_col} DIV {$seconds}, {$triggered_by_uid_col}) as subquery";
            $statement = \b2db\Statement::getPreparedStatement($custom_sql_unread);
            $res = $statement->statement->execute(array());
            if (!$res)
            {
                $read_count = 0;
            }
            else
            {
                $resultset = $statement->statement->fetch();
                $read_count = is_null($resultset['custom_count']) ? 0 : $resultset['custom_count'];
            }

            return array($unread_count, $read_count);
        }
        
        public function getByUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::TRIGGERED_BY_UID, $user_id, Criteria::DB_NOT_EQUALS);
            $crit->addOrderBy(self::ID, 'DESC');

            return $this->select($crit);
        }

        public function getByUserIDAndGroupableMinutes($user_id, $minutes = 0)
        {
            if ($minutes <= 0 || !is_numeric($minutes)) return $this->getByUserID($user_id);

            $notification_type_issue_updated_col = \thebuggenie\core\entities\Notification::TYPE_ISSUE_UPDATED;
            $seconds = $minutes * 60;

            list($target_id_col, $notification_type_col, $module_name_col, $is_read_col, $created_at_col, $triggered_by_user_id_col, $user_id_col, $shown_at_col, $scope_col, $id_col) = $this->getAliasColumns();

            $sql = 'SELECT ';
            $sql_selects = array();
            foreach ($this->getAliasColumns() as $column) $sql_selects[] = $column . ' AS ' . str_replace('.', '_', $column);

            $sql .= join(', ', $sql_selects);
            $sql .= ", (CASE WHEN {$notification_type_col} = '{$notification_type_issue_updated_col}' THEN 1 ELSE {$id_col} END) as {$this->b2db_alias}_custom_group_by";
            $sql .= ' FROM ' . Core::getTablePrefix() . $this->getB2DBName() . ' ' . $this->getB2DBAlias();
            $sql .= " WHERE {$user_id_col} = {$user_id} AND {$triggered_by_user_id_col} != {$user_id}";
            $sql .= " GROUP BY {$this->b2db_alias}_custom_group_by, {$created_at_col} DIV {$seconds}, {$triggered_by_user_id_col}";
            $sql .= " ORDER BY {$id_col} DESC";

            $crit = $this->getCriteria();
            $crit->sql = $sql;
            $crit->action = 'select';
            $statement = \b2db\Statement::getPreparedStatement($crit);
            $resultset = $statement->performQuery();

            return $this->_populateFromResultset(($resultset->count()) ? $resultset : null);
        }

        public function markUserNotificationsReadByTypesAndId($types, $id, $user_id)
        {
            if (!is_array($types)) $types = array($types);
            
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            if (count($types))
            {
                if (is_array($id))
                {
                    $crit->addWhere(self::TARGET_ID, $id, Criteria::DB_IN);
                }
                else
                {
                    $crit->addWhere(self::TARGET_ID, $id);
                }
                $crit->addWhere(self::NOTIFICATION_TYPE, $types, Criteria::DB_IN);
            }
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addUpdate(self::IS_READ, true);
            $this->doUpdate($crit);

            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::IS_READ, true);
            $crit->addWhere('notifications.created_at', NOW - (86400 * 30), Criteria::DB_LESS_THAN_EQUAL);
            $this->doDelete($crit);
        }

        public function markUserNotificationsReadByTypesAndIdAndGroupableMinutes($types, $id, $user_id, $minutes = 0, $is_read = 1, $mark_all = true)
        {
            if (!is_array($types)) $types = array($types);

            $notification_type_issue_updated_col = \thebuggenie\core\entities\Notification::TYPE_ISSUE_UPDATED;

            if (($key = array_search($notification_type_issue_updated_col, $types)) === false || ($minutes <= 0 || !is_numeric($minutes)))
            {
                if (! $mark_all) return;

                return $this->markUserNotificationsReadByTypesAndId($types, $id, $user_id);
            }

            $cols = array_map(function ($col) {
                return str_replace(self::B2DBNAME . '.', '', $col);
            }, array(
                'id' => self::ID,
                'target_id' => self::TARGET_ID,
                'created_at' => self::CREATED_AT,
                'is_read' => self::IS_READ,
                'notification_type' => self::NOTIFICATION_TYPE,
                'user_id' => self::USER_ID,
                'scope' => self::SCOPE,
            ));

            $b2dbname = \b2db\Core::getTablePrefix() . self::B2DBNAME;
            $seconds = $minutes * 60;
            $scope = framework\Context::getScope()->getID();

            $sub_sql = "SELECT {$cols['id']}, {$cols['target_id']}, ({$cols['created_at']} DIV {$seconds}) AS created_at_div FROM {$b2dbname} WHERE ";

            if (is_array($id))
            {
                $sub_sql .= $cols['target_id'] . ' IN (' . implode(', ', $id) . ')';
            }
            else
            {
                $sub_sql .= "{$cols['target_id']} = {$id}";
            }

            $sql = "UPDATE {$b2dbname} a JOIN ({$sub_sql}) b ON a.{$cols['id']} = b.{$cols['id']} SET a.{$cols['is_read']} = {$is_read} WHERE (a.{$cols['notification_type']} = '{$notification_type_issue_updated_col}') AND (a.{$cols['user_id']} = {$user_id}) AND (a.{$cols['scope']} = {$scope}) AND ((a.{$cols['created_at']} DIV {$seconds}) * a.{$cols['created_at']} DIV (a.{$cols['created_at']})) IN (b.created_at_div)";

            $crit = $this->getCriteria();
            $crit->sql = $sql;
            $crit->action = 'update';
            $statement = \b2db\Statement::getPreparedStatement($crit);
            $statement->performQuery();

            if (! $mark_all) return;

            unset($types[$key]);
            $this->markUserNotificationsReadByTypesAndId($types, $id, $user_id);
        }

        public function _migrateData(\b2db\Table $old_table)
        {
            switch ($old_table::B2DB_TABLE_VERSION)
            {
                case 2:
                    $crit = $this->getCriteria();
                    $crit->addUpdate(self::SHOWN_AT, time());
                    $this->doUpdate($crit);
                    break;
            }
        }

        protected function _setupIndexes()
        {
            $this->_addIndex('userid_targetid_notificationtype_scope', array(self::USER_ID, self::TARGET_ID, self::NOTIFICATION_TYPE, self::SCOPE));
        }

    }
