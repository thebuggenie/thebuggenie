<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * User dashboards table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * User dashboards table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="dashboards")
     * @Entity(class="\thebuggenie\core\entities\Dashboard")
     */
    class Dashboards extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

    }
