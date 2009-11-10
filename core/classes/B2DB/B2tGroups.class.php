<?php

	/**
	 * Groups table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Groups table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tGroups extends B2DBTable 
	{

		const B2DBNAME = 'groups';
		const ID = 'groups.id';
		const GNAME = 'groups.gname';
		const SCOPE = 'groups.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::GNAME, 50);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
