<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\LivelinkImport;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * LiveLink imports table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * LiveLink imports table
     *
     * @method static LivelinkImports getTable()
     *
     * @Table(name="livelink_imports")
     * @Entity(class="\thebuggenie\core\entities\LivelinkImport")
     */
    class LivelinkImports extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'livelink_imports';
        const SCOPE = 'livelink_imports.scope';

        /**
         * @return LivelinkImport[]
         */
        public function getPending()
        {
            $crit = $this->getCriteria();
            $crit->addWhere('livelink_imports.completed_at', 0);
            $res = $this->select($crit, false);

            return $res;
        }

    }
