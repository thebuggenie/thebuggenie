<?php

	if ($workflow instanceof TBGWorkflow)
		$tbg_response->setTitle(__('Configure workflow "%workflow_name"', array('%workflow_name' => $workflow->getName())));
	else
		$tbg_response->setTitle(__('Configure workflows'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top" style="padding-left: 15px;">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'transition', 'workflow' => $workflow, 'transition' => $transition)); ?>
			<div class="content" style="width: 730px;" id="workflow_step_container">
				<?php if ($transition instanceof TBGWorkflowTransition): ?>
					<h3>
						<?php if (!$transition->isCore()): ?>
							<?php echo javascript_link_tag(__('Edit details'), array('onclick' => "\$('transition_details_form').toggle();\$('transition_details_info').toggle();", 'class' => 'button button-silver')); ?>
						<?php endif; ?>
						<?php echo __('Transition "%transition_name"', array('%transition_name' => $transition->getName())); ?>
					</h3>
					<div class="workflow_step_intro">
						<div class="content">
							<?php echo __('This page shows all the available details for this transition for the selected workflow, as well as incoming and outgoing steps from this transition.'); ?>
							<?php echo __('You can edit all details about the selected transitions from this page.'); ?><br>
						</div>
					</div>
					<div class="lightyellowbox" id="workflow_browser_step">
						<div class="header"><?php echo __('Transition path'); ?></div>
						<div class="content">
							<?php if ($transition->getNumberOfIncomingSteps() == 0 && $transition->getID() !== $workflow->getInitialTransition()->getID()): ?>
								<div class="faded_out"><?php echo __("This transaction doesn't have any originating step"); ?></div>
							<?php elseif ($transition === $workflow->getInitialTransition()): ?>
								<div class="faded_out"><?php echo __("Issue is created"); ?></div>
							<?php else: ?>
								<?php

								$output = array();
								foreach ($transition->getIncomingSteps() as $step)
								{
									$output[] = '<div class="workflow_browser_step_transition">'.link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), $step->getName())."</div>";
								}
								$glue = "<div class=\"faded_out\">".__('%a_workflow_step_transition or %a_workflow_step_transition', array('%a_workflow_step_transition' => ''))."</div>";
								echo join($glue, $output);

								?>
							<?php endif; ?>
							<div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
							<div class="workflow_browser_step_name"><?php echo $transition->getName(); ?></div>
							<div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
							<div class="workflow_browser_step_transition"><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getOutgoingStep()->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></div>
						</div>
					</div>
					<div id="workflow_details_transition">
						<dl id="transition_details_info">
							<dt><?php echo __('Name'); ?></dt>
							<dd><?php echo $transition->getName(); ?></dd>
							<dt><?php echo __('Description'); ?></dt>
							<dd class="description"><?php echo $transition->getDescription(); ?></dd>
							<dt><?php echo __('Template'); ?></dt>
							<dd><?php echo ($transition->hasTemplate()) ? $transition->getTemplateName() : __('No template used - transition happens instantly'); ?></dd>
							<dt><?php echo __('Outgoing step'); ?></dt>
							<dd><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getOutgoingStep()->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></dd>
						</dl>
						<?php if (!$transition->isCore()): ?>
							<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'mode' => 'edit')); ?>" id="transition_details_form" style="display: none;" onsubmit="$('transition_update_indicator').show();$('update_transition_buttons').hide();">
								<dl>
									<?php if (!$transition->isInitialTransition()): ?>
										<dt><label for="edit_transition_<?php echo $transition->getID(); ?>_name"><?php echo __('Transition name'); ?></label></dt>
										<dd>
											<input type="text" id="edit_transition_<?php echo $transition->getID(); ?>_name" name="transition_name" style="width: 300px;" value="<?php echo $transition->getName(); ?>"><br>
											<div class="faded_out"><?php echo __('This name will be presented to the user as a link'); ?></div>
										</dd>
										<dt><label for="edit_transition_<?php echo $transition->getID(); ?>_description" class="optional"><?php echo __('Description'); ?></label></dt>
										<dd>
											<input type="text" id="edit_transition_<?php echo $transition->getID(); ?>_description" name="transition_description" style="width: 300px;" value="<?php echo $transition->getDescription(); ?>">
											<div class="faded_out"><?php echo __('This optional description will be presented to the user'); ?></div>
										</dd>
										<dt><label for="edit_transition_<?php echo $transition->getID(); ?>_template"><?php echo __('Popup template'); ?></label></dt>
										<dd>
											<select id="edit_transition_<?php echo $transition->getID(); ?>_template" name="template">
												<option value=""<?php if ($transition->getTemplate() == ''): ?> selected<?php endif; ?>><?php echo __('No template used - transition happens instantly'); ?></option>
												<?php foreach (TBGWorkflowTransition::getTemplates() as $template_key => $template_name): ?>
													<option value="<?php echo $template_key; ?>"<?php if ($transition->getTemplate() == $template_key): ?> selected<?php endif; ?>><?php echo $template_name; ?></option>
												<?php endforeach; ?>
											</select>
										</dd>
									<?php else: ?>
										<dt><?php echo __('Name'); ?></dt>
										<dd><?php echo $transition->getName(); ?></dd>
										<dt><?php echo __('Description'); ?></dt>
										<dd class="description"><?php echo $transition->getDescription(); ?></dd>
										<dt><?php echo __('Template'); ?></dt>
										<dd><?php echo __('No template used - transition happens instantly'); ?></dd>
									<?php endif; ?>
									<dt><label for="edit_transition_<?php echo $transition->getID(); ?>_outgoing_step_id"><?php echo __('Outgoing step'); ?></label></dt>
									<dd>
										<select id="edit_transition_<?php echo $transition->getID(); ?>_outgoing_step_id" name="outgoing_step_id">
											<?php foreach ($transition->getWorkflow()->getSteps() as $workflow_step): ?>
												<option value="<?php echo $workflow_step->getID(); ?>"<?php if ($workflow_step->getID() == $transition->getOutgoingStep()->getID()): ?> selected<?php endif; ?>><?php echo $workflow_step->getName(); ?></option>
											<?php endforeach; ?>
										</select>
									</dd>
								</dl>
								<br style="clear: both;">
								<div style="text-align: right; clear: both; padding: 10px 0 0 0;" id="update_transition_buttons">
									<input type="submit" value="<?php echo __('Update transition details'); ?>" name="edit">
									<?php echo __('%update_transition_details or %cancel', array('%update_transition_details' => '', '%cancel' => '')); ?>
									<b><?php echo javascript_link_tag(__('cancel'), array('onclick' => "\$('transition_details_form').toggle();\$('transition_details_info').toggle();")); ?></b>
								</div>
								<div style="text-align: right; padding: 10px 0 10px 0; display: none;" id="transition_update_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
							</form>
						<?php endif; ?>
					</div>
					<br style="clear: both;">
					<div id="workflow_transition_actions_validations">
						<div id="pre_validation_tab_pane">
							<h3>
								<?php if (!$transition->isCore()): ?>
									<a href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('add_pre_validation_rule').toggle();" class="button button-silver">Add validation rule</a>
									<ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_pre_validation_rule" style="display: none;">
										<li <?php if ($transition->hasPreValidationRule(TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'pre', 'rule' => TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES)); ?>', 'pre', '<?php echo TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES; ?>');"><?php echo __('Max number of assigned issues'); ?></a></li>
										<li <?php if ($transition->hasPreValidationRule(TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'pre', 'rule' => TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID)); ?>', 'pre', '<?php echo TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID; ?>');"><?php echo __('User must be member of a certain team'); ?></a></li>
									</ul>
								<?php endif; ?>
								<?php echo __('Pre-transition validation rules'); ?>
								<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionprevalidationrule_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
							</h3>
							<?php if ($transition !== $workflow->getInitialTransition()): ?>
								<div class="content" style="padding: 5px 0 10px 2px;">
									<?php echo __('The following validation rules has to be fullfilled for the transition to be available to the user'); ?>
								</div>
								<table cellpadding="0" cellspacing="0" style="width: 100%;">
									<tbody class="hover_highlight" id="workflowtransitionprevalidationrules_list">
									<?php foreach ($transition->getPreValidationRules() as $rule): ?>
										<?php include_template('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
									<?php endforeach; ?>
									</tbody>
								</table>
								<span class="faded_out" id="no_workflowtransitionprevalidationrules"<?php if ($transition->hasPreValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no pre-validation rules'); ?></span>
							<?php else: ?>
								<span class="faded_out"><?php echo __('This is the initial transition, so no pre-transition validation is performed'); ?></span>
							<?php endif; ?>
						</div>
						<div id="post_validation_tab_pane">
							<h3>
								<?php if (!$transition->isCore() && $transition->hasTemplate()): ?>
									<a href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('add_post_validation_rule').toggle();" class="button button-silver dropper">Add validation rule</a>
									<ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_post_validation_rule" style="display: none;">
										<li <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID)); ?>', 'post', '<?php echo TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID; ?>');"><?php echo __('Validate specified priority'); ?></a></li>
										<li <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)); ?>', 'post', '<?php echo TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID; ?>');"><?php echo __('Validate specified reproducability'); ?></a></li>
										<li <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)); ?>', 'post', '<?php echo TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID; ?>');"><?php echo __('Validate specified resolution'); ?></a></li>
										<li <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID)); ?>', 'post', '<?php echo TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID; ?>');"><?php echo __('Validate specified status'); ?></a></li>
										<li <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID)); ?>', 'post', '<?php echo TBGWorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID; ?>');"><?php echo __('Validate team membership of assignee'); ?></a></li>
									</ul>
								<?php endif; ?>
								<?php echo __('Post-transition validation rules'); ?>
								<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionpostvalidationrule_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
							</h3>
							<?php if ($transition->hasTemplate()): ?>
								<div class="content" style="padding: 5px 0 10px 2px;">
									<?php echo __('The following validation rules will be applied to the input given by the user in the transition view. If the validation fails, the transition will not take place.'); ?>
								</div>
								<table cellpadding="0" cellspacing="0" style="width: 100%;">
									<tbody class="hover_highlight" id="workflowtransitionpostvalidationrules_list">
									<?php foreach ($transition->getPostValidationRules() as $rule): ?>
										<?php include_template('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
									<?php endforeach; ?>
									</tbody>
								</table>
								<span class="faded_out" id="no_workflowtransitionpostvalidationrules"<?php if ($transition->hasPostValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no post validation rules'); ?></span>
							<?php else: ?>
								<span class="faded_out"><?php echo __('This transition does not use any template, so user input cannot be validated'); ?></span>
							<?php endif; ?>
						</div>
						<div id="actions_tab_pane">
							<h3>
								<?php if (!$transition->isCore()): ?>
									<a href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('add_post_action').toggle();" class="button button-silver">Add transition action</a>
									<ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_post_action" style="display: none;">
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE; ?>');"><?php echo __('Assign the issue to a user'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF; ?>');"><?php echo __('Assign the issue to the current user'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE; ?>');"><?php echo __('Clear issue assignee'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY; ?>');"><?php echo __('Clear issue priority'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_PRIORITY; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_PRIORITY; ?>');"><?php echo __('Set issue priority'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT; ?>');"><?php echo __('Clear issue percent'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_PERCENT; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_PERCENT)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_PERCENT; ?>');"><?php echo __('Set issue percent'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY; ?>');"><?php echo __('Clear issue reproducability'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY; ?>');"><?php echo __('Set issue reproducability'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION; ?>');"><?php echo __('Clear issue resolution'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION; ?>');"><?php echo __('Set issue resolution'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE; ?>');"><?php echo __('Mark as not duplicate'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE; ?>');"><?php echo __('Mark as duplicate'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_STATUS; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_STATUS)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_STATUS; ?>');"><?php echo __('Set issue status'); ?></a></li>
										<?php if ($transition->hasTemplate()): ?>
											<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_MILESTONE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_MILESTONE; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_SET_MILESTONE)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_SET_MILESTONE; ?>');"><?php echo __('Set issue milestone'); ?></a></li>
										<?php endif; ?>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_USER_START_WORKING)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_USER_START_WORKING; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_USER_START_WORKING)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_USER_START_WORKING; ?>');"><?php echo __('Start logging time'); ?></a></li>
										<li <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING)); ?>', '<?php echo TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING; ?>');"><?php echo __('Stop logging time and optionally add time spent'); ?></a></li>
									</ul>
								<?php endif; ?>
								<?php echo __('Post-transition actions'); ?>
								<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionaction_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
							</h3>
							<div class="content" style="padding: 5px 0 10px 2px;">
								<?php echo __('The following actions will be applied to the issue during this transition.'); ?>
							</div>
							<table cellpadding="0" cellspacing="0" style="width: 100%;">
								<tbody class="hover_highlight" id="workflowtransitionactions_list">
								<?php foreach ($transition->getActions() as $action): ?>
									<?php include_component('configuration/workflowtransitionaction', array('action' => $action)); ?>
								<?php endforeach; ?>
								<?php if ($transition->hasTemplate()): ?>
									<tr>
										<td colspan="2"><?php echo __('Add a comment if one is specified'); ?></td>
									</tr>
								<?php endif; ?>
								</tbody>
							</table>
							<span class="faded_out" id="no_workflowtransitionactions"<?php if ($transition->hasActions()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no actions'); ?></span>
						</div>
					</div>
				<?php else: ?>
					<div class="redbox" id="no_such_workflow_error">
						<div class="header"><?php echo $error; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>
