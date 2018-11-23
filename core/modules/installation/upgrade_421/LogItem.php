<?php

    namespace thebuggenie\core\modules\installation\upgrade_421;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Log item class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_421\LogItems")
     */
    class LogItem extends IdentifiableScoped
    {

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_change_type;

        /**
         * @Column(type="text")
         */
        protected $_previous_value;

        /**
         * @Column(type="text")
         */
        protected $_current_value;

        /**
         * @Column(type="text")
         */
        protected $_text;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_time;

        /**
         * Who posted the comment
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_uid;

        /**
         * Related comment
         *
         * @var \thebuggenie\core\entities\Comment
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Comment")
         */
        protected $_comment_id;

    }
