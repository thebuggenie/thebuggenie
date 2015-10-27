<?php

    namespace thebuggenie\core\modules\installation\upgrade_411;

    use thebuggenie\core\entities\common\QaLeadable,
      thebuggenie\core\helpers\MentionableProvider;

    /**
     * Project class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_411\ProjectsTable")
     */
    class Project extends QaLeadable implements MentionableProvider
    {
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
         * The projects homepage
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_homepage = '';

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

        public function getMentionableUsers()
        {
        }

    }
