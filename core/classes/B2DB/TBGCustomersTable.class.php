<?php

	/**
	 * Customers table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Customers table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGCustomersTable extends B2DBTable 
	{

		const B2DBNAME = 'customers';
		const ID = 'customers.id';
		const NAME = 'customers.cname';
		const SCOPE = 'customers.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
	}
