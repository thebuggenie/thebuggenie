<?php

	/**
	 * Custom field options table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom field options table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tCustomFieldOptions extends B2DBTable
	{

		const B2DBNAME = 'customfieldoptions';
		const ID = 'customfieldoptions.id';
		const NAME = 'customfieldoptions.cname';
		const OPTION_VALUE = 'customfieldoptions.option_value';
		const SORT_ORDER = 'customfieldoptions.sort_order';
		const CUSTOMFIELDS_ID = 'customfieldoptions.customfields_id';
		const SCOPE = 'customfieldoptions.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::OPTION_VALUE, 100);
			parent::_addInteger(self::SORT_ORDER, 100);
			parent::_addForeignKeyColumn(self::CUSTOMFIELDS_ID, B2DB::getTable('B2tCustomFields'), B2tCustomFields::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

	}
