<?php

	if (!(isset($print_friendly) && $print_friendly))
	{
		?>
		<div class="menu_top"></div>
		
		<table class="menu_strip" cellpadding=0 cellspacing=0 width="100%" style="table-layout: auto;">
		<tr>
		<td class="menu_container">
		<table cellpadding=0 cellspacing=0>
		<tr>
		<?php
		
			if ($page == "index")
			{
				?>
				<td align="center" valign="middle" class="selected" id="link_index"><table align="center"><tr><td><?php echo image_tag('tab_index.png', 'align="left"'); ?></td><td><?php echo __('Frontpage'); ?></td></tr></table></td>
				<?php
			}
			else
			{
				?>
				<td align="center" valign="middle" id="link_index" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected"><table align="center"><tr><td><a href="<?php print BUGScontext::getTBGPath(); ?>index.php"><?php echo image_tag('tab_index.png', 'align="left"'); ?></a></td><td><a href="<?php print BUGScontext::getTBGPath(); ?>index.php"><?php echo __('Frontpage'); ?></a></td></tr></table></td>
				<?php
			}
		
			if (BUGScontext::getUser()->hasPermission("b2canreportissues", 0, "core"))
			{
				if ($page == "reportissue")
				{
					?>
					<td align="center" valign="middle" class="selected" id="link_reportissue"><table align="center"><tr><td><?php echo image_tag('tab_reportissue.png', 'align="left"'); ?></td><td><?php echo __('Report an issue'); ?></td></tr></table></td>
					<?php
				}
				else
				{
					?>
					<td align="center" valign="middle" id="link_reportissue" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected"><table align="center"><tr><td><a href="<?php print BUGScontext::getTBGPath(); ?>reportissue.php"><?php echo image_tag('tab_reportissue.png', 'align="left"'); ?></a></td><td><a href="<?php print BUGScontext::getTBGPath(); ?>reportissue.php"><?php echo (isset($_SESSION['rni_step2_issuetype']) || isset($_SESSION['rni_step2_category']) || isset($_SESSION['rni_step2_component']) || isset($_SESSION['rni_step2_severity'])) ? __('Continue reporting') : __('Report an issue'); ?></a></td></tr></table></td>
					<?php
				}
			}
		
			foreach (BUGScontext::getModules() as $module)
			{
				if ($module->hasAccess())
				{
					if ($module->isVisibleInMenu() && $module->isEnabled())
					{
						if ($page == $module->getName())
						{
							?>
							<td align="center" valign="middle" class="selected"><table align="center"><tr><td><?php echo image_tag('tab_' . $module->getName() . '.png'); ?></td><td><?php print __($module->getMenuTitle()); ?></td></tr></table></td>
							<?php
						}
						else
						{
							?>
							<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected"><table align="center"><tr><td><a href="<?php print BUGScontext::getTBGPath() . "modules/" . $module->getName() . "/" . $module->getName(); ?>.php"><?php echo image_tag('tab_' . $module->getName() . '.png'); ?></a></td><td><a href="<?php print BUGScontext::getTBGPath() . "modules/" . $module->getName() . "/" . $module->getName(); ?>.php"><?php echo __($module->getMenuTitle()); ?></a></td></tr></table></td>
							<?php
						}
					}
				}
			}
			if ($page == "config")
			{
				if (BUGScontext::getUser()->hasPermission("b2viewconfig", 0, 'core'))
				{
					?>
					<td align="center" valign="middle" class="selected"><table align="center"><tr><td><?php echo image_tag('tab_config.png'); ?></td><td><?php echo __('Configuration center'); ?></td></tr></table></td>
					<?php
				}
			}
			else
			{
				if (BUGScontext::getUser()->hasPermission("b2viewconfig", 0, 'core'))
				{
					?>
					<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected"><table align="center"><tr><td><a href="<?php print BUGScontext::getTBGPath(); ?>config.php"><?php echo image_tag('tab_config.png'); ?></a></td><td><a href="<?php print BUGScontext::getTBGPath(); ?>config.php"><?php echo __('Configuration center'); ?></a></td></tr></table></td>
					<?php
				}
			}
			if ($page == "about")
			{
				?>
				<td align="center" valign="middle" class="selected"><table align="center"><tr><td><?php echo image_tag('tab_about.png'); ?></td><td><?php echo __('About'); ?></td></tr></table></td>
				<?php
			}
			else
			{
				?>
				<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected"><table align="center"><tr><td><a href="<?php print BUGScontext::getTBGPath(); ?>about.php"><?php echo image_tag('tab_about.png'); ?></a></td><td><a href="<?php print BUGScontext::getTBGPath(); ?>about.php"><?php echo __('About'); ?></a></td></tr></table></td>
				<?php
			}
		
		?>
		</tr>
		</table>
		</td>
		<td class="spacer" style="text-align: right; padding-right: 10px;"><table align="right"><tr><td><?php echo image_tag('icon_user.png'); ?></td><td><b>
		<?php

		if (!BUGScontext::getUser()->hasPermission('b2noaccountaccess'))
		{
			echo '<a style="color: #555;" href="' . BUGScontext::getTBGPath() . 'account.php">' . BUGScontext::getUser()->getRealname() . '</a>';
		}
		else
		{
			echo BUGScontext::getUser()->getRealname();
		}
		 
		?>
		</b></td></tr></table></td>
		<?php
		
		if (!BUGScontext::getUser()->isThisGuest())
		{
			if ($page == "account")
			{
				?>
				<td align="center" valign="middle" class="selected" style="width: 110px;"><table align="center"><tr><td><?php echo image_tag('tab_account.png'); ?></td><td><?php echo __('My account'); ?></td></tr></table></td>
				<?php
			}
			else
			{
				?>
				<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected" style="width: 110px;"><table align="center"><tr><td><a href="javascript:void(0);" onclick="Effect.Appear('myacct', { duration: 0.5 });"><?php echo image_tag('tab_account.png'); ?></a></td><td><a href="javascript:void(0);" onclick="Effect.Appear('myacct', { duration: 0.5 });"><?php echo __('My account'); ?></a></td></tr></table>
				<div style="display: none; position: absolute; right: 1px; width: 180px; padding: 5px; background-color: #FFF; border: 1px solid #DDD;" id="myacct">
				<div style="text-align: left; padding-bottom: 2px; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><b><?php echo BUGScontext::getUser()->getRealname(); ?></b></div>
				<div style="text-align: left; padding-bottom: 2px;"><b><?php echo __('Places'); ?></b></div>
				<table style="width: 100%; table-layout: fixed;" cellpadding=0 cellspacing=2>
				<?php
	
					foreach (BUGScontext::getModules() as $module)
					{
						if ($module->hasAccess())
						{
							if ($module->isEnabled() && $module->isVisibleInUsermenu())
							{
								?>
								<tr>
								<td class="imgtd" style="width: 20px;"><?php echo image_tag('tab_' . $module->getName() . '.png'); ?></td>
								<td><a href="<?php print BUGScontext::getTBGPath() . "modules/" . $module->getName() . "/" . $module->getName(); ?>.php"><?php echo __($module->getMenuTitle()); ?></a></td>
								</tr>
								<?php
							}
						}
					}
	
				?>
				</table>
				<div style="text-align: left; padding-top: 5px; padding-bottom: 2px;"><b><?php echo __('Settings'); ?></b></div>
				<table style="width: 100%; table-layout: fixed;" cellpadding=0 cellspacing=2>
					<?php BUGScontext::trigger('core', 'account_settingslist'); ?>
				</table>
				<div style="text-align: left; padding-top: 5px; padding-bottom: 2px;"><b><?php echo __('Common actions'); ?></b></div>
				<table style="width: 100%;" cellpadding=0 cellspacing=2>
				<tr>
				<td class="imgtd"><?php echo image_tag('tab_account.png'); ?></td>
				<td><a href="<?php print BUGScontext::getTBGPath(); ?>account.php"><?php echo __('View / edit my details'); ?></a></td>
				</tr>
				<tr>
				<td class="imgtd"><?php echo image_tag('icon_userstate.png'); ?></td>
				<td><a href="javascript:void(0);" onclick="Element.show('availStatus');getUserStateList();"><?php echo __('Change my status to'); ?></a></td>
				</tr>
				<tr id="availStatus" style="display: none;"><td>&nbsp;</td><td><span id="user_statelist"></span>
				<div align="right" class="small"><a href="javascript:void(0);" onclick="Element.hide('availStatus');" style="font-size: 10px;"><?php echo __('Never mind'); ?></a></div></td>
				</tr>
				<tr>
				<td class="imgtd"><?php echo image_tag('logout.png'); ?></td>
				<td style="text-align: left;"><a href="<?php echo BUGScontext::getTBGPath(); ?>login.php?logout=1"><?php echo __('Log out'); ?></a></td>
				</tr>
				<?php if (isset($_COOKIE['b2_username_preswitch']) && $_COOKIE['b2_username_preswitch'] != ''): ?>
					<tr>
					<td class="imgtd"><?php echo image_tag('switchuser.png'); ?></td>
					<td style="text-align: left;"><a href="login_validate.inc.php?switch_user=true"><?php echo __('Switch back to original user'); ?></a></td>
					</tr>
				<?php endif; ?>
				</table>
				<div style="font-size: 9px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Fade('myacct', { duration: 0.5 });"><?php echo __('Close menu'); ?></a></div>
				</div></td>
				<?php
			}
		}
		else
		{
			if ($page == "login")
			{
				?>
				<td align="center" valign="middle" class="selected" style="width: 130px;"><table align="center"><tr><td><?php echo image_tag('tab_login.png'); ?></td><td><?php echo (BUGSsettings::get('allowreg') == 1) ? __('Login / Register') : __('Login'); ?></td></tr></table></td>
				<?php
			}
			else
			{
				?>
				<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected" style="width: 130px;"><table align="center"><tr><td><a href="<?php print BUGScontext::getTBGPath(); ?>login.php?action=login"><?php echo image_tag('tab_login.png'); ?></a></td><td><a href="<?php print BUGScontext::getTBGPath(); ?>login.php?action=login"><?php echo (BUGSsettings::get('allowreg') == 1) ? __('Login / Register') : __('Login'); ?></a></td></tr></table></td>
				<?php
			}
		}
		
		?>
		
		</tr>
		</table>
		
		<?php
		
			if (BUGScontext::getUser()->hasPermission("b2no".$page."access", 0, "core") == true)
			{
				exit();
			}
		
	}
?>