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

    }
