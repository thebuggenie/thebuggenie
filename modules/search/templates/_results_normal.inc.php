<?php include_template('search/bulkactions', array('mode' => 'top')); ?>
<?php $current_count = 0; ?>
<?php foreach ($issues as $issue): ?>
	<?php list ($showtablestart, $showheader, $prevgroup_id, $groupby_description) = searchActions::resultGrouping($issue, $groupby, $cc, $prevgroup_id); ?>
	<?php if (($showtablestart || $showheader) && $cc > 1): ?>
		<?php echo '</tbody></table>'; ?>
		<div class="results_summary"><?php echo __('Total number of issues in this group: %number%', array('%number%' => "<b>{$current_count}</b>")); ?></div>
		<?php $current_count = 0; ?>
	<?php endif; ?>
	<?php $current_count++; ?>
	<?php if ($showheader): ?>
		<h5>
			<?php if ($groupby == 'issuetype'): ?>
				<?php echo image_tag($issue->getIssueType()->getIcon() . '_small.png', array('title' => $issue->getIssueType()->getName())); ?>
			<?php endif; ?>
			<?php echo $groupby_description; ?>
		</h5>
	<?php endif; ?>
	<?php if ($showtablestart): ?>
		<table style="width: 100%;" cellpadding="0" cellspacing="0" class="results_container resizable sortable">
			<thead>
				<tr>
					<th class="nosort" style="width: 20px; padding: 1px !important;"><input type="checkbox" onclick="TBG.Search.toggleCheckboxes(this);"></th>
					<?php if (!TBGContext::isProjectContext() && $show_project == true): ?>
						<th style="padding-left: 3px;"><?php echo __('Project'); ?></th>
					<?php endif; ?>
					<th class="sc_issuetype"<?php if (!in_array('issuetype', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Issue type'); ?></th>
					<th><span class="sc_title"<?php if (!in_array('title', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Issue'); ?></span></th>
					<th class="sc_assigned_to"<?php if (!in_array('assigned_to', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Assigned to'); ?></th>
					<th class="sc_status"<?php if (!in_array('status', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Status'); ?></th>
					<th class="sc_resolution"<?php if (!in_array('resolution', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Resolution'); ?></th>
					<th class="sc_category"<?php if (!in_array('category', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Category'); ?></th>
					<th class="sc_severity"<?php if (!in_array('severity', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Severity'); ?></th>
					<th class="sc_percent_complete" style="width: 150px;<?php if (!in_array('percent_complete', $visible_columns)): ?> display: none;<?php endif; ?>"><?php echo __('% completed'); ?></th>
					<th class="sc_reproducability"<?php if (!in_array('reproducability', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Reproducability'); ?></th>
					<th class="sc_priority"<?php if (!in_array('priority', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Priority'); ?></th>
					<th class="sc_milestone"<?php if (!in_array('milestone', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Milestone'); ?></th>
					<th class="sc_last_updated"<?php if (!in_array('last_updated', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Last updated'); ?></th>
					<th class="sc_comments" style="width: 20px; padding-bottom: 0; text-align: center;<?php if (!in_array('comments', $visible_columns)): ?> display: none;<?php endif; ?>"><?php echo image_tag('icon_comments.png', array('title' => __('Number of user comments on this issue'))); ?></th>
				</tr>
			</thead>
			<tbody>
	<?php endif; ?>
				<tr class="<?php if ($issue->isClosed()): ?> closed<?php endif; ?><?php if ($issue->hasUnsavedChanges()): ?> changed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?> priority_<?php echo ($issue->getPriority() instanceof TBGPriority) ? $issue->getPriority()->getValue() : 0; ?>" id="issue_<?php echo $issue->getID(); ?>">
					<td style="padding: 2px;"><input type="checkbox" name="update_issue[<?php echo $issue->getID(); ?>]" onclick="TBG.Search.toggleCheckbox(this);" value="<?php echo $issue->getID(); ?>"></td>
				<?php if (!TBGContext::isProjectContext() && $show_project == true): ?>
					<td style="padding-left: 5px;"><?php echo link_tag(make_url('project_issues', array('project_key' => $issue->getProject()->getKey())), $issue->getProject()->getName()); ?></td>
				<?php endif; ?>
					<td class="sc_issuetype"<?php if (!in_array('issuetype', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('title' => $issue->getIssueType()->getName())); ?>
						<?php echo $issue->getIssuetype()->getName(); ?>
					</td>
					<td class="result_issue"<?php if (TBGContext::isProjectContext()): ?> style="padding-left: 3px;"<?php endif; ?>>
						<?php if ($issue->countFiles()): ?>
							<?php echo image_tag('icon_attached_information.png', array('style' => 'float: left; margin-right: 3px;', 'title' => __('This issue has %num% attachments', array('%num%' => $issue->countFiles())))); ?>
						<?php endif; ?>
						<?php $title_visible = (in_array('title', $visible_columns)) ? '' : ' style="display: none;'; ?>
						<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), '<span class="issue_no">' . $issue->getFormattedIssueNo(true) . '</span><span class="issue_title sc_title"'.$title_visible.'> - ' . $issue->getTitle() . '</span>'); ?>
					</td>
					<td class="sc_assigned_to<?php if (!$issue->isAssigned()): ?> faded_out<?php endif; ?>"<?php if (!in_array('assigned_to', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
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
					<td class="sc_status<?php if (!$issue->getStatus() instanceof TBGDatatype): ?> faded_out<?php endif; ?>"<?php if (!in_array('status', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php if ($issue->getStatus() instanceof TBGDatatype): ?>
							<table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 12px; height: 12px;"><div class="sc_status_color" style="border: 1px solid rgba(0, 0, 0, 0.2); background-color: <?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 11px; height: 11px; margin-right: 2px;">&nbsp;</div></td>
									<td class="sc_status_name" style="padding-left: 0px; font-size: 1em;"><?php echo $issue->getStatus()->getName(); ?></td>
								</tr>
							</table>
						<?php else: ?>
							-
						<?php endif; ?>
					</td>
					<td class="sc_resolution<?php if (!$issue->getResolution() instanceof TBGResolution): ?> faded_out<?php endif; ?>"<?php if (!in_array('resolution', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getResolution() instanceof TBGResolution) ? mb_strtoupper($issue->getResolution()->getName()) : '-'; ?>
					</td>
					<td class="sc_category<?php if (!$issue->getCategory() instanceof TBGCategory): ?> faded_out<?php endif; ?>"<?php if (!in_array('category', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getCategory() instanceof TBGCategory) ? $issue->getCategory()->getName() : '-'; ?>
					</td>
					<td class="sc_severity<?php if (!$issue->getSeverity() instanceof TBGSeverity): ?> faded_out<?php endif; ?>"<?php if (!in_array('severity', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getSeverity() instanceof TBGSeverity) ? $issue->getSeverity()->getName() : '-'; ?>
					</td>
					<td class="smaller sc_percent_complete"<?php if (!in_array('percent_complete', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<span style="display: none;"><?php echo $issue->getPercentComplete(); ?></span><?php include_template('main/percentbar', array('percent' => $issue->getPercentComplete(), 'height' => 15)) ?>
					</td>
					<td class="sc_reproducability<?php if (!$issue->getReproducability() instanceof TBGReproducability): ?> faded_out<?php endif; ?>"<?php if (!in_array('reproducability', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getReproducability() instanceof TBGReproducability) ? $issue->getReproducability()->getName() : '-'; ?>
					</td>
					<td class="sc_priority<?php if (!$issue->getPriority() instanceof TBGPriority): ?> faded_out<?php endif; ?>"<?php if (!in_array('priority', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getPriority() instanceof TBGPriority) ? $issue->getPriority()->getName() : '-'; ?>
					</td>
					<td class="sc_milestone<?php if (!$issue->getMilestone() instanceof TBGMilestone): ?> faded_out<?php endif; ?>"<?php if (!in_array('milestone', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getMilestone() instanceof TBGMilestone) ? $issue->getMilestone()->getName() : '-'; ?>
					</td>
					<td class="smaller sc_last_updated" title="<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?>"<?php if (!in_array('last_updated', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo tbg_formatTime($issue->getLastUpdatedTime(), 20); ?></td>
					<td class="smaller sc_comments" style="text-align: center;<?php if (!in_array('comments', $visible_columns)): ?> display: none;<?php endif; ?>">
						<?php echo $issue->countUserComments(); ?>
					</td>
				</tr>
	<?php if ($cc == count($issues)): ?>
			</tbody>
		</table>
		<div class="results_summary"><?php echo __('Total number of issues in this group: %number%', array('%number%' => "<b>{$current_count}</b>")); ?></div>
	<?php endif; ?>
	<?php $cc++; ?>
<?php endforeach; ?>
<?php include_template('search/bulkactions', array('mode' => 'bottom')); ?>
<script type="text/javascript">
	document.observe('dom:loaded', function() {
		TBG.Search.setColumns('results_normal', ['title', 'issuetype', 'assigned_to', 'status', 'resolution', 'category', 'severity', 'percent_complete', 'reproducability', 'priority', 'milestone', 'last_updated', 'comments'], [<?php echo "'".join("', '", $visible_columns)."'"; ?>], [<?php echo "'".join("', '", $default_columns)."'"; ?>]);
	});
</script>