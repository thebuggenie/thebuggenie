<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			$settings_arr = array('enablebillboards', 'enableteambillboards', 'showbillboardmainheader', 'showbillboardteamheader', 'showlastarticlesonfrontpage');
			foreach ($settings_arr as $setting)
			{
				if (BUGScontext::getRequest()->getParameter($setting) == null)
				{
					BUGScontext::getModule('publish')->saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
				}
			}
		}
		
		$enable_billboards = BUGScontext::getModule('publish')->getSetting('enablebillboards');
		$enable_teambillboards = BUGScontext::getModule('publish')->getSetting('enableteambillboards');
		$show_billboardmainheader = BUGScontext::getModule('publish')->getSetting('showbillboardmainheader');
		$show_billboardteamheader = BUGScontext::getModule('publish')->getSetting('showbillboardteamheader');
		$showlastarticlesonfrontpage = BUGScontext::getModule('publish')->getSetting('showlastarticlesonfrontpage');

		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure publish module'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Set up the publishing module here, billboards and article.'); ?>
						<?php

						if (!$enable_billboards && $enable_teambillboards)
						{
							?><br>
							<br><b><?php echo __('Remember that billboards must be enabled before team billboards will be visible'); ?></b><?php
						}
						
						?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="publish">
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: 90%;"><?php echo __('Billboard settings'); ?></div>
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Articles on frontpage'); ?></b></td>
				<td style="width: 250px;">
					<select name="showlastarticlesonfrontpage" style="width: 100%;">
						<option value=0 <?php print ($showlastarticlesonfrontpage == 0) ? 'selected' : ''; ?>><?php echo __('No, don\'t show the last articles on the front page'); ?></option>
						<option value=1 <?php print ($showlastarticlesonfrontpage == 1) ? 'selected' : ''; ?>><?php echo __('Yes, show the last 3 articles on the front page'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('If enabled, this will show a short list with the last three articles on the frontpage'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Enable billboards'); ?></b></td>
				<td style="width: 250px;">
					<select name="enablebillboards" style="width: 100%;">
						<option value=0 <?php print ($enable_billboards == 0) ? 'selected' : ''; ?>><?php echo __('No, don\'t enable billboards'); ?></option>
						<option value=1 <?php print ($enable_billboards == 1) ? 'selected' : ''; ?>><?php echo __('Yes, enable billboards'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('The billboard is a place where people can post stuff'); ?></td>
			</tr>
			<?php /* ?>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Show billboard header'); ?></b></td>
				<td style="width: 250px;">
					<select name="showbillboardmainheader" style="width: 100%;">
						<option value=1 <?php print ($show_billboardmainheader == 1) ? 'selected' : ''; ?>><?php echo __('Yes, show the headers on the frontpage'); ?></option>
						<option value=0 <?php print ($show_billboardmainheader == 0) ? 'selected' : ''; ?>><?php echo __('No, don\'t show the headers on the frontpage'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Choose whether or not to display the header for the global billboard on the frontpage'); ?></td>
			</tr> */ ?>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Enable team billboards'); ?></b></td>
				<td style="width: 250px;">
					<select name="enableteambillboards" style="width: 100%;">
						<option value=0 <?php print ($enable_teambillboards == 0) ? 'selected' : ''; ?>><?php echo __('No, don\'t enable team billboards'); ?></option>
						<option value=1 <?php print ($enable_teambillboards == 1) ? 'selected' : ''; ?>><?php echo __('Yes, enable team billboards'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('The team billboards are accessible only by the team members'); ?></td>
			</tr>
			<?php /* ?>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Show team billboard headers'); ?></b></td>
				<td style="width: 250px;">
					<select name="showbillboardteamheader" style="width: 100%;">
						<option value=1 <?php print ($show_billboardteamheader == 1) ? 'selected' : ''; ?>><?php echo __('Yes, show the headers on the frontpage'); ?></option>
						<option value=0 <?php print ($show_billboardteamheader == 0) ? 'selected' : ''; ?>><?php echo __('No, don\'t show the headers on the frontpage'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Choose whether or not to display the headers for the team billboards on the frontpage'); ?></td>
			</tr> */ ?>
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
