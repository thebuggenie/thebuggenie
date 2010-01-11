<?php

	class BUGScalendarEvent extends TBGIdentifiableClass implements TBGIdentifiable
	{
		
		const EVENT = 1;
		const TASK = 2;
		const MEETING = 3;
		
		protected $_startdate = 0;
		protected $_enddate = 0;
		protected $_userstatus = 0;
		protected $_description = '';
		
		/**
		 * Create a new calendar event
		 *
		 * @param B2DBRow $id Integer or B2DBRow
		 */
		public function __construct($id)
		{
			if (!$id instanceof B2DBRow)
			{
				$id = B2DB::getTable('B2tCalendarTasks')->doSelectById($id);
			}
			$this->_itemid = $id->get(B2tCalendarTasks::ID);
			$this->_name = $id->get(B2tCalendarTasks::TITLE);
			$this->_itemtype = $id->get(B2tCalendarTasks::ITEMTYPE);
			$this->_description = $id->get(B2tCalendarTasks::DESCRIPTION);
			$this->_startdate = $id->get(B2tCalendarTasks::STARTS);
			$this->_enddate = $id->get(B2tCalendarTasks::ENDS);
			$this->_userstatus = $id->get(B2tCalendarTasks::STATUS);
		}
		
		/**
		 * Creates a new calendar event and returns it
		 *
		 * @param string $title
		 * @param int $type
		 * @param string $description
		 * @param int $starts
		 * @param int $ends
		 * @param int $userstatus
		 * 
		 * @return BUGScalendarEvent 
		 */
		public static function createNew($title, $type, $description, $starts, $ends, $userstatus, $calendar)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tCalendarTasks::DESCRIPTION, $description);
			$crit->addInsert(B2tCalendarTasks::STARTS, $starts);
			$crit->addInsert(B2tCalendarTasks::ENDS, $ends);
			$crit->addInsert(B2tCalendarTasks::ITEMTYPE, $type);
			$crit->addInsert(B2tCalendarTasks::TITLE, $title);
			$crit->addInsert(B2tCalendarTasks::STATUS, $userstatus);
			$crit->addInsert(B2tCalendarTasks::CALENDAR, $calendar);
			$crit->addInsert(B2tCalendarTasks::LOCATION, 0);
			$crit->addInsert(B2tCalendarTasks::SCOPE, TBGContext::getScope()->getID());
			$res = B2DB::getTable('B2tCalendarTasks')->doInsert($crit);
			return new BUGScalendarEvent($res->getInsertID());
		}
		
		public function isOnDate($period_start, $period_end)
		{
			if ($this->getStartDate() > $period_start && $this->getStartDate() < $period_end) return true;

			if ($this->getEndDate() < $period_end && $this->getEndDate() > $period_start) return true;
			
			if ($this->getEndDate() > $period_end && $this->getStartDate() < $period_start) return true;
			
			return false;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}
		
		public function setDescription($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCalendarTasks::DESCRIPTION, $val);
			$res = B2DB::getTable('B2tCalendarTasks')->doUpdateById($crit, $this->getID());
			$this->_description = $val;
		}
		
		public function getStartDate()
		{
			return $this->_startdate;
		}

		public function setStartDate($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCalendarTasks::STARTS, $val);
			$res = B2DB::getTable('B2tCalendarTasks')->doUpdateById($crit, $this->getID());
			$this->_startdate = $val;
		}
		
		public function getEndDate()
		{
			return $this->_enddate;
		}
		
		public function setEndDate($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCalendarTasks::ENDS, $val);
			$res = B2DB::getTable('B2tCalendarTasks')->doUpdateById($crit, $this->getID());
			$this->_enddate = $val;
		}
		
		public function getType()
		{
			return $this->_itemtype;
		}
		
		public function setType($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCalendarTasks::ITEMTYPE, $val);
			$res = B2DB::getTable('B2tCalendarTasks')->doUpdateById($crit, $this->getID());
			$this->_itemtype = $val;
		}
		
		public function getTitle()
		{
			return $this->_name;
		}
		
		public function setTitle($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCalendarTasks::TITLE, $val);
			$res = B2DB::getTable('B2tCalendarTasks')->doUpdateById($crit, $this->getID());
			$this->_name = $val;
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
		
		public function getUserStatus()
		{
			return $this->_userstatus;
		}

		public function setUserStatus($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tCalendarTasks::STATUS, $val);
			$res = B2DB::getTable('B2tCalendarTasks')->doUpdateById($crit, $this->getID());
			$this->_userstatus = $val;
		}
		
	}

?>