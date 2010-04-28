<?php if ($child_issue->getIssueType()->getItemdata() == 'task'): ?>
	<div class="user_story_task">
<?php endif; ?>
<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($child_issue->getStatus() instanceof TBGStatus) ? $child_issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo ($child_issue->getStatus() instanceof TBGStatus) ? $child_issue->getStatus()->getName() : ''; ?>">&nbsp;</div></td>
		<td style="padding: 1px; width: auto;" valign="middle"><?php echo link_tag(make_url('viewissue', array('issue_no' => $child_issue->getIssueNo(), 'project_key' => $child_issue->getProject()->getKey())), $child_issue->getFormattedIssueNo() . ' - ' . $child_issue->getTitle()); ?></td>
		<td style="padding: 1px; width: 20px;" valign="middle">
			<?php if ($child_issue->getState() == TBGIssue::STATE_CLOSED): ?>
				<?php echo image_tag('action_ok_small.png', array('title' => ($child_issue->getIssuetype()->isTask()) ? __('This relation is solved because the task has been closed') : __('This relation is solved because the issue has been closed'))); ?>
			<?php else: ?>
				<?php echo image_tag('action_cancel_small.png', array('title' => ($child_issue->getIssuetype()->isTask()) ? __('This task must be closed before the issue relation is solved') : __('This issue must be closed before the issue relation is solved'))); ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php if ($child_issue->getIssueType()->getItemdata() == 'task'): ?>
	</div>
<?php endif; ?>
