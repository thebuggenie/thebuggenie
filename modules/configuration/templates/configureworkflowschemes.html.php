<?php

	$tbg_response->setTitle(__('Configure workflow schemes'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'schemes')); ?>
			<div class="content" style="width: 750px;">
				<ul class="workflow_list simple_list">
					<?php foreach ($schemes as $workflow_scheme): ?>
						<li id="workflow_<?php echo $workflow_scheme->getID(); ?>" class="rounded_box lightgrey">
							<table>
								<tr>
									<td class="workflow_info workflow_scheme">
										<div class="workflow_name"><?php echo $workflow_scheme->getName(); ?></div>
										<div class="workflow_description"><?php echo $workflow_scheme->getDescription(); ?></div>
									</td>
									<td class="workflow_scheme_issuetypes"><?php echo __('Issue types with associated workflows: %number_of_associated_workflows%', array('%number_of_associated_workflows%' => '<span>'.$workflow_scheme->getNumberOfAssociatedWorkflows().'</span>')); ?></td>
									<td class="workflow_actions">
										<?php echo __('Actions: %list%', array('%list%' => '')); ?><br>
										<a href="#" class="rounded_box"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this workflow scheme'))); ?></a>
										<a href="#" class="rounded_box"><?php echo image_tag('icon_copy.png', array('title' => __('Create a copy of this workflow scheme'))); ?></a>
										<?php echo link_tag(make_url('configure_workflow_scheme', array('scheme_id' => $workflow_scheme->getID())), image_tag('icon_workflow_list_steps.png', array('title' => __('Show / edit workflow steps'))), array('class' => 'rounded_box')); ?></a>
									</td>
								</tr>
							</table>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
</table>