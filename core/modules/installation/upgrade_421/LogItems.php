<?php

    namespace thebuggenie\core\modules\installation\upgrade_421;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Log table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method static LogItems getTable()
     *
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_421\LogItem")
     * @Table(name="log")
     */
    class LogItems extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

    }
