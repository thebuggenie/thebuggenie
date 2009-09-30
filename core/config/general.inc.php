<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			$settings_saved = false;
			$settings_arr = array('theme_name', 'user_themes', 'onlinestate', 
								'offlinestate', 'awaystate', 'requirelogin', 'allowreg', 'defaultgroup', 
								'returnfromlogin', 'returnfromlogout', 'showloginbox', 'limit_registration', 
								'showprojectsoverview', 'showprojectsoverview', 'cleancomments');
			foreach ($settings_arr as $setting)
			{
				if (BUGScontext::getRequest()->getParameter($setting) !== null)
				{
					BUGSsettings::saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
					$settings_saved = true;
				}
			}
			if (BUGScontext::getRequest()->getParameter('b2_name') !== null)
			{
				BUGSsettings::saveSetting('b2_name', BUGScontext::getRequest()->getParameter('b2_name'));
			}
			if (BUGScontext::getRequest()->getParameter('b2_tagline') !== null)
			{
				BUGSsettings::saveSetting('b2_tagline', BUGScontext::getRequest()->getParameter('b2_tagline', null, false));
			}
			if (BUGScontext::getRequest()->getParameter('defaultuname'))
			{
				$defaultuname_error = false;
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tUsers::UNAME, BUGScontext::getRequest()->getParameter('defaultuname'));
				$crit->addWhere(B2tUsers::SCOPE, BUGScontext::getScope()->getID());
				$crit->addWhere(B2tUsers::ENABLED, 1);
				$row = B2DB::getTable('B2tUsers')->doSelectOne($crit);
				if ($row instanceof B2DBRow)
				{
					BUGSsettings::saveSetting('defaultuname', BUGScontext::getRequest()->getParameter('defaultuname'));
					BUGSsettings::saveSetting('defaultpwd', $row->get(B2tUsers::PASSWD));
				}
				else
				{
					$defaultuname_error = true;
					$settings_saved = false;
				}
			}
			if ($settings_saved)
			{
				bugs_moveTo('config.php?module=core&section=12');
			}
		}

		$themes = BUGScontext::getThemes();
		$languages = BUGSi18n::getLanguages();
		
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure general settings'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('From here you can manage common The Bug Genie settings.'); ?>
						<?php echo __('To find out more about what each setting does, please refer to the %bugs_online_help%', array('%bugs_online_help%' => bugs_helpBrowserHelper('generalsettings', __('The Bug Genie online help')))); ?>.
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<?php 
		
		if ($defaultuname_error)
		{
			?>
			<div style="color: #A44; padding: 5px;"><b><?php echo __('Could not find this username. Please type the username of an existing, enabled user.'); ?></div>
			<?php 
		}
		
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="12">
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: 90%;"><?php echo __('Global settings'); ?></div>
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Interface language'); ?></b></td>
				<td style="width: 250px;">
				<select name="language" style="width: 100%;">
				<?php 
				
				foreach ($languages as $lang_code => $lang_desc)
				{
					echo '<option value="' . $lang_code . '"' . ((BUGSsettings::get('language') == $lang_code) ? ' selected' : '') . '>' . $lang_desc . '</option>';
				}
				
				?>
				</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('The default language in BUGS 2'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Charset'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="charset" value="<?php echo BUGSsettings::get('charset'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The charset to use - leave blank to use the charset specified in the language file (currently %charset%)', array('%charset%' => '<b>' . BUGScontext::getI18n()->getLangCharset() . '</b>')); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('BUGS 2 name'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="b2_name" value="<?php echo BUGSsettings::get('b2_name'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The name appearing as the BUGS 2 name'); ?> <i>(<?php echo __('HTML allowed'); ?>)</i></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Tagline / slogan'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="b2_tagline" value="<?php echo BUGSsettings::get('b2_tagline'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The name appearing beneath the BUGS 2 name'); ?> <i>(<?php echo __('HTML allowed'); ?>)</i></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Show projects overview'); ?></b></td>
				<td style="width: 250px;">
					<select name="showprojectsoverview" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('showprojectsoverview') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, show on the frontpage'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('showprojectsoverview') == 0) ? ' selected' : ''; ?>><?php echo __('No, don\'t show'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Whether the project overview list should appear on the frontpage or not'); ?></td>
			</tr>
		</table>
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: 90%;"><?php echo __('Theme settings'); ?></div>
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Selected theme'); ?></b></td>
				<td style="width: 250px;">
					<select name="theme_name" style="width: 100%;">
						<?php foreach ($themes as $aTheme): ?>
							<option value="<?php echo $aTheme; ?>"<?php echo (BUGSsettings::getThemeName() == $aTheme) ? ' selected' : ''; echo ($access_level != 'full') ? ' disabled' : ''; ?>><?php echo $aTheme; ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				<td style="width: auto; padding: 5px;"><?php echo __('The selected BUGS 2 theme'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Individual themes'); ?></b></td>
				<td style="width: 250px;">
					<select name="user_themes" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('user_themes') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, users can choose their preferred theme'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('user_themes') == 0) ? ' selected' : ''; ?>><?php echo __('No, this theme will always be used'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Choose if users can select a different theme than the default theme'); ?></td>
			</tr>
		</table>
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: 90%;">Issue settings</div>
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Comment trail'); ?></b></td>
				<td style="width: 250px;">
					<select name="cleancomments" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('cleancomments') == 1) ? ' selected' : ''; ?>><?php echo __('Don\'t post system comments when an issue is updated') ?></option>
						<option value=0 <?php echo (BUGSsettings::get('cleancomments') != 1) ? ' selected' : ''; ?>><?php echo __('Always post comments when an issue is updated'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('To keep the comment trail clean in issues, you can select to not post system comments when an issue updates'); ?></td>
			</tr>
		</table>
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: 90%;"><?php echo __('User settings'); ?></div>
		<div style="padding: 5px; padding-top: 0px; padding-bottom: 10px;"><?php echo __('These are some user-specific settings.'); ?> <?php echo __('To manage users and their individual permissions, go to the %manage_users% page.', array('%manage_users%' => '<span style="display: float;">' . image_tag('cfg_icon_users.png') . '&nbsp;<a href="config.php?module=core&amp;section=2"><b>' . __('Manage users') . '</b></a></span>')); ?></div>
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Require login'); ?></b></td>
				<td style="width: 250px;">
					<select name="requirelogin" id="requirelogin" onchange="bB = document.getElementById('requirelogin'); bC = document.getElementById('defaultuname'); bD = document.getElementById('defaultisguest'); if (bB.value == '1') { bC.disabled = true; bD.disabled = true; } else { bC.disabled = false; bD.disabled = false; }" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('requirelogin') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, you need a valid login to access any content'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('requirelogin') == 0) ? ' selected' : ''; ?>><?php echo __('No, use the default username provided below'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select if the default user is a guest user or a normal user'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Show login links on front page'); ?></b></td>
				<td style="width: 250px;">
					<select name="showloginbox" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('showloginbox') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, show a login box at the left hand side'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('showloginbox') == 0) ? ' selected' : ''; ?>><?php echo __('No, only show in the menu'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select if the default user is a guest user or a normal user'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Default user'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="defaultuname" id="defaultuname" <?php echo (BUGSsettings::get('requirelogin') == 1 || $access_level != 'full') ? ' disabled' : ''; ?> value="<?php echo BUGSsettings::get('defaultuname'); ?>" style="width: 100%;" ></td>
				<td style="width: auto; padding: 5px;"><?php echo __('When you\'re not logged in, you will be "logged in" with this username'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Default user is guest'); ?></b></td>
				<td style="width: 250px;">
					<select name="defaultisguest" id="defaultisguest" style="width: 100%" <?php echo (BUGSsettings::get('requirelogin') == 1 || $access_level != 'full') ? ' disabled' : ''; ?> >
						<option value=1 <?php echo (BUGSsettings::get('defaultisguest') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, the default user is a guest'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('defaultisguest') == 0) ? ' selected' : ''; ?>><?php echo __('No, the default user is a normal account'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select if the default user is a guest user or a normal user'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('User self-registration'); ?></b></td>
				<td style="width: 250px;">
					<select name="allowreg" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGSsettings::get('allowreg') == 1) ? ' selected' : ''; ?>><?php echo __('Yes, users can register their new accounts'); ?></option>
						<option value=0 <?php echo (BUGSsettings::get('allowreg') == 0) ? ' selected' : ''; ?>><?php echo __('No, new users will be created by an admin'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Choose if users can register new accounts'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Limit registration'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="limit_registration" id="limit_registration" <?php echo (BUGSsettings::get('allowreg') == 0 || $access_level != 'full') ? ' disabled' : ''; ?> value="<?php echo BUGSsettings::get('limit_registration'); ?>" style="width: 100%;" ></td>
				<td style="width: auto; padding: 5px;"><?php echo __('Comma-separated list of allowed domains (ex: %example%). Leave empty to allow all domains.', array('%example%' => 'thebuggenie.com, zegeniestudios.net')); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Default user group'); ?></b></td>
				<td style="width: 250px;">
					<select name="defaultgroup" style="width: 100%;">
					<?php
					
					foreach (BUGSgroup::getAll() as $aGroup)
					{
						?>
						<option value="<?php print $aGroup->getID(); ?>"<?php if (BUGSsettings::get('defaultgroup') == $aGroup->getID()) { print " selected"; } ?>><?php print $aGroup->getName(); ?></option>
						<?php
					}

					?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('New users will automatically be added to this group'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Page after login'); ?></b></td>
				<td style="width: 250px;">
					<select name="returnfromlogin" style="width: 100%;">
						<option value="index.php"<?php if (BUGSsettings::get('returnfromlogin') == 'index.php') { print " selected"; } ?>><?php echo __('Frontpage'); ?></option>
						<option value="account.php"<?php if (BUGSsettings::get('returnfromlogin') == 'account.php') { print " selected"; } ?>><?php echo __('Account details'); ?></option>
						<?php 
						
						if (BUGScontext::isModuleLoaded('publish') && BUGScontext::getModule('publish')->isEnabled())
						{
							?>
							<option value="modules/publish/publish.php"<?php if (BUGSsettings::get('returnfromlogin') == 'modules/publish/publish.php') { print " selected"; } ?>><?php echo __('News frontpage ("publish" module)'); ?></option>
							<?php 
						}
						if (BUGScontext::isModuleLoaded('calendar') && BUGScontext::getModule('calendar')->isEnabled())
						{
							?>
							<option value="modules/calendar/calendar.php"<?php if (BUGSsettings::get('returnfromlogin') == 'modules/calendar/calendar.php') { print " selected"; } ?>><?php echo __('Calendar ("calendar" module)'); ?></option>
							<?php 
						}
						
						BUGScontext::trigger('core', 'setting_returnfromlogin'); 
						
						?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Users will be redirected to this page after logging in'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Online user state'); ?></b></td>
				<td style="width: 250px;">
					<select name="onlinestate" style="width: 100%;">
					<?php
						$userstates = BUGScontext::getStates();
						foreach ($userstates as &$aState)
						{
							$aState = BUGSfactory::userstateLab($aState);
							?>
							<option value="<?php print $aState->getID(); ?>"<?php if (BUGSsettings::get('onlinestate') == $aState->getID()) { print " selected"; } ?>><?php print $aState->getName(); ?></option>
							<?php
						}
					?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('When logged in, users will be in this state, unless manually set to another state'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('"Away" user state'); ?></b></td>
				<td style="width: 250px;">
					<select name="awaystate" style="width: 100%;">
					<?php
						foreach ($userstates as $aState)
						{
							?>
							<option value="<?php print $aState->getID(); ?>"<?php if (BUGSsettings::get('awaystate') == $aState->getID()) { print " selected"; } ?>><?php print $aState->getName(); ?></option>
							<?php
						}
					?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('When idle, users will be in this state, unless manually set to another state'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Page after logout'); ?></b></td>
				<td style="width: 250px;">
					<select name="returnfromlogout" style="width: 100%;">
						<option value="index.php"<?php if (BUGSsettings::get('returnfromlogout') == 'index.php') { print " selected"; } ?>><?php echo __('Frontpage'); ?></option>
						<option value="account.php"<?php if (BUGSsettings::get('returnfromlogout') == 'account.php') { print " selected"; } ?>><?php echo __('Account details'); ?></option>
						<?php 
						
						if (BUGScontext::isModuleLoaded('publish') && BUGScontext::getModule('publish')->isEnabled())
						{
							?>
							<option value="modules/publish/publish.php"<?php if (BUGSsettings::get('returnfromlogout') == 'modules/publish/publish.php') { print " selected"; } ?>><?php echo __('News frontpage ("publish" module)'); ?></option>
							<?php 
						}
						if (BUGScontext::isModuleLoaded('calendar') && BUGScontext::getModule('calendar')->isEnabled())
						{
							?>
							<option value="modules/calendar/calendar.php"<?php if (BUGSsettings::get('returnfromlogout') == 'modules/calendar/calendar.php') { print " selected"; } ?>><?php echo __('Calendar ("calendar" module)'); ?></option>
							<?php 
						}
						
						BUGScontext::trigger('core', 'settings_returnfromlogout'); 
						
						?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Users will be redirected to this page after logging out'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Offline user state'); ?></b></td>
				<td style="width: 250px;">
					<select name="offlinestate" style="width: 100%;">
					<?php
						foreach ($userstates as $aState)
						{
							?>
							<option value="<?php print $aState->getID(); ?>"<?php if (BUGSsettings::get('offlinestate') == $aState->getID()) { print " selected"; } ?>><?php print $aState->getName(); ?></option>
							<?php
						}
					?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('After logging out, users will be in this state'); ?></td>
			</tr>
			<?php 
			
			if ($access_level == 'full')
			{
				?>
				<tr>
					<td colspan=3 style="padding: 5px; text-align: right;"><input type="submit" value="<?php echo __('Save'); ?>"></td>
				</tr>
				<?php 
			}

			?>
		</table>
		</form>
		<?php
	}
	
?>