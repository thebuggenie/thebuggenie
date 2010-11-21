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
		const STATE_NAME = 'userstate.state_name';
		const UNAVAILABLE = 'userstate.unavailable';
		const BUSY = 'userstate.busy';
		const ONLINE = 'userstate.online';
		const MEETING = 'userstate.meeting';
		const COLOR = 'userstate.color';
		const ABSENT = 'userstate.absent';

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
			
			parent::_addVarchar(self::STATE_NAME, 100);
			parent::_addBoolean(self::UNAVAILABLE);
			parent::_addBoolean(self::BUSY);
			parent::_addBoolean(self::ONLINE);
			parent::_addBoolean(self::MEETING);
			parent::_addBoolean(self::ABSENT);
			parent::_addVarchar(self::COLOR, 7, '');
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
	}
