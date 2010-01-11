<?php

	$tbg__response->setTitle('My account');

?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="account_lefthand" style="vertical-align: top; width: 350px;">
			<?php TBGContext::trigger('core', 'account_left_top'); ?>
			<div class="rounded_box iceblue_borderless" style="margin: 10px 0px 10px 10px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 14px;">
					<?php echo image_tag($tbg__user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;'), true); ?>
					<strong><?php echo $tbg__user->getRealname(); ?></strong>
					<br />
					<span style="font-size: 12px;">
						(<?php echo $tbg__user->getUsername(); ?>)<br>
						<?php echo '<b>' . __('Status: %status%', array('%status%' => '</b>' . (($tbg__user->getState() instanceof TBGUserstate) ? $tbg__user->getState()->getName() : '<span class="faded_medium">' . __('Unknown') . '</span>'))); ?>
					</span>
					<br />
					<div style="font-size: 13px;">
						<div style="clear: both; margin-top: 15px;">
							<?php echo image_tag('icon_change_password.png', array('style' => 'float: left; margin-right: 5px;')); ?>
							<a href="javascript:void(0);" onclick="$('change_password_form').toggle();"><?php echo __('Change my password'); ?></a>
						</div>
						<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_change_password'); ?>" onsubmit="changePassword('<?php echo make_url('account_change_password'); ?>'); return false;" method="post" id="change_password_form" style="display: none;">
							<div class="rounded_box white" style="margin: 10px 0 10px 0;" id="change_password_div">
								<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
								<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 14px;">
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
								<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
							</div>
						</form>
						<div style="<?php if (!$tbg__user->usesGravatar()): ?>display: none; <?php endif; ?>clear: both; margin: 3px 0 15px 0;" id="gravatar_change">
							<?php echo image_tag('gravatar.png', array('style' => 'float: left; margin-right: 5px;')); ?>
							<?php echo link_tag('http://en.gravatar.com/emails/', __('Change my profile picture / avatar'), array('target' => '_blank')); ?>
						</div>
						<div style="clear: both; margin-top: 3px;">
							<?php echo image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')); ?>
							<?php echo link_tag(make_url('search', array('searchfor' => '%%', 'issues_per_page' => 30, 'filters' => array('posted_by' => array('value' => $tbg__user->getID(), 'operator' => '=')))), __("Show a list of all issues I've reported")); ?>
						</div>
					</div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<div style="margin: 10px 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php TBGContext::trigger('core', 'account_left_bottom'); ?>
		</td>
		<td valign="top" align="left" style="padding: 0 10px 0 5px;">
			<div style="margin: 10px 0 0 0; clear: both; height: 30px;" class="tab_menu">
				<ul id="account_tabs">
					<li class="selected" id="tab_profile"><a onclick="switchSubmenuTab('tab_profile', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_users.png', array('style' => 'float: left;')).__('My profile'); ?></a></li>
					<li id="tab_settings"><a onclick="switchSubmenuTab('tab_settings', 'account_tabs');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('Settings'); ?></a></li>
					<?php TBGContext::trigger('core', 'account_tabs'); ?>
				</ul>
			</div>
			<div id="account_tabs_panes">
				<div id="tab_profile_pane">
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_information'); ?>" onsubmit="updateProfileInformation('<?php echo make_url('account_save_information'); ?>'); return false;" method="post" id="profile_information_form">
						<div class="rounded_box borderless" style="margin: 5px 0 0 0; width: 700px;">
							<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
							<div class="xboxcontent" style="padding: 5px;">
								<p class="content"><?php echo __('Edit your profile details here, including additional information.'); ?><br><?php echo __('Required fields are marked with a little star.'); ?></p>
								<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
									<tr>
										<td style="padding: 5px;"><label for="profile_buddyname">* <?php echo __('"Friendly" name / nickname'); ?></label></td>
										<td>
											<input type="text" name="buddyname" id="profile_buddyname" value="<?php echo $tbg__user->getBuddyname(); ?>" style="width: 200px;">
										</td>
									</tr>
									<tr>
										<td class="config_explanation" colspan="2"><?php echo __('This is the name used across the site for your profile.'); ?></td>
									</tr>
									<tr>
										<td style="padding: 5px;"><label for="profile_realname"><?php echo __('Full name'); ?></label></td>
										<td>
											<input type="text" name="realname" id="profile_realname" value="<?php echo $tbg__user->getRealname(); ?>" style="width: 300px;">
										</td>
									</tr>
									<tr>
										<td class="config_explanation" colspan="2"><?php echo __('This is your real name, mostly used in communication with you, and rarely shown to others'); ?></td>
									</tr>
									<tr>
										<td style="padding: 5px;"><label for="profile_email">* <?php echo __('Email address'); ?></label></td>
										<td>
											<input type="text" name="email" id="profile_email" value="<?php echo $tbg__user->getEmail(); ?>" style="width: 300px;">
										</td>
									</tr>
									<tr>
										<td style="padding: 5px;"><label for="profile_email_private_yes">* <?php echo __('Show my email address to others'); ?></label></td>
										<td>
											<input type="radio" name="email_private" value="1" id="profile_email_private_yes"<?php if ($tbg__user->isEmailPrivate()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_yes"><?php echo __('No'); ?></label>&nbsp;&nbsp;
											<input type="radio" name="email_private" value="0" id="profile_email_private_no"<?php if ($tbg__user->isEmailPublic()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_no"><?php echo __('Yes'); ?></label>
										</td>
									</tr>
									<tr>
										<td class="config_explanation" colspan="2"><?php echo __('Whether your email address is visible to other users in your profile information card. The email address is always visible to admins.'); ?></td>
									</tr>
									<tr>
										<td style="padding: 5px;"><label for="profile_homepage"><?php echo __('Homepage'); ?></label></td>
										<td>
											<input type="text" name="homepage" id="profile_homepage" value="<?php echo $tbg__user->getHomepage(); ?>" style="width: 300px;">
										</td>
									</tr>
									<tr>
										<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="rounded_box iceblue_borderless" style="margin: 0 0 5px 0; width: 700px;">
							<div class="xboxcontent" style="padding: 8px 5px 2px 5px; height: 23px;">
								<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your account information', array('%save%' => __('Save'))); ?></div>
								<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
								<span id="profile_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
							</div>
							<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
						</div>
					</form>
				</div>
				<div id="tab_settings_pane" style="display: none;">
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_settings'); ?>" onsubmit="updateProfileSettings('<?php echo make_url('account_save_settings'); ?>'); return false;" method="post" id="profile_settings_form">
						<div class="rounded_box borderless" style="margin: 5px 0 0 0; width: 700px;">
							<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
							<div class="xboxcontent" style="padding: 5px;">
								<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
									<tr>
										<td style="width: 200px; padding: 5px;"><label for="profile_use_gravatar_yes"><?php echo __('Use Gravatar avatar'); ?></label></td>
										<td>
											<input type="radio" name="use_gravatar" value="1" id="profile_use_gravatar_yes"<?php if ($tbg__user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_yes"><?php echo __('Yes'); ?></label><br>
											<input type="radio" name="use_gravatar" value="0" id="profile_use_gravatar_no"<?php if (!$tbg__user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_no"><?php echo __('No'); ?></label><br>
										</td>
									</tr>
									<tr>
										<td class="config_explanation" colspan="2">
											<?php echo __("The Bug Genie can use your <a href=\"http://www.gravatar.com\" target=\"_blank\">Gravatar</a> profile picture, if you have one. If you don't have one but still want to use Gravatar for profile pictures, The Bug Genie will use a Gravatar <a href=\"http://blog.gravatar.com/2008/04/22/identicons-monsterids-and-wavatars-oh-my/\" target=\"_blank\">auto-generated image unique for your email address</a>."); ?><br>
											<br>
											<?php echo __("Don't have a Gravatar yet? %link_to_get_one_now%", array('%link_to_get_one_now%' => link_tag('http://en.gravatar.com/site/signup/'.urlencode($tbg__user->getEmail()), __('Get one now!'), array('target' => '_blank')))); ?>
										</td>
									</tr>
									<tr>
										<td style="width: 200px; padding: 5px;"><label for="profile_timezone"><?php echo __('Current timezone'); ?></label></td>
										<td>
											<select name="timezone" id="profile_timezone" style="width: 150px;">
												<?php for ($cc = 12;$cc >= 1;$cc--): ?>
													<option value="-<?php echo $cc; ?>"<?php if ($tbg__user->getTimezone() == -$cc): ?> selected<?php endif; ?>>GMT -<?php echo $cc; ?></option>
												<?php endfor; ?>
												<option value="0"<?php if ($tbg__user->getTimezone() == 0): ?> selected<?php endif; ?>>GMT/UTC</option>
												<?php for ($cc = 1;$cc <= 12;$cc++): ?>
													<option value="<?php echo $cc; ?>"<?php if ($tbg__user->getTimezone() == $cc): ?> selected<?php endif; ?>>GMT +<?php echo $cc; ?></option>
												<?php endfor; ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="config_explanation" colspan="2">
											<?php echo __('This setting is used to display issues, comments and more in your local timezone.'); ?><br>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="rounded_box iceblue_borderless" style="margin: 0 0 5px 0; width: 700px;">
							<div class="xboxcontent" style="padding: 8px 5px 2px 5px; height: 23px;">
								<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save your profile settings', array('%save%' => __('Save'))); ?></div>
								<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
								<span id="profile_settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
							</div>
							<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
						</div>
					</form>
				</div>
				<?php TBGContext::trigger('core', 'account_tab_panes'); ?>
			</div>
		</td>
	</tr>
</table>
<?php

/*<script type="text/javascript" src="<?php echo TBGContext::getTBGPath(); ?>js/account.js"></script>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 225px;" valign="top">
<div style="margin-top: 0px;">
	<?php TBGContext::trigger('core', 'account_left_top'); ?>
	<table class="b2_section_microframe" cellpadding=0 cellspacing=0>
	<tr>
	<td class="b2_section_microframe_header"><?php echo __('Common actions'); ?></td>
	</tr>
	<tr>
	<td class="td1">
	<table cellpadding=0 cellspacing=0 width="100%">
	<tr>
	<td class="imgtd"><?php echo image_tag('logout.png'); ?></td>
	<td><?php echo link_tag(make_url('logout'), __('Log out')); ?></td>
	</tr>
	<tr>
	<td class="imgtd"><?php echo image_tag('icon_userstate.png'); ?></td>
	<td><a href="javascript:void(0);" onclick="$('availStatus').show();getUserStateList();"><?php echo __('Change my status to'); ?></a></td>
	</tr>
	<tr id="availStatus" style="display: none;"><td>&nbsp;</td><td><span id="user_statelist"></span>
	<div align="right" class="small"><a href="javascript:void(0);" onclick="$('availStatus').hide();" style="font-size: 10px;"><?php echo __('Never mind'); ?></a></div></td>
	</tr>
	<tr>
	<td class="imgtd"><?php echo image_tag('acct_icon_pwd.png'); ?></td>
	<td><a href="javascript:void(0);" onclick="$('newPass').show();"><?php echo __('Change my password'); ?></a></td>
	</tr>
	<tr><td colspan=2><span id="password_changed_span"></span></td></tr>
	<tr id="newPass" style="display: none;"><td colspan=2>
	<div style="padding: 2px;"><?php echo __('Enter your current password, and your new pasword twice to change'); ?>:<br>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="account.php" method="post" id="changepassword_form" onsubmit="return false">
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<tr><td style="width: 50px;"><b style="font-size: 10px;"><?php echo __('Current: %password%', array('%password%' => '')); ?></b></td><td style="width: auto;"><input type="password" class="text" name="old_pass" style="width: 100%;"></td></tr>
	<tr><td style="width: 50px;"><b style="font-size: 10px;"><?php echo __('New: %password%', array('%password%' => '')); ?></b></td><td style="width: auto;"><input type="password" class="text" name="new_pass1" style="width: 100%;"></td></tr>
	<tr><td style="width: 50px;"><b style="font-size: 10px;"><?php echo __('Repeat: %password%', array('%password%' => '')); ?></b></td><td style="width: auto;"><input type="password" class="text" name="new_pass2" style="width: 100%;"></td></tr>
	<tr><td style="text-align: right;" colspan=2><input type="submit" onclick="submitNewPassword();" value="<?php echo __('Change'); ?>" style="width: 50px;"></td></tr>
	</table>
	</form>
	</div>
	<div align="right" class="small"><a href="javascript:void(0);" onclick="$('newPass').toggle();"><?php echo __('Cancel'); ?></a></div>
	</td>
	</tr>
	<?php if (!TBGContext::getRequest()->hasParameter('settings')): ?>
		<tr>
		<td class="imgtd"><?php echo image_tag('icon_edit.png'); ?></td>
		<td><a href="javascript:void(0);" onclick="getEditAccount();"><?php echo __('Edit my account details'); ?></a></td>
		</tr>
	<?php endif; ?>
	<?php if (TBGContext::getRequest()->hasCookie('b2_username_preswitch')): ?>
		<tr>
		<td class="imgtd"><?php echo image_tag('switchuser.png'); ?></td>
		<td style="text-align: left;"><a href="login_validate.inc.php?switch_user=true"><?php echo __('Switch back to original user'); ?></a></td>
		</tr>
	<?php endif; ?>
	</table>
	</td>
	</tr>
	</table>

	<table class="b2_section_microframe" cellpadding=0 cellspacing=0>
	<tr>
	<td class="b2_section_microframe_header"><?php echo __('Places'); ?></td>
	</tr>
	<tr>
	<td class="td1">
	<table cellpadding=0 cellspacing=0 width="100%">
	<?php

		foreach (TBGContext::getModules() as $module)
		{
			if ($module->isEnabled() && $module->hasAccess())
			{
				if ($module->isVisibleInUsermenu())
				{
					?>
					<tr>
					<td class="imgtd"><?php echo image_tag('tab_' . $module->getName() . '.png', array(), false, $module->getName()); ?></td>
					<td><a href="<?php print TBGContext::getTBGPath() . "modules/" . $module->getName() . "/" . $module->getName(); ?>.php"><?php print $module->getMenuTitle(); ?></a></td>
					</tr>
					<?php
				}
			}
		}

	?>
	</table>
	</td>
	</tr>
	</table>

	<table class="b2_section_microframe" cellpadding=0 cellspacing=0>
	<tr>
	<td class="b2_section_microframe_header"><?php echo __('Settings'); ?></td>
	</tr>
	<tr>
	<td class="td1 settings_list">
		<ul>
			<li>
				<?php echo image_tag('tab_account.png', array('style' => 'float: left; margin-right: 5px;')); ?>
				<a href="<?php print TBGContext::getTBGPath(); ?>account.php"><?php echo __('Account details'); ?></a>
			</li>
			<?php TBGContext::trigger('core', 'account_settingslist'); ?>
		</ul>
	</td>
	</tr>
	</table>

	<div style="margin: 10px 10px 10px 10px;">
		<div style="margin: 2px 0 5px 0; padding: 3px; font-weight: bold; font-size: 13px; border-bottom: 1px solid #CCC;"><?php echo __('Friends'); ?></div>
		<table cellpadding=0 cellspacing=0 width="100%">
		<?php
	
			foreach (TBGContext::getUser()->getFriends() as $friend)
			{
				echo include_component('main/userdropdown', array('user' => $friend));
			}
	
			if (count(TBGContext::getUser()->getFriends()) == 0)
			{
				?><tr><td><?php echo __('Friends will appear here'); ?></td></tr><?php
			}
		?>
		</table>
	</div>
	<?php

	TBGContext::trigger('core', 'account_left_middle');
	TBGContext::trigger('core', 'account_left_bottom');

	?>
	</td>
	<td valign="top" align="left" style="padding-right: 10px;">
	<?php

	if (TBGContext::getRequest()->getParameter('settings'))
	{
		TBGContext::trigger('core', 'account_settings', TBGContext::getRequest()->getParameter('settings'));
	}
	else
	{
		TBGContext::trigger('core', 'account_right_top');
	
		?>
		<span id="account_main">
		<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 10px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo __('Welcome, %username%', array('%username%' => TBGContext::getUser()->getRealname())); ?></b></td>
		<td style="background: url('<?php print "themes/" .  TBGSettings::getThemeName(); ?>/clock.png') no-repeat right; text-align: right; padding: 3px; padding-right: 25px;"><?php print tbg__formatTime($_SERVER["REQUEST_TIME"], 3); ?></td>
		<td style="width: 7px;">&nbsp;</td>
		</tr>
		</table>
		<table style="table-layout: fixed; width: 100%; background-color: #FFF; margin-top: 7px; margin-left: 0px;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="padding: 0px; height: 70px; width: 70px; border: 1px solid #DDD; text-align: center;" align="right" valign="middle" id="avatar_td"><?php echo image_tag($tbg__user->getAvatarURL(false), array(), true); ?></td>
		<td style="padding-left: 10px; width: 250px;" valign="top">
		<b><?php echo __('Real name: %real_name%', array('%real_name%' => '')); ?></b> <?php print TBGContext::getUser()->getRealname(); ?><br>
		<b><?php echo __('People see you as: %buddy_name%', array('%buddy_name%' => '')); ?></b> <?php print TBGContext::getUser()->getBuddyname(); ?><br>
		<br>
		<?php echo __('You are currently: %user_state%', array('%user_state%' => '<b>' . TBGContext::getUser()->getState()->getName() . '</b>')); ?>
		</td>
		<td style="width: 40px; text-align: left;" valign="middle"><a href="javascript:void(0);" onclick="getEditAccount();" style="font-size: 9px;">Edit</a></td>
		<td style="padding-left: 10px; width: auto;" valign="top">
		<?php if (TBGContext::getUser()->getHomepage() != ""): ?>
			<b><?php echo __('Homepage: %link_to_homepage%', array('%link_to_homepage%' => '')); ?></b><a href="<?php echo TBGContext::getUser()->getHomepage(); ?>" target="_blank"><?php echo TBGContext::getUser()->getHomepage(); ?></a><br>
		<?php endif; ?>
		<?php if (TBGContext::getUser()->getEmail() != ''): ?>
			<b><?php echo __('Email address: %email_address%', array('%email_address%' => '')); ?></b><a href="mailto:<?php print TBGContext::getUser()->getEmail(); ?>"><?php print TBGContext::getUser()->getEmail(); ?></a><br>
		<?php else: ?>
			<b style="color: #AAA;"><?php echo __('Email address not provided'); ?></b>
		<?php endif; ?>
		<div style="padding-top: 5px;" id="account_email">
		<?php echo (TBGContext::getUser()->isEmailPrivate()) ? __('Your email-address is currently private, which means only you and staff members can view it.') : __('Your email-address is currently public, which means anyone can view it.'); ?><br>
		<a href="javascript:void(0);" onclick="setEmailPrivacy(<?php print (TBGContext::getUser()->getEmailPrivacy()) ? 0 : 1; ?>);"><?php echo (TBGContext::getUser()->getEmailPrivacy()) ? __('Make my email-address public') : __('Make my email-address private'); ?></a>
		</div> 
		</td>
		</tr>
		</table>
		</span>
		<div style="width: 70px; text-align: center;"><a href="javascript:void(0);" onclick="Effect.Appear('avatarlist', { duration: 0.5 });" style="font-size: 9px; padding: 3px;"><?php echo __('Change'); ?></a></div>
		<div style="position: absolute; border: 1px solid #DDD; padding: 5px; background-color: #FFF; display: none; width: 200px; text-align: left;" id="avatarlist">
		<b><?php echo __('Change avatar'); ?></b><br>
		<?php echo __('To change your avatar image, select your new avatar from the list below'); ?>
		<div style="height: 300px; overflow: auto;">
		<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
		<?php
			
		$avatar_path = TBGSettings::get('local_path') . 'avatars/';
		$avatar_path_handle = opendir($avatar_path);
		$avatars = array();
		while ($avatar = readdir($avatar_path_handle))
		{
			if (strlen(strstr($avatar, '.')) != strlen($avatar) && strstr($avatar, '_small.') == '') 
			{ 
				$avatar = substr($avatar, 0, strripos($avatar, '.png'));
				?><tr>
				<td style="width: 30px; padding-top: 2px; padding-bottom: 2px; vertical-align: center;"><div style="text-align: center; border: 1px solid #DDD; padding: 1px;"><?php echo image_tag('avatars/' . $avatar . '_small.png', '', '', '', 0, 0, 0, true); ?></div></td>
				<td style="padding: 3px;"><a href="javascript:void(0);" onclick="setAvatar('<?php echo $avatar; ?>');"><?php echo __('Switch avatar to %avatar_name%', array('%avatar_name%' => '<b>' . $avatar . '</b>')); ?></a></td>
				</tr><?php
			}
		}
			
		?>
		</table>
		</div>
		<div style="text-align: right;"><a href="javascript:void(0);" onclick="Effect.Fade('avatarlist', { duration: 0.5 });" style="font-size: 9px; padding: 3px;"><?php echo __('Never mind'); ?></a></div>
		</div>
		<?php
	
		TBGContext::trigger('core', 'account_right_bottom');
	}

?>
</td>
</tr>
</table>*/