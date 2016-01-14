<?php

    namespace thebuggenie\core\modules\installation\upgrade_415;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Projects table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Projects getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Project selectById(integer $id) Retrieves a project
     *
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_415\Project")
     * @Table(name="projects")
     */
    class ProjectsTable extends ScopedTable
    {

    }
