<div class="profile_buttons">
	<div class="button-group">
		<a style="<?php if (!$tbg_user->usesGravatar()): ?>display: none; <?php endif; ?>" id="gravatar_change" href="http://en.gravatar.com/emails/" class="button button-silver">
			<?php echo image_tag('gravatar.png'); ?>
			<?php echo __('Change my profile picture / avatar'); ?>
		</a>
		<?php if ($tbg_user->canChangePassword() && !$tbg_user->isOpenIdLocked()): ?>
			<a href="javascript:void(0);" onclick="var show = !$(this).hasClassName('button-pressed');TBG.Main.Profile.clearPopupsAndButtons(); if(show) { $(this).toggleClassName('button-pressed');$('change_password_div').toggle(); }" id="change_password_button" class="button button-silver"><?php echo __('Change my password'); ?></a>
		<?php elseif (!$tbg_user->isOpenIdLocked()): ?>
			<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('Changing password disabled'); ?>', '<?php echo __('Changing your password can not be done via this interface. Please contact your administrator to change your password.'); ?>')" class="button button-silver disabled"><?php echo __('Change my password'); ?></a>
		<?php endif; ?>
		<a class="button button-silver" id="security_key_button" type="button" href="javascript:void(0);" onclick="var show = !$(this).hasClassName('button-pressed');TBG.Main.Profile.clearPopupsAndButtons();if(show) { $(this).toggleClassName('button-pressed');$('security_key').toggle(); }"><?php echo __('My security key'); ?></a>
		<a class="button button-silver" id="more_actions_button" type="button" href="javascript:void(0);" onclick="var show = !$(this).hasClassName('button-pressed');TBG.Main.Profile.clearPopupsAndButtons();if(show) { $(this).toggleClassName('button-pressed');$('more_actions').toggle(); }"><?php echo image_tag('tab_search.png').__('Show my issues'); ?></a>
		<?php if ($tbg_user->isOpenIdLocked()): ?>
			<a href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('pick_username_div').toggle();" id="pick_username_button" class="button button-blue"><?php echo __('Pick a username'); ?></a>
		<?php endif; ?>
	</div>
	<div id="security_key" style="display: none; position: absolute; width: 350px; padding: 10px; top: 23px; right: 0; z-index: 1000;" class="rounded_box white shadowed popup_box">
		<?php echo __('Your security key is %securitykey%', array('%securitykey%' => '<b>'.TBGSettings::getRemoteSecurityKey().'</b>')); ?>
	</div>
	<ul id="more_actions" style="display: none; position: absolute; width: 300px; top: 23px; margin-top: 0; right: 0; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$('more_actions_button').toggleClassName('button-pressed');$('more_actions').toggle();">
		<li><?php echo link_tag(make_url('my_reported_issues'), image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')).__("Show issues I've reported")); ?></li>
		<li><?php echo link_tag(make_url('my_assigned_issues'), image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')).__("Show open issues assigned to me")); ?></li>
		<li><?php echo link_tag(make_url('my_teams_assigned_issues'), image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')).__("Show open issues assigned to my teams")); ?></li>
	</ul>
	<?php if ($tbg_user->isOpenIdLocked()): ?>
		<div class="rounded_box white shadowed popup_box"  style="display: none; position: absolute; right: 0; top: 23px; z-index: 100; padding: 5px 10px 5px 10px; font-size: 13px; width: 400px;" id="pick_username_div">
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
		<div class="rounded_box white shadowed popup_box"  style="display: none; position: absolute; right: 0; top: 23px; z-index: 100; padding: 5px 10px 5px 10px; font-size: 13px; width: 350px;" id="change_password_div">
			<?php if (TBGSettings::isUsingExternalAuthenticationBackend()): ?>
				<?php echo tbg_parse_text(TBGSettings::get('changepw_message'), null, null, array('embedded' => true)); ?>
			<?php else: ?>
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
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>