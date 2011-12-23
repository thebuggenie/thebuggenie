<?php

	/**
	 * Issue types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issuetypes")
	 */
	class TBGIssueTypesTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuetypes';
		const ID = 'issuetypes.id';
		const SCOPE = 'issuetypes.scope';
		const NAME = 'issuetypes.name';
		const DESCRIPTION = 'issuetypes.description';
		const ICON = 'issuetypes.itemdata';
		const TASK = 'issuetypes.task';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addVarchar(self::ICON, 30, 'bug_report');
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::TASK);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
