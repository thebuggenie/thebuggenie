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
		
		protected $_itemtype = TBGDatatype::USERSTATE;
		
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
