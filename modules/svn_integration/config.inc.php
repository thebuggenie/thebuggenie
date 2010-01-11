<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			if (TBGContext::getRequest()->getParameter('svn_passkey'))
			{
				TBGContext::getModule('svn_integration')->saveSetting('svn_passkey', TBGContext::getRequest()->getParameter('svn_passkey'));
			}
			if (TBGContext::getRequest()->getParameter('use_web_interface'))
			{
				TBGContext::getModule('svn_integration')->saveSetting('use_web_interface', TBGContext::getRequest()->getParameter('use_web_interface'));
			}
			foreach (TBGProject::getAll() as $aProject)
			{
				if (TBGContext::getRequest()->getParameter('viewvc_path_' . $aProject['id']))
				{
					TBGContext::getModule('svn_integration')->saveSetting('viewvc_path_' . $aProject['id'], TBGContext::getRequest()->getParameter('viewvc_path_' . $aProject['id']));
				}
			}
		}
		
		$svn_passkey = TBGContext::getModule('svn_integration')->getSetting('svn_passkey');
		$use_web_interface = TBGContext::getModule('svn_integration')->getSetting('use_web_interface');

		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure SVN Integration module'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Setup the SVN integration module here. To be able to use SVN integration, you should read more about how to set up commit hooks in the SVN documentation.'); ?><br>
						<br>
						<b><?php echo __('Please read the included %svn_integration_online_help% for information on how to set this up.', array('%svn_integration_online_help%' => bugs_helpBrowserHelper('svn_integration/howto', __('SVN Integration online help')))); ?></b><br>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('Commit hook settings'); ?></div>
		<div style="padding: 5px; margin-bottom: 5px;"><?php echo __('The SVN integration module can respond both to a URL hit and via the included post-commit.sh script. Please specify here if you are using the included script, or if you will also use the URL-hit method.'); ?></div> 
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="svn_integration_form">
		<input type="hidden" name="module" value="svn_integration">
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Allow SVN updates via web'); ?></b></td>
				<td style="width: 250px;">
					<select name="use_web_interface" style="width: 100%;">
						<option value=0 <?php print ($use_web_interface == 0) ? 'selected' : ''; ?>><?php echo __('No, use the command line client'); ?></option>
						<option value=1 <?php print ($use_web_interface == 1) ? 'selected' : ''; ?>><?php echo __('Yes, use the web interface'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Choose whether to allow updating SVN information via web'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Passkey for SVN web update'); ?></b></td>
				<td style="width: 250px;">
					<input name="svn_passkey" style="width: 100%;" value="<?php echo $svn_passkey ?>">
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('A unique SVN passkey to be used when updating with SVN information through the web interface'); ?></td>
			</tr>
		</table>
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('ViewVC URL (repository locations)'); ?></div>
		<div style="padding: 5px; margin-bottom: 5px;"><?php echo __('The SVN integration module integrates with ViewVC for browsing repositories. ViewVC is a web-based repository browser, which you can read more about at %www.viewvc.org%.', array('%www.viewvc.org%' => '<a href="http://www.viewvc.org/" target="_blank">www.viewvc.org</a>')); ?><br>
		<br>
		<?php echo __('You can set up the ViewVC URL for each project here. The URL must be the top-level directory which contains the files you commit.'); ?><br><?php echo __('Even though you are not using the commit-hooks, you can still use ViewVC to browse the repository from the project overview.'); ?></div> 
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<?php

			if (count(TBGProject::getAll()) > 0)
			{
				foreach (TBGProject::getAll() as $aProject)
				{
					$aProject = TBGFactory::projectLab($aProject['id']);
					?>
					<tr>
						<td style="width: 125px; padding: 5px;"><b><?php echo $aProject->getName(); ?></b></td>
						<td style="width: 250px;">
							<input name="viewvc_path_<?php echo $aProject->getID(); ?>" style="width: 100%;" value="<?php echo TBGContext::getModule('svn_integration')->getSetting('viewvc_path_' . $aProject->getID()); ?>">
						</td>
						<td style="width: auto; padding: 5px;"><?php echo __('ViewVC URL for %project_name%', array('%project_name%' => $aProject->getName())); ?></td>
					</tr>
					<?php
				}
			}
			else
			{
				?><tr><td style="color: #AAA; padding: 5px; width: 375px;"><?php echo __('There are no projects'); ?></td><td>&nbsp;</td><td>&nbsp;</td></tr><?php
			}
			
			?>
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