<?php

	/**
	 * Group class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
		
		static protected $_b2dbtablename = 'TBGGroupsTable';

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
		
		public static function postSave($is_new)
		{
			if ($is_new)
			{
				if (self::$_groups !== null)
				{
					self::$_groups[$group->getID()] = $group;
				}
			}
		}
		
		public static function loadFixtures(TBGScope $scope)
		{
			$scope_id = $scope->getID();

			$admin_group = new TBGGroup();
			$admin_group->setName('Administrators');
			$admin_group->setScope($scope);
			$admin_group->save();
			TBGSettings::saveSetting('admingroup', $admin_group->getID(), 'core', $scope_id);

			$user_group = new TBGGroup();
			$user_group->setName('Regular users');
			$user_group->setScope($scope);
			$user_group->save();
			TBGSettings::saveSetting('defaultgroup', $user_group->getID(), 'core', $scope_id);

			$guest_group = new TBGGroup();
			$guest_group->setName('Guests');
			$guest_group->setScope($scope);
			$guest_group->save();
			
			// Set up initial users, and their permissions
			TBGUser::loadFixtures($scope, $admin_group, $user_group, $guest_group);
			TBGPermissionsTable::getTable()->loadFixtures($scope, $admin_group->getID(), $guest_group->getID());
		}
		
		public function _preDelete()
		{
			$crit = TBGUsersTable::getTable()->getCriteria();
			$crit->addWhere(TBGUsersTable::GROUP_ID, $this->getID());
			
			if ($this->getID() == TBGSettings::getDefaultGroup()->getID())
				$crit->addUpdate(TBGUsersTable::GROUP_ID, null);
			else
				$crit->addUpdate(TBGUsersTable::GROUP_ID, TBGSettings::getDefaultGroup()->getID());

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
		
		public function removeMember(TBGUser $user)
		{
			if ($this->_members !== null)
			{
				unset($this->_members[$user->getID()]);
			}
			if ($this->_num_members !== null)
			{
				$this->_num_members--;
			}
		}

	}
