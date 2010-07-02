<?php

	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addJavascript('scrum.js');

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="scrum">
	<tr>
		<td style="width: 210px; padding: 0 5px 0 5px;">
			<div class="rounded_box mediumgrey borderless" style="margin-top: 5px;" id="scrum_menu">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<div class="header"><?php echo __('Actions'); ?></div>
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('scrum_planning.png'); ?></td>
							<td style="padding: 3px 0 0 2px; text-align: left; font-size: 12px; font-weight: normal;"><?php echo link_tag(make_url('project_scrum', array('project_key' => $selected_project->getKey())), __('Show scrum planning page')); ?></td>
						</tr>
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_burndown.png'); ?></td>
							<td style="padding: 3px 0 0 2px; text-align: left; font-size: 12px; font-weight: bold;"><?php echo link_tag(make_url('project_scrum_sprint_details', array('project_key' => $selected_project->getKey())), __('Show sprint details')); ?></td>
						</tr>
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_burndown.png'); ?></td>
							<td style="padding: 3px 0 0 2px; text-align: left; font-size: 12px; font-weight: normal;"><?php echo link_tag('#', __('Show release burndown'), array('class' => 'faded_medium')); ?></td>
						</tr>
					</table>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</td>
		<td style="width: auto; padding-right: 5px; position: relative;" id="scrum_sprint_burndown">
			<div class="header_div">
				<?php if ($selected_sprint instanceof TBGMilestone): ?>
					<?php echo __('Sprint details, "%sprint_name%"', array('%sprint_name%' => $selected_sprint->getName())); ?>
				<?php else: ?>
					<?php echo __('No sprint selected'); ?>
				<?php endif; ?>
			</div>
			<?php if ($selected_sprint instanceof TBGMilestone): ?>
				<?php echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey(), 'sprint_id' => $selected_sprint->getID())), array('style' => 'margin: 15px 0 15px 0;', 'id' => 'selected_burndown_image'), true); ?>
				<table style="width: 800px; position: relative; margin-bottom: 20px;" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="width: 20px; border-bottom: 1px solid #DDD;">&nbsp;</td>
						<td style="width: auto; border-bottom: 1px solid #DDD;">&nbsp;</td>
						<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Points'); ?></td>
						<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Hours'); ?></td>
						<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Actions'); ?></td>
					</tr>
				<?php foreach ($selected_sprint->getIssues() as $issue): ?>
					<?php if (!$issue->getIssueType()->isTask()): ?>
						<tr class="canhover_light">
							<td style="padding: 3px 0 3px 3px;"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?></td>
							<td style="padding: 3px 3px 3px 5px; font-weight: bold; font-size: 13px;"><?php echo $issue->getFormattedTitle(); ?></td>
							<td style="padding: 3px; text-align: center; font-size: 13px; font-weight: normal;"<?php if (!$issue->getEstimatedPoints()): ?> class="faded_medium"<?php endif; ?> id="scrum_story_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></td>
							<td style="padding: 3px; text-align: center; font-size: 13px; font-weight: normal;" class="faded_medium">-</td>
							<td style="padding: 3px;">
								<div style="position: relative; text-align: center;" class="scrum_sprint_details_actions">
									<?php if ($issue->canEditEstimatedTime()): ?>
										<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
										<?php include_template('quickestimate', array('issue' => $issue)); ?>
									<?php endif; ?>
									<?php if ($issue->canAddRelatedIssues()): ?>
										<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_add_task_div').toggle();"><?php echo image_tag('scrum_add_task.png', array('title' => __('Add a task to this user story'))); ?></a>
										<?php include_template('quickaddtask', array('issue' => $issue, 'mode' => 'sprint')); ?>
									<?php endif; ?>
								</div>
							</td>
						</tr>
						<?php $total_estimated_points += $issue->getEstimatedPoints(); ?>
						<?php $hastasks = false; ?>
						<tbody id="scrum_story_<?php echo $issue->getID(); ?>_tasks">
							<?php if (count($issue->getChildIssues())): ?>
								<?php foreach ($issue->getChildIssues() as $child_issue): ?>
									<?php if ($child_issue->getIssueType()->isTask()): ?>
										<?php $hastasks = true; ?>
										<?php include_template('project/scrumsprintdetailstask', array('task' => $child_issue, 'can_estimate' => $issue->canEditEstimatedTime())); ?>
										<?php $total_estimated_hours += $child_issue->getEstimatedHours(); ?>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
						<tr id="no_tasks_<?php echo $issue->getID(); ?>"<?php if ($hastasks): ?> style="display: none;"<?php endif; ?>><td>&nbsp;</td><td colspan="5" class="faded_medium" style="padding: 0 0 10px 3px; font-size: 13px;"><?php echo __("This story doesn't have any tasks"); ?></td></tr>
					<?php endif; ?>
				<?php endforeach; ?>
					<tr>
						<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;" colspan="2"><?php echo __('Total estimated effort'); ?></td>
						<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $selected_sprint->getID(); ?>_estimated_points"><?php echo $total_estimated_points; ?></td>
						<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $selected_sprint->getID(); ?>_estimated_hours"><?php echo $total_estimated_hours; ?></td>
						<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;" colspan="2">&nbsp;</td>
					</tr>
				</table>
			<?php else: ?>
				<img src="" id="selected_burndown_image" alt="">
			<?php endif; ?>
		</td>
	</tr>
</table>