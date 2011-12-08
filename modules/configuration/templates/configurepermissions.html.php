<?php

	$tbg_response->setTitle(__('Configure permissions'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => 5)); ?>
		<td valign="top" style="padding-left: 15px;">
			<div style="width: 788px;">
				<h3><?php echo __('Configure permissions'); ?></h3>
				<div id="config_permissions" class="config_permissions">
					<?php include_component('configuration/permissionsconfigurator', array('access_level' => $access_level, 'base_id' => 'configurator')); ?>
				</div>
			</div>
		</td>
	</tr>
</table>