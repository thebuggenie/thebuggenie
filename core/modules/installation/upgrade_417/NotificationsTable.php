<?php

    namespace thebuggenie\core\modules\installation\upgrade_417;

    use thebuggenie\core\framework;
    use b2db\Core;
    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Notifications table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="notifications")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_417\Notification")
     */
    class NotificationsTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        
    }
