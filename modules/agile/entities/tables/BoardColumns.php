<?php

    namespace thebuggenie\modules\agile\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Agile board columns table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Agile board columns table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method \thebuggenie\core\entities\tables\BoardColumns getTable() Retrieves an instance of this table
     * @method \thebuggenie\modules\agile\entities\BoardColumn selectById(integer $id) Retrieves an agile board
     *
     * @Table(name="agileboard_columns")
     * @Entity(class="\thebuggenie\modules\agile\entities\BoardColumn")
     */
    class BoardColumns extends ScopedTable
    {

    }
