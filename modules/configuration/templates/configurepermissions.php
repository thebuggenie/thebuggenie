<?php

	$bugs_response->setTitle(__('Configure permissions'));
	//$bugs_response->addJavascript('config/issuetypes.js');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('configleftmenu', array('selected_section' => 6)); ?>
<td valign="top">
	<div style="width: 750px;" id="config_permissions">
		<div class="configheader"><?php echo __('Configure permissiosn'); ?></div>
		<div class="content"><?php echo __('Edit all global, group and team permissions from this page. User-specific permissions are handled from the user configuration page.'); ?></div>
		<div class="header_div" style="margin-top: 15px;"><?php echo __('General permissions'); ?></div>
		<ul style="width: 750px;">
			<?php foreach (BUGScontext::getAvailablePermissions('general') as $permission): ?>
				<li><a href="#"><?php echo image_tag('cfg_icon_permissions.png', array('style' => 'float: right;')); ?><?php echo $permission['description']; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</td>
</tr>
</table>