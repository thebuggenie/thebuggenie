<?php

	/**
	 * Customer class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class BUGScustomer extends BUGSidentifiableclass implements BUGSidentifiable 
	{
		static $_customers = null;
		
		/**
		 * Class constructor
		 *
		 * @param integer $g_id
		 */
		public function __construct($c_id, $row = null)
		{
			$this->_itemid = $c_id;
			if ($row === null)
			{
				$row = B2DB::getTable('B2tCustomers')->doSelectById($c_id); 
			}
			try
			{
				$this->_name = $row->get(B2tCustomers::NAME);
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
			return $this->_itemid;
		}
		
		/**
		 * Creates a customer
		 *
		 * @param unknown_type $c_name
		 * @return BUGScustomer
		 */
		public static function createNew($c_name, $scope = null)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tCustomers::NAME, $c_name);
			if ($scope === null)
			{
				$scope = BUGScontext::getScope()->getID();
			}
			$crit->addInsert(B2tCustomer::SCOPE, $scope);
			$res = B2DB::getTable('B2tCustomers')->doInsert($crit);
			$customer = BUGSfactory::customerLab($res->getInsertID());
			if (self::$_customers !== null)
			{
				self::$_customers[$customer->getID()] = $customer;
			}
			return $customer;
		}
		
		/**
		 * Adds a user to the group
		 *
		 * @param integer $uid
		 */
		public function addMember($uid)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::CUSTOMER_ID, $this->_itemid);
			B2DB::getTable('B2tUsers')->doUpdateById($crit, $uid);
		}
		
		public function setName($c_name)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCustomers::NAME, $c_name);
			B2DB::getTable('B2tCustomers')->doUpdateById($crit, $this->getID());
			$this->_name = $c_name;
		}
		
		public function delete()
		{
			$res = B2DB::getTable('B2tCustomers')->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tUsers::CUSTOMER_ID, $this->getID());
			$crit->addUpdate(B2tUsers::CUSTOMER_ID, 0);
			$res = B2DB::getTable('B2tUsers')->doUpdate($crit);
		}

		public static function getAll()
		{
			if (self::$_groups === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tCustomers::SCOPE, BUGScontext::getScope()->getID());
				
				$res = B2DB::getTable('B2tCustomers')->doSelect($crit);
		
				$customers = array();
		
				while ($row = $res->getNextRow())
				{
					$customers[$row->get(B2tCustomers::ID)] = BUGSfactory::customerLab($row->get(B2tCustomers::ID), $row);
				}
				self::$_customers = $customers;
			}
			return self::$_customers;
		}

		static public function findCustomers($details)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tCustomers::NAME, "%$details%", B2DBCriteria::DB_LIKE);
			$customers = array();
			if ($res = B2DB::getTable('B2tCustomers')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$customers[$row->get(B2tCustomers::ID)] = BUGSfactory::customerLab($row->get(B2tCustomers::ID), $row);
				}
			}
			return $customers;
		}
		
		
	}
