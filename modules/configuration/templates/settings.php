<?php

	$bugs_response->setTitle(__('Configure settings'));
	
?>
<script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/settings.js"></script>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('configleftmenu', array('selected_section' => 12)); ?>
<td valign="top">
<div class="configheader" style="width: 750px;"><?php echo __('Configure settings'); ?></div>
<p style="padding-top: 5px;"><?php echo __('This section lets you configure all of the different settings in The Bug Genie'); ?>.<br>
<?php echo __('Click on a header to look at or change the settings in that category'); ?>.</p>
<div style="height: 60px; position: absolute;">
	<?php echo bugs_failureStrip('', '', 'message_failed', true); ?>
	<?php echo bugs_successStrip(__('Your changes has been saved'), '', 'message_changes_saved', true); ?>
</div>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_settings'); ?>" method="post" onsubmit="submitSettings('<?php echo make_url('configure_settings'); ?>'); return false;" id="config_settings">
<?php endif; ?>
<div style="margin-top: 30px; width: 750px; clear: both; height: 30px;" class="tab_menu">
	<ul>
		<li class="selected" id="tab_general_settings"><a onclick="switchTab('general');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General'); ?></a></li>
		<li id="tab_server_settings"><a onclick="switchTab('server');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_server.png', array('style' => 'float: left;')).__('Server'); ?></a></li>
		<li id="tab_reglang_settings"><a onclick="switchTab('reglang');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_reglang.png', array('style' => 'float: left;')).__('Regional &amp; language'); ?></a></li>
		<li id="tab_user_settings"><a onclick="switchTab('user');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_users.png', array('style' => 'float: left;')).__('Users &amp; security'); ?></a></li>
	</ul>
</div>
<div id="general_settings"><?php include_template('general', array('access_level' => $access_level, 'themes' => $themes)); ?></div>
<div id="server_settings" style="display: none;"><?php include_template('server', array('access_level' => $access_level)); ?></div>
<div id="reglang_settings" style="display: none;"><?php include_template('reglang', array('access_level' => $access_level, 'languages' => $languages)); ?></div>
<div id="user_settings" style="display: none;"><?php include_template('user', array('access_level' => $access_level)); ?></div>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<div class="rounded_box" style="margin: 5px 0px 5px 0px; width: 700px;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; height: 23px; padding: 5px 10px 5px 10px;">
			<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "Save" to save your changes in all categories'); ?></div>
			<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
			<span id="settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
<?php endif; ?>
</form>
</td>
</tr>
</table>