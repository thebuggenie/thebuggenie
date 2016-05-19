<header>
    <div id="logo_container">
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_before_logo')->trigger(); ?>
        <span class="mobile_menuanchor" onclick="$('body').toggleClassName('mobile_leftmenu_visible');">&nbsp;</span>
        <?php $link = (\thebuggenie\core\framework\Settings::getHeaderLink() == '') ? \thebuggenie\core\framework\Context::getWebroot() : \thebuggenie\core\framework\Settings::getHeaderLink(); ?>
        <a class="logo" href="<?php print $link; ?>"><?php echo image_tag(\thebuggenie\core\framework\Settings::getHeaderIconUrl(), array('style' => 'max-height: 24px;'), \thebuggenie\core\framework\Settings::isUsingCustomHeaderIcon()); ?></a>
        <div class="logo_name"><?php echo \thebuggenie\core\framework\Settings::getSiteHeaderName(); ?></div>
    </div>
    <?php if (!\thebuggenie\core\framework\Settings::isMaintenanceModeEnabled()): ?>
        <div id="topmenu-container">
            <?php if (\thebuggenie\core\framework\Event::createNew('core', 'header_mainmenu_decider')->trigger()->getReturnValue() !== false): ?>
                <?php require THEBUGGENIE_CORE_PATH . 'templates/headermainmenu.inc.php'; ?>
            <?php endif; ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/headerusermenu.inc.php'; ?>
        </div>
        <?php if (\thebuggenie\core\framework\Event::createNew('core', 'header_mainmenu_decider')->trigger()->getReturnValue() !== false): ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/submenu.inc.php'; ?>
        <?php endif; ?>
        <div id="mobile_menu" class="mobile_menu left">
        </div>
        <div id="mobile_menu_aborter" class="mobile_menu_aborter" onclick="$('body').toggleClassName('mobile_leftmenu_visible');"></div>
        <div id="mobile_usermenu" class="mobile_menu right">
        </div>
        <div id="mobile_usermenu_aborter" class="mobile_menu_aborter" onclick="$('body').toggleClassName('mobile_rightmenu_visible');"></div>
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

                    if ($('header_avatar')) {
                        $('header_avatar').observe('click', function(e) { $('body').toggleClassName('mobile_rightmenu_visible');e.preventDefault(); });
                    }
                    Event.observe(window, 'resize', TBG.Core._mobileMenuMover);
                    TBG.Core._mobileMenuMover();
                });
            });
        </script>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_menu_end')->trigger(); ?>
    <?php endif; ?>
</header>
