<?php

	if ($workflow instanceof TBGWorkflow)
	{
		$tbg_response->setTitle(__('Configure workflow "%workflow_name%"', array('%workflow_name%' => $workflow->getName())));
		$tbg_response->addJavascript('config/workflow.js');
	}
	else
	{
		$tbg_response->setTitle(__('Configure workflows'));
	}
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'transition', 'workflow' => $workflow, 'transition' => $transition)); ?>
			<div class="content" style="width: 750px;" id="workflow_step_container">
				<?php if ($transition instanceof TBGWorkflowTransition): ?>
					<div class="rounded_box lightgrey workflow_step_intro">
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
					<br style="clear: both;">
					<div class="tab_menu">
						<ul id="transition_menu">
							<li class="selected" id="validation_tab"><a href="javascript:void(0);" onclick="switchSubmenuTab('validation_tab', 'transition_menu');"><?php echo __('Pre-transition validation'); ?></a></li>
							<li id="actions_tab"><a href="javascript:void(0);" onclick="switchSubmenuTab('actions_tab', 'transition_menu');"><?php echo __('Post-transition actions'); ?></a></li>
						</ul>
					</div>
					<div id="transition_menu_panes">
						<div id="validation_tab_menu">
							<div class="content" style="padding: 5px 0 10px 2px;">
								<?php echo __('The following validation rules has to be fullfilled for the transition to be available to the user'); ?>
							</div>
							<?php if ($transition->hasValidationRules()): ?>
								<table cellpadding="0" cellspacing="0" style="width: 100%;">
									<tbody class="hover_highlight">
									<?php foreach ($transition->getValidationRules() as $rule): ?>
										<?php include_template('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
									<?php endforeach; ?>
									</tbody>
								</table>
							<?php else: ?>
								<span class="faded_out"><?php echo __('This transition has no validation rules'); ?></span>
							<?php endif; ?>
						</div>
						<div id="actions_tab_menu">
							
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