<div class="backdrop_box medium issuedetailspopup workflow_transition" style="padding: 5px; text-align: left; font-size: 13px; <?php if ($issue instanceof TBGIssue): ?>display: none;<?php endif; ?>" id="issue_transition_container_<?php echo $transition->getId(); ?>">
	<div class="backdrop_detail_header"><?php echo $transition->getDescription(); ?></div>
<?php if ($issue instanceof TBGIssue): ?>
	<form action="<?php echo make_url('transition_issue', array('project_key' => $project->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
<?php else: ?>
		<form action="<?php echo make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>" method="post" onsubmit="TBG.Search.bulkWorkflowTransition('<?php echo make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>', <?php echo $transition->getID(); ?>);return false;" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" id="bulk_workflow_transition_form">
	<?php foreach ($issues as $issue_id => $i): ?>
		<input type="hidden" name="issue_ids[<?php echo $issue_id; ?>]" value="<?php echo $issue_id; ?>">
	<?php endforeach; ?>
<?php endif; ?>
		<div id="backdrop_detail_content" class="backdrop_detail_content">
			<?php if (!$issue instanceof TBGIssue): ?>
			<div class="rounded_box yellow borderless"><?php echo __('This transition will be applied to %count% selected issues', array('%count%' => count($issues))); ?></div>
			<?php endif; ?>
			<ul class="simple_list">
				<?php if ((($issue instanceof TBGIssue && $issue->isUpdateable() && $issue->canEditAssignee()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
					<li id="transition_popup_assignee_div_<?php echo $transition->getID(); ?>">
						<input type="hidden" name="assignee_id" id="popup_assigned_to_id_<?php echo $transition->getID(); ?>" value="<?php echo ($issue instanceof TBGIssue && $issue->hasAssignee() ? $issue->getAssignee()->getID() : 0); ?>">
						<input type="hidden" name="assignee_type" id="popup_assigned_to_type_<?php echo $transition->getID(); ?>" value="<?php echo ($issue instanceof TBGIssue ? $issue->getAssigneeType() : ''); ?>">
						<input type="hidden" name="assignee_teamup" id="popup_assigned_to_teamup_<?php echo $transition->getID(); ?>" value="0">
						<label for="transition_popup_set_assignee_<?php echo $transition->getID(); ?>"><?php echo __('Assignee'); ?></label>
						<span style="width: 170px; display: <?php if ($issue instanceof TBGIssue && $issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="popup_assigned_to_name_<?php echo $transition->getID(); ?>">
							<?php if ($issue instanceof TBGIssue): ?>
								<?php if ($issue->getAssignee() instanceof TBGUser): ?>
									<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
								<?php elseif ($issue->getAssignee() instanceof TBGTeam): ?>
									<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
								<?php endif; ?>
							<?php endif; ?>
						</span>
						<span class="faded_out" id="popup_no_assigned_to_<?php echo $transition->getID(); ?>"<?php if ($issue instanceof TBGIssue && $issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not assigned to anyone'); ?></span>
						<a href="javascript:void(0);" onclick="$('popup_assigned_to_change_<?php echo $transition->getID(); ?>').toggle();" title="<?php echo __('Click to change assignee'); ?>"><?php echo image_tag('action_dropdown_small.png', array('style' => 'float: right;')); ?></a>
						<div id="popup_assigned_to_name_indicator_<?php echo $transition->getID(); ?>" style="display: none;"><?php echo image_tag('spinning_16.gif', array('style' => 'float: right; margin-left: 5px;')); ?></div>
						<div class="faded_out" id="popup_assigned_to_teamup_info_<?php echo $transition->getID(); ?>" style="clear: both; display: none;"><?php echo __('You will be teamed up with this user'); ?></div>
					</li>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isUpdateable() && !$issue->isDuplicate()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>
					<li class="duplicate_search">
						<h6><?php echo __('Mark as duplicate of another, existing issue'); ?></h6>
						<label for="viewissue_find_issue_<?php echo $transition->getID(); ?>_input"><?php echo __('Find issue(s)'); ?>&nbsp;</label>
						<input class="button button-blue" style="float: right;" type="button" onclick="TBG.Issues.findDuplicate($('duplicate_finder_transition_<?php echo $transition->getID(); ?>').getValue(), <?php echo $transition->getID(); ?>);return false;" value="<?php echo __('Find'); ?>" id="viewissue_find_issue_<?php echo $transition->getID(); ?>_submit">
						<input type="text" name="searchfor" id="viewissue_find_issue_<?php echo $transition->getID(); ?>_input">
						<?php echo image_tag('spinning_20.gif', array('id' => 'find_issue_'.$transition->getID().'_indicator', 'style' => 'display: none;')); ?><br>
						<div id="viewissue_<?php echo $transition->getID(); ?>_duplicate_results"></div>
						<input type="hidden" name="transition_duplicate_ulr[<?php echo $transition->getID(); ?>]" id="duplicate_finder_transition_<?php echo $transition->getID(); ?>" value="<?php echo ($issue instanceof TBGIssue) ? make_url('viewissue_find_issue', array('project_key' => $project->getKey(), 'issue_id' => $issue->getID(), 'type' => 'duplicate')) : make_url('viewissue_find_issue', array('project_key' => $project->getKey(), 'type' => 'duplicate')); ?>">
						<?php if (!$issue instanceof TBGIssue): ?>
						<script type="text/javascript">
							var transition_id = <?php echo $transition->getID(); ?>;
							$('viewissue_find_issue_' + transition_id + '_input').observe('keypress', function(event) {
								console.log(event.keyCode);
								if (event.keyCode == Event.KEY_RETURN) {
									TBG.Issues.findDuplicate($('duplicate_finder_transition_' + transition_id).getValue(), transition_id);
									event.stop();
								}
							});
						</script>
						<?php endif; ?>
					</li>
					<li class="faded_out">
						<?php echo __('If you want to mark this issue as duplicate of another, existing issue, find the issue by entering details to search for, in the box above.'); ?>
					</li>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isUpdateable() && $issue->canEditStatus()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS)->hasTargetValue()): ?>
					<li>
						<label for="transition_popup_set_status_<?php echo $transition->getID(); ?>"><?php echo __('Status'); ?></label>
						<select name="status_id" id="transition_popup_set_status_<?php echo $transition->getID(); ?>">
							<?php foreach ($statuses as $status): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID)->isValueValid($status)): ?>
									<option value="<?php echo $status->getID(); ?>"<?php if ($issue instanceof TBGIssue && $issue->getStatus() instanceof TBGStatus && $issue->getStatus()->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isUpdateable() && $issue->canEditPriority()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)->hasTargetValue()): ?>
					<li id="transition_popup_priority_div_<?php echo $transition->getID(); ?>">
						<label for="transition_popup_set_priority_<?php echo $transition->getID(); ?>"><?php echo __('Priority'); ?></label>
						<select name="priority_id" id="transition_popup_set_priority_<?php echo $transition->getID(); ?>">
							<?php foreach ($fields_list['priority']['choices'] as $priority): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID)->isValueValid($priority)): ?>
									<option value="<?php echo $priority->getID(); ?>"<?php if ($issue instanceof TBGIssue && $issue->getPriority() instanceof TBGPriority && $issue->getPriority()->getID() == $priority->getID()): ?> selected<?php endif; ?>><?php echo $priority->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if ($issue instanceof TBGIssue && !$issue->isPriorityVisible()): ?>
						<li class="faded_out">
							<?php echo __("Priority isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isUpdateable()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT)->hasTargetValue()): ?>
					<li id="transition_popup_percent_complete_div_<?php echo $transition->getID(); ?>">
						<label for="transition_popup_set_percent_complete_<?php echo $transition->getID(); ?>"><?php echo __('Percent complete'); ?></label>
						<select name="percent_complete_id" id="transition_popup_set_percent_complete_<?php echo $transition->getID(); ?>">
							<?php foreach (range(0, 100) as $percent_complete): ?>
								<option value="<?php echo $percent_complete; ?>"<?php if ($issue instanceof TBGIssue && $issue->getPercentCompleted() == $percent_complete): ?> selected<?php endif; ?>><?php echo $percent_complete; ?></option>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (($issue instanceof TBGIssue && (!$issue->isPercentCompletedVisible())) || isset($issues)): ?>
						<li class="faded_out">
							<?php echo __("Percent completed isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isEditable() && $issue->canEditReproducability()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)->hasTargetValue()): ?>
					<li id="transition_popup_reproducability_div_<?php echo $transition->getID(); ?>">
						<label for="transition_popup_set_reproducability_<?php echo $transition->getID(); ?>"><?php echo __('Reproducability'); ?></label>
						<select name="reproducability_id" id="transition_popup_set_reproducability_<?php echo $transition->getID(); ?>">
							<?php foreach ($fields_list['reproducability']['choices'] as $reproducability): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)->isValueValid($reproducability)): ?>
									<option value="<?php echo $reproducability->getID(); ?>"<?php if ($issue instanceof TBGIssue && $issue->getReproducability() instanceof TBGReproducability && $issue->getReproducability()->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (($issue instanceof TBGIssue && (!$issue->isReproducabilityVisible())) || isset($issues)): ?>
						<li class="faded_out">
							<?php echo __("Reproducability isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isUpdateable()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)->hasTargetValue()): ?>
					<li id="transition_popup_resolution_div_<?php echo $transition->getID(); ?>">
						<label for="transition_popup_set_resolution_<?php echo $transition->getID(); ?>"><?php echo __('Resolution'); ?></label>
						<select name="resolution_id" id="transition_popup_set_resolution_<?php echo $transition->getID(); ?>">
							<?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)->isValueValid($resolution)): ?>
									<option value="<?php echo $resolution->getID(); ?>"<?php if ($issue instanceof TBGIssue && $issue->getResolution() instanceof TBGResolution && $issue->getResolution()->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?php echo $resolution->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (($issue instanceof TBGIssue && (!$issue->isResolutionVisible())) || isset($issues)): ?>
						<li class="faded_out">
							<?php echo __("Resolution isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (($issue instanceof TBGIssue && ($issue->isUpdateable() && $issue->canEditMilestone()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_MILESTONE)): ?>
					<li id="transition_popup_milestone_div_<?php echo $transition->getID(); ?>">
						<label for="transition_popup_set_milestone_<?php echo $transition->getID(); ?>"><?php echo __('Milestone'); ?></label>
						<select name="milestone_id" id="transition_popup_set_milestone_<?php echo $transition->getID(); ?>">
							<option value="0"<?php if (!$issue instanceof TBGIssue && $issue->getMilestone() instanceof TBGMilestone): ?> selected<?php endif; ?>><?php echo __('Not determined') ?></option>
							<?php foreach ($project->getMilestones() as $milestone): ?>
								<option value="<?php echo $milestone->getID(); ?>"<?php if ($issue instanceof TBGIssue && $issue->getMilestone() instanceof TBGMilestone && $issue->getMilestone()->getID() == $milestone->getID()): ?> selected<?php endif; ?>><?php echo $milestone->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</li>
					<li class="faded_out">
						<?php echo __("Specify the target milestone for this issue"); ?>
					</li>
					<?php if (($issue instanceof TBGIssue && (!$issue->isMilestoneVisible())) || isset($issues)): ?>
						<li class="faded_out">
							<?php echo __("Milestone isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING)): ?>
					<?php if ($issue instanceof TBGIssue): ?>
						<li id="transition_popup_stop_working_div_<?php echo $transition->getID(); ?>">
							<label for="transition_popup_set_stop_working"><?php echo __('Log time spent'); ?></label>
							<div style="width: 435px; float: left;">
								<?php $time_spent = $issue->calculateTimeSpent(); ?>
								<input type="radio" name="did" id="transition_popup_set_stop_working_<?php echo $transition->getID(); ?>" value="something" checked onchange="$('transition_popup_set_stop_working_specify_log_div_<?php echo $transition->getID(); ?>').hide();"><label for="transition_popup_set_stop_working_<?php echo $transition->getID(); ?>" class="simple"><?php echo __('Yes'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<span class="faded_out"><?php echo __('Adds %hours% hour(s), %days% day(s) and %weeks% week(s)', array('%hours%' => $time_spent['hours'], '%days%' => $time_spent['days'], '%weeks%' => $time_spent['weeks'])); ?></span><br>
								<input type="radio" name="did" id="transition_popup_set_stop_working_no_log_<?php echo $transition->getID(); ?>" value="nothing" onchange="$('transition_popup_set_stop_working_specify_log_div_<?php echo $transition->getID(); ?>').hide();"><label for="transition_popup_set_stop_working_no_log_<?php echo $transition->getID(); ?>" class="simple"><?php echo __('No'); ?></label><br>
								<input type="radio" name="did" id="transition_popup_set_stop_working_specify_log_<?php echo $transition->getID(); ?>" value="this" onchange="$('transition_popup_set_stop_working_specify_log_div_<?php echo $transition->getID(); ?>').show()"><label for="transition_popup_set_stop_working_specify_log_<?php echo $transition->getID(); ?>" class="simple"><?php echo __('Yes, let me specify'); ?></label>
								<div id="transition_popup_set_stop_working_specify_log_div_<?php echo $transition->getID(); ?>" style="display: none;">
									<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'spent_time', 'mode' => 'inline', 'clear' => false, 'save' => false, 'hidden' => false, 'headers' => false)); ?>
								</div>
							</div>
							<br style="clear: both;">
						</li>
					<?php else: ?>
						<input type="hidden" name="did" id="transition_popup_set_stop_working_no_log_<?php echo $transition->getID(); ?>" value="nothing">
					<?php endif; ?>
				<?php endif; ?>
				<li style="margin-top: 10px;">
					<label for="transition_popup_comment_body"><?php echo __('Write a comment if you want it to be added'); ?></label><br>
					<?php include_template('main/textarea', array('area_name' => 'comment_body', 'area_id' => 'transition_popup_comment_body_'.$transition->getID(), 'height' => '120px', 'width' => '585px', 'value' => '')); ?>
				</li>
			</ul>
			<div style="text-align: right; margin-right: 5px;">
				<?php echo image_tag('spinning_32.gif', array('style' => 'margin: -3px 0 -3px 5px; display: none;', 'id' => 'transition_working_'.$transition->getID().'_indicator')); ?>
				<input type="submit" class="workflow_transition_submit_button" value="<?php echo $transition->getName(); ?>" id="transition_working_<?php echo $transition->getID(); ?>_submit">
			</div>
		</div>
		<div class="backdrop_detail_footer">
			<a href="javascript:void(0);" onclick="$('workflow_transition_fullpage').fade({duration: 0.2});"><?php echo __('Cancel and close this pop-up'); ?></a>
		</div>
	</form>
	<?php if (($issue instanceof TBGIssue && ($issue->canEditAssignee()) || isset($issues)) && $transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
		<?php include_component('identifiableselector', array(	'html_id' 			=> 'popup_assigned_to_change_'.$transition->getID(),
																'header' 			=> __('Assign this issue'),
																'callback'		 	=> "TBG.Issues.updateWorkflowAssignee('" . make_url('issue_gettempfieldvalue', array('field' => 'assigned_to', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', %identifiable_value%, %identifiable_type%, ".$transition->getID().");",
																'teamup_callback' 	=> "TBG.Issues.updateWorkflowAssigneeTeamup('" . make_url('issue_gettempfieldvalue', array('field' => 'assigned_to', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', %identifiable_value%, %identifiable_type%, ".$transition->getID().");",
																'clear_link_text'	=> __('Clear current assignee'),
																'base_id'			=> 'popup_assigned_to',
																'include_teams'		=> true,
																'allow_clear'		=> false,
																'style'				=> array('top' => '65px', 'right' => '5px'),
																'absolute'			=> true)); ?>
	<?php endif; ?>
</div>