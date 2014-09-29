<?php

    namespace thebuggenie\core\entities;

    use TBGContext,
        TBGProject,
        TBGUser,
        TBGTeam,
        TBGClient;

    /**
     * Dashboard class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Dashboard class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\b2db\Dashboards")
     */
    class Dashboard extends \TBGIdentifiableScopedClass
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
         * @var \TBGUser
         * @Column(type="integer", length=10)
         * @Relates(class="\TBGUser")
         */
        protected $_user_id;

        /**
         * @var \TBGProject
         * @Column(type="integer", length=10)
         * @Relates(class="\TBGProject")
         */
        protected $_project_id;

        /**
         * @var \TBGTeam
         * @Column(type="integer", length=10)
         * @Relates(class="\TBGTeam")
         */
        protected $_team_id;

        /**
         * @var \TBGClient
         * @Column(type="integer", length=10)
         * @Relates(class="\TBGClient")
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

        /**
         * Returns the associated user
         *
         * @return \TBGUser
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
         * @return \TBGTeam
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
         * @return \TBGClient
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
         * @return \TBGProject
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
            if ($this->getProject() instanceof TBGProject)
            {
                return TBGContext::getUser()->canEditProjectDetails($this->getProject());
            }
            elseif ($this->getUser() instanceof TBGUser)
            {
                return $this->getUser()->getID() == TBGContext::getUser()->getID();
            }
        }

        public function getType()
        {
            if ($this->getProject() instanceof TBGProject)
                return self::TYPE_PROJECT;
            if ($this->getUser() instanceof TBGUser)
                return self::TYPE_USER;
            if ($this->getClient() instanceof TBGClient)
                return self::TYPE_CLIENT;
            if ($this->getTeam() instanceof TBGTeam)
                return self::TYPE_TEAM;
        }

    }
