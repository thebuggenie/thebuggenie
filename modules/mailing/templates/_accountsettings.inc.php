<table style="width: 895px; margin-bottom: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td valign="middle">
			<label for="notification_settings_preset_recommended"><?php echo __('Notification preset'); ?></label>
		</td>
		<td>
			<input type="radio" name="notification_settings_preset" value="silent"<?php if ($selected_preset == 'silent') echo ' checked'; ?> onchange="TBG.Main.Profile.toggleNotificationSettings('silent');" id="notification_settings_preset_silent">&nbsp;<label for="notification_settings_preset_silent"><?php echo __('Silent notification settings'); ?></label><br>
			<div class="faded_out"><?php echo __('We will hardly ever send you any notification emails. Check back regularly.'); ?></div>
			<input type="radio" name="notification_settings_preset" value="recommended"<?php if ($selected_preset == 'recommended') echo ' checked'; ?> onchange="TBG.Main.Profile.toggleNotificationSettings('recommended');" id="notification_settings_preset_recommended">&nbsp;<label for="notification_settings_preset_recommended"><?php echo __('Recommended notification settings'); ?></label><br>
			<div class="faded_out"><?php echo __("We will keep you updated when important stuff happens, but we'll keep quiet about less important stuff."); ?></div>
			<input type="radio" name="notification_settings_preset" value="verbose"<?php if ($selected_preset == 'verbose') echo ' checked'; ?> onchange="TBG.Main.Profile.toggleNotificationSettings('verbose');" id="notification_settings_preset_verbose">&nbsp;<label for="notification_settings_preset_verbose"><?php echo __('Verbose notification settings'); ?></label><br>
			<div class="faded_out"><?php echo __("If anything happens, you'll know. You should read up on email filters."); ?></div>
			<input type="radio" name="notification_settings_preset" value="custom"<?php if ($selected_preset == 'custom') echo ' checked'; ?> onchange="TBG.Main.Profile.toggleNotificationSettings('custom');" id="notification_settings_preset_custom">&nbsp;<label for="notification_settings_preset_custom"><?php echo __('Advanced settings'); ?></label><br>
			<div class="faded_out"><?php echo __("Pick and choose, mix or match - it's like an all-you-can-eat notification feast."); ?></div>
		</td>
	</tr>
</table>
<div id="notification_settings_selectors" <?php if ($selected_preset != 'custom'): ?>style="display: none;"<?php endif; ?>>
	<table style="width: 895px; margin-bottom: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
		<tr>
			<td style="border-bottom: 1px solid #CCC; font-size: 12px; font-weight: bold;"><?php echo __('Issues'); ?></td>
			<td style="width: 50px; text-align: center; border-bottom: 1px solid #CCC;">&nbsp;</td>
		</tr>
		<?php foreach ($issues_settings as $setting => $description): ?>
			<tr>
				<td style="width: auto; padding: 5px; border-bottom: 1px solid #DDD;"><label for="<?php echo $setting; ?>_yes" style="font-weight: normal;"><?php echo $description; ?></label></td>
				<td style="width: 50px; padding: 5px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
					<input type="checkbox" name="<?php echo $setting; ?>" value="1" id="<?php echo $setting; ?>_yes"<?php if ($module->getSetting($setting, $uid) == 1): ?> checked<?php endif; ?>>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>