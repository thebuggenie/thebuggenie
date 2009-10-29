<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 200px;"><label for="b2_name"><?php echo __('The Bug Genie custom name'); ?></label></td>
		<td style="width: auto;"><input type="text" name="b2_name" id="b2_name" value="<?php echo str_replace('"', '&quot;', BUGSsettings::getTBGname()); ?>" style="width: 100%;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>></td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('This is the name appearing in the headers and several other places, usually displaying "The Bug Genie"'); ?> <i>(<?php echo __('HTML allowed'); ?>)</i></td>
	</tr>
	<tr>
		<td><label for="b2_tagline"><?php echo __('Tagline / slogan'); ?></label></td>
		<td><input type="text" name="b2_tagline" id="b2_tagline" value="<?php echo str_replace('"', '&quot;', BUGSsettings::getTBGtagline()); ?>" style="width: 100%;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>></td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('This will appear beneath the name in the header on all pages'); ?> <i>(<?php echo __('HTML allowed'); ?>)</i></td>
	</tr>
	<tr>
		<td><label for="singleprojecttracker"><?php echo __('Single project tracker mode'); ?></label></td>
		<td>
			<select name="singleprojecttracker" id="singleprojecttracker" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isSingleProjectTracker()): ?> selected<?php endif; ?>><?php echo __('Behave as tracker for a single project'); ?></option>
				<option value=0<?php if (!BUGSsettings::isSingleProjectTracker()): ?> selected<?php endif; ?>><?php echo __('No, use regular index page'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2">
			<?php echo __('In single project tracker mode, The Bug Genie will display the homepage for the first project as the main page instead of the regular index page'); ?><br>
			<?php if (count(BUGSproject::getAll()) > 1): ?>
				<br>
				<b class="more_than_one_project_warning"><?php echo __('More than one project exists. When in "single project" mode, accessing other projects than the first will become harder.'); ?></b>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="showprojectsoverview"><?php echo __('Show projects overview'); ?></label></td>
		<td>
			<select name="showprojectsoverview" id="showprojectsoverview" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isProjectOverviewEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes, show on the frontpage'); ?></option>
				<option value=0<?php if (!BUGSsettings::isProjectOverviewEnabled()): ?> selected<?php endif; ?>><?php echo __('No, don\'t show'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Whether the project overview list should appear on the frontpage or not'); ?></td>
	</tr>
	<tr>
		<td><label for="theme_name"><?php echo __('Selected theme'); ?></label></td>
		<td>
			<select name="theme_name" id="theme_name" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach ($themes as $aTheme): ?>
				<option value="<?php echo $aTheme; ?>"<?php if (BUGSsettings::getThemeName() == $aTheme): ?> selected<?php endif; ?><?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo $aTheme; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('The selected theme used. Depending on other settings, users might be able to use another theme for their account.'); ?></td>
	</tr>
	<tr>
		<td><label for="cleancomments"><?php echo __('Comment trail'); ?></label></td>
		<td>
			<select name="cleancomments" id="cleancomments" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isCommentTrailClean()): ?> selected<?php endif; ?>><?php echo __('Don\'t post system comments when an issue is updated'); ?></option>
				<option value=0<?php if (!BUGSsettings::isCommentTrailClean()): ?> selected<?php endif; ?>><?php echo __('Always post comments when an issue is updated'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('To keep the comment trail clean in issues, you can select not to post system comments when an issue is updated.'); ?><br>(<?php echo __('The issue log will always be updated regardless of this setting.'); ?>)</td>
	</tr>
	<tr>
		<td><label for="showloginbox"><?php echo __('Show login links on front page'); ?></label></td>
		<td>
			<select name="showloginbox" id="showloginbox" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::showLoginBox()): ?> selected<?php endif; ?>><?php echo __('Yes, show a login box at the left hand side'); ?></option>
				<option value=0<?php if (!BUGSsettings::showLoginBox()): ?> selected<?php endif; ?>><?php echo __('No, only show in the menu'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Choose whether to display the login box on the front page or not'); ?></td>
	</tr>
</table>