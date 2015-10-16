<?php

    namespace thebuggenie\core\modules\installation\upgrade_41;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Milestones table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Milestones getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Milestone selectById(integer $id) Retrieves a milestone
     *
     * @Table(name="milestones")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_41\Milestone")
     */
    class MilestonesTable extends ScopedTable
    {

    }
