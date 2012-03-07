<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion,
		b2db\Table;

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
	 * @Entity(class="TBGUser")
	 */
	class TBGUsersTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'users';
		const ID = 'users.id';
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
		const OPENID_LOCKED = 'users.openid_locked';
		
		public function getAll()
		{
			$res = $this->doSelectAll();
			
			return $res;
		}

		protected function _setupIndexes()
		{
			$this->_addIndex('userstate', self::USERSTATE);
			$this->_addIndex('username_password', array(self::UNAME, self::PASSWORD));
			$this->_addIndex('username_deleted', array(self::UNAME, self::DELETED));
		}

		protected function _migrateData(Table $old_users_table)
		{
			switch ($old_users_table->getVersion())
			{
				case 1:
					$users = $this->getUserMigrationDetails();
					$table = TBGUserScopesTable::getTable();
					foreach ($users as $user_id => $details)
					{
						$table->addUserToScope($user_id, $details['scope_id'], $details['group_id'], true);
					}
					break;
			}
		}

		protected function getUserMigrationDetails()
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn('users.id');
			$crit->addSelectionColumn('users.scope');
			$crit->addSelectionColumn('users.group_id');
			$res = $this->doSelect($crit);

			$users = array();
			while ($row = $res->getNextRow())
			{
				$users[$row->get('users.id')] = array('scope_id' => $row->get('users.scope'), 'group_id' => $row->get('users.group_id'));
			}

			return $users;
		}

		public function getByUsername($username)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UNAME, $username);
			$crit->addWhere(self::DELETED, false);
			
			return $this->selectOne($crit);
		}
		
		public function isUsernameAvailable($username)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UNAME, $username);
			
			return !(bool) $this->doCount($crit);
		}

		public function getByUsernameAndPassword($username, $password)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UNAME, $username);
			$crit->addWhere(self::PASSWORD, $password);
			$crit->addWhere(self::DELETED, false);
			return $this->selectOne($crit);
		}

		public function getByUserID($userid)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			return $this->selectById($userid, $crit);
		}

		public function doesIDExist($userid)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			$crit->addWhere(self::ID, $userid);
			return $this->doCount($crit);
		}

		public function getByDetails($details, $limit = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::DELETED, false);
			if (mb_stristr($details, "@"))
			{
				$crit->addWhere(self::EMAIL, "%$details%", Criteria::DB_LIKE);
			}
			else
			{
				$crit->addWhere(self::UNAME, "%$details%", Criteria::DB_LIKE);
			}
	
			if ($limit)
			{
				$crit->setLimit($limit);
			}
			if (!$res = $this->select($crit))
			{
				$crit = $this->getCriteria();
				$crit->addWhere(self::DELETED, false);
				$crit->addWhere(self::UNAME, "%$details%", Criteria::DB_LIKE);
				$crit->addOr(self::BUDDYNAME, "%$details%", Criteria::DB_LIKE);
				$crit->addOr(self::REALNAME, "%$details%", Criteria::DB_LIKE);
				$crit->addOr(self::EMAIL, "%$details%", Criteria::DB_LIKE);
				if ($limit)
				{
					$crit->setLimit($limit);
				}
				$res = $this->select($crit);
			}

			$users = array();
			if ($res)
			{
				foreach ($res as $key => $user)
				{
					if ($user->isScopeConfirmed())
					{
						$users[$key] = $user;
					}
				}
			}
			
			return $users;
		}

		public function findInConfig($details, $limit = 50)
		{
			$crit = $this->getCriteria();
			switch ($details)
			{
				case 'unactivated':
					$crit->addWhere(self::ACTIVATED, false);
					break;
				case 'newusers':
					$crit->addWhere(self::JOINED, NOW - 1814400, Criteria::DB_GREATER_THAN_EQUAL);
					break;
				case '0-9':
					$ctn = $crit->returnCriterion(self::UNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), Criteria::DB_IN);
					$ctn->addOr(self::BUDDYNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), Criteria::DB_IN);
					$ctn->addOr(self::REALNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), Criteria::DB_IN);
					$crit->addWhere($ctn);
					break;
				case 'all':
					break;
				default:
					$details = (mb_strlen($details) == 1) ? mb_strtolower("$details%") : mb_strtolower("%$details%");
					$ctn = $crit->returnCriterion(self::UNAME, $details, Criteria::DB_LIKE);
					$ctn->addOr(self::BUDDYNAME, $details, Criteria::DB_LIKE);
					$ctn->addOr(self::REALNAME, $details, Criteria::DB_LIKE);
					$ctn->addOr(self::EMAIL, $details, Criteria::DB_LIKE);
					$crit->addWhere($ctn);
					break;
			}
			$crit->addWhere(self::DELETED, false);

			$users = array();
			$res = null;

			if ($details != '' && $res = $this->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && count($users) < $limit)
				{
					$user_id = (int) $row->get(self::ID);
					$details = TBGUserScopesTable::getTable()->getUserDetailsByScope($user_id, TBGContext::getScope()->getID());
					if (!$details) continue;
					$users[$user_id] = TBGContext::factory()->TBGUser($user_id);
					$users[$user_id]->setScopeConfirmed($details['confirmed']);
				}
			}

			return $users;
		}

		public function getAllUserIDs()
		{
			$crit = $this->getCriteria();
			
			$crit->addSelectionColumn(self::ID, 'uid');
			$res = $this->doSelect($crit);
			
			$uids = array();
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$uid = $row->get('uid');
					$uids[$uid] = $uid;
				}
			}
			
			return $uids;
		}

	}
