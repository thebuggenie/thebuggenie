<?php

	/**
	 * Modules table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Modules table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tModules extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_modules';
		const ID = 'bugs2_modules.id';
		const MODULE_NAME = 'bugs2_modules.module_name';
		const MODULE_LONGNAME = 'bugs2_modules.module_longname';
		const DESC = 'bugs2_modules.module_desc';
		const ENABLED = 'bugs2_modules.enabled';
		const VERSION = 'bugs2_modules.version';
		const SHOW_IN_MENU = 'bugs2_modules.show_in_menu';
		const MODULE_TYPE = 'bugs2_modules.module_type';
		const SHOW_IN_CONFIG = 'bugs2_modules.show_in_config';
		const SHOW_IN_USERMENU = 'bugs2_modules.show_in_usermenu';
		const CLASSNAME = 'bugs2_modules.classname';
		const SCOPE = 'bugs2_modules.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::MODULE_NAME, 50);
			parent::_addVarchar(self::MODULE_LONGNAME, 100);
			parent::_addBoolean(self::MODULE_TYPE);
			parent::_addText(self::DESC, false);
			parent::_addBoolean(self::ENABLED);
			parent::_addVarchar(self::VERSION, 10);
			parent::_addVarchar(self::CLASSNAME, 50);
			parent::_addBoolean(self::SHOW_IN_CONFIG);
			parent::_addBoolean(self::SHOW_IN_MENU);
			parent::_addBoolean(self::SHOW_IN_USERMENU);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doSelect($crit);
			return $res;
		}
			
	}
