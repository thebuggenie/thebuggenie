<thead>
	<tr>
		<th>&nbsp;</th>
		<th><?php echo __('Issue title'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th><?php echo __('Resolution'); ?></th>
		<th><?php echo __('Last updated'); ?></th>
	</tr>
</thead>
<tbody>
	<?php foreach ($issues as $issue): ?>
		<tr>
			<td class="result_issue_no" style="padding-left: 5px;"><?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true)); ?></td>
			<td class="result_issue_title"><?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getTitle()); ?></td>
			<td<?php if (!$issue->getStatus() instanceof BUGSdatatype): ?> class="faded_medium"<?php endif; ?>>
				<?php if ($issue->getStatus() instanceof BUGSdatatype): ?>
					<table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
						<tr>
							<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof BUGSdatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
							<td style="padding-left: 5px;"><?php echo $issue->getStatus()->getName(); ?></td>
						</tr>
					</table>
				<?php else: ?>
					-
				<?php endif; ?>
			</td>
			<td<?php if (!$issue->getResolution() instanceof BUGSresolution): ?> class="faded_medium"<?php endif; ?>>
				<?php echo ($issue->getResolution() instanceof BUGSresolution) ? strtoupper($issue->getResolution()->getName()) : '-'; ?>
			</td>
			<td class="smaller"><?php echo bugs_formatTime($issue->getLastUpdatedTime(), 12); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>