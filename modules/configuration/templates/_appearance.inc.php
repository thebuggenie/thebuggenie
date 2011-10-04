<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td><label for="theme_name"><?php echo __('Selected theme'); ?></label></td>
		<td>
			<select name="<?php echo TBGSettings::SETTING_THEME_NAME; ?>" id="theme_name" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach ($themes as $aTheme): ?>
				<option value="<?php echo $aTheme; ?>"<?php if (TBGSettings::getThemeName() == $aTheme): ?> selected<?php endif; ?><?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo $aTheme; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Themes provide the look and feel of The Bug Genie, other than the icons. Therefore, changing the theme will change the colours, fonts and layout of your installation'); ?></td>
	</tr>
	<tr>
		<td><label for="theme_name"><?php echo __('Selected iconset'); ?></label></td>
		<td>
			<select name="<?php echo TBGSettings::SETTING_ICONSET; ?>" id="iconset" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach ($icons as $anIcon): ?>
				<option value="<?php echo $anIcon; ?>"<?php if (TBGSettings::getIconsetName() == $anIcon): ?> selected<?php endif; ?><?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo $anIcon; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('An iconset contains all the icons used in The Bug Genie. You can change the icons to be used using this option'); ?></td>
	</tr>
	<tr>
		<td>
			<label><?php echo __('Custom header and favicons'); ?></label>
		</td>
		<td>
			<div class="button button-blue" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_icons')); ?>');"><span><?php echo __('Configure icons'); ?></span></div>
		</td>
	</tr>
	<tr>
		<td><label for="header_link"><?php echo __('Custom header link'); ?></label></td>
		<td>
			<input type="text" name="<?php echo TBGSettings::SETTING_HEADER_LINK; ?>" id="header_link" value="<?php echo TBGSettings::getHeaderLink(); ?>" style="width: 100%;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('You can alter the webpage that clicking on the header icon navigates to. If left blank it will link to the main page of this installation.'); ?></td>
	</tr>
</table>
