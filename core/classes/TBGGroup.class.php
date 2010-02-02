<?php

	/**
	 * Group class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class TBGGroup extends TBGIdentifiableClass implements TBGIdentifiable 
	{
		protected static $_groups = null;

		public static function getAll()
		{
			if (self::$_groups === null)
			{
				self::$_groups = array();
				if ($res = B2DB::getTable('TBGGroupsTable')->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_groups[$row->get(TBGGroupsTable::ID)] = TBGFactory::groupLab($row->get(TBGGroupsTable::ID), $row);
					}
				}
			}
			return self::$_groups;
		}
		
		/**
		 * Class constructor
		 *
		 * @param integer $g_id
		 */
		public function __construct($g_id, $row = null)
		{
			$this->_itemid = $g_id;
			if ($row === null)
			{
				$row = B2DB::getTable('TBGGroupsTable')->doSelectById($g_id); 
			}
			try
			{
				$this->_name = $row->get(TBGGroupsTable::GNAME);
			}
			catch (Exception $e)
			{
				throw new Exception('The group (' . $g_id . ') does not exist: ' . $e->getMessage());
			}
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		public function getName()
		{
			return $this->_name;
		}
		
		public function getID()
		{
			return $this->_itemid;
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
			$crit->addInsert(TBGGroupsTable::GNAME, $groupname);
			if ($scope === null)
			{
				$scope = TBGContext::getScope()->getID();
			}
			$crit->addInsert(TBGGroupsTable::SCOPE, $scope);
			$res = B2DB::getTable('TBGGroupsTable')->doInsert($crit);
			$group = TBGFactory::groupLab($res->getInsertID());
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
			$crit->addUpdate(TBGUsersTable::GROUP_ID, $this->_itemid);
			B2DB::getTable('TBGUsersTable')->doUpdateById($crit, $uid);
		}
		
		public function setName($gname)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGGroupsTable::GNAME, $gname);
			B2DB::getTable('TBGGroupsTable')->doUpdateById($crit, $this->getID());
			$this->_name = $gname;
		}
		
		public function delete()
		{
			$res = B2DB::getTable('TBGGroupsTable')->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGUsersTable::GROUP_ID, $this->getID());
			$crit->addUpdate(TBGUsersTable::GROUP_ID, 0);
			$res = B2DB::getTable('TBGUsersTable')->doUpdate($crit);
		}
		
	}
