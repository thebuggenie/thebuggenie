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
		const B2DBNAME = 'modules';
		const ID = 'modules.id';
		const MODULE_NAME = 'modules.module_name';
		const MODULE_LONGNAME = 'modules.module_longname';
		const DESC = 'modules.module_desc';
		const ENABLED = 'modules.enabled';
		const VERSION = 'modules.version';
		const SHOW_IN_MENU = 'modules.show_in_menu';
		const MODULE_TYPE = 'modules.module_type';
		const SHOW_IN_CONFIG = 'modules.show_in_config';
		const SHOW_IN_USERMENU = 'modules.show_in_usermenu';
		const CLASSNAME = 'modules.classname';
		const SCOPE = 'modules.scope';
		
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
