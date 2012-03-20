<?php $child = (isset($child)) ? $child : false; ?>
<tr class="hover_highlight">
	<td style="font-weight: normal;" class="issue_title_container">
		<?php if ($child) echo image_tag('icon_tree_child.png', array('style' => 'float: left; margin: 0 5px 0 0;')); ?>
		<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('style' => 'float: left; margin: 2px 5px -2px 0;', 'title' => $issue->getIssueType()->getName())); ?>
		<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(false), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle(), array('title' => $issue->getFormattedTitle(), 'style' => 'width: ' . (550 - 30*($child)).'px')); ?>
	</td>
	<td class="estimates">
		<span class="<?php if (!$issue->getSpentPoints()): ?> faded_out<?php endif; ?>" id="spent_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getSpentPoints(); ?></span> /
		<span class="<?php if (!$issue->getEstimatedPoints()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></span>
	</td>
	<td class="estimates">
		<span class="<?php if (!$issue->getSpentHours()): ?> faded_out<?php endif; ?>" id="spent_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getSpentHours(); ?></span> /
		<span class="<?php if (!$issue->getEstimatedHours()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getEstimatedHours(); ?></span>
	</td>
	<td class="milestone_issue_actions">
		<a class="button button-icon button-silver" id="more_actions_<?php echo $issue->getID(); ?>_button" href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('more_actions_<?php echo $issue->getID(); ?>').toggle();"><?php echo image_tag('action_dropdown_small.png', array('title' => __('Show more actions'))); ?></a>
		<div class="rounded_box popup_box shadowed white more_actions_dropdown" style="width: auto; margin-top: 0; display: none;" onclick="$('more_actions_<?php echo $issue->getID(); ?>_button').toggleClassName('button-pressed');$(this).toggle();" id="more_actions_<?php echo $issue->getID(); ?>">
			<?php if ($issue->canAddRelatedIssues()): ?>
				<?php echo javascript_link_tag(__('Create a new related issue'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'parent_issue_id' => $issue->getID()))."');")); ?>
			<?php endif; ?>
			<?php if ($issue->canEditEstimatedTime()): ?>
				<a href="javascript:void(0);" onclick="$('estimated_time_<?php echo $issue->getID(); ?>_change').toggle();" title="<?php echo __('Change estimate'); ?>"><?php echo __('Change estimate'); ?></a>
			<?php endif; ?>
			<?php if ($issue->canEditSpentTime()): ?>
				<a href="javascript:void(0);" onclick="$('spent_time_<?php echo $issue->getID(); ?>_change').toggle();" title="<?php echo __('Change time spent'); ?>"><?php echo __('Change time spent'); ?></a>
			<?php endif; ?>
		</div>
		<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true)); ?>
		<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'spent_time', 'instant_save' => true)); ?>
	</td>
</tr>
<?php if (count($issue->getChildIssues())): ?>
	<?php foreach ($issue->getChildIssues() as $child_issue): ?>
		<?php include_template('project/milestonedetailsissue', array('issue' => $child_issue, 'milestone' => $milestone, 'child' => true)); ?>
	<?php endforeach; ?>
<?php endif; ?>