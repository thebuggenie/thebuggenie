<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td><label for="theme_name"><?php echo __('Selected theme'); ?></label></td>
		<td>
			<select name="theme_name" id="theme_name" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach ($themes as $aTheme): ?>
				<option value="<?php echo $aTheme; ?>"<?php if (TBGSettings::getThemeName() == $aTheme): ?> selected<?php endif; ?><?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo $aTheme; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('The selected theme used. Depending on other settings, users might be able to use another theme for their account.'); ?></td>
	</tr>
	<tr>
		<td style="width: 200px;"><label for="user_themes"><?php echo __('Individual themes'); ?></label></td>
		<td style="width: auto;">
			<select name="user_themes" id="user_themes" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isUserThemesEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes, users can choose their preferred theme'); ?></option>
				<option value=0<?php if (!TBGSettings::isUserThemesEnabled()): ?> selected<?php endif; ?>><?php echo __('No, this theme will always be used'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Choose whether users can select a different theme than the default theme for their own account.'); ?></td>
	</tr>
	<tr>
		<td><label for="icon_header"><?php echo __('Custom header icon'); ?></label></td>
		<td>
			<select name="icon_header" id="icon_header" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value="0"<?php if (!TBGSettings::isUsingCustomHeaderIcon()): ?> selected<?php endif; ?><?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo __('Use the theme\'s default icon in the header'); ?></option>
				<option value="1"<?php if (TBGSettings::isUsingCustomHeaderIcon()): ?> selected<?php endif; ?><?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo __('Use a custom icon in the header'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('You can optionally use an alternative icon in the header. Select whether you want to do so here. If you choose to, you may need to upload a header icon, see below.'); ?></td>
	</tr>
	<tr>
		<td><label for="icon_fav"><?php echo __('Custom favicon'); ?></label></td>
		<td>
			<select name="icon_fav" id="icon_fav" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value="0"<?php if (!TBGSettings::isUsingCustomFavicon()): ?> selected<?php endif; ?><?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo __('Use the theme\'s default favicon'); ?></option>
				<option value="1"<?php if (TBGSettings::isUsingCustomFavicon()): ?> selected<?php endif; ?><?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo __('Use a custom favicon'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('You can optionally use an alternative favicon (the icon that appears next to the URL in your browser, as well as in your favourites). If you choose to do so, you may need to upload a favicon, see below.'); ?></td>
	</tr>
</table>
