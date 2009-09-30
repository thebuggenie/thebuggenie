<?php

	/**
	 * Buddies table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Buddies table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tBuddies extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_buddies';
		const ID = 'bugs2_buddies.id';
		const SCOPE = 'bugs2_buddies.scope';
		const UID = 'bugs2_buddies.uid';
		const BID = 'bugs2_buddies.bid';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::BID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		
	}
