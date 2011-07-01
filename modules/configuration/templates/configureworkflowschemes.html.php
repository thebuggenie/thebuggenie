<?php

	$tbg_response->setTitle(__('Configure workflow schemes'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'schemes')); ?>
			<div class="content" style="width: 750px;">
				<ul class="scheme_list workflow_list simple_list" id="workflow_schemes_list">
					<?php foreach ($schemes as $workflow_scheme): ?>
						<?php include_template('configuration/workflowscheme', array('scheme' => $workflow_scheme)); ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
</table>