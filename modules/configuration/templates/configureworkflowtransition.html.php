<?php

	if ($workflow instanceof TBGWorkflow)
		$tbg_response->setTitle(__('Configure workflow "%workflow_name%"', array('%workflow_name%' => $workflow->getName())));
	else
		$tbg_response->setTitle(__('Configure workflows'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top" style="padding-left: 15px;">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'transition', 'workflow' => $workflow, 'transition' => $transition)); ?>
			<div class="content" style="width: 788px;" id="workflow_step_container">
				<?php if ($transition instanceof TBGWorkflowTransition): ?>
					<div class="rounded_box lightgrey borderless workflow_step_intro">
						<div class="header"><?php echo __('Transition "%transition_name%"', array('%transition_name%' => $transition->getName())); ?></div>
						<div class="content">
							<?php echo __('This page shows all the available details for this transition for the selected workflow, as well as incoming and outgoing steps from this transition.'); ?>
							<?php echo __('You can edit all details about the selected transitions from this page.'); ?><br>
							<?php if (!$transition->isCore()): ?>
								<br>
								<b><?php echo javascript_link_tag(__('Edit this transition'), array('onclick' => "\$('transition_details_form').toggle();\$('transition_details_info').toggle();")); ?></b>
							<?php endif; ?>
						</div>
					</div>
					<div id="workflow_details_transition">
						<dl id="transition_details_info">
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
									<dt><label for="edit_transition_<?php echo $transition->getID(); ?>_outgoing_step_id"><?php echo __('Outgoing step'); ?></label></dt>
									<dd>
										<select id="edit_transition_<?php echo $transition->getID(); ?>_outgoing_step_id" name="outgoing_step_id">
											<?php foreach ($transition->getWorkflow()->getSteps() as $workflow_step): ?>
												<option value="<?php echo $workflow_step->getID(); ?>"<?php if ($workflow_step->getID() == $transition->getOutgoingStep()->getID()): ?> selected<?php endif; ?>><?php echo $workflow_step->getName(); ?></option>
											<?php endforeach; ?>
										</select>
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
								</dl>
								<br style="clear: both;">
								<div style="text-align: right; clear: both; padding: 10px 0 0 0;" id="update_transition_buttons">
									<input type="submit" value="<?php echo __('Update transition details'); ?>" name="edit">
									<?php echo __('%update_transition_details% or %cancel%', array('%update_transition_details%' => '', '%cancel%' => '')); ?>
									<b><?php echo javascript_link_tag(__('cancel'), array('onclick' => "\$('transition_details_form').toggle();\$('transition_details_info').toggle();")); ?></b>
								</div>
								<div style="text-align: right; padding: 10px 0 10px 0; display: none;" id="transition_update_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
							</form>
						<?php endif; ?>
					</div>
					<div class="rounded_box lightyellow" id="workflow_browser_step">
						<div class="header"><?php echo __('Transition path'); ?></div>
						<div class="content">
							<?php if ($transition->getNumberOfIncomingSteps() == 0): ?>
								<div class="faded_out"><?php echo __("This transaction doesn't have any originating step"); ?></div>
							<?php else: ?>
								<?php

								$output = array();
								foreach ($transition->getIncomingSteps() as $step)
								{
									$output[] = '<div class="workflow_browser_step_transition">'.link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), $step->getName())."</div>";
								}
								$glue = "<div class=\"faded_out\">".__('%a_workflow_step_transition% or %a_workflow_step_transition%', array('%a_workflow_step_transition%' => ''))."</div>";
								echo join($glue, $output);

								?>
							<?php endif; ?>
							<div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
							<div class="workflow_browser_step_name"><?php echo $transition->getName(); ?></div>
							<div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
							<div class="workflow_browser_step_transition"><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getOutgoingStep()->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></div>
						</div>
					</div>
					<br style="clear: both;">
					<div class="tab_menu" style="margin-top: 55px;">
						<ul id="transition_menu">
							<li class="selected" id="pre_validation_tab"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.tabSwitcher('pre_validation_tab', 'transition_menu');"><?php echo __('Pre-transition validation'); ?></a></li>
							<?php if ($transition->hasTemplate()): ?>
								<li id="post_validation_tab"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.tabSwitcher('post_validation_tab', 'transition_menu');"><?php echo __('Post-transition validation'); ?></a></li>
							<?php endif; ?>
							<li id="actions_tab"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.tabSwitcher('actions_tab', 'transition_menu');"><?php echo __('Post-transition actions'); ?></a></li>
						</ul>
					</div>
					<div id="transition_menu_panes" style="margin-bottom: 100px;">
						<div id="pre_validation_tab_pane">
							<div class="content" style="padding: 5px 0 10px 2px;">
								<?php echo __('The following validation rules has to be fullfilled for the transition to be available to the user'); ?>
							</div>
							<?php if (!$transition->isCore()): ?>
								<div class="rounded_box lightyellow" style="margin-bottom: 15px;">
									<form action="<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'pre')); ?>" onsubmit="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'pre')); ?>', 'pre');return false;" id="workflowtransitionprevalidationrule_add_form">
										<label for="workflowtransitionprevalidationrule_add_type"><?php echo __('Add pre transition validation rule'); ?></label>
										<select name="rule" id="workflowtransitionprevalidationrule_add_type">
											<option <?php if ($transition->hasPreValidationRule(TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionprevalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES; ?>" value="<?php echo TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES; ?>"><?php echo __('Max number of assigned issues'); ?></option>
										</select>
										<input type="submit" value="<?php echo __('Add pre transition validation rule'); ?>">
										<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionprevalidationrule_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
									</form>
								</div>
							<?php endif; ?>
							<table cellpadding="0" cellspacing="0" style="width: 100%;">
								<tbody class="hover_highlight" id="workflowtransitionprevalidationrules_list">
								<?php foreach ($transition->getPreValidationRules() as $rule): ?>
									<?php include_template('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
								<?php endforeach; ?>
								</tbody>
							</table>
							<span class="faded_out" id="no_workflowtransitionprevalidationrules"<?php if ($transition->hasPreValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no pre-validation rules'); ?></span>
						</div>
						<?php if ($transition->hasTemplate()): ?>
							<div id="post_validation_tab_pane" style="display: none;">
								<div class="content" style="padding: 5px 0 10px 2px;">
									<?php echo __('The following validation rules will be applied to the input given by the user in the transition view. If the validation fails, the transition will not take place.'); ?>
								</div>
								<?php if (!$transition->isCore()): ?>
									<div class="rounded_box lightyellow" style="margin-bottom: 15px;">
										<form action="<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post')); ?>" onsubmit="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post')); ?>', 'post');return false;" id="workflowtransitionpostvalidationrule_add_form">
											<label for="workflowtransitionpostvalidationrule_add_type"><?php echo __('Add post transition validation rule'); ?></label>
											<select name="rule" id="workflowtransitionpostvalidationrule_add_type">
												<option <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionpostvalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID; ?>" value="<?php echo TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID; ?>"><?php echo __('Validate specified priority'); ?></option>
												<option <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionpostvalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID; ?>" value="<?php echo TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID; ?>"><?php echo __('Validate specified reproducability'); ?></option>
												<option <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionpostvalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID; ?>" value="<?php echo TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID; ?>"><?php echo __('Validate specified resolution'); ?></option>
												<option <?php if ($transition->hasPostValidationRule(TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionpostvalidationrule_<?php echo TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID; ?>" value="<?php echo TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID; ?>"><?php echo __('Validate specified status'); ?></option>
											</select>
											<input type="submit" value="<?php echo __('Add post transition validation rule'); ?>">
											<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionpostvalidationrule_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
										</form>
									</div>
								<?php endif; ?>
								<table cellpadding="0" cellspacing="0" style="width: 100%;">
									<tbody class="hover_highlight" id="workflowtransitionpostvalidationrules_list">
									<?php foreach ($transition->getPostValidationRules() as $rule): ?>
										<?php include_template('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
									<?php endforeach; ?>
									</tbody>
								</table>
								<span class="faded_out" id="no_workflowtransitionpostvalidationrules"<?php if ($transition->hasPostValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no post validation rules'); ?></span>
							</div>
						<?php endif; ?>
						<div id="actions_tab_pane" style="display: none;">
							<div class="content" style="padding: 5px 0 10px 2px;">
								<?php echo __('The following actions will be applied to the issue during this transition.'); ?>
							</div>
							<?php if (!$transition->isCore()): ?>
								<div class="rounded_box lightyellow" style="margin-bottom: 15px;">
									<form action="<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())); ?>" onsubmit="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())); ?>');return false;" id="workflowtransitionaction_add_form">
										<label for="workflowtransitionaction_add_type"><?php echo __('Add transition action'); ?></label>
										<select name="action_type" id="workflowtransitionaction_add_type">
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE; ?>"><?php echo __('Assign the issue to a user'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF; ?>"><?php echo __('Assign the issue to the current user'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE; ?>"><?php echo __('Clear issue assignee'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PRIORITY; ?>"><?php echo __('Clear issue priority'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PRIORITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_PRIORITY; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_PRIORITY; ?>"><?php echo __('Set issue priority'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_PERCENT; ?>"><?php echo __('Clear issue percent completed'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_PERCENT)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_PERCENT; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_PERCENT; ?>"><?php echo __('Set issue percent completed'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY; ?>"><?php echo __('Clear issue reproducability'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY; ?>"><?php echo __('Set issue reproducability'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_RESOLUTION; ?>"><?php echo __('Clear issue resolution'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION; ?>"><?php echo __('Set issue resolution'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_CLEAR_DUPLICATE; ?>"><?php echo __('Mark as not duplicate'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_DUPLICATE; ?>"><?php echo __('Mark as duplicate'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_STATUS)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_STATUS; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_STATUS; ?>"><?php echo __('Set issue status'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_SET_MILESTONE)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_SET_MILESTONE; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_SET_MILESTONE; ?>"><?php echo __('Set issue milestone'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_USER_START_WORKING)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_USER_START_WORKING; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_USER_START_WORKING; ?>"><?php echo __('Mark issue as being worked on by the assigned user'); ?></option>
											<option <?php if ($transition->hasAction(TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING)): ?>style="display: none;"<?php endif; ?> id="add_workflowtransitionaction_<?php echo TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING; ?>" value="<?php echo TBGWorkflowTransitionAction::ACTION_USER_STOP_WORKING; ?>"><?php echo __('Mark issue as no longer being worked on, and optionally add time spent'); ?></option>
										</select>
										<input type="submit" value="<?php echo __('Add transition action'); ?>">
										<?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionaction_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
									</form>
								</div>
							<?php endif; ?>
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
					<div class="rounded_box red borderless" id="no_such_workflow_error">
						<div class="header"><?php echo $error; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>
