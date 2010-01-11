<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		tbg_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			if (TBGContext::getRequest()->getParameter('viewmode') != null)
			{
				TBGContext::getModule('messages')->saveSetting('viewmode', TBGContext::getRequest()->getParameter('viewmode'));
			}
		}
		
		$default_viewmode = TBGContext::getModule('messages')->getSetting('viewmode'); // tbg_module_loadSetting('messages', 'viewmode');

		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure messaging module'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Set up the messaging module here.'); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="messages">
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Default view mode'); ?></b></td>
				<td style="width: 250px;">
					<select name="viewmode" style="width: 100%;">
						<option value=0 <?php print ($default_viewmode == 0) ? 'selected' : ''; ?>><?php echo __('Vertical (preview pane to the right)'); ?></option>
						<option value=1 <?php print ($default_viewmode == 1) ? 'selected' : ''; ?>><?php echo __('Classical (preview pane at the bottom)'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('The default view mode for the message list/preview pane'); ?></td>
			</tr>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;">&nbsp;</td>
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
