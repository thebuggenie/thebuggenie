<?php

	/**
	 * Group class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Group class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGGroup extends TBGIdentifiableClass 
	{
		
		protected static $_groups = null;
		
		protected $_b2dbtablename = 'TBGGroupsTable';

		protected $_members = null;

		protected $_num_members = null;

		public static function doesGroupNameExist($group_name)
		{
			return TBGGroupsTable::getTable()->doesGroupNameExist($group_name);
		}

		public static function getAll()
		{
			if (self::$_groups === null)
			{
				self::$_groups = array();
				if ($res = TBGGroupsTable::getTable()->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_groups[$row->get(TBGGroupsTable::ID)] = TBGContext::factory()->TBGGroup($row->get(TBGGroupsTable::ID), $row);
					}
				}
			}
			return self::$_groups;
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		/**
		 * Creates a group
		 *
		 * @param unknown_type $groupname
		 * @return TBGGroup
		 */
		public static function createNew($groupname, $scope = null)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGGroupsTable::NAME, $groupname);
			if ($scope === null)
			{
				$scope = TBGContext::getScope()->getID();
			}
			$crit->addInsert(TBGGroupsTable::SCOPE, $scope);
			$res = TBGGroupsTable::getTable()->doInsert($crit);
			$group = TBGContext::factory()->TBGGroup($res->getInsertID());
			if (self::$_groups !== null)
			{
				self::$_groups[$group->getID()] = $group;
			}
			return $group;
		}
		
		/**
		 * Adds a user to the group
		 *
		 * @param integer $uid
		 */
		public function addMember($uid)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::GROUP_ID, $this->_id);
			TBGUsersTable::getTable()->doUpdateById($crit, $uid);
		}
		
		public function delete()
		{
			$res = TBGGroupsTable::getTable()->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGUsersTable::GROUP_ID, $this->getID());
			$crit->addUpdate(TBGUsersTable::GROUP_ID, 0);
			$res = TBGUsersTable::getTable()->doUpdate($crit);
		}

		/**
		 * Return an array of all members in this group
		 *
		 * @return array
		 */
		public function getMembers()
		{
			if ($this->_members === null)
			{
				$this->_members = array();
				if ($res = TBGUsersTable::getTable()->getUsersByGroupID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$uid = $row->get(TBGUsersTable::ID);
						$this->_members[$uid] = TBGContext::factory()->TBGUser($uid);
					}
				}
			}
			return $this->_members;
		}

		public function getNumberOfMembers()
		{
			if ($this->_members !== null)
			{
				return count($this->_members);
			}
			elseif ($this->_num_members === null)
			{
				$this->_num_members = TBGUsersTable::getTable()->getNumberOfMembersByGroupID($this->getID());
			}

			return $this->_num_members;
		}

	}
