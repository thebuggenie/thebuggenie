<?php
if (!$tbg_user->isGuest()) include_template('search/bulkactions', array('mode' => 'top'));
$current_count = 0;
$current_estimated_time = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'points' => 0);
$current_spent_time = $current_estimated_time;
foreach ($search_object->getIssues() as $issue):
	list ($showtablestart, $showheader, $prevgroup_id, $groupby_description) = searchActions::resultGrouping($issue, $search_object->getGroupBy(), $cc, $prevgroup_id);
	if (($showtablestart || $showheader) && $cc > 1):
				echo '</tbody></table>';
				include_template('search/results_summary', compact('current_count', 'current_estimated_time', 'current_spent_time'));
				$current_count = 0;
				$current_estimated_time = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'points' => 0);
				$current_spent_time = $current_estimated_time;
	endif;
	$current_count++;
	$estimate = $issue->getEstimatedTime();
	$spenttime = $issue->getSpentTime();
	foreach ($current_estimated_time as $key => $value) $current_estimated_time[$key] += $estimate[$key];
	foreach ($current_spent_time as $key => $value) $current_spent_time[$key] += $spenttime[$key];
	if ($showheader):
?>
		<h5>
			<?php if ($search_object->getGroupBy() == 'issuetype'): ?>
				<?php echo image_tag((($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'icon_unknown') . '_small.png', array('title' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype')))); ?>
			<?php endif; ?>
			<?php echo $groupby_description; ?>
		</h5>
	<?php endif; ?>
	<?php if ($showtablestart): ?>
		<table style="width: 100%;" cellpadding="0" cellspacing="0" class="results_container results_normal">
			<thead>
				<tr>
					<?php if (!$tbg_user->isGuest()): ?>
						<th class="nosort sca_action_selector" style="width: 20px; padding: 1px"><input type="checkbox" /></th>
					<?php endif; ?>
					<?php if (!TBGContext::isProjectContext() && $show_project == true): ?>
						<th style="padding-left: 3px;"><?php echo __('Project'); ?></th>
					<?php endif; ?>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::ISSUE_TYPE); ?>" data-sort-field="<?php echo TBGIssuesTable::ISSUE_TYPE; ?>" class="sc_issuetype <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::ISSUE_TYPE)) echo "sort_{$dir}"; ?>" <?php if (!in_array('issuetype', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Issue type'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::TITLE); ?>" data-sort-field="<?php echo TBGIssuesTable::TITLE; ?>" class="sc_title_container <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::TITLE)) echo "sort_{$dir}"; ?>"><span data-sort-direction="asc" class="sc_title"<?php if (!in_array('title', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Issue'); ?></span></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::ASSIGNEE_USER); ?>" data-sort-field="<?php echo TBGIssuesTable::ASSIGNEE_USER; ?>" class="sc_assigned_to <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::ASSIGNEE_USER)) echo "sort_{$dir}"; ?>"<?php if (!in_array('assigned_to', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Assigned to'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::STATUS); ?>" data-sort-field="<?php echo TBGIssuesTable::STATUS; ?>" class="sc_status <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::STATUS)) echo "sort_{$dir}"; ?>"<?php if (!in_array('status', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Status'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::RESOLUTION); ?>" data-sort-field="<?php echo TBGIssuesTable::RESOLUTION; ?>" class="sc_resolution <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::RESOLUTION)) echo "sort_{$dir}"; ?>"<?php if (!in_array('resolution', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Resolution'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::CATEGORY); ?>" data-sort-field="<?php echo TBGIssuesTable::CATEGORY; ?>" class="sc_category <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::CATEGORY)) echo "sort_{$dir}"; ?>"<?php if (!in_array('category', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Category'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::SEVERITY); ?>" data-sort-field="<?php echo TBGIssuesTable::SEVERITY; ?>" class="sc_severity <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::SEVERITY)) echo "sort_{$dir}"; ?>"<?php if (!in_array('severity', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Severity'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::PERCENT_COMPLETE); ?>" data-sort-field="<?php echo TBGIssuesTable::PERCENT_COMPLETE; ?>" class="sc_percent_complete <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::PERCENT_COMPLETE)) echo "sort_{$dir}"; ?>" style="width: 150px;<?php if (!in_array('percent_complete', $visible_columns)): ?> display: none;<?php endif; ?>"><?php echo __('% completed'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::REPRODUCABILITY); ?>" data-sort-field="<?php echo TBGIssuesTable::REPRODUCABILITY; ?>" class="sc_reproducability <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::REPRODUCABILITY)) echo "sort_{$dir}"; ?>"<?php if (!in_array('reproducability', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Reproducability'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::PRIORITY); ?>" data-sort-field="<?php echo TBGIssuesTable::PRIORITY; ?>" class="sc_priority <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::PRIORITY)) echo "sort_{$dir}"; ?>"<?php if (!in_array('priority', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Priority'); ?></th>
					<th class="sc_components nosort"<?php if (!in_array('components', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Component(s)'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::MILESTONE); ?>" data-sort-field="<?php echo TBGIssuesTable::MILESTONE; ?>" class="sc_milestone <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::MILESTONE)) echo "sort_{$dir}"; ?>"<?php if (!in_array('milestone', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Milestone'); ?></th>
					<th class="sc_estimated_time nosort sc_datetime"<?php if (!in_array('estimated_time', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Estimate'); ?></th>
					<th class="sc_spent_time nosort sc_datetime"<?php if (!in_array('spent_time', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Time spent'); ?></th>
					<th data-sort-direction="<?php echo $search_object->getSortDirection(TBGIssuesTable::LAST_UPDATED); ?>" data-sort-field="<?php echo TBGIssuesTable::LAST_UPDATED; ?>" class="sc_last_updated <?php if ($dir = $search_object->getSortDirection(TBGIssuesTable::LAST_UPDATED)) echo "sort_{$dir}"; ?> numeric sc_datetime"<?php if (!in_array('last_updated', $visible_columns)): ?> style="display: none;"<?php endif; ?>><?php echo __('Last updated'); ?></th>
					<th class="sc_comments nosort" style="width: 20px; padding-bottom: 0; text-align: center;<?php if (!in_array('comments', $visible_columns)): ?> display: none;<?php endif; ?>"><?php echo image_tag('icon_comments.png', array('title' => __('Number of user comments on this issue'))); ?></th>
					<th class="sc_actions nosort" style="width: 20px; padding-bottom: 0; text-align: center;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
	<?php endif; ?>
				<tr class="<?php if ($issue->isClosed()): ?> closed<?php endif; ?><?php if ($issue->hasUnsavedChanges()): ?> changed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?> priority_<?php echo ($issue->getPriority() instanceof TBGPriority) ? $issue->getPriority()->getValue() : 0; ?>" id="issue_<?php echo $issue->getID(); ?>">
					<?php if (!$tbg_user->isGuest()): ?>
						<td class="sca_actions">
							<?php if ($issue->isWorkflowTransitionsAvailable()): ?>
								<input type="checkbox" name="update_issue[<?php echo $issue->getID(); ?>]" value="<?php echo $issue->getID(); ?>">
							<?php endif; ?>
						</td>
					<?php endif; ?>
				<?php if (!TBGContext::isProjectContext() && $show_project == true): ?>
					<td style="padding-left: 5px;"><?php echo link_tag(make_url('project_issues', array('project_key' => $issue->getProject()->getKey())), $issue->getProject()->getName()); ?></td>
				<?php endif; ?>
					<td class="sc_issuetype"<?php if (!in_array('issuetype', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo image_tag((($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'icon_unknown') . '_tiny.png', array('title' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype')))); ?>
						<?php echo ($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype'); ?>
					</td>
					<td class="result_issue">
						<?php $title_visible = (in_array('title', $visible_columns)) ? '' : ' style="display: none;'; ?>
						<a class="issue_link" href="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>">
							<?php if ($issue->countFiles()): ?>
								<?php echo image_tag('icon_attached_information.png', array('title' => __('This issue has %num attachments', array('%num' => $issue->countFiles())))); ?>
							<?php endif; ?>
							<?php if ($issue->isLocked()): ?>
								<?php echo image_tag('icon_locked.png', array('title' => __('Access to this issue is restricted'))); ?>
							<?php endif; ?>
							<span class="issue_no"><?php echo $issue->getFormattedIssueNo(true); ?></span><span class="issue_state <?php echo $issue->isClosed() ? 'closed' : 'open'; ?>"><?php echo $issue->isClosed() ? __('Closed') : __('Open'); ?></span>
							<span class="issue_title sc_title"<?php echo $title_visible; ?>><span class="sc_dash"> - </span><?php echo $issue->getTitle(); ?></span>
						</a>
					</td>
					<td class="sc_assigned_to<?php if (!$issue->isAssigned()): ?> faded_out<?php endif; ?>"<?php if (!in_array('assigned_to', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php if ($issue->isAssigned()): ?>
							<?php if ($issue->getAssignee() instanceof TBGUser): ?>
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
							<div class="sc_status_color status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;"><span class="sc_status_name"><?php echo $issue->getStatus()->getName(); ?></span></div>
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
						<span style="display: none;"><?php echo $issue->getPercentCompleted(); ?></span><?php include_template('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 15)) ?>
					</td>
					<td class="sc_reproducability<?php if (!$issue->getReproducability() instanceof TBGReproducability): ?> faded_out<?php endif; ?>"<?php if (!in_array('reproducability', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getReproducability() instanceof TBGReproducability) ? $issue->getReproducability()->getName() : '-'; ?>
					</td>
					<td class="sc_priority<?php if (!$issue->getPriority() instanceof TBGPriority): ?> faded_out<?php endif; ?>"<?php if (!in_array('priority', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getPriority() instanceof TBGPriority) ? $issue->getPriority()->getName() : '-'; ?>
					</td>
					<?php $component_names = $issue->getComponentNames(); ?>
					<td class="sc_components<?php if (!count($component_names)): ?> faded_out<?php endif; ?>"<?php if (!in_array('components', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo (count($component_names)) ? join(', ', $component_names) : '-'; ?>
					</td>
					<td class="sc_milestone<?php if (!$issue->getMilestone() instanceof TBGMilestone): ?> faded_out<?php endif; ?>"<?php if (!in_array('milestone', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo ($issue->getMilestone() instanceof TBGMilestone) ? link_tag(make_url('project_milestone_details', array('project_key' => $issue->getProject()->getKey(), 'milestone_id' => $issue->getMilestone()->getID())), $issue->getMilestone()->getName()) : '-'; ?>
					</td>
					<td class="sc_estimated_time<?php if (!$issue->hasEstimatedTime()): ?> faded_out<?php endif; ?>"<?php if (!in_array('estimated_time', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo (!$issue->hasEstimatedTime()) ? '-' : TBGIssue::getFormattedTime($issue->getEstimatedTime()); ?>
					</td>
					<td class="sc_spent_time<?php if (!$issue->hasSpentTime()): ?> faded_out<?php endif; ?>"<?php if (!in_array('spent_time', $visible_columns)): ?> style="display: none;"<?php endif; ?>>
						<?php echo (!$issue->hasSpentTime()) ? '-' : TBGIssue::getFormattedTime($issue->getSpentTime()); ?>
					</td>
					<td class="smaller sc_last_updated" title="<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?>"<?php if (!in_array('last_updated', $visible_columns)): ?> style="display: none;"<?php endif; ?>><span style="display: none;"><?php echo $issue->getLastUpdatedTime(); ?></span><?php echo tbg_formatTime($issue->getLastUpdatedTime(), 20); ?></td>
					<td class="smaller sc_comments" style="text-align: center;<?php if (!in_array('comments', $visible_columns)): ?> display: none;<?php endif; ?>">
						<?php echo $issue->countUserComments(); ?>
					</td>
					<td class="sc_actions">
						<div style="position: relative;">
							<a title="<?php echo __('Show more actions'); ?>" class="image dropper" data-id="<?php echo $issue->getID(); ?>" id="more_actions_<?php echo $issue->getID(); ?>_button" href="javascript:void(0);"></a>
							<?php include_template('main/issuemoreactions', array('issue' => $issue, 'multi' => true)); ?>
						</div>
					</td>
				</tr>
	<?php if ($cc == $search_object->getNumberOfIssues()): ?>
			</tbody>
		</table>
		<?php include_template('search/results_summary', compact('current_count', 'current_estimated_time', 'current_spent_time')); ?>
	<?php endif; ?>
	<?php $cc++; ?>
<?php endforeach; ?>
<?php if (!$tbg_user->isGuest()) include_template('search/bulkactions', array('mode' => 'bottom')); ?>
<style type="text/css">
.sc_actions .image {background-image:url(<?php echo image_url('action_dropdown_small.png'); ?>);width:16px;height:16px;display:inline-block;}
</style>
<script type="text/javascript">
	document.observe('dom:loaded', function() {
		setTimeout(function() {
			TBG.Search.setColumns('results_normal', ['title', 'issuetype', 'assigned_to', 'status', 'resolution', 'category', 'severity', 'percent_complete', 'reproducability', 'priority', 'components', 'milestone', 'estimated_time', 'spent_time', 'last_updated', 'comments'], [<?php echo "'".join("', '", $visible_columns)."'"; ?>], [<?php echo "'".join("', '", $default_columns)."'"; ?>]);
		}, 250);
		(function($) {
			// sort headers
			$("#search_results").on("click", "th:not(.nosort)", TBG.Search.sortResults);
			// issue checkboxes
			$(".sca_actions").on("click", "input[type='checkbox']", TBG.Search.toggleCheckbox);
			// issue checkboxes select all
			$(".sca_action_selector").on("click", "input[type='checkbox']", TBG.Search.toggleCheckboxes);
		})(jQuery);
	});
</script>
