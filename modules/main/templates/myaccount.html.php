<?php

	$tbg_response->setTitle('Your account details');
	$tbg_response->addBreadcrumb(__('Account details'), make_url('account'), tbg_get_breadcrumblinks('main_links'));
	
?>
<div id="account_info_container">
	<div id="account_user_info">
		<?php echo image_tag($tbg_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;', 'alt' => '[avatar]'), true); ?>
		<span id="user_name_span">
			<?php echo $tbg_user->getRealname(); ?><br>
			<?php if (!$tbg_user->isOpenIdLocked()): ?>
				<?php echo $tbg_user->getUsername(); ?>
			<?php endif; ?>
		</span>
	</div>
	<br style="clear: both;">
	<div class="profile_buttons">
		<div class="button-group">
			<a style="<?php if (!$tbg_user->usesGravatar()): ?>display: none; <?php endif; ?>" id="gravatar_change" href="http://en.gravatar.com/emails/" class="button button-silver">
				<?php echo image_tag('gravatar.png'); ?>
				<?php echo __('Change my profile picture / avatar'); ?>
			</a>
			<?php if ($tbg_user->canChangePassword() && !$tbg_user->isOpenIdLocked()): ?>
				<a href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('change_password_div').toggle();" id="change_password_button" class="button button-silver"><?php echo __('Change my password'); ?></a>
			<?php elseif (!$tbg_user->isOpenIdLocked()): ?>
				<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('Changing password disabled'); ?>', '<?php echo __('Changing your password can not be done via this interface. Please contact your administrator to change your password.'); ?>')" class="button button-silver disabled"><?php echo __('Change my password'); ?></a>
			<?php endif; ?>
			<a class="button button-silver" id="security_key_button" type="button" href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('security_key').toggle();"><?php echo __('My security key'); ?></a>
			<a class="button button-silver" id="more_actions_button" type="button" href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('more_actions').toggle();"><?php echo image_tag('tab_search.png').__('Show my issues'); ?></a>
			<?php if ($tbg_user->isOpenIdLocked()): ?>
				<a href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('pick_username_div').toggle();" id="pick_username_button" class="button button-blue"><?php echo __('Pick a username'); ?></a>
			<?php endif; ?>
		</div>
		<div id="security_key" style="display: none; position: absolute; width: 350px; padding: 10px; top: 36px; right: 0; z-index: 1000;" class="rounded_box white shadowed">
			<?php echo __('Your security key is %securitykey%', array('%securitykey%' => '<b>'.TBGSettings::getRemoteSecurityKey().'</b>')); ?>
		</div>
		<ul id="more_actions" style="display: none; position: absolute; width: 300px; top: 36px; right: 0; z-index: 1000;" class="simple_list rounded_box white shadowed" onclick="$('more_actions_button').toggleClassName('button-pressed');$('more_actions').toggle();">
			<li><?php echo link_tag(make_url('my_reported_issues'), image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')).__("Show issues I've reported")); ?></li>
			<li><?php echo link_tag(make_url('my_assigned_issues'), image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')).__("Show open issues assigned to me")); ?></li>
			<li><?php echo link_tag(make_url('my_teams_assigned_issues'), image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')).__("Show open issues assigned to my teams")); ?></li>
		</ul>
		<?php if ($tbg_user->isOpenIdLocked()): ?>
			<div class="rounded_box white shadowed"  style="display: none; position: absolute; right: 0; top: 38px; z-index: 100; padding: 5px 10px 5px 10px; font-size: 13px; width: 400px;" id="pick_username_div">
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_check_username'); ?>" onsubmit="TBG.Main.Profile.checkUsernameAvailability('<?php echo make_url('account_check_username'); ?>'); return false;" method="post" id="check_username_form">
					<b><?php echo __('Picking a username'); ?></b><br>
					<div style="font-size: 13px; margin-bottom: 10px;"><?php echo __('Since this account was created via an OpenID login, you will have to pick a username to be able to log in with a username or password. You can continue to use your account with your OpenID login, so this is only if you want to pick a username for your account.'); ?><br>
					<br><?php echo __('Click "%check_availability%" to see if your desired username is available.', array('%check_availability%' => __('Check availability'))); ?></div>
					<label for="username_pick" class="smaller"><?php echo __('Type desired username'); ?></label><br>
					<input type="text" name="desired_username" id="username_pick" style="width: 390px;"><br>
					<?php echo csrf_tag(); ?>
					<div id="username_unavailable" style="display: none;"><?php echo __('This username is not available'); ?></div>
					<div class="smaller" style="text-align: right; margin: 10px 2px 5px 0; height: 23px;">
						<div style="float: right; padding: 3px;"><?php echo __('%check_availability% or %cancel%', array('%check_availability%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'pick_username_div\').toggle();$(\'pick_username_button\').toggleClassName(\'button-pressed\');"><b>' . __('cancel') . '</b></a>')); ?></div>
						<input type="submit" value="<?php echo __('Check availability'); ?>" style="font-weight: bold; float: right;">
						<span id="pick_username_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</div>
				</form>
			</div>
		<?php endif; ?>
		<?php if ($tbg_user->canChangePassword()): ?>
			<div class="rounded_box white shadowed"  style="display: none; position: absolute; right: 0; top: 38px; z-index: 100; padding: 5px 10px 5px 10px; font-size: 13px; width: 350px;" id="change_password_div">
				<?php
				if (TBGSettings::isUsingExternalAuthenticationBackend())
				{
					echo tbg_parse_text(TBGSettings::get('changepw_message'), null, null, array('embedded' => true));
				}
				else
				{
				?>
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_change_password'); ?>" onsubmit="TBG.Main.Profile.changePassword('<?php echo make_url('account_change_password'); ?>'); return false;" method="post" id="change_password_form">
					<b><?php echo __('Changing your password'); ?></b><br>
					<div style="font-size: 13px; margin-bottom: 10px;"><?php echo __('Enter your current password in the first box, then enter your new password twice (to prevent you from typing mistakes).'); ?><br>
					<br><?php echo __('Click "%change_password%" to change it.', array('%change_password%' => __('Change password'))); ?></div>
					<label for="current_password" class="smaller"><?php echo __('Current password'); ?></label><br>
					<input type="password" name="current_password" id="current_password" value="" style="width: 200px;"><br>
					<br>
					<label for="new_password_1" class="smaller"><?php echo __('New password'); ?></label><br>
					<input type="password" name="new_password_1" id="new_password_1" value="" style="width: 200px;"><br>
					<label for="new_password_2" class="smaller"><?php echo __('New password (repeat it)'); ?></label><br>
					<input type="password" name="new_password_2" id="new_password_2" value="" style="width: 200px;"><br>
					<div class="smaller" style="text-align: right; margin: 10px 2px 5px 0; height: 23px;">
						<div style="float: right; padding: 3px;"><?php echo __('%change_password% or %cancel%', array('%change_password%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'change_password_div\').toggle();$(\'change_password_button\').toggleClassName(\'button-pressed\');"><b>' . __('cancel') . '</b></a>')); ?></div>
						<input type="submit" value="<?php echo __('Change password'); ?>" style="font-weight: bold; float: right;">
						<span id="change_password_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</div>
				</form>
				<?php
				}
				?>
			</div>
		<?php endif; ?>
	</div>
	<br style="clear: both;">
	<div style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;">
		<div style="margin: 0; clear: both; height: 30px; width: 100%;" class="tab_menu">
			<ul id="account_tabs">
				<li class="selected" id="tab_profile"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_profile', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_users.png', array('style' => 'float: left;')).__('Profile information'); ?></a></li>
				<li id="tab_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_settings', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General settings'); ?></a></li>
				<?php if (TBGSettings::isOpenIDavailable()): ?>
					<li id="tab_openid"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_openid', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('icon_openid.png', array('style' => 'float: left;')).__('Login accounts'); ?></a></li>
				<?php endif; ?>
				<?php TBGEvent::createNew('core', 'account_tabs')->trigger(); ?>
				<?php foreach (TBGContext::getModules() as $module_name => $module): ?>
					<?php if ($module->hasAccountSettings()): ?>
						<li id="tab_settings_<?php echo $module_name; ?>"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_settings_<?php echo $module_name; ?>', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag($module->getAccountSettingsLogo(), array('style' => 'float: left;'), false, $module_name).__($module->getAccountSettingsName()); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div id="account_tabs_panes">
			<div id="tab_profile_pane">
				<?php
				if (TBGSettings::isUsingExternalAuthenticationBackend())
				{
					echo tbg_parse_text(TBGSettings::get('changedetails_message'), null, null, array('embedded' => true));
				}
				else
				{
				?>
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_information'); ?>" onsubmit="TBG.Main.Profile.updateInformation('<?php echo make_url('account_save_information'); ?>'); return false;" method="post" id="profile_information_form">
					<div class="rounded_box borderless lightgrey cut_bottom" style="margin: 5px 0 0 0; width: 895px; border-bottom: 0;">
						<p class="content"><?php echo __('Edit your profile details here, including additional information.'); ?><br><?php echo __('Required fields are marked with a little star.'); ?></p>
						<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
							<tr>
								<td style="padding: 5px;"><label for="profile_buddyname">* <?php echo __('Display name'); ?></label></td>
								<td>
									<input type="text" name="buddyname" id="profile_buddyname" value="<?php echo $tbg_user->getBuddyname(); ?>" style="width: 200px;">
								</td>
							</tr>
							<tr>
								<td class="config_explanation" colspan="2"><?php echo __('This is the name used across the site for your profile.'); ?></td>
							</tr>
							<tr>
								<td style="padding: 5px;"><label for="profile_realname"><?php echo __('Full name'); ?></label></td>
								<td>
									<input type="text" name="realname" id="profile_realname" value="<?php echo $tbg_user->getRealname(); ?>" style="width: 300px;">
								</td>
							</tr>
							<tr>
								<td class="config_explanation" colspan="2"><?php echo __('This is your real name, mostly used in communication with you, and rarely shown to others'); ?></td>
							</tr>
							<tr>
								<td style="padding: 5px;"><label for="profile_email">* <?php echo __('Email address'); ?></label></td>
								<td>
									<input type="email" name="email" id="profile_email" value="<?php echo $tbg_user->getEmail(); ?>" style="width: 300px;">
								</td>
							</tr>
							<tr>
								<td style="padding: 5px;"><label for="profile_email_private_yes">* <?php echo __('Show my email address to others'); ?></label></td>
								<td>
									<input type="radio" name="email_private" value="0" id="profile_email_private_no"<?php if ($tbg_user->isEmailPublic()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_no"><?php echo __('Yes'); ?></label>&nbsp;&nbsp;
									<input type="radio" name="email_private" value="1" id="profile_email_private_yes"<?php if ($tbg_user->isEmailPrivate()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_yes"><?php echo __('No'); ?></label>
								</td>
							</tr>
							<tr>
								<td class="config_explanation" colspan="2"><?php echo __('Whether your email address is visible to other users in your profile information card. The email address is always visible to admins.'); ?></td>
							</tr>
							<tr>
								<td style="padding: 5px;"><label for="profile_homepage"><?php echo __('Homepage'); ?></label></td>
								<td>
									<input type="url" name="homepage" id="profile_homepage" value="<?php echo $tbg_user->getHomepage(); ?>" style="width: 300px;">
								</td>
							</tr>
							<tr>
								<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
							</tr>
						</table>
					</div>
					<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 895px; border-top: 0; padding: 3px; height: 26px;">
						<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your account information', array('%save%' => __('Save'))); ?></div>
						<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
						<span id="profile_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</div>
				</form>
				<?php
				}
				?>
			</div>
			<div id="tab_settings_pane" style="display: none;">
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_settings'); ?>" onsubmit="TBG.Main.Profile.updateSettings('<?php echo make_url('account_save_settings'); ?>'); return false;" method="post" id="profile_settings_form">
					<div class="rounded_box borderless lightgrey cut_bottom" style="margin: 5px 0 0 0; width: 895px; border-bottom: 0;">
						<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
							<tr>
								<td style="width: 200px; padding: 5px;"><label for="profile_use_gravatar_yes"><?php echo __('Use Gravatar avatar'); ?></label></td>
								<td>
									<input type="radio" name="use_gravatar" value="1" id="profile_use_gravatar_yes"<?php if ($tbg_user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_yes"><?php echo __('Yes'); ?></label>&nbsp;&nbsp;
									<input type="radio" name="use_gravatar" value="0" id="profile_use_gravatar_no"<?php if (!$tbg_user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_no"><?php echo __('No'); ?></label>
								</td>
							</tr>
							<tr>
								<td class="config_explanation" colspan="2">
									<?php echo __("The Bug Genie can use your <a href=\"http://www.gravatar.com\" target=\"_blank\">Gravatar</a> profile picture, if you have one. If you don't have one but still want to use Gravatar for profile pictures, The Bug Genie will use a Gravatar <a href=\"http://blog.gravatar.com/2008/04/22/identicons-monsterids-and-wavatars-oh-my/\" target=\"_blank\">auto-generated image unique for your email address</a>."); ?><br>
									<br>
									<?php echo __("Don't have a Gravatar yet? %link_to_get_one_now%", array('%link_to_get_one_now%' => link_tag('http://en.gravatar.com/site/signup/'.urlencode($tbg_user->getEmail()), __('Get one now!'), array('target' => '_blank')))); ?>
								</td>
							</tr>
							<tr>
								<td style="width: 200px; padding: 5px;"><label for="profile_timezone"><?php echo __('Current timezone'); ?></label></td>
								<td>
									<select name="timezone" id="profile_timezone" style="width: 300px;">
										<option value="sys"<?php if ($tbg_user->getTimezone() == 'sys'): ?> selected<?php endif; ?>><?php echo __('Use global setting - GMT%time%', array('%time%' => ' '.TBGSettings::getGMTOffset())); ?></option>
										<?php for ($cc = 12;$cc >= 1;$cc--): ?>
											<option value="-<?php echo $cc; ?>"<?php if ($tbg_user->getTimezone() == -$cc): ?> selected<?php endif; ?>>GMT -<?php echo $cc; ?></option>
										<?php endfor; ?>
										<option value="0"<?php if ($tbg_user->getTimezone() == '0'): ?> selected<?php endif; ?>>GMT/UTC</option>
										<?php for ($cc = 1;$cc <= 12;$cc++): ?>
											<option value="<?php echo $cc; ?>"<?php if ($tbg_user->getTimezone() == $cc): ?> selected<?php endif; ?>>GMT +<?php echo $cc; ?></option>
										<?php endfor; ?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="config_explanation" colspan="2">
									<?php echo __('This setting is used to display issues, comments and more in your local timezone.'); ?><br>
									<?php echo __('The time is now: %time%', array('%time%' => tbg_formatTime(time(), 1))); ?>
								</td>
							</tr>
							<tr>
								<td style="width: 200px; padding: 5px;"><label for="profile_timezone"><?php echo __('Language'); ?></label></td>
								<td>
									<select name="profile_language" id="profile_language" style="width: 300px;">
										<option value="sys"<?php if ($tbg_user->getLanguage() == 'sys'): ?> selected<?php endif; ?>><?php echo __('Use global setting - %lang%', array('%lang%' => TBGSettings::getLanguage())); ?></option>
									<?php foreach ($languages as $lang_code => $lang_desc): ?>
										<option value="<?php echo $lang_code; ?>" <?php if ($tbg_user->getLanguage() == $lang_code): ?> selected<?php endif; ?>><?php echo $lang_desc; ?><?php if (TBGSettings::getLanguage() == $lang_code): ?> <?php echo __('(site default)'); endif;?></option>
									<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="config_explanation" colspan="2">
									<?php echo __('The language you select here will be used instead of the language chosen by the administrator.'); ?><br>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
							</tr>
						</table>
					</div>
					<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 895px; border-top: 0; padding: 3px; height: 26px;">
						<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your profile settings', array('%save%' => __('Save'))); ?></div>
						<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
						<span id="profile_settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</div>
				</form>
			</div>
			<?php if (TBGSettings::isOpenIDavailable()): ?>
				<div id="tab_openid_pane" style="display: none;">
					<div style="padding: 10px;">
						<?php echo __('The Bug Genie supports logging in via external authentication providers via %openid%. This means you can use your account details from other services (such as Google, Wordpress, etc.) to log in here, without having to remember another set of login details.', array('%openid%' => link_tag('http://openid.net', 'OpenID'))); ?><br>
						<div style="padding: 15px 0;"><button class="button button-blue" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'openid')); ?>');"><?php echo __('Add login from another provider'); ?></button></div>
						<div class="faded_out" id="no_openid_accounts"<?php if (count($tbg_user->getOpenIDAccounts())): ?> style="display: none;"<?php endif; ?>><?php echo __('You have not linked your account with any external authentication providers.'); ?></div>
						<?php if (count($tbg_user->getOpenIDAccounts())): ?>
							<ul class="simple_list openid_accounts_list hover_highlight" id="openid_accounts_list">
							<?php foreach ($tbg_user->getOpenIDAccounts() as $identity => $details): ?>
								<li id="openid_account_<?php echo $details['id']; ?>">
									<?php if (count($tbg_user->getOpenIDAccounts()) > 1 || !$tbg_user->isOpenIDLocked()): ?>
										<button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove this account link?'); ?>', '<?php echo __('Do you really want to remove the link to this external account?').'<br>'.__('By doing this, it will not be possible to log into this account via this authentication provider'); ?>', {yes: {click: function() {TBG.Main.Profile.removeOpenIDIdentity('<?php echo make_url('account_remove_openid', array('openid' => $details['id'], 'csrf_token' => TBGContext::generateCSRFtoken())); ?>', <?php echo $details['id']; ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Delete'); ?></button>
									<?php endif; ?>
									<?php echo image_tag('openid_providers.small/'.$details['type'].'.ico.png'); ?>
									<span class="openid_provider_name">
										<?php if ($details['type'] == 'google'): ?>
											<?php echo __('Google account'); ?>
										<?php elseif ($details['type'] == 'yahoo'): ?>
											<?php echo __('Yahoo account'); ?>
										<?php elseif ($details['type'] == 'blogger'): ?>
											<?php echo __('Blogger (google) account'); ?>
										<?php elseif ($details['type'] == 'wordpress'): ?>
											<?php echo __('Wordpress account'); ?>
										<?php else: ?>
											<?php echo __('Other OpenID provider'); ?>
										<?php endif; ?>
									</span>
								</li>
							<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'account_tab_panes')->trigger(); ?>
			<?php foreach (TBGContext::getModules() as $module_name => $module): ?>
				<?php if ($module->hasAccountSettings()): ?>
					<div id="tab_settings_<?php echo $module_name; ?>_pane" style="display: none;">
						<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_module_settings', array('target_module' => $module_name)); ?>" onsubmit="TBG.Main.Profile.updateModuleSettings('<?php echo make_url('account_save_module_settings', array('target_module' => $module_name)); ?>', '<?php echo $module_name; ?>'); return false;" method="post" id="profile_<?php echo $module_name; ?>_form">
							<div class="rounded_box borderless lightgrey cut_bottom" style="margin: 5px 0 0 0; width: 895px; border-bottom: 0;">
								<?php include_component("{$module_name}/accountsettings", array('module' => $module)); ?>
							</div>
							<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 895px; border-top: 0; padding: 3px; height: 26px;">
								<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save changes in the "%module_settings_name%" category', array('%save%' => __('Save'), '%module_settings_name%' => $module->getAccountSettingsName())); ?></div>
								<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
								<span id="profile_<?php echo $module_name; ?>_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
							</div>
						</form>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php if ($error): ?>
	<script type="text/javascript">
		TBG.Main.Helpers.Message.error('<?php echo __('An error occurred'); ?>', '<?php echo $error; ?>');
	</script>
<?php endif; ?>
<?php if ($username_chosen): ?>
	<script type="text/javascript">
		TBG.Main.Helpers.Message.success('<?php echo __("You've chosen the username '%username%'", array('%username%' => $tbg_user->getUsername())); ?>', '<?php echo __('Before you can use the new username to log in, you must pick a password via the "%change_password%" button.', array('%change_password%' => __('Change password'))); ?>');
	</script>
<?php endif; ?>
<?php if ($openid_used): ?>
	<script type="text/javascript">
		TBG.Main.Helpers.Message.error('<?php echo __('This OpenID identity is already in use'); ?>', '<?php echo __('Someone is already using this identity. Check to see if you have already added this account.'); ?>');
	</script>
<?php endif; ?>
