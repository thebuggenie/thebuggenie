<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			if (BUGScontext::getRequest()->getParameter('url_host'))
			{
				BUGSsettings::saveSetting('url_host', BUGScontext::getRequest()->getParameter('url_host'));
			}
			if (BUGScontext::getRequest()->getParameter('url_subdir'))
			{
				BUGSsettings::saveSetting('url_subdir', BUGScontext::getRequest()->getParameter('url_subdir'));
			}
			if (BUGScontext::getRequest()->getParameter('local_path') && BUGScontext::getScope()->getID() == BUGSsettings::getDefaultScope()->getID())
			{
				BUGSsettings::saveSetting('local_path', BUGScontext::getRequest()->getParameter('local_path'));
			}
		}
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure server settings'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('From here you can manage The Bug Genie server settings.') . __('To find out more about what each setting does, please refer to the %bugs_online_help%', array('%bugs_online_help%' => bugs_helpBrowserHelper('serversettings', 'The Bug Genie online help'))); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="11">
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Server URL'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="url_host" value="<?php echo BUGSsettings::get('url_host'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The full url to the bug genie server, without the trailing slash.') ?><br>(<i><?php echo __('ex: http://localhost'); ?></i>)</td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('BUGS 2 subdirectory'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="url_subdir" value="<?php echo BUGSsettings::get('url_subdir'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The path from the server url root to The Bug Genie, including the trailing slash.'); ?><br>(ex: <i><?php echo __('/bugs2/'); ?></i>)</td>
			</tr>
			<?php if (BUGScontext::getScope()->getID() == BUGSsettings::getDefaultScope()->getID()): ?>
				<tr>
					<td style="width: 125px; padding: 5px;"><b><?php echo __('Local path'); ?></b></td>
					<td style="width: 250px;"><input type="text" name="local_path" value="<?php echo BUGSsettings::get('local_path'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
					<td style="width: auto; padding: 5px;"><?php echo __('The full local path to The Bug Genie, including the trailing slash.'); ?><br>(<?php echo __('always use forward slashes, even if installed on Windows-systems'); ?>)</td>
				</tr>
			<?php endif; ?>
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