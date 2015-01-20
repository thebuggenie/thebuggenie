<?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>
    <a href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('forgot_password_container');$('forgot_password_username').focus();"><?php echo image_tag('icon_forgot.png').__('Forgot password'); ?></a>
<?php endif; ?>