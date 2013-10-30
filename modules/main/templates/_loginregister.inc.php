<div id="register" class="logindiv regular">
	<?php if (TBGSettings::isUsingExternalAuthenticationBackend()): ?>
		<?php echo tbg_parse_text(TBGSettings::get('register_message'), null, null, array('embedded' => true)); ?>
	<?php else: ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register'); ?>" method="post" id="register_form" onsubmit="TBG.Main.Login.register('<?php echo make_url('register'); ?>'); return false;">
			<?php if ($registrationintro instanceof TBGWikiArticle): ?>
				<?php include_component('publish/articledisplay', array('article' => $registrationintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
			<?php endif; ?>
			<ul class="login_formlist">
				<li>
					<label for="fieldusername">*&nbsp;<?php echo __('Username'); ?></label>
					<input type="text" class="required" id="fieldusername" name="fieldusername">
				</li>
				<li>
					<label for="buddyname">*&nbsp;<?php echo __('Nickname'); ?></label>
					<input type="text" class="required" id="buddyname" name="buddyname">
				</li>
				<li class="faded_out">
					<?php echo __('The "nickname" will be shown to other users'); ?>
				</li>
				<li>
					<label for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label>
					<input type="email" class="required" id="email_address" name="email_address">
				</li>
				<li>
					<label for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label>
					<input type="email" class="required" id="email_confirm" name="email_confirm">
				</li>
				<li class="security_check">
					<?php TBGActionComponent::includeComponent('main/captcha'); ?>
					<label for="verification_no"><?php echo __('Enter the number you see above'); ?></label>
					<input type="text" class="required" id="verification_no" name="verification_no" maxlength="6" style="width: 100px;"><br><br>
				</li>
			</ul>
			<div class="login_button_container">
				<a style="float: left;" href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('regular_login_container');">&laquo;&nbsp;<?php echo __('Back'); ?></a>
				<?php echo image_tag('spinning_20.gif', array('id' => 'register_indicator', 'style' => 'display: none;')); ?>
				<input type="submit" class="button button-green" id="register_button" value="<?php echo __('Register'); ?>">
			</div>
		</form>
		<div class="rounded_box green borderless register_success" style="display: none; vertical-align: middle; padding: 5px;" id="register2">
			<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
			<span><?php echo __('Thank you for registering!'); ?></span>
			<br>
			<span style="font-size: 14px;" id="register_message"></span>
		</div>
		<br>
	<?php endif; ?>
</div>
