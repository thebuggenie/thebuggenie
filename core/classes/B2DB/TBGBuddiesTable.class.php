<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Buddies table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
	class TBGBuddiesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'buddies';
		const ID = 'buddies.id';
		const SCOPE = 'buddies.scope';
		const UID = 'buddies.uid';
		const BID = 'buddies.bid';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGBuddiesTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGBuddiesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::BID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function addFriend($user_id, $friend_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::UID, $user_id);
			$crit->addInsert(self::BID, $friend_id);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$this->doInsert($crit);
		}

		public function getFriendsByUserID($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			return $this->doSelect($crit, false);
		}

		public function removeFriendByUserID($user_id, $friend_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(self::BID, $friend_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$this->doDelete($crit);
		}


	}
