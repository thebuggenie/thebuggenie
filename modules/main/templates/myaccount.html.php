<?php

	$tbg_response->setTitle('Your account details');
	$tbg_response->addBreadcrumb(__('Account details'), make_url('account'), tbg_get_breadcrumblinks('main_links'));
	
?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="account_lefthand" class="side_bar">
			<?php TBGEvent::createNew('core', 'account_left_top')->trigger(); ?>
			<div class="rounded_box iceblue borderless account_details">
				<?php echo image_tag($tbg_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;'), true); ?>
				<div class="user_realname"><?php echo $tbg_user->getRealname(); ?></div>
				<div class="user_username">
					(<?php echo $tbg_user->getUsername(); ?>)
				</div>
				<div class="user_status">
					<?php echo '<b>' . __('Status: %status%', array('%status%' => '</b>' . (($tbg_user->getState() instanceof TBGUserstate) ? $tbg_user->getState()->getName() : '<span class="faded_out">' . __('Unknown') . '</span>'))); ?>
				</div>
				<div style="font-size: 13px;">
					<div style="clear: both; margin-top: 15px;">
						<?php echo image_tag('icon_change_password.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<a href="javascript:void(0);" onclick="$('change_password_form').toggle();"><?php echo __('Change my password'); ?></a>
					</div>
					<?php
					if (TBGSettings::getAuthenticationBackend() != 'tbg' && TBGSettings::getAuthenticationBackend() != null)
					{
						echo tbg_parse_text(TBGSettings::get('changepw_message'), null, null, array('embedded' => true));
					}
					else
					{
					?>
					<?php if ($tbg_user->canChangePassword()): ?>
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_change_password'); ?>" onsubmit="TBG.Main.Profile.changePassword('<?php echo make_url('account_change_password'); ?>'); return false;" method="post" id="change_password_form" style="display: none;">
						<div class="rounded_box white shadowed" style="position: absolute; padding: 5px 10px 5px 10px; font-size: 13px; width: 300px;" id="change_password_div">
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
									<div style="float: right; padding: 3px;"><?php echo __('%change_password% or %cancel%', array('%change_password%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'change_password_form\').hide();"><b>' . __('cancel') . '</b></a>')); ?></div>
									<input type="submit" value="<?php echo __('Change password'); ?>" style="font-weight: bold; float: right;">
									<span id="change_password_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
								</div>
						</div>
					</form>
					<?php else: ?>
					<div class="rounded_box white shadowed" style="position: absolute; padding: 5px 10px 5px 10px; font-size: 13px; width: 300px; display: none;" id="change_password_form">
							<b><?php echo __('Changing your password'); ?></b>
							<div style="font-size: 13px; margin-bottom: 10px;">
								<?php echo __('You\'re not allowed to change your password.'); ?>
								<br><?php echo __('Please contact your administrator to change it.'); ?>
							</div>
							<div class="smaller" style="text-align: right; margin: 10px 2px 5px 0; height: 23px;">
								<div style="float: right; padding: 3px;"><?php echo __('%cancel%', array('%cancel%' => '<a href="javascript:void(0);" onclick="$(\'change_password_form\').hide();"><b>' . __('cancel') . '</b></a>')); ?></div>
							</div>
					</div>
					<?php endif; ?>
					<?php
					}
					?>
					<div style="<?php if (!$tbg_user->usesGravatar()): ?>display: none; <?php endif; ?>clear: both; margin: 3px 0 15px 0;" id="gravatar_change">
						<?php echo image_tag('gravatar.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<?php echo link_tag('http://en.gravatar.com/emails/', __('Change my profile picture / avatar'), array('target' => '_blank')); ?>
						<p class="faded_out" style="font-size: 11px; padding-top: 3px;">
							<?php echo __('This will open up gravatar.com, which will let you change your avatar in The Bug Genie, and other webpages that uses Gravatar.'); ?>&nbsp;<?php echo link_tag('http://en.gravatar.com/', __('Read more ...'), array('target' => '_blank')); ?>
						</p>
					</div>
					<div style="clear: both; margin-top: 3px;">
						<?php echo image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<?php echo link_tag(make_url('my_reported_issues'), __("Show a list of all issues I've reported")); ?>
					</div>
					<div style="clear: both; margin-top: 3px;">
						<?php echo image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<?php echo link_tag(make_url('my_assigned_issues'), __("Show a list of all open issues assigned to me")); ?>
					</div>
					<div style="clear: both; margin-top: 3px;">
						<?php echo image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<?php echo link_tag(make_url('my_teams_assigned_issues'), __("Show a list of all open issues assigned to my teams")); ?>
					</div>
					<div style="clear: both; margin-top: 10px;">
						<?php echo image_tag('icon_user.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $tbg_user->getID())); ?>');"><?php echo __('Preview my user card'); ?></a>
					</div>
				</div>
			</div>
			<div style="margin: 10px 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php TBGEvent::createNew('core', 'account_left_bottom')->trigger(); ?>
		</td>
		<td valign="top" align="left" style="padding: 0 10px 0 5px;">
			<div style="margin: 10px 0 0 0; clear: both; height: 30px; width: 700px;" class="tab_menu">
				<ul id="account_tabs">
					<li class="selected" id="tab_profile"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_profile', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_users.png', array('style' => 'float: left;')).__('Profile information'); ?></a></li>
					<li id="tab_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_settings', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('Settings'); ?></a></li>
					<?php TBGEvent::createNew('core', 'account_tabs')->trigger(); ?>
					<?php foreach (TBGContext::getModules() as $module_name => $module): ?>
						<?php if ($module->hasAccountSettings()): ?>
							<li id="tab_settings_<?php echo $module_name; ?>"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_settings_<?php echo $module_name; ?>', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag($module->getAccountSettingsLogo(), array('style' => 'float: left;'), false, $module_name).$module->getAccountSettingsName(); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<div id="account_tabs_panes">
				<div id="tab_profile_pane">
					<?php
					if (TBGSettings::getAuthenticationBackend() != 'tbg' && TBGSettings::getAuthenticationBackend() != null)
					{
						echo tbg_parse_text(TBGSettings::get('changedetails_message'), null, null, array('embedded' => true));
					}
					else
					{
					?>
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_information'); ?>" onsubmit="TBG.Main.Profile.updateInformation('<?php echo make_url('account_save_information'); ?>'); return false;" method="post" id="profile_information_form">
						<div class="rounded_box borderless lightgrey cut_bottom" style="margin: 5px 0 0 0; width: 690px; border-bottom: 0;">
							<p class="content"><?php echo __('Edit your profile details here, including additional information.'); ?><br><?php echo __('Required fields are marked with a little star.'); ?></p>
							<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
								<tr>
									<td style="padding: 5px;"><label for="profile_buddyname">* <?php echo __('"Friendly" name / nickname'); ?></label></td>
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
										<input type="radio" name="email_private" value="1" id="profile_email_private_yes"<?php if ($tbg_user->isEmailPrivate()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_yes"><?php echo __('No'); ?></label>&nbsp;&nbsp;
										<input type="radio" name="email_private" value="0" id="profile_email_private_no"<?php if ($tbg_user->isEmailPublic()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_no"><?php echo __('Yes'); ?></label>
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
						<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 690px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
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
						<div class="rounded_box borderless lightgrey cut_bottom" style="margin: 5px 0 0 0; width: 690px; border-bottom: 0;">
							<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 200px; padding: 5px;"><label for="profile_use_gravatar_yes"><?php echo __('Use Gravatar avatar'); ?></label></td>
									<td>
										<input type="radio" name="use_gravatar" value="1" id="profile_use_gravatar_yes"<?php if ($tbg_user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_yes"><?php echo __('Yes'); ?></label><br>
										<input type="radio" name="use_gravatar" value="0" id="profile_use_gravatar_no"<?php if (!$tbg_user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_no"><?php echo __('No'); ?></label><br>
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
										<select name="timezone" id="profile_timezone" style="width: 150px;">
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
						<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 690px; border-top: 0;padding: 8px 5px 2px 5px; height: 25px;">
							<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your profile settings', array('%save%' => __('Save'))); ?></div>
							<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
							<span id="profile_settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
						</div>
					</form>
				</div>
				<?php TBGEvent::createNew('core', 'account_tab_panes')->trigger(); ?>
				<?php foreach (TBGContext::getModules() as $module_name => $module): ?>
					<?php if ($module->hasAccountSettings()): ?>
						<div id="tab_settings_<?php echo $module_name; ?>_pane" style="display: none;">
							<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_module_settings', array('target_module' => $module_name)); ?>" onsubmit="TBG.Main.Profile.updateModuleSettings('<?php echo make_url('account_save_module_settings', array('target_module' => $module_name)); ?>', '<?php echo $module_name; ?>'); return false;" method="post" id="profile_<?php echo $module_name; ?>_form">
								<div class="rounded_box borderless lightgrey cut_bottom" style="margin: 5px 0 0 0; width: 690px; border-bottom: 0;">
									<?php include_component("{$module_name}/accountsettings", array('module' => $module)); ?>
								</div>
								<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 690px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
									<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your %module_settings_name% settings', array('%save%' => __('Save'), '%module_settings_name%' => $module->getAccountSettingsName())); ?></div>
									<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
									<span id="profile_<?php echo $module_name; ?>_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
								</div>
							</form>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</td>
	</tr>
</table>