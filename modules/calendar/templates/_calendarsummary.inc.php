<div class="rounded_box iceblue_borderless" style="margin: 10px 0px 10px 10px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px;">
		<?php echo image_tag('summary_badge.png', array('style' => 'float: left; margin-right: 5px;'), false, 'calendar'); ?>
		<span style="font-size: 13px; font-weight: bold;"><?php echo __('Current events'); ?></span>
		<div style="clear: both;">
			<?php if (count($eventstoday) == 0): ?>
				<div style="padding-top: 5px;" class="faded_dark"><?php echo __('You have nothing scheduled today'); ?></div>
			<?php else: ?>
				<table cellpadding=0 cellspacing=0 style="width: 100%;">
				<?php foreach ($eventstoday as $anevent): ?>
					<tr>
						<td class="imgtd">
							<?php if ($anevent->getType() == BUGScalendarEvent::EVENT): ?>
								<?php echo image_tag('calendar/event.png'); ?>
							<?php elseif ($anevent->getType() == BUGScalendarEvent::TASK): ?>
								<?php echo image_tag('calendar/task.png'); ?>
							<?php elseif ($anevent->getType() == BUGScalendarEvent::MEETING): ?>
								<?php echo image_tag('calendar/meeting.png'); ?>
							<?php endif; ?>
						</td>
						<td>
							<a href="javascript:void(0);" onclick="window.open('<?php echo TBGContext::getTBGPath(); ?>modules/calendar/show_event.php?id=<?php echo $anevent->getID(); ?>','showevent','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');"><?php echo $anevent->getTitle(); ?></a><br>
							<div><?php echo tbg_formatTime($anevent->getStartDate(), 12); ?> - <?php echo tbg_formatTime($anevent->getEndDate(), 12); ?></div>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php endif; ?>
		</div>
		<div style="clear: both; margin-top: 15px;">
			<?php echo image_tag('tab_calendar.png', array('style' => 'float: left; margin-right: 5px;'), false, 'calendar'); ?>
			<?php echo javascript_link_tag(__('Open my calendar'), array('onclick' => "failedMessage('Not available', 'Calendar functionality is not implemented yet');")); ?>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>