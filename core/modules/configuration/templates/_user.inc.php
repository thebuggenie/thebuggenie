<?php

    $themes = \thebuggenie\core\framework\Context::getThemes();
    $languages = \thebuggenie\core\framework\I18n::getLanguages();
    
?>
<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
    <tr>
        <td><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_yes"><?= __('Enable elevated login for configuration section'); ?></label></td>
        <td>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_yes" value=0<?php if (\thebuggenie\core\framework\Settings::isElevatedLoginRequired()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_no" value=1<?php if (!\thebuggenie\core\framework\Settings::isElevatedLoginRequired()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
            <?= config_explanation(
                __('If this is turned on, users will have to re-enter their password to go to the configuration section')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="requirelogin"><?= __('Allow anonymous access'); ?></label></td>
        <td>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isLoginRequired()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isLoginRequired()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_REQUIRE_LOGIN; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
            <?= config_explanation(
                __('If anonymous access is turned off, a valid user account is required to access any content in this installation')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>_yes"><?= __('Enable %gravatar user icons', ['%gravatar' => link_tag('https://gravatar.com', 'gravatar.com')]); ?></label></td>
        <td>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isGravatarsEnabled()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isGravatarsEnabled()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ENABLE_GRAVATARS; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
            <?= config_explanation(
                __('Select whether to use the %gravatar.com user icon service for user avatars, or just use the default ones', array('%gravatar.com' => link_tag('http://www.gravatar.com', 'gravatar.com')))
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>>"><?= __('Allow self-registration'); ?></label></td>
        <td>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isRegistrationEnabled()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isRegistrationEnabled()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_ALLOW_REGISTRATION; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
        </td>
    </tr>
    <tr>
        <td><label for="limit_registration"><?= __('Registration domain whitelist'); ?></label></td>
        <td>
            <textarea name="<?= \thebuggenie\core\framework\Settings::SETTING_REGISTRATION_DOMAIN_WHITELIST; ?>"
                   id="limit_registration"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>
                   style="width: 394px; height: 50px;"><?= \thebuggenie\core\framework\Settings::getRegistrationDomainWhitelist(); ?></textarea>
            <?= config_explanation(
                __('Comma-separated list of allowed domains (ex: %example). Leave empty to allow all domains.', array('%example' => 'thebuggenie.com, zegeniestudios.net'))
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="displayname_format"><?= __('User\'s display name format'); ?></label></td>
        <td>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_no" value=<?= \thebuggenie\core\framework\Settings::USER_DISPLAYNAME_FORMAT_BUDDY; ?> <?php if (\thebuggenie\core\framework\Settings::getUserDisplaynameFormat() == \thebuggenie\core\framework\Settings::USER_DISPLAYNAME_FORMAT_BUDDY): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Prefer nickname'); ?></label>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_yes" value=<?= \thebuggenie\core\framework\Settings::USER_DISPLAYNAME_FORMAT_REALNAME; ?> <?php if (\thebuggenie\core\framework\Settings::getUserDisplaynameFormat() == \thebuggenie\core\framework\Settings::USER_DISPLAYNAME_FORMAT_REALNAME): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Prefer full name'); ?></label>
        </td>
    </tr>
    <tr>
        <td><label for="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>_yes"><?= __('Security policy'); ?></label></td>
        <td>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isPermissive()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Restrictive'); ?></label>
            <input type="radio" name="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isPermissive()): ?> checked<?php endif; ?>><label for="<?= \thebuggenie\core\framework\Settings::SETTING_IS_PERMISSIVE_MODE; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Permissive'); ?></label>
            <?= config_explanation(
                __("%restrictive: With this security policy, users don't automatically get access to projects, modules, etc., but must be granted access specifically.", array('%restrictive' => '<b>'.__('Restrictive').'</b>')) .
                "<br>" .
                __("%permissive: This security policy assume you have access to things like projects, pages, etc.", array('%permissive' => '<b>'.__('Permissive').'</b>')) .
                "<br><br>" .
                __("If you're running a public tracker, or a tracker with several projects you probably want to use a restrictive security policy - however, with smaller teams or and simpler projects, permissive security policy will be most efficient.") .
                "<br><br><i>" .
                __("Some permissions, such as configuration access are not affected by this setting, but must always be explicitly defined").
                "</i>"
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="<?= \thebuggenie\core\framework\Settings::SETTING_USER_GROUP; ?>"><?= __('Default user group'); ?></label></td>
        <td>
            <select name="<?= \thebuggenie\core\framework\Settings::SETTING_USER_GROUP; ?>" id="<?= \thebuggenie\core\framework\Settings::SETTING_USER_GROUP; ?>" style="width: 400px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <?php foreach (\thebuggenie\core\entities\Group::getAll() as $aGroup): ?>
                    <option value="<?php print $aGroup->getID(); ?>"<?php if (($default_group = \thebuggenie\core\framework\Settings::getDefaultGroup()) instanceof \thebuggenie\core\entities\Group && $default_group->getID() == $aGroup->getID()): ?> selected<?php endif; ?>><?php print $aGroup->getName(); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
</table>
