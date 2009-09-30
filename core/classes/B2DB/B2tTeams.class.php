<?php

	/**
	 * Teams table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Teams table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tTeams extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_teams';
		const ID = 'bugs2_teams.id';
		const SCOPE = 'bugs2_teams.scope';
		const TEAMNAME = 'bugs2_teams.teamname';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::TEAMNAME, 50);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
