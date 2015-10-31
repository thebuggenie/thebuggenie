<?php

    namespace thebuggenie\modules\mailing\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;
    use b2db\Criteria;

    /**
     * @Table(name="mailing_incoming_email_account")
     * @Entity(class="\thebuggenie\modules\mailing\entities\IncomingEmailAccount")
     */
    class IncomingEmailAccounts extends ScopedTable
    {

        const B2DBNAME = 'mailing_incoming_email_account';
        const ID = 'mailing_incoming_email_account.id';
        const NAME = 'mailing_incoming_email_account.name';
        const USERNAME = 'mailing_incoming_email_account.username';
        const PASSWORD = 'mailing_incoming_email_account.password';
        const SERVER = 'mailing_incoming_email_account.server';
        const PORT = 'mailing_incoming_email_account.port';
        const FOLDER = 'mailing_incoming_email_account.folder';
        const SERVER_TYPE = 'mailing_incoming_email_account.server_type';
        const SSL = 'mailing_incoming_email_account.ssl';
        const KEEP_EMAIL = 'mailing_incoming_email_account.keep_email';
        const PROJECT = 'mailing_incoming_email_account.project';
        const ISSUETYPE = 'mailing_incoming_email_account.issuetype';
        const NUM_LAST_FETCHED = 'mailing_incoming_email_account.num_last_fetched';
        const TIME_LAST_FETCHED = 'mailing_incoming_email_account.time_last_fetched';
        const SCOPE = 'mailing_incoming_email_account.scope';

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addOrderBy('mailing_incoming_email_account.project', Criteria::SORT_ASC);
            return $this->select($crit);
        }

        public function getAllByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT, $project_id);

            return $this->select($crit);
        }

    }
