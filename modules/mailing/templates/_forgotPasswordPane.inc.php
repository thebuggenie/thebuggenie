<div id="tab_forgot_pane"<?php if ($selected_tab != 'forgot'): ?> style="display: none;<?php endif; ?>">
<?php
if (TBGSettings::getAuthenticationBackend() != 'tbg' && TBGSettings::getAuthenticationBackend() != null)
{
	echo tbg_parse_text(TBGSettings::get('forgot_message'), null, null, array('embedded' => true));
}
else
{
?>
	<div style="vertical-align: middle; padding: 10px;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('forgot'); ?>" method="post" id="forgot_password_form" onsubmit="TBG.Main.Login.resetForgotPassword('<?php echo make_url('forgot'); ?>'); return false;">
			<div class="login_boxheader"><?php echo __('Forgot password?'); ?></div>
			<p><?php echo __('If you have forgot your password, enter your username here, and we will send you an email that will allow you to change your password'); ?>.</p><br>
			<div>
				<label class="login_fieldheader" for="forgot_password_username"><?php echo __('Username'); ?></label>
				<input type="text" id="forgot_password_username" name="forgot_password_username" style="width: 200px;"><br>
				<br>
				<input type="submit" class="button button-green" id="forgot_password_button" value="<?php echo __('Send email'); ?>">
				<span id="forgot_password_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
			</div>
		</form>
	</div>
	<br>
<?php
}
?>
</div>