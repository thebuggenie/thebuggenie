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

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="width: auto; padding-right: 5px; position: relative;" id="scrum_sprint_burndown">
				<?php if (!$milestone instanceof TBGMilestone): ?>
					<div class="header_div">
						<?php //echo __('Sprint details, "%sprint_name%"', array('%sprint_name%' => $milestone->getName())); ?>
					<?php //else: ?>
						<?php echo __('No sprint selected'); ?>
					</div>
				<?php endif; ?>
				<?php if ($milestone instanceof TBGMilestone): ?>
					<?php echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey(), 'sprint_id' => $milestone->getID())), array('style' => 'margin: 15px 0 15px 0;', 'id' => 'selected_burndown_image'), true); ?>
					<table style="width: 800px; position: relative; margin-bottom: 20px;" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="width: 20px; border-bottom: 1px solid #DDD;">&nbsp;</td>
							<td style="width: auto; border-bottom: 1px solid #DDD;">&nbsp;</td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Points'); ?></td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Hours'); ?></td>
							<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Actions'); ?></td>
						</tr>
					<?php foreach ($milestone->getIssues() as $issue): ?>
						<?php if (!$issue->getIssueType()->isTask()): ?>
							<tr class="hover_highlight">
								<td style="padding: 3px 0 3px 3px;"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?></td>
								<td style="padding: 3px 3px 3px 5px; font-weight: bold; font-size: 13px;"><?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(false), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle()); ?></td>
								<td class="estimates" <?php if (!$issue->getEstimatedPoints()): ?> class="faded_out"<?php endif; ?> id="scrum_story_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></td>
								<td class="estimates faded_out">-</td>
								<td style="padding: 3px;">
									<div style="position: relative; text-align: center;" class="scrum_sprint_details_actions">
										<?php if ($issue->canEditEstimatedTime()): ?>
											<a href="javascript:void(0);" class="img" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
											<?php include_template('project/quickestimate', array('issue' => $issue)); ?>
										<?php endif; ?>
										<?php if ($issue->canAddRelatedIssues()): ?>
											<a href="javascript:void(0);" class="img" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_add_task_div').toggle();"><?php echo image_tag('scrum_add_task.png', array('title' => __('Add a task to this issue'))); ?></a>
											<?php include_template('project/quickaddtask', array('issue' => $issue, 'mode' => 'sprint')); ?>
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
										<?php if ($child_issue->getIssueType()->isTask()): ?>
											<?php $hastasks = true; ?>
											<?php include_template('project/scrumsprintdetailstask', array('task' => $child_issue, 'can_estimate' => $issue->canEditEstimatedTime())); ?>
											<?php $total_estimated_hours += $child_issue->getEstimatedHours(); ?>
											<?php $total_spent_hours += $child_issue->getSpentHours(); ?>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
							<tr id="no_tasks_<?php echo $issue->getID(); ?>"<?php if ($hastasks): ?> style="display: none;"<?php endif; ?>><td>&nbsp;</td><td colspan="5" class="faded_out" style="padding: 0 0 10px 3px; font-size: 0.9em;"><?php echo __("This issue doesn't have any tasks"); ?></td></tr>
						<?php endif; ?>
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
				<?php else: ?>
					<img src="" id="selected_burndown_image" alt="">
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>