<div id="register" class="logindiv regular">
    <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
        <?php echo tbg_parse_text(\thebuggenie\core\framework\Settings::get('register_message'), false, null, array('embedded' => true)); ?>
    <?php else: ?>
        <div id="register_container">
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('register'); ?>" method="post" id="register_form" onsubmit="TBG.Main.Login.register('<?php echo make_url('register'); ?>'); return false;">
                <h2 class="login_header"><?php echo __('Create an account'); ?></h2>
                <?php if ($registrationintro instanceof \thebuggenie\modules\publish\entities\Article): ?>
                    <?php include_component('publish/articledisplay', array('article' => $registrationintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
                <?php endif; ?>
                <ul class="login_formlist">
                    <li>
                        <label for="fieldusername">*&nbsp;<?php echo __('Username'); ?></label>
                        <input type="text" class="required" id="fieldusername" name="fieldusername" onblur="TBG.Main.Login.checkUsernameAvailability('<?php echo make_url('register_check_username'); ?>');">
                        <div class="error_message"><?php echo __('This username is invalid or in use'); ?></div>
                        <?php echo image_tag('spinning_20.gif', array('id' => 'username_check_indicator', 'style' => 'display: none;')); ?>
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
                    <?php include_component('main/captcha'); ?>
                </ul>
                <div class="login_button_container">
                    <a style="float: left;" href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('regular_login_container');">&laquo;&nbsp;<?php echo __('Back'); ?></a>
                    <?php echo image_tag('spinning_20.gif', array('id' => 'register_indicator', 'style' => 'display: none;')); ?>
                    <input type="submit" class="button button-green" id="register_button" value="<?php echo __('Register'); ?>">
                </div>
            </form>
        </div>
        <div style="display: none;" id="register_confirmation">
            <h2 class="login_header"><?php echo __('Register a new account'); ?></h2>
            <div class="article">
                <span><?php echo __('Thank you for registering!'); ?></span>
                <br>
                <span id="register_message"></span>
                <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('login'); ?>" method="post" id="register_auto_form" onsubmit="TBG.Main.Login.registerAutologin('<?php echo make_url('login'); ?>'); return false;">
                    <input id="register_username_hidden" name="tbg3_username" type="hidden" value="">
                    <input id="register_password_hidden" name="tbg3_password" type="hidden" value="">
                    <input type="hidden" name="return_to" value="<?php echo make_url('account'); ?>">
                    <div class="login_button_container">
                        <?php echo image_tag('spinning_20.gif', array('id' => 'register_autologin_indicator', 'style' => 'display: none;')); ?>
                        <input type="submit" class="button button-silver" id="register_autologin_button" value="<?php echo __('Continue'); ?>">
                    </div>
                </form>
                <div class="login_button_container" id="register_confirm_back" style="display: none;">
                    <a style="float: left;" href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('regular_login_container');">&laquo;&nbsp;<?php echo __('Back'); ?></a>
                </div>
            </div>
        </div>
        <br>
    <?php endif; ?>
</div>
