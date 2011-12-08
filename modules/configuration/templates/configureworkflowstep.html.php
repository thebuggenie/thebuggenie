<?php

	if ($step instanceof TBGWorkflowStep)
	{
		$tbg_response->setTitle(__('Configure workflow step "%step_name%"', array('%step_name%' => $step->getName())));
		$glue = '<div class="faded_out" style="clear: both;">'.__('%a_workflow_step_transition% or %a_workflow_step_transition%', array('%a_workflow_step_transition%' => '')).'</div>';
	}
	else
	{
		$tbg_response->setTitle(__('Configure workflows'));
	}
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top" style="padding-left: 15px;">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'step', 'workflow' => $workflow, 'step' => $step)); ?>
			<div class="content" style="width: 788px;" id="workflow_step_container">
				<?php if ($step instanceof TBGWorkflowStep): ?>
					<div class="rounded_box lightgrey borderless workflow_step_intro">
						<div class="header"><?php echo __('Workflow step "%step_name%"', array('%step_name%' => $step->getName())); ?></div>
						<div class="content">
							<?php echo __('This page shows all the available details for this step for the selected workflow, as well as transitions to and from this step.'); ?>
							<?php echo __('You can add and remove transitions from this page, as well as manage properties for this step.'); ?><br>
							<?php if (!$step->isCore()): ?>
								<br>
								<b><?php echo javascript_link_tag(__('Edit this step'), array('onclick' => "\$('step_details_form').toggle();\$('step_details_info').toggle();")); ?></b><br>
								<b><?php echo javascript_link_tag(__('Add outgoing transition'), array('onclick' => "$('step_{$step->getID()}_transition_add').toggle()")); ?></b>
							<?php endif; ?>
						</div>
					</div>
					<?php include_template('configuration/workflowaddtransition', array('step' => $step)); ?>
					<div id="workflow_details_step">
						<dl id="step_details_info">
							<dt><?php echo __('Description'); ?></dt>
							<dd class="description"><?php echo $step->getDescription(); ?></dd>
							<dt><?php echo __('State'); ?></dt>
							<dd>
								<?php if (!$step->isClosed()): ?>
									<?php echo ($step->isEditable()) ? __('Open and editable') : __('Open, but not editable'); ?>
								<?php else: ?>
									<?php echo ($step->isEditable()) ? __('Closed, but editable') : __('Closed and not editable'); ?>
								<?php endif; ?>
							</dd>
							<dt><?php echo __('Connected status'); ?></dt>
							<dd>
								<?php if ($step->hasLinkedStatus()): ?>
									<div class="workflow_step_status" style="background-color: <?php echo $step->getLinkedStatus()->getColor(); ?>;"> </div>
									<?php echo $step->getLinkedStatus()->getName(); ?>
								<?php else: ?>
									<span class="faded_out"><?php echo __('This step is not connected to a specific status'); ?></span>
								<?php endif; ?>
							</dd>
						</dl>
						<?php if (!$step->isCore()): ?>
							<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID(), 'mode' => 'edit')); ?>" id="step_details_form" style="display: none;" onsubmit="$('step_update_indicator').show();$('update_step_buttons').hide();">
								<dl>
									<dt><label for="step_name"><?php echo __('Name'); ?></label></dt>
									<dd><input type="text" name="name" id="step_name" value="<?php echo $step->getName(); ?>" style="width: 150px;"></dd>
									<dt><label for="step_description"><?php echo __('Description'); ?></label></dt>
									<dd><input type="text" name="description" id="step_description" value="<?php echo $step->getDescription(); ?>" style="width: 250px;"></dd>
									<dt><label for="step_state"><?php echo __('State'); ?></label></dt>
									<dd>
										<select name="state" id="step_state" style="width: 125px;">
											<option value="<?php echo TBGIssue::STATE_OPEN; ?>"<?php if (!$step->isClosed()) echo " selected"; ?>><?php echo __('Open'); ?></option>
											<option value="<?php echo TBGIssue::STATE_CLOSED; ?>"<?php if ($step->isClosed()) echo " selected"; ?>><?php echo __('Closed'); ?></option>
										</select>
										<select name="is_editable" id="step_editable" style="width: 125px;">
											<option value="1"<?php if ($step->isEditable()) echo " selected"; ?>><?php echo __('Editable'); ?></option>
											<option value="0"<?php if (!$step->isEditable()) echo " selected"; ?>><?php echo __('Not editable'); ?></option>
										</select>
									</dd>
									<dt><label for="step_status"><?php echo __('Connected status'); ?></label></dt>
									<dd>
										<select name="status_id" id="step_status">
											<option value="0"<?php if (!$step->hasLinkedStatus()) echo " selected"; ?>><?php echo __('Not connected to a status'); ?></option>
											<?php foreach (TBGStatus::getAll() as $status): ?>
											<option value="<?php echo $status->getID(); ?>"<?php if ($step->hasLinkedStatus() && $step->getLinkedStatus()->getID() == $status->getID()) echo " selected"; ?>><?php echo $status->getName(); ?></option>
											<?php endforeach; ?>
										</select>
									</dd>
								</dl>
								<br style="clear: both;">
								<div style="text-align: right; clear: both; padding: 10px 0 0 0;" id="update_step_buttons">
									<input type="submit" value="<?php echo __('Update step details'); ?>" name="edit">
									<?php echo __('%update_step_details% or %cancel%', array('%update_step_details%' => '', '%cancel%' => '')); ?>
									<b><?php echo javascript_link_tag(__('cancel'), array('onclick' => "\$('step_details_form').toggle();\$('step_details_info').toggle();")); ?></b>
								</div>
								<div style="text-align: right; padding: 10px 0 10px 0; display: none;" id="step_update_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
							</form>
						<?php endif; ?>
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
									$output[] = get_template_html('configuration/workflowtransition', array('transition' => $transition, 'direction' => 'incoming'));
								}
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
									$output[] = get_template_html('configuration/workflowtransition', array('transition' => $transition, 'direction' => 'outgoing'));
								}
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