<?php if (TBGSettings::isGuestCaptchaEnabled() && $tbg_user->isGuest()): ?>
	<label class="required" for="verification_no">*&nbsp;<?php echo __('Security check'); ?> </label><br>
	<?php TBGActionComponent::includeComponent('main/captcha'); ?>
	<br><input type="text" class="required verification_no_input" name="verification_no" maxlength="6" style="width: 100px;"><br>
	<span class="faded_out"><?php echo __('Enter the above number in this box'); ?></span><br>
<?php endif ?>
