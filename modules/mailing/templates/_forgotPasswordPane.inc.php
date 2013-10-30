<div id="forgot_password_container" class="logindiv regular">
	<?php if (TBGSettings::isUsingExternalAuthenticationBackend()): ?>
		<?php echo tbg_parse_text(TBGSettings::get('forgot_message'), null, null, array('embedded' => true)); ?>
	<?php else: ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('forgot'); ?>" method="post" id="forgot_password_form" onsubmit="TBG.Main.Login.resetForgotPassword('<?php echo make_url('forgot'); ?>'); return false;">
			<?php if ($forgottenintro instanceof TBGWikiArticle): ?>
				<?php include_component('publish/articledisplay', array('article' => $forgottenintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
			<?php endif; ?>
			<ul class="login_formlist">
				<li>
					<label for="forgot_password_username"><?php echo __('Username or email address'); ?></label>
					<input type="text" id="forgot_password_username" name="forgot_password_username"><br>
				</li>
			</ul>
			<div class="login_button_container">
				<a style="float: left;" href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('regular_login_container');">&laquo;&nbsp;<?php echo __('Back'); ?></a>
				<?php echo image_tag('spinning_20.gif', array('id' => 'forgot_password_indicator', 'class' => 'indicator', 'style' => 'display: none;')); ?></span>
				<input type="submit" class="button button-green" id="forgot_password_button" value="<?php echo __('Send email'); ?>">
			</div>
		</form>
	<?php endif; ?>
</div>
