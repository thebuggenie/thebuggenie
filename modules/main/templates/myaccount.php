<?php

	$bugs_response->setTitle('My account');

?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="account_lefthand" style="vertical-align: top; width: 350px;">
			<?php BUGScontext::trigger('core', 'account_left_top'); ?>
			<div class="rounded_box iceblue_borderless" style="margin: 10px 0px 10px 10px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 14px;">
					<?php echo image_tag($bugs_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;'), true); ?>
					<strong><?php echo $bugs_user->getRealname(); ?></strong>
					<br />
					<span style="font-size: 12px;">
						(<?php echo $bugs_user->getUsername(); ?>)<br>
						<?php echo '<b>' . __('Status: %status%', array('%status%' => '</b>' . (($bugs_user->getState() instanceof BUGSuserstate) ? $bugs_user->getState()->getName() : '<span class="faded_medium">' . __('Unknown') . '</span>'))); ?>
					</span>
					<br />
					<div style="font-size: 13px;">
						<div style="clear: both; margin-top: 15px;">
							<?php echo image_tag('icon_change_password.png', array('style' => 'float: left; margin-right: 5px;')); ?>
							<?php echo link_tag('#', __('Change my password')); ?>
						</div>
						<div style="clear: both; margin-top: 3px;">
							<?php echo image_tag('tab_search.png', array('style' => 'float: left; margin-right: 5px;')); ?>
							<?php echo link_tag(make_url('search', array('searchfor' => '%%', 'issues_per_page' => 30, 'filters' => array('posted_by' => array('value' => $bugs_user->getID(), 'operator' => '=')))), __("Show a list of all issues I've reported")); ?>
						</div>
					</div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<div style="margin: 10px 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php BUGScontext::trigger('core', 'account_left_bottom'); ?>
		</td>
		<td valign="top" align="left" style="padding-right: 10px;">
		</td>
	</tr>
</table>
<?php

/*<script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/account.js"></script>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 225px;" valign="top">
<div style="margin-top: 0px;">
	<?php BUGScontext::trigger('core', 'account_left_top'); ?>
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
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="account.php" method="post" id="changepassword_form" onsubmit="return false">
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
	<?php if (!BUGScontext::getRequest()->hasParameter('settings')): ?>
		<tr>
		<td class="imgtd"><?php echo image_tag('icon_edit.png'); ?></td>
		<td><a href="javascript:void(0);" onclick="getEditAccount();"><?php echo __('Edit my account details'); ?></a></td>
		</tr>
	<?php endif; ?>
	<?php if (BUGScontext::getRequest()->hasCookie('b2_username_preswitch')): ?>
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

		foreach (BUGScontext::getModules() as $module)
		{
			if ($module->isEnabled() && $module->hasAccess())
			{
				if ($module->isVisibleInUsermenu())
				{
					?>
					<tr>
					<td class="imgtd"><?php echo image_tag('tab_' . $module->getName() . '.png', array(), false, $module->getName()); ?></td>
					<td><a href="<?php print BUGScontext::getTBGPath() . "modules/" . $module->getName() . "/" . $module->getName(); ?>.php"><?php print $module->getMenuTitle(); ?></a></td>
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
				<a href="<?php print BUGScontext::getTBGPath(); ?>account.php"><?php echo __('Account details'); ?></a>
			</li>
			<?php BUGScontext::trigger('core', 'account_settingslist'); ?>
		</ul>
	</td>
	</tr>
	</table>

	<div style="margin: 10px 10px 10px 10px;">
		<div style="margin: 2px 0 5px 0; padding: 3px; font-weight: bold; font-size: 13px; border-bottom: 1px solid #CCC;"><?php echo __('Friends'); ?></div>
		<table cellpadding=0 cellspacing=0 width="100%">
		<?php
	
			foreach (BUGScontext::getUser()->getFriends() as $friend)
			{
				echo include_component('main/userdropdown', array('user' => $friend));
			}
	
			if (count(BUGScontext::getUser()->getFriends()) == 0)
			{
				?><tr><td><?php echo __('Friends will appear here'); ?></td></tr><?php
			}
		?>
		</table>
	</div>
	<?php

	BUGScontext::trigger('core', 'account_left_middle');
	BUGScontext::trigger('core', 'account_left_bottom');

	?>
	</td>
	<td valign="top" align="left" style="padding-right: 10px;">
	<?php

	if (BUGScontext::getRequest()->getParameter('settings'))
	{
		BUGScontext::trigger('core', 'account_settings', BUGScontext::getRequest()->getParameter('settings'));
	}
	else
	{
		BUGScontext::trigger('core', 'account_right_top');
	
		?>
		<span id="account_main">
		<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 10px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo __('Welcome, %username%', array('%username%' => BUGScontext::getUser()->getRealname())); ?></b></td>
		<td style="background: url('<?php print "themes/" .  BUGSsettings::getThemeName(); ?>/clock.png') no-repeat right; text-align: right; padding: 3px; padding-right: 25px;"><?php print bugs_formatTime($_SERVER["REQUEST_TIME"], 3); ?></td>
		<td style="width: 7px;">&nbsp;</td>
		</tr>
		</table>
		<table style="table-layout: fixed; width: 100%; background-color: #FFF; margin-top: 7px; margin-left: 0px;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="padding: 0px; height: 70px; width: 70px; border: 1px solid #DDD; text-align: center;" align="right" valign="middle" id="avatar_td"><?php echo image_tag($bugs_user->getAvatarURL(false), array(), true); ?></td>
		<td style="padding-left: 10px; width: 250px;" valign="top">
		<b><?php echo __('Real name: %real_name%', array('%real_name%' => '')); ?></b> <?php print BUGScontext::getUser()->getRealname(); ?><br>
		<b><?php echo __('People see you as: %buddy_name%', array('%buddy_name%' => '')); ?></b> <?php print BUGScontext::getUser()->getBuddyname(); ?><br>
		<br>
		<?php echo __('You are currently: %user_state%', array('%user_state%' => '<b>' . BUGScontext::getUser()->getState()->getName() . '</b>')); ?>
		</td>
		<td style="width: 40px; text-align: left;" valign="middle"><a href="javascript:void(0);" onclick="getEditAccount();" style="font-size: 9px;">Edit</a></td>
		<td style="padding-left: 10px; width: auto;" valign="top">
		<?php if (BUGScontext::getUser()->getHomepage() != ""): ?>
			<b><?php echo __('Homepage: %link_to_homepage%', array('%link_to_homepage%' => '')); ?></b><a href="<?php echo BUGScontext::getUser()->getHomepage(); ?>" target="_blank"><?php echo BUGScontext::getUser()->getHomepage(); ?></a><br>
		<?php endif; ?>
		<?php if (BUGScontext::getUser()->getEmail() != ''): ?>
			<b><?php echo __('Email address: %email_address%', array('%email_address%' => '')); ?></b><a href="mailto:<?php print BUGScontext::getUser()->getEmail(); ?>"><?php print BUGScontext::getUser()->getEmail(); ?></a><br>
		<?php else: ?>
			<b style="color: #AAA;"><?php echo __('Email address not provided'); ?></b>
		<?php endif; ?>
		<div style="padding-top: 5px;" id="account_email">
		<?php echo (BUGScontext::getUser()->isEmailPrivate()) ? __('Your email-address is currently private, which means only you and staff members can view it.') : __('Your email-address is currently public, which means anyone can view it.'); ?><br>
		<a href="javascript:void(0);" onclick="setEmailPrivacy(<?php print (BUGScontext::getUser()->getEmailPrivacy()) ? 0 : 1; ?>);"><?php echo (BUGScontext::getUser()->getEmailPrivacy()) ? __('Make my email-address public') : __('Make my email-address private'); ?></a>
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
			
		$avatar_path = BUGSsettings::get('local_path') . 'avatars/';
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
	
		BUGScontext::trigger('core', 'account_right_bottom');
	}

?>
</td>
</tr>
</table>*/