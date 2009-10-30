<?php

	/**
	 * Users table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Users table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tUsers extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_users';
		const ID = 'bugs2_users.id';
		const SCOPE = 'bugs2_users.scope';
		const UNAME = 'bugs2_users.uname';
		const PASSWD = 'bugs2_users.passwd';
		const BUDDYNAME = 'bugs2_users.buddyname';
		const REALNAME = 'bugs2_users.realname';
		const CALENDAR = 'bugs2_users.calendar';
		const EMAIL = 'bugs2_users.email';
		const STATE = 'bugs2_users.state';
		const HOMEPAGE = 'bugs2_users.homepage';
		const LASTSEEN = 'bugs2_users.lastseen';
		const LASTLOGIN = 'bugs2_users.lastlogin';
		const QUOTA = 'bugs2_users.quota';
		const ACTIVATED = 'bugs2_users.activated';
		const ENABLED = 'bugs2_users.enabled';
		const DELETED = 'bugs2_users.deleted';
		const SHOWFOLLOWUPS = 'bugs2_users.showfollowups';
		const SHOWASSIGNED = 'bugs2_users.showassigned';
		const AVATAR = 'bugs2_users.avatar';
		const USE_GRAVATAR = 'bugs2_users.use_gravatar';
		const PRIVATE_EMAIL = 'bugs2_users.private_email';
		const JOINED = 'bugs2_users.joined';
		const GROUP_ID = 'bugs2_users.group_id';
		const CUSTOMER_ID = 'bugs2_users.customer_id';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::UNAME, 50);
			parent::_addVarchar(self::PASSWD, 50);
			parent::_addVarchar(self::BUDDYNAME, 50);
			parent::_addVarchar(self::REALNAME, 100);
			parent::_addVarchar(self::EMAIL, 200);
			parent::_addForeignKeyColumn(self::STATE, B2DB::getTable('B2tUserState'), B2tUserState::ID);
			parent::_addVarchar(self::HOMEPAGE, 250, '');
			parent::_addInteger(self::LASTSEEN, 10);
			parent::_addInteger(self::LASTLOGIN, 10);
			parent::_addInteger(self::QUOTA);
			parent::_addBoolean(self::ACTIVATED);
			parent::_addBoolean(self::ENABLED);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::SHOWFOLLOWUPS);
			parent::_addBoolean(self::SHOWASSIGNED);
			parent::_addVarchar(self::AVATAR, 30, '');
			parent::_addBoolean(self::USE_GRAVATAR, true);
			parent::_addBoolean(self::PRIVATE_EMAIL);
			parent::_addInteger(self::JOINED, 10);
			parent::_addForeignKeyColumn(self::GROUP_ID, B2DB::getTable('B2tGroups'), B2tGroups::ID);
			parent::_addForeignKeyColumn(self::CUSTOMER_ID, B2DB::getTable('B2tCustomers'), B2tCustomers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

		public function getByUsername($username)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UNAME, $username);
			$crit->addWhere(self::DELETED, false);
			return B2DB::getTable('B2tUsers')->doSelectOne($crit);
		}

		public function getByUsernameAndPassword($username, $password)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UNAME, $username);
			$crit->addWhere(self::PASSWD, $password);
			$crit->addWhere(self::DELETED, false);
			return B2DB::getTable('B2tUsers')->doSelectOne($crit);
		}

		public function getByUserID($userid)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			return B2DB::getTable('B2tUsers')->doSelectById($userid, $crit);
		}

	}
