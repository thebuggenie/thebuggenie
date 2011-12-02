<?php

	/**
	 * Custom fields table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom fields table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="customfields")
	 */
	class TBGCustomFieldsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'customfields';
		const ID = 'customfields.id';
		const FIELD_NAME = 'customfields.name';
		const FIELD_DESCRIPTION = 'customfields.description';
		const FIELD_INSTRUCTIONS = 'customfields.instructions';
		const FIELD_KEY = 'customfields.key';
		const FIELD_TYPE = 'customfields.itemtype';
		const SCOPE = 'customfields.scope';

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FIELD_NAME, 100);
			parent::_addVarchar(self::FIELD_KEY, 100);
			parent::_addVarchar(self::FIELD_DESCRIPTION, 200);
			parent::_addText(self::FIELD_INSTRUCTIONS);
			parent::_addInteger(self::FIELD_TYPE);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
