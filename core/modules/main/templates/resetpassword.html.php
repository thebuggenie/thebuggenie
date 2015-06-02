<div class="backdrop_box login_page login_popup" id="reset_popup">
    <div class="backdrop_detail_content login_content">
        <div class="logindiv regular active" id="reset_password_container">
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey())); ?>" method="post" onsubmit="['reset_button', 'reset_indicator'].each(Element.toggle);">
                <input type="hidden" id="tbg3_referer" name="reset_referer" value="<?php echo make_url('home'); ?>" />
                <h2 class="login_header"><?php echo __('Reset your password'); ?></h2>
                <ul class="login_formlist">
                    <li>
                        <label for="tbg3_username"><?php echo __('Username'); ?></label>
                        <input type="text" id="tbg3_username" name="username" value="<?php echo $user->getUsername(); ?>" disabled>
                    </li>
                    <li>
                        <label for="_password"><?php echo __('New password'); ?></label>
                        <input type="password" id="_password" name="password_1"><br>
                    </li>
                    <li>
                        <label for="_password_repeat"><?php echo __('Repeat your new password'); ?></label>
                        <input type="password" id="_password_repeat" name="password_2"><br>
                    </li>
                </ul>
                <div class="login_button_container">
                    <?php echo image_tag('spinning_20.gif', array('id' => 'reset_indicator', 'style' => 'display: none;')); ?>
                    <input type="submit" id="reset_button" class="button button-silver" value="<?php echo __('Reset my password'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>
