<?php

    namespace thebuggenie\core\modules\installation\upgrade_414;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Files table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="files")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_414\File")
     */
    class FilesTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

    }
