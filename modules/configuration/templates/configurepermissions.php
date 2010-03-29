<?php

	$tbg_response->setTitle(__('Configure permissions'));
	$tbg_response->addJavascript('config/permissions.js');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('configleftmenu', array('selected_section' => 5)); ?>
		<td valign="top">
			<div style="width: 740px;" id="config_permissions">
				<div class="configheader"><?php echo __('Configure permissions'); ?></div>
				<div class="rounded_box borderless" style="margin: 5px 0px 10px 0px; width: 740px;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="text-align: left; min-height: 85px;">
						<div class="header_div smaller" style="clear: both; margin: 0 0 5px 0;"><?php echo __('Icon legend:'); ?></div>
						<div style="clear: both;">
							<?php echo image_tag('icon_project_permissions.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Show more detailed permissions for this permission group'); ?></span>
							<?php echo image_tag('cfg_icon_permissions.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Set permissions'); ?></span>
						</div>
						<div class="header_div smaller" style="clear: both; margin: 0 0 5px 0; padding-top: 10px;"><?php echo __('Permissions icon legend:'); ?></div>
						<div style="clear: both;">
							<?php echo image_tag('permission_unset_ok.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Not set (permissive system setting)'); ?></span>
							<?php echo image_tag('permission_unset_denied.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Not set (restrictive system setting)'); ?></span>
							<?php echo image_tag('permission_set_unset.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Not set (uses global permission)'); ?></span>
						</div>
						<div style="clear: both;">
							<?php echo image_tag('permission_set_ok.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Allowed'); ?></span>
							<?php echo image_tag('permission_set_denied.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Denied'); ?></span>
						</div>
						<div style="clear: both; padding: 10px 0 5px 5px;">
							<?php echo tbg_parse_text(__("Edit all global, group and team permissions from this page - user-specific permissions are handled from the user configuration page. The Bug Genie permissions are thoroughly explained in [[ConfigurePermissions]] in the wiki - look it up if you're ever stuck.")); ?>
						</div>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
				<div style="margin: 10px 0 0 0; clear: both; height: 30px;" class="tab_menu">
					<ul id="permissions_tabs">
						<li class="selected" id="tab_general"><a onclick="switchSubmenuTab('tab_general', 'permissions_tabs');" href="javascript:void(0);"><?php echo __('General permissions'); ?></a></li>
						<li id="tab_pages"><a onclick="switchSubmenuTab('tab_pages', 'permissions_tabs');" href="javascript:void(0);"><?php echo __('Page access permissions'); ?></a></li>
						<li id="tab_projects"><a onclick="switchSubmenuTab('tab_projects', 'permissions_tabs');" href="javascript:void(0);"><?php echo __('Project-specific permissions'); ?></a></li>
						<li id="tab_modules"><a onclick="switchSubmenuTab('tab_modules', 'permissions_tabs');" href="javascript:void(0);"><?php echo __('Module-specific permissions'); ?></a></li>
					</ul>
				</div>
				<div id="permissions_tabs_panes" class="permission_list">
					<?php include_template('configuration/permissionsconfigurator', array('access_level' => $access_level)); ?>
				</div>
			</div>
		</td>
	</tr>
</table>