<div class="rounded_box white borderless shadowed backdrop_box mediumsmall issuedetailspopup" style="padding: 5px; text-align: left; font-size: 13px;">
	<div class="backdrop_detail_header"><?php echo $transition->getDescription(); ?></div>
	<form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<div class="backdrop_detail_content">
			<ul class="simple_list">
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
					<li id="transition_popup_assignee_div">
						<input type="hidden" name="assignee_id" id="popup_assigned_to_id" value="<?php echo $issue->getAssigneeID(); ?>">
						<input type="hidden" name="assignee_type" id="popup_assigned_to_type" value="<?php echo $issue->getAssigneeType(); ?>">
						<input type="hidden" name="assignee_teamup" id="popup_assigned_to_teamup" value="<?php echo $issue->getAssigneeType(); ?>">
						<label for="transition_popup_set_assignee"><?php echo __('Assignee'); ?></label>
						<span style="width: 170px; display: <?php if ($issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="popup_assigned_to_name">
							<?php if ($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER): ?>
								<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
							<?php elseif ($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_TEAM): ?>
								<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
							<?php endif; ?>
						</span>
						<span class="faded_out" id="popup_no_assigned_to"<?php if ($issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not assigned to anyone'); ?></span>
						<a href="javascript:void(0);" onclick="$('popup_assigned_to_change').toggle();" title="<?php echo __('Click to change assignee'); ?>"><?php echo image_tag('action_dropdown_small.png', array('style' => 'float: right;')); ?></a>
						<div id="popup_assigned_to_name_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif', array('style' => 'float: right; margin-left: 5px;')); ?></div>
						<div class="faded_out" id="popup_assigned_to_teamup_info" style="clear: both; display: none;"><?php echo __('You will be teamed up with this user'); ?></div>
					</li>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS)->hasTargetValue()): ?>
					<li>
						<label for="transition_popup_set_status"><?php echo __('Status'); ?></label>
						<select name="status_id" id="transition_popup_set_status">
							<?php foreach ($statuses as $status): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID)->isValueValid($status)): ?>
									<option value="<?php echo $status->getID(); ?>"<?php if ($issue->getStatus() instanceof TBGStatus && $issue->getStatus()->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)->hasTargetValue()): ?>
					<li id="transition_popup_priority_div">
						<label for="transition_popup_set_priority"><?php echo __('Priority'); ?></label>
						<select name="priority_id" id="transition_popup_set_priority">
							<?php foreach ($fields_list['priority']['choices'] as $priority): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID)->isValueValid($priority)): ?>
									<option value="<?php echo $priority->getID(); ?>"<?php if ($issue->getPriority() instanceof TBGPriority && $issue->getPriority()->getID() == $priority->getID()): ?> selected<?php endif; ?>><?php echo $priority->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isPriorityVisible()): ?>
						<li class="faded_out">
							<?php echo __("Priority isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT)->hasTargetValue()): ?>
					<li id="transition_popup_percent_complete_div">
						<label for="transition_popup_set_percent_complete"><?php echo __('Percent complete'); ?></label>
						<select name="percent_complete_id" id="transition_popup_set_percent_complete">
							<?php foreach (range(0, 100) as $percent_complete): ?>
								<option value="<?php echo $percent_complete; ?>"<?php if ($issue->getPercentComplete() == $percent_complete): ?> selected<?php endif; ?>><?php echo $percent_complete; ?></option>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isPercentCompletedVisible()): ?>
						<li class="faded_out">
							<?php echo __("Percent completed isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)->hasTargetValue()): ?>
					<li id="transition_popup_reproducability_div">
						<label for="transition_popup_set_reproducability"><?php echo __('Reproducability'); ?></label>
						<select name="reproducability_id" id="transition_popup_set_reproducability">
							<?php foreach ($fields_list['reproducability']['choices'] as $reproducability): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)->isValueValid($reproducability)): ?>
									<option value="<?php echo $reproducability->getID(); ?>"<?php if ($issue->getReproducability() instanceof TBGReproducability && $issue->getReproducability()->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isReproducabilityVisible()): ?>
						<li class="faded_out">
							<?php echo __("Reproducability isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)->hasTargetValue()): ?>
					<li id="transition_popup_resolution_div">
						<label for="transition_popup_set_resolution"><?php echo __('Resolution'); ?></label>
						<select name="resolution_id" id="transition_popup_set_resolution">
							<?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)->isValueValid($resolution)): ?>
									<option value="<?php echo $resolution->getID(); ?>"<?php if ($issue->getResolution() instanceof TBGResolution && $issue->getResolution()->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?php echo $resolution->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isResolutionVisible()): ?>
						<li class="faded_out">
							<?php echo __("Resolution isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING) && $issue->isBeingWorkedOn()): ?>
					<li id="transition_popup_stop_working_div">
						<label for="transition_popup_set_stop_working"><?php echo __('Log time spent'); ?></label>
						<input type="radio" name="did" id="transition_popup_set_stop_working" value="something" checked><label for="transition_popup_set_stop_working" class="simple"><?php echo __('Yes'); ?></label>&nbsp;
						<input type="radio" name="did" id="transition_popup_set_stop_working_no_log" value="nothing"><label for="transition_popup_set_stop_working_no_log" class="simple"><?php echo __('No'); ?></label>
					</li>
				<?php endif; ?>
				<li style="margin-top: 10px;">
					<label for="transition_popup_comment_body"><?php echo __('Write a comment if you want it to be added'); ?></label><br>
					<?php include_template('main/textarea', array('area_name' => 'comment_body', 'area_id' => 'transition_popup_comment_body', 'height' => '120px', 'width' => '480px', 'value' => '')); ?>
				</li>
			</ul>
			<div style="text-align: right; margin-right: 5px;">
				<input type="submit" value="<?php echo $transition->getName(); ?>">
			</div>
		</div>
		<div class="backdrop_detail_footer">
			<?php echo '<a href="javascript:void(0);" onclick="resetFadedBackdrop();">' . __('Cancel and close this pop-up') . '</a>'; ?>
		</div>
	</form>
	<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
		<?php include_component('identifiableselector', array(	'html_id' 			=> 'popup_assigned_to_change', 
																'header' 			=> __('Assign this issue'),
																'callback'		 	=> "updateWorkflowAssignee('" . make_url('issue_gettempfieldvalue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', %identifiable_value%, %identifiable_type%);",
																'teamup_callback' 	=> "updateWorkflowAssigneeTeamup('" . make_url('issue_gettempfieldvalue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', %identifiable_value%, %identifiable_type%);",
																'clear_link_text'	=> __('Clear current assignee'),
																'base_id'			=> 'popup_assigned_to',
																'include_teams'		=> true,
																'allow_clear'		=> false,
																'absolute'			=> true)); ?>
	<?php endif; ?>
</div>