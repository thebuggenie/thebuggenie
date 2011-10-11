<?php

	$tbg_response->setTitle(__('Configure permissions'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => 5)); ?>
		<td valign="top">
			<div style="width: 740px;" id="config_permissions" class="config_permissions">
				<div class="config_header"><?php echo __('Configure permissions'); ?></div>
				<?php include_component('configuration/permissionsconfigurator', array('access_level' => $access_level, 'base_id' => 'configurator')); ?>
			</div>
		</td>
	</tr>
</table>