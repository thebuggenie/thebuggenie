<?php

	/**
	 * Customer class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Customer class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGCustomer extends TBGIdentifiableClass 
	{
		static $_customers = null;
		
		/**
		 * Class constructor
		 *
		 * @param integer $g_id
		 */
		public function __construct($c_id, $row = null)
		{
			$this->_id = $c_id;
			if ($row === null)
			{
				$row = B2DB::getTable('TBGCustomersTable')->doSelectById($c_id); 
			}
			try
			{
				$this->_name = $row->get(TBGCustomersTable::NAME);
			}
			catch (Exception $e)
			{
				throw new Exception('The customer (' . $c_id . ') does not exist: ' . $e->getMessage());
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
			return $this->_id;
		}
		
		public function postSave($is_new)
		{
			if ($is_new)
			{
				if (self::$_customers !== null)
				{
					self::$_customers[$this->getID()] = $this;
				}
			}
		}
		
		/**
		 * Adds a user to the group
		 *
		 * @param integer $uid
		 */
		public function addMember($uid)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::CUSTOMER_ID, $this->_id);
			TBGUsersTable::getTable()->doUpdateById($crit, $uid);
		}
		
		public function setName($c_name)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCustomersTable::NAME, $c_name);
			B2DB::getTable('TBGCustomersTable')->doUpdateById($crit, $this->getID());
			$this->_name = $c_name;
		}
		
		public function _preDelete()
		{
			$crit = TBGUsersTable::getTable()->getCriteria();
			$crit->addWhere(TBGUsersTable::CUSTOMER_ID, $this->getID());
			$crit->addUpdate(TBGUsersTable::CUSTOMER_ID, null);
			$res = TBGUsersTable::getTable()->doUpdate($crit);
		}

		public static function getAll()
		{
			if (self::$_groups === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGCustomersTable::SCOPE, TBGContext::getScope()->getID());
				
				$res = B2DB::getTable('TBGCustomersTable')->doSelect($crit);
		
				$customers = array();
		
				while ($row = $res->getNextRow())
				{
					$customers[$row->get(TBGCustomersTable::ID)] = TBGContext::factory()->TBGCustomer($row->get(TBGCustomersTable::ID), $row);
				}
				self::$_customers = $customers;
			}
			return self::$_customers;
		}

		public static function findCustomers($details)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGCustomersTable::NAME, "%$details%", B2DBCriteria::DB_LIKE);
			$customers = array();
			if ($res = B2DB::getTable('TBGCustomersTable')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$customers[$row->get(TBGCustomersTable::ID)] = TBGContext::factory()->TBGCustomer($row->get(TBGCustomersTable::ID), $row);
				}
			}
			return $customers;
		}
		
		
	}
