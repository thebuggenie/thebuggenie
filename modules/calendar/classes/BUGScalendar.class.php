<?php

	class BUGScalendar extends BUGSmodule 
	{
		
		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_menu_title = BUGScontext::getI18n()->__("Calendar");
			$this->_module_config_title = BUGScontext::getI18n()->__("Calendar");
			$this->_module_config_description = BUGScontext::getI18n()->__('Configure the Calendar module in this section.');
			$this->_module_version = "1.0";
			if ($this->_enabled)
			{
				//B2DB::loadNewTable(new B2tCalendars());
				//B2DB::loadNewTable(new B2tCalendarTasks());
			}
			$this->addAvailableSection('core', 'dashboard_left_top', 'Dashboard calendar summary');
			$this->addAvailableSection('core', 'account_settings', '"My account" settings');
			$this->addAvailableSection('core', 'account_settingslist', '"My account" drop-down settings');
			$this->addAvailableSection('core', 'BUGSUser::getState', 'Automatic user-state change');
		}
		
		public function getCommentAccess($target_type, $target_id, $type = 'view')
		{
			
		}
		
		public function enableSection($module, $identifier, $scope)
		{
			$function_name = '';
			switch ($module . '_' . $identifier)
			{
				case 'core_account_settingslist':
					$function_name = 'section_accountSettingsList';
					break;
				case 'core_BUGSUser::getState':
					$function_name = 'section_bugsuser_getState';
					break;
				case 'core_account_settings':
					$function_name = 'section_accountSettings';
					break;
				case 'core_dashboard_left_top':
					$function_name = 'section_calendarSummary';
					break;
			}
			if ($function_name != '') parent::createSection($module, $identifier, $function_name, $scope);
		}
		
		static public function install($scope = null)
		{
  			if ($scope === null)
  			{
  				$scope = BUGScontext::getScope()->getID();
  			}
			$module = parent::_install('calendar', 
  									  'Calendar', 
  									  'Enables calendar, todo\'s and meetings',
  									  'BUGScalendar',
  									  true, false, true,
  									  '1.0',
  									  true,
  									  $scope);

			$module->setPermission(0, 0, 0, true, $scope);
			$module->enableSection('core', 'dashboard_left_top', $scope);
			$module->enableSection('core', 'account_settings', $scope);
			$module->enableSection('core', 'account_settingslist', $scope);
			$module->enableSection('core', 'BUGSUser::getState', $scope);
			
			if ($scope == BUGScontext::getScope()->getID())
			{
				$module->setPermission(0, 3, 0, false, $scope);
				//B2DB::loadNewTable(new B2tCalendars());
				//B2DB::loadNewTable(new B2tCalendarTasks());
				B2DB::getTable('B2tCalendars')->create();
				B2DB::getTable('B2tCalendarTasks')->create();
			}
			
			try
			{
				self::loadFixtures($scope, $module);
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
		}
		
		public function uninstall($scope)
		{
			B2DB::getTable('B2tCalendars')->drop();
			B2DB::getTable('B2tCalendarTasks')->drop();
			parent::uninstall($scope);
		}

		static function loadFixtures($scope, $module)
		{
			try
			{
				$module->saveSetting('weekstart', 1);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		/**
		 * Get events for a user
		 *
		 * @param unknown_type $startdate
		 * @param unknown_type $enddate
		 * @param unknown_type $uid
		 * @param unknown_type $calendar
		 * @param unknown_type $bypass
		 * @return unknown
		 */
		public function getEvents($startdate, $enddate, $uid = null, $calendar = null, $bypass = false)
		{
			if ($uid === null)
			{
				$uid = BUGScontext::getUser()->getUID();
			}
			if ($calendar == null)
			{
				$calendar = BUGScontext::getModule('calendar')->getSetting('calendar', $uid);
				if ($calendar === null)
				{
					$calendar = BUGScalendarCalendar::createNew($uid)->getID();
					BUGScontext::getModule('calendar')->saveSetting('calendar', $calendar, $uid);
				}
			}
			
			$tasks = array();
			if ($bypass == true)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tCalendars::UID, $uid);
				$res = B2DB::getTable('B2tCalendars')->doSelect($crit);
				$calendar = array();
				while ($row = $res->getNextRow())
				{
					$calendar[] = $row->get(B2tCalendars::ID);
				}
			}
			
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tCalendarTasks::CALENDAR, $calendar, B2DBCriteria::DB_IN);
			$ctn = $crit->returnCriterion(B2tCalendarTasks::STARTS, $startdate, B2DBCriteria::DB_LESS_THAN);
			$ctn2 = $crit->returnCriterion(B2tCalendarTasks::STARTS, $startdate, B2DBCriteria::DB_GREATER_THAN);
			$ctn2->addWhere(B2tCalendarTasks::STARTS, $enddate, B2DBCriteria::DB_LESS_THAN);
			$ctn->addOr($ctn2);
			$crit->addWhere($ctn);
			$ctn = $crit->returnCriterion(B2tCalendarTasks::ENDS, $enddate, B2DBCriteria::DB_GREATER_THAN);
			$ctn2 = $crit->returnCriterion(B2tCalendarTasks::ENDS, $enddate, B2DBCriteria::DB_LESS_THAN);
			$ctn2->addWhere(B2tCalendarTasks::ENDS, $startdate, B2DBCriteria::DB_GREATER_THAN);
			$ctn->addOr($ctn2);
			$crit->addWhere($ctn);
			$crit->addOrderBy(B2tCalendarTasks::STARTS, 'asc');
			
			if ($res = B2DB::getTable('B2tCalendarTasks')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$tasks[$row->get(B2tCalendarTasks::ID)] = new BUGScalendarEvent($row);
				}
			}
			
			return $tasks;
			
		}		
		
		public function section_accountSettingsList()
		{
			include_template('calendar/accountsettingslist');
		}

		public function section_accountSettings($module)
		{
			if ($module != $this->getName()) return;
			if (BUGScontext::getRequest()->getParameter('weekstart'))
			{
				BUGScontext::getModule('calendar')->saveSetting('weekstart', BUGScontext::getRequest()->getParameter('weekstart'), BUGScontext::getUser()->getUID());
			}
			if (BUGScontext::getRequest()->getParameter('calendarstartup'))
			{
				BUGScontext::getModule('calendar')->saveSetting('calendarstartup', BUGScontext::getRequest()->getParameter('calendarstartup'), BUGScontext::getUser()->getUID());
			}
			if (BUGScontext::getRequest()->getParameter('hideweekends'))
			{
				BUGScontext::getModule('calendar')->saveSetting('hideweekends', BUGScontext::getRequest()->getParameter('hideweekends'), BUGScontext::getUser()->getUID());
			}
			?>
			<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 15px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-left: 4px; width: 20px;"><?php echo image_tag('tab_calendar.png'); ?></td>
			<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo BUGScontext::getI18n()->__('Calendar settings'); ?></b></td>
			</tr>
			</table>
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="account.php" method="post">
			<input type="hidden" name="settings" value="<?php echo $this->getName(); ?>">
			<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
			<tr>
			<td style="width: 150px;"><b><?php echo BUGScontext::getI18n()->__('Week starts on'); ?></b></td>
			<td style="width: 300px;"><select name="weekstart" style="width: 100%;">
			<option value=1 <?php if (BUGScontext::getModule('calendar')->getSetting('weekstart', BUGScontext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Monday'); ?></option>
			<option value=0 <?php if (BUGScontext::getModule('calendar')->getSetting('weekstart', BUGScontext::getUser()->getUID()) == 0) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Sunday'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td style="width: 150px;"><b><?php echo BUGScontext::getI18n()->__('Show only workdays'); ?></b></td>
			<td style="width: 300px;"><select name="hideweekends" style="width: 100%;">
			<option value=1 <?php if (BUGScontext::getModule('calendar')->getSetting('hideweekends', BUGScontext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Yes, only show workdays'); ?></option>
			<option value=0 <?php if (BUGScontext::getModule('calendar')->getSetting('hideweekends', BUGScontext::getUser()->getUID()) == 0) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('No, show the whole week in week overview'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td style="width: 150px;"><b><?php echo BUGScontext::getI18n()->__('Calendar display'); ?></b></td>
			<td style="width: 300px;"><select name="calendarstartup" style="width: 100%;">
			<option value="month" <?php if (BUGScontext::getModule('calendar')->getSetting('calendarstartup', BUGScontext::getUser()->getUID()) == "month") echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Show the current month'); ?></option>
			<option value="week" <?php if (BUGScontext::getModule('calendar')->getSetting('calendarstartup', BUGScontext::getUser()->getUID()) == "week") echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Show current week'); ?></option>
			<option value="day" <?php if (BUGScontext::getModule('calendar')->getSetting('calendarstartup', BUGScontext::getUser()->getUID()) == "day") echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Show todays overview'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td colspan=2><?php echo BUGScontext::getI18n()->__('Select what to show by default when you go to your calendar'); ?></td>
			</tr>
			<tr>
			<td colspan=2 style="text-align: right;"><input type="submit" value="<?php echo BUGScontext::getI18n()->__('Save'); ?>"></td>
			</tr>
			</table>
			</form>
			<?php
		}
		
		public function section_bugsuser_getState($theUser)
		{
			$theUser = $vars;
			$events = $this->getEvents($_SERVER["REQUEST_TIME"], $_SERVER["REQUEST_TIME"]);
			foreach ($events as $anevent)
			{
				$theUser->setState($anevent->getUserStatus());
			}
		}
		
		public function section_calendarSummary()
		{
			include_component('calendar/calendarsummary');
		}
		
		public function section_indexLeftTop()
		{
			if (BUGScontext::getUser()->getUID() != 0)
			{
				?>
				<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
				<tr>
				<td class="b2_section_miniframe_header"><?php echo BUGScontext::getI18n()->__('Calendar items and tasks'); ?></td>
				</tr>
				<tr>
				<td class="td1">
				<?php

					$nowstart = ($_SERVER["REQUEST_TIME"] - (date("H") * (60 * 60)) - (date("i") * 60) - (date("s")));
					$eventstoday = $this->getEvents($nowstart, $nowstart + 86400);

					if (count($eventstoday) > 0)
					{

						echo '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
						foreach ($eventstoday as $anevent)
						{
							echo '<tr>';
							switch ($anevent->getType())
							{
								case BUGScalendarEvent::EVENT:
									?>
									<td class="imgtd"><?php echo image_tag('calendar/event.png'); ?></td>
									<?php
									break;
								case BUGScalendarEvent::TASK:
									?>
									<td class="imgtd"><?php echo image_tag('calendar/task.png'); ?></td>
									<?php
									break;
								case BUGScalendarEvent::MEETING:
									?>
									<td class="imgtd"><?php echo image_tag('calendar/meeting.png'); ?></td>
									<?php
									break;
							}
							?>
							<td><a href="javascript:void(0);" onclick="window.open('<?php echo BUGScontext::getTBGPath(); ?>modules/calendar/show_event.php?id=<?php echo $anevent->getID(); ?>','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');"><?php echo $anevent->getTitle(); ?></a><br>
							<div><?php echo bugs_formatTime($anevent->getStartDate(), 12); ?> - <?php echo bugs_formatTime($anevent->getEndDate(), 12); ?></div></td>
							</tr>
							<?php
						}
					}
					else
					{
						?>
						<div style="padding: 2px;"><?php echo BUGScontext::getI18n()->__('You have nothing scheduled for today'); ?></div>
						<table cellpadding=0 cellspacing=0 style="width: 100%;">
						<?php
					}

					?>
					<tr>
					<td class="imgtd">&nbsp;</td>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td class="imgtd"><?php echo image_tag('tab_calendar.png'); ?></td>
					<td><a href="modules/calendar/calendar.php"><i><?php echo BUGScontext::getI18n()->__('Go to my calendar'); ?></i></a></td>
					</tr>
					</table>
				</td>
				</tr>
				</table>
				<?php
			}
		}
		
		public function html_day($day, $month, $year)
		{
			$daystart = mktime(0, 0, 0, $month, $day, $year);
			$dayend = ($daystart + 86400);
			
			$events = BUGScontext::getModule('calendar')->getEvents($daystart, $dayend);

			$retval = '<table class="calendar_day" cellpadding=0 cellspacing=0>';
			$retval .= '<caption class="calendar-day-big">';
			// prev-link
			$retval .= '<span class="smaller_day_link"><a href="javascript:void(0);" onclick="getOverview(' . date('d, m, Y', ($daystart - 1)) . ');">' . bugs_formatTime(($daystart - 1), 16) . '</a></span>';
			
			$retval .= bugs_formatTime($daystart, 16) . ',&nbsp;' . $year;

			// next-link
			$retval .= '<span class="smaller_day_link"><a href="javascript:void(0);" onclick="getOverview(' . date('d, m, Y', ($dayend + 1)) . ');">' . bugs_formatTime(($dayend + 1), 16) . '</a></span>';
			
			$retval .= '</caption>';
			$retval .= '<tr>';
			$retval .= '</table>';
			$retval .= '<div style="padding: 5px;" id="calendar_day_list">';
			
			foreach ($events as $anevent)
			{
				$retval .= '<div class="calendar_event"';
				$retval .= " onclick=\"window.open('" . BUGScontext::getTBGPath() . "modules/calendar/show_event.php?id=" . $anevent->getID() . "','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');\"";
				$retval .= '>';
				switch ($anevent->getType())
				{
					case BUGScalendarEvent::EVENT:
						$retval .= image_tag('calendar/event.png', 'style="float: left;"');
						break;
					case BUGScalendarEvent::TASK:
						$retval .= image_tag('calendar/task.png', 'style="float: left;"');
						break;
					case BUGScalendarEvent::MEETING:
						$retval .= image_tag('calendar/meeting.png', 'style="float: left;"');
						break;
				}
				$retval .= '<b>' . bugs_formatTime($anevent->getStartDate(), 12) . ' -&gt; ' . bugs_formatTime($anevent->getEndDate(), 12) . ':&nbsp; ' . $anevent->getTitle() . '</b><br>';
				$retval .= bugs_BBDecode($anevent->getDescription());
				$retval .= '</div>';
			}
			
			$retval .= '</div>';
			
			return $retval;
		}
		
		public function html_week($day, $month, $year)
		{
			$given_day = mktime(0, 0, 0, $month, $day, $year);

			//echo $day . ',' . $month . ',' . $year;
			$week_offset = BUGScontext::getModule('calendar')->getSetting('weekstart', BUGScontext::getUser()->getUID());
			$hideweekend = BUGScontext::getModule('calendar')->getSetting('hideweekends', BUGScontext::getUser()->getUID());
			$weekday = date('w', $given_day) - $week_offset;
			if ($weekday < 0) $weekday = 6;

			$first_of_week = mktime(0, 0, 0, $month, ($day - $weekday), $year);
			$last_of_week = ($first_of_week + (86400 * 7));
			
			$events = BUGScontext::getModule('calendar')->getEvents($first_of_week, $last_of_week);

			$day_names = array();
			for($n = 0, $t = $first_of_week; $n < 7; $n++, $t += 86400)
			{
				$day_names[$n] = bugs_formatTime($t, 17);
			}
			
			$retval = '<table class="calendar_week" cellpadding=0 cellspacing=0>';
			$retval .= '<caption class="calendar-week-big">';
			// prev-link
			$retval .= '<span class="smaller_week_link"><a href="javascript:void(0);" onclick="getWeek(' . date('d, m, Y', ($first_of_week - 1)) . ');">Week ' . date('W', ($first_of_week - 1)) . ',&nbsp;' . date('Y', ($first_of_week - 1)) . '</a></span>';
			
			$retval .= 'Week ' . date('W', $first_of_week) . ',&nbsp;' . $year;

			// next-link
			$retval .= '<span class="smaller_week_link"><a href="javascript:void(0);" onclick="getWeek(' . date('d, m, Y', ($last_of_week + 1)) . ');">Week ' . date('W', ($last_of_week + 1)) . ',&nbsp;' . date('Y', ($last_of_week + 1)) . '</a></span>';
			
			$retval .= '</caption>';
			$retval .= '<tr>';
		
			$cc = 0;
			foreach ($day_names as $day)
			{
				if ($hideweekend == 0 || $cc < 5) 
				$retval .= '<th abbr="' . bugs_sanitize_string($day) . '">' . bugs_sanitize_string($day) . '</th>';
				$cc++;
			}
			$retval .= '</tr>';
			
			for ($day = $first_of_week, $cc = 0; $cc < 7; $cc++, $day += 86400)
			{
				if ($hideweekend == 0 || $cc < 5)
				{
					$retval .= '<td><div style="padding: 3px; text-align: right; font-size: 10px;">' . date('d', $day) . '</div>';
					foreach ($events as $anevent)
					{
						if ($anevent->isOnDate($day, ($day + 86400)))
						{
							$retval .= '<div class="calendar_event"';
							$retval .= " onclick=\"window.open('" . BUGScontext::getTBGPath() . "modules/calendar/show_event.php?id=" . $anevent->getID() . "','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');\"";
							$retval .= '>';
							switch ($anevent->getType())
							{
								case BUGScalendarEvent::EVENT:
									$retval .= image_tag('calendar/event.png', 'style="float: left;"');
									break;
								case BUGScalendarEvent::TASK:
									$retval .= image_tag('calendar/task.png', 'style="float: left;"');
									break;
								case BUGScalendarEvent::MEETING:
									$retval .= image_tag('calendar/meeting.png', 'style="float: left;"');
									break;
							}
							$retval .= '<b>' . $anevent->getTitle() . '</b><br>';
							$retval .= bugs_formatTime($anevent->getStartDate(), 12) . ' -&gt; ' . bugs_formatTime($anevent->getEndDate(), 12); 
							$retval .= '</div>';
						}
					}
					$retval .= '</td>';
				}
			}

			$retval .= '</table>';
			
			return $retval;
		}
		
		public function html_calendar($year, $month, $mode = 'mini', $show_events = true, $load_events_in_div = '')
		{
			/**
			 * Original code by Keith Devens (keithdevens.com)
			 */
			$week_offset = BUGScontext::getModule('calendar')->getSetting('weekstart', BUGScontext::getUser()->getUID());
			$first_of_month = mktime(0, 0, 0, $month, 1, $year);
			$last_of_month = mktime(23, 59, 59, $month, date('t', $first_of_month), $year);
			
			//echo $first_of_month . '-' . $last_of_month;
			$events = BUGScontext::getModule('calendar')->getEvents($first_of_month, $last_of_month);
			
			$prev_month_days = (date('t', $first_of_month - 1) + 1);
			$prev_month = date('m', $first_of_month - 1);
			$prev_month_name = bugs_formatTime($first_of_month - 1, 15);
			$prev_month_year = date('Y', $first_of_month - 1);
			
			$next_month = date('m', $last_of_month + 1);
			$next_month_name = bugs_formatTime($last_of_month + 1, 15);
			$next_month_year = date('Y', $last_of_month + 1);
						
			$week_no = date('W', $first_of_month);
		
			$day_names = array();
			for($n = 0, $t = (3 + $week_offset) * 86400; $n < 7; $n++, $t += 86400)
			{
				$day_names[$n] = bugs_formatTime($t, 17);
			}
			
			list($month, $year, $weekday) = explode(',', date('m,Y,w', $first_of_month));
			$month_name = bugs_formatTime($first_of_month, 15);
			$weekday = (($weekday + 7 - $week_offset) % 7);
			
			if ($mode == 'mini')
			{
				$retval = '<table class="calendar" cellpadding=0 cellspacing=0>';
				$retval .= '<caption class="calendar-month b2_section_miniframe_header">';
				// prev-link
				$retval .= '<span class="smallest_month_link"><a class="image" href="javascript:void(0);" onclick="getMonth(' . $month . ', ' . ($year - 1) . ', \'mini\');">' . image_tag('calendar/prev_year.png', '', '', '', 0, 12, 12) . '</a></span>';
				$retval .= '<span class="smaller_month_link"><a class="image" href="javascript:void(0);" onclick="getMonth(' . $prev_month . ', ' . $prev_month_year . ', \'mini\');">' . image_tag('calendar/prev_month.png', '', '', '', 0, 12, 12) . '</a></span>';

				// current month
				$retval .= '<span class="minimonth_monthname"><a href="javascript:void(0);" onclick="getMonth(' . $month . ', ' . $year . ', \'full\');">' . bugs_sanitize_string(ucfirst($month_name)) . '&nbsp;' . $year . '</a></span>';

				// next-link
				$retval .= '<span class="smaller_month_link"><a class="image" href="javascript:void(0);" onclick="getMonth(' . $next_month . ', ' . $next_month_year . ', \'mini\');">' . image_tag('calendar/next_month.png', '', '', '', 0, 12, 12) . '</a></span>';
				$retval .= '<span class="smallest_month_link"><a class="image" href="javascript:void(0);" onclick="getMonth(' . $month . ', ' . ($year + 1) . ', \'mini\');">' . image_tag('calendar/next_year.png', '', '', '', 0, 12, 12) . '</a></span>';
				// next-link
				$retval .= '</caption>';
				$retval .= '<tr>';
				$retval .= '<th class="weeknumber">&nbsp;</th>';
			
				foreach ($day_names as $day)
				{
					$retval .= '<th abbr="' . bugs_sanitize_string($day) . '">' . bugs_sanitize_string($day) . '</th>';
				}
				$retval .= '</tr>';
				
				$retval .= '<tr>';
				$retval .= '<td class="weeknumber">';
				$retval .= '<a href="javascript:void(0);" onclick="getWeek(' . date('d', $first_of_month) . ', ' . $month . ', ' . $year .');">' . $week_no . '</a>';
				$retval .= '</td>';
				$prev_weekday = $weekday;
				while ($weekday > 0)
				{
					$retval .= '<td class="day prev_month">' . ($prev_month_days - $weekday) . '</td>';
					$weekday--;
				}
				$weekday = $prev_weekday;
				
				for ($day = 1, $days_in_month = date('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++)
				{
					$daystart = mktime(0, 0, 0, $month, $day, $year);
					$dayend = mktime(23, 59, 59, $month, $day, $year);
					if ($weekday == 7)
					{
						$week_no++;
						$weekday   = 0;
						$retval .= '</tr><tr>';
						$retval .= '<td class="weeknumber">';
						$retval .= '<a href="javascript:void(0);" onclick="getWeek(' . $day . ', ' . $month . ', ' . $year .');">' . $week_no . '</a>';
						$retval .= '</td>';
					}
					$retval .= '<td class="day';
					if ($daystart == mktime(0, 0, 0)) $retval .= ' current';
					$retval .= '">';
					$bold = false;
					foreach ($events as $anevent)
					{
						if ($anevent->isOnDate($daystart, $dayend))
						{
							$bold = true;
							break;
						}
					}
					if ($bold)
					{
						$retval .= '<b>';
					}
					$retval .= '<a href="javascript:void(0);" onclick="getOverview(' . $day . ', ' . $month . ', ' . $year .');">' . $day . '</a>';
					if ($bold)
					{
						$retval .= '</b>';
					}
					$retval .= '</td>';
				}
				
				$cc = 1;
				while ($weekday < 7)
				{
					$retval .= '<td class="day prev_month">' . $cc . '</td>';
					$weekday++;
					$cc++;
				}
			
				$retval .= '</tr>';
				$retval .= '</table>';
			}
			elseif ($mode == 'full')
			{
				$retval = '<table class="calendar_main" cellpadding=0 cellspacing=0 style="width: 100%; height: 100%;">';
				$retval .= '<caption class="calendar-month-big">';
				// prev-link
				$retval .= '<span class="smallest_month_link"><a href="javascript:void(0);" onclick="getMonth(' . $month . ', ' . ($year - 1) . ', \'full\');">' . ($year - 1). '</a></span>';
				$retval .= '<span class="smaller_month_link"><a href="javascript:void(0);" onclick="getMonth(' . $prev_month . ', ' . $prev_month_year . ', \'full\');">' . bugs_sanitize_string(ucfirst($prev_month_name)) . '&nbsp;' . $prev_month_year . '</a></span>';

				// current month
				$retval .= bugs_sanitize_string(ucfirst($month_name)) . '&nbsp;' . $year;

				// next-link
				$retval .= '<span class="smaller_month_link"><a href="javascript:void(0);" onclick="getMonth(' . $next_month . ', ' . $next_month_year . ', \'full\');">' . bugs_sanitize_string(ucfirst($next_month_name)) . '&nbsp;' . $next_month_year . '</a></span>';
				$retval .= '<span class="smallest_month_link"><a href="javascript:void(0);" onclick="getMonth(' . $month . ', ' . ($year + 1) . ', \'full\');">' . ($year + 1). '</a></span>';
				
				$retval .= '</caption>';
				$retval .= '<tr>';
				$retval .= '<th class="weeknumber">&nbsp;</th>';
			
				foreach ($day_names as $day)
				{
					$retval .= '<th abbr="' . bugs_sanitize_string($day) . '">' . bugs_sanitize_string($day) . '</th>';
				}
				$retval .= '</tr>';
				
				$retval .= '<tr>';
				$retval .= '<td class="weeknumber">';
				$retval .= '<a href="javascript:void(0);" onclick="getWeek(' . date('d', $first_of_month) . ', ' . $month . ', ' . $year .');">W<br>' . $week_no . '</a>';
				$retval .= '</td>';

				$prev_weekday = $weekday;
				while ($weekday > 0)
				{
					$retval .= '<td class="day prev_month">' . ($prev_month_days - $weekday) . '</td>';
					$weekday--;
				}
				$weekday = $prev_weekday;
				
				for ($day = 1, $days_in_month = date('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++)
				{
					$daystart = mktime(0, 0, 0, $month, $day, $year);
					$dayend = mktime(23, 59, 59, $month, $day, $year);
					if ($weekday == 7)
					{
						$week_no++;
						$weekday   = 0;
						$retval .= '</tr><tr>';
						$retval .= '<td class="weeknumber">';
						$retval .= '<a href="javascript:void(0);" onclick="getWeek(' . $day . ', ' . $month . ', ' . $year .');">W<br>' . $week_no . '</a>';
						$retval .= '</td>';
					}
					$retval .= '<td class="day';
					if ($daystart == mktime(0, 0, 0)) $retval .= ' current';
					$retval .= '">';
					$retval .= '<a href="javascript:void(0);" onclick="getOverview(' . $day . ', ' . $month . ', ' . $year .');">' . $day . '</a>';
					foreach ($events as $anevent)
					{
						if ($anevent->isOnDate($daystart, $dayend))
						{
							$retval .= '<div class="calendar_event"';
							$retval .= " onclick=\"window.open('" . BUGScontext::getTBGPath() . "modules/calendar/show_event.php?id=" . $anevent->getID() . "','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');\"";
							$retval .= '>';
							switch ($anevent->getType())
							{
								case BUGScalendarEvent::EVENT:
									$retval .= image_tag('calendar/event.png', 'style="float: left;"');
									break;
								case BUGScalendarEvent::TASK:
									$retval .= image_tag('calendar/task.png', 'style="float: left;"');
									break;
								case BUGScalendarEvent::MEETING:
									$retval .= image_tag('calendar/meeting.png', 'style="float: left;"');
									break;
							}
							$retval .= '<b>' . $anevent->getTitle() . '</b>';
							$retval .= '</div>';
						}
					}
					$retval .= '</td>';
				}
				
				$cc = 1;
				while ($weekday < 7)
				{
					$retval .= '<td class="day prev_month">' . $cc . '</td>';
					$weekday++;
					$cc++;
				}
			
				$retval .= '</tr>';
				$retval .= '</table>';
			}
			
			return $retval;
		}
		
		
	}

?>