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
					<?php if (!$workflow_scheme->isCore()): ?>
						<form action="<?php echo make_url('configure_workflow_scheme', array('scheme_id' => $workflow_scheme->getID())); ?>" onsubmit="TBG.Config.Workflows.Scheme.update('<?php echo make_url('configure_workflow_scheme', array('scheme_id' => $workflow_scheme->getID())); ?>', <?php echo $workflow_scheme->getID(); ?>); return false;" method="post" id="workflow_scheme_form">
					<?php endif; ?>
						<table id="workflow_steps_list" cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><?php echo __('Issue type'); ?></th>
									<th style="text-align: right;"><?php echo __('Associated workflow'); ?></th>
								</tr>
							</thead>
							<tbody class="padded_table hover_highlight" id="workflow_steps_list_tbody">
								<?php foreach ($issuetypes as $issuetype): ?>
									<tr class="step">
										<td><?php echo $issuetype->getName(); ?></td>
										<td style="text-align: right;">
											<?php if (!$workflow_scheme->isCore()): ?>
												<select name="workflow_id[<?php echo $issuetype->getID(); ?>]">
													<option value=""<?php if (!$workflow_scheme->hasWorkflowAssociatedWithIssuetype($issuetype)): ?> selected<?php endif; ?>><?php echo __('No workflow selected - will use default workflow'); ?></option>
													<?php foreach (TBGWorkflow::getAll() as $workflow): ?>
														<option value="<?php echo $workflow->getID(); ?>"<?php if ($workflow_scheme->hasWorkflowAssociatedWithIssuetype($issuetype) && $workflow_scheme->getWorkflowForIssuetype($issuetype)->getID() == $workflow->getID()): ?> selected<?php endif; ?>><?php echo $workflow->getName(); ?></option>
													<?php endforeach; ?>
												</select>
											<?php elseif ($workflow_scheme->hasWorkflowAssociatedWithIssuetype($issuetype)): ?>
												<?php echo $workflow_scheme->getWorkflowForIssuetype($issuetype)->getName(); ?>
											<?php else: ?>
												<span class="faded_out"><?php echo __('No workflow associated - will use "Default workflow"'); ?></span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php if (!$workflow_scheme->isCore()): ?>
						<div style="text-align: right; padding: 10px;">
							<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'workflow_scheme_indicator')); ?>
							<input type="submit" value="<?php echo __('Save workflow associations'); ?>">
						</div>
						</form>
					<?php endif; ?>
				<?php else: ?>
					<div class="rounded_box red borderless" id="no_such_workflow_error">
						<div class="header"><?php echo $error; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>