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
        
        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'notifications';
        const ID = 'notifications.id';
        const SCOPE = 'notifications.scope';
        const MODULE_NAME = 'notifications.module_name';
        const NOTIFICATION_TYPE = 'notifications.notification_type';
        const TARGET_ID = 'notifications.target_id';
        const TRIGGERED_BY_UID = 'notifications.triggered_by_user_id';
        const USER_ID = 'notifications.user_id';
        const IS_READ = 'notifications.is_read';

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
        
    }
