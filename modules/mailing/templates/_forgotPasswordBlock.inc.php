<?php if (TBGContext::hasMessage('forgot_error')): ?>
	<div class="rounded_box red borderless" style="vertical-align: middle; padding: 5px;">
		<span class="login_fieldheader"><?php echo TBGContext::getMessageAndClear('forgot_error'); ?></span>
	</div>
	<br>
<?php endif; ?>

<?php if (TBGContext::hasMessage('forgot_success')): ?>
	<div class="rounded_box green borderless" style="vertical-align: middle; padding: 5px;">
		<div class="login_boxheader"><?php echo __('Forgot password?'); ?></div>
		<span class="login_fieldheader">
			<?php echo TBGContext::getMessageAndClear('forgot_success'); ?>
		</span>
	</div>
	<br>
<?php else: ?>
	<div class="rounded_box lightgrey" style="vertical-align: middle; padding: 5px;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('forgot'); ?>" enctype="multipart/form-data" method="post" name="lostpasswordform">
			<input type="hidden" name="lostpassword" value="true">
			<div class="login_boxheader"><?php echo __('Forgot password?'); ?></div>
			<p><?php echo __('If you have forgot your password, enter your username here, and we will send you an email that will allow you to change your password'); ?>.</p><br>
			<div>
				<label class="login_fieldheader" for="forgot_password_username"><?php echo __('Username'); ?></label>
				<input type="text" id="forgot_password_username" name="forgot_password_username" style="width: 200px;"><br>
				<br>
				<input type="submit" id="login_button" value="<?php echo __('Send email'); ?>">
			</div>
		</form>
	</div>
	<br>
<?php endif; ?>
