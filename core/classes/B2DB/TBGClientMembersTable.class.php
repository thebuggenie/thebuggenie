<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Client members table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Client members table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="clientmembers")
	 */
	class TBGClientMembersTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'clientmembers';
		const ID = 'clientmembers.id';
		const SCOPE = 'clientmembers.scope';
		const UID = 'clientmembers.uid';
		const CID = 'clientmembers.cid';
		
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable());
			parent::_addForeignKeyColumn(self::CID, TBGClientsTable::getTable());
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable());
		}

		public function getUIDsForClientID($client_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CID, $client_id);

			$uids = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$uids[$row->get(self::UID)] = $row->get(self::UID);
				}
			}

			return $uids;
		}
		
		public function clearClientsByUserID($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$res = $this->doDelete($crit);
		}

		public function getNumberOfMembersByClientID($client_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CID, $client_id);
			$count = $this->doCount($crit);

			return $count;
		}

		public function cloneClientMemberships($cloned_client_id, $new_client_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CID, $cloned_client_id);
			$memberships_to_add = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$memberships_to_add[] = $row->get(self::UID);
				}
			}

			foreach ($memberships_to_add as $uid)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::UID, $uid);
				$crit->addInsert(self::CID, $new_client_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
			}
		}

		public function getClientIDsForUserID($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			return $this->doSelect($crit);
		}

	}
