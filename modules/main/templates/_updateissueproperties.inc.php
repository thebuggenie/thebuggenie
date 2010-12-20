<div class="rounded_box white borderless shadowed backdrop_box small" style="padding: 5px; text-align: left; font-size: 13px;">
	<div class="backdrop_detail_header"><?php echo $transition->getDescription(); ?></div>
	<form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<div class="backdrop_detail_content">
			<ul class="simple_list">
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS)): ?>
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
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)): ?>
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
						<li id="transition_popup_priority_link" class="faded_out">
							<?php echo __("Priority isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT)): ?>
					<li id="transition_popup_percent_complete_div">
						<label for="transition_popup_set_percent_complete"><?php echo __('Percent complete'); ?></label>
						<select name="percent_complete_id" id="transition_popup_set_percent_complete">
							<?php foreach (range(0, 100) as $percent_complete): ?>
								<option value="<?php echo $percent_complete; ?>"<?php if ($issue->getPercentComplete() == $percent_complete): ?> selected<?php endif; ?>><?php echo $percent_complete; ?></option>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isPercentCompletedVisible()): ?>
						<li id="transition_popup_percent_complete_link" class="faded_out">
							<?php echo __("Percent completed isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)): ?>
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
						<li id="transition_popup_reproducability_link" class="faded_out">
							<?php echo __("Reproducability isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)): ?>
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
						<li id="transition_popup_resolution_link" class="faded_out">
							<?php echo __("Resolution isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)): ?>
					<li id="transition_popup_assignee_div">
						<label for="transition_popup_set_assignee"><?php echo __('Assignee'); ?></label>
						<select name="assignee_id" id="transition_popup_set_assignee">
							<?php foreach ($available_assignees as $assignee): ?>
								<?php if (!$transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_ASSIGNEE_VALID) || $transition->getPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_ASSIGNEE_VALID)->isValueValid($assignee)): ?>
									<option value="<?php echo $assignee->getID(); ?>"<?php if ($issue->getAssignee() instanceof TBGUser && $issue->getAssignee()->getID() == $assignee->getID()): ?> selected<?php endif; ?>><?php echo $assignee->getName(); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isAssigneeVisible()): ?>
						<li id="transition_popup_assignee_link" class="faded_out">
							<?php echo __("Assignee isn't visible for this issuetype / product combination"); ?>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<li style="margin-top: 10px;">
					<label for="transition_popup_comment_body"><?php echo __('Write a comment if you want it to be added'); ?></label>
					<textarea name="comment_body" id="transition_popup_comment_body" style="width: 372px; height: 50px;"></textarea>
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
</div>