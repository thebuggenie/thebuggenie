<?php foreach ($issues as $issue): ?>
	<?php list ($showtablestart, $showheader, $prevgroup_id, $groupby_description) = searchActions::resultGrouping($issue, $groupby, $cc, $prevgroup_id); ?>
	<?php if ($showtablestart && $cc > 1): ?>
		<?php echo '</tbody></table>'; ?>
	<?php endif; ?>
	<?php if ($showheader): ?>
		<h3 style="margin-top: 20px;"><?php echo $groupby_description; ?></h3>
	<?php endif; ?>
	<?php if ($showtablestart): ?>
		<table style="width: 100%;" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<?php if (!BUGScontext::isProjectContext()): ?>
						<th style="padding-left: 3px;"><?php echo __('Project'); ?></th>
					<?php endif; ?>
					<th style="width: 16px; text-align: right; padding: 0;<?php if (BUGScontext::isProjectContext()): ?> padding-left: 3px;<?php endif; ?>">&nbsp;</th>
					<th><?php echo __('Issue'); ?></th>
					<th><?php echo __('Assigned to'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th><?php echo __('Resolution'); ?></th>
					<th><?php echo __('Last updated'); ?></th>
				</tr>
			</thead>
			<tbody>
	<?php endif; ?>
			<tr class="<?php if ($issue->hasUnsavedChanges()): ?> changed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?>">
				<?php if (!BUGScontext::isProjectContext()): ?>
					<td style="padding-left: 5px;"><?php echo $issue->getProject()->getName(); ?></td>
				<?php endif; ?>
				<td>
					<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?>
				</td>
				<td class="result_issue"<?php if (BUGScontext::isProjectContext()): ?> style="padding-left: 3px;"<?php endif; ?>>
					<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), '<span class="issue_no">' . $issue->getFormattedIssueNo(true) . '</span> - <span class="issue_title">' . $issue->getTitle() . '</span>'); ?>
				</td>
				<td<?php if (!$issue->isAssigned()): ?> class="faded_medium"<?php endif; ?>>
					<?php if ($issue->isAssigned()): ?>
						<table style="display: inline;" cellpadding=0 cellspacing=0>
							<?php if ($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_USER): ?>
								<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
							<?php else: ?>
								<?php echo include_component('main/teamdropdown', array('user' => $issue->getAssignee())); ?>
							<?php endif; ?>
						</table>
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
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
	<?php if ($cc == count($issues)): ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php $cc++; ?>
<?php endforeach; ?>