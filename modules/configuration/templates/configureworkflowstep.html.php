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
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'step', 'workflow' => $workflow, 'step' => $step)); ?>
			<div class="content" style="width: 750px;" id="workflow_step_container">
				<?php if ($step instanceof TBGWorkflowStep): ?>
					<div class="rounded_box lightgrey workflow_step_intro">
						<div class="header"><?php echo __('Workflow step "%step_name%"', array('%step_name%' => $step->getName())); ?></div>
						<div class="content">
							<?php echo __('This page shows all the available details for this step for the selected workflow, as well as transitions to and from this step.'); ?>
							<?php echo __('You can add and remove transitions from this page, as well as manage properties for this step.'); ?>
						</div>
					</div>
					<div id="workflow_details_step">
						<dl>
							<dt><?php echo __('Description'); ?></dt>
							<dd class="description"><?php echo $step->getDescription(); ?></dd>
							<dt><?php echo __('Connected status'); ?></dt>
							<dd class="description">
								<?php if ($step->hasLinkedStatus()): ?>
									<div class="workflow_step_status" style="background-color: <?php echo $step->getLinkedStatus()->getColor(); ?>;"> </div>
									<?php echo $step->getLinkedStatus()->getName(); ?>
								<?php else: ?>
									<span class="faded_out"><?php echo __('This step is not connected to a specific status'); ?></span>
								<?php endif; ?>
							</dd>
						</dl>
					</div>
					<div class="rounded_box lightyellow" id="workflow_browser_step">
						<div class="header"><?php echo __('Step path'); ?></div>
						<div class="content">
							<?php if ($workflow->getFirstStep() === $step): ?>
								<div class="workflow_browser_step_transition"><?php echo __('Issue is created'); ?><br><span class="faded_out"><?php echo __('This is the initial reporting step'); ?></span></div>
								<?php if ($step->getNumberOfIncomingTransitions() > 0): ?>
									<div class="faded_out"><?php echo __('%a_workflow_step_transition% or %a_workflow_step_transition%', array('%a_workflow_step_transition%' => '')); ?></div>
								<?php endif; ?>
							<?php elseif ($step->getNumberOfIncomingTransitions() == 0): ?>
								<div class="faded_out"><?php echo __("This step doesn't have any incoming transitions"); ?></div>
							<?php endif; ?>
							<?php if ($step->getNumberOfIncomingTransitions() > 0): ?>
								<?php

								$output = array();
								foreach ($step->getIncomingTransitions() as $transition)
								{
									$output[] = '<div class="workflow_browser_step_transition">'.link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName())."</div>";
								}
								$glue = "<div class=\"faded_out\">".__('%a_workflow_step_transition% or %a_workflow_step_transition%', array('%a_workflow_step_transition%' => ''))."</div>";
								echo join($glue, $output);

								?>
							<?php endif; ?>
							<div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
							<div class="workflow_browser_step_name"><?php echo $step->getName(); ?></div>
							<div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
							<?php if ($step->getNumberOfOutgoingTransitions() == 0): ?>
								<div class="faded_out"><?php echo __("This step doesn't have any outgoing transitions"); ?></div>
							<?php else: ?>
								<?php 
								
								$output = array();
								foreach ($step->getOutgoingTransitions() as $transition)
								{
									$output[] = '<div class="workflow_browser_step_transition">'.link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName())."</div>";
								}
								$glue = "<div class=\"faded_out\">".__('%a_workflow_step_transition% or %a_workflow_step_transition%', array('%a_workflow_step_transition%' => ''))."</div>";
								echo join($glue, $output);

								?>
							<?php endif; ?>
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