<?php

    namespace thebuggenie\core\modules\installation\upgrade_421;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_421\IncomingEmailAccounts")
     */
    class IncomingEmailAccount extends IdentifiableScoped
    {

        const SERVER_IMAP = 0;
        const SERVER_POP3 = 1;

        /**
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="string", length=200)
         */
        protected $_server;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_port;

        /**
         * @Column(type="string", length=200)
         */
        protected $_folder;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_server_type;

        /**
         * @Column(type="boolean")
         */
        protected $_ssl;

        /**
         * @Column(type="boolean")
         */
        protected $_ignore_certificate_validation = false;

        /**
         * @Column(type="boolean")
         */
        protected $_plaintext_authentication = false;

        /**
         * @Column(type="boolean")
         */
        protected $_keep_email;

        /**
         * @Column(type="string", length=200)
         */
        protected $_username;

        /**
         * @Column(type="string", length=200)
         */
        protected $_password;

        /**
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project;

        /**
         * @var \thebuggenie\core\entities\Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issuetype")
         */
        protected $_issuetype;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_num_last_fetched = 0;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_time_last_fetched = 0;

    }
