<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Milestones table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Milestones table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="milestones")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_32\TBGMilestone")
     */
    class TBGMilestonesTable extends ScopedTable
    {

    }
