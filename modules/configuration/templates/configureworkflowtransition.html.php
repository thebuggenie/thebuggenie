<?php

	if ($workflow instanceof TBGWorkflow)
	{
		$tbg_response->setTitle(__('Configure workflow "%workflow_name%"', array('%workflow_name%' => $workflow->getName())));
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
							<?php echo __('You can edit all details about the selected transitions from this page.'); ?>
						</div>
					</div>
					<div id="workflow_details_transition">
						<dl>
							<dt><?php echo __('Description'); ?></dt>
							<dd class="description"><?php echo $transition->getDescription(); ?></dd>
							<dt><?php echo __('Template'); ?></dt>
							<dd><?php echo ($transition->hasTemplate()) ? $transition->getTemplateName() : __('No template used - transition happens instantly'); ?></dd>
							<dt><?php echo __('Outgoing step'); ?></dt>
							<dd><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getOutgoingStep()->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></dd>
						</dl>
					</div>
					<div class="rounded_box lightyellow" id="workflow_browser_step">
						<div class="header"><?php echo __('Transition path'); ?></div>
						<div class="content">
							<?php if ($transition->getNumberOfIncomingSteps() == 0): ?>
								<div class="faded_out"><?php echo __("This step doesn't have any incoming transitions"); ?></div>
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
				<?php else: ?>
					<div class="rounded_box red borderless" id="no_such_workflow_error">
						<div class="header"><?php echo $error; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>