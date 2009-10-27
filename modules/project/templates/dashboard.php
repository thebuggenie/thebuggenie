<?php

	$bugs_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="project_dashboard">
	<tr>
		<td style="width: 400px; padding: 0 5px 0 5px;">
			<div class="header_div"><?php echo __('Recent activity'); ?></div>
			<?php if (count($recent_activities) > 0): ?>
				<table cellpadding=0 cellspacing=0 id="recent_activities">
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
			<?php endif; ?>
		</td>
		<td style="width: auto; padding-right: 5px;">
			<div class="header_div"><?php echo __('Something'); ?></div>
		</td>
		<td style="width: auto; padding-right: 5px;">
			<div class="header_div"><?php echo __('Something else'); ?></div>
		</td>
	</tr>
</table>
