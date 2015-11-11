<?php

    namespace thebuggenie\core\modules\installation\upgrade_413;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Agile boards table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="agileboards")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_413\AgileBoard")
     */
    class AgileBoardsTable extends ScopedTable
    {

    }
