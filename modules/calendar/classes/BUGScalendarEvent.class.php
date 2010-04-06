<?php

	class BUGScalendarEvent extends TBGIdentifiableClass
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
				$id = B2DB::getTable('TBGCalendarTasksTable')->doSelectById($id);
			}
			$this->_itemid = $id->get(TBGCalendarTasksTable::ID);
			$this->_name = $id->get(TBGCalendarTasksTable::TITLE);
			$this->_itemtype = $id->get(TBGCalendarTasksTable::ITEMTYPE);
			$this->_description = $id->get(TBGCalendarTasksTable::DESCRIPTION);
			$this->_startdate = $id->get(TBGCalendarTasksTable::STARTS);
			$this->_enddate = $id->get(TBGCalendarTasksTable::ENDS);
			$this->_userstatus = $id->get(TBGCalendarTasksTable::STATUS);
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
			$crit->addInsert(TBGCalendarTasksTable::DESCRIPTION, $description);
			$crit->addInsert(TBGCalendarTasksTable::STARTS, $starts);
			$crit->addInsert(TBGCalendarTasksTable::ENDS, $ends);
			$crit->addInsert(TBGCalendarTasksTable::ITEMTYPE, $type);
			$crit->addInsert(TBGCalendarTasksTable::TITLE, $title);
			$crit->addInsert(TBGCalendarTasksTable::STATUS, $userstatus);
			$crit->addInsert(TBGCalendarTasksTable::CALENDAR, $calendar);
			$crit->addInsert(TBGCalendarTasksTable::LOCATION, 0);
			$crit->addInsert(TBGCalendarTasksTable::SCOPE, TBGContext::getScope()->getID());
			$res = B2DB::getTable('TBGCalendarTasksTable')->doInsert($crit);
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
			$crit->addUpdate(TBGCalendarTasksTable::DESCRIPTION, $val);
			$res = B2DB::getTable('TBGCalendarTasksTable')->doUpdateById($crit, $this->getID());
			$this->_description = $val;
		}
		
		public function getStartDate()
		{
			return $this->_startdate;
		}

		public function setStartDate($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCalendarTasksTable::STARTS, $val);
			$res = B2DB::getTable('TBGCalendarTasksTable')->doUpdateById($crit, $this->getID());
			$this->_startdate = $val;
		}
		
		public function getEndDate()
		{
			return $this->_enddate;
		}
		
		public function setEndDate($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCalendarTasksTable::ENDS, $val);
			$res = B2DB::getTable('TBGCalendarTasksTable')->doUpdateById($crit, $this->getID());
			$this->_enddate = $val;
		}
		
		public function getType()
		{
			return $this->_itemtype;
		}
		
		public function setType($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCalendarTasksTable::ITEMTYPE, $val);
			$res = B2DB::getTable('TBGCalendarTasksTable')->doUpdateById($crit, $this->getID());
			$this->_itemtype = $val;
		}
		
		public function getTitle()
		{
			return $this->_name;
		}
		
		public function setTitle($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCalendarTasksTable::TITLE, $val);
			$res = B2DB::getTable('TBGCalendarTasksTable')->doUpdateById($crit, $this->getID());
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
			$crit->addUpdate(TBGCalendarTasksTable::STATUS, $val);
			$res = B2DB::getTable('TBGCalendarTasksTable')->doUpdateById($crit, $this->getID());
			$this->_userstatus = $val;
		}
		
	}

?>