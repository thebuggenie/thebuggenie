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
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'workflow', 'workflow' => $workflow)); ?>
			<div class="content" style="width: 750px;" id="workflow_steps_container">
				<?php if ($workflow instanceof TBGWorkflow): ?>
					<div class="rounded_box lightgrey workflow_steps_intro">
						<div class="header"><?php echo __('Editing steps for %workflow_name%', array('%workflow_name%' => $workflow->getName())); ?></div>
						<div class="content">
							<?php echo __('This page shows all the available steps for the selected workflow, as well as transitions between these steps.'); ?>
							<?php echo __('You can add and remove steps from this page, as well as manage the transitions between them.'); ?>
						</div>
					</div>
					<table id="workflow_steps_list" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th><?php echo __('Step name'); ?></th>
								<th><?php echo __('Connected status'); ?></th>
								<th><?php echo __('Outgoing transitions'); ?></th>
								<th><?php echo __('Actions'); ?></th>
							</tr>
						</thead>
						<tbody class="padded_table hover_highlight" id="workflow_steps_list_tbody">
							<?php foreach ($workflow->getSteps() as $step): ?>
								<?php include_template('configuration/workflowstep', array('step' => $step)); ?>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php /*foreach ($workflow->getSteps() as $step): ?>
						$steps[] = array('name' => '<?php echo $step->getName(); ?>', 'description' => '<?php echo $step->getDescription(); ?>', 'status_id' => <?php echo ($step->hasLinkedStatus()) ? $step->getLinkedStatus()->getID() : 'null'; ?>, 'editable' => <?php echo ($step->isEditable()) ? 'true' : 'false'; ?>, 'is_closed' => <?php echo ($step->isClosed()) ? 'true' : 'false'; ?>);<br>
					<?php endforeach;*/ ?>
				<?php else: ?>
					<div class="rounded_box red borderless" id="no_such_workflow_error">
						<div class="header"><?php echo $error; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>