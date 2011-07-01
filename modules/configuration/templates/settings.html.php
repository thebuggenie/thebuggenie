<?php

	$tbg_response->setTitle(__('Configure settings'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('leftmenu', array('selected_section' => 12)); ?>
<td valign="top" style="padding-top: 10px;">
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_settings'); ?>" method="post" onsubmit="TBG.Main.Helpers.formSubmit('<?php echo make_url('configure_settings'); ?>', 'config_settings'); return false;" id="config_settings">
<?php endif; ?>
<div style="margin-top: 5px; width: 750px; clear: both; height: 30px;" class="tab_menu">
	<ul id="settings_menu">
		<li class="selected" id="tab_general_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_general_settings', 'settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General'); ?></a></li>
		<li id="tab_appearance_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_appearance_settings', 'settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_appearance.png', array('style' => 'float: left;')).__('Appearance'); ?></a></li>
		<li id="tab_reglang_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_reglang_settings', 'settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_reglang.png', array('style' => 'float: left;')).__('Regional &amp; language'); ?></a></li>
		<li id="tab_user_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_user_settings', 'settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_users.png', array('style' => 'float: left;')).__('Users &amp; security'); ?></a></li>
		<li id="tab_offline_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_offline_settings', 'settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_offline.png', array('style' => 'float: left;')).__('Maintenance mode'); ?></a></li>
	</ul>
</div>
<div id="settings_menu_panes">
	<div id="tab_general_settings_pane"><?php include_component('general', array('access_level' => $access_level)); ?></div>
	<div id="tab_appearance_settings_pane" style="display: none;"><?php include_component('appearance', array('access_level' => $access_level)); ?></div>
	<div id="tab_reglang_settings_pane" style="display: none;"><?php include_component('reglang', array('access_level' => $access_level)); ?></div>
	<div id="tab_user_settings_pane" style="display: none;"><?php include_component('user', array('access_level' => $access_level)); ?></div>
	<div id="tab_offline_settings_pane" style="display: none;"><?php include_component('offline', array('access_level' => $access_level)); ?></div>
</div>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="rounded_box mediumgrey" style="margin: 5px 0px 5px 0px; width: 725px; height: 25px; padding: 5px 10px 5px 10px;">
		<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your changes in all categories', array('%save%' => __('Save'))); ?></div>
		<input type="submit" id="config_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
		<span id="config_settings_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
	</div>
<?php endif; ?>
</form>
</td>
</tr>
</table>
