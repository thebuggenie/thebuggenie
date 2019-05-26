<?php /* \thebuggenie\core\framework\Event::createNew('core', 'login_form_tab')->trigger(array('selected_tab' => $selected_tab)); */ ?>
<div class="logindiv regular active" id="regular_login_container">
    <?php if ($loginintro instanceof \thebuggenie\modules\publish\entities\Article): ?>
        <?php include_component('publish/articledisplay', array('article' => $loginintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
    <?php endif; ?>
    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('login'); ?>" method="post" id="login_form" onsubmit="TBG.Main.Login.login('<?= make_url('login'); ?>'); return false;">
        <?php if (!\thebuggenie\core\framework\Context::hasMessage('login_force_redirect') || \thebuggenie\core\framework\Context::getMessage('login_force_redirect') !== true): ?>
            <input type="hidden" id="tbg_referer" name="referer" value="<?= $referer; ?>" />
        <?php else: ?>
            <input type="hidden" id="return_to" name="return_to" value="<?= $referer; ?>" />
        <?php endif; ?>
        <h2 class="login_header"><?= __('Log in with your username and password'); ?></h2>
        <ul class="login_formlist">
            <li>
                <label for="tbg_username"><?= __('Username'); ?></label>
                <input type="text" id="tbg_username" name="username">
            </li>
            <li>
                <label for="tbg_password"><?= __('Password'); ?></label>
                <input type="password" id="tbg_password" name="password"><br>
            </li>
            <li>
                <input type="checkbox" class="fancycheckbox" name="rememberme" value="1" id="tbg_rememberme"><label class="login_fieldlabel" for="tbg_rememberme"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Keep me logged in'); ?></label>
            </li>
        </ul>
        <div class="login_button_container">
            <?php \thebuggenie\core\framework\Event::createNew('core', 'login_button_container')->trigger(); ?>
            <?= image_tag('spinning_20.gif', array('id' => 'login_indicator', 'style' => 'display: none;')); ?>
            <input type="submit" id="login_button" class="button button-silver" value="<?= __('Log in'); ?>">
        </div>
    </form>
</div>
<?php \thebuggenie\core\framework\Event::createNew('core', 'login_form_pane')->trigger(array_merge(array('selected_tab' => $selected_tab), $options)); ?>
<?php if (\thebuggenie\core\framework\Settings::isRegistrationAllowed()): ?>
    <div style="text-align: center;" id="registration-button-container" class="logindiv login_button_container registration_button_container active">
        <fieldset style="border: 0; border-top: 1px dotted rgba(0, 0, 0, 0.3); padding: 5px 100px; width: 100px; margin: 5px auto 0 auto;">
            <legend style="text-align: center; width: 100%; background-color: transparent;"><?= __('%login or %signup', array('%login' => '', '%signup' => '')); ?></legend>
        </fieldset>
        <a href="javascript:void(0);" id="create-account-button" onclick="$('register').addClassName('active');$('registration-button-container').removeClassName('active');$('regular_login_container').removeClassName('active');"><?= __('Create an account'); ?></a>
    </div>
    <?php include_component('main/loginregister', compact('registrationintro')); ?>
<?php endif; ?>
<?php if (isset($error)): ?>
    <script type="text/javascript">
        require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
            domReady(function () {
                TBG.Main.Helpers.Message.error('<?= $error; ?>');
            });
        });
    </script>
<?php endif; ?>
