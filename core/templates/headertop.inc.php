<header>
<?php // SHOW/HIDE LOGO AND TBGNAME ?>
	<?php if (TBGSettings::getThemeName() != 'nitrogen'): ?>
	<div id="logo_container">
		<?php $link = (TBGSettings::getHeaderLink() == '') ? TBGContext::getTBGPath() : TBGSettings::getHeaderLink(); ?>
		<a class="logo" href="<?php print $link; ?>"><?php echo image_tag(TBGSettings::getHeaderIconUrl(), array('style' => 'width: 24px; height: 24px;'), TBGSettings::isUsingCustomHeaderIcon()); ?></a>
		<div class="logo_name"><?php echo TBGSettings::getTBGname(); ?></div>
	</div> 
	<?php endif; ?>


<?php // BEGIN: SHOW/HIDE FOUR BUTTONS TO FRONT OF TOP MENU FOR Forum, GitHub, Overview (A WIKI PAGE), AND PERMANENT PROJECT PAGE (BUG GENIE FRONTPAGE) ?>
	<?php if (TBGSettings::getThemeName() == 'nitrogen'): ?>
        <div><nav class="tab_menu header_menu" id="main_menu">

<?php // Code in your forum and GitHub url's ?>      
            <li><div><a class= "tab_menu header_menu" id="main_menu" href="http://YOURSITE.COM/YOURforum/"> Forum</a></li>
            <li><div><a class= "tab_menu header_menu" id="main_menu" href="https://github.com/YOURorganization/"> GitHub</a></li>
                    
            <li <?php if ($tbg_response->getTitle() == 'Overview'): ?> class="selected"><?php endif; ?>
                <?php if ($tbg_response->getTitle() != 'Overview'): ?> class="logo_name"><?php endif; ?>
                <div><a href="<?php echo TBGContext::getTBGPath(); ?>wiki/Overview">Overview</a> </div></li>
            <li <?php if ($tbg_response->getPage() == 'home'): ?> class="selected"><?php endif; ?>
                <?php if ($tbg_response->getPage() != 'home'): ?> class="logo_name"><?php endif; ?>
                <div><a href="<?php echo TBGContext::getTBGPath(); ?>">Project Manager</a> </div></li>
        </div>	
        <?php endif; ?>
