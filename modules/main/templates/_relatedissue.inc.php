<li class="hover_highlight<?php if ($issue->isClosed()): ?> closed<?php endif; ?>" id="related_issue_<?php echo $issue->getID(); ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
	<?php if ($related_issue->canAddRelatedIssues()): ?>
		<button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove relation to issue %itemname?', array('%itemname' => $issue->getFormattedIssueNo(true))); ?>', '<?php echo __('Please confirm that you want to remove this item from the list of issues related to this issue'); ?>', {yes: {click: function() {TBG.Issues.removeRelated('<?php echo make_url('viewissue_remove_related_issue', array('project_key' => $related_issue->getProject()->getKey(), 'issue_id' => $related_issue->getID(), 'related_issue_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>);TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Remove'); ?></button>
	<?php endif; ?>
	<span style="float: left;" class="issue_state <?php echo $issue->isClosed() ? 'closed' : 'open'; ?>"><?php echo $issue->isClosed() ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open')); ?></span>
	<div class="status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($issue->getStatus() instanceof TBGDatatype) ? __($issue->getStatus()->getName()) : __('Status not determined'); ?>"><?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getName() : __('Unknown'); ?></div>
	<a href="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>"><?php echo __('%issuetype %issue_no', array('%issuetype' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype')), '%issue_no' => $issue->getFormattedIssueNo(true))); ?>
	<br style="clear: both;">
	<span title="<?php echo tbg_decodeUTF8($issue->getTitle()); ?>"><?php echo tbg_decodeUTF8($issue->getTitle()); ?></span></a>
	<?php if ($issue->isAssigned()): ?>
		<div class="faded_out">
			<?php if ($issue->getAssignee() instanceof TBGUser): ?>
				(<?php echo __('Assigned to %assignee', array('%assignee' => get_component_html('main/userdropdown', array('user' => $issue->getAssignee(), 'show_avatar' => true)))); ?>)
			<?php else: ?>
				(<?php echo __('Assigned to %assignee', array('%assignee' => get_component_html('main/teamdropdown', array('team' => $issue->getAssignee())))); ?>)
			<?php endif; ?>
		</div>
	<?php endif; ?>
</li>
