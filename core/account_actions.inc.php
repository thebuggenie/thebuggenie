<?php

	$page = "account";
	define ('BUGS2_INCLUDE_PATH', '../');
	
	require BUGS2_INCLUDE_PATH . 'include/checkcookie.inc.php';
	require BUGS2_INCLUDE_PATH . "include/b2_engine.inc.php";
	require BUGScontext::getIncludePath() . "include/ui_functions.inc.php";
	
	if (!BUGScontext::getRequest()->isAjaxCall())
	{
		exit();
	}
	
	header ("Content-Type: text/html; charset=" . BUGScontext::getI18n()->getCharset());
	
	if (BUGScontext::getRequest()->getParameter('saveaccountdetails'))
	{
		BUGScontext::getUser()->updateUserDetails(BUGScontext::getRequest()->getParameter('realname'), 
												  BUGScontext::getRequest()->getParameter('buddyname'),
												  BUGScontext::getRequest()->getParameter('homepage'),
												  BUGScontext::getRequest()->getParameter('email'));
	}
	if (BUGScontext::getRequest()->getParameter('set_avatar'))
	{
		$avatar_path = BUGSsettings::get('local_path') . 'avatars/';
		$newavatar_path = pathinfo(BUGSsettings::get('local_path') . 'avatars/' . BUGScontext::getRequest()->getParameter('set_avatar') . '.png');
		$newavatar_path = $newavatar_path['dirname'] . '/';
		if ($avatar_path == $newavatar_path)
		{
			BUGScontext::getUser()->setAvatar(BUGScontext::getRequest()->getParameter('set_avatar'));
			echo image_tag('avatars/' . BUGScontext::getUser()->getAvatar() . '.png', '', '', '', 0, 0, 0, true);
		}
		exit();
	}

?>
<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 15px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
<tr>
<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo __('Welcome, %username%', array('%username%' => BUGScontext::getUser()->getRealname())); ?></b></td>
<td style="background: url('<?php print "themes/" .  BUGSsettings::getThemeName(); ?>/clock.png') no-repeat right; text-align: right; padding: 3px; padding-right: 25px;"><?php print bugs_formatTime($_SERVER["REQUEST_TIME"], 3); ?></td>
<td style="width: 7px;">&nbsp;</td>
</tr>
</table>
<table style="table-layout: fixed; width: <?php echo (BUGScontext::getRequest()->getParameter('edit_details')) ? '700px' : 'auto'; ?>; background-color: #FFF; margin-top: 7px; margin-left: 0px;" cellpadding=0 cellspacing=0>
<tr>
<td style="padding: 0px; height: 70px; width: 70px; border: 1px solid #DDD; text-align: center;" align="right" valign="middle"><?php echo image_tag('avatars/' . BUGScontext::getUser()->getAvatar() . '.png', '', '', '', 0, 0, 0, true); ?></td>

<?php
	
if (BUGScontext::getRequest()->getParameter('edit_details'))
{
	?>
	<td style="padding-left: 10px; width: auto;" valign="top">
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="account.php" method="post" id="update_account_form" onsubmit="return false">
	<input type="hidden" name="saveaccountdetails" value="true">
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<tr>
	<td style="width: 110px;"><b><?php echo __('Real name: %real_name%', array('%real_name%' => '')); ?></b></td>
	<td style="width: 150px;"><input type="text" style="width: 100%;" name="realname" value="<?php print BUGScontext::getUser()->getRealname(); ?>"></td>
	<td style="width: 10px;">&nbsp;</td>
	<td style="width: 110px;"><b><?php echo __('Homepage: %link_to_homepage%', array('%link_to_homepage%' => '')); ?></b></td>
	<td style="width: auto;"><input type="text" style="width: 100%;" name="homepage" value="<?php print BUGScontext::getUser()->getHomepage(); ?>"></td>
	</tr>
	<tr>
	<td><b><?php echo __('People see you as: %buddy_name%', array('%buddy_name%' => '')); ?></b></td>
	<td><input type="text" style="width: 100%;" name="buddyname" value="<?php print BUGScontext::getUser()->getBuddyname(); ?>"></td>
	<td></td>
	<td><b><?php echo __('Email address: %email_address%', array('%email_address%' => '')); ?></b></td>
	<td><input type="text" style="width: 100%;" name="email" value="<?php print BUGScontext::getUser()->getEmail(); ?>"></td>
	</tr>
	<tr>
	<td style="padding-top: 5px;"><a href="javascript:void(0);" onclick="getAccountInfo();" style="font-size: 9px;"><?php echo __('Never mind'); ?></a></td>
	<td style="padding-top: 5px; text-align: right;" colspan=4><button onclick="saveAccountInfo();" style="width: 40px;"><?php echo __('Save'); ?></button></td>
	</tr>
	</table>
	</form>
	<?php
}
else
{
	?>
	<td style="padding-left: 10px; width: 250px;" valign="top">
	<b><?php echo __('Real name: %real_name%', array('%real_name%' => '')); ?></b> <?php print BUGScontext::getUser()->getRealname(); ?><br>
	<b><?php echo __('People see you as: %buddy_name%', array('%buddy_name%' => '')); ?></b> <?php print BUGScontext::getUser()->getBuddyname(); ?><br>
	<br>
	<?php echo __('You are currently: %user_state%', array('%user_state%' => '<b>' . BUGScontext::getUser()->getState()->getName() . '</b>'));
}

?>
</td>
<?php if (!BUGScontext::getRequest()->getParameter('edit_details')): ?>
	<td style="width: 40px; text-align: left;" valign="middle"><a href="javascript:void(0);" onclick="getEditAccount();" style="font-size: 9px;"><?php echo __('Edit'); ?></a></td>
<?php endif; ?>
<?php

if (!BUGScontext::getRequest()->getParameter('edit_details'))
{
	?>
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
	<?php
}

?>
</tr>
</table>