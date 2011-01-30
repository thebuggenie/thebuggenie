<div style="vertical-align: middle; padding: 5px; text-align: center;">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('reset_password', array('user' => $username, 'id' => $id)); ?>" method="post" id="forgot_password_form" onsubmit="resetForgotPassword('<?php echo make_url('reset_password', array('user' => $username, 'id' => $id)); ?>'); return false;">
		<div class="login_boxheader"><?php echo __('Reset password?'); ?></div>
		<p><?php echo __('If you have forgot your password, enter your email adress here, and we will send you an email with your new password'); ?>.</p><br>
		<div>
			<label class="login_fieldheader" for="forgot_password_mail"><?php echo __('Email'); ?></label>
			<input type="text" id="forgot_password_mail" name="forgot_password_mail" style="width: 200px;"><br>
			<br>
			<input type="submit" id="forgot_password_button" value="<?php echo __('Reset password'); ?>">
			<span id="forgot_password_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
		</div>
	</form>
</div>