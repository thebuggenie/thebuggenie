<?php

	$tbg_response->setTitle(__('Configure workflows'));
	$tbg_response->addJavascript('config/workflow.js')
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'workflows')); ?>
			<div class="content" style="width: 750px;">
				<ul class="scheme_list workflow_list simple_list" id="workflows_list">
					<?php foreach ($workflows as $workflow): ?>
						<?php include_template('configuration/workflow', array('workflow' => $workflow)); ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
</table>