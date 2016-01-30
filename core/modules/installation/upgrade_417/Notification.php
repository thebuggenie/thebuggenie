<?php

    namespace thebuggenie\core\modules\installation\upgrade_417;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Notification item class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_417\NotificationsTable")
     */
    class Notification extends IdentifiableScoped
    {

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_id;

        /**
         * @Column(type="string", length=100)
         */
        protected $_notification_type;

        /**
         * @Column(type="string", length=50, default="core")
         */
        protected $_module_name = 'core';

        /**
         * @Column(type="boolean", default="0")
         */
        protected $_is_read = false;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * Who triggered the notification
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_triggered_by_user_id;

        /**
         * Who the notification is for
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_user_id;

    }
