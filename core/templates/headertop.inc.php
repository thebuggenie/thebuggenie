<header>
	<div id="logo_container">
		<?php TBGEvent::createNew('core', 'header_before_logo')->trigger(); ?>
		<?php $link = (TBGSettings::getHeaderLink() == '') ? TBGContext::getTBGPath() : TBGSettings::getHeaderLink(); ?>
		<a class="logo" href="<?php print $link; ?>"><?php echo image_tag(TBGSettings::getHeaderIconUrl(), array('style' => 'max-height: 24px;'), TBGSettings::isUsingCustomHeaderIcon()); ?></a>
		<div class="logo_name"><?php echo TBGSettings::getTBGname(); ?></div>
	</div>
	<?php if (!TBGSettings::isMaintenanceModeEnabled()): ?>
		<?php if (TBGEvent::createNew('core', 'header_mainmenu_decider')->trigger()->getReturnValue() !== false): ?>
			<?php require THEBUGGENIE_CORE_PATH . 'templates/headermainmenu.inc.php'; ?>
		<?php endif; ?>
		<nav class="tab_menu header_menu" id="header_userinfo">
			<div class="notifications" id="user_notifications">
				<h1><?php echo __('Your notifications'); ?></h1>
				<?php echo image_tag('spinning_32.gif', array('id' => 'user_notifications_loading_indicator')); ?>
				<div id="user_notifications_list" data-notifications-url="<?php echo make_url('get_partial_for_backdrop', array('key' => 'notifications')); ?>"></div>
			</div>
			<ul>
				<?php if (!$tbg_user->isGuest()): ?>
					<li class="user_notifications_container nohover" onclick="TBG.Main.Profile.toggleNotifications();" id="user_notifications_container">
						<div id="user_notifications_count" data-callback-url=""><?php echo image_tag('spinning_16.gif'); ?></div>
						<?php echo image_tag('emblem-generic.png'); ?>
					</li>
				<?php endif; ?>
				<li<?php if ($tbg_request->hasCookie('tbg3_original_username')): ?> class="temporarily_switched"<?php endif; ?>>
					<div id="header_userinfo_details">
						<?php if ($tbg_user->isGuest()): ?>
							<a href="javascript:void(0);" <?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>onclick="TBG.Main.Login.showLogin('regular_login_container');"<?php endif; ?>><?php echo image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]', 'class' => 'guest_avatar'), true) . __('You are not logged in'); ?></a>
						<?php else: ?>
							<?php echo link_tag(make_url('dashboard'), image_tag($tbg_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true) . '<span id="header_user_fullname">'.tbg_decodeUTF8($tbg_user->getDisplayName()).'</span>'); ?>
						<?php endif; ?>
						<?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>
							<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
						<?php endif; ?>
					</div>
					<?php if (TBGEvent::createNew('core', 'header_usermenu_decider')->trigger()->getReturnValue() !== false): ?>
						<?php require THEBUGGENIE_CORE_PATH . 'templates/headerusermenu.inc.php'; ?>
					<?php endif; ?>
				</li>
				<?php TBGEvent::createNew('core', 'after_header_userinfo')->trigger(); ?>
			</ul>
		</nav>
		<?php if (TBGEvent::createNew('core', 'header_mainmenu_decider')->trigger()->getReturnValue() !== false): ?>
			<?php require THEBUGGENIE_CORE_PATH . 'templates/submenu.inc.php'; ?>
		<?php endif; ?>
		<?php TBGEvent::createNew('core', 'header_menu_end')->trigger(); ?>
	<?php endif; ?>
</header>
