<?php

	$tbg_response->addBreadcrumb(link_tag(make_url('project_planning', array('project_key' => $selected_project->getKey())), __('Project sprint planning')));
	if ($milestone instanceof TBGMilestone)
	{
		$tbg_response->addBreadcrumb(__('%sprint_name% overview', array('%sprint_name%' => $milestone->getName())));
	}
	else
	{
		$tbg_response->addBreadcrumb(__('No sprint selected'));
	}
	$tbg_response->setTitle(__('"%project_name%" sprint overview', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addJavascript(make_url('home').'js/jquery.flot.min.js', false);
	$tbg_response->addJavascript(make_url('home').'js/jquery.flot.resize.min.js', false);
	$tbg_response->addJavascript(make_url('home').'js/jquery.flot.dashes.js', false);

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="width: 788px; padding-right: 5px; position: relative;" id="milestone_details_overview">
				<?php if ($milestone instanceof TBGMilestone): ?>
					<h3>
						<span id="milestone_name"><?php echo $milestone->getName(); ?></span>&nbsp;
						<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID()))."');", 'class' => 'button button-icon button-silver')); ?>
						<br>
						<span class="date">
							<?php if ($milestone->getStartingDate() && $milestone->isScheduled()): ?>
								(<?php echo tbg_formatTime($milestone->getStartingDate(), 22, true, true); ?> - <?php echo tbg_formatTime($milestone->getScheduledDate(), 22, true, true); ?>)
							<?php elseif ($milestone->getStartingDate() && !$milestone->isScheduled()): ?>
								(<?php echo __('Starting %start_date%', array('%start_date%' => tbg_formatTime($milestone->getStartingDate(), 22, true, true))); ?>)
							<?php elseif (!$milestone->getStartingDate() && $milestone->isScheduled()): ?>
								(<?php echo __('Ends %end_date%', array('%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 22, true, true))); ?>)
							<?php endif; ?>
						</span>
					</h3>
				<?php else: ?>
					<?php echo __('No milestone selected'); ?>
				<?php endif; ?>
				<?php if ($milestone instanceof TBGMilestone): ?>
					<div id="selected_burndown_image" class="graph_view" style="margin: 5px; width: 800px; height: 350px;"></div>
					<?php //echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey(), 'sprint_id' => $milestone->getID())), array('style' => 'margin: 15px 0 15px 0;', 'id' => 'selected_burndown_image'), true); ?>
					<table style="width: 800px; position: relative; margin-bottom: 20px;" cellpadding="0" cellspacing="0" border="0">
						<thead>
							<tr>
								<td style="width: 640px; border-bottom: 1px solid #DDD;">&nbsp;</td>
								<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Points'); ?></td>
								<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Hours'); ?></td>
								<td style="width: 30px; border-bottom: 1px solid #DDD;">&nbsp;</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($milestone->getIssues() as $issue): ?>
								<?php if ($issue->isChildIssue()) continue; ?>
								<?php include_template('project/milestonedetailsissue', array('issue' => $issue, 'milestone' => $milestone)); ?>
							<?php endforeach; ?>
							<tr>
								<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;"><?php echo __('Total estimated effort'); ?></td>
								<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_estimated_points"><?php echo $milestone->getPointsEstimated(); ?></td>
								<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_estimated_hours"><?php echo $milestone->getHoursEstimated(); ?></td>
								<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;">&nbsp;</td>
							</tr>
							<tr>
								<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;"><?php echo __('Current effort'); ?></td>
								<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_spent_points"><?php echo $milestone->getPointsSpent(); ?></td>
								<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_spent_hours"><?php echo $milestone->getHoursSpent(); ?></td>
								<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;">&nbsp;</td>
							</tr>
							<tr>
								<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;"><?php echo __('Total remaining effort'); ?></td>
								<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_remaining_points"><?php echo $milestone->getPointsEstimated() - $milestone->getPointsSpent(); ?></td>
								<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_remaining_hours"><?php echo $milestone->getHoursEstimated() - $milestone->getHoursSpent(); ?></td>
								<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;">&nbsp;</td>
							</tr>
						</tbody>
					</table>
					<script type="text/javascript">
							jQuery(function () {

								var d_e_points = [];
								var d_e_hours = [];
								var d_s_points = [];
								var d_s_hours = [];
								var d_b_points = [];
								var d_b_hours = [];

								<?php if ($milestone->hasStartingDate() && $milestone->hasScheduledDate()): ?>
									TBG.Chart.burndownChart(<?php echo json_encode($burndown_data); ?>, '<?php echo time() * 1000; ?>');
								<?php else: ?>
									<?php foreach (range(0, 8) as $cc): ?>
										<?php $eh_val = ($cc != 3) ? 0 : array_sum($burndown_data['estimations']['hours']); ?>
										d_e_hours.push([<?php echo $cc; ?>,<?php echo $eh_val; ?>]);
										<?php $sh_val = ($cc != 3) ? 0 : array_sum($burndown_data['spent_times']['hours']); ?>
										d_s_hours.push([<?php echo $cc; ?>,<?php echo $sh_val; ?>]);
										<?php $ep_val = ($cc != 5) ? 0 : array_sum($burndown_data['estimations']['points']); ?>
										d_e_points.push([<?php echo $cc; ?>,<?php echo $ep_val; ?>]);
										<?php $sp_val = ($cc != 5) ? 0 : array_sum($burndown_data['spent_times']['points']); ?>
										d_s_points.push([<?php echo $cc; ?>,<?php echo $sp_val; ?>]);
									<?php endforeach; ?>
									var x_config = TBG.Chart.config.x_config;
									x_config.ticks = [[0, ' '], [1, ' '], [2, ' '], [3, '<?php echo __('Hours'); ?>'], [4, ' '], [5, '<?php echo __('Points'); ?>'], [6, ' '], [7, ' '], [8, ' ']]
									jQuery.plot(jQuery("#selected_burndown_image"), [
										{
											data: d_e_hours,
											color: '#92BA6F',
											label: '<?php echo __('Estimated hours'); ?>'
										},
										{
											data: d_e_points,
											color: '#F8C939',
											label: '<?php echo __('Estimated points'); ?>'
										},
										{
											data: d_s_hours,
											color: '#923A6F',
											label: '<?php echo __('Spent hours'); ?>'
										},
										{
											data: d_s_points,
											color: '#F83A39',
											label: '<?php echo __('Spent points'); ?>'
										}
									], {
										series: {
											stack: true,
											lines: { show: false },
											bars: { show: true, barWidth: 0.6 }
										},
										yaxis: TBG.Chart.config.y_config,
										xaxis: x_config,
										grid: TBG.Chart.config.grid_config
									});
								<?php endif; ?>
							});
					</script>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>