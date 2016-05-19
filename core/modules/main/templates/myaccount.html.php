<?php

    use thebuggenie\core\framework\Settings;

    $tbg_response->setTitle('Your account details');
    $tbg_response->addBreadcrumb(__('Account details'), make_url('account'));

?>
<?php if ($tbg_user->canChangePassword()): ?>
    <div class="fullpage_backdrop" id="change_password_div" style="<?php if (!$has_autopassword) echo 'display: none;'; ?>">
        <div class="backdrop_box login_page login_popup">
            <div class="backdrop_detail_content login_content">
                <div class="logindiv regular active" id="change_password_container">
                    <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                        <?php echo tbg_parse_text(\thebuggenie\core\framework\Settings::get('changepw_message'), false, null, array('embedded' => true)); ?>
                    <?php else: ?>
                        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_change_password'); ?>" onsubmit="TBG.Main.Profile.changePassword('<?php echo make_url('account_change_password'); ?>'); return false;" method="post" id="change_password_form">
                            <h2><?php echo __('Changing your password'); ?></h2>
                            <div class="article"><?php echo __('Enter your current password in the first box, then enter your new password twice (to prevent you from typing mistakes). Press the "%change_password" button to change your password.', array('%change_password' => __('Change password'))); ?></div>
                            <ul class="login_formlist">
                                <?php if (!$has_autopassword): ?>
                                    <li>
                                        <label for="current_password"><?php echo __('Current password'); ?></label>
                                        <input type="password" name="current_password" id="current_password" value="">
                                    </li>
                                <?php else: ?>
                                    <li style="display: none;">
                                        <input type="hidden" name="current_password" id="current_password" value="<?php echo $autopassword; ?>">
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <label for="new_password_1"><?php echo __('New password'); ?></label>
                                    <input type="password" name="new_password_1" id="new_password_1" value="">
                                </li>
                                <li>
                                    <label for="new_password_2"><?php echo __('New password (repeat it)'); ?></label>
                                    <input type="password" name="new_password_2" id="new_password_2" value="">
                                </li>
                            </ul>
                            <div class="login_button_container">
                                <?php echo image_tag('spinning_20.gif', array('id' => 'change_password_indicator', 'style' => 'display: none;')); ?>
                                <input type="submit" class="button button-silver" value="<?php echo __('Change password'); ?>">
                                <?php echo __('%change_password or %cancel', array('%change_password' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'change_password_div\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($tbg_user->isOpenIdLocked()): ?>
    <div class="fullpage_backdrop" id="pick_username_div" style="display: none;">
        <div class="backdrop_box login_page login_popup">
            <div class="backdrop_detail_content login_content">
                <div class="logindiv regular active" id="add_application_password_container">
                    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_check_username'); ?>" onsubmit="TBG.Main.Profile.checkUsernameAvailability('<?php echo make_url('account_check_username'); ?>'); return false;" method="post" id="check_username_form">
                        <h2><?php echo __('Picking a username'); ?></h2>
                        <div class="article">
                            <p><?php echo __('Since this account was created via an OpenID login, you will have to pick a username to be able to log in with a username or password. You can continue to use your account with your OpenID login, so this is only if you want to pick a username for your account.'); ?><p>
                            <p><?php echo __('Click "%check_availability" to see if your desired username is available.', array('%check_availability' => __('Check availability'))); ?></p>
                        </div>
                        <ul class="account_popupform">
                            <li>
                                <label for="username_pick"><?php echo __('Type desired username'); ?></label>
                                <input type="text" name="desired_username" id="username_pick">
                            </li>
                            <li id="username_unavailable" style="display: none;">
                                <?php echo __('This username is not available'); ?>
                            </li>
                        </ul>
                        <div class="login_button_container">
                            <?php echo image_tag('spinning_20.gif', array('id' => 'pick_username_indicator', 'style' => 'display: none;')); ?>
                            <input type="submit" class="button button-silver" value="<?php echo __('Check availability'); ?>">
                            <?php echo __('%check_availability or %cancel', array('%check_availability' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'pick_username_div\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="fullpage_backdrop" id="add_application_password_div" style="display: none;">
    <div class="backdrop_box login_page login_popup">
        <div class="backdrop_detail_content login_content">
            <div class="logindiv regular active" id="add_application_password_container">
                <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_add_application_password'); ?>" onsubmit="TBG.Main.Profile.addApplicationPassword('<?php echo make_url('account_add_application_password'); ?>'); return false;" method="post" id="add_application_password_form">
                    <h2><?php echo __('Add application-specific password'); ?></h2>
                    <div class="article"><?php echo __('Please enter the name of the application or computer which will be using this password. Examples include "Toms computer", "Work laptop", "My iPhone" and similar.'); ?></div>
                    <ul class="account_popupform">
                        <li>
                            <label for="add_application_password_name"><?php echo __('Application name'); ?></label>
                            <input type="text" name="name" id="add_application_password_name" value="">
                        </li>
                    </ul>
                    <div class="login_button_container">
                        <?php echo image_tag('spinning_20.gif', array('id' => 'add_application_password_indicator', 'style' => 'display: none;')); ?>
                        <input type="submit" class="button button-silver" value="<?php echo __('Add application password'); ?>">
                        <?php echo __('%add_application_password or %cancel', array('%add_application_password' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'add_application_password_div\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
                    </div>
                </form>
            </div>
            <div id="add_application_password_response" style="display: none;">
                <h2><?php echo __('Application password generated'); ?></h2>
                <div class="article"><?php echo __("Use this one-time password when authenticating with the application. Spaces don't matter, and you don't have to write it down."); ?></div>
                <div class="application_password_preview" id="application_password_preview"></div>
                <a href="<?php echo make_url('account'); ?>" class="button button-silver"><?php echo __('Done'); ?></a>
            </div>
        </div>
    </div>
</div>
<div id="account_info_container">
    <div id="account_user_info">
        <?php echo image_tag($tbg_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;', 'alt' => '[avatar]'), true); ?>
        <span id="user_name_span">
            <?php echo $tbg_user->getRealname(); ?><br>
            <?php if (!$tbg_user->isOpenIdLocked()): ?>
                <?php echo $tbg_user->getUsername(); ?>
            <?php endif; ?>
        </span>
    </div>
    <div style="margin: 30px 0 20px 0; table-layout: fixed; width: 100%; height: 100%;">
        <div style="clear: both;" class="tab_menu inset">
            <ul id="account_tabs">
                <li <?php if ($selected_tab == 'profile'): ?> class="selected"<?php endif; ?> id="tab_profile"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_profile', 'account_tabs', true);" href="javascript:void(0);"><?php echo fa_image_tag('pencil-square-o').__('Profile'); ?></a></li>
                <li id="tab_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_settings', 'account_tabs', true);" href="javascript:void(0);"><?php echo fa_image_tag('cog').__('Settings'); ?></a></li>
                <li id="tab_notificationsettings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_notificationsettings', 'account_tabs', true);" href="javascript:void(0);"><?php echo fa_image_tag('bell').__('Notification settings'); ?></a></li>
                <?php \thebuggenie\core\framework\Event::createNew('core', 'account_tabs')->trigger(); ?>
                <?php foreach (\thebuggenie\core\framework\Context::getModules() as $module_name => $module): ?>
                    <?php if ($module->hasAccountSettings()): ?>
                        <li id="tab_settings_<?php echo $module_name; ?>"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_settings_<?php echo $module_name; ?>', 'account_tabs', true);" href="javascript:void(0);"><?php echo $module->getAccountSettingsLogo().__($module->getAccountSettingsName()); ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <li <?php if ($selected_tab == 'security'): ?> class="selected"<?php endif; ?> id="tab_security"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_security', 'account_tabs', true);" href="javascript:void(0);"><?php echo fa_image_tag('lock').__('Security'); ?></a></li>
                <?php if (count($tbg_user->getScopes()) > 1): ?>
                    <li id="tab_scopes"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_scopes', 'account_tabs', true);" href="javascript:void(0);"><?php echo fa_image_tag('clone').__('Scope memberships'); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div id="account_tabs_panes">
            <div id="tab_profile_pane" style="<?php if ($selected_tab != 'profile'): ?> display: none;<?php endif; ?>">
                <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                    <?php echo tbg_parse_text(\thebuggenie\core\framework\Settings::get('changedetails_message'), false, null, array('embedded' => true)); ?>
                <?php else: ?>
                    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_information'); ?>" onsubmit="TBG.Main.Profile.updateInformation('<?php echo make_url('account_save_information'); ?>'); return false;" method="post" id="profile_information_form">
                        <h3><?php echo __('About yourself'); ?></h3>
                        <p><?php echo __('Edit your profile details here, including additional information (Required fields are marked with a little star). Keep in mind that some of this information may be seen by other users.'); ?></p>
                        <table class="padded_table" cellpadding=0 cellspacing=0>
                            <tr>
                                <td style="width: 300px;"><label for="profile_buddyname">* <?php echo __('Display name'); ?></label></td>
                                <td>
                                    <input type="text" name="buddyname" id="profile_buddyname" value="<?php echo $tbg_user->getBuddyname(); ?>" style="width: 200px;">
                                </td>
                            </tr>
                            <tr>
                                <td class="config_explanation" colspan="2"><?php echo __('This name is what other people will see you as.'); ?></td>
                            </tr>
                            <tr>
                                <td ><label for="profile_email">* <?php echo __('Email address'); ?></label></td>
                                <td>
                                    <input type="email" name="email" id="profile_email" value="<?php echo $tbg_user->getEmail(); ?>" style="width: 300px;">
                                </td>
                            </tr>
                            <tr>
                                <td ><label for="profile_email_private_yes">* <?php echo __('Show my email address to others'); ?></label></td>
                                <td>
                                    <input type="radio" name="email_private" value="0" id="profile_email_private_no"<?php if ($tbg_user->isEmailPublic()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_no"><?php echo __('Yes'); ?></label>&nbsp;&nbsp;
                                    <input type="radio" name="email_private" value="1" id="profile_email_private_yes"<?php if ($tbg_user->isEmailPrivate()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_email_private_yes"><?php echo __('No'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="config_explanation" colspan="2"><?php echo __('Whether your email address is visible to other users in your profile information card. The email address is always visible to admins.'); ?></td>
                            </tr>
                            <tr>
                                <td ><label for="profile_use_gravatar_yes"><?php echo __('Use Gravatar avatar'); ?></label></td>
                                <td>
                                    <input type="radio" name="use_gravatar" value="1" id="profile_use_gravatar_yes"<?php if ($tbg_user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_yes"><?php echo __('Yes'); ?></label>&nbsp;&nbsp;
                                    <input type="radio" name="use_gravatar" value="0" id="profile_use_gravatar_no"<?php if (!$tbg_user->usesGravatar()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_no"><?php echo __('No'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="config_explanation" colspan="2">
                                    <?php echo __("The Bug Genie can use your %link_to_gravatar profile picture, if you have one. If you don't have one but still want to use Gravatar for profile pictures, The Bug Genie will use a Gravatar %auto_generated_image_unique_for_your_email_address. Don't have a Gravatar yet? %link_to_get_one_now",
                                                    array('%link_to_gravatar' => link_tag('http://www.gravatar.com', 'Gravatar', ['target' => '_blank']),
                                                        '%auto_generated_image_unique_for_your_email_address' => link_tag('http://blog.gravatar.com/2008/04/22/identicons-monsterids-and-wavatars-oh-my', __('auto-generated image unique for your email address'), ['target' => '_blank']),
                                                        '%link_to_get_one_now' => link_tag('http://en.gravatar.com/site/signup/'.urlencode($tbg_user->getEmail()), __('Get one now!'), array('target' => '_blank')))); ?>
                                    <br>
                                    <a style="<?php if (!$tbg_user->usesGravatar()): ?>display: none; <?php endif; ?>" id="gravatar_change" href="http://en.gravatar.com/emails/" class="button button-silver">
                                        <?php echo image_tag('gravatar.png'); ?>
                                        <?php echo __('Change my profile picture / avatar'); ?>
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <h3><?php echo __('Language and location'); ?></h3>
                        <p><?php echo __('This information is used to provide a more localized experience based on your location and language preferences. Items such as timestamps will be displayed in your local timezone, and you can choose to use The Bug Genie in your own language.'); ?></p>
                        <table class="padded_table" cellpadding=0 cellspacing=0>
                            <tr>
                                <td style="width: 300px;"><label for="profile_timezone"><?php echo __('Current timezone'); ?></label></td>
                                <td>
                                    <select name="timezone" id="profile_timezone" style="width: 300px;">
                                        <option value="sys"<?php if (in_array($tbg_user->getTimezoneIdentifier(), array('sys', null))): ?> selected<?php endif; ?>><?php echo __('Use server timezone'); ?></option>
                                        <?php foreach ($timezones as $timezone => $description): ?>
                                            <option value="<?php echo $timezone; ?>"<?php if ($tbg_user->getTimezoneIdentifier() == $timezone): ?> selected<?php endif; ?>><?php echo $description; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="config_explanation" colspan="2">
                                    <?php echo __('Based on this information, the time at your location should be: %time', array('%time' => tbg_formatTime(time(), 1))); ?>
                                </td>
                            </tr>
                            <tr>
                                <td ><label for="profile_timezone"><?php echo __('Language'); ?></label></td>
                                <td>
                                    <select name="profile_language" id="profile_language" style="width: 300px;">
                                        <option value="sys"<?php if ($tbg_user->getLanguage() == 'sys'): ?> selected<?php endif; ?>><?php echo __('Use global setting - %lang', array('%lang' => \thebuggenie\core\framework\Settings::getLanguage())); ?></option>
                                    <?php foreach ($languages as $lang_code => $lang_desc): ?>
                                        <option value="<?php echo $lang_code; ?>" <?php if ($tbg_user->getLanguage() == $lang_code): ?> selected<?php endif; ?>><?php echo $lang_desc; ?><?php if (\thebuggenie\core\framework\Settings::getLanguage() == $lang_code): ?> <?php echo __('(site default)'); endif;?></option>
                                    <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <h3><?php echo __('Additional information'); ?></h3>
                        <p><?php echo __('You may want to provide more information about yourself here. This is completely optional, and only used to show more information about yourself to other users.'); ?></p>
                        <table class="padded_table" cellpadding=0 cellspacing=0>
                            <tr>
                                <td style="width: 200px;"><label for="profile_realname"><?php echo __('Full name'); ?></label></td>
                                <td>
                                    <input type="text" name="realname" id="profile_realname" value="<?php echo $tbg_user->getRealname(); ?>" style="width: 300px;">
                                </td>
                            </tr>
                            <tr>
                                <td ><label for="profile_homepage"><?php echo __('Homepage'); ?></label></td>
                                <td>
                                    <input type="url" name="homepage" id="profile_homepage" value="<?php echo $tbg_user->getHomepage(); ?>" style="width: 300px;">
                                </td>
                            </tr>
                        </table>
                        <div class="greybox" style="margin: 25px 0 0 0; height: 24px;">
                            <div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save" to save your account information', array('%save' => __('Save'))); ?></div>
                            <input type="submit" id="submit_information_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
                            <span id="profile_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <div id="tab_settings_pane" style="display: none;">
                <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_settings'); ?>" onsubmit="TBG.Main.Profile.updateSettings('<?php echo make_url('account_save_settings'); ?>'); return false;" method="post" id="profile_settings_form">
                    <h3><?php echo __('Navigation'); ?></h3>
                    <p><?php echo __('These settings apply to all areas of The Bug Genie, and lets you customize your experience to fit your own style.'); ?></p>
                    <table class="padded_table" cellpadding=0 cellspacing=0>
                        <tr>
                            <td style="width: 200px;"><label for="profile_enable_keyboard_navigation_yes"><?php echo __('Enable keyboard navigation'); ?></label></td>
                            <td>
                                <input type="radio" name="enable_keyboard_navigation" value="1" id="profile_enable_keyboard_navigation_yes"<?php if ($tbg_user->isKeyboardNavigationEnabled()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_yes"><?php echo __('Yes'); ?></label>&nbsp;&nbsp;
                                <input type="radio" name="enable_keyboard_navigation" value="0" id="profile_enable_keyboard_navigation_no"<?php if (!$tbg_user->isKeyboardNavigationEnabled()): ?> checked<?php endif; ?>>&nbsp;<label for="profile_use_gravatar_no"><?php echo __('No'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="config_explanation" colspan="2">
                                <?php echo __('Lets you use arrow up / down in issue lists to navigate'); ?><br>
                            </td>
                        </tr>
                    </table>
                    <h3><?php echo __('Editing'); ?></h3>
                    <p><?php echo __('The settings you select here will be used as the default formatting syntax for comments you post, issues you create and articles you write. Remember that you can switch this on a case by case basis - look for the syntax selector next to any text area with formatting buttons.'); ?></p>
                    <table class="padded_table" cellpadding=0 cellspacing=0>
                        <tr>
                            <td colspan="2">
                                <table class="profile_syntax_table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><?php echo __('Mediawiki'); ?></th>
                                            <th><?php echo __('Markdown'); ?></th>
                                            <th><?php echo __('Plain text'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><label for="syntax_issues_md"><?php echo __('Preferred syntax when creating issues'); ?></label></td>
                                            <td><input type="radio" name="syntax_issues" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_MW; ?>" id="syntax_issues_mw" <?php if ($tbg_user->getPreferredIssuesSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_MW) echo 'checked'; ?>></td>
                                            <td><input type="radio" name="syntax_issues" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_MD; ?>" id="syntax_issues_md" <?php if ($tbg_user->getPreferredIssuesSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_MD) echo 'checked'; ?>></td>
                                            <td><input type="radio" name="syntax_issues" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_PT; ?>" id="syntax_issues_pt" <?php if ($tbg_user->getPreferredIssuesSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_PT) echo 'checked'; ?>></td>
                                        </tr>
                                        <tr>
                                            <td><label for="syntax_articles_mw"><?php echo __('Preferred syntax when creating articles'); ?></label></td>
                                            <td><input type="radio" name="syntax_articles" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_MW; ?>" id="syntax_articles_mw" <?php if ($tbg_user->getPreferredWikiSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_MW) echo 'checked'; ?>></td>
                                            <td><input type="radio" name="syntax_articles" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_MD; ?>" id="syntax_articles_md" <?php if ($tbg_user->getPreferredWikiSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_MD) echo 'checked'; ?>></td>
                                            <td><input type="radio" name="syntax_articles" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_PT; ?>" id="syntax_articles_pt" <?php if ($tbg_user->getPreferredWikiSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_PT) echo 'checked'; ?>></td>
                                        </tr>
                                        <tr>
                                            <td><label for="syntax_comments_md"><?php echo __('Preferred syntax when posting comments'); ?></label></td>
                                            <td><input type="radio" name="syntax_comments" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_MW; ?>" id="syntax_comments_mw" <?php if ($tbg_user->getPreferredCommentsSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_MW) echo 'checked'; ?>></td>
                                            <td><input type="radio" name="syntax_comments" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_MD; ?>" id="syntax_comments_md" <?php if ($tbg_user->getPreferredCommentsSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_MD) echo 'checked'; ?>></td>
                                            <td><input type="radio" name="syntax_comments" value="<?php echo \thebuggenie\core\framework\Settings::SYNTAX_PT; ?>" id="syntax_comments_pt" <?php if ($tbg_user->getPreferredCommentsSyntax(true) == \thebuggenie\core\framework\Settings::SYNTAX_PT) echo 'checked'; ?>></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="greybox" style="margin: 25px 0 0 0; height: 24px;">
                        <div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save" to update the settings on this tab', array('%save' => __('Save'))); ?></div>
                        <input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
                        <span id="profile_settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
                    </div>
                </form>
            </div>
            <div id="tab_notificationsettings_pane" style="display: none;">
                <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_settings'); ?>" onsubmit="TBG.Main.Profile.updateNotificationSettings('<?php echo make_url('account_save_notificationsettings'); ?>'); return false;" method="post" id="profile_notificationsettings_form">
                    <h3><?php echo __('Subscriptions'); ?></h3>
                    <p><?php echo __('The Bug Genie can subscribe you to issues, articles and other items in the system, so you can receive notifications when they are updated. Please select when you would like The Bug Genie to subscribe you.'); ?></p>
                    <table class="padded_table" cellpadding=0 cellspacing=0>
                        <?php foreach ($subscriptionssettings as $key => $description): ?>
                            <?php if (in_array($key, [Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY, Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS])) continue; ?>
                            <tr>
                                <td style="width: auto; border-bottom: 1px solid #DDD;"><label for="<?php echo $key; ?>_yes"><?php echo $description ?></label></td>
                                <?php if ($key == \thebuggenie\core\framework\Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY): ?>
                                    <td style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                                        <div class="filter interactive_dropdown" data-filterkey="<?php echo $key; ?>" data-value="" data-all-value="<?php echo __('All'); ?>">
                                            <input type="hidden" name="core_<?= $key; ?>" value="" id="filter_<?php echo $key; ?>_value_input">
                                            <label><?php echo __('Category'); ?></label>
                                            <span class="value"><?php if (true || !$filter->hasValue()) echo __('All'); ?></span>
                                            <div class="interactive_menu">
                                                <h1><?php echo __('Select category'); ?></h1>
                                                <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
                                                <div class="interactive_values_container">
                                                    <ul class="interactive_menu_values">
                                                        <?php foreach (\thebuggenie\core\entities\Category::getAll() as $category_id => $category): ?>
                                                            <li data-value="<?php echo $category_id; ?>" class="filtervalue<?php if (false && $filter->hasValue($category_id)) echo ' selected'; ?>">
                                                                <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                                <input type="checkbox" value="<?php echo $category_id; ?>" name="core_<?php echo $key; ?>_value_<?php echo $category_id; ?>" data-text="<?php echo __($category->getName()); ?>" id="core_<?php echo $key; ?>_value_<?php echo $category_id; ?>" <?php if (false && $filter->hasValue($category_id)) echo 'checked'; ?>>
                                                                <label for="core_<?php echo $key; ?>_value_<?php echo $category_id; ?>"><?php echo __($category->getName()); ?></label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                                        <input type="checkbox" name="core_<?php echo $key; ?>" value="1" id="<?php echo $key; ?>_yes"<?php if (!$tbg_user->getNotificationSetting($key, true)->isOff()): ?> checked<?php endif; ?>>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php $category_key = \thebuggenie\core\framework\Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY; ?>
                    <?php $project_issues_key = \thebuggenie\core\framework\Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS; ?>
                    <table class="padded_table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: auto; border-bottom: 1px solid #DDD;"><label for="<?php echo $project_issues_key; ?>_yes"><?= __('Automatically subscribe to new issues that are created in my project(s)'); ?></label></td>
                            <td style="width: 350px; text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
                                <div class="filter interactive_dropdown rightie" data-filterkey="<?php echo $project_issues_key; ?>" data-value="" data-all-value="<?php echo __('No projects'); ?>">
                                    <input type="hidden" name="core_<?= $project_issues_key; ?>" value="<?= join(',', $selected_project_subscriptions); ?>" id="filter_<?php echo $project_issues_key; ?>_value_input">
                                    <label><?php echo __('Projects'); ?></label>
                                    <span class="value"><?= (empty($selected_project_subscriptions) && !$all_projects_subscription) ? __('No projects') : __('All my projects'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?php echo __('Select which projects to subscribe to'); ?></h1>
                                        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter projects'); ?>">
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <li data-value="0" class="filtervalue <?php if ($all_projects_subscription) echo ' selected'; ?>" data-exclusive data-selection-group="1" data-exclude-group="2">
                                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                    <input type="checkbox" value="all" name="core_<?php echo $project_issues_key; ?>_all" data-text="<?php echo __('All my projects'); ?>" id="core_<?= $project_issues_key; ?>_value_all" <?php if ($all_projects_subscription) echo 'checked'; ?>>
                                                    <label for="core_<?= $project_issues_key; ?>_value_all"><?php echo __('All my projects'); ?></label>
                                                </li>
                                                <li class="separator"></li>
                                                <?php foreach ($projects as $project_id => $project): ?>
                                                    <li data-value="<?php echo $project_id; ?>" class="filtervalue<?php if (in_array($project_id, $selected_project_subscriptions)) echo ' selected'; ?>" data-selection-group="2" data-exclude-group="1">
                                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                        <input type="checkbox" value="<?php echo $project_id; ?>" name="core_<?php echo $project_issues_key; ?>_<?php echo $project_id; ?>" data-text="<?php echo __($project->getName()); ?>" id="core_<?php echo $project_issues_key; ?>_value_<?php echo $project_id; ?>" <?php if (in_array($project_id, $selected_project_subscriptions)) echo 'checked'; ?>>
                                                        <label for="core_<?php echo $project_issues_key; ?>_value_<?php echo $project_id; ?>"><?php echo __($project->getName()); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #DDD;">
                                <label for="<?php echo $category_key; ?>_yes"><?= __('Automatically subscribe to new issues in selected categories'); ?></label><br>
                                <?= __("If you don't want to set up automatic subscriptions for all projects you're participating in, you can choose to subscribe to categories, instead. Note that if '%all_my_projects' is selected in the project subscriptions dropdown, the category subscription will have no further effect.", ['%all_my_projects' => __('All my projects')]); ?>
                            </td>
                            <td style="text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
                                <div class="filter interactive_dropdown rightie" data-filterkey="<?php echo $category_key; ?>" data-value="" data-all-value="<?php echo __('None selected'); ?>">
                                    <input type="hidden" name="core_<?= $category_key; ?>" value="<?= join(',', $selected_category_subscriptions); ?>" id="filter_<?php echo $category_key; ?>_value_input">
                                    <label><?php echo __('Categories'); ?></label>
                                    <span class="value"><?php if (empty($selected_category_subscriptions)) echo __('None selected'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?php echo __('Select which categories to subscribe to'); ?></h1>
                                        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter categories'); ?>">
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <?php foreach ($categories as $category_id => $category): ?>
                                                    <li data-value="<?php echo $category_id; ?>" class="filtervalue<?php if (in_array($category_id, $selected_category_subscriptions)) echo ' selected'; ?>">
                                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                        <input type="checkbox" value="<?php echo $category_id; ?>" name="core_<?php echo $category_key; ?>_<?php echo $category_id; ?>" data-text="<?php echo __($category->getName()); ?>" id="core_<?php echo $category_key; ?>_value_<?php echo $category_id; ?>" <?php if (in_array($category_id, $selected_category_subscriptions)) echo 'checked'; ?>>
                                                        <label for="core_<?php echo $category_key; ?>_value_<?php echo $category_id; ?>"><?php echo __($category->getName()); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <h3><?php echo __('Notifications'); ?></h3>
                    <p><?php echo __('The Bug Genie will send you notifications based on system actions and/or your subscriptions. Notifications can be receieved in the notifications box (the counter visible next to your avatar in the top menu) and/or via email.'); ?></p>
                    <table class="padded_table" cellpadding=0 cellspacing=0>
                        <thead>
                            <tr>
                                <th></th>
                                <th style="white-space: nowrap; width: 120px;"><?php echo __('Notifications box'); ?></th>
                                <?php \thebuggenie\core\framework\Event::createNew('core', 'account_pane_notificationsettings_thead')->trigger(); ?>
                            </tr>
                        </thead>
                        <?php foreach ($notificationsettings as $key => $description): ?>
                            <?php if ($key == \thebuggenie\core\framework\Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY) continue; ?>
                            <tr>
                                <td style="width: auto; border-bottom: 1px solid #DDD;"><label for="<?php echo $key; ?>_yes"><?php echo $description ?></label></td>
                                <?php if ($key == \thebuggenie\core\framework\Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS): ?>
                                    <td style="text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                                        <input type="text" name="core_<?php echo $key; ?>" id="<?php echo $key; ?>_yes" value="<?php echo $tbg_user->getNotificationSetting($key, false, 'core')->getValue(); ?>" style="width:30px;">
                                    </td>
                                    <td style="text-align: center; border-bottom: 1px solid #DDD;" valign="middle"></td>
                                <?php else: ?>
                                    <td style="text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                                        <input type="checkbox" name="core_<?php echo $key; ?>" value="1" id="<?php echo $key; ?>_yes"<?php if ($tbg_user->getNotificationSetting($key, $key == Settings::SETTINGS_USER_NOTIFY_MENTIONED, 'core')->isOn()) echo ' checked'; ?>>
                                    </td>
                                    <?php \thebuggenie\core\framework\Event::createNew('core', 'account_pane_notificationsettings_cell')->trigger(compact('key')); ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php $category_key = \thebuggenie\core\framework\Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY; ?>
                    <table class="padded_table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: auto; border-bottom: 1px solid #DDD; vertical-align: middle;">
                                <label for="<?php echo $category_key; ?>_yes"><?= __('Notify to notifications box when issues are created in selected categories') ?></label><br>
                                <?= __('If you want to be notified when an issue is created in a specific category, but do not want to automatically subscribe for updates to these issues, make sure auto-subscriptions are turned off in the "%subscriptions"-section, then use this dropdown to configure notifications.', ['%subscriptions' => __('Subscriptions')]); ?>
                            </td>
                            <td style="width: 350px; text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
                                <label><?= __('Notifications box'); ?></label><br>
                                <div class="filter interactive_dropdown rightie" data-filterkey="<?php echo $category_key; ?>" data-value="" data-all-value="<?php echo __('None selected'); ?>">
                                    <input type="hidden" name="core_<?= $category_key; ?>" value="<?= join(',', $selected_category_notifications); ?>" id="filter_<?php echo $category_key; ?>_value_input">
                                    <label><?php echo __('Categories'); ?></label>
                                    <span class="value"><?php if (empty($selected_category_notifications)) echo __('None selected'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?php echo __('Select which categories to subscribe to'); ?></h1>
                                        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter categories'); ?>">
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <?php foreach ($categories as $category_id => $category): ?>
                                                    <li data-value="<?php echo $category_id; ?>" class="filtervalue<?php if (in_array($category_id, $selected_category_notifications)) echo ' selected'; ?>">
                                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                        <input type="checkbox" value="<?php echo $category_id; ?>" name="core_<?php echo $category_key; ?>_<?php echo $category_id; ?>" data-text="<?php echo __($category->getName()); ?>" id="core_<?php echo $category_key; ?>_value_<?php echo $category_id; ?>" <?php if (in_array($category_id, $selected_category_notifications)) echo 'checked'; ?>>
                                                        <label for="core_<?php echo $category_key; ?>_value_<?php echo $category_id; ?>"><?php echo __($category->getName()); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <?php \thebuggenie\core\framework\Event::createNew('core', 'account_pane_notificationsettings_notification_categories')->trigger(compact('categories')); ?>
                            </td>
                        </tr>
                    </table>
                    <?php \thebuggenie\core\framework\Event::createNew('core', 'account_pane_notificationsettings_subscriptions')->trigger(compact('categories')); ?>
                    <h3><?php echo __('Desktop notifications'); ?></h3>
                    <p><?php echo __('You can receive desktop notifications based on system actions or your subscriptions. Choose your desktop notification preferences from this section.'); ?></p>
                    <table class="padded_table desktop-notifications-settings" cellpadding=0 cellspacing=0>
                        <tr>
                            <td><label for="profile_enable_desktop_notifications"><?php echo __('Enable desktop notifications'); ?></label></td>
                            <td>
                                <input type="button" class="button button-silver" value="<?php echo __('Grant Permission'); ?>" id="profile_enable_desktop_notifications" onclick="TBG.Main.Notifications.Web.GrantPermissionOrSendTest('<?php echo __('Test notification'); ?>', '<?php echo __('This is a test notification.'); ?>', '<?php echo \thebuggenie\core\framework\Settings::isUsingCustomFavicon() ? \thebuggenie\core\framework\Settings::getFaviconURL() : image_url('favicon.png'); ?>');">
                            </td>
                        </tr>
                        <tr>
                            <td class="config_explanation" colspan="2">
                                <?php echo __('If your web browser supports desktop notification, The Bug Genie can show a desktop notification whenever a new notification is received. To allow this, please click the "%grant_permission" button', ['%grant_permission' => __('Grant permission')]); ?>
                            </td>
                        </tr>
                    </table>
                    <table class="padded_table desktop-notifications-settings" cellpadding=0 cellspacing=0>
                        <tr>
                            <td><label for="profile_enable_desktop_notifications_new_tab"><?php echo __('Open desktop notifications in new tab'); ?></label></td>
                            <td>
                                <input type="checkbox" name="enable_desktop_notifications_new_tab" value="1" id="profile_enable_desktop_notifications_new_tab"<?php if ($tbg_user->isDesktopNotificationsNewTabEnabled()): ?> checked<?php endif; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td class="config_explanation" colspan="2">
                                <?php echo __('Whether clicking on notification will open target url in new or current tab. Notifications which target is backdrop will not be affected and will open in current tab. By default browsers will block opening of new tab programmatically, unless you enable pop-ups.'); ?>
                            </td>
                        </tr>
                    </table>
                    <div class="greybox" style="margin: 25px 0 0 0; height: 24px;">
                        <div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save" to update the settings on this tab', array('%save' => __('Save'))); ?></div>
                        <input type="submit" id="submit_notificationsettings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
                        <span id="profile_notificationsettings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
                    </div>
                </form>
            </div>
            <div id="tab_security_pane" style="<?php if ($selected_tab != 'security'): ?> display: none;<?php endif; ?>">
                <h3 style="position: relative;">
                    <?php echo __('Passwords and keys'); ?>
                    <a class="button button-silver dropper" id="password_actions" href="javascript:void(0);"><?php echo __('Actions'); ?></a>
                    <ul id="password_more_actions" style="width: 300px; font-size: 0.8em; text-align: right; top: 29px; margin-top: 0; right: 3px; z-index: 1000;" class="more_actions_dropdown popup_box dropper">
                        <?php if ($tbg_user->canChangePassword() && !$tbg_user->isOpenIdLocked()): ?>
                            <li><a href="javascript:void(0);" onclick="$('change_password_div').toggle();"><?php echo __('Change my password'); ?></a></li>
                        <?php elseif ($tbg_user->isOpenIdLocked()): ?>
                            <li><a href="javascript:void(0);" onclick="$('pick_username_div').toggle();" id="pick_username_button"><?php echo __('Pick a username'); ?></a></li>
                        <?php else: ?>
                            <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('Changing password disabled'); ?>', '<?php echo __('Changing your password can not be done via this interface. Please contact your administrator to change your password.'); ?>');" class="disabled"><?php echo __('Change my password'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="javascript:void(0);" onclick="$('add_application_password_div').toggle();"><?php echo __('Add application-specific password'); ?></a></li>
                    </ul>
                </h3>
                <p><?php echo __("When authenticating with The Bug Genie you only use your main password on the website - other applications and RSS feeds needs specific access tokens that you can enable / disable on an individual basis. You can control all your passwords and keys from here."); ?></p>
                <ul class="access_keys_list">
                    <li>
                        <button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Regenerate your RSS key?'); ?>', '<?php echo __('Do you really want to regenerate your RSS access key? By doing this all your previously bookmarked or linked RSS feeds will stop working and you will have to get the link from inside The Bug Genie again.'); ?>', {yes: {href: '<?php echo make_url('account_regenerate_rss_key', array('csrf_token' => \thebuggenie\core\framework\Context::generateCSRFtoken())); ?>'}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Reset'); ?></button>
                        <h4><?php echo __('RSS feeds access key'); ?></h4>
                        <p><?php echo __('Automatically used as part of RSS feed URLs. Regenerating this key prevents your previous RSS feed links from working.'); ?></p>
                    </li>
                    <?php foreach ($tbg_user->getApplicationPasswords() as $password): ?>
                        <li id="application_password_<?php echo $password->getID(); ?>">
                            <button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove this application-specific password?'); ?>', '<?php echo __('Do you really want to remove this application-specific password? By doing this, that application will no longer have access, and you will have to generate a new application password for the application to regain access.'); ?>', {yes: {click: function() {TBG.Main.Profile.removeApplicationPassword('<?php echo make_url('account_remove_application_password', array('id' => $password->getID(), 'csrf_token' => \thebuggenie\core\framework\Context::generateCSRFtoken())); ?>', <?php echo $password->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Delete'); ?></button>
                            <h4><?php echo __('Application password: %password_name', array('%password_name' => $password->getName())); ?></h4>
                            <p><?php echo __('Last used: %last_used_time, created at: %created_at_time', array('%last_used_time' => ($password->getLastUsedAt()) ? tbg_formatTime($password->getLastUsedAt(), 20) : __('never used'), '%created_at_time' => tbg_formatTime($password->getCreatedAt(), 20))); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (\thebuggenie\core\framework\Settings::isOpenIDavailable()): ?>
                    <h3>
                        <?php echo __('Linked OpenID accounts'); ?>
                        <button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'openid')); ?>');"><?php echo __('Link an OpenID account'); ?></button>
                    </h3>
                    <p><?php echo __("Via %openid you can log in to The Bug Genie by authenticating via Google, Wordpress and a lot of other websites. This means you don't have to register an account specifically for The Bug Genie, but authenticate with your existing Google, Wordpress, etc. user account instead. The Bug Genie will not receive or store your external usernames or passwords.", array('%openid' => link_tag('http://openid.net', 'OpenID'))); ?></p>
                    <div class="faded_out" id="no_openid_accounts"<?php if (count($tbg_user->getOpenIDAccounts())): ?> style="display: none;"<?php endif; ?>><?php echo __('You have not linked your account with any external authentication providers.'); ?></div>
                    <?php if (count($tbg_user->getOpenIDAccounts())): ?>
                        <ul class="simple_list openid_accounts_list hover_highlight" id="openid_accounts_list">
                        <?php foreach ($tbg_user->getOpenIDAccounts() as $identity => $details): ?>
                            <li id="openid_account_<?php echo $details['id']; ?>">
                                <?php if (count($tbg_user->getOpenIDAccounts()) > 1 || !$tbg_user->isOpenIDLocked()): ?>
                                    <button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove this account link?'); ?>', '<?php echo __('Do you really want to remove the link to this external account?').'<br>'.__('By doing this, it will not be possible to log into this account via this authentication provider'); ?>', {yes: {click: function() {TBG.Main.Profile.removeOpenIDIdentity('<?php echo make_url('account_remove_openid', array('openid' => $details['id'], 'csrf_token' => \thebuggenie\core\framework\Context::generateCSRFtoken())); ?>', <?php echo $details['id']; ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Delete'); ?></button>
                                <?php endif; ?>
                                <?php echo image_tag('openid_providers.small/'.$details['type'].'.ico.png'); ?>
                                <span class="openid_provider_name">
                                    <?php if ($details['type'] == 'google' || $details['type'] == 'google_profile'): ?>
                                        <?php echo __('Google account'); ?>
                                    <?php elseif ($details['type'] == 'yahoo'): ?>
                                        <?php echo __('Yahoo account'); ?>
                                    <?php elseif ($details['type'] == 'blogger'): ?>
                                        <?php echo __('Blogger (google) account'); ?>
                                    <?php elseif ($details['type'] == 'wordpress'): ?>
                                        <?php echo __('Wordpress account'); ?>
                                    <?php elseif ($details['type'] == 'launchpad'): ?>
                                        <?php echo __('Launchpad account'); ?>
                                    <?php else: ?>
                                        <?php echo __('Other OpenID provider'); ?>
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'account_tab_panes')->trigger(); ?>
            <?php foreach (\thebuggenie\core\framework\Context::getModules() as $module_name => $module): ?>
                <?php if ($module->hasAccountSettings()): ?>
                    <div id="tab_settings_<?php echo $module_name; ?>_pane" style="display: none;">
                        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_save_module_settings', array('target_module' => $module_name)); ?>" onsubmit="TBG.Main.Profile.updateModuleSettings('<?php echo make_url('account_save_module_settings', array('target_module' => $module_name)); ?>', '<?php echo $module_name; ?>'); return false;" method="post" id="profile_<?php echo $module_name; ?>_form">
                            <?php include_component("{$module_name}/accountsettings", array('module' => $module)); ?>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (count($tbg_user->getScopes()) > 1): ?>
                <div id="tab_scopes_pane" style="display: none;">
                    <h3><?php echo __('Pending memberships'); ?></h3>
                    <ul class="simple_list" id="pending_scope_memberships">
                        <?php foreach ($tbg_user->getUnconfirmedScopes() as $scope): ?>
                            <?php include_component('main/userscope', array('scope' => $scope)); ?>
                        <?php endforeach; ?>
                    </ul>
                    <span id="no_pending_scope_memberships" class="faded_out" style="<?php if (count($tbg_user->getUnconfirmedScopes())): ?>display: none;<?php endif; ?>"><?php echo __('You have no pending scope memberships'); ?></span>
                    <h3 style="margin-top: 20px;"><?php echo __('Confirmed memberships'); ?></h3>
                    <ul class="simple_list" id="confirmed_scope_memberships">
                        <?php foreach ($tbg_user->getConfirmedScopes() as $scope_id => $scope): ?>
                            <?php include_component('main/userscope', array('scope' => $scope)); ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var TBG, jQuery;
    require(['domReady', 'thebuggenie/tbg', 'jquery', 'jquery.nanoscroller'], function (domReady, tbgjs, jquery, nanoscroller) {
        domReady(function () {
            TBG = tbgjs;

            TBG.Main.Helpers.tabSwitchFromHash('account_tabs');

            $$('.filter').each(function (filter) {
                TBG.Search.initializeFilterField(filter);
            });

            <?php if ($error): ?>
                TBG.Main.Helpers.Message.error('<?php echo __('An error occurred'); ?>', '<?php echo $error; ?>');
            <?php endif; ?>
            <?php if ($rsskey_generated): ?>
                TBG.Main.Helpers.Message.success('<?php echo __('Your RSS key has been regenerated'); ?>', '<?php echo __('All previous RSS links have been invalidated.'); ?>');
            <?php endif; ?>
            <?php if ($username_chosen): ?>
                TBG.Main.Helpers.Message.success('<?php echo __("You\'ve chosen the username \'%username\'", array('%username' => $tbg_user->getUsername())); ?>', '<?php echo __('Before you can use the new username to log in, you must pick a password via the "%change_password" button.', array('%change_password' => __('Change password'))); ?>');
            <?php endif; ?>
            <?php if ($openid_used): ?>
                TBG.Main.Helpers.Message.error('<?php echo __('This OpenID identity is already in use'); ?>', '<?php echo __('Someone is already using this identity. Check to see if you have already added this account.'); ?>');
            <?php endif; ?>
        });
    });
</script>
