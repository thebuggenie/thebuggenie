<div id="tab_register_pane"<?php if ($selected_tab != 'register'): ?> style="display: none;"<?php endif; ?>>
<?php
if (TBGSettings::isUsingExternalAuthenticationBackend())
{
	echo tbg_parse_text(TBGSettings::get('register_message'), null, null, array('embedded' => true));
}
else
{
?>
		<div style="vertical-align: middle; padding: 10px;" id="register">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register'); ?>" method="post" id="register_form" onsubmit="TBG.Main.Login.register('<?php echo make_url('register'); ?>'); return false;">
				<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
				<div>
					<?php echo __('To register, please fill out the information below.'); ?>
					<br>
					<i>(<?php echo __('Required information is marked with an asterisk'); ?>: <b>*</b>)</i><br><br>
					<table border="0" class="login_fieldtable">
						<tr>
							<td><label class="login_fieldheader" for="fieldusername">*&nbsp;<?php echo __('Username'); ?></label></td>
							<td><input type="text" class="required" id="fieldusername" name="fieldusername" style="width: 200px;"></td>
						</tr>					
						<tr>
							<td><label class="login_fieldheader" for="buddyname">*&nbsp;<?php echo __('Display name'); ?></label></td>
							<td><input type="text" class="required" id="buddyname" name="buddyname" style="width: 200px;"></td>
						</tr>
						<tr>
							<td colspan="2" class="faded_out"><?php echo __('The "display name" is the name shown to others'); ?></td>
						</tr>
						<tr>
							<td><label class="login_fieldheader" for="realname">&nbsp;<?php echo __('Real name'); ?></label></td>
							<td><input type="text" id="realname" name="realname" style="width: 200px;"></td>
						</tr>
						<tr>
							<td><label class="login_fieldheader" for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label></td>
							<td><input type="text" class="required" id="email_address" name="email_address" style="width: 200px;"></td>
						</tr>
						<tr>
							<td><label class="login_fieldheader" for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label></td>
							<td><input type="text" class="required" id="email_confirm" name="email_confirm" style="width: 200px;"></td>
						</tr>
					</table>
					<br>
					
					<?php TBGActionComponent::includeComponent('captcha'); ?>
					
					<br><b><?php echo __('Enter the above number in this box'); ?></b><br><br>
					<label class="login_fieldheader" for="verification_no">*&nbsp;<?php echo __('Security check'); ?></label>
					<input type="text" class="required" id="verification_no" name="verification_no" maxlength="6" style="width: 100px;"><br><br>
					<input type="submit" class="button button-green" id="register_button" value="<?php echo __('Register'); ?>">
					<span id="register_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
				</div>
			</form>
		</div>
		<div class="rounded_box green borderless register_success" style="display: none; vertical-align: middle; padding: 5px;" id="register2">
			<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
			<span class="login_fieldheader"><?php echo __('Thank you for registering!'); ?></span>
			<br>
			<span style="font-size: 14px;" id="register_message"></span>
		</div>
	<br>
<?php
}
?>
</div>