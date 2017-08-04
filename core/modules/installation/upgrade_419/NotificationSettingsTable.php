<?php

    namespace thebuggenie\core\modules\installation\upgrade_419;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * User notification settings table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * User notification settings table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="notificationsettings")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_419\NotificationSetting")
     */
    class NotificationSettingsTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

    }
