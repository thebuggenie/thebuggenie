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
						<?php echo $milestone->getName(); ?><br>
						<span class="date">
							<?php if ($milestone->getStartingDate() && $milestone->isScheduled()): ?>
								(<?php echo tbg_formatTime($milestone->getStartingDate(), 22); ?> - <?php echo tbg_formatTime($milestone->getScheduledDate(), 22); ?>)
							<?php elseif ($milestone->getStartingDate() && !$milestone->isScheduled()): ?>
								(<?php echo __('Starting %start_date%', array('%start_date%' => tbg_formatTime($milestone->getStartingDate(), 22))); ?>)
							<?php elseif (!$milestone->getStartingDate() && $milestone->isScheduled()): ?>
								(<?php echo __('Ends %end_date%', array('%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 22))); ?>)
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
						<tr>
							<td style="width: auto; border-bottom: 1px solid #DDD;">&nbsp;</td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Points'); ?></td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Hours'); ?></td>
							<td style="width: 100px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: right; padding: 5px;"><?php echo __('Actions'); ?></td>
						</tr>
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
					</table>
					<script type="text/javascript">
							jQuery(function () {

								var d_e_points = [];
								var d_e_hours = [];
								var d_s_points = [];
								var d_s_hours = [];
								
								<?php if ($milestone->hasStartingDate() && $milestone->hasScheduledDate()): ?>
									<?php foreach ($burndown_data['estimations']['points'] as $d => $p): ?>
										d_e_points.push([<?php echo $d * 1000; ?>, <?php echo $p; ?>]);
									<?php endforeach; ?>

									<?php foreach ($burndown_data['estimations']['hours'] as $d => $h): ?>
										d_e_hours.push([<?php echo $d * 1000; ?>, <?php echo $h; ?>]);
									<?php endforeach; ?>

									<?php foreach ($burndown_data['spent_times']['points'] as $d => $p): ?>
										d_s_points.push([<?php echo $d * 1000; ?>, <?php echo $p; ?>]);
									<?php endforeach; ?>

									<?php foreach ($burndown_data['spent_times']['hours'] as $d => $h): ?>
										d_s_hours.push([<?php echo $d * 1000; ?>, <?php echo $h; ?>]);
									<?php endforeach; ?>
									var d_e_velocity_hours = [[<?php echo min(array_keys($burndown_data['estimations']['hours'])) * 1000; ?>, <?php echo max($burndown_data['estimations']['hours']); ?>], [<?php echo max(array_keys($burndown_data['estimations']['hours'])) * 1000; ?>, 0]];
									var d_e_velocity_points = [[<?php echo min(array_keys($burndown_data['estimations']['points'])) * 1000; ?>, <?php echo max($burndown_data['estimations']['points']); ?>], [<?php echo max(array_keys($burndown_data['estimations']['points'])) * 1000; ?>, 0]];
								<?php else: ?>
									d_e_hours.push([0,0]);
									d_e_hours.push([1,0]);
									d_e_hours.push([2,0]);
									d_e_hours.push([3, <?php echo array_sum($burndown_data['estimations']['hours']); ?>]);
									d_e_hours.push([4,0]);
									d_e_hours.push([5,0]);
									d_e_hours.push([6,0]);
									d_e_hours.push([7,0]);
									d_e_hours.push([8,0]);
									d_s_hours.push([0,0]);
									d_s_hours.push([1,0]);
									d_s_hours.push([2,0]);
									d_s_hours.push([3, <?php echo array_sum($burndown_data['spent_times']['hours']); ?>]);
									d_s_hours.push([4,0]);
									d_s_hours.push([5,0]);
									d_s_hours.push([6,0]);
									d_s_hours.push([7,0]);
									d_s_hours.push([8,0]);
									d_e_points.push([0,0]);
									d_e_points.push([1,0]);
									d_e_points.push([2,0]);
									d_e_points.push([3,0]);
									d_e_points.push([4,0]);
									d_e_points.push([5, <?php echo array_sum($burndown_data['estimations']['points']); ?>]);
									d_e_points.push([6,0]);
									d_e_points.push([7,0]);
									d_e_points.push([8,0]);
									d_s_points.push([0,0]);
									d_s_points.push([1,0]);
									d_s_points.push([2,0]);
									d_s_points.push([3,0]);
									d_s_points.push([4,0]);
									d_s_points.push([5, <?php echo array_sum($burndown_data['spent_times']['points']); ?>]);
									d_s_points.push([6,0]);
									d_s_points.push([7,0]);
									d_s_points.push([8,0]);
								<?php endif; ?>

								var y_config = { color: '#AAA', min: 0, tickDecimals: 0 };
								var x_config = { color: '#AAA', tickDecimals: 0 };
								var grid_config = {
									color: '#CCC',
									borderWidth: 1,
									backgroundColor: { colors: ["#FFF", "#EEE"] },
									hoverable: true,
									autoHighlight: true
								};

								<?php if ($milestone->hasStartingDate() && $milestone->hasScheduledDate()): ?>
									x_config.mode = 'time';
									grid_config.markings = [{xaxis: { from: <?php echo time() * 1000; ?>, to: <?php echo time() * 1000; ?>}, color: '#955', lineWidth: 1}];
									jQuery.plot(jQuery("#selected_burndown_image"), [
										{
											data: d_e_velocity_hours,
											dashes: { show: true, lineWidth: 1 },
											points: { show: false },
											color: '#66F',
											label: '<?php echo __('Estimated velocity (hours)'); ?>'
										},
										{
											data: d_e_velocity_points,
											dashes: { show: true, lineWidth: 1 },
											points: { show: false },
											color: '#333',
											label: '<?php echo __('Estimated velocity (points)'); ?>'
										},
										{
											data: d_e_hours,
											lines: { show: true },
											points: { show: true },
											color: '#92BA6F',
											label: '<?php echo __('Estimated hours'); ?>'
										},
										{
											data: d_e_points,
											lines: { show: true },
											points: { show: true },
											color: '#F8C939',
											label: '<?php echo __('Estimated points'); ?>'
										},
										{
											data: d_s_hours,
											lines: { show: true },
											points: { show: true },
											color: '#923A6F',
											label: '<?php echo __('Spent hours'); ?>'
										},
										{
											data: d_s_points,
											lines: { show: true },
											points: { show: true },
											color: '#F83A39',
											label: '<?php echo __('Spent points'); ?>'
										}
									], {
									yaxis: y_config,
									xaxis: x_config,
									grid: grid_config
									});
								<?php else: ?>
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
										yaxis: y_config,
										xaxis: x_config,
										grid: grid_config
									});
								<?php endif; ?>
							});
					</script>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>