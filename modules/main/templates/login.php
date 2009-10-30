<?php

	BUGScontext::loadLibrary('ui');

?>
<div class="logindiv">

<h1><?php echo __('Welcome to'); ?> <?php echo(BUGSsettings::getTBGname()); ?></h1>
<?php echo __('Please fill in your username and password below, and press "Continue" to log in.'); ?>
<?php if (BUGSsettings::get('allowreg') == true): ?> 
	<?php echo __('If you have not already registered, please use the "Register new account" link. It is completely free and takes only a minute.'); ?>
<?php else: ?>
	<?php echo __('It is not possible to register new accounts from this page. To register a new account, please contact the administrator.'); ?>
<?php endif; ?>
<br><br>

<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('login'); ?>" enctype="multipart/form-data" method="post" name="loginform">
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

<?php if (BUGScontext::hasMessage('forgot_error')) { ?>
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<span class="login_fieldheader"><?php echo BUGScontext::getMessageAndClear('forgot_error'); ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php } ?>

<?php if (BUGScontext::hasMessage('forgot_success')) {
BUGScontext::clearMessage('forgot_success'); ?>
<div class="rounded_box green_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Forgot password?'); ?></div>
		<span class="login_fieldheader"><?php echo BUGScontext::getMessageAndClear('forgot_success'); ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php } else { ?>

<div class="rounded_box gray">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('forgot'); ?>" enctype="multipart/form-data" method="post" name="lostpasswordform">
	<input type="hidden" name="lostpassword" value="true">
		<div class="login_boxheader"><?php echo __('Forgot password?'); ?></div>
			<p><?php echo __('If you have forgot your password, enter your username here, and we will send you an email that will allow you to change your username'); ?>.</p><br>
			<div>
				<label class="login_fieldheader" for="forgot_password_username"><?php echo __('Username'); ?></label>&nbsp;
				<input type="text" id="forgot_password_username" name="forgot_password_username" style="width: 200px"><br><br>
			
				<input type="submit" id="login_button" value="<?php echo __('Send email'); ?>">
			</div>
			
		<?php if (false): ?>
			<div style="padding: 3px;"><?php echo __('Your password has been reset, and an email has been sent to the email address you provided when you registered, with your new login details'); ?>.</div>
			<div style="padding: 3px;"><?php echo __('An email has been sent to the email address you provided when you registered, with instructions on how to reset your password'); ?>.</div>
		<?php endif; ?>
	</form>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>

<?php } ?>

<?php if (BUGSsettings::get('allowreg')): ?>

<?php if (BUGScontext::hasMessage('prereg_error')) { ?>
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<span class="login_fieldheader"><?php echo BUGScontext::getMessageAndClear('prereg_error'); ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php } ?>

<?php if (BUGScontext::hasMessage('postreg_error')) { ?>
<div class="rounded_box red_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<span class="login_fieldheader"><?php echo BUGScontext::getMessageAndClear('postreg_error'); ?></span>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<br>
<?php } ?>

<?php if (BUGScontext::hasMessage('postreg_success')) {
BUGScontext::clearMessage('postreg_success'); ?>
<div class="rounded_box green_borderless">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
		<span class="login_fieldheader"><?php echo __('Thank you for registering!'); ?></span>
		<p><?php echo __('The account has now been registered - check your email inbox for the activation email. Please be patient - this email can take up to two hours to arrive.'); ?></p>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php } elseif (BUGScontext::hasMessage('prereg_success')) {
BUGScontext::clearMessage('prereg_success'); ?>
<div class="rounded_box gray">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 10px;">
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register2'); ?>" enctype="multipart/form-data" method="post" name="registerform">
		<input type="hidden" name="register" value="true">
		<input type="hidden" name="username" value="<?php BUGScontext::getRequest()->getParameter('desired_username'); ?>">
			<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
			<div>
				<?php echo __('The username you requested is available. To register it, please fill out the information below.'); ?>
				<i>(<?php echo __('Required information is marked with an asterisk'); ?>: <b>*</b>)</i><br><br>
				<table border="0" class="login_fieldtable">
					<tr>
						<td><label class="login_fieldheader" for="buddyname">*&nbsp;<?php echo __('Buddy name'); ?></label></td>
						<td><input type="text" id="buddyname" name="buddyname" value="<?php print BUGScontext::getRequest()->getParameter('buddyname'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td><label class="login_fieldheader" for="realname"><?php echo __('Real name'); ?></label></td>
						<td><input type="text" id="realname" name="realname" value="<?php print BUGScontext::getRequest()->getParameter('realname'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td><label class="login_fieldheader" for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label></td>
						<td><input type="text" id="email_address" name="email_address" value="<?php print BUGScontext::getRequest()->getParameter('email_address'); ?>" style="width: 200px;"></td>
					</tr>
					<tr>
						<td><label class="login_fieldheader" for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label></td>
						<td><input type="text" id="email_confirm" name="email_confirm" value="<?php print BUGScontext::getRequest()->getParameter('email_confirm'); ?>" style="width: 200px;"></td>
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
				<input type="submit" id="register_button" value="<?php echo __('Register'); ?>">
			</div>
		</form>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php } else { ?>

<div class="rounded_box gray">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 10px;">
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register1'); ?>" enctype="multipart/form-data" method="post" name="registerform">
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
<?php } endif; ?>
