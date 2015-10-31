<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable;

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
     * @Table(name="dashboard_views")
     * @Entity(class="\thebuggenie\core\entities\DashboardView")
     */
    class DashboardViews extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'dashboard_views';
        const ID = 'dashboard_views.id';
        const NAME = 'dashboard_views.name';
        const VIEW = 'dashboard_views.view';
        const TID = 'dashboard_views.tid';
        const PID = 'dashboard_views.pid';
        const TARGET_TYPE = 'dashboard_views.target_type';
        const SCOPE = 'dashboard_views.scope';
        const TYPE_USER = 1;
        const TYPE_PROJECT = 2;
        const TYPE_TEAM = 3;
        const TYPE_CLIENT = 4;

        protected function _migrateData(\b2db\Table $old_table)
        {
            $crit = $old_table->getCriteria();
            $old_table->doDelete($crit);
//            foreach (array(self::TYPE_USER, self::TYPE_PROJECT) as $target_type)
//            {
//                $crit = $this->getCriteria();
//                $crit->addSelectionColumn('dashboard_views.tid', 'target_id', Criteria::DB_DISTINCT);
//                $crit->addSelectionColumn('dashboard_views.scope');
//                $crit->addWhere('dashboard_views.target_type', $target_type);
//                $res = $this->doSelect($crit, 'none');
//                $views = array();
//                if ($res)
//                {
//                    while ($row = $res->getNextRow())
//                    {
//                        $dashboard = new Dashboard();
//                        $dashboard->setName('Dashboard');
//                        $dashboard->setIsDefault(true);
//                        if ($target_type == self::TYPE_USER)
//                            $dashboard->setUser($row['target_id']);
//                        elseif ($target_type == self::TYPE_PROJECT)
//                            $dashboard->setProject($row['target_id']);
//
//                        $dashboard->setScope($row['dashboard_views.scope']);
//                        $dashboard->save();
//
//                        $views[$dashboard->getID()] = array('target_id' => $row['target_id'], 'scope_id' => $row['dashboard_views.scope']);
//                    }
//
//                    foreach ($views as $dashboard_id => $target)
//                    {
//                        $crit = $this->getCriteria();
//                        $crit->addUpdate('dashboard_views.dashboard_id', $dashboard_id);
//                        $crit->addWhere('dashboard_views.tid', $target['target_id']);
//                        $crit->addWhere('dashboard_views.target_type', $target_type);
//                        $crit->addWhere('dashboard_views.scope', $target['scope_id']);
//                        $this->doUpdate($crit);
//                    }
//                }
//            }
        }

        public function addView($target_id, $target_type, $view)
        {
            if ($view['type'])
            {
                $view_id = (array_key_exists('id', $view)) ? $view['id'] : 0;
                $crit = $this->getCriteria();
                $crit->addInsert(self::TID, $target_id);
                $crit->addInsert(self::TARGET_TYPE, $target_type);
                $crit->addInsert(self::NAME, $view['type']);
                $crit->addInsert(self::VIEW, $view_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $this->doInsert($crit);
            }
        }

        public function clearViews($target_id, $target_type)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::TID, $target_id);
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $this->doDelete($crit);
        }

        public function getViews($target_id, $target_type)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::TID, $target_id);
            $crit->addWhere(self::TARGET_TYPE, $target_type);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOrderBy(self::ID);
            $res = $this->select($crit);

            return $res;
        }

    }
