<div class="backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Change workflow'); ?>
	</div>
	<div id="backdrop_detail_content">
		<?php echo __("Issues in the workflow step on the left will have their workflow step changed to the one on the right. This will change the issue's status to the one assigned to the new step."); ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_workflow', array('project_id' => $project->getID())); ?>" method="post" id="workflow_form" onsubmit="$('update_workflow_indicator').show();return true;" enctype="multipart/form-data">
			<div class="change_workflow_table">
				<table cellpadding="0" cellspacing="0" class="padded_table">
					<tbody class="hover_highlight">
						<tr>
							<td style="width: 450px; padding-right: 10px;">
								<h4><?php echo __('Old: ').$project->getWorkflowScheme()->getName(); ?></h4>
							</td>
							<td style="width: 450px;">
								<h4><?php echo __('New: '); ?><select name="new_workflow">TO BE DONE</select></h4>
	
							</td>
						</tr>
						<?php foreach ($project->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
							<tr>
								<td><h5><?php echo $issuetype->getName().' - '.$project->getWorkflowScheme()->getWorkflowForIssuetype($issuetype)->getName(); ?></h5></td>
								<td><h5><?php echo $issuetype->getName(); ?></h5></td>
							</tr>
							<?php foreach ($project->getWorkflowScheme()->getWorkflowForIssuetype($issuetype)->getSteps() as $step): ?>
								<tr>
									<td><?php echo $step->getName(); ?></td>
									<td><select name="new_step_<?php echo $issuetype->getID(); ?>_<?php echo $step->getID(); ?>">TO BE DONE</select></td>
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
						<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation">
							<?php echo __('When you are done, click "%update_workflow%" to switch to the new workflow', array('%update_workflow%' => __('Update workflow'))); ?>
						</div>
						<div class="button button-green" style="float: right;">
							<input type="submit" value="<?php echo __('Update workflow'); ?>">
						</div>
						<span id="update_workflow_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="backdrop_detail_footer">
		<?php echo javascript_link_tag(__('Close popup'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')); ?>
	</div>
</div>