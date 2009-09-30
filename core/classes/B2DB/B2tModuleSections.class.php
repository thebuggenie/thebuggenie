<?php

	/**
	 * Module sections table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Module sections table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tModuleSections extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_modulesections';
		const ID = 'bugs2_modulesections.id';
		const IDENTIFIER = 'bugs2_modulesections.identifier';
		const MODULE = 'bugs2_modulesections.module';
		const MODULE_NAME = 'bugs2_modulesections.module_name';
		const ORDER = 'bugs2_modulesections.order';
		const FUNCTION_NAME = 'bugs2_modulesections.function_name';
		const SCOPE = 'bugs2_modulesections.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::MODULE, 50);
			parent::_addVarchar(self::IDENTIFIER, 50);
			parent::_addInteger(self::ORDER, 3);
			parent::_addVarchar(self::FUNCTION_NAME, 100);
			parent::_addForeignKeyColumn(self::MODULE_NAME, B2DB::getTable('B2tModules'), B2tModules::MODULE_NAME);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
