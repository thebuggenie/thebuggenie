<?php $child = (isset($child)) ? $child : false; ?>
<tr class="hover_highlight">
	<td style="font-weight: <?php echo ($child) ? 'normal' : 'bold'; ?>;" class="issue_title_container">
		<?php if ($child) echo image_tag('icon_tree_child.png', array('style' => 'float: left; margin: 0 5px 0 0;')); ?>
		<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('style' => 'float: left; margin: 2px 5px -2px 0;', 'title' => $issue->getIssueType()->getName())); ?>
		<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(false), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle(), array('title' => $issue->getFormattedTitle(), 'style' => 'width: ' . (550 - 30*($child)).'px')); ?>
	</td>
	<td class="estimates <?php if (!$issue->getEstimatedPoints()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></td>
	<td class="estimates <?php if (!$issue->getEstimatedHours()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getEstimatedHours(); ?></td>
	<td class="milestone_issue_actions">
		<div class="button-group" style="float: right;">
			<?php if ($issue->canAddRelatedIssues()): ?>
				<?php echo javascript_link_tag(image_tag('icon_new_related_issue.png', array('title' => __('Create a new related issue'))), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'parent_issue_id' => $issue->getID()))."');", 'class' => 'button button-silver button-icon')); ?>
			<?php endif; ?>
			<?php if ($issue->canEditEstimatedTime()): ?>
				<a href="javascript:void(0);" class="button button-silver button-icon" onclick="$('estimated_time_<?php echo $issue->getID(); ?>_change').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('icon_estimated_time.png'); ?></a>
			<?php endif; ?>
		</div>
		<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true)); ?>
	</td>
</tr>
<?php if (count($issue->getChildIssues())): ?>
	<?php foreach ($issue->getChildIssues() as $child_issue): ?>
		<?php include_template('project/milestonedetailsissue', array('issue' => $child_issue, 'milestone' => $milestone, 'child' => true)); ?>
	<?php endforeach; ?>
<?php endif; ?>