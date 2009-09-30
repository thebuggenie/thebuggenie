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
	class BUGSgroup extends BUGSidentifiableclass implements BUGSidentifiable 
	{
		static $_groups = null;
		
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
				$row = B2DB::getTable('B2tGroups')->doSelectById($g_id); 
			}
			try
			{
				$this->_name = $row->get(B2tGroups::GNAME);
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
		 * @return BUGSgroup
		 */
		public static function createNew($groupname, $scope = null)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tGroups::GNAME, $groupname);
			if ($scope === null)
			{
				$scope = BUGScontext::getScope()->getID();
			}
			$crit->addInsert(B2tGroups::SCOPE, $scope);
			$res = B2DB::getTable('B2tGroups')->doInsert($crit);
			$group = BUGSfactory::groupLab($res->getInsertID());
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
			$crit->addUpdate(B2tUsers::GROUP_ID, $this->_itemid);
			B2DB::getTable('B2tUsers')->doUpdateById($crit, $uid);
		}
		
		public function setName($gname)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tGroups::GNAME, $gname);
			B2DB::getTable('B2tGroups')->doUpdateById($crit, $this->getID());
			$this->_name = $gname;
		}
		
		public function delete()
		{
			$res = B2DB::getTable('B2tGroups')->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tUsers::GROUP_ID, $this->getID());
			$crit->addUpdate(B2tUsers::GROUP_ID, 0);
			$res = B2DB::getTable('B2tUsers')->doUpdate($crit);
		}

		public static function getAll()
		{
			if (self::$_groups === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tGroups::SCOPE, BUGScontext::getScope()->getID());
				
				$res = B2DB::getTable('B2tGroups')->doSelect($crit);
		
				$groups = array();
		
				while ($row = $res->getNextRow())
				{
					$groups[$row->get(B2tGroups::ID)] = BUGSfactory::groupLab($row->get(B2tGroups::GNAME), $row);
				}
				self::$_groups = $groups;
			}
			return self::$_groups;
		}
		
	}
