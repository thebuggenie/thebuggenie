<?php

	/**
	 * Custom fields table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom fields table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tCustomFields extends B2DBTable
	{

		const B2DBNAME = 'bugs2_customfields';
		const ID = 'bugs2_customfields.id';
		const FIELD_NAME = 'bugs2_customfields.field_name';
		const FIELD_KEY = 'bugs2_issuecustomfields.field_key';
		const FIELD_TYPE = 'bugs2_customfields.field_type';
		const SCOPE = 'bugs2_customfields.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FIELD_NAME, 50);
			parent::_addVarchar(self::FIELD_KEY, 50);
			parent::_addInteger(self::FIELD_TYPE);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

	}
