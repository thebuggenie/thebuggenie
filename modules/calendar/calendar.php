<?php

	define ('THEBUGGENIE_PATH', '../../');
	$page = 'calendar';

	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';
	
	require TBGContext::getIncludePath() . "include/ui_functions.inc.php";
	
	require TBGContext::getIncludePath() . "include/header.inc.php";
	require TBGContext::getIncludePath() . "include/menu.inc.php";
	
	TBGContext::getModule('calendar')->activate();
	
	$month = date('m');
	$year = date('Y');

?>

<table style="margin-top: 0px; table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
<tr>
	<td style="width: 255px;" valign="top">
	<div id="calendar_mini_container">
	<?php 

	echo TBGContext::getModule('calendar')->html_calendar($year, $month, 'mini'); 
	
	?>
	</div>
	<div id="events_list">
	<div class="heading"><?php echo __('What\'s happening today'); ?></div>
	<table cellpadding=0 cellspacing=0 style="width: 100%;" id="today_list">
	<?php

	$events = TBGContext::getModule('calendar')->getEvents(mktime(0, 0, 0), mktime(23, 59, 59));
	foreach ($events as $anevent)
	{
		?>
		<tr>
		<?php
		switch ($anevent->getType())
		{
			case BUGScalendarEvent::EVENT:
				?>
				<td class="imgtd" style="vertical-align: top;"><?php echo image_tag('calendar/event.png'); ?></td>
				<?php
				break;
			case BUGScalendarEvent::TASK:
				?>
				<td class="imgtd" style="vertical-align: top;"><?php echo image_tag('calendar/task.png'); ?></td>
				<?php
				break;
			case BUGScalendarEvent::MEETING:
				?>
				<td class="imgtd" style="vertical-align: top;"><?php echo image_tag('calendar/meeting.png'); ?></td>
				<?php
				break;
		}
		?>
		<td><a href="javascript:void(0);" onclick="window.open('show_event.php?id=<?php echo $anevent->getID(); ?>','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');"><?php print $anevent->getTitle(); ?></a><br>
		<div class="today_list_entry"><?php print tbg__formatTime($anevent->getStartDate(), 12); ?> -&gt; <?php print tbg__formatTime($anevent->getEndDate(), 12); ?></div></td>
		</tr>
		<?php
	}
	
	?>
	</table>
	<?php

	if (count($events) == 0)
	{
		echo '<div style="color: #AAA; padding: 5px;">' . __('You have nothing scheduled for today') . '</div>';
	}
	
	?>
	</div>
	</td>
	<td style="width: auto;" valign="top">
	<div id="calendar_main_view">
	<div id="calendar_main_icons_help"></div>
	<div id="calendar_main_icons">
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td><a href="javascript:void(0);" class="image" onclick="getMonth(<?php echo $month; ?>, <?php echo $year; ?>, 'full');"><?php echo image_tag('calendar/month.png'); ?></a></td>
				<td class="text"><a href="javascript:void(0);" onclick="getMonth(<?php echo $month; ?>, <?php echo $year; ?>, 'full');"><?php echo __('Show current month'); ?></a></td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td><a href="javascript:void(0);" class="image" onclick="getWeek(<?php echo date('d'); ?>, <?php echo $month; ?>, <?php echo $year; ?>);"><?php echo image_tag('calendar/7days.png'); ?></a></td>
				<td class="text"><a href="javascript:void(0);" onclick="getWeek(<?php echo date('d'); ?>, <?php echo $month; ?>, <?php echo $year; ?>);"><?php echo __('Show current week'); ?></a></td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td><a href="javascript:void(0);" class="image" onclick="getOverview(<?php echo date('d'); ?>, <?php echo $month; ?>, <?php echo $year; ?>);"><?php echo image_tag('calendar/timespan.png'); ?></a></td>
				<td class="text"><a href="javascript:void(0);" onclick="getOverview(<?php echo date('d'); ?>, <?php echo $month; ?>, <?php echo $year; ?>);"><?php echo __('Show today'); ?></a></td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td><a href="javascript:void(0);" class="image" onclick="window.open('<?php echo TBGContext::getTBGPath(); ?>modules/calendar/show_event.php','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');"><?php echo image_tag('calendar/newtodo.png'); ?></a></td>
				<td class="text"><a href="javascript:void(0);" onclick="window.open('<?php echo TBGContext::getTBGPath(); ?>modules/calendar/show_event.php','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');"><?php echo __('New calendar entry'); ?></a></td>
			</tr>
		</table>
	</div>
	<div id="calendar_full_container">
	<?php 
	
	switch (TBGContext::getModule('calendar')->getSetting('calendarstartup', TBGContext::getUser()->getUID()))
	{
		case 'week':
			echo TBGContext::getModule('calendar')->html_week(date('d'), $month, $year);
			break;
		case 'day':
			echo TBGContext::getModule('calendar')->html_day(date('d'), $month, $year);
			break;
		case 'month':
		default:
			echo TBGContext::getModule('calendar')->html_calendar($year, $month, 'full');
			break;
	}
	
	?>
	</div>
	</div>
	</td>
</tr>
</table>

<?php

	require_once TBGContext::getIncludePath() . "include/footer.inc.php";

?>