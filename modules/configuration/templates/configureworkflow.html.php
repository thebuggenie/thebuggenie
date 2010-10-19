<?php

	$tbg_response->setTitle(__('Configure workflows'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<div style="width: 750px;" id="config_workflows">
				<div class="config_header"><?php echo __('Configure workflows'); ?></div>
			</div>
		</td>
	</tr>
</table>