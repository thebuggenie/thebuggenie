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

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="width: auto; padding-right: 5px; position: relative;" id="scrum_sprint_burndown">
				<?php if ($milestone instanceof TBGMilestone): ?>
					<h3>
						<?php echo __('Milestone details, "%milestone_name%"', array('%milestone_name%' => $milestone->getName())); ?>
					</h3>
				<?php else: ?>
					<?php echo __('No milestone selected'); ?>
				<?php endif; ?>
				<?php if ($milestone instanceof TBGMilestone): ?>
					<div id="selected_burndown_image" class="graph_view" style="margin: 5px; width: 800px; height: 250px;"></div>
					<?php //echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey(), 'sprint_id' => $milestone->getID())), array('style' => 'margin: 15px 0 15px 0;', 'id' => 'selected_burndown_image'), true); ?>
					<table style="width: 800px; position: relative; margin-bottom: 20px;" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="width: 20px; border-bottom: 1px solid #DDD;">&nbsp;</td>
							<td style="width: auto; border-bottom: 1px solid #DDD;">&nbsp;</td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Points'); ?></td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Hours'); ?></td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Actions'); ?></td>
						</tr>
					<?php foreach ($milestone->getIssues() as $issue): ?>
						<tr class="hover_highlight">
							<td style="padding: 3px 0 3px 3px;"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?></td>
							<td style="padding: 3px 3px 3px 5px; font-weight: bold; font-size: 13px;"><?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(false), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle()); ?></td>
							<td class="estimates <?php if (!$issue->getEstimatedPoints()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></td>
							<td class="estimates <?php if (!$issue->getEstimatedHours()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getEstimatedHours(); ?></td>
							<td style="padding: 3px;">
								<div style="position: relative; text-align: center;" class="scrum_sprint_details_actions">
									<?php if ($issue->canEditEstimatedTime()): ?>
										<a href="javascript:void(0);" class="img" onclick="$('estimated_time_<?php echo $issue->getID(); ?>_change').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('icon_estimated_time.png'); ?></a>
										<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true)); ?>
									<?php endif; ?>
									<?php if ($issue->canAddRelatedIssues()): ?>
										<?php echo javascript_link_tag(image_tag('scrum_add_task.png', array('title' => __('Add a task to this issue'))), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'parent_issue_id' => $issue->getID()))."');", 'class' => 'img')); ?>
									<?php endif; ?>
								</div>
							</td>
						</tr>
						<?php $total_estimated_points += $issue->getEstimatedPoints(); ?>
						<?php $total_spent_points += $issue->getSpentPoints(); ?>
						<?php $hastasks = false; ?>
						<tbody id="scrum_story_<?php echo $issue->getID(); ?>_tasks">
							<?php if (count($issue->getChildIssues())): ?>
								<?php foreach ($issue->getChildIssues() as $child_issue): ?>
									<?php $hastasks = true; ?>
									<?php include_template('project/scrumsprintdetailstask', array('task' => $child_issue, 'can_estimate' => $issue->canEditEstimatedTime())); ?>
									<?php $total_estimated_hours += $child_issue->getEstimatedHours(); ?>
									<?php $total_spent_hours += $child_issue->getSpentHours(); ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
						<tr id="no_tasks_<?php echo $issue->getID(); ?>"<?php if ($hastasks): ?> style="display: none;"<?php endif; ?>><td>&nbsp;</td><td colspan="5" class="faded_out" style="padding: 0 0 10px 3px; font-size: 0.9em;"><?php echo __("This issue doesn't have any related issues / tasks"); ?></td></tr>
					<?php endforeach; ?>
						<tr>
							<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;" colspan="2"><?php echo __('Total estimated effort'); ?></td>
							<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_estimated_points"><?php echo $total_estimated_points; ?></td>
							<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_estimated_hours"><?php echo $total_estimated_hours; ?></td>
							<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;" colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;" colspan="2"><?php echo __('Current effort'); ?></td>
							<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_spent_points"><?php echo $total_spent_points; ?></td>
							<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_spent_hours"><?php echo $total_spent_hours; ?></td>
							<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;" colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;" colspan="2"><?php echo __('Total remaining effort'); ?></td>
							<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_remaining_points"><?php echo $total_estimated_points - $total_spent_points; ?></td>
							<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_remaining_hours"><?php echo $total_estimated_hours - $total_spent_hours; ?></td>
							<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;" colspan="2">&nbsp;</td>
						</tr>
					</table>
					<script type="text/javascript">
							jQuery(function () {
								var d_e_points = [];
								<?php foreach ($burndown_data['estimations']['points'] as $d => $p): ?>
									d_e_points.push([<?php echo $d; ?>, <?php echo $p; ?>]);
								<?php endforeach; ?>
								var d_e_hours = [];
								<?php foreach ($burndown_data['estimations']['hours'] as $d => $h): ?>
									d_e_hours.push([<?php echo $d; ?>, <?php echo $h; ?>]);
								<?php endforeach; ?>
								var d_s_points = [];
								<?php foreach ($burndown_data['spent_times']['points'] as $d => $p): ?>
									d_s_points.push([<?php echo $d; ?>, <?php echo $p; ?>]);
								<?php endforeach; ?>
								var d_s_hours = [];
								<?php foreach ($burndown_data['spent_times']['hours'] as $d => $h): ?>
									d_s_hours.push([<?php echo $d; ?>, <?php echo $h; ?>]);
								<?php endforeach; ?>
								console.log(d_s_hours);
								jQuery.plot(jQuery("#selected_burndown_image"), [
									{
										data: d_e_hours,
										lines: { show: true },
										points: { show: true },
										color: '#92BA6F',
										label: '<?php echo __('Estimated hours'); ?>'
									},
									{
										data: d_e_points,
										lines: { show: true, fill: true },
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
										lines: { show: true, fill: true },
										points: { show: true },
										color: '#F83A39',
										label: '<?php echo __('Spent points'); ?>'
									}
								], {
								xaxis: {
									color: '#AAA'
								},
								yaxis: {
									color: '#AAA'
								},
								grid: {
									color: '#CCC',
									borderWidth: 1,
									backgroundColor: { colors: ["#FFF", "#EEE"] },
									hoverable: true,
									autoHighlight: true
								}
								});
							});
					</script>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>