<?php

	/**
	 * Customers table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class B2tCustomers extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_customers';
		const ID = 'bugs2_customers.id';
		const NAME = 'bugs2_customers.cname';
		const SCOPE = 'bugs2_customers.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
