<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
<div style="margin-top: 5px; width: 750px; clear: both; height: 30px;" class="tab_menu">
	<ul id="vcsintegration_settings_menu">
		<li class="selected" id="tab_general_settings"><a onclick="switchSubmenuTab('tab_general_settings', 'vcsintegration_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General settings'); ?></a></li>
		<li id="tab_project_settings"><a onclick="switchSubmenuTab('tab_project_settings', 'vcsintegration_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_projects.png', array('style' => 'float: left;')).__('Project settings'); ?></a></li>
	</ul>
</div>
<div id="vcsintegration_settings_menu_panes">
	<div id="tab_general_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == configurationActions::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == configurationActions::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="header"><?php echo __('General settings'); ?></div>
		<div class="content" style="padding-bottom: 10px;"><?php echo __('These are the settings that apply to all communications between The Bug Genie and any VCS, regardless of the project.'); ?></div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="vcsintegration_settings_table">
			<tr>
				<td style="padding: 5px;"><label for="use_web_interface"><?php echo __('Access method'); ?></label></td>
				<td>
					<select name="use_web_interface" id="use_web_interface" onchange="if ($(this).getValue() == 0) { $('vcs_passkey').disable(); } else { $('vcs_passkey').enable(); }">
						<option value="1"<?php if ($module->isUsingHTTPMethod()): ?> selected<?php endif; ?>><?php echo __('Use the HTTP access method'); ?></option>
						<option value="0"<?php if (!$module->isUsingHTTPMethod()): ?> selected<?php endif; ?>><?php echo __('Use the direct access method'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The Bug Genie can be notified of new commits by either a direct access call, or via HTTP. Select the method you wish to use here.'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="vcs_passkey"><?php echo __('Passkey for HTTP access'); ?></label></td>
				<td><input type="text" name="vcs_passkey" id="vcs_passkey" value="<?php echo $module->getSetting('vcs_passkey'); ?>" style="width: 100%;"<?php if (!$module->isUsingHTTPMethod()): ?> disabled="disabled"<?php endif; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('If the HTTP method has been chosen, a passkey must be entered so that malicious users can not add fake commit details.'); ?></td>
			</tr>
		</table>
	</div>
	<div id="tab_project_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == configurationActions::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; display: none; width: 700px;<?php if ($access_level == configurationActions::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="header"><?php echo __('Project settings'); ?></div>
		<div class="content" style="padding-bottom: 10px;"><?php echo __('These settings apply to each individual project.'); ?></div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="vcsintegration_settings_table">

		</table>
	</div>
</div>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
		<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save the settings on both tabs', array('%save%' => __('Save'))); ?></div>
		<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
	</div>
<?php endif; ?>
</form>