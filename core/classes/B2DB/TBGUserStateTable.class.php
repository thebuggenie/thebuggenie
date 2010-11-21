<?php

	/**
	 * Userstate table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Userstate table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGUserStateTable extends TBGB2DBTable 
	{

		const B2DBNAME = 'userstate';
		const ID = 'userstate.id';
		const SCOPE = 'userstate.scope';
		const NAME = 'userstate.name';
		const UNAVAILABLE = 'userstate.is_unavailable';
		const BUSY = 'userstate.is_busy';
		const ONLINE = 'userstate.is_online';
		const MEETING = 'userstate.is_in_meeting';
		const COLOR = 'userstate.itemdata';
		const ABSENT = 'userstate.is_absent';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGUserStateTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGUserStateTable');
		}
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::NAME, 100);
			parent::_addBoolean(self::UNAVAILABLE);
			parent::_addBoolean(self::BUSY);
			parent::_addBoolean(self::ONLINE);
			parent::_addBoolean(self::MEETING);
			parent::_addBoolean(self::ABSENT);
			parent::_addVarchar(self::COLOR, 7, '');
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
	}
