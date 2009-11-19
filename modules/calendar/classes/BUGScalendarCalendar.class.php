<?php

	class BUGScalendarCalendar extends BUGSidentifiableclass implements BUGSidentifiable
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
				$id = B2DB::getTable('B2tCalendars')->doSelectById($id);
			}
			$this->_itemid = $id->get(B2tCalendars::ID);
			$this->_name = $id->get(B2tCalendars::NAME);
			$this->_itemtype = 0;
			$this->_user = BUGSfactory::userLab($id->get(B2tCalendars::UID));
			$this->_exclusive = ($id->get(B2tCalendars::EXCLUSIVE) == 1) ? true : false;
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
			$crit->addInsert(B2tCalendars::UID, $uid);
			$crit->addInsert(B2tCalendars::EXCLUSIVE, false);
			$row = B2DB::getTable('B2tCalendars')->doInsert($crit);
			$calendar = new BUGScalendarCalendar($row->getInsertID());
			return $calendar;
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		public function getID()
		{
			return $this->_itemid;
		}
		
		public function getName()
		{
			return $this->_name;
		}
		
	}

?>