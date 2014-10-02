<header>
    <div id="logo_container">
        <?php TBGEvent::createNew('core', 'header_before_logo')->trigger(); ?>
        <?php $link = (TBGSettings::getHeaderLink() == '') ? TBGContext::getTBGPath() : TBGSettings::getHeaderLink(); ?>
        <a class="logo" href="<?php print $link; ?>"><?php echo image_tag(TBGSettings::getHeaderIconUrl(), array('style' => 'max-height: 24px;'), TBGSettings::isUsingCustomHeaderIcon()); ?></a>
        <div class="logo_name"><?php echo TBGSettings::getTBGname(); ?></div>
    </div>
    <?php if (!TBGSettings::isMaintenanceModeEnabled()): ?>
        <div id="topmenu-container">
            <?php if (TBGEvent::createNew('core', 'header_mainmenu_decider')->trigger()->getReturnValue() !== false): ?>
                <?php require THEBUGGENIE_CORE_PATH . 'templates/headermainmenu.inc.php'; ?>
            <?php endif; ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/headerusermenu.inc.php'; ?>
        </div>
        <?php if (TBGEvent::createNew('core', 'header_mainmenu_decider')->trigger()->getReturnValue() !== false): ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/submenu.inc.php'; ?>
        <?php endif; ?>
        <?php TBGEvent::createNew('core', 'header_menu_end')->trigger(); ?>
    <?php endif; ?>
</header>
