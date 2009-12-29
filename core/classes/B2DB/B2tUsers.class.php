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

		const B2DBNAME = 'users';
		const ID = 'users.id';
		const SCOPE = 'users.scope';
		const UNAME = 'users.uname';
		const PASSWD = 'users.passwd';
		const BUDDYNAME = 'users.buddyname';
		const REALNAME = 'users.realname';
		const CALENDAR = 'users.calendar';
		const EMAIL = 'users.email';
		const STATE = 'users.state';
		const HOMEPAGE = 'users.homepage';
		const LASTSEEN = 'users.lastseen';
		const LASTLOGIN = 'users.lastlogin';
		const QUOTA = 'users.quota';
		const ACTIVATED = 'users.activated';
		const ENABLED = 'users.enabled';
		const DELETED = 'users.deleted';
		const SHOWFOLLOWUPS = 'users.showfollowups';
		const SHOWASSIGNED = 'users.showassigned';
		const AVATAR = 'users.avatar';
		const USE_GRAVATAR = 'users.use_gravatar';
		const PRIVATE_EMAIL = 'users.private_email';
		const JOINED = 'users.joined';
		const GROUP_ID = 'users.group_id';
		const CUSTOMER_ID = 'users.customer_id';
		
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

		public function getByUserIDs($userids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ID, $userids, B2DBCriteria::DB_IN);
			$crit->addWhere(self::DELETED, false);
			return B2DB::getTable('B2tUsers')->doSelect($crit);
		}

		public function getByDetails($details)
		{
			$crit = $this->getCriteria();
			if (stristr($details, "@"))
			{
				$crit->addWhere(B2tUsers::EMAIL, "%$details%", B2DBCriteria::DB_LIKE);
			}
			else
			{
				$crit->addWhere(B2tUsers::UNAME, "%$details%", B2DBCriteria::DB_LIKE);
			}
	
			if ($limit)
			{
				$crit->setLimit($limit);
			}
			if (!$res = $this->doSelect($crit))
			{
				$crit = $this->getCriteria();
				$crit->addWhere(B2tUsers::UNAME, "%$details%", B2DBCriteria::DB_LIKE);
				$crit->addOr(B2tUsers::BUDDYNAME, "%$details%", B2DBCriteria::DB_LIKE);
				$crit->addOr(B2tUsers::REALNAME, "%$details%", B2DBCriteria::DB_LIKE);
				$crit->addOr(B2tUsers::EMAIL, "%$details%", B2DBCriteria::DB_LIKE);
				if ($limit)
				{
					$crit->setLimit($limit);
				}
				$res = $this->doSelect($crit);
			}
			
			return $res;
		}
		
	}
