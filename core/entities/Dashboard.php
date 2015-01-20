<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;
    use thebuggenie\core\entities\common\IdentifiableScoped,
        \thebuggenie\core\entities\Project,
        \thebuggenie\core\entities\User,
        \thebuggenie\core\entities\Team,
        \thebuggenie\core\entities\Client;

    /**
     * Dashboard class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Dashboard class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Dashboards")
     */
    class Dashboard extends IdentifiableScoped
    {

        const TYPE_USER = 1;
        const TYPE_PROJECT = 2;
        const TYPE_TEAM = 3;
        const TYPE_CLIENT = 4;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Whether the dashboard is the default
         *
         * @var boolean
         * @Column(type="boolean", default=0)
         */
        protected $_is_default = false;

        /**
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_user_id;

        /**
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @var \thebuggenie\core\entities\Team
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Team")
         */
        protected $_team_id;

        /**
         * @var \thebuggenie\core\entities\Client
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Client")
         */
        protected $_client_id;

        /**
         * Dashboard views
         *
         * @var array|\thebuggenie\core\entities\DashboardView
         * @Relates(class="\thebuggenie\core\entities\DashboardView", collection=true, foreign_column="dashboard_id", orderby="sort_order")
         */
        protected $_dashboard_views = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200, default="main/dashboardlayoutstandard")
         */
        protected $_layout = 'main/dashboardlayoutstandard';

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                switch ($this->getType())
                {
                    case self::TYPE_USER:
                        $dv_issues = new DashboardView();
                        $dv_issues->setDashboard($this);
                        $dv_issues->setColumn(1);
                        $dv_issues->setType(DashboardView::VIEW_PROJECTS);
                        $dv_issues->setDetail(0);
                        $dv_issues->save();
                        
                        $dv_logged = new DashboardView();
                        $dv_logged->setDashboard($this);
                        $dv_logged->setColumn(2);
                        $dv_logged->setType(DashboardView::VIEW_LOGGED_ACTIONS);
                        $dv_logged->setDetail(0);
                        $dv_logged->save();
                        break;
                    case self::TYPE_PROJECT:
                        $dv_project_info = new DashboardView();
                        $dv_project_info->setDashboard($this);
                        $dv_project_info->setColumn(1);
                        $dv_project_info->setType(DashboardView::VIEW_PROJECT_INFO);
                        $dv_project_info->setDetail(0);
                        $dv_project_info->save();

                        $dv_project_team = new DashboardView();
                        $dv_project_team->setDashboard($this);
                        $dv_project_team->setColumn(1);
                        $dv_project_team->setType(DashboardView::VIEW_PROJECT_TEAM);
                        $dv_project_team->setDetail(0);
                        $dv_project_team->save();

                        $dv_project_statistics = new DashboardView();
                        $dv_project_statistics->setDashboard($this);
                        $dv_project_statistics->setColumn(2);
                        $dv_project_statistics->setType(DashboardView::VIEW_PROJECT_STATISTICS_LAST15);
                        $dv_project_statistics->setDetail(0);
                        $dv_project_statistics->save();

                        $dv_project_activities = new DashboardView();
                        $dv_project_activities->setDashboard($this);
                        $dv_project_activities->setColumn(2);
                        $dv_project_activities->setType(DashboardView::VIEW_PROJECT_RECENT_ACTIVITIES);
                        $dv_project_activities->setDetail(0);
                        $dv_project_activities->save();
                        break;
                }
            }
        }
        
        /**
         * Returns the associated user
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getUser()
        {
            return $this->_b2dbLazyload('_user_id');
        }

        public function setUser($user)
        {
            $this->_user_id = $user;
        }

        /**
         * Returns the associated team
         *
         * @return \thebuggenie\core\entities\Team
         */
        public function getTeam()
        {
            return $this->_b2dbLazyload('_team_id');
        }

        public function setTeam($team)
        {
            $this->_team_id = $team;
        }

        /**
         * Returns the associated client
         *
         * @return \thebuggenie\core\entities\Client
         */
        public function getClient()
        {
            return $this->_b2dbLazyload('_client_id');
        }

        public function setClient($client)
        {
            $this->_client_id = $client;
        }

        /**
         * Returns the associated project
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project_id');
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
        }

        public function countViews()
        {
            if (is_array($this->_dashboard_views))
            {
                return count($this->_dashboard_views);
            }
            return $this->_b2dbLazycount('_dashboard_views');
        }

        public function getViews()
        {
            return $this->_b2dbLazyload('_dashboard_views');
        }

        public function getName()
        {
            return $this->_name;
        }

        public function setName($name)
        {
            $this->_name = $name;
        }

        public function getIsDefault()
        {
            return $this->_is_default;
        }

        public function setIsDefault($is_default)
        {
            $this->_is_default = $is_default;
        }

        public function getLayout()
        {
            return $this->_layout;
        }

        public function setLayout($layout)
        {
            $this->_layout = $layout;
        }

        public function canEdit()
        {
            if ($this->getProject() instanceof \thebuggenie\core\entities\Project)
            {
                return framework\Context::getUser()->canEditProjectDetails($this->getProject());
            }
            elseif ($this->getUser() instanceof \thebuggenie\core\entities\User)
            {
                return $this->getUser()->getID() == framework\Context::getUser()->getID();
            }
        }

        public function getType()
        {
            if ($this->getProject() instanceof \thebuggenie\core\entities\Project)
                return self::TYPE_PROJECT;
            if ($this->getUser() instanceof \thebuggenie\core\entities\User)
                return self::TYPE_USER;
            if ($this->getClient() instanceof \thebuggenie\core\entities\Client)
                return self::TYPE_CLIENT;
            if ($this->getTeam() instanceof \thebuggenie\core\entities\Team)
                return self::TYPE_TEAM;
        }

    }
