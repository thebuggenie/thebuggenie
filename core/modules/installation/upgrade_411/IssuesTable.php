<?php

    namespace thebuggenie\core\modules\installation\upgrade_411;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Issues table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Issues getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Issue selectById(integer $id, Criteria $query = null, $join = 'all') Retrieves an issue
     *
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_411\Issue")
     * @Table(name='issues')
     */
    class IssuesTable extends ScopedTable
    {

    }
