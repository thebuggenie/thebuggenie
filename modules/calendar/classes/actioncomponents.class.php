<?php

	class calendarActionComponents extends BUGSactioncomponent
	{
		
		public function componentCalendarsummary()
		{
			$nowstart = ($_SERVER["REQUEST_TIME"] - (date("H") * (60 * 60)) - (date("i") * 60) - (date("s")));
			$this->eventstoday = BUGScontext::getModule('calendar')->getEvents($nowstart, $nowstart + 86400);
		}
		
	}
