<tr id="workflowtransitionaction_<?php echo $action->getID(); ?>">
	<?php
	
		$show_edit = true;

		switch ($action->getActionType())
		{
			case TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF:
			case TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE:
			case TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY:
			case TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT:
			case TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY:
			case TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION:
			case TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE:
			case TBGWorkflowTransitionAction::ACTION_USER_START_WORKING:
			case TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING:
			case TBGWorkflowTransitionAction::ACTION_SET_MILESTONE:
			case TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE:
				?>
				<td id="workflowtransitionaction_<?php echo $action->getID(); ?>_description" style="padding: 2px;">
					<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF): ?>
						<?php echo __('Assign the issue to the current user'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_MILESTONE): ?>
						<?php echo __('Set milestone to milestone provided by user'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE): ?>
						<?php echo __('Clear issue assignee'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY): ?>
						<?php echo __('Clear issue priority'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT): ?>
						<?php echo __('Clear issue percent completed'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY): ?>
						<?php echo __('Clear issue reproducability'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION): ?>
						<?php echo __('Clear issue resolution'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_USER_START_WORKING): ?>
						<?php echo __('Start logging time'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING): ?>
						<?php echo __('Stop logging time and optionally add time spent'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE): ?>
						<?php echo __('Mark issue as duplicate of another, existing issue'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE): ?>
						<?php echo __('Mark issue as unique (no longer a duplicate) issue'); ?>
					<?php endif; ?>
				</td>
				<?php
				$show_edit = false;
				break;
			case TBGWorkflowTransitionAction::ACTION_SET_STATUS:
			case TBGWorkflowTransitionAction::ACTION_SET_PRIORITY:
			case TBGWorkflowTransitionAction::ACTION_SET_PERCENT:
			case TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
			case TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION:
			case TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
				?>
				<td id="workflowtransitionaction_<?php echo $action->getID(); ?>_description" style="padding: 2px;">
					<?php if ($action->hasValidTarget()): ?>
						<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_STATUS): ?>
							<?php echo __('Set status to %status', array('%status' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGStatus((int) $action->getTargetValue())->getName() : __('Status provided by user')) . '</span>')); ?>
						<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
							<?php echo __('Set priority to %priority', array('%priority' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGPriority((int) $action->getTargetValue())->getName() : __('Priority provided by user')) . '</span>')); ?>
						<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PERCENT): ?>
							<?php echo __('Set percent completed to %percentcompleted', array('%percentcompleted' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? (int) $action->getTargetValue() : __('Percentage provided by user')) . '</span>')); ?>
						<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
							<?php echo __('Set resolution to %resolution', array('%resolution' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGResolution((int) $action->getTargetValue())->getName() : __('Resolution provided by user')) . '</span>')); ?>
						<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
							<?php echo __('Set reproducability to %reproducability', array('%reproducability' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGReproducability((int) $action->getTargetValue())->getName() : __('Reproducability provided by user')) . '</span>')); ?>
						<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
							<?php 

							if ($action->hasTargetValue())
							{
								$target_details = explode('_', $action->getTargetValue());
								echo __('Assign issue to %assignee', array('%assignee' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($target_details[0] == 'user') ? TBGUser::getB2DBTable()->selectById((int) $target_details[1])->getNameWithUsername() : TBGTeam::getB2DBTable()->selectById((int) $target_details[1])->getName()) . '</span>')); 
							}
							else
							{
								echo __('Assign issue to %assignee', array('%assignee' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . __('User or team specified during transition') . '</span>')); 
							}
							
							?>
						<?php endif; ?>
					<?php elseif ($action->getTargetValue()): ?>
						<span class="generic_error_message"><?php echo __('Invalid transition configuration'); ?></span>
					<?php endif; ?>
				</td>
				<?php if (!$action->getTransition()->isCore()): ?>
					<td id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit" style="display: none; padding: 2px;">
						<form action="<?php echo make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>" onsubmit="TBG.Config.Workflows.Transition.Actions.update('<?php echo make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>', <?php echo $action->getID(); ?>);return false;" id="workflowtransitionaction_<?php echo $action->getID(); ?>_form">
							<input type="submit" value="<?php echo __('Update'); ?>" style="float: right;">
							<label for="workflowtransitionaction_<?php echo $action->getID(); ?>_input">
								<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_STATUS): ?>
									<?php echo __('Set status to %status', array('%status' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
									<?php echo __('Set priority to %priority', array('%priority' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
									<?php echo __('Set resolution to %resolution', array('%resolution' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
									<?php echo __('Set reproducability to %reproducability', array('%reproducability' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
									<?php echo __('Assign issue to %user', array('%user' => '')); ?>
								<?php endif; ?>
							</label>
							<?php

								if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_STATUS)
									$options = TBGStatus::getAll();
								elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)
									$options = TBGPriority::getAll();
								elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PERCENT)
									$options = range(1, 100);
								elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)
									$options = TBGResolution::getAll();
								elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)
									$options = TBGReproducability::getAll();

							?>
							<select id="workflowtransitionaction_<?php echo $action->getID(); ?>_input" name="target_value">
								<option value="0"<?php if ((int) $action->getTargetValue() == 0) echo ' selected'; ?> <?php if (!$action->getTransition()->hasTemplate()): ?>style="display: none;"<?php endif; ?>>
									<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_STATUS): ?>
										<?php echo __('Status provided by user'); ?>
									<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
										<?php echo __('Priority provided by user'); ?>
									<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PERCENT): ?>
										<?php echo __('Percentage provided by user'); ?>
									<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
										<?php echo __('Resolution provided by user'); ?>
									<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
										<?php echo __('Reproducability provided by user'); ?>
									<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
										<?php echo __('User or team specified during transition'); ?>
									<?php endif; ?>
								</option>
								<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
									<optgroup label="<?php echo __('Available users'); ?>">
										<?php foreach ($available_assignees_users as $option): ?>
											<option value="user_<?php echo $option->getID(); ?>"<?php if (isset($target_details) && (int) $target_details[1] == $option->getID()) echo ' selected'; ?>>
												<?php echo $option->getNameWithUsername(); ?>
											</option>
										<?php endforeach; ?>
									</optgroup>
									<optgroup label="<?php echo __('Available teams'); ?>">
										<?php foreach ($available_assignees_teams as $option): ?>
											<option value="team_<?php echo $option->getID(); ?>"<?php if (isset($target_details) && (int) $target_details[1] == $option->getID()) echo ' selected'; ?>>
												<?php echo $option->getName(); ?>
											</option>
										<?php endforeach; ?>
									</optgroup>
								<?php else: ?>
									<?php foreach ($options as $option): ?>
										<option value="<?php echo ($option instanceof TBGIdentifiable) ? $option->getID() : $option; ?>"<?php if (($option instanceof TBGIdentifiable && (int) $action->getTargetValue() == $option->getID()) || (!$option instanceof TBGIdentifiable && (int) $action->getTargetValue() == $option)) echo ' selected'; ?>>
											<?php if ($option instanceof TBGUser): ?>
												<?php echo $option->getNameWithUsername(); ?>
											<?php elseif ($option instanceof TBGIdentifiable): ?>
												<?php echo $option->getName(); ?>
											<?php else: ?>
												<?php echo $option; ?>
											<?php endif; ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionaction_' . $action->getID() . '_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
						</form>
					</td>
				<?php endif; ?>
				<?php
				break;
		}

	?>
	<?php if (!$action->getTransition()->isCore()): ?>
		<td style="width: 100px; text-align: right;">
			<?php if ($show_edit): ?>
				<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_description').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit').toggle();"><?php echo __('Edit'); ?></button>
			<?php endif; ?>
			<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this transition action?'); ?>', '<?php echo __('Please confirm that you really want to delete this transition action.'); ?>', {yes: {click: function() {TBG.Config.Workflows.Transition.Actions.remove('<?php echo make_url('configure_workflow_transition_delete_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>', <?php echo $action->getID(); ?>, '<?php echo $action->getActionType(); ?>'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"><?php echo __('Delete'); ?></button>
			<?php if ($show_edit): ?>
				<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_description').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit').toggle();" style="display: none;"><?php echo __('Cancel'); ?></button>
			<?php endif; ?>
		</td>
	<?php endif; ?>
</tr>
