<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    /**
     * Milestone class, version 3.2
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Milestone class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_32\TBGMilestonesTable")
     */
    class TBGMilestone extends \TBGIdentifiableScopedClass
    {

        /**
         * This milestone's project
         *
         * @var \TBGProject
         * @Column(type="integer", length=10)
         * @Relates(class="\TBGProject")
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
         * The milestone description
         *
         * @var string
         * @Column(type="text")
         */
        protected $_description;

    }
