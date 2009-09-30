<?php

	/**
	 * Settings table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Settings table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tSettings extends B2DBTable 
	{
		const B2DBNAME = 'bugs2_settings';
		const ID = 'bugs2_settings.id';
		const SCOPE = 'bugs2_settings.scope';
		const NAME = 'bugs2_settings.name';
		const MODULE = 'bugs2_settings.module';
		const VALUE = 'bugs2_settings.value';
		const UID = 'bugs2_settings.uid';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::NAME, 45);
			parent::_addVarchar(self::MODULE, 45);
			parent::_addVarchar(self::VALUE, 200);
			parent::_addInteger(self::UID, 10);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getDefaultScope()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, 0);
			$crit->addWhere(self::NAME, 'defaultscope');
			$row = $this->doSelectOne($crit);
			return $row;
		}
		
	}
