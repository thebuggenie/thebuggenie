<?php

	TBGContext::loadLibrary('ui');

?>
<div class="logindiv">

<h1><?php echo __('Welcome to'); ?> <?php echo(TBGSettings::getTBGname()); ?></h1>
<?php echo __('Please fill in your username and password below, and press "Continue" to log in.'); ?>
<?php if (TBGSettings::get('allowreg') == true): ?> 
	<?php echo __('If you have not already registered, please use the "Register new account" link. It is completely free and takes only a minute.'); ?>
<?php else: ?>
	<?php echo __('It is not possible to register new accounts from this page. To register a new account, please contact the administrator.'); ?>
<?php endif; ?>
<br><br>

<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('login'); ?>" enctype="multipart/form-data" method="post" name="loginform">
<?php if (isset($login_error)): ?>
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<span  class="login_fieldheader"><?php echo $login_error; ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php endif; ?>

<div class="rounded_box iceblue">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Log in to an existing account'); ?></div>
		<div>
			<table border="0" class="login_fieldtable">
				<tr>
					<td><label class="login_fieldheader" for="b2_username"><?php echo __('Username'); ?></label></td>
					<td><input type="text" id="b2_username" name="b2_username" style="width: 200px;"></td>
				</tr>
				<tr>
					<td><label class="login_fieldheader" for="b2_password"><?php echo __('Password'); ?></label></td>
					<td><input type="password" id="b2_password" name="b2_password" style="width: 200px;"></td>
				</tr>
			</table>
			<br>
			<input type="submit" id="login_button" value="<?php echo __('Continue'); ?>">
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
</form>

<br>

<?php TBGContext::trigger('core', 'login_middle'); ?>

<?php if (TBGSettings::get('allowreg')): ?>

<?php if (TBGContext::hasMessage('account_activate')) {
TBGContext::clearMessage('account_activate');

if (TBGContext::hasMessage('activate_success')) {
TBGContext::clearMessage('activate_success'); ?>

<div class="rounded_box green_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
		<span class="login_fieldheader"><?php echo __('Thank you for registering!'); ?></span>
		<p><?php echo __('Your account has now been activated. Please log in by entering your username and password in the fields above.'); ?></p>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>

<?php } elseif (TBGContext::hasMessage('activate_failure')) {
TBGContext::clearMessage('activate_success'); ?>
	
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
		<span class="login_fieldheader"><?php echo __('There seems to be something wrong with your verification code.'); ?></span>
		<p><?php echo __('Please copy and paste the link from the activation email into your browser address bar, and try again.'); ?></p>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>

<?php }
}
else
{

if (TBGContext::hasMessage('prereg_error')) { ?>
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<span class="login_fieldheader"><?php echo TBGContext::getMessageAndClear('prereg_error'); ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php } ?>

<?php if (TBGContext::hasMessage('postreg_error')) { ?>
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<span class="login_fieldheader"><?php echo TBGContext::getMessageAndClear('postreg_error'); ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php } ?>

<?php if (TBGContext::hasMessage('postreg_success')) {
TBGContext::clearMessage('postreg_success'); ?>
<div class="rounded_box green_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
		<span class="login_fieldheader"><?php echo __('Thank you for registering!'); ?></span>
		<p><?php echo __('The account has now been registered - check your email inbox for the activation email. Please be patient - this email can take up to two hours to arrive.'); ?></p>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php } elseif (TBGContext::hasMessage('prereg_success')) { ?>
<div class="rounded_box gray">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 10px;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register2'); ?>" enctype="multipart/form-data" method="post" name="registerform">
		<input type="hidden" name="register" value="true">
		<input type="hidden" name="username" value="<?php echo TBGContext::getMessageAndClear('prereg_success'); ?>">
			<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
			<div>
				<?php echo __('The username you requested is available. To register it, please fill out the information below.'); ?>
				<i>(<?php echo __('Required information is marked with an asterisk'); ?>: <b>*</b>)</i><br><br>
				<table border="0" class="login_fieldtable">
					<tr>
						<td><label class="login_fieldheader" for="buddyname">*&nbsp;<?php echo __('Buddy name'); ?></label></td>
						<td><input type="text" id="buddyname" name="buddyname" value="<?php print TBGContext::getRequest()->getParameter('buddyname'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td><label class="login_fieldheader" for="realname"><?php echo __('Real name'); ?></label></td>
						<td><input type="text" id="realname" name="realname" value="<?php print TBGContext::getRequest()->getParameter('realname'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td><label class="login_fieldheader" for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label></td>
						<td><input type="text" id="email_address" name="email_address" value="<?php print TBGContext::getRequest()->getParameter('email_address'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td><label class="login_fieldheader" for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label></td>
						<td><input type="text" id="email_confirm" name="email_confirm" value="<?php print TBGContext::getRequest()->getParameter('email_confirm'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
					</tr>
				</table>
				<br>

				<label class="login_fieldheader" for="verification_no">*&nbsp;<?php echo __('Security check'); ?></label><br><br>
				<?php
					$_SESSION['activation_number'] = bugs_printRandomNumber();
				?>

				<br><b><?php echo __('Enter the above number in this box'); ?></b><br><br>
				<input type="text" id="verification_no" name="verification_no" style="width: 100px;<?php print (!true) ? " background-color: #FBB;" : ""; ?>"><br><br>
				<input type="submit" id="login_button" value="<?php echo __('Register'); ?>">
			</div>
		</form>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php } else { ?>

<div class="rounded_box gray">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 10px;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register1'); ?>" enctype="multipart/form-data" method="post" name="registerform">
		<input type="hidden" name="register" value="true">
			<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
			<div>
				<label style="font-size: larger; font-weight: bold; padding-bottom: 5px;" for="desired_username"><?php echo __('Desired username'); ?></label>&nbsp;
				<input type="text" id="desired_username" name="desired_username" style="width: 200px;"><br><br>
			
				<input type="submit" id="login_button" value="<?php echo __('Check availability'); ?>">
			</div>
		</form>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php } } endif; ?>
