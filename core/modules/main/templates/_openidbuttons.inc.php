<div class="logindiv openid_container" id="openid_container" style="<?php if (isset($mode) && $mode == 'add_signin') echo 'display: block;'; ?>">
    <form action="<?php echo make_url('login'); ?>" method="post" id="openid_form" onsubmit="return TBG.OpenID.submit();">
        <?php if ($openidintro instanceof \thebuggenie\modules\publish\entities\Article): ?>
            <?php include_component('publish/articledisplay', array('article' => $openidintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
        <?php endif; ?>
        <input type="hidden" name="action" value="verify" />
        <div id="openid_choice">
            <div id="openid_btns"></div>
        </div>
        <div id="openid_input_area">
            <input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
        </div>
        <input id="openid_provider" type="hidden" value="" />
        <input id="openid_submit_button" type="submit" value="<?php echo __('Sign in'); ?>" class="button button-silver" style="display: none;">
    </form>
    <br style="clear: both;">
    <div style="<?php if (isset($mode) && $mode == 'add_signin') echo 'display: none;'; ?> text-align: center;">
        <fieldset style="border: 0; border-top: 1px dotted rgba(0, 0, 0, 0.3); padding: 10px 100px; width: 100px; margin: 20px auto 0 auto;">
            <legend style="text-align: center; width: 100%; background-color: transparent;"><?php echo __('%regular_login or %persona_or_openid_login', array('%regular_login' => '', '%persona_or_openid_login' => '')); ?></legend>
        </fieldset>
        <?php if (\thebuggenie\core\framework\Settings::isPersonaAvailable()): ?>
            <a class="persona-button" id="persona-signin-button" href="#"><span><?php echo __('Sign in with Persona'); ?></span></a>
        <?php endif; ?>
        <a class="persona-button dark" id="regular-signin-button" href="javascript:void(0);" onclick="$('regular_login_container').toggleClassName('active');$('openid_container').toggleClassName('active');"><span><?php echo __('Regular signin'); ?></span></a>
    </div>
</div>
<script type="text/javascript">

    var providers_large = {
        google : {
            name : 'Google',
            url : 'https://www.google.com/accounts/o8/id'
        },
        openid : {
            name : 'OpenID',
            label : '<?php echo htmlspecialchars(__('Enter your OpenID'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : null
        },
        wordpress : {
            name : 'Wordpress',
            label : '<?php echo htmlspecialchars(__('Enter your Wordpress.com username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://{username}.wordpress.com/'
        },
        launchpad: {
            name: 'Launchpad',
            label : '<?php echo htmlspecialchars(__('Your Launchpad username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url: 'https://launchpad.net/~{username}'
        }
    };

    var providers_small = {
        yahoo : {
            name : 'Yahoo',
            url : 'http://me.yahoo.com/'
        },
        livejournal : {
            name : 'LiveJournal',
            label : '<?php echo htmlspecialchars(__('Enter your Livejournal username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://{username}.livejournal.com/'
        },
        aol : {
            name : 'AOL',
            label : '<?php echo htmlspecialchars(__('Enter your AOL screenname'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://openid.aol.com/{username}'
        },
        flickr: {
            name: 'Flickr',
            label : '<?php echo htmlspecialchars(__('Enter your Flickr username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url: 'http://flickr.com/{username}/'
        },
        technorati: {
            name: 'Technorati',
            label : '<?php echo htmlspecialchars(__('Your Technorati username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url: 'http://technorati.com/people/technorati/{username}/'
        },
        blogger : {
            name : 'Blogger',
            label : '<?php echo htmlspecialchars(__('Your Blogger account'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://{username}.blogspot.com/'
        },
        verisign : {
            name : 'Verisign',
            label : '<?php echo htmlspecialchars(__('Your Verisign username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://{username}.pip.verisignlabs.com/'
        },
        vidoop: {
            name: 'Vidoop',
            label : '<?php echo htmlspecialchars(__('Your Vidoop username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url: 'http://{username}.myvidoop.com/'
        },
        claimid : {
            name : 'ClaimID',
            label : '<?php echo htmlspecialchars(__('Your ClaimID username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://claimid.com/{username}'
        },
        clickpass : {
            name : 'ClickPass',
            label : '<?php echo htmlspecialchars(__('Enter your ClickPass username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://clickpass.com/public/{username}'
        },
        google_profile : {
            name : 'Google Profile',
            label : '<?php echo htmlspecialchars(__('Enter your Google Profile username'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>',
            url : 'http://www.google.com/profiles/{username}'
        }
    };

    require(['thebuggenie/tbg'], function (TBG) {
        TBG.OpenID.signin_text = '<?php echo htmlspecialchars(__('Sign in'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>';
        TBG.OpenID.image_title = '<?php echo htmlspecialchars(__('Log in with %openid_provider_name'), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>';
        TBG.OpenID.providers_small = providers_small;
        TBG.OpenID.providers_large = providers_large;
        <?php if ($tbg_request->isAjaxCall()): ?>
            TBG.OpenID.init();
        <?php endif; ?>
    });
</script>
