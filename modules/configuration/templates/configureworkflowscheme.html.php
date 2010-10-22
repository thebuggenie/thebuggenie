<?php

	if ($workflow_scheme instanceof TBGWorkflowScheme)
	{
		$tbg_response->setTitle(__('Configure workflow scheme "%workflow_scheme_name%"', array('%workflow_scheme_name%' => $workflow_scheme->getName())));
	}
	else
	{
		$tbg_response->setTitle(__('Configure workflow schemes'));
	}
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'workflow', 'workflow' => $workflow_scheme)); ?>
			<div class="content" style="width: 750px;" id="workflow_steps_container">
				<?php if ($workflow_scheme instanceof TBGWorkflowScheme): ?>
					<div class="rounded_box lightgrey workflow_steps_intro">
						<div class="header"><?php echo __('Workflow scheme "%workflow_scheme_name%"', array('%workflow_scheme_name%' => $workflow_scheme->getName())); ?></div>
						<div class="content">
							<?php echo __('This page shows all the issuetype / workflow associations for the selected workflow scheme'); ?>
						</div>
					</div>
					<table id="workflow_steps_list" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th><?php echo __('Issue type'); ?></th>
								<th><?php echo __('Associated workflow'); ?></th>
								<th><?php echo __('Actions'); ?></th>
							</tr>
						</thead>
						<tbody class="padded_table hover_highlight" id="workflow_steps_list_tbody">
							<?php foreach ($issuetypes as $issuetype): ?>
								<tr class="step">
									<td><?php echo $issuetype->getName(); ?></td>
									<td>
										<?php if ($workflow_scheme->hasWorkflowAssociatedWithIssuetype($issuetype)): ?>
											<?php echo link_tag(make_url('configure_workflow_steps', array('workflow_id' => $workflow_scheme->getWorkflowForIssuetype($issuetype)->getID())), $workflow_scheme->getWorkflowForIssuetype($issuetype)->getName()); ?></a>
										<?php else: ?>
											<span class="faded_out"><?php echo __('No workflow associated - will use "Default workflow"'); ?></span>
										<?php endif; ?>
									</td>
									<td>
										<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('class' => 'image')); ?>
									</td>
								</tr>
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