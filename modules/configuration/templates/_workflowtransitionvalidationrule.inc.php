<tr id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>">
	<?php

		switch ($rule->getRule())
		{
			case TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES:
				?>
				<td id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_description" style="padding: 2px;">
					<?php echo __('Current user can have no more than %number issues already assigned', array('%number' => '<span id="workflowtransitionvalidationrule_'.$rule->getID().'_value" style="font-weight: bold;">' . (($rule->getRuleValue()) ? (int) $rule->getRuleValue() : __('Unlimited')) . '</span>')); ?>
				</td>
				<?php if (!$rule->getTransition()->isCore()): ?>
					<td id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit" style="display: none; padding: 2px;">
						<form action="<?php echo make_url('configure_workflow_transition_update_validation_rule', array('workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID())); ?>" onsubmit="TBG.Config.Workflows.Transition.Validations.update('<?php echo make_url('configure_workflow_transition_update_validation_rule', array('workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID())); ?>', <?php echo $rule->getID(); ?>);return false;" id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_form">
							<input type="submit" value="<?php echo __('Update'); ?>" style="float: right;">
							<label for="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_input"><?php echo __('Current user can have no more than this many issues assigned'); ?></label>
							<select id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_input" name="rule_value">
								<?php foreach (range(0, 5) as $option): ?>
									<option value="<?php echo $option; ?>"<?php if ((int) $rule->getRuleValue() == $option) echo ' selected'; ?>><?php echo ($option == 0) ? __('Unlimited') : $option; ?></option>
								<?php endforeach; ?>
							</select>
							<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionvalidationrule_' . $rule->getID() . '_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
						</form>
					</td>
				<?php endif; ?>
				<?php
				break;
			case TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID:
			case TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID:
			case TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID:
			case TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID:
			case TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID:
				?>
				<td id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_description" style="padding: 2px;">
					<?php if ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID): ?>
						<?php echo __('Status is any of these values: %statuses', array('%statuses' => '<span id="workflowtransitionvalidationrule_'.$rule->getID().'_value" style="font-weight: bold;">' . (($rule->getRuleValue()) ? $rule->getRuleValueAsJoinedString() : __('Any valid value')) . '</span>')); ?>
					<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID): ?>
						<?php echo __('Priority is any of these values: %priorities', array('%priorities' => '<span id="workflowtransitionvalidationrule_'.$rule->getID().'_value" style="font-weight: bold;">' . (($rule->getRuleValue()) ? $rule->getRuleValueAsJoinedString() : __('Any valid value')) . '</span>')); ?>
					<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID): ?>
						<?php echo __('Resolution is any of these values: %resolutions', array('%resolutions' => '<span id="workflowtransitionvalidationrule_'.$rule->getID().'_value" style="font-weight: bold;">' . (($rule->getRuleValue()) ? $rule->getRuleValueAsJoinedString() : __('Any valid value')) . '</span>')); ?>
					<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID): ?>
						<?php echo __('Reproducability is any of these values: %reproducabilities', array('%reproducabilities' => '<span id="workflowtransitionvalidationrule_'.$rule->getID().'_value" style="font-weight: bold;">' . (($rule->getRuleValue()) ? $rule->getRuleValueAsJoinedString() : __('Any valid value')) . '</span>')); ?>
					<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID): ?>
						<?php echo __('Assignee is member of any of these teams: %teams', array('%teams' => '<span id="workflowtransitionvalidationrule_'.$rule->getID().'_value" style="font-weight: bold;">' . (($rule->getRuleValue()) ? $rule->getRuleValueAsJoinedString() : __('Any valid value')) . '</span>')); ?>
					<?php endif; ?>
				</td>
				<?php if (!$rule->getTransition()->isCore()): ?>
					<td id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit" style="display: none; padding: 2px;">
						<form action="<?php echo make_url('configure_workflow_transition_update_validation_rule', array('workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID())); ?>" onsubmit="TBG.Config.Workflows.Transition.Validations.update('<?php echo make_url('configure_workflow_transition_update_validation_rule', array('workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID())); ?>', <?php echo $rule->getID(); ?>);return false;" id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_form">
							<input type="submit" value="<?php echo __('Update'); ?>" style="float: right;">
							<label>
								<?php if ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID): ?>
									<?php echo __('Status must be any of these values'); ?>
								<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID): ?>
									<?php echo __('Priority must be any of these values'); ?>
								<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID): ?>
									<?php echo __('Resolution must be any of these values'); ?>
								<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID): ?>
									<?php echo __('Reproducability must be any of these values'); ?>
								<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID && $rule->isPost()): ?>
									<?php echo __('Assignee must be member of any of these teams'); ?>
								<?php elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID && $rule->isPre()): ?>
									<?php echo __('User must be member of any of these teams'); ?>
								<?php endif; ?>
							</label>
							<?php

								if ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID)
									$options = TBGStatus::getAll();
								elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID)
									$options = TBGPriority::getAll();
								elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)
									$options = TBGResolution::getAll();
								elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)
									$options = TBGReproducability::getAll();
								elseif ($rule->getRule() == TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID)
									$options = TBGTeam::getAll();

							?>
							<?php foreach ($options as $option): ?>
							<br><input type="checkbox" style="margin-left: 25px;" name="rule_value[<?php echo $option->getID(); ?>]" value="<?php echo $option->getID(); ?>"<?php if ($rule->isValueValid($option->getID())) echo ' checked'; ?> id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_input_<?php echo $option->getID(); ?>"><label for="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_input_<?php echo $option->getID(); ?>" style="font-weight: normal;"><?php echo $option->getName(); ?></label>
							<?php endforeach; ?>
							<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionvalidationrule_' . $rule->getID() . '_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
						</form>
					</td>
				<?php endif; ?>
				<?php
				break;
		}

	?>
	<?php if (!$rule->getTransition()->isCore()): ?>
		<td style="width: 100px; text-align: right;">
			<button id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit_button" onclick="$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit_button').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_delete_button').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_cancel_button').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_description').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit').toggle();"><?php echo __('Edit'); ?></button>
			<button id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_delete_button" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this transition validation rule?'); ?>', '<?php echo __('Please confirm that you really want to delete this transition validation rule.'); ?>', {yes: {click: function() {TBG.Config.Workflows.Transition.Validations.remove('<?php echo make_url('configure_workflow_transition_delete_validation_rule', array('workflow_id' => $rule->getWorkflow()->getID(), 'transition_id' => $rule->getTransition()->getID(), 'rule_id' => $rule->getID())); ?>', <?php echo $rule->getID(); ?>, '<?php echo $rule->isPreOrPost(); ?>', '<?php echo $rule->getRule(); ?>'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"><?php echo __('Delete'); ?></button>
			<button id="workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_cancel_button" onclick="$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit_button').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_delete_button').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_cancel_button').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_description').toggle();$('workflowtransitionvalidationrule_<?php echo $rule->getID(); ?>_edit').toggle();" style="display: none;"><?php echo __('Cancel'); ?></button>
		</td>
	<?php endif; ?>
</tr>
