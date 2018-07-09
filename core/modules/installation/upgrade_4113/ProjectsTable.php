<?php

    namespace thebuggenie\core\modules\installation\upgrade_4113;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Projects table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Projects table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @static @method ProjectsTable getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Project selectById(integer $id) Retrieves a project
     *
     * @Table(name="projects")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_4113\Project")
     */
    class ProjectsTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 4;

    }
