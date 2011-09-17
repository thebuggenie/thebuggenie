<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	class TBGIncomingEmailAccountTable extends TBGB2DBTable
	{
		
		const B2DB_TABLE_VERSION = 1;
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
		const SCOPE = 'mailing_incoming_email_account.scope';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGIncomingEmailAccountTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGIncomingEmailAccountTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addVarchar(self::SERVER, 200);
			parent::_addVarchar(self::USERNAME, 200);
			parent::_addVarchar(self::PASSWORD, 200);
			parent::_addInteger(self::SERVER_TYPE);
			parent::_addInteger(self::PORT);
			parent::_addBoolean(self::SSL);
			parent::_addBoolean(self::KEEP_EMAIL);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
	}