<div class="backdrop_box login_page login_popup" id="reset_popup">
    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey())); ?>" method="post" onsubmit="['reset_button', 'reset_indicator'].each(Element.toggle);">
        <div class="backdrop_detail_header">
            <span><?= __('Reset your password'); ?></span>
        </div>
        <div class="backdrop_detail_content login_content">
            <div class="logindiv regular active" id="reset_password_container">
                <input type="hidden" id="tbg_referer" name="reset_referer" value="<?= make_url('home'); ?>" />
                <ul class="login_formlist">
                    <li>
                        <label for="tbg_username"><?= __('Username'); ?></label>
                        <input type="text" id="tbg_username" name="username" value="<?= $user->getUsername(); ?>" disabled>
                    </li>
                    <li>
                        <label for="_password"><?= __('New password'); ?></label>
                        <input type="password" id="_password" name="password_1"><br>
                    </li>
                    <li>
                        <label for="_password_repeat"><?= __('Repeat your new password'); ?></label>
                        <input type="password" id="_password_repeat" name="password_2"><br>
                    </li>
                </ul>
            </div>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="submit_container">
                <button type="submit" id="reset_button" class="button button-silver"><?= image_tag('spinning_16.gif', array('id' => 'reset_indicator', 'style' => 'display: none;')) . __('Reset my password'); ?></button>
            </div>
        </div>
    </form>
</div>
