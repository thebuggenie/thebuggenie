<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		tbg_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		require 'teamgroups_logic.inc.php';
		if (!TBGContext::getRequest()->isAjaxCall())
		{
			?><script type="text/javascript" src="<?php echo TBGContext::getTBGPath(); ?>js/config/teamgroups_ajax.js"></script><?php
			?>
			<table style="width: 100%" cellpadding=0 cellspacing=0>
				<tr>
					<td style="padding-right: 10px;">
						<table class="configstrip" cellpadding=0 cellspacing=0>
							<tr>
								<td class="cleft"><b><?php echo __('Configure teams &amp; groups'); ?></b></td>
								<td class="cright">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=2 class="cdesc">
								<?php echo __('From here you can manage available teams and groups, as well as their permissions.'); ?><br><b><?php echo __('Please read more about applying permissions in the %tbg_online_help% before using this section.', array('%tbg_online_help%' => tbg_helpBrowserHelper('permissions', 'The Bug Genie online help'))); ?></b>
								<?php
								
								if ($access_level == "full")
								{
									if ($theGroup instanceof TBGGroup && TBGContext::getRequest()->getParameter('remove') && !TBGContext::getRequest()->getParameter('confirm'))
									{
										if ($theGroup->getID() == TBGContext::getUser()->getGroup()->getID())
										{
											?>
											<div class="e_div" style="width: 400px; background: #FFF;">
											<?php echo __('You can not remove the "%group_name%" group', array('%group_name%' => $theGroup->getName())); ?><br>
											<?php echo __('You are a member of this group, and can therefore not remove it. You must change your user settings before you can remove this group.'); ?>
											</div>
											<?php
										}
										elseif (TBGSettings::get('defaultgroup') == $theGroup->getID())
										{
											?>
											<div class="e_div" style="width: 400px; background: #FFF;">
											<?php echo __('You can not remove the "%group_name%" group', array('%group_name%' => $theGroup->getName())); ?><br>
											<?php echo __('This group is the default user group and can not be removed.'); ?>&nbsp;<?php echo __('Please change the default user group in %general_settings% before removing this group.', array('%general_settings%' => '<a href="config.php?module=core&amp;section=12"><b>' . __('General settings') . '</b></a>')); ?>
											</div>
											<?php
										}
										elseif (!TBGContext::getRequest()->getParameter('confirm'))
										{
											?>
											<div class="w_div" style="width: 450px; background: #FFF;">
											<?php echo __('Are you sure you want to remove the "%group_name%" group?', array('%group_name%' => $theGroup->getName())); ?><br><br>
											<?php echo __('This action cannot be reversed. Also remember that all users in this group will be disabled until they are moved to another group.'); ?>
											<div style="font-weight: normal; text-align: right;"><a href="config.php?module=core&amp;section=1&amp;group=<?php print $theGroup->getID(); ?>&amp;remove=true&amp;confirm=true"><?php echo __('Yes'); ?></a> | <a href="config.php?module=core&amp;section=1"><b><?php echo __('No'); ?></b></a></div>
											</div>
											<?php
										}
									}
									elseif ($theTeam instanceof TBGTeam && TBGContext::getRequest()->getParameter('remove') && !TBGContext::getRequest()->getParameter('confirm'))
									{
										?>
										<div class="w_div" style="width: 450px; background: #FFF;">
										<?php echo __('Are you sure you want to remove the "%team_name%" team?', array('%team_name%' => $theTeam->getName())); ?><br><br><br>
										<?php echo __('This action cannot be reversed. All users in this will be removed from the team.'); ?>
										<div style="font-weight: normal; text-align: right;"><a href="config.php?module=core&amp;section=1&amp;team=<?php print $theTeam->getID(); ?>&amp;remove=true&amp;confirm=true"><?php echo __('Yes'); ?></a> | <a href="config.php?module=core&amp;section=1"><b><?php echo __('No'); ?></b></a></div>
										</div>
										<?php
									}
								}
	
								?>
								</td>
							</tr>
						</table>
						<table style="width: 100%;" cellpadding=0 cellspacing=0>
							<tr>
								<td style="padding: 5px; width: 210px;" valign="top">
									<div style="border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('Available groups'); ?></div>
									<div style="padding: 3px; padding-left: 0px; margin-bottom: 15px;">
									<table class="grouplist" style="width: 100%; table-layout: auto; margin-top: 0px;" cellpadding=0 cellspacing=0>
									<tr<?php (TBGContext::getRequest()->hasParameter('group') && TBGContext::getRequest()->getParameter('group') < 1) ? print " class=\"g_marked\"" : print ""; ?>>
										<td style="width: auto;" valign="middle"><a href="config.php?module=core&amp;section=1&amp;group=0"><?php echo __('Everyone'); ?></a></td>
										<td style="width: 18px; padding: 0px; text-align: center;">&nbsp;</td>
									</tr>
									</table>
									<div id="group_list">
									<?php
	
									$include_table = true;
									foreach (TBGGroup::getAll() as $aGroup)
									{
										require TBGContext::getIncludePath() . 'include/config/teamgroups_groupbox.inc.php';
									}
									
									?>
									</div>
									<?php
									
									if ($access_level == "full")
									{
										?>
										<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" id="add_group_form" onsubmit="return false;">
										<input type="hidden" name="addgroup" value="true">
										<table class="grouplist" style="width: 100%; table-layout: auto; margin-top: 0px;" cellpadding=0 cellspacing=0>
										<tr>
											<td style="width: auto;" valign="middle"><input type="text" name="groupname" style="width: 155px;"></td>
											<td style="width: 18px; padding: 0px; text-align: center;"><?php echo image_submit_tag('icon_plus_small.png', 'onclick="addGroup();"', __('Add group'), __('Add group'), '', 0, 16, 16); ?></td>
										</tr>
										</table>
										</form>
										<?php
									}
	
									?>
									</div>
									<div style="border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('Available teams'); ?></div>
									<div style="padding: 3px; padding-left: 0px; margin-bottom: 15px;">
									<div id="team_list">
									<?php
	
									$include_table = true;
									foreach (TBGTeam::getAll() as $aTeam)
									{
										$aTeam = new TBGTeam($aTeam['id']);
										require TBGContext::getIncludePath() . 'include/config/teamgroups_teambox.inc.php';
									}
									
									?>
									</div>
									<?php
	
									if (count(TBGTeam::getAll()) == 0)
									{
										?>
										<table class="teamlist" style="width: 100%; table-layout: auto; margin-top: 0px;" cellpadding=0 cellspacing=0>
										<tr>
										<td style="color: #AAA;"><?php echo __('There are no teams'); ?></td>
										</tr>
										</table>
										<?php
									}
									
									if ($access_level == "full")
									{
										?>
										<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" id="add_team_form" onsubmit="return false;">
										<input type="hidden" name="addteam" value="true">
										<table class="grouplist" style="width: 100%; table-layout: auto; margin-top: 5px;" cellpadding=0 cellspacing=0>
										<tr>
											<td style="width: auto;" valign="middle"><input type="text" name="teamname" style="width: 155px;"></td>
											<td style="width: 20px; padding: 0px; text-align: center;"><?php echo image_submit_tag('icon_plus_small.png', 'onClick="addTeam();"', __('Add team'), __('Add team'), '', 0, 16, 16); ?></td>
										</tr>
										</table>
										</form>
										<?php
									}
	
									?>
									</div>
								</td>
								<td style="padding: 5px; width: auto;" valign="top">
									<div style="border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('Permissions / restrictions'); ?></div>
									<?php
									
									if (is_numeric(TBGContext::getRequest()->getParameter('team')))
									{
										$theuid = 0;
										$tid = TBGContext::getRequest()->getParameter('team');
										$gid = 0;
										$all = 0;
										$thelink = "config.php?module=core&amp;section=1&amp;team=$tid";
										require_once TBGContext::getIncludePath() . 'include/permissions.inc.php';
									}
									elseif (is_numeric(TBGContext::getRequest()->getParameter('group')))
									{
										$theuid = 0;
										$tid = 0;
										$gid = TBGContext::getRequest()->getParameter('group');
										($gid == 0) ? $all = 1 : $all = 0;
										$thelink = "config.php?module=core&amp;section=1&amp;group=$gid";
										require_once TBGContext::getIncludePath() . 'include/permissions.inc.php';
									}
									else
									{
										?>
										<div style="padding: 5px; color: #C5C5C5;"><?php echo __('Please select a group or team to the left to view its permissions.'); ?></div>
										<?php
									}
	
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
		}
	} // END PERMISSION DOUBLECHECK
?>