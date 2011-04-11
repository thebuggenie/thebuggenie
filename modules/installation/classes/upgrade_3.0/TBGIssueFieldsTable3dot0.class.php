<?php

	/**
	 * Issue fields table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue fields table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssueFieldsTable3dot0 extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuefields';
		const ID = 'issuefields.id';
		const SCOPE = 'issuefields.scope';
		const ADDITIONAL = 'issuefields.is_additional';
		const ISSUETYPE_ID = 'issuefields.issuetype_id';
		const ISSUETYPE_SCHEME_ID = 'issuefields.issuetype_scheme_id';
		const FIELD_KEY = 'issuefields.field_key';
		const REPORTABLE = 'issuefields.is_reportable';
		const REQUIRED = 'issuefields.required';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FIELD_KEY, 20);
			parent::_addBoolean(self::REQUIRED);
			parent::_addBoolean(self::REPORTABLE);
			parent::_addBoolean(self::ADDITIONAL);
			parent::_addForeignKeyColumn(self::ISSUETYPE_ID, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, TBGIssuetypeSchemesTable::getTable(), TBGIssuetypeSchemesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
	}
