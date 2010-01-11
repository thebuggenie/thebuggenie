<div class="header"><?php echo __('Basic information'); ?></div>
<div class="content"><?php echo __('This is the basic settings for the wiki'); ?></div>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
<div class="rounded_box borderless" style="margin: 10px 0 0 0; width: 700px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 200px; padding: 5px;"><label for="publish_menu_title"><?php echo __('Menu title'); ?></label></td>
				<td>
					<select name="menu_title" id="publish_menu_title" style="width: 250px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>>
						<option value=5 <?php echo ($module->getSetting('menu_title') == 5) ? ' selected' : ''; ?>><?php echo __('Archive'); ?></option>
						<option value=3 <?php echo ($module->getSetting('menu_title') == 3) ? ' selected' : ''; ?>><?php echo __('Documentation'); ?></option>
						<option value=4 <?php echo ($module->getSetting('menu_title') == 4) ? ' selected' : ''; ?>><?php echo __('Documents'); ?></option>
						<option value=2 <?php echo ($module->getSetting('menu_title') == 2) ? ' selected' : ''; ?>><?php echo __('Help'); ?></option>
						<option value=1 <?php echo ($module->getSetting('menu_title') == 1 || $module->getSetting('menu_title') == 0) ? ' selected' : ''; ?>><?php echo __('Wiki'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('Specify here if you want to show a different menu title than "Wiki" in the header menu'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="allow_camelcase_links_yes"><?php echo __('Allow "CamelCased" links'); ?></label></td>
				<td>
					<input type="radio" name="allow_camelcase_links" value="1" id="allow_camelcase_links_yes"<?php if ($module->getSetting('allow_camelcase_links') == 1): ?> checked<?php endif; ?>>&nbsp;<label for="allow_camelcase_links_yes"><?php echo __('Yes'); ?></label><br>
					<input type="radio" name="allow_camelcase_links" value="0" id="allow_camelcase_links_no"<?php if ($module->getSetting('allow_camelcase_links') == 0): ?> checked<?php endif; ?>>&nbsp;<label for="allow_camelcase_links_no"><?php echo __('No'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('Traditionally, %CamelCasing% has been used to specify links between documents in Wikis. If you want to keep this turned on, specify so here. Make sure you read the %wikiformatting% wiki article if you are unsure how to use this feature.', array('%CamelCasing%' => link_tag('http://wikipedia.org/wiki/CamelCase', __('CamelCasing'), array('target' => '_blank')), '%wikiformatting%' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting', array('target' => '_blank')))); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
		</table>
	</div>
	<?php if ($access_level != configurationActions::ACCESS_FULL): ?>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	<?php endif; ?>
</div>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<div class="rounded_box iceblue_borderless" style="margin: 0 0 5px 0; width: 700px;">
		<div class="xboxcontent" style="padding: 8px 5px 2px 5px; height: 23px;">
			<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save wiki settings', array('%save%' => __('Save'))); ?></div>
			<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
<?php endif; ?>
</form>