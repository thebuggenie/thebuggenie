<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 200px;"><label for="offline"><?php echo __('Enable maintenance mode'); ?></label></td>
		<td>
		<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
			<select name="offline" id="offline" style="width: 70px;">
				<option value=1<?php if (TBGSettings::isMaintenanceModeEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
				<option value=0<?php if (!TBGSettings::isMaintenanceModeEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
			</select>
		<?php else: ?>
			<?php echo (TBGSettings::isMaintenanceModeEnabled()) ? __('Yes') : __('No'); ?>
		<?php endif; ?>
		</td>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('In maintenance mode, access to The Bug Genie will be disabled, except for the Configuration pages. This allows you to perform upgrades and other maintance without interruption. Please remember that if you log out, you will be unable to log back in again whilst maintenace mode is enabled.'); ?></td>
	</tr>
	<tr>
		<td><label for="offline_msg"><?php echo __('Maintenance mode message'); ?></label></td>
		<td>
		<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
			<?php include_template('main/textarea', array('area_name' => 'offline_msg', 'area_id' => 'offline_msg', 'height' => '75px', 'width' => '100%', 'value' => TBGSettings::getMaintenanceMessage(), 'hide_hint' => true)); ?></td>
		<?php elseif (TBGSettings::hasMaintenanceMessage()): ?>
			<?php echo TBGSettings::getMaintenanceMessage(); ?>
		<?php else: ?>
			<span class="faded_out"><?php echo __('No message set'); ?></span>
		<?php endif; ?>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('The message you enter here will be displayed to users whilst in maintenance mode. If you do not enter anything, users will be shown a generic message.'); ?></td>
	</tr>
</table>
