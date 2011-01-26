<?php $current_count = 0; ?>
<?php foreach ($issues as $issue): ?>
	<?php list ($showtablestart, $showheader, $prevgroup_id, $groupby_description) = searchActions::resultGrouping($issue, $groupby, $cc, $prevgroup_id); ?>
	<?php if (($showtablestart || $showheader) && $cc > 1): ?>
		<tr>
			<td colspan="<?php if (!TBGContext::isProjectContext()): ?>9<?php else: ?>8<?php endif; ?>" class="results_summary">
				<?php echo __('Total number of issues in this group: %number%', array('%number%' => "<b>{$current_count}</b>")); ?>
			</td>
		</tr>
		<?php $current_count = 0; ?>
		<?php echo '</tbody></table>'; ?>
	<?php endif; ?>
	<?php $current_count++; ?>
	<?php if ($showheader): ?>
		<h5><?php echo $groupby_description; ?></h5>
	<?php endif; ?>
	<?php if ($showtablestart): ?>
		<table style="width: 100%;" cellpadding="0" cellspacing="0" class="results_container">
			<thead>
				<tr>
					<?php if (!TBGContext::isProjectContext()): ?>
						<th style="padding-left: 3px;"><?php echo __('Project'); ?></th>
					<?php endif; ?>
					<th style="width: 16px; text-align: right; padding: 0;<?php if (TBGContext::isProjectContext()): ?> padding-left: 3px;<?php endif; ?>">&nbsp;</th>
					<th><?php echo __('Issue'); ?></th>
					<th><?php echo __('Assigned to'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th><?php echo __('Resolution'); ?></th>
					<th><?php echo __('Last updated'); ?></th>
					<th style="width: 20px; padding-bottom: 0; text-align: center;"><?php echo image_tag('icon_comments.png'); ?></th>
				</tr>
			</thead>
			<tbody>
	<?php endif; ?>
				<tr class="<?php if ($issue->isClosed()): ?> closed<?php endif; ?><?php if ($issue->hasUnsavedChanges()): ?> changed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?> priority_<?php echo ($issue->getPriority() instanceof TBGPriority) ? $issue->getPriority()->getValue() : 0; ?>">
				<?php if (!TBGContext::isProjectContext()): ?>
				<td style="padding-left: 5px;"><?php echo link_tag(make_url('project_issues', array('project_key' => $issue->getProject()->getKey())), $issue->getProject()->getName()); ?></td>
				<?php endif; ?>
				<td>
					<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?>
				</td>
				<td class="result_issue"<?php if (TBGContext::isProjectContext()): ?> style="padding-left: 3px;"<?php endif; ?>>
					<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), '<span class="issue_no">' . $issue->getFormattedIssueNo(true) . '</span> - <span class="issue_title">' . $issue->getTitle() . '</span>'); ?>
				</td>
				<td<?php if (!$issue->isAssigned()): ?> class="faded_out"<?php endif; ?>>
					<?php if ($issue->isAssigned()): ?>
						<?php if ($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER): ?>
							<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
						<?php else: ?>
							<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
						<?php endif; ?>
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
				<td<?php if (!$issue->getStatus() instanceof TBGDatatype): ?> class="faded_out"<?php endif; ?>>
					<?php if ($issue->getStatus() instanceof TBGDatatype): ?>
						<table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
							<tr>
								<td style="width: 12px; height: 12px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 11px; height: 11px; margin-right: 2px;">&nbsp;</div></td>
								<td style="padding-left: 0px; font-size: 1em;"><?php echo $issue->getStatus()->getName(); ?></td>
							</tr>
						</table>
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
				<td<?php if (!$issue->getResolution() instanceof TBGResolution): ?> class="faded_out"<?php endif; ?>>
					<?php echo ($issue->getResolution() instanceof TBGResolution) ? strtoupper($issue->getResolution()->getName()) : '-'; ?>
				</td>
				<td class="smaller" title="<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?>"><?php echo tbg_formatTime($issue->getLastUpdatedTime(), 20); ?></td>
				<td class="smaller" style="text-align: center;">
					<?php echo $issue->getCommentCount(); ?>
				</td>
			</tr>
	<?php if ($cc == count($issues)): ?>
			<tr>
				<td colspan="<?php if (!TBGContext::isProjectContext()): ?>9<?php else: ?>8<?php endif; ?>">
					<?php echo __('Total number of issues in this group: %number%', array('%number%' => "<b>{$current_count}</b>")); ?>
				</td>
			</tr>
			</tbody>
		</table>
	<?php endif; ?>
	<?php $cc++; ?>
<?php endforeach; ?>