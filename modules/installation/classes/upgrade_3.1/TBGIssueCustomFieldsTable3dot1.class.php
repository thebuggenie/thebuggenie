<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue <-> custom fields relations table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue <-> custom fields relations table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issuecustomfields")
	 */
	class TBGIssueCustomFieldsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuecustomfields';
		const ID = 'issuecustomfields.id';
		const SCOPE = 'issuecustomfields.scope';
		const ISSUE_ID = 'issuecustomfields.issue_id';
		const OPTION_VALUE = 'issuecustomfields.option_value';
		const CUSTOMFIELDS_ID = 'issuecustomfields.customfields_id';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::CUSTOMFIELDS_ID, Core::getTable('TBGCustomFieldsTable'), TBGCustomFieldsTable::ID);
			parent::_addText(self::OPTION_VALUE, false);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}