<?php // END: SHOW/HIDE FOUR BUTTONS ... ?>


	<?php if (!TBGSettings::isMaintenanceModeEnabled()): ?>
		<nav class="tab_menu header_menu<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>" id="main_menu">
			<ul>
				<?php if (!TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>


<?php // SHOW/HIDE FRONT PAGE BUTTON/TAB ?>
					<?php if (TBGSettings::getThemeName() != 'nitrogen'): ?><li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>><div><?php echo link_tag(make_url('home'), image_tag('tab_index.png').__('Frontpage')); ?></div></li><?php endif; ?>
				<?php elseif (TBGContext::isProjectContext()): ?>
					<li<?php if (in_array($tbg_response->getPage(), array('project_dashboard', 'project_planning', 'project_scrum', 'project_scrum_sprint_details', 'project_timeline', 'project_team', 'project_roadmap', 'project_statistics', 'vcs_commitspage'))): ?> class="selected"<?php endif; ?>>
						<div>


<?php // SHOW/HIDE ICONS  (Not sure if the best method for doing this is one icon a time or all at once.  So, this is the only icon that can be toggled.  Until I learn more, the rest of them are just hidden all the time.) ?>
							<?php if (TBGSettings::getThemeName() != 'nitrogen'): ?>							<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_dashboard_small.png').__('Summary')); ?>
							<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
							<?php else: ?>
							<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Summary')); ?>
							<?php endif; ?>
						</div>
						<div id="project_information_menu" class="tab_menu_dropdown">
							<?php include_template('project/projectinfolinks', array('submenu' => true)); ?>
						</div>
					</li>
				<?php endif; ?>


<?php // CHANGE STRING DASHBOARD TO MY DASHBOARD SO USER CAN SEE DIFFERENCE BETWEEN THEIR DASHBOARD BUTTON AND PROJECT DASHBOARD BUTTON ON BUG GENIE FRONTPAGE ?>
				<?php if (!$tbg_user->isThisGuest() && !TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
					<li<?php if ($tbg_response->getPage() == 'dashboard'): ?> class="selected"<?php endif; ?>><div><?php echo link_tag(make_url('dashboard'), __('My Dashboard')); ?></div></li>
				<?php endif; ?>
				
				
			<?php if (TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived() && !TBGContext::getCurrentProject()->isLocked() && ($tbg_user->canReportIssues() || $tbg_user->canReportIssues(TBGContext::getCurrentProject()->getID()))): ?>
					<li<?php if ($tbg_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>>
						<div>
							<?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey())),  __('Report an issue')); ?>
							
							</div>
							<div id="project_issue_menu" class="tab_menu_dropdown">
							<?php foreach (TBGContext::getCurrentProject()->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
								<?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'issuetype' => $issuetype->getKey())), __($issuetype->getName())); ?>
							<?php endforeach;?>
						</div>
					</li>
				<?php endif; ?>
				<?php if (TBGContext::isProjectContext() && $tbg_user->canSearchForIssues()): ?>
					<li<?php if (in_array($tbg_response->getPage(), array('project_issues', 'viewissue'))): ?> class="selected"<?php endif; ?>>
						<div>
							<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Issues')); ?>
							<?php if (TBGContext::isProjectContext()): ?>
								
							<?php endif; ?>
						</div>
						<?php if (TBGContext::isProjectContext()): ?>
							<div id="issues_menu" class="tab_menu_dropdown">
								<?php echo link_tag(make_url('project_open_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Open issues for this project')); ?>
								<?php echo link_tag(make_url('project_closed_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Closed issues for this project')); ?>
								<?php echo link_tag(make_url('project_wishlist_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Wishlist for this project')); ?>
								<?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Milestone todo-list for this project')); ?>
								<?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Most voted for issues')); ?>
								<?php echo link_tag(make_url('project_month_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Issues reported this month')); ?>
								<?php echo link_tag(make_url('project_last_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'units' => 30, 'time_unit' => 'days')), __('Issues reported last 30 days')); ?>
							</div>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if (!TBGContext::isProjectContext() && ($tbg_user->hasPageAccess('teamlist') || count($tbg_user->getTeams())) && !is_null(TBGTeamsTable::getTable()->getAll())): ?>
					<li<?php if ($tbg_response->getPage() == 'team'): ?> class="selected"<?php endif; ?>>
						<div>
							<?php echo link_tag('javascript:void(0)', __('Teams'), array('class' => 'not_clickable')); ?>
							
						</div>
						<div id="team_menu" class="tab_menu_dropdown">
							<?php foreach (TBGTeam::getAll() as $team): ?>
								<?php if (!$team->hasAccess()) continue; ?>
								<?php echo link_tag(make_url('team_dashboard', array('team_id' => $team->getID())),  $team->getName()); ?>
							<?php endforeach;?>
						</div>
					</li>
				<?php endif; ?>
				<?php if (!TBGContext::isProjectContext() && $tbg_user->hasPageAccess('clientlist') && count($tbg_user->getClients()) && !is_null(TBGClient::getAll())): ?>
					<li<?php if ($tbg_response->getPage() == 'client'): ?> class="selected"<?php endif; ?>>
						<div>
							<?php echo link_tag('javascript:void(0)',  __('Clients'), array('class' => 'not_clickable')); ?>
							
						</div>
						<div id="client_menu" class="tab_menu_dropdown">
							<?php foreach (TBGClient::getAll() as $client): ?>
								<?php if (!$client->hasAccess()) continue; ?>
								<?php echo link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), $client->getName()); ?>
							<?php endforeach;?>
						</div>
					</li>
				<?php endif; ?>
				<?php TBGEvent::createNew('core', 'menustrip_item_links', null, array('selected_tab' => $tbg_response->getPage()))->trigger(); ?>
			</ul>
			<?php TBGEvent::createNew('core', 'before_header_userinfo')->trigger(); ?>
		</nav>
		<nav class="tab_menu header_menu" id="header_userinfo">
			<ul>
				<li<?php if ($tbg_request->hasCookie('tbg3_original_username')): ?> class="temporarily_switched"<?php endif; ?>>
					<div>
						<?php if ($tbg_user->isGuest()): ?>
							<a href="javascript:void(0);" <?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>onclick="$('login_backdrop').show();TBG.Main.Helpers.tabSwitcher('tab_login', 'login_menu');$('tbg3_username').focus();"<?php endif; ?>><?php echo  __('You are not logged in'); ?></a>
						<?php else: ?>
							<?php echo link_tag(make_url('dashboard'),  tbg_decodeUTF8($tbg_user->getDisplayName())); ?>
						<?php endif; ?>
						<?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>
							
						<?php endif; ?>
					</div>
					<?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>
						<div class="tab_menu_dropdown user_menu_dropdown">
							<?php if ($tbg_user->isGuest()): ?>
								<a href="javascript:void(0);" onclick="$('login_backdrop').show();TBG.Main.Helpers.tabSwitcher('tab_login', 'login_menu');$('tbg3_username').focus();"><?php echo __('Login'); ?></a>
								<?php if (TBGSettings::isRegistrationAllowed()): ?>
									<a href="javascript:void(0);" onclick="$('login_backdrop').show();TBG.Main.Helpers.tabSwitcher('tab_register', 'login_menu');$('fieldusername').focus();"><?php echo __('Register'); ?></a>
								<?php endif; ?>
								<?php TBGEvent::createNew('core', 'user_dropdown_anon')->trigger(); ?>
							<?php else: ?>
								<div class="header" style="margin-bottom: 5px;">
									<a href="javascript:void(0);" onclick="$('usermenu_changestate').toggle();" class="button button-lightblue" style="float: right; margin-left: 5px; padding: 1px 3px !important; font-size: 1em !important; border: 1px solid rgba(0, 0, 0, 0.2) !important;"><?php echo __('Change'); ?></a>
									<?php echo image_tag('spinning_16.gif', array('style' => 'float: right; display: none; margin: -2px 5px 2px;', 'id' => 'change_userstate_dropdown')); ?>
									<?php echo __('You are: %userstate%', array('%userstate%' => '<span class="current_userstate userstate">'.__($tbg_user->getState()->getName()).'</span>')); ?>
								</div>
								<div id="usermenu_changestate" style="clear: both; margin: 5px 10px 10px 10px; display: none;" onclick="$('usermenu_changestate').toggle();">
									<?php foreach (TBGUserstate::getAll() as $state): ?>
										<?php if ($state->getID() == TBGSettings::getOfflineState()->getID()) continue; ?>
										<a href="javascript:void(0);" onclick="TBG.Main.Profile.setState('<?php echo make_url('set_state', array('state_id' => $state->getID())); ?>', 'change_userstate_dropdown');"><?php echo __($state->getName()); ?></a>
									<?php endforeach; ?>
								</div>
								<?php echo link_tag(make_url('dashboard'), __('Your dashboard')); ?>
								<?php if ($tbg_response->getPage() == 'dashboard'): ?>
									<?php echo javascript_link_tag(__('Customize your dashboard'), array('title' => __('Customize your dashboard'), 'onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'dashboard_config', 'tid' => TBGContext::getUser()->getID(), 'target_type' => TBGDashboardViewsTable::TYPE_USER))."')")); ?>
								<?php endif; ?>
								<?php echo link_tag(make_url('account'), __('Your account')); ?>
								<?php if ($tbg_request->hasCookie('tbg3_original_username')): ?>
								<div class="header"><?php echo __('You are temporarily this user'); ?></div>
								<?php echo link_tag(make_url('switch_back_user'), __('Switch back to original user')); ?>
								<?php endif; ?>
								<?php if ($tbg_user->canAccessConfigurationPage()): ?>
									<?php echo link_tag(make_url('configure'), __('Configure The Bug Genie')); ?>
								<?php endif; ?>
								<?php TBGEvent::createNew('core', 'user_dropdown_reg')->trigger(); ?>
								<?php echo link_tag('http://www.thebuggenie.com/help/'.TBGContext::getRouting()->getCurrentRouteName(), __('Help for this page')); ?>
								<a href="<?php echo make_url('logout'); ?>" onclick="<?php if (TBGSettings::isPersonaAvailable()): ?>navigator.id.logout();return false;<?php endif; ?>"><?php echo __('Logout'); ?></a>
								<div class="header"><?php echo __('Your issues'); ?></div>
								<?php echo link_tag(make_url('my_reported_issues'),  __('Issues reported by me')); ?>
								<?php echo link_tag(make_url('my_assigned_issues'),  __('Open issues assigned to me')); ?>
								<?php echo link_tag(make_url('my_teams_assigned_issues'),  __('Open issues assigned to my teams')); ?>
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
				</li>
				<?php TBGEvent::createNew('core', 'after_header_userinfo')->trigger(); ?>
			</ul>
		</nav>
	<?php endif; ?>
</header>