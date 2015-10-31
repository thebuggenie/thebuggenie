<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    use thebuggenie\core\framework as framework,
        \thebuggenie\core\entities\Project,
        \thebuggenie\core\entities\User,
        \thebuggenie\core\entities\Team,
        \thebuggenie\core\entities\Client,
        \thebuggenie\core\entities\Issuetype;

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
     * @Table(name="\thebuggenie\core\entities\tables\DashboardViews")
     */
    class DashboardView extends IdentifiableScoped
    {

        const VIEW_PREDEFINED_SEARCH = 1;
        const VIEW_SAVED_SEARCH = 2;
        const VIEW_LOGGED_ACTIONS = 3;
        const VIEW_RECENT_COMMENTS = 4;
        const VIEW_FRIENDS = 5;
        const VIEW_PROJECTS = 6;
        const VIEW_MILESTONES = 7;
        const VIEW_PROJECT_INFO = 101;
        const VIEW_PROJECT_TEAM = 102;
        const VIEW_PROJECT_CLIENT = 103;
        const VIEW_PROJECT_SUBPROJECTS = 104;
        const VIEW_PROJECT_STATISTICS_LAST15 = 105;
        const VIEW_PROJECT_STATISTICS_PRIORITY = 106;
        const VIEW_PROJECT_STATISTICS_STATUS = 111;
        const VIEW_PROJECT_STATISTICS_WORKFLOW_STEP = 115;
        const VIEW_PROJECT_STATISTICS_RESOLUTION = 112;
        const VIEW_PROJECT_STATISTICS_STATE = 113;
        const VIEW_PROJECT_STATISTICS_CATEGORY = 114;
        const VIEW_PROJECT_STATISTICS_SEVERITY = 116;
        const VIEW_PROJECT_RECENT_ISSUES = 107;
        const VIEW_PROJECT_RECENT_ACTIVITIES = 108;
        const VIEW_PROJECT_UPCOMING = 109;
        const VIEW_PROJECT_DOWNLOADS = 110;
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
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_column = 1;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_view;

        /**
         * @var \thebuggenie\core\entities\Dashboard
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Dashboard")
         */
        protected $_dashboard_id;

        public static function getViews($tid, $target_type)
        {
            $views = self::getB2DBTable()->getViews($tid, $target_type);
            return $views;
        }

        public static function getUserViews($user_id)
        {
            return self::getViews($user_id, self::TYPE_USER);
        }

        public static function getProjectViews($project_id)
        {
            return self::getViews($project_id, self::TYPE_PROJECT);
        }

        public static function getAvailableViews($target_type)
        {
            $i18n = framework\Context::getI18n();
            $searches = array('info' => array(), 'searches' => array());
            switch ($target_type)
            {
                case self::TYPE_USER:
                    $searches['info'][self::VIEW_LOGGED_ACTIONS] = array(0 => array('title' => $i18n->__("What you've done recently"), 'description' => $i18n->__('A widget that shows your most recent actions, such as issue edits, wiki edits and more')));
                    if (framework\Context::getUser()->canViewComments())
                    {
                        $searches['info'][self::VIEW_RECENT_COMMENTS] = array(0 => array('title' => $i18n->__('Recent comments'), 'description' => $i18n->__('Shows a list of your most recent comments')));
                    }
                    $searches['searches'][self::VIEW_PREDEFINED_SEARCH] = array(\thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_REPORTED_ISSUES => array('title' => $i18n->__('Issues reported by me'), 'description' => $i18n->__('Shows a list of all issues you have reported, across all projects')),
                        \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES => array('title' => $i18n->__('Open issues assigned to me'), 'description' => $i18n->__('Shows a list of all issues assigned to you')),
                        \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES => array('title' => $i18n->__('Open issues owned by me'), 'description' => $i18n->__('Shows a list of all issues owned by you')),
                        \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES => array('title' => $i18n->__('Open issues assigned to my teams'), 'description' => $i18n->__('Shows all issues assigned to any of your teams')));
                    $searches['info'][self::VIEW_PROJECTS] = array(0 => array('title' => $i18n->__("Your projects"), 'description' => $i18n->__('A widget that shows projects you are involved in')));
                    $searches['info'][self::VIEW_MILESTONES] = array(0 => array('title' => $i18n->__("Upcoming milestones / sprints"), 'description' => $i18n->__('A widget that shows all upcoming milestones or sprints for any projects you are involved in')));
                    break;
                case self::TYPE_PROJECT:
                    $searches['statistics'] = array();

                    $issuetype_icons = array();
                    foreach (Issuetype::getAll() as $id => $issuetype)
                    {
                        $issuetype_icons[$id] = array('title' => $i18n->__('Recent issues: %issuetype', array('%issuetype' => $issuetype->getName())), 'description' => $i18n->__('Show recent issues of type %issuetype', array('%issuetype' => $issuetype->getName())));
                    }

                    $searches['info'][self::VIEW_PROJECT_INFO] = array(0 => array('title' => $i18n->__('About this project'), 'description' => $i18n->__('Basic project information widget, showing project name, important people and links')));
                    $searches['info'][self::VIEW_PROJECT_TEAM] = array(0 => array('title' => $i18n->__('Project team'), 'description' => $i18n->__('A widget with information about project developers and the project team and their respective project roles')));
                    $searches['info'][self::VIEW_PROJECT_CLIENT] = array(0 => array('title' => $i18n->__('Project client'), 'description' => $i18n->__('Shows information about the associated project client (if any)')));
                    $searches['info'][self::VIEW_PROJECT_SUBPROJECTS] = array(0 => array('title' => $i18n->__('Subprojects'), 'description' => $i18n->__('Lists all subprojects of this project, with quick links to report an issue, open the project wiki and more')));
                    $searches['info'][self::VIEW_PROJECT_RECENT_ACTIVITIES] = array(0 => array('title' => $i18n->__('Recent activities'), 'description' => $i18n->__('Displays project timeline')));
                    $searches['info'][self::VIEW_PROJECT_UPCOMING] = array(0 => array('title' => $i18n->__('Upcoming milestones and deadlines'), 'description' => $i18n->__('A widget showing a list of upcoming milestones and deadlines for the next three weeks')));
                    $searches['info'][self::VIEW_PROJECT_DOWNLOADS] = array(0 => array('title' => $i18n->__('Latest downloads'), 'description' => $i18n->__('Lists recent downloads released in the release center')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_LAST15] = array(0 => array('title' => $i18n->__('Graph of closed vs open issues'), 'description' => $i18n->__('Shows a line graph comparing closed vs open issues for the past 15 days')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_PRIORITY] = array(0 => array('title' => $i18n->__('Statistics by priority'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by priority')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_SEVERITY] = array(0 => array('title' => $i18n->__('Statistics by severity'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by severity')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_CATEGORY] = array(0 => array('title' => $i18n->__('Statistics by category'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by category')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_STATUS] = array(0 => array('title' => $i18n->__('Statistics by status'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by status')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_RESOLUTION] = array(0 => array('title' => $i18n->__('Statistics by resolution'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by resolution')));
                    $searches['statistics'][self::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP] = array(0 => array('title' => $i18n->__('Statistics by workflow step'), 'description' => $i18n->__('Displays a bar graph of open and closed issues grouped by current workflow step')));
                    $searches['searches'][self::VIEW_PROJECT_RECENT_ISSUES] = $issuetype_icons;
                    break;
            }

            return $searches;
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        public function getType()
        {
            return $this->_name;
        }

        public function setType($type)
        {
            $this->_name = $type;
        }

        public function getDetail()
        {
            return $this->_view;
        }

        public function setDetail($detail)
        {
            $this->_view = $detail;
        }

        public function getProjectID()
        {
            return $this->getProject()->getID();
        }

        /**
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->getDashboard()->getProject();
        }

        public function getTargetType()
        {
            if ($this->getDashboard()->getUser() instanceof \thebuggenie\core\entities\User)
                return self::TYPE_USER;
            if ($this->getDashboard()->getProject() instanceof \thebuggenie\core\entities\Project)
                return self::TYPE_PROJECT;
            if ($this->getDashboard()->getTeam() instanceof \thebuggenie\core\entities\Team)
                return self::TYPE_TEAM;
            if ($this->getDashboard()->getClient() instanceof \thebuggenie\core\entities\Client)
                return self::TYPE_CLIENT;
        }

        public function isSearchView()
        {
            return (in_array($this->getType(), array(
                        self::VIEW_PREDEFINED_SEARCH,
                        self::VIEW_SAVED_SEARCH
            )));
        }

        public function hasRSS()
        {
            return (in_array($this->getType(), array(
                        self::VIEW_PREDEFINED_SEARCH,
                        self::VIEW_SAVED_SEARCH,
                        self::VIEW_PROJECT_RECENT_ACTIVITIES
            )));
        }

        public function hasJS()
        {
            return (in_array($this->getType(), array(
                        self::VIEW_PROJECT_STATISTICS_LAST15,
            )));
        }

        public function getJS()
        {
            return array('excanvas', 'jquery.flot', 'jquery.flot.resize', 'jquery.flot.time');
        }

        public function getRSSUrl()
        {
            switch ($this->getType())
            {
                case self::VIEW_PREDEFINED_SEARCH:
                case self::VIEW_SAVED_SEARCH:
                    return framework\Context::getRouting()->generate('search', $this->getSearchParameters(true));
                    break;
                case self::VIEW_PROJECT_RECENT_ACTIVITIES:
                    return framework\Context::getRouting()->generate('project_timeline', array('project_key' => $this->getProject()->getKey(), 'format' => 'rss'));
                    break;
            }
        }

        public function getSearchParameters($rss = false)
        {
            $parameters = ($rss) ? array('format' => 'rss') : array();
            switch ($this->getType())
            {
                case self::VIEW_PREDEFINED_SEARCH :
                    $parameters['predefined_search'] = $this->getDetail();
                    break;
                case self::VIEW_SAVED_SEARCH :
                    $parameters['saved_search'] = $this->getDetail();
                    break;
            }
            return $parameters;
        }

        public function shouldBePreloaded()
        {
            return (boolean) in_array($this->getType(), array(self::VIEW_FRIENDS,
                        self::VIEW_PROJECT_DOWNLOADS,
                        self::VIEW_PROJECT_INFO,
                        self::VIEW_PROJECT_UPCOMING));
        }

        public function getTitle()
        {
            $all_titles = self::getAvailableViews($this->getTargetType());
            foreach ($all_titles as $type => $titles)
            {
                if (array_key_exists($this->getType(), $titles) && array_key_exists($this->getDetail(), $titles[$this->getType()]))
                {
                    $title = $titles[$this->getType()][$this->getDetail()]['title'];
                    break;
                }
            }
            return (isset($title)) ? $title : framework\Context::getI18n()->__('Unknown dashboard item');
        }

        public function setDashboard($dashboard)
        {
            $this->_dashboard_id = $dashboard;
        }

        /**
         * @return \thebuggenie\core\entities\Dashboard
         */
        public function getDashboard()
        {
            return $this->_b2dbLazyload('_dashboard_id');
        }

        public function getTemplate()
        {
            switch ($this->getType())
            {
                case self::VIEW_PREDEFINED_SEARCH:
                case self::VIEW_SAVED_SEARCH:
                    return 'search/results_view';
                case self::VIEW_PROJECT_INFO:
                    return 'project/dashboardviewprojectinfo';
                case self::VIEW_PROJECT_TEAM:
                    return 'project/dashboardviewprojectteam';
                case self::VIEW_PROJECT_CLIENT:
                    return 'project/dashboardviewprojectclient';
                case self::VIEW_PROJECT_SUBPROJECTS:
                    return 'project/dashboardviewprojectsubprojects';
                case self::VIEW_PROJECT_STATISTICS_LAST15:
                    return 'project/dashboardviewprojectstatisticslast15';
                case self::VIEW_PROJECT_RECENT_ISSUES:
                    return 'project/dashboardviewprojectrecentissues';
                case self::VIEW_PROJECT_RECENT_ACTIVITIES:
                    return 'project/dashboardviewprojectrecentactivities';
                case self::VIEW_PROJECT_STATISTICS_CATEGORY:
                case self::VIEW_PROJECT_STATISTICS_PRIORITY:
                case self::VIEW_PROJECT_STATISTICS_SEVERITY:
                case self::VIEW_PROJECT_STATISTICS_RESOLUTION:
                case self::VIEW_PROJECT_STATISTICS_STATE:
                case self::VIEW_PROJECT_STATISTICS_STATUS:
                case self::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP:
                    return 'project/dashboardviewprojectstatistics';
                case self::VIEW_PROJECT_UPCOMING:
                    return 'project/dashboardviewprojectupcoming';
                case self::VIEW_PROJECT_DOWNLOADS:
                    return 'project/dashboardviewprojectdownloads';
                case self::VIEW_RECENT_COMMENTS:
                    return 'main/dashboardviewrecentcomments';
                case self::VIEW_LOGGED_ACTIONS:
                    return 'main/dashboardviewloggedactions';
                case self::VIEW_MILESTONES:
                    return 'main/dashboardviewusermilestones';
                case self::VIEW_PROJECTS:
                    return 'main/dashboardviewuserprojects';
            }
        }

        public function getColumn()
        {
            return $this->_column;
        }

        public function setColumn($column)
        {
            $this->_column = $column;
        }

        public function getSortOrder()
        {
            return $this->_sort_order;
        }

        public function setSortOrder($sort_order)
        {
            $this->_sort_order = $sort_order;
        }

    }
