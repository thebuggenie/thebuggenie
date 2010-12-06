<div class="header"><?php echo __('Basic information'); ?></div>
<div class="content"><?php echo __('This is the basic settings for the wiki'); ?></div>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
<div class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
	<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: 200px; padding: 5px;"><label for="publish_menu_title"><?php echo __('Menu title'); ?></label></td>
			<td>
				<select name="menu_title" id="publish_menu_title" style="width: 250px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>>
					<option value=5 <?php echo ($module->getSetting('menu_title') == 5) ? ' selected' : ''; ?>><?php echo __('Project archive / Archive'); ?></option>
					<option value=3 <?php echo ($module->getSetting('menu_title') == 3) ? ' selected' : ''; ?>><?php echo __('Project documentation / Documentation'); ?></option>
					<option value=4 <?php echo ($module->getSetting('menu_title') == 4) ? ' selected' : ''; ?>><?php echo __('Project documents / Documents'); ?></option>
					<option value=2 <?php echo ($module->getSetting('menu_title') == 2) ? ' selected' : ''; ?>><?php echo __('Project help / Help'); ?></option>
					<option value=1 <?php echo ($module->getSetting('menu_title') == 1 || $module->getSetting('menu_title') == 0) ? ' selected' : ''; ?>><?php echo __('Project wiki / Wiki'); ?></option>
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
			<td style="padding: 5px;"><label for="hide_wiki_links_no"><?php echo __('Show "Wiki" links'); ?></label></td>
			<td>
				<input type="radio" name="hide_wiki_links" value="0" id="hide_wiki_links_no"<?php if ($module->getSetting('hide_wiki_links') != 1): ?> checked<?php endif; ?>>&nbsp;<label for="hide_wiki_links_no"><?php echo __('Yes'); ?></label><br>
				<input type="radio" name="hide_wiki_links" value="1" id="hide_wiki_links_yes"<?php if ($module->getSetting('hide_wiki_links') == 1): ?> checked<?php endif; ?>>&nbsp;<label for="hide_wiki_links_yes"><?php echo __('No'); ?></label>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2"><?php echo __('Setting this to "%yes%" will hide all "Wiki" tabs and links', array('%yes%' => __('Yes'))); ?></td>
		</tr>
		<tr>
			<td style="padding: 5px;"><label for="hide_wiki_links_no"><?php echo __('Wiki permissions'); ?></label></td>
			<td>
				<select name="free_edit">
					<option value="2" id="free_edit_everyone"<?php if ($module->getSetting('free_edit') == 2): ?> selected<?php endif; ?>><?php echo __('Open for everyone with access to add / remove content'); ?></label><br>
					<option value="1" id="free_edit_registered"<?php if ($module->getSetting('free_edit') == 1): ?> selected<?php endif; ?>><?php echo __('Only registered users can add / remove content'); ?></label>
					<option value="0" id="free_edit_permissions"<?php if ($module->getSetting('free_edit') == 0): ?> selected<?php endif; ?>><?php echo __('Set wiki permissions manually'); ?></label>
				</select>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2"><?php echo __('Specify how you want to control access to wiki editing functionality'); ?></td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
		</tr>
	</table>
</div>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
		<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save wiki settings', array('%save%' => __('Save'))); ?></div>
		<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
	</div>
<?php endif; ?>
</form>