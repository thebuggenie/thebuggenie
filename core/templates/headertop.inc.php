<header>
    <div id="logo_container">
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_before_logo')->trigger(); ?>
        <span id="mobile_menuanchor" class="mobile_menuanchor" onclick="$('body').toggleClassName('mobile_leftmenu_visible');"><?= fa_image_tag('bars'); ?></span>
        <?php $link = (\thebuggenie\core\framework\Settings::getHeaderLink() == '') ? \thebuggenie\core\framework\Context::getWebroot() : \thebuggenie\core\framework\Settings::getHeaderLink(); ?>
        <a class="logo" href="<?php print $link; ?>"><?php echo image_tag(\thebuggenie\core\framework\Settings::getHeaderIconUrl(), [], \thebuggenie\core\framework\Settings::isUsingCustomHeaderIcon()); ?></a>
        <?php if (\thebuggenie\core\framework\Settings::getSiteHeaderName() != '' || \thebuggenie\core\framework\Context::isProjectContext()): ?>
            <div id="logo_name" class="logo_name"><?php echo \thebuggenie\core\framework\Settings::getSiteHeaderName(); ?></div>
            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                <a id="logo_project_name" href="<?= make_url('project_dashboard', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())); ?>" class="logo_name"><?php echo \thebuggenie\core\framework\Context::getCurrentProject()->getName(); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (!\thebuggenie\core\framework\Settings::isMaintenanceModeEnabled()): ?>
        <div id="topmenu-container">
            <?php require THEBUGGENIE_CORE_PATH . 'templates/headermainmenu.inc.php'; ?>
            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                <?php require THEBUGGENIE_CORE_PATH . 'templates/headermainmenuprojectcontext.inc.php'; ?>
            <?php endif; ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/headerusermenu.inc.php'; ?>
            <?php if ($tbg_user->canSearchForIssues()): ?>
                <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('search', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" name="quicksearchform" id="quicksearchform">
                    <input type="hidden" name="fs[text][o]" value="=">
                    <i class="fa fa-circle-o-notch fa-spin fa-fw" id="quicksearch_indicator" style="display: none;"></i>
                    <input type="search" name="fs[text][v]" accesskey="f" id="searchfor" placeholder="<?php echo __('Search'); ?>"><div id="searchfor_autocomplete_choices" class="autocomplete rounded_box"></div>
                    <button type="submit" id="quicksearch_submit"><?= fa_image_tag('search'); ?></button>
                </form>
            <?php endif; ?>
        </div>
        <div id="mobile_menu" class="mobile_menu left">
            <div id="header_banner">
                <span id="header_mobile_menuanchor" class="mobile_menuanchor" onclick="$('body').toggleClassName('mobile_leftmenu_visible');"><?= fa_image_tag('bars'); ?></span>
                <?php if ($tbg_user->isGuest()): ?>
                    <a id="header_banner_user_link" href="javascript:void(0);" <?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page'): ?>onclick="TBG.Main.Login.showLogin('regular_login_container');"<?php endif; ?>><?php echo image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]', 'class' => 'guest_avatar header_avatar'), true) . __('You are not logged in') . fa_image_tag('caret-down') . fa_image_tag('caret-up'); ?></a>
                <?php else: ?>
                    <a id="header_banner_user_link" href="javascript:void(0);" onclick="$('header_banner').toggleClassName('selected');"><?php echo image_tag($tbg_user->getAvatarURL(false), array('alt' => '[avatar]', 'class' => 'header_avatar'), true) . '<span class="header_user_fullname">' . tbg_decodeUTF8($tbg_user->getDisplayName()) . '<br>' . $tbg_user->getEmail() . fa_image_tag('caret-down') . fa_image_tag('caret-up') . '</span>'; ?></a>
                <?php endif; ?>
            </div>
        </div>
        <div id="mobile_menu_aborter" class="mobile_menu_aborter" onclick="$('body').toggleClassName('mobile_leftmenu_visible');"></div>
        <script type="text/javascript">
            var TBG, jQuery;
            require(['domReady', 'thebuggenie/tbg', 'jquery', 'jquery.nanoscroller'], function (domReady, tbgjs, jquery, nanoscroller) {
                domReady(function () {
                    TBG = tbgjs;
                    jQuery = jquery;

                    var mm = $('main_menu');
                    if (mm.hasClassName('project_context')) {
                        mm.select('div.menuitem_container').each(function(elm) {
                            elm.observe('click', function(e) { elm.toggleClassName('selected');e.preventDefault(); });
                        });
                    }

                    Event.observe(window, 'resize', TBG.Core._mobileMenuMover);
                    TBG.Core._mobileMenuMover();
                });
            });
        </script>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_menu_end')->trigger(); ?>
    <?php endif; ?>
</header>
