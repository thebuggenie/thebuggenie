<?php

	define ('THEBUGGENIE_PATH', '../../');
	$page = 'calendar';

	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';
	
	require TBGContext::getIncludePath() . "include/ui_functions.inc.php";
	
	$noshow = true;
	$noshowtitle = '';
	require TBGContext::getIncludePath() . "include/header.inc.php";
	
	TBGContext::getModule('calendar')->activate();

	if (TBGContext::getRequest()->getParameter('id'))
	{
		try
		{
			$theEvent = new BUGScalendarEvent((int) TBGContext::getRequest()->getParameter('id'));
		}
		catch (Exception $e) {}
	}
	
	if (TBGContext::getRequest()->getParameter('title'))
	{
		if ($theEvent instanceof BUGScalendarEvent)
		{
			$theEvent->setDescription(TBGContext::getRequest()->getParameter('description'));
			$theEvent->setTitle(TBGContext::getRequest()->getParameter('title'));
			$theEvent->setType((int) TBGContext::getRequest()->getParameter('event_type'));
			$theEvent->setUserStatus((int) TBGContext::getRequest()->getParameter('status'));
			$theEvent->setStartDate(mktime((int) TBGContext::getRequest()->getParameter('start_at_hour'), (int) TBGContext::getRequest()->getParameter('start_at_minute'), 0, (int) TBGContext::getRequest()->getParameter('start_month'), (int) TBGContext::getRequest()->getParameter('start_day'), (int) TBGContext::getRequest()->getParameter('start_year')));
			$theEvent->setEndDate(mktime((int) TBGContext::getRequest()->getParameter('end_at_hour'), (int) TBGContext::getRequest()->getParameter('end_at_minute'), 0, (int) TBGContext::getRequest()->getParameter('end_month'), (int) TBGContext::getRequest()->getParameter('end_day'), (int) TBGContext::getRequest()->getParameter('end_year')));
			$issaved = true;
		}
		else
		{
			$start_date = mktime((int) TBGContext::getRequest()->getParameter('start_at_hour'), (int) TBGContext::getRequest()->getParameter('start_at_minute'), 0, (int) TBGContext::getRequest()->getParameter('start_month'), (int) TBGContext::getRequest()->getParameter('start_day'), (int) TBGContext::getRequest()->getParameter('start_year'));
			$end_date = mktime((int) TBGContext::getRequest()->getParameter('end_at_hour'), (int) TBGContext::getRequest()->getParameter('end_at_minute'), 0, (int) TBGContext::getRequest()->getParameter('end_month'), (int) TBGContext::getRequest()->getParameter('end_day'), (int) TBGContext::getRequest()->getParameter('end_year'));
			$theEvent = BUGScalendarEvent::createNew(TBGContext::getRequest()->getParameter('title'), (int) TBGContext::getRequest()->getParameter('event_type'), TBGContext::getRequest()->getParameter('description'), $start_date, $end_date, (int) TBGContext::getRequest()->getParameter('status'), (int) TBGContext::getModule('calendar')->getSetting('calendar', TBGContext::getUser()->getUID()));

			?>
			<div style="padding: 10px; text-align: center;"><b><?php echo __('The event has been saved in your calendar.'); ?></b>&nbsp;<?php echo __('What do you want to do next?'); ?><br>
			<br>
			<a href="show_event.php"><?php echo __('Create a new event'); ?></a><br>
			<br>
			<a href="javascript:void(0);" onclick="window.close();"><b><?php echo __('Close this window'); ?></b></a>
			</div>
			<?php
			
			require_once TBGContext::getIncludePath() . "include/footer.inc.php";

		}
	}
	else
	{
		$issaved = false;
	}
	
?>
<div class="event_window" style="height: 100%;">
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="show_event.php">
<?php

if ($theEvent instanceof BUGScalendarEvent)
{
	echo '<input type="hidden" name="id" value="' . $theEvent->getID() . '">';
}

?>
<div style="border-bottom: 1px solid #DDD; font-size: 14px; font-weight: bold; padding: 5px; text-align: left;">
<?php echo ($theEvent instanceof BUGScalendarEvent) ? __('Editing calendar entry') : __('Creating new calendar entry'); ?></div>
<?php

if ($issaved)
{
	?><div style="background-color: #F1F1F1; padding: 3px; font-weight: bold;"><?php echo __('The event has been saved in your calendar'); ?></div><?php
}

