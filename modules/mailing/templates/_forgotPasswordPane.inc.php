<div id="tab_forgot_pane"<?php if ($selected_tab != 'forgot'): ?> style="display: none;<?php endif; ?>">
	<?php if (isset($reset) && $reset == true): ?>
	<div class="rounded_box lightgrey" style="vertical-align: middle; padding: 5px;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('reset_password', array('user' => $user, 'id' => $id)); ?>" method="post" id="forgot_password_form" onsubmit="resetForgotPassword('<?php echo make_url('reset_password', array('user' => $user, 'id' => $id)); ?>'); return false;">
			<div class="login_boxheader"><?php echo __('Reset password?'); ?></div>
			<p><?php echo __('If you have forgot your password, enter your email adress here, and we will send you an email with your new password'); ?>.</p><br>
			<div>
				<label class="login_fieldheader" for="forgot_password_mail"><?php echo __('Email'); ?></label>
				<input type="text" id="forgot_password_username" name="forgot_password_mail" style="width: 200px;"><br>
				<br>
				<input type="submit" id="forgot_password_button" value="<?php echo __('Reset password'); ?>">
				<span id="forgot_password_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
			</div>
		</form>
	</div>
	<?php else: ?>
	<div class="rounded_box lightgrey" style="vertical-align: middle; padding: 5px;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('forgot'); ?>" method="post" id="forgot_password_form" onsubmit="resetForgotPassword('<?php echo make_url('forgot'); ?>'); return false;">
			<div class="login_boxheader"><?php echo __('Forgot password?'); ?></div>
			<p><?php echo __('If you have forgot your password, enter your username here, and we will send you an email that will allow you to change your password'); ?>.</p><br>
			<div>
				<label class="login_fieldheader" for="forgot_password_username"><?php echo __('Username'); ?></label>
				<input type="text" id="forgot_password_username" name="forgot_password_username" style="width: 200px;"><br>
				<br>
				<input type="submit" id="forgot_password_button" value="<?php echo __('Send email'); ?>">
				<span id="forgot_password_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
			</div>
		</form>
	</div>
	<?php endif; ?>
	<br>
</div>