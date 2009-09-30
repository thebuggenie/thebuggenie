<?php
	
	/**
	 * User state class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * User state class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class BUGSuserstate extends BUGSdatatype 
	{
		protected $_is_online = false;
		protected $_is_unavailable = false;
		protected $_is_busy = false;
		protected $_is_in_meeting = false;
		protected $_is_absent = false;
		
		static $_userstates = null;
		
		/**
		 * returns a BUGSuserstate object
		 *
		 * @param integer $us_id
		 * @param B2DBRow $row
		 * 
		 * @return BUGSuserstate
		 */
		public function __construct($us_id, $row = null)
		{
			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$row = B2DB::getTable('B2tUserState')->doSelectById($us_id, $crit);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_itemdata = $row->get(B2tUserState::COLOR);
				$this->_name = $row->get(B2tUserState::STATE_NAME);
				$this->_itemid = $us_id;
				$this->_is_absent = ($row->get(B2tUserState::ABSENT) == 1) ? true : false;
				$this->_is_online = ($row->get(B2tUserState::ONLINE) == 1) ? true : false;
				$this->_is_unavailable = ($row->get(B2tUserState::UNAVAILABLE) == 1) ? true : false;
				$this->_is_busy = ($row->get(B2tUserState::BUSY) == 1) ? true : false;
				$this->_is_in_meeting = ($row->get(B2tUserState::MEETING) == 1) ? true : false;
			}
			else
			{
				throw new Exception('This userstate does not exist');
			}
		}
		
		public function isOnline()
		{
			return $this->_is_online;
		}
		
		public function isUnavailable()
		{
			return $this->_is_unavailable;
		}
		
		public function isBusy()
		{
			return $this->_is_busy;
		}
		
		public function isInMeeting()
		{
			return $this->_is_in_meeting;
		}
		
		public function isAbsent()
		{
			return $this->_is_absent;
		}
		
		public static function getAll()
		{
			if (self::$_userstates === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tUserState::SCOPE, BUGScontext::getScope()->getID());
				
				$res = B2DB::getTable('B2tUserState')->doSelect($crit);
		
				$aStates = array();
				
				while ($row = $res->getNextRow())
				{
					$aStates[$row->get(B2tUserState::ID)] = BUGSfactory::userstateLab($row->get(B2tUserState::ID), $row);
				}
				self::$_userstates = $aStates;
			}
			return self::$_userstates;
		}
		
	}
