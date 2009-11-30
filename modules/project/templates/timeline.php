<?php

	$bugs_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));

?>
<div id="timeline">
	<?php if (count($recent_activities) > 0): ?>
		<table cellpadding=0 cellspacing=0 class="recent_activities">
			<?php $prev_date = null; ?>
			<?php foreach ($recent_activities as $timestamp => $activities): ?>
				<?php $date = bugs_formatTime($timestamp, 5); ?>
					<?php if ($date != $prev_date): ?>
					<tr>
						<td class="latest_action_dates" colspan="2"><?php echo bugs_formatTime($timestamp, 5); ?></td>
					</tr>
				<?php endif; ?>
				<?php foreach ($activities as $activity): ?>
					<?php if ($activity['change_type'] == 'build_release'): ?>
						<tr>
							<td class="imgtd"><?php echo image_tag('icon_build.png'); ?></td>
							<td style="padding-bottom: 10px;"><span class="time"><?php echo bugs_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('New version released'); ?></i></td>
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