<?php

	/**
	 * Users table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Users table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="users")
	 */
	class TBGUsersTable3dot1 extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'users';
		const ID = 'users.id';
		const SCOPE = 'users.scope';
		const UNAME = 'users.username';
		const PASSWORD = 'users.password';
		const BUDDYNAME = 'users.buddyname';
		const REALNAME = 'users.realname';
		const EMAIL = 'users.email';
		const USERSTATE = 'users.userstate';
		const CUSTOMSTATE = 'users.customstate';
		const HOMEPAGE = 'users.homepage';
		const LANGUAGE = 'users.language';
		const LASTSEEN = 'users.lastseen';
		const QUOTA = 'users.quota';
		const ACTIVATED = 'users.activated';
		const ENABLED = 'users.enabled';
		const DELETED = 'users.deleted';
		const AVATAR = 'users.avatar';
		const USE_GRAVATAR = 'users.use_gravatar';
		const PRIVATE_EMAIL = 'users.private_email';
		const JOINED = 'users.joined';
		const GROUP_ID = 'users.group_id';
		
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::UNAME, 50);
			parent::_addVarchar(self::PASSWORD, 50);
			parent::_addVarchar(self::BUDDYNAME, 50);
			parent::_addVarchar(self::REALNAME, 100);
			parent::_addVarchar(self::EMAIL, 200);
			parent::_addBoolean(self::CUSTOMSTATE);
			parent::_addVarchar(self::HOMEPAGE, 250, '');
			parent::_addVarchar(self::LANGUAGE, 100, '');
			parent::_addInteger(self::LASTSEEN, 10);
			parent::_addInteger(self::QUOTA);
			parent::_addBoolean(self::ACTIVATED);
			parent::_addBoolean(self::ENABLED);
			parent::_addBoolean(self::DELETED);
			parent::_addVarchar(self::AVATAR, 30, '');
			parent::_addBoolean(self::USE_GRAVATAR, true);
			parent::_addBoolean(self::PRIVATE_EMAIL);
			parent::_addInteger(self::JOINED, 10);
			parent::_addForeignKeyColumn(self::USERSTATE, TBGUserStateTable::getTable(), TBGUserStateTable::ID);
			parent::_addForeignKeyColumn(self::GROUP_ID, TBGGroupsTable::getTable(), TBGGroupsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
