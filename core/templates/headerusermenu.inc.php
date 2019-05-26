<?php

/**
 * @var \thebuggenie\core\entities\User $tbg_user
 */

?>
<nav class="header_menu" id="header_userinfo">
    <?php if (!$tbg_user->isGuest()): ?>
    <?php endif; ?>
    <ul>
        <?php if ($tbg_user->canAccessConfigurationPage()): ?>
            <li id="header_config_link" class="<?php if (in_array(\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteModule(), ['configuration', 'import'])) echo ' selected'; ?>">
                <?= link_tag(make_url('configure'), fa_image_tag('cog')); ?>
            </li>
        <?php endif; ?>
        <?php if (!$tbg_user->isGuest()): ?>
            <li class="user_notifications_container" id="user_notifications_container">
                <div id="user_notifications_count" class="notifications-indicator" data-callback-url=""><?= image_tag('spinning_16_white.gif'); ?></div>
                <a href="javascript:void(0);" class="dropper"><?= fa_image_tag('bell'); ?></a>
                <div class="popup_box tab_menu_dropdown notifications" id="user_notifications">
                    <div class="header with-link">
                        <span><?= __('Your notifications'); ?></span>
                        <a class="icon-link" href="javascript:void(0);" onclick="TBG.Main.Notifications.markAllRead();"><?= fa_image_tag('check'); ?></a>
                    </div>
                    <div id="user_notifications_list_wrapper_nano" class="nano">
                        <div id="user_notifications_list_wrapper" class="nano-content">
                            <ul id="user_notifications_list" data-notifications-url="<?= make_url('get_partial_for_backdrop', array('key' => 'notifications')); ?>" data-offset="25"></ul>
                        </div>
                    </div>
                    <?= image_tag('spinning_32.gif', array('id' => 'user_notifications_loading_indicator')); ?>
                </div>
            </li>
        <?php endif; ?>
        <li class="with-dropdown <?php if ($tbg_request->hasCookie('original_username')): ?>temporarily_switched<?php endif; ?>" id="header_usermenu_link">
            <?php if ($tbg_user->isGuest()): ?>
                <a href="javascript:void(0);" <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>onclick="TBG.Main.Login.showLogin('regular_login_container');"<?php endif; ?>><?= fa_image_tag('user'); ?></a>
            <?php else: ?>
                <a href="javascript:void(0);" class="dropper">
                    <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>
                        <?= image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true) . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']); ?>
                    <?php else: ?>
                        <?= image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true); ?>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>
                <?php if (\thebuggenie\core\framework\Event::createNew('core', 'header_usermenu_decider')->trigger()->getReturnValue() !== false): ?>
                    <?php if (!$tbg_user->isGuest()): ?>
                        <ul class="tab_menu_dropdown user_menu_dropdown popup_box" id="user_menu">
                            <li class="header userinfo">
                                <span class="user_name"><?= $tbg_user->getRealname(); ?></span>
                                <span class="user_username">@<?= $tbg_user->getUsername(); ?></span>
                            </li>
                            <li><?= link_tag(make_url('dashboard'), fa_image_tag('columns').__('Your dashboard')); ?></li>
                            <?php if ($tbg_response->getPage() == 'dashboard'): ?>
                                <li><?= javascript_link_tag(fa_image_tag('edit').__('Customize your dashboard'), array('title' => __('Customize your dashboard'), 'onclick' => "$$('.dashboard').each(function (elm) { elm.toggleClassName('editable');});")); ?></li>
                            <?php endif; ?>
                            <li><?= link_tag(make_url('account'), fa_image_tag('user-md').__('Your account')); ?></li>
                            <?php if ($tbg_request->hasCookie('original_username')): ?>
                                <li class="header"><?= __('You are temporarily this user'); ?></li>
                                <li><?= link_tag(make_url('switch_back_user'), image_tag('switchuser.png').__('Switch back to original user')); ?></li>
                            <?php endif; ?>
                            <?php \thebuggenie\core\framework\Event::createNew('core', 'user_dropdown_reg')->trigger(); ?>
                            <li class="help"><?= link_tag('https://thebuggenie.com/help/'.\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(), fa_image_tag('question-circle').__('Help for this page'), array('id' => 'global_help_link')); ?></li>
                            <li class="header"><?= __('Your issues'); ?></li>
                            <li><?= link_tag(make_url('my_reported_issues'), fa_image_tag('search') . __('Issues reported by me')); ?></li>
                            <li><?= link_tag(make_url('my_assigned_issues'), fa_image_tag('search') . __('Open issues assigned to me')); ?></li>
                            <li><?= link_tag(make_url('my_teams_assigned_issues'), fa_image_tag('search') . __('Open issues assigned to my teams')); ?></li>
                            <li class="separator"></li>
                            <li class="delete"><a href="<?= make_url('logout'); ?>"><?= fa_image_tag('sign-out-alt').__('Logout'); ?></a></li>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </li>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'after_header_userinfo')->trigger(); ?>
    </ul>
</nav>
