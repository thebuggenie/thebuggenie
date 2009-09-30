<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			if (BUGScontext::getRequest()->getParameter('max_file_size'))
			{
				$max_file_size = (int) BUGScontext::getRequest()->getParameter('max_file_size', null, false);
				if ($max_file_size > (int) ini_get('upload_max_filesize')) $max_file_size = (int) ini_get('upload_max_filesize');
				BUGSsettings::saveSetting('max_file_size', $max_file_size);
			}
			if (BUGScontext::getRequest()->getParameter('enable_uploads'))
			{
				BUGSsettings::saveSetting('enable_uploads', BUGScontext::getRequest()->getParameter('enable_uploads'));
			}
			if (BUGScontext::getRequest()->getParameter('enable_guest_uploads'))
			{
				BUGSsettings::saveSetting('enable_guest_uploads', BUGScontext::getRequest()->getParameter('enable_guest_uploads'));
			}
			if (BUGScontext::getRequest()->getParameter('uploads_blacklist'))
			{
				BUGSsettings::saveSetting('uploads_blacklist', BUGScontext::getRequest()->getParameter('uploads_blacklist'));
			}
			if (BUGScontext::getRequest()->getParameter('uploads_filetypes'))
			{
				BUGSsettings::saveSetting('uploads_filetypes', BUGScontext::getRequest()->getParameter('uploads_filetypes'));
			}
		}
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure file upload settings'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('From here you can manage file uploads.') . __('To find out more about what each setting does, please refer to the %bugs_online_help%', array('%bugs_online_help%' => bugs_helpBrowserHelper('uploadsettings', 'The Bug Genie online help'))); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="3">
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Allow file uploads'); ?></b></td>
				<td style="width: 250px;">
					<select name="enable_uploads" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('enable_uploads') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, allow file uploads'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('enable_uploads') == 0) ? ' selected' : ''; ?>><?php echo __('No, don\'t allow file uploads'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Whether to allow attaching files to issue reports'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Allow guest uploads'); ?></b></td>
				<td style="width: 250px;">
					<select name="enable_guest_uploads" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('enable_guest_uploads') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, let anonymous users upload files'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('enable_guest_uploads') == 0) ? ' selected' : ''; ?>><?php echo __('No, only allow registered users to upload files'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Whether to allow anonymous file uploads'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Max file size'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="max_file_size" value="<?php echo BUGSsettings::get('max_file_size'); ?>" style="width: 25%; text-align: right;">&nbsp;<b>MB</b></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The max allowed file size for uploads') ?><br>(<i><?php echo __('Your PHP settings only allows max %php_upload_max_filesize%', array('%php_upload_max_filesize%' => ini_get('upload_max_filesize'))); ?>B. <a href="http://php.net/manual/en/ini.core.php#ini.upload-max-filesize" target="_blank"><?php echo __('More info'); ?></a></i>)</td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Limit file types'); ?></b></td>
				<td style="width: 250px;">
					<select name="uploads_blacklist" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('uploads_blacklist') == 1) ? ' selected' : ''; ?>><?php echo __('Blacklist - allow everything but these filetypes'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('uploads_blacklist') == 0) ? ' selected' : ''; ?>><?php echo __('Whitelist - allow nothing but these filetypes'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select how to limit filetypes'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('File types'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="uploads_filetypes" value="<?php echo BUGSsettings::get('uploads_filetypes'); ?>"></td>
				<td style="width: auto; padding: 5px;"><?php echo __('Enter a comma-separated list of extensions (ex: "%files%")', array('%files%' => '<i>exe, bat</i>')); ?><br><?php echo __('Scripts (php, asp, etc.) are never allowed'); ?></td>
			</tr>
			<?php if ($access_level == 'full'): ?>
				<tr>
					<td colspan=3 style="padding: 5px; text-align: right;"><input type="submit" value="<?php echo __('Save'); ?>"></td>
				</tr>
			<?php endif; ?>
		</table>
		</form>
		<?php
	}

?>