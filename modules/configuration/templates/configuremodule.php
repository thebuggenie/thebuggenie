<?php

	$bugs_response->setTitle(__('Configure modules'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php

include_component('configleftmenu', array('selected_section' => 15));

?>
<td valign="top">
	<div style="width: 750px;" id="config_modules">
		<div class="configheader"><?php echo __('Configure module %module_name%', array('%module_name%' => $module->getName())); ?></div>
	</div>
</td>
</tr>
</table>