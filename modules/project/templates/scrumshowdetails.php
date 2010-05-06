<?php

	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));

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
		<td style="width: auto; padding-right: 5px;" id="scrum_sprint_burndown">
			<div class="header_div">
				<?php if ($selected_sprint instanceof TBGMilestone): ?>
					<?php echo __('Sprint details, "%sprint_name%"', array('%sprint_name%' => $selected_sprint->getName())); ?>
				<?php else: ?>
					<?php echo __('No sprint selected'); ?>
				<?php endif; ?>
			</div>
			<?php if ($selected_sprint instanceof TBGMilestone): ?>
				<?php echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey(), 'sprint_id' => $selected_sprint->getID())), array('style' => 'margin: 15px 0 15px 0;', 'id' => 'selected_burndown_image'), true); ?>
				<table style="width: 800px;" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="width: 20px; border-bottom: 1px solid #DDD;">&nbsp;</td>
						<td style="width: auto; border-bottom: 1px solid #DDD;">&nbsp;</td>
						<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Points'); ?></td>
						<td style="width: 50px; border-bottom: 1px solid #DDD; font-size: 11px; font-weight: bold; text-align: center; padding: 5px;"><?php echo __('Hours'); ?></td>
					</tr>
				<?php foreach ($selected_sprint->getIssues() as $issue): ?>
					<tr class="canhover_light">
						<td style="padding: 3px 0 3px 3px;"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?></td>
						<td style="padding: 3px 3px 3px 5px; font-weight: bold; font-size: 13px;"><?php echo $issue->getFormattedTitle(); ?></td>
						<td style="padding: 3px; text-align: center; font-size: 13px; font-weight: normal;"<?php if (!$issue->getEstimatedPoints()): ?> class="faded_medium"<?php endif; ?>><?php echo $issue->getEstimatedPoints(); ?></td>
						<td style="padding: 3px; text-align: center; font-size: 13px; font-weight: normal;" class="faded_medium">-</td>
					</tr>
					<?php $total_estimated_points += $issue->getEstimatedPoints(); ?>
					<?php if (count($issue->getChildIssues())): ?>
						<?php foreach ($issue->getChildIssues() as $child_issue): ?>
							<tr class="canhover_light">
								<td>&nbsp;</td>
								<td style="padding: 3px 0 3px 10px; font-size: 12px; <?php if ($child_issue->isClosed()): ?> text-decoration: line-through; <?php endif; ?>"><?php echo link_tag(make_url('viewissue', array('issue_no' => $child_issue->getIssueNo(), 'project_key' => $child_issue->getProject()->getKey())), $child_issue->getTitle()); ?></td>
								<td style="text-align: center; padding: 3px; font-size: 13px; font-weight: normal;" class="faded_medium">-</td>
								<td style="text-align: center; padding: 3px; font-size: 13px; font-weight: normal;<?php if ($child_issue->isClosed()): ?> text-decoration: line-through; <?php endif; ?>"<?php if ($child_issue->isClosed()): ?> class="faded_medium"<?php endif; ?>><?php echo $child_issue->getEstimatedHours(); ?></td>
							</tr>
							<?php $total_estimated_hours += $child_issue->getEstimatedHours(); ?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr><td>&nbsp;</td><td colspan="3" class="faded_medium" style="padding: 0 0 10px 3px; font-size: 13px;"><?php echo __("This story doesn't have any tasks"); ?></td></tr>
					<?php endif; ?>
				<?php endforeach; ?>
					<tr>
						<td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;" colspan="2"><?php echo __('Total estimated effort'); ?></td>
						<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;"><?php echo $total_estimated_points; ?></td>
						<td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;"><?php echo $total_estimated_hours; ?></td>
					</tr>
				</table>
			<?php else: ?>
				<img src="" id="selected_burndown_image" alt="">
			<?php endif; ?>
		</td>
	</tr>
</table>