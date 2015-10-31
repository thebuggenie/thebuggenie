<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * User dashboard views table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * User dashboard views table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="dashboard_views_32")
     */
    class TBGDashboardViewsTable extends ScopedTable
    {

        const B2DBNAME = 'dashboard_views';
        const ID = 'dashboard_views.id';
        const NAME = 'dashboard_views.name';
        const VIEW = 'dashboard_views.view';
        const TID = 'dashboard_views.tid';
        const PID = 'dashboard_views.pid';
        const TARGET_TYPE = 'dashboard_views.target_type';
        const SCOPE = 'dashboard_views.scope';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::NAME, 200);
            parent::_addInteger(self::VIEW);
            parent::_addInteger(self::PID);
            parent::_addInteger(self::TARGET_TYPE);
            parent::_addInteger(self::TID);
            parent::_addInteger(self::SCOPE, 10);
        }

    }
