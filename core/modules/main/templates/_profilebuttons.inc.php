<div class="profile_buttons">
    <div class="button-group">
    <?php if ($tbg_user->isOpenIdLocked()): ?>
        <a href="javascript:void(0);" id="pick_username_button" class="button button-blue dropper"><?php echo __('Pick a username'); ?></a>
        <div class="rounded_box white shadowed popup_box"  style="top: 23px; z-index: 100; padding: 5px 10px 5px 10px; font-size: 13px; width: 400px;" id="pick_username_div">
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_check_username'); ?>" onsubmit="TBG.Main.Profile.checkUsernameAvailability('<?php echo make_url('account_check_username'); ?>'); return false;" method="post" id="check_username_form">
                <b><?php echo __('Picking a username'); ?></b><br>
                <div style="font-size: 13px; margin-bottom: 10px;"><?php echo __('Since this account was created via an OpenID login, you will have to pick a username to be able to log in with a username or password. You can continue to use your account with your OpenID login, so this is only if you want to pick a username for your account.'); ?><br>
                <br><?php echo __('Click "%check_availability" to see if your desired username is available.', array('%check_availability' => __('Check availability'))); ?></div>
                <label for="username_pick" class="smaller"><?php echo __('Type desired username'); ?></label><br>
                <input type="text" name="desired_username" id="username_pick" style="width: 390px;"><br>
                <div id="username_unavailable" style="display: none;"><?php echo __('This username is not available'); ?></div>
                <div class="smaller" style="text-align: right; margin: 10px 2px 5px 0; height: 23px;">
                    <div style="float: right; padding: 3px;"><?php echo __('%check_availability or %cancel', array('%check_availability' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'pick_username_div\').toggle();$(\'pick_username_button\').toggleClassName(\'button-pressed\');"><b>' . __('cancel') . '</b></a>')); ?></div>
                    <input type="submit" value="<?php echo __('Check availability'); ?>" style="font-weight: bold; float: right;">
                    <span id="pick_username_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
                </div>
            </form>
        </div>
    <?php endif; ?>
    </div>
</div>
