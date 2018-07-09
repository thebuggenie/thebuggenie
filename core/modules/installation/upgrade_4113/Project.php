<?php

    namespace thebuggenie\core\modules\installation\upgrade_4113;

    use thebuggenie\core\entities\common\QaLeadable,
        thebuggenie\core\helpers\MentionableProvider;

    /**
     * Project class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Project class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_4113\ProjectsTable")
     */
    class Project extends QaLeadable implements MentionableProvider
    {
        /**
         * New issues lock type project and category access
         *
         * @static integer
         */
        const ISSUES_LOCK_TYPE_PUBLIC_CATEGORY = 0;

        /**
         * New issues lock type project access
         *
         * @static integer
         */
        const ISSUES_LOCK_TYPE_PUBLIC = 1;

        /**
         * New issues lock type restricted access to poster
         *
         * @static integer
         */
        const ISSUES_LOCK_TYPE_RESTRICTED = 2;

        /**
         * Project list cache
         *
         * @var array
         */
        protected static $_projects = null;

        protected static $_num_projects = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The project prefix
         *
         * @var string
         * @Column(type="string", length=25)
         */
        protected $_prefix = '';

        /**
         * Whether or not the project uses prefix
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_use_prefix = false;

        /**
         * Whether the item is locked or not
         *
         * @var boolean
         * @access protected
         * @Column(type="boolean")
         */
        protected $_locked = null;

        /**
         * Whether or not the project uses sprint planning
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_use_scrum = true;

        /**
         * Whether or not the project uses builds
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_builds = true;

        /**
         * Edition builds
         *
         * @var array|\thebuggenie\core\entities\Build
         */
        protected $_builds = null;

        /**
         * Whether or not the project uses editions
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_editions = null;

        /**
         * Whether or not the project uses components
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enable_components = null;

        /**
         * Project key
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_key = null;

        /**
         * List of editions for this project
         *
         * @var array|Edition
         * @Relates(class="\thebuggenie\core\entities\Edition", collection=true, foreign_column="project")
         */
        protected $_editions = null;

        /**
         * The projects homepage
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_homepage = '';

        /**
         * List of milestones for this project
         *
         * @var array|Milestone
         * @Relates(class="\thebuggenie\core\entities\Milestone", collection=true, foreign_column="project", orderby="sort_order")
         */
        protected $_milestones = null;

        /**
         * List of components for this project
         *
         * @var array|Component
         * @Relates(class="\thebuggenie\core\entities\Component", collection=true, foreign_column="project", orderby="name")
         */
        protected $_components = null;

        /**
         * Count of issues registered for this project
         *
         * @var array
         */
        protected $_issuecounts = null;

        /**
         * The small project icon, if set
         *
         * @var \thebuggenie\core\entities\File
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\File")
         */
        protected $_small_icon = null;

        /**
         * The large project icon, if set
         *
         * @var \thebuggenie\core\entities\File
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\File")
         */
        protected $_large_icon = null;

        /**
         * Issues registered for this project with no milestone assigned
         *
         * @var array
         */
        protected $_unassignedissues = null;

        /**
         * Developer reports registered for this project with no milestone assigned
         *
         * @var array
         */
        protected $_unassignedstories = null;

        /**
         * The projects documentation URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_doc_url = '';

        /**
         * The projects wiki URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_wiki_url = '';

        /**
         * The project description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description = '';

        /**
         * Available / applicable issue types for this project
         *
         * @var array
         */
        protected $_issuetypes = null;

        /**
         * Issue types visible in the frontpage summary
         *
         * @var array
         */
        protected $_visible_issuetypes = null;

        /**
         * Milestones visible in the frontpage summary
         *
         * @var array
         */
        protected $_visible_milestones = null;

        /**
         * Whether or not this project is visible in the frontpage summary
         *
         * @var boolean
         * @Column(type="boolean", default=true)
         */
        protected $_show_in_summary = null;

        /**
         * What to show on the frontpage summary
         *
         * @var string
         * @Column(type="string", length=15, default="issuetypes")
         */
        protected $_summary_display = null;

        /**
         * @Relates(class="\thebuggenie\core\entities\User", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\ProjectAssignedUsers")
         */
        protected $_assigned_users;

        protected $_user_roles = null;

        /**
         * @Relates(class="\thebuggenie\core\entities\Team", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\ProjectAssignedTeams")
         */
        protected $_assigned_teams;

        protected $_team_roles = null;

        /**
         * List of issue fields per issue type
         *
         * @var array
         */
        protected $_fieldsarrays = array();

        /**
         * Whether a user can change details about an issue without working on the issue
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_allow_freelancing = false;

        /**
         * Is project deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = 0;

        /**
         * Set to true if the project is set to be deleted, but not saved yet
         *
         * @var boolean
         */
        protected $_dodelete = false;

        /**
         * Recent log items
         *
         * @var array
         */
        protected $_recentlogitems = null;

        /**
         * Recent important log items
         *
         * @var array
         */
        protected $_recentimportantlogitems = null;

        /**
         * Recent issues reported
         *
         * @var array
         */
        protected $_recentissues = array();

        /**
         * Priority count
         *
         * @var array
         */
        protected $_prioritycount = null;

        /**
         * Severity count
         *
         * @var array
         */
        protected $_severitycount = null;

        /**
         * Workflow step count
         *
         * @var array
         */
        protected $_workflowstepcount = null;

        /**
         * Status count
         *
         * @var array
         */
        protected $_statuscount = null;

        /**
         * Category count
         *
         * @var array
         */
        protected $_categorycount = null;

        /**
         * Resolution count
         *
         * @var array
         */
        protected $_resolutioncount = null;

        /**
         * State count
         *
         * @var array
         */
        protected $_statecount = null;

        /**
         * The selected workflow scheme
         *
         * @var \thebuggenie\core\entities\WorkflowScheme
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\WorkflowScheme")
         */
        protected $_workflow_scheme_id = 1;

        /**
         * The selected workflow scheme
         *
         * @var \thebuggenie\core\entities\IssuetypeScheme
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\IssuetypeScheme")
         */
        protected $_issuetype_scheme_id = 1;

        /**
         * Assigned client
         *
         * @var \thebuggenie\core\entities\Client
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Client")
         */
        protected $_client = null;

        /**
         * Autoassignment
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_autoassign = null;

        /**
         * Parent project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_parent = null;

        /**
         * Child projects
         *
         * @var Array
         */
        protected $_children = null;

        /**
         * Recent activities
         *
         * @var Array
         */
        protected $_recentactivities = null;

        /**
         * Whether to show a "Download" link and corresponding section
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_has_downloads = true;

        /**
         * Whether a project is archived (read-only mode)
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_archived = false;

        /**
         * List of project's dashboards
         *
         * @var array|\thebuggenie\core\entities\Dashboard
         * @Relates(class="\thebuggenie\core\entities\Dashboard", collection=true, foreign_column="project_id", orderby="name")
         */
        protected $_dashboards = null;

        public function getMentionableUsers()
        {
        }

    }
