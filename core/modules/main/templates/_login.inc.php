<?php /* \thebuggenie\core\framework\Event::createNew('core', 'login_form_tab')->trigger(array('selected_tab' => $selected_tab)); */ ?>
<script type="text/javascript">
    require(['domReady', 'prototype'], function (domReady, prototype) {
        domReady(function () {
            if (document.location.href.search('<?php echo make_url('login_page'); ?>') != -1)
                if ($('tbg3_referer')) $('tbg3_referer').setValue('<?php echo make_url('dashboard'); ?>');
                else if ($('return_to')) $('return_to').setValue('<?php echo make_url('dashboard'); ?>');
        });
    });

</script>
<div class="logindiv regular active" id="regular_login_container">
    <?php if ($loginintro instanceof \thebuggenie\modules\publish\entities\Article): ?>
        <?php include_component('publish/articledisplay', array('article' => $loginintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
    <?php endif; ?>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('login'); ?>" method="post" id="login_form" onsubmit="TBG.Main.Login.login('<?php echo make_url('login'); ?>'); return false;">
        <?php if (!\thebuggenie\core\framework\Context::hasMessage('login_force_redirect') || \thebuggenie\core\framework\Context::getMessage('login_force_redirect') !== true): ?>
            <input type="hidden" id="tbg3_referer" name="tbg3_referer" value="<?php echo $referer; ?>" />
        <?php else: ?>
            <input type="hidden" id="return_to" name="return_to" value="<?php echo $referer; ?>" />
        <?php endif; ?>
        <h2 class="login_header"><?php echo __('Log in with your username and password'); ?></h2>
        <ul class="login_formlist">
            <li>
                <label for="tbg3_username"><?php echo __('Username'); ?></label>
                <input type="text" id="tbg3_username" name="tbg3_username">
            </li>
            <li>
                <label for="tbg3_password"><?php echo __('Password'); ?></label>
                <input type="password" id="tbg3_password" name="tbg3_password"><br>
            </li>
            <li>
                <input type="checkbox" name="tbg3_rememberme" value="1" id="tbg3_rememberme"><label class="login_fieldlabel" for="tbg3_rememberme"><?php echo __('Keep me logged in'); ?></label>
            </li>
        </ul>
        <div class="login_button_container">
            <?php \thebuggenie\core\framework\Event::createNew('core', 'login_button_container')->trigger(); ?>
            <?php echo image_tag('spinning_20.gif', array('id' => 'login_indicator', 'style' => 'display: none;')); ?>
            <input type="submit" id="login_button" class="button button-silver" value="<?php echo __('Log in'); ?>">
        </div>
    </form>
    <?php if (\thebuggenie\core\framework\Settings::isPersonaAvailable() || \thebuggenie\core\framework\Settings::isOpenIDavailable()): ?>
        <div style="text-align: center;">
            <fieldset style="border: 0; border-top: 1px dotted rgba(0, 0, 0, 0.3); padding: 10px 100px; width: 100px; margin: 15px auto 0 auto;">
                <legend style="text-align: center; width: 100%; background-color: transparent;"><?php echo __('%regular_login or %persona_or_openid_login', array('%regular_login' => '', '%persona_or_openid_login' => '')); ?></legend>
            </fieldset>
            <?php if (\thebuggenie\core\framework\Settings::isPersonaAvailable()): ?>
                <a class="persona-button" id="persona-signin-button" href="#"><span><?php echo __('Sign in with Persona'); ?></span></a>
            <?php endif; ?>
            <?php if (\thebuggenie\core\framework\Settings::isOpenIDavailable()): ?>
                <a class="persona-button orange" id="openid-signin-button" href="javascript:void(0);" onclick="$('regular_login_container').toggleClassName('active');$('openid_container').toggleClassName('active');"><span><?php echo __('Sign in with OpenID'); ?></span></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php if (\thebuggenie\core\framework\Settings::isOpenIDavailable()): ?>
    <?php include_component('main/openidbuttons'); ?>
<?php endif; ?>
<?php \thebuggenie\core\framework\Event::createNew('core', 'login_form_pane')->trigger(array_merge(array('selected_tab' => $selected_tab), $options)); ?>
<?php if (\thebuggenie\core\framework\Settings::isRegistrationAllowed()): ?>
    <div style="text-align: center;" id="registration-button-container" class="logindiv login_button_container registration_button_container active">
        <fieldset style="border: 0; border-top: 1px dotted rgba(0, 0, 0, 0.3); padding: 5px 100px; width: 100px; margin: 5px auto 0 auto;">
            <legend style="text-align: center; width: 100%; background-color: transparent;"><?php echo __('%login or %signup', array('%login' => '', '%signup' => '')); ?></legend>
        </fieldset>
        <a href="javascript:void(0);" id="create-account-button" onclick="$('register').addClassName('active');$('registration-button-container').removeClassName('active');$('regular_login_container').removeClassName('active');$('openid_container').removeClassName('active');"><?php echo __('Create an account'); ?></a>
    </div>
    <?php include_component('main/loginregister', compact('registrationintro')); ?>
<?php endif; ?>
<?php if (isset($error)): ?>
    <script type="text/javascript">
        require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
            domReady(function () {
                TBG.Main.Helpers.Message.error('<?php echo $error; ?>');
            });
        });
    </script>
<?php endif; ?>
