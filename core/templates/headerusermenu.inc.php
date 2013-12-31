<?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>
	<div class="tab_menu_dropdown user_menu_dropdown" id="user_menu">
		<?php if ($tbg_user->isGuest()): ?>
			<a href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('regular_login_container');"><?php echo image_tag('icon_login.png').__('Login'); ?></a>
			<?php if (TBGSettings::isRegistrationAllowed()): ?>
				<a href="javascript:void(0);" onclick="TBG.Main.Login.showLogin('register');"><?php echo image_tag('icon_register.png').__('Register'); ?></a>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'user_dropdown_anon')->trigger(); ?>
		<?php else: ?>
			<div class="header" style="margin-bottom: 5px;">
				<a href="javascript:void(0);" onclick="$('usermenu_changestate').toggle();" id="usermenu_changestate_toggler" class="button button-lightblue"><?php echo __('Change'); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'change_userstate_dropdown')); ?>
				<?php echo __('You are: %userstate', array('%userstate' => '<span class="current_userstate userstate">'.__($tbg_user->getState()->getName()).'</span>')); ?>
			</div>
			<div id="usermenu_changestate" style="display: none;" onclick="$('usermenu_changestate').toggle();">
				<?php foreach (TBGUserstate::getAll() as $state): ?>
					<?php if ($state->getID() == TBGSettings::getOfflineState()->getID()) continue; ?>
					<a href="javascript:void(0);" onclick="TBG.Main.Profile.setState('<?php echo make_url('set_state', array('state_id' => $state->getID())); ?>', 'change_userstate_dropdown');"><?php echo __($state->getName()); ?></a>
				<?php endforeach; ?>
			</div>
			<?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png').__('Your dashboard')); ?>
			<?php if ($tbg_response->getPage() == 'dashboard'): ?>
				<?php echo javascript_link_tag(image_tag('icon_dashboard_config.png').__('Customize your dashboard'), array('title' => __('Customize your dashboard'), 'onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'dashboard_config', 'tid' => TBGContext::getUser()->getID(), 'target_type' => TBGDashboardViewsTable::TYPE_USER))."')")); ?>
			<?php endif; ?>
			<?php echo link_tag(make_url('account'), image_tag('icon_account.png').__('Your account')); ?>
			<?php if ($tbg_request->hasCookie('tbg3_original_username')): ?>
				<div class="header"><?php echo __('You are temporarily this user'); ?></div>
				<?php echo link_tag(make_url('switch_back_user'), image_tag('switchuser.png').__('Switch back to original user')); ?>
			<?php endif; ?>
			<?php if ($tbg_user->canAccessConfigurationPage()): ?>
				<?php echo link_tag(make_url('configure'), image_tag('tab_config.png').__('Configure %thebuggenie_name', array('%thebuggenie_name' => TBGSettings::getTBGname()))); ?>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'user_dropdown_reg')->trigger(); ?>
			<?php echo link_tag('http://www.thebuggenie.com/help/'.TBGContext::getRouting()->getCurrentRouteName(), image_tag('help.png').__('Help for this page'), array('id' => 'global_help_link')); ?>
			<a href="<?php echo make_url('logout'); ?>" onclick="<?php if (TBGSettings::isPersonaAvailable()): ?>if (navigator.id) { navigator.id.logout();return false; }<?php endif; ?>"><?php echo image_tag('logout.png').__('Logout'); ?></a>
			<div class="header"><?php echo __('Your issues'); ?></div>
			<?php echo link_tag(make_url('my_reported_issues'), image_tag('icon_savedsearch.png') . __('Issues reported by me')); ?>
			<?php echo link_tag(make_url('my_assigned_issues'), image_tag('icon_savedsearch.png') . __('Open issues assigned to me')); ?>
			<?php echo link_tag(make_url('my_teams_assigned_issues'), image_tag('icon_savedsearch.png') . __('Open issues assigned to my teams')); ?>
			<?php foreach ($tbg_user->getStarredIssues() as $issue): ?>
				<?php if (!TBGContext::isProjectContext() || $issue->getProject()->getID() != TBGContext::getCurrentProject()->getID()) continue; ?>
				<?php

				$link_text = image_tag('star_small.png');
				if ($issue->isBlocking()) $link_text .= image_tag('icon_important.png', array('style' => 'margin-right: 5px;', 'title' => __('This issue is blocking the next release')));
				$link_text .= $issue->getIssueType()->getName() . ' ' . $issue->getFormattedIssueNo(true) . '<br>';
				$link_text .= (mb_strlen($issue->getTitle()) > 43) ? mb_substr($issue->getTitle(), 0, 40) . '...' : $issue->getTitle();
				$classes = ($issue->isClosed()) ? 'issue_closed' : '';

				?>
				<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $link_text, array('class' => $classes)); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
<?php endif; ?>
