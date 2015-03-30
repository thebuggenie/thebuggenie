<?php

    $themes = \thebuggenie\core\framework\Context::getThemes();
    $languages = \thebuggenie\core\framework\I18n::getLanguages();
    
?>
<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
    <tr>
        <td><label for="requirelogin"><?php echo __('Require re-authentication'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>" id="disableelevatedlogin" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=0<?php if (\thebuggenie\core\framework\Settings::isElevatedLoginRequired()): ?> selected<?php endif; ?>><?php echo __('You need to re-enter your password to access the configuration section'); ?></option>
                <option value=1<?php if (!\thebuggenie\core\framework\Settings::isElevatedLoginRequired()): ?> selected<?php endif; ?>><?php echo __("You don't need to re-enter your password"); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="requirelogin"><?php echo __('Anonymous access'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>" id="requirelogin" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=1<?php if (\thebuggenie\core\framework\Settings::isLoginRequired()): ?> selected<?php endif; ?>><?php echo __('You need a valid user account to access any content'); ?></option>
                <option value=0<?php if (!\thebuggenie\core\framework\Settings::isLoginRequired()): ?> selected<?php endif; ?>><?php echo __('Use the guest user account'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="defaultisguest"><?php echo __('Guest user is authenticated'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_DEFAULT_USER_IS_GUEST; ?>" id="defaultisguest" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=1<?php if (\thebuggenie\core\framework\Settings::isDefaultUserGuest()): ?> selected<?php endif; ?>><?php echo __('No, the default user is a guest account'); ?></option>
                <option value=0<?php if (!\thebuggenie\core\framework\Settings::isDefaultUserGuest()): ?> selected<?php endif; ?>><?php echo __('Yes, the default user is a normal account'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2"><?php echo __('Select if the default user is a guest user or a normal user'); ?></td>
    </tr>
    <tr>
        <td><label for="permissive"><?php echo __('Security policy'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>" id="permissive" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=1<?php if (\thebuggenie\core\framework\Settings::isPermissive()): ?> selected<?php endif; ?>><?php echo __('Permissive'); ?></option>
                <option value=0<?php if (!\thebuggenie\core\framework\Settings::isPermissive()): ?> selected<?php endif; ?>><?php echo __('Restrictive'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2">
            <?php echo __("%restrictive: With this security policy, users don't automatically get access to projects, modules, etc., but must be granted access specifically.", array('%restrictive' => '<b>'.__('Restrictive').'</b>')); ?><br>
            <?php echo __("%permissive: This security policy assume you have access to things like projects, pages, etc.", array('%permissive' => '<b>'.__('Permissive').'</b>')); ?><br>
            <br>
            <?php echo __("If you're running a public tracker, or a tracker with several projects you probably want to use a restrictive security policy - however, with smaller teams or and simpler projects, permissive security policy will be most efficient."); ?><br>
            <i><?php echo __("Some permissions, such as configuration access are not affected by this setting, but must always be explicitly defined"); ?></i>
        </td>
    </tr>
    <tr>
        <td><label for="guestcaptcha"><?php echo __('Use captcha for guest actions'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_GUEST_CAPTCHA; ?>" id="guestcaptcha" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=1<?php if (\thebuggenie\core\framework\Settings::isGuestCaptchaEnabled()): ?> selected<?php endif; ?>><?php echo __('Guests must enter a captcha when reporting issues or posting commments.'); ?></option>
                <option value=0<?php if (!\thebuggenie\core\framework\Settings::isGuestCaptchaEnabled()): ?> selected<?php endif; ?>><?php echo __('Guests can report issues or post comments without captcha.'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2">
            <?php echo __("If you allow guests to report issues and post comments (without logging in), you can request that a captcha has to be filled-in every time a user performs these actions. This can help in reducing the amount of spam coming from bots on public issue trackers."); ?><br>
            <?php echo __("This setting does not affect the captcha on registration form, nor any other guest action. The captcha on registration form is mandatory."); ?><br>
        </td>
    </tr>
    <tr>
        <td><label for="allowreg"><?php echo __('Gravatar user icons'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>" id="allowreg" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=1<?php if (\thebuggenie\core\framework\Settings::isGravatarsEnabled()): ?> selected<?php endif; ?>><?php echo __('Users icons will use the gravatar.com service'); ?></option>
                <option value=0<?php if (!\thebuggenie\core\framework\Settings::isGravatarsEnabled()): ?> selected<?php endif; ?>><?php echo __('Users will use default user icons'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2">
            <?php echo __('Select whether to use the %gravatar.com user icon service for user avatars, or just use the default ones', array('%gravatar.com' => link_tag('http://www.gravatar.com', 'gravatar.com'))); ?>
        </td>
    </tr>
    <tr>
        <td><label for="allowreg"><?php echo __('New user accounts'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>" id="allowreg" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=1<?php if (\thebuggenie\core\framework\Settings::isRegistrationEnabled()): ?> selected<?php endif; ?>><?php echo __('Users can register new accounts'); ?></option>
                <option value=0<?php if (!\thebuggenie\core\framework\Settings::isRegistrationEnabled()): ?> selected<?php endif; ?>><?php echo __('All new user accounts will be created by an admin'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="allowpersona"><?php echo __('Enable Mozilla Persona'); ?></label></td>
        <td>
            <?php if (!\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ALLOW_PERSONA; ?>" id="allowpersona" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                    <option value="1"<?php if (\thebuggenie\core\framework\Settings::isPersonaEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes, let users log in with Mozilla Persona'); ?></option>
                    <option value="0"<?php if (!\thebuggenie\core\framework\Settings::isPersonaEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
                </select>
            <?php else: ?>
                <div class="faded_out"><?php echo __('Mozilla Persona support is unavailable when not using internal authentication'); ?></div>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td><label for="allowopenid"><?php echo __('Enable OpenID'); ?></label></td>
        <td>
            <?php if (!\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ALLOW_OPENID; ?>" id="allowopenid" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                    <option value="all"<?php if (\thebuggenie\core\framework\Settings::getOpenIDStatus() == 'all'): ?> selected<?php endif; ?>><?php echo __('Users can register with OpenID and link OpenID to existing accounts'); ?></option>
                    <option value="existing"<?php if (\thebuggenie\core\framework\Settings::getOpenIDStatus() == 'existing'): ?> selected<?php endif; ?>><?php echo __('Users can only link OpenID logins with existing accounts'); ?></option>
                    <option value="none"<?php if (\thebuggenie\core\framework\Settings::getOpenIDStatus() == 'none'): ?> selected<?php endif; ?>><?php echo __('OpenID authentication is disabled'); ?></option>
                </select>
            <?php else: ?>
                <div class="faded_out"><?php echo __('OpenID support is not available when not using internal authentication'); ?></div>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td><label for="limit_registration"><?php echo __('Registration domain whitelist'); ?></label></td>
        <td><input type="text" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_REGISTRATION_DOMAIN_WHITELIST; ?>" id="limit_registration"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> value="<?php echo \thebuggenie\core\framework\Settings::getRegistrationDomainWhitelist(); ?>" style="width: 400px;"></td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2"><?php echo __('Comma-separated list of allowed domains (ex: %example). Leave empty to allow all domains.', array('%example' => 'thebuggenie.com, zegeniestudios.net')); ?></td>
    </tr>
    <tr>
        <td><label for="defaultgroup"><?php echo __('Default user group'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_USER_GROUP; ?>" id="defaultgroup" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
            <?php foreach (\thebuggenie\core\entities\Group::getAll() as $aGroup): ?>
                <option value="<?php print $aGroup->getID(); ?>"<?php if (($default_group = \thebuggenie\core\framework\Settings::getDefaultGroup()) instanceof \thebuggenie\core\entities\Group && $default_group->getID() == $aGroup->getID()): ?> selected<?php endif; ?>><?php print $aGroup->getName(); ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2"><?php echo __('New users will automatically be added to this group'); ?></td>
    </tr>
    <tr>
        <td><label for="returnfromlogin"><?php echo __('Redirect after login'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_RETURN_FROM_LOGIN; ?>" id="returnfromlogin" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <?php $return_routes = array('home' => __('Frontpage'), 'dashboard' => __('Dashboard'), 'account' => __('Account details'), 'referer' => __('Page before login')); ?>
                <?php $return_routes = \thebuggenie\core\framework\Event::createNew('core', 'setting_returnfromlogin', null, array(), $return_routes)->trigger()->getReturnList(); ?>
                <?php foreach ($return_routes as $route => $description): ?> 
                    <option value="<?php echo $route; ?>"<?php if (\thebuggenie\core\framework\Settings::getLoginReturnRoute() == $route): ?> selected<?php endif; ?>><?php echo $description; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="returnfromlogout"><?php echo __('Redirect after logout'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_RETURN_FROM_LOGOUT; ?>" id="returnfromlogout" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <?php $return_routes = array('home' => __('Frontpage')); ?>
                <?php $return_routes = \thebuggenie\core\framework\Event::createNew('core', 'setting_returnfromlogout', null, array(), $return_routes)->trigger()->getReturnList(); ?>
                <?php foreach ($return_routes as $route => $description): ?> 
                    <option value="<?php echo $route; ?>"<?php if (\thebuggenie\core\framework\Settings::getLogoutReturnRoute() == $route): ?> selected<?php endif; ?>><?php echo $description; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="onlinestate"><?php echo __('User state when online'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ONLINESTATE; ?>" id="onlinestate" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
            <?php foreach ($userstates as $userstate): ?>
                <option value="<?php print $userstate->getID(); ?>"<?php if ($onlinestate instanceof \thebuggenie\core\entities\Userstate && $onlinestate->getID() == $userstate->getID()): ?> selected<?php endif; ?>><?php print $userstate->getName(); ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="awaystate"><?php echo __('User state when inactive'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_AWAYSTATE; ?>" id="awaystate" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
            <?php foreach ($userstates as $userstate): ?>
                <option value="<?php print $userstate->getID(); ?>"<?php if ($awaystate instanceof \thebuggenie\core\entities\Userstate && $awaystate->getID() == $userstate->getID()): ?> selected<?php endif; ?>><?php print $userstate->getName(); ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="offlinestate"><?php echo __('User state when offline'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_OFFLINESTATE; ?>" id="offlinestate" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
            <?php foreach ($userstates as $userstate): ?>
                <option value="<?php print $userstate->getID(); ?>"<?php if ($offlinestate instanceof \thebuggenie\core\entities\Userstate && $offlinestate->getID() == $userstate->getID()): ?> selected<?php endif; ?>><?php print $userstate->getName(); ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
</table>
