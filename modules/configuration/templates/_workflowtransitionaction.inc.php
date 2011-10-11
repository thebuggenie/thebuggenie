<tr id="workflowtransitionaction_<?php echo $action->getID(); ?>">
	<?php

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
						<?php echo __('Mark issue as being worked on by the assigned user'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING): ?>
						<?php echo __('Mark issue as no longer being worked on, and optionally add time spent'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE): ?>
						<?php echo __('Mark issue as duplicate of another, existing issue'); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE): ?>
						<?php echo __('Mark issue as unique (no longer a duplicate) issue'); ?>
					<?php endif; ?>
				</td>
				<?php if (!$action->getTransition()->isCore()): ?>
					<td style="width: 100px; text-align: right;">
						<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete').toggle();"><?php echo __('Delete'); ?></button>
					</td>
				<?php endif; ?>
				<?php
				break;
			case TBGWorkflowTransitionAction::ACTION_SET_STATUS:
			case TBGWorkflowTransitionAction::ACTION_SET_PRIORITY:
			case TBGWorkflowTransitionAction::ACTION_SET_PERCENT:
			case TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
			case TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION:
			case TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
				?>
				<td id="workflowtransitionaction_<?php echo $action->getID(); ?>_description" style="padding: 2px;">
					<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_STATUS): ?>
						<?php echo __('Set status to %status%', array('%status%' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGStatus((int) $action->getTargetValue())->getName() : __('Status provided by user')) . '</span>')); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
						<?php echo __('Set priority to %priority%', array('%priority%' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGPriority((int) $action->getTargetValue())->getName() : __('Priority provided by user')) . '</span>')); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PERCENT): ?>
						<?php echo __('Set percent completed to %percentcompleted%', array('%percentcompleted%' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? (int) $action->getTargetValue() : __('Percentage provided by user')) . '</span>')); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
						<?php echo __('Set resolution to %resolution%', array('%resolution%' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGResolution((int) $action->getTargetValue())->getName() : __('Resolution provided by user')) . '</span>')); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
						<?php echo __('Set reproducability to %reproducability%', array('%reproducability%' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGReproducability((int) $action->getTargetValue())->getName() : __('Reproducability provided by user')) . '</span>')); ?>
					<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
						<?php echo __('Assign issue to %user%', array('%user%' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? TBGContext::factory()->TBGUser((int) $action->getTargetValue())->getNameWithUsername() : __('User specified during transition')) . '</span>')); ?>
					<?php endif; ?>
				</td>
				<?php if (!$action->getTransition()->isCore()): ?>
					<td id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit" style="display: none; padding: 2px;">
						<form action="<?php echo make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>" onsubmit="TBG.Config.Workflows.Transition.Actions.update('<?php echo make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>', <?php echo $action->getID(); ?>);return false;" id="workflowtransitionaction_<?php echo $action->getID(); ?>_form">
							<input type="submit" value="<?php echo __('Update'); ?>" style="float: right;">
							<label for="workflowtransitionaction_<?php echo $action->getID(); ?>_input">
								<?php if ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_STATUS): ?>
									<?php echo __('Set status to %status%', array('%status%' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
									<?php echo __('Set priority to %priority%', array('%priority%' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
									<?php echo __('Set resolution to %resolution%', array('%resolution%' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
									<?php echo __('Set reproducability to %reproducability%', array('%reproducability%' => '')); ?>
								<?php elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
									<?php echo __('Assign issue to %user%', array('%user%' => '')); ?>
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
								elseif ($action->getActionType() == TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)
									$options = $available_assignees;

							?>
							<select id="workflowtransitionaction_<?php echo $action->getID(); ?>_input" name="target_value">
								<option value="0"<?php if ((int) $action->getTargetValue() == 0) echo ' selected'; ?>>
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
										<?php echo __('User specified during transition'); ?>
									<?php endif; ?>
								</option>
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
							</select>
							<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionaction_' . $action->getID() . '_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
						</form>
					</td>
					<td style="width: 100px; text-align: right;">
						<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_description').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit').toggle();"><?php echo __('Edit'); ?></button>
						<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete').toggle();"><?php echo __('Delete'); ?></button>
						<button id="workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_description').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit').toggle();" style="display: none;"><?php echo __('Cancel'); ?></button>
					</td>
				<?php endif; ?>
				<?php
				break;
		}

	?>
</tr>
<?php if (!$action->getTransition()->isCore()): ?>
<tr>
	<td colspan="2">
		<div class="rounded_box white shadowed" style="position: absolute; width: 285px; display: none;" id="workflowtransitionaction_<?php echo $action->getID(); ?>_delete">
			<div class="header"><?php echo __('Confirm delete transition action'); ?></div>
			<div class="content">
				<?php echo __('Do you really want to delete this transition action?'); ?>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Yes'), array('onclick' => "TBG.Config.Workflows.Transition.Actions.remove('".make_url('configure_workflow_transition_delete_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID()))."', {$action->getID()}, '{$action->getActionType()}');")); ?> ::
					<b><?php echo javascript_link_tag(__('No'), array('onclick' => "\$('workflowtransitionaction_{$action->getID()}_delete').toggle();")); ?></b>
				</div>
				<div style="padding: 10px 0 10px 0; display: none;" id="workflowtransitionaction_<?php echo $action->getID(); ?>_delete_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
			</div>
		</div>
	</td>
</tr>
<?php endif; ?>