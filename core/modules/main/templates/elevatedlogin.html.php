<?php

    \thebuggenie\core\framework\Context::loadLibrary('ui');

?>
<div id="login_backdrop">
    <div class="backdrop_box login_page login_popup" id="login_popup">
        <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
            <div class="logindiv regular active" id="regular_login_container">
                <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('elevated_login'); ?>" method="post" id="login_form" onsubmit="TBG.Main.Login.elevatedLogin('<?php echo make_url('elevated_login'); ?>'); return false;">
                    <h2 class="login_header"><?php echo __('Authentication required'); ?></h2>
                    <div class="article">
                        <?php echo __('This page requires an extra authentication step. Please re-enter your password to continue'); ?>
                    </div>
                    <ul class="login_formlist">
                        <li>
                            <label for="tbg3_username"><?php echo __('Username'); ?></label>
                            <input type="text" id="tbg3_username" name="dummy_username" disabled value="<?php echo $tbg_user->getUsername(); ?>">
                        </li>
                        <li>
                            <label for="tbg3_password"><?php echo __('Password'); ?></label>
                            <input type="password" id="tbg3_password" name="tbg3_elevated_password"><br>
                        </li>
                        <li>
                            <label for="tbg3_elevation_duration"><?php echo __('Re-authentication duration'); ?></label>
                            <select name="tbg3_elevation_duration">
                                <?php foreach (array(5, 10, 15, 30, 60) as $minute): ?>
                                    <option value="<?php echo $minute; ?>" <?php if ($minute == 30) echo 'selected'; ?>><?php echo __('Remember for %minutes minutes', array('%minutes' => $minute)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                    </ul>
                    <div class="login_button_container">
                        <?php echo image_tag('spinning_20.gif', array('id' => 'elevated_login_indicator', 'style' => 'display: none;')); ?>
                        <input type="submit" id="login_button" class="button button-silver" value="<?php echo __('Authenticate'); ?>">
                    </div>
                </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['domReady', 'thebuggenie/tbg', 'prototype'], function (domReady, TBG, prototype) {
        domReady(function () {
        <?php if (\thebuggenie\core\framework\Context::hasMessage('elevated_login_message')): ?>
            TBG.Main.Helpers.Message.success('<?php echo \thebuggenie\core\framework\Context::getMessageAndClear('elevated_login_message'); ?>');
        <?php elseif (\thebuggenie\core\framework\Context::hasMessage('elevated_login_message_err')): ?>
            TBG.Main.Helpers.Message.error('<?php echo \thebuggenie\core\framework\Context::getMessageAndClear('elevated_login_message_err'); ?>');
        <?php endif; ?>
            $('tbg3_password').focus();
        });
    });
</script>