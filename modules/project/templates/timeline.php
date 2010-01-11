<?php

	$tbg__response->setTitle(__('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));
	$tbg__response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
<div class="timeline_actions features">
	<div class="feature" style="margin: 10px; padding-top: 0;">
		<div class="header_div"><?php echo __('Timeline actions'); ?></div>
		<div class="content">
			<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png', array('style' => 'float: left; margin-right: 5px;')) . __('Subscribe to updates via RSS')); ?>
		</div>
	</div>
</div>
<div id="timeline">
	<?php if (count($recent_activities) > 0): ?>
		<table cellpadding=0 cellspacing=0 class="recent_activities">
			<?php $prev_date = null; ?>
			<?php foreach ($recent_activities as $timestamp => $activities): ?>
				<?php $date = tbg__formatTime($timestamp, 5); ?>
					<?php if ($date != $prev_date): ?>
					<tr>
						<td class="latest_action_dates" colspan="2"><?php echo tbg__formatTime($timestamp, 5); ?></td>
					</tr>
				<?php endif; ?>
				<?php foreach ($activities as $activity): ?>
					<?php if ($activity['change_type'] == 'build_release'): ?>
						<tr>
							<td class="imgtd"><?php echo image_tag('icon_build.png'); ?></td>
							<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg__formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('New version released'); ?></i></td>
						</tr>
					<?php else: ?>
						<?php include_template('main/logitem', array('action' => $activity, 'include_time' => true, 'extra_padding' => true)); ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php $prev_date = $date; ?>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<div class="faded_dark" style="font-size: 13px; padding-top: 3px;"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
	<?php endif; ?>
</div>