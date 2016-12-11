<nav class="header_menu" id="header_userinfo">
    <?php if (!$tbg_user->isGuest()): ?>
        <div class="notifications" id="user_notifications">
            <h1>
                <?php echo __('Your notifications'); ?>
                <a href="javascript:void(0);" onclick="TBG.Main.Notifications.markAllRead();"><?php echo __('Mark all read'); ?></a>
            </h1>
            <div id="user_notifications_list_wrapper_nano" class="nano">
                <div id="user_notifications_list_wrapper" class="nano-content">
                    <ul id="user_notifications_list" data-notifications-url="<?php echo make_url('get_partial_for_backdrop', array('key' => 'notifications')); ?>" data-offset="25">
                    </ul>
                </div>
            </div>
            <?php echo image_tag('spinning_32.gif', array('id' => 'user_notifications_loading_indicator')); ?>
        </div>
    <?php endif; ?>
    <ul>
        <?php if (!$tbg_user->isGuest()): ?>
            <li class="user_notifications_container nohover" id="user_notifications_container">
                <a href="javascript:void(0);"><?= fa_image_tag('bell-o'); ?></a>
                <div id="user_notifications_count" class="notifications-indicator" data-callback-url=""><?php echo image_tag('spinning_16_white.gif'); ?></div>
            </li>
        <?php endif; ?>
        <?php if ($tbg_user->canAccessConfigurationPage()): ?>
            <li id="header_config_link" class="<?php if (in_array(\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteModule(), ['configuration', 'import'])) echo ' selected'; ?>">
                <?php echo link_tag(make_url('configure'), fa_image_tag('cog')); ?>
            </li>
        <?php endif; ?>
        <li class="with-dropdown <?php if ($tbg_request->hasCookie('tbg3_original_username')): ?>temporarily_switched<?php endif; ?>" id="header_usermenu_link">
            <?php if ($tbg_user->isGuest()): ?>
                <a href="javascript:void(0);" <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>onclick="TBG.Main.Login.showLogin('regular_login_container');"<?php endif; ?>><?php echo fa_image_tag('user'); ?></a>
            <?php else: ?>
                <?php echo link_tag(make_url('dashboard'), image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true)); ?>
                <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>
                    <?php echo javascript_link_tag(fa_image_tag('caret-down', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>
                <?php if (\thebuggenie\core\framework\Event::createNew('core', 'header_usermenu_decider')->trigger()->getReturnValue() !== false): ?>
                    <?php if (!$tbg_user->isGuest()): ?>
                        <ul class="tab_menu_dropdown user_menu_dropdown" id="user_menu">
                            <li><?php echo link_tag(make_url('dashboard'), fa_image_tag('columns').__('Your dashboard')); ?></li>
                            <?php if ($tbg_response->getPage() == 'dashboard'): ?>
                                <li><?php echo javascript_link_tag(fa_image_tag('pencil-square-o').__('Customize your dashboard'), array('title' => __('Customize your dashboard'), 'onclick' => "$$('.dashboard').each(function (elm) { elm.toggleClassName('editable');});")); ?></li>
                            <?php endif; ?>
                            <li><?php echo link_tag(make_url('account'), fa_image_tag('user-md').__('Your account')); ?></li>
                            <?php if ($tbg_request->hasCookie('tbg3_original_username')): ?>
                                <li class="header"><?php echo __('You are temporarily this user'); ?></li>
                                <li><?php echo link_tag(make_url('switch_back_user'), image_tag('switchuser.png').__('Switch back to original user')); ?></li>
                            <?php endif; ?>
                            <?php \thebuggenie\core\framework\Event::createNew('core', 'user_dropdown_reg')->trigger(); ?>
                            <li><?php echo link_tag('http://www.thebuggenie.com/help/'.\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(), fa_image_tag('question-circle').__('Help for this page'), array('id' => 'global_help_link')); ?></li>
                            <li><a href="<?php echo make_url('logout'); ?>" onclick="<?php if (\thebuggenie\core\framework\Settings::isPersonaAvailable()): ?>if (navigator.id) { navigator.id.logout();return false; }<?php endif; ?>"><?php echo fa_image_tag('sign-out').__('Logout'); ?></a></li>
                            <li class="header"><?php echo __('Your issues'); ?></li>
                            <li><?php echo link_tag(make_url('my_reported_issues'), fa_image_tag('search') . __('Issues reported by me')); ?></li>
                            <li><?php echo link_tag(make_url('my_assigned_issues'), fa_image_tag('search') . __('Open issues assigned to me')); ?></li>
                            <li><?php echo link_tag(make_url('my_teams_assigned_issues'), fa_image_tag('search') . __('Open issues assigned to my teams')); ?></li>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </li>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'after_header_userinfo')->trigger(); ?>
    </ul>
</nav>
