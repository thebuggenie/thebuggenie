<h3><?php echo __('Email notifications'); ?></h3>
<p><?php echo __('In addition to being notified when logging in, you can choose to also be notified via email for issues or articles you subscribe to. The following settings control when you receive emails.'); ?></p>
<table style="width: 885px; margin-top: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
	<?php foreach ($notificationsettings as $key => $description): ?>
		<tr>
			<td style="width: auto; padding: 5px; border-bottom: 1px solid #DDD;"><label for="<?php echo $key; ?>_yes" style="font-weight: normal;"><?php echo $description ?></label></td>
			<td style="width: 50px; padding: 5px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
				<input type="checkbox" name="<?php echo $key; ?>" value="1" id="<?php echo $key; ?>_yes"<?php if (TBGSettings::getUserSetting($tbg_user->getID(), $key, 'mailing')): ?> checked<?php endif; ?>>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
