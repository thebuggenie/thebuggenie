<?php

    namespace thebuggenie\core\modules\installation\upgrade_41;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Milestone class
     *
     * @package thebuggenie
     * @subpackage main
     *
         * @method \thebuggenie\core\entities\tables\Milestones getB2DBTable Returns an instance of the associated table object
         *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_41\MilestonesTable")
     */
    class Milestone extends IdentifiableScoped
    {
        /**
         * This milestone's project
         *
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_itemtype;

        /**
         * Whether the milestone has been closed
         *
         * @var boolean
         * @Column(type="boolean", default=false)
         */
        protected $_closed;

        /**
         * When the milestone was reached
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_reacheddate;

        /**
         * When the milestone is scheduled for release
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_scheduleddate;

        /**
         * When the milestone is scheduled to start
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_startingdate;

        /**
         * Whether the milestone is visible on the project roadmap
         *
         * @var boolean
         * @Column(type="boolean", default=true)
         */
        protected $_visible_roadmap = true;

        /**
         * Whether the milestone is available for issues
         *
         * @var boolean
         * @Column(type="boolean", default=true)
         */
        protected $_visible_issues = true;

        /**
         * The milestone description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description;

        /**
         * Sort order of this item
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order = null;

    }
