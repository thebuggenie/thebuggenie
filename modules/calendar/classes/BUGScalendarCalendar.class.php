<?php

	class BUGScalendarCalendar extends TBGIdentifiableClass
	{
		protected $_user = null;
		protected $_exclusive = false;
		
		/**
		 * Create a new calendar event
		 *
		 * @param B2DBRow $id Integer or B2DBRow
		 */
		public function __construct($id)
		{
			if (!$id instanceof B2DBRow)
			{
				$id = B2DB::getTable('TBGCalendarsTable')->doSelectById($id);
			}
			$this->_id = $id->get(TBGCalendarsTable::ID);
			$this->_name = $id->get(TBGCalendarsTable::NAME);
			$this->_itemtype = 0;
			$this->_user = TBGContext::factory()->TBGUser($id->get(TBGCalendarsTable::UID));
			$this->_exclusive = ($id->get(TBGCalendarsTable::EXCLUSIVE) == 1) ? true : false;
		}
		
		/**
		 * Create a new calendar and return it
		 *
		 * @param integer $uid
		 * @return BUGScalendarCalendar
		 */
		public static function createNew($uid)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGCalendarsTable::UID, $uid);
			$crit->addInsert(TBGCalendarsTable::EXCLUSIVE, false);
			$row = B2DB::getTable('TBGCalendarsTable')->doInsert($crit);
			$calendar = new BUGScalendarCalendar($row->getInsertID());
			return $calendar;
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		public function getID()
		{
			return $this->_id;
		}
		
		public function getName()
		{
			return $this->_name;
		}
		
	}

?>