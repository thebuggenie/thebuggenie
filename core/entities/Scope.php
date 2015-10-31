<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\tables\IssueFields,
        thebuggenie\core\entities\tables\Links,
        thebuggenie\core\entities\tables\Scopes,
        thebuggenie\core\entities\tables\ScopeHostnames,
        thebuggenie\core\entities\tables\Settings,
        thebuggenie\core\entities\tables\Users,
        thebuggenie\core\entities\tables\WorkflowIssuetype,
        thebuggenie\core\entities\common\Identifiable;
    use thebuggenie\core\framework;

    /**
     * The scope class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * The scope class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Scopes")
     */
    class Scope extends Identifiable
    {

        protected static $_scopes = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = '';

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enabled = false;

        /**
         * @var string
         */
        protected $_shortname = '';

        protected $_administrator = null;

        protected $_hostnames = null;

        protected $_is_secure = false;

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_uploads_enabled = true;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_upload_limit = 0;

        /**
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_custom_workflows_enabled = true;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_workflows = 0;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_users = 0;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_projects = 0;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_teams = 0;

        /**
         * @Relates(class="\thebuggenie\core\entities\Project", collection=true, foreign_column="scope")
         */
        protected $_projects = null;

        /**
         * @Relates(class="\thebuggenie\core\entities\Issue", collection=true, foreign_column="scope")
         */
        protected $_issues = null;

        /**
         * Return all available scopes
         *
         * @return array|\thebuggenie\core\entities\Scope
         */
        static function getAll()
        {
            if (self::$_scopes === null)
            {
                self::$_scopes = tables\Scopes::getTable()->selectAll();
            }

            return self::$_scopes;
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

        public function isEnabled()
        {
            return $this->_enabled;
        }

        public function isDefault()
        {
            return in_array('*', $this->getHostnames());
        }

        public function setEnabled($enabled = true)
        {
            $this->_enabled = (bool) $enabled;
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function setDescription($description)
        {
            $this->_description = $description;
        }

        protected function _populateHostnames()
        {
            if ($this->_hostnames === null)
            {
                if ($this->_id)
                    $this->_hostnames = tables\ScopeHostnames::getTable()->getHostnamesForScope($this->getID());
                else
                    $this->_hostnames = array();
            }
        }

        public function getHostnames()
        {
            $this->_populateHostnames();
            return $this->_hostnames;
        }

        public function addHostname($hostname)
        {
            $hostname = trim($hostname, "/");
            $this->_populateHostnames();
            $this->_hostnames[] = $hostname;
        }

        /**
         * Returns the scope administrator
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getScopeAdmin()
        {
            if (!$this->_administrator instanceof \thebuggenie\core\entities\User && $this->_administrator != 0)
            {
                try
                {
                    $this->_administrator = tables\Users::getTable()->selectById($this->_administrator);
                }
                catch (\Exception $e) { }
            }
            return $this->_administrator;
        }

        protected function _preDelete()
        {
            $tables = array(
                '\thebuggenie\core\entities\tables\IssueCustomFields',
                '\thebuggenie\core\entities\tables\IssueAffectsEdition',
                '\thebuggenie\core\entities\tables\IssueAffectsBuild',
                '\thebuggenie\core\entities\tables\IssueAffectsComponent',
                '\thebuggenie\core\entities\tables\IssueFiles',
                '\thebuggenie\core\entities\tables\IssueRelations',
                '\thebuggenie\core\entities\tables\IssuetypeSchemeLink',
                '\thebuggenie\core\entities\tables\IssuetypeSchemes',
                '\thebuggenie\core\entities\tables\IssueTypes',
                '\thebuggenie\core\entities\tables\ListTypes',
                '\thebuggenie\core\entities\tables\Issues',
                '\thebuggenie\core\entities\tables\Comments',
                '\thebuggenie\core\entities\tables\ProjectAssignedTeams',
                '\thebuggenie\core\entities\tables\ProjectAssignedUsers',
                '\thebuggenie\core\entities\tables\Components',
                '\thebuggenie\core\entities\tables\Editions',
                '\thebuggenie\core\entities\tables\Builds',
                '\thebuggenie\core\entities\tables\Milestones',
                '\thebuggenie\core\entities\tables\Issues',
                '\thebuggenie\core\entities\tables\Projects',
                '\thebuggenie\core\entities\tables\UserScopes',
                '\thebuggenie\core\entities\tables\Dashboards',
                '\thebuggenie\core\entities\tables\DashboardViews',
                '\thebuggenie\core\entities\tables\ScopeHostnames',
                '\thebuggenie\core\entities\tables\Settings'
            );
            foreach($tables as $table)
            {
                $table::getTable()->deleteFromScope($this->getID());
            }
        }

        protected function _postSave($is_new)
        {
            tables\ScopeHostnames::getTable()->saveScopeHostnames($this->getHostnames(), $this->getID());
            // Load fixtures for this scope if it's a new scope
            if ($is_new)
            {
                if (!$this->isDefault())
                {
                    $prev_scope = framework\Context::getScope();
                    framework\Context::setScope($this);
                }
                $this->loadFixtures();
                if (!$this->isDefault())
                {
                    Module::installModule('publish', $this);
                    framework\Context::setScope($prev_scope);
                    framework\Context::clearPermissionsCache();
                }
            }
        }

        public function _construct(\b2db\Row $row, $foreign_key = null)
        {
            if (framework\Context::isCLI())
            {
                $this->_hostname = php_uname('n');
            }
            else
            {
                $hostprefix = (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
                $this->_is_secure = (bool) ($hostprefix == 'https');
                if(isset($_SERVER["HTTP_X_FORWARDED_HOST"]) && $_SERVER["HTTP_X_FORWARDED_HOST"]!="")
                {
                   $this->_hostname = "{$hostprefix}://{$_SERVER["HTTP_X_FORWARDED_HOST"]}";
                }
                else
                {
                    $this->_hostname = "{$hostprefix}://{$_SERVER['SERVER_NAME']}";
                }
                $port = $_SERVER['SERVER_PORT'];
                if ($port != 80)
                {
                    $this->_hostname .= ":{$port}";
                }
            }
        }

        public function isSecure()
        {
            return $this->_is_secure;
        }

        public function getCurrentHostname($clean = false)
        {
            if ($clean)
            {
                // a scheme is needed before php 5.4.7
                // thus, let's add the prefix http://
                if (!stristr($this->_hostname,'http'))
                {
                    $url = parse_url('http://'.$this->_hostname);
                }
                else
                {
                    $url = parse_url($this->_hostname);
                }
                return $url['host'];
            }
            return $this->_hostname;
        }

        public function loadFixtures()
        {
            // Load initial settings
            tables\Settings::getTable()->loadFixtures($this);
            \thebuggenie\core\framework\Settings::loadSettings();

            // Load group, users and permissions fixtures
            Group::loadFixtures($this);

            // Load initial teams
            Team::loadFixtures($this);

            // Set up user states, like "available", "away", etc
            Userstate::loadFixtures($this);

            // Set up data types
            list($b_id, $f_id, $e_id, $t_id, $u_id, $i_id, $ep_id) = Issuetype::loadFixtures($this);
            $scheme = IssuetypeScheme::loadFixtures($this);
            tables\IssueFields::getTable()->loadFixtures($this, $scheme, $b_id, $f_id, $e_id, $t_id, $u_id, $i_id, $ep_id);
            Datatype::loadFixtures($this);

            // Set up workflows
            Workflow::loadFixtures($this);
            WorkflowScheme::loadFixtures($this);
            tables\WorkflowIssuetype::getTable()->loadFixtures($this);

            // Set up left menu links
            tables\Links::getTable()->loadFixtures($this);
        }

        public function isUploadsEnabled()
        {
            return ($this->isDefault() || $this->_uploads_enabled);
        }

        public function setUploadsEnabled($enabled = true)
        {
            $this->_uploads_enabled = $enabled;
        }

        public function isCustomWorkflowsEnabled()
        {
            return ($this->isDefault() || $this->_custom_workflows_enabled);
        }

        public function setCustomWorkflowsEnabled($enabled = true)
        {
            $this->_custom_workflows_enabled = $enabled;
        }

        public function setMaxWorkflowsLimit($limit)
        {
            $this->_max_workflows = $limit;
        }

        public function getMaxWorkflowsLimit()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_workflows;
        }

        public function hasCustomWorkflowsAvailable()
        {
            if ($this->isCustomWorkflowsEnabled())
                return ($this->getMaxWorkflowsLimit()) ? (Workflow::getCustomWorkflowsCount() < $this->getMaxWorkflowsLimit()) : true;
            else
                return false;
        }

        public function setMaxUploadLimit($limit)
        {
            $this->_max_upload_limit = $limit;
        }

        public function getMaxUploadLimit()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_upload_limit;
        }

        public function getMaxUsers()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_users;
        }

        public function setMaxUsers($limit)
        {
            $this->_max_users = $limit;
        }

        public function hasUsersAvailable()
        {
            return ($this->getMaxUsers()) ? (\thebuggenie\core\entities\User::getUsersCount() < $this->getMaxUsers()) : true;
        }

        public function getMaxProjects()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_projects;
        }

        public function setMaxProjects($limit)
        {
            $this->_max_projects = $limit;
        }

        public function hasProjectsAvailable()
        {
            return ($this->getMaxProjects()) ? (Project::getProjectsCount() < $this->getMaxProjects()) : true;
        }

        public function getMaxTeams()
        {
            return ($this->isDefault()) ? 0 : (int) $this->_max_teams;
        }

        public function setMaxTeams($limit)
        {
            $this->_max_teams = $limit;
        }

        public function hasTeamsAvailable()
        {
            return ($this->getMaxTeams()) ? (Team::countAll() < $this->getMaxTeams()) : true;
        }

        public function getNumberOfProjects()
        {
            return (int) $this->_b2dbLazycount('_projects');
        }

        public function getNumberOfIssues()
        {
            return (int) $this->_b2dbLazycount('_issues');
        }

    }
