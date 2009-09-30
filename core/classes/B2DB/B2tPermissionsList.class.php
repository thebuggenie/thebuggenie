<?php

	/**
	 * Permissions list table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Permissions list table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tPermissionsList extends B2DBTable 
	{
		
		const B2DBNAME = 'bugs2_permissionslist';
		const ID = 'bugs2_permissionslist.id';
		const SCOPE = 'bugs2_permissionslist.scope';
		const PERMISSION_NAME = 'bugs2_permissionslist.permission_name';
		const LEVELS = 'bugs2_permissionslist.levels';
		const DESCRIPTION = 'bugs2_permissionslist.description';
		const APPLIES_TO = 'bugs2_permissionslist.applies_to';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::PERMISSION_NAME, 100);
			parent::_addInteger(self::LEVELS, 3);
			parent::_addVarchar(self::DESCRIPTION, 200, '');
			parent::_addVarchar(self::APPLIES_TO, 100);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getByAppliesTo($applies_to)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::APPLIES_TO, $applies_to);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doSelect($crit);
			return $res;
		}
		
	}
