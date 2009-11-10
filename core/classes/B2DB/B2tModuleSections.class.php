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
		const B2DBNAME = 'modulesections';
		const ID = 'modulesections.id';
		const IDENTIFIER = 'modulesections.identifier';
		const MODULE = 'modulesections.module';
		const MODULE_NAME = 'modulesections.module_name';
		const ORDER = 'modulesections.order';
		const FUNCTION_NAME = 'modulesections.function_name';
		const SCOPE = 'modulesections.scope';

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
