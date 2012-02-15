<tr class="hover_highlight<?php if ($issue->isClosed()): ?> closed<?php endif; ?>" id="related_issue_<?php echo $issue->getID(); ?>">
	<td><?php echo image_tag((($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'icon_unknown') . '_tiny.png', array('title' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype')))); ?></td>
	<td style="padding: 1px; width: auto; font-size: 1em;" valign="middle">
		<?php if ($related_issue->canAddRelatedIssues()): ?>
			<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove relation to issue %itemname%?', array('%itemname%' => $issue->getFormattedIssueNo(true))); ?>', '<?php echo __('Please confirm that you want to remove this item from the list of issues related to this issue'); ?>', {yes: {click: function() {TBG.Issues.removeRelated('<?php echo make_url('viewissue_remove_related_issue', array('project_key' => $related_issue->getProject()->getKey(), 'issue_id' => $related_issue->getID(), 'related_issue_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>);TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_delete.png', array('class' => 'hover_visible', 'alt' => __('Remove relation'))); ?></a>
		<?php endif; ?>
		<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), (($issue->getIssueType()->isTask() ? $issue->getTitle() : $issue->getFormattedTitle()))); ?>
	</td>
	<td style="font-size: 1em; line-height: 1;"><div style="border: 1px solid rgba(0, 0, 0, 0.3); background-color: <?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 14px; height: 14px; float: left; margin-right: 5px;" title="<?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : __('Unknown'); ?>">&nbsp;</div><?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : __('Unknown'); ?></td>
	<td style="font-size: 1em;">
		<?php if ($issue->isAssigned()): ?>
			<?php if ($issue->getAssignee() instanceof TBGUser): ?>
				<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
			<?php else: ?>
				<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
			<?php endif; ?>
		<?php else: ?>
			<span class="faded_out"><?php echo __('Not assigned'); ?></span>
		<?php endif; ?>
	</td>
</tr>