?>
<table style="width: 590px; border: 0px;" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 100px; padding: 2px; font-weight: bold;"><?php echo __('Title'); ?></td>
		<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" value="<?php echo ($theEvent instanceof BUGScalendarEvent) ? $theEvent->getTitle() : '' ?>" name="title"></td>
	</tr>
	<tr>
		<td style="width: 100px; padding: 2px; font-weight: bold; vertical-align: top;"><?php echo __('Description'); ?></td>
		<td style="width: auto; padding: 2px;"><?php echo tbg_newTextArea('description', '150px', '100%', ($theEvent instanceof BUGScalendarEvent) ? $theEvent->getDescription() : ''); ?></td>
	</tr>
	<tr>
		<td style="width: 100px; padding: 2px; font-weight: bold; vertical-align: top;"><?php echo __('Event type'); ?></td>
		<td style="width: auto; padding: 2px;">
		<select name="event_type">
			<option value="<?php echo BUGScalendarEvent::EVENT; ?>" <?php echo ($theEvent instanceof BUGScalendarEvent && $theEvent->getType() == BUGScalendarEvent::EVENT) ? 'selected' : '' ?>><?php echo __('Event'); ?></option>
			<option value="<?php echo BUGScalendarEvent::TASK; ?>" <?php echo ($theEvent instanceof BUGScalendarEvent && $theEvent->getType() == BUGScalendarEvent::TASK) ? 'selected' : '' ?>><?php echo __('Task / to-do'); ?></option>
			<option value="<?php echo BUGScalendarEvent::MEETING; ?>" <?php echo ($theEvent instanceof BUGScalendarEvent && $theEvent->getType() == BUGScalendarEvent::MEETING) ? 'selected' : '' ?>><?php echo __('Meeting'); ?></option>
		</select>
		</td>
	</tr>
	<?php

	if ($theEvent instanceof BUGScalendarEvent)
	{
		list ($start_day, $start_month, $start_year, $start_hour, $start_minute) = explode(',', date('d,m,Y,H,i', $theEvent->getStartDate()));
		list ($end_day, $end_month, $end_year, $end_hour, $end_minute) = explode(',', date('d,m,Y,H,i', $theEvent->getEndDate()));
	}
	else
	{
		list ($this_day, $this_month, $this_year, $this_hour, $this_minute) = explode(',', date('d,m,Y,H,i'));
	}

	for ($cc1 = 1; $cc1 <= 2; $cc1++)
	{
		?>
		<tr>
			<td style="width: 100px; padding: 2px; font-weight: bold; vertical-align: top;"><?php echo ($cc1 == 1) ? __('Starts') : __('Ends') ?></td>
			<td style="width: auto; padding: 2px;">
			<select name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_month">
			<?php
			
			for ($cc = 1; $cc <= 12; $cc++)
			{
				echo '<option value="' . $cc . '"';
				if ($theEvent instanceof BUGScalendarEvent)
				{
					if (($cc == $start_month && $cc1 == 1) || ($cc == $end_month && $cc1 == 2)) echo ' selected';
					echo ($cc1 == 1) ? '>' . tbg_formatTime($theEvent->getStartDate(), 15) . '</option>' : '>' . tbg_formatTime($theEvent->getEndDate(), 15) . '</option>'; 
				}
				else
				{
					if ($cc == $this_month) echo ' selected';
					echo '>' . tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15) . '</option>';
				}
			}
			
			?>
			</select>
			<select name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_day">
			<?php
			
			for ($cc = 1; $cc <= 31; $cc++)
			{
				echo '<option value="' . $cc . '"';
				if ($theEvent instanceof BUGScalendarEvent)
				{
					if (($cc == $start_day && $cc1 == 1) || ($cc == $end_day && $cc1 == 2)) echo ' selected';
				}
				else
				{
					if ($cc == $this_day) echo ' selected';
				}
				echo '>' . $cc . '</option>';
			}
			
			?>
			</select>
			<select name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_year">
			<?php
			
			for ($cc = date('Y') - 2; $cc <= date('Y') + 4; $cc++)
			{
				echo '<option value="' . $cc . '"';
				if ($theEvent instanceof BUGScalendarEvent)
				{
					if (($cc == $start_year && $cc1 == 1) || ($cc == $end_year && $cc1 == 2)) echo ' selected';
				}
				else
				{
					if ($cc == $this_year) echo ' selected';
				}
				echo '>' . $cc . '</option>';
			}
			
			?>
			</select>
			&nbsp;<?php echo __('at'); ?>&nbsp;
			<?php

			if ($theEvent instanceof BUGScalendarEvent)
			{
				?><input type="text" name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_at_hour" value="<?php echo ($cc1 == 1) ? $start_hour : $end_hour; ?>" style="width: 20px;" maxlength="2">:<input type="text" name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_at_minute" value="<?php echo ($cc1 == 1) ? $start_minute : $end_minute; ?>" style="width: 20px;" maxlength="2"><?php
			}
			else
			{
				?><input type="text" name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_at_hour" value="<?php echo ($cc1 == 1) ? date('H') : date('H', NOW + 1800); ?>" style="width: 20px;" maxlength="2">:<input type="text" name="<?php echo ($cc1 == 1) ? 'start' : 'end' ?>_at_minute" value="<?php echo ($cc1 == 1) ? date('i') : date('i', NOW + 1800); ?>" style="width: 20px;" maxlength="2"><?php
			}
			
			?>
			</td>
		</tr>
		<?php
	}

	?>
	<tr>
		<td style="width: 100px; padding: 2px; font-weight: bold; vertical-align: top;"><?php echo __('Show as'); ?></td>
		<td style="width: auto; padding: 2px;">
		<select name="status">
		<?php  
		
		foreach (TBGContext::getStates() as $aState)
		{
			$aState = TBGContext::factory()->TBGUserstate($aState);
			echo '<option value="' . $aState->getID() . '"';
			if ($theEvent instanceof BUGScalendarEvent && $theEvent->getUserStatus() == $aState->getID()) echo ' selected';
			echo '>' . $aState->getName() . '</option>';
		}
		
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td style="padding: 2px; padding-top: 20px;" colspan=2>
		<span style="float: left;"><?php echo __('When you are done, click "Save event" to save the event to your calendar'); ?></span>
		<input type="submit" value="<?php echo __('Save event'); ?>" style="width: 100px; float: right;"></td>
	</tr>
</table>
</form>
</div>
<?php

	require_once TBGContext::getIncludePath() . "include/footer.inc.php";

?>