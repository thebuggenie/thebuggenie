<?php

	class calendarActionComponents extends TBGActionComponent
	{
		
		public function componentCalendarsummary()
		{
			$nowstart = (NOW - (date("H") * (60 * 60)) - (date("i") * 60) - (date("s")));
			$this->eventstoday = TBGContext::getModule('calendar')->getEvents($nowstart, $nowstart + 86400);
		}
		
	}
