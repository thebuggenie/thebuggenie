<?php

    namespace thebuggenie\modules\mailing\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * @Table(name="mailing_incoming_email_account_32")
     */
    class TBGIncomingEmailAccountTable extends ScopedTable
    {

        const B2DBNAME = 'mailing_incoming_email_account';
        const ID = 'mailing_incoming_email_account.id';
        const NAME = 'mailing_incoming_email_account.name';
        const USERNAME = 'mailing_incoming_email_account.username';
        const PASSWORD = 'mailing_incoming_email_account.password';
        const SERVER = 'mailing_incoming_email_account.server';
        const PORT = 'mailing_incoming_email_account.port';
        const SERVER_TYPE = 'mailing_incoming_email_account.server_type';
        const SSL = 'mailing_incoming_email_account.ssl';
        const KEEP_EMAIL = 'mailing_incoming_email_account.keep_email';
        const PROJECT = 'mailing_incoming_email_account.project';
        const ISSUETYPE = 'mailing_incoming_email_account.issuetype';
        const NUM_LAST_FETCHED = 'mailing_incoming_email_account.num_last_fetched';
        const TIME_LAST_FETCHED = 'mailing_incoming_email_account.time_last_fetched';
        const SCOPE = 'mailing_incoming_email_account.scope';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::NAME, 200);
            parent::addVarchar(self::SERVER, 200);
            parent::addInteger(self::PORT, 3);
            parent::addInteger(self::SERVER_TYPE, 10);
            parent::addBoolean(self::SSL);
            parent::addBoolean(self::KEEP_EMAIL);
            parent::addVarchar(self::USERNAME, 200);
            parent::addVarchar(self::PASSWORD, 200);
            parent::addInteger(self::PROJECT, 10);
            parent::addInteger(self::ISSUETYPE, 10);
            parent::addInteger(self::NUM_LAST_FETCHED, 10);
            parent::addInteger(self::TIME_LAST_FETCHED, 10);
            parent::addInteger(self::SCOPE, 10);
        }

    }
