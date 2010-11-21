<?php

	/**
	 * Client class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Client class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGClient extends TBGIdentifiableClass 
	{
		
		protected $_b2dbtablename = 'TBGClientsTable';
		
		protected $_members = null;

		protected $_num_members = null;
		
		protected static $_clients = null;
		
		public static function doesClientNameExist($client_name)
		{
			return TBGClientsTable::getTable()->doesClientNameExist($client_name);
		}

		public static function getAll()
		{
			if (self::$_clients === null)
			{
				self::$_clients = array();
				if ($res = B2DB::getTable('TBGClientsTable')->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_clients[$row->get(TBGClientsTable::ID)] = TBGContext::factory()->TBGClient($row->get(TBGClientsTable::ID), $row);
					}
				}
			}
			return self::$_clients;
		}
		
		public static function loadFixtures(TBGScope $scope)
		{

		}
		
		public function __toString()
		{
			return "" . $this->_name;
		}
		
		public function getType()
		{
			return self::TYPE_CLIENT;
		}
		
		/**
		 * Creates a client
		 *
		 * @param unknown_type $groupname
		 * @return TBGClient
		 */
		public static function createNew($clientname)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGClientsTable::NAME, $clientname);
			$crit->addInsert(TBGClientsTable::SCOPE, TBGContext::getScope()->getID());
			$res = B2DB::getTable('TBGClientsTable')->doInsert($crit);
			return TBGContext::factory()->TBGClient($res->getInsertID());
		}
		
		/**
		 * Adds a user to the client
		 *
		 * @param TBGUser $user
		 */
		public function addMember(TBGUser $user)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGClientMembersTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(TBGClientMembersTable::TID, $this->_id);
			$crit->addInsert(TBGClientMembersTable::UID, $user->getID());
			B2DB::getTable('TBGClientMembersTable')->doInsert($crit);
			if ($this->_members === null)
			{
				$this->_members = array();
			}
			$this->_members[] = $user->getID();
			array_unique($this->_members);
		}
		
		public function getMembers()
		{
			if ($this->_members === null)
			{
				$this->_members = array();
				foreach (TBGClientMembersTable::getTable()->getUIDsForClientID($this->getID()) as $uid)
				{
					$this->_members[$uid] = TBGContext::factory()->TBGUser($uid);
				}
			}
			return $this->_members;
		}

		/**
		 * Removes a user from the client
		 *
		 * @param integer $uid
		 */
		public function removeMember($uid)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGClientMembersTable::UID, $uid);
			$crit->addWhere(TBGClientMembersTable::TID, $this->_id);
			B2DB::getTable('TBGClientMembersTable')->doDelete($crit);
		}
		
		public function _preDelete()
		{
			$crit = TBGClientMembersTable::getTable()->getCriteria();
			$crit->addWhere(TBGClientMembersTable::TID, $this->getID());
			$res = TBGClientMembersTable::getTable()->doDelete($crit);
		}
		
		public static function findClients($details)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGClientsTable::NAME, "%$details%", B2DBCriteria::DB_LIKE);
			$clients = array();
			if ($res = B2DB::getTable('TBGClientsTable')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$clients[$row->get(TBGClientsTable::ID)] = TBGContext::factory()->TBGClient($row->get(TBGClientsTable::ID), $row);
				}
			}
			return $clients;
		}

		public function getNumberOfMembers()
		{
			if ($this->_members !== null)
			{
				return count($this->_members);
			}
			elseif ($this->_num_members === null)
			{
				$this->_num_members = TBGClientMembersTable::getTable()->getNumberOfMembersByClientID($this->getID());
			}

			return $this->_num_members;
		}
		
	}
