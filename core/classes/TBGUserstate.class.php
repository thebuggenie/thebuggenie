<?php
	
	/**
	 * User state class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGUserstate extends TBGDatatype 
	{
		protected $_is_online = false;
		protected $_is_unavailable = false;
		protected $_is_busy = false;
		protected $_is_in_meeting = false;
		protected $_is_absent = false;
		
		static $_userstates = null;
		
		/**
		 * returns a TBGUserstate object
		 *
		 * @param integer $us_id
		 * @param B2DBRow $row
		 * 
		 * @return TBGUserstate
		 */
		public function __construct($us_id, $row = null)
		{
			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$row = B2DB::getTable('TBGUserStateTable')->doSelectById($us_id, $crit);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_itemdata = $row->get(TBGUserStateTable::COLOR);
				$this->_name = $row->get(TBGUserStateTable::STATE_NAME);
				$this->_id = $us_id;
				$this->_is_absent = ($row->get(TBGUserStateTable::ABSENT) == 1) ? true : false;
				$this->_is_online = ($row->get(TBGUserStateTable::ONLINE) == 1) ? true : false;
				$this->_is_unavailable = ($row->get(TBGUserStateTable::UNAVAILABLE) == 1) ? true : false;
				$this->_is_busy = ($row->get(TBGUserStateTable::BUSY) == 1) ? true : false;
				$this->_is_in_meeting = ($row->get(TBGUserStateTable::MEETING) == 1) ? true : false;
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
				$crit->addWhere(TBGUserStateTable::SCOPE, TBGContext::getScope()->getID());
				
				$res = B2DB::getTable('TBGUserStateTable')->doSelect($crit);
		
				$aStates = array();
				
				while ($row = $res->getNextRow())
				{
					$aStates[$row->get(TBGUserStateTable::ID)] = TBGContext::factory()->TBGUserstate($row->get(TBGUserStateTable::ID), $row);
				}
				self::$_userstates = $aStates;
			}
			return self::$_userstates;
		}
		
	}
