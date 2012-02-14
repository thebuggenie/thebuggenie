<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Custom field options table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom field options table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="customfieldoptions")
	 */
	class TBGCustomFieldOptionsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'customfieldoptions';
		const ID = 'customfieldoptions.id';
		const NAME = 'customfieldoptions.name';
		const ITEMDATA = 'customfieldoptions.itemdata';
		const OPTION_VALUE = 'customfieldoptions.value';
		const SORT_ORDER = 'customfieldoptions.sort_order';
		const CUSTOMFIELDS_KEY = 'customfieldoptions.customfield_key';
		const SCOPE = 'customfieldoptions.scope';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::OPTION_VALUE, 100);
			parent::_addVarchar(self::ITEMDATA, 100);
			parent::_addInteger(self::SORT_ORDER, 100);
			parent::_addVarchar(self::CUSTOMFIELDS_KEY, 100);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
