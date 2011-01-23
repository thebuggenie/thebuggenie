<?php foreach ($milestone->getIssues() as $issue): ?>
	<div class="roadmap_issue<?php if ($issue->isClosed()): ?> faded_out issue_closed<?php elseif ($issue->isBlocking()): ?> blocking<?php endif; ?>">
		<div class="issue_status"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : ''; ?>">&nbsp;</div></div>
		<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true, true) . ' - <span class="issue_title">' . $issue->getTitle() . '</span>'); ?>
		<?php if ($milestone->isSprint()): ?>
			<div class="issue_points"><?php echo __('%pts% points', array('%pts%' => $issue->getEstimatedPoints())); ?></div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>