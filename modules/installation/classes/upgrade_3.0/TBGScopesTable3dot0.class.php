<?php

	/**
	 * Scopes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Scopes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 * 
	 * @Table(name="scopes")
	 */
	class TBGScopesTable3dot0 extends TBGB2DBTable 
	{
		
		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'scopes';
		const ID = 'scopes.id';
		const ENABLED = 'scopes.enabled';
		const DESCRIPTION = 'scopes.description';
		const ADMINISTRATOR = 'scopes.administrator';
		const HOSTNAME = 'scopes.hostname';
		
		protected function _initialize()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::ENABLED, false);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addInteger(self::ADMINISTRATOR, 10);
			parent::_addVarchar(self::HOSTNAME, 150, '');
		}

	}
