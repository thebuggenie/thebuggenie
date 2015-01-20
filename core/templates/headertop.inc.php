<header>
    <div id="logo_container">
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_before_logo')->trigger(); ?>
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
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_menu_end')->trigger(); ?>
    <?php endif; ?>
</header>
