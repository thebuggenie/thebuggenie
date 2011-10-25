<?php if ($resultcount > 0): ?>
	<table cellpadding=0 cellspacing=0 style="margin: 0 5px 5px 5px;">
	<?php foreach ($issues as $issue): ?>
		<tr class="<?php if ($issue->isClosed()): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($issue->isBlocking()): ?>issue_blocking<?php endif; ?>">
			<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('style' => 'padding: 10px 5px 0 0')); ?></td>
			<td>
				<span class="faded_out smaller"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey())), '['.$issue->getProject()->getKey().']'); ?></span>
				<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedTitle(true)); ?>
				<div class="faded_out"><?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => tbg_formatTime($issue->getLastUpdatedTime(), 12))); ?></div>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php else: ?>
	<div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __('No issues in this list'); ?></div>
<?php endif; ?>