			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" onsubmit="TBG.Project.workflow('<?php echo make_url('configure_projects_workflow', array('project_id' => $project->getID())); ?>');return false;" action="<?php echo make_url('configure_projects_workflow', array('project_id' => $project->getID())); ?>" method="post" id="workflow_form2" enctype="multipart/form-data">
				<div class="workflow_change_div">
					<table cellpadding="0" cellspacing="0" class="padded_table">
						<tbody class="hover_highlight">
							<tr>
								<td style="width: 450px; padding-right: 10px;">
									<h4><?php echo __('Old: ').$project->getWorkflowScheme()->getName(); ?></h4>
								</td>
								<td style="width: 450px;">
									<h4><?php echo __('New: ').$new_workflow->getName(); ?></h4>
									<a href="javascript:void();" onclick="$('change_workflow_table').hide(); $('change_workflow_box').show();"><?php echo __('Choose a different one'); ?></a>
								</td>
							</tr>
							<?php foreach ($project->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
								<tr>
									<td><h5><?php echo $issuetype->getName().' - '.$project->getWorkflowScheme()->getWorkflowForIssuetype($issuetype)->getName(); ?></h5></td>
									<td><h5><?php echo $issuetype->getName().' - '.$new_workflow->getWorkflowForIssuetype($issuetype)->getName(); ?></h5></td>
								</tr>
								<?php foreach ($project->getWorkflowScheme()->getWorkflowForIssuetype($issuetype)->getSteps() as $step): ?>
									<tr>
										<td><?php echo $step->getName(); ?></td>
										<td>
											<select style="width: 100%" name="new_step_<?php echo $issuetype->getID(); ?>_<?php echo $step->getID(); ?>">
												<?php foreach ($new_workflow->getWorkflowForIssuetype($issuetype)->getSteps() as $new_step): ?>
													<option value="<?php echo $new_step->getID(); ?>"><?php echo $new_step->getName(); ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<table style="clear: both; width: 100%;" class="padded_table" cellpadding=0 cellspacing=0>
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