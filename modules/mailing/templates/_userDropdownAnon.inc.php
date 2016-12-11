<?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>
    <li><a href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('forgot_password_container');$('forgot_password_username').focus();"><?php echo fa_image_tag('key').__('Forgot password'); ?></a></li>
<?php endif; ?>