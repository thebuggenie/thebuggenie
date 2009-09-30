<?php

	/**
	 * Userstate table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class B2tUserState extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_userstate';
		const ID = 'bugs2_userstate.id';
		const SCOPE = 'bugs2_userstate.scope';
		const STATE_NAME = 'bugs2_userstate.state_name';
		const UNAVAILABLE = 'bugs2_userstate.unavailable';
		const BUSY = 'bugs2_userstate.busy';
		const ONLINE = 'bugs2_userstate.online';
		const MEETING = 'bugs2_userstate.meeting';
		const COLOR = 'bugs2_userstate.color';
		const ABSENT = 'bugs2_userstate.absent';

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
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
