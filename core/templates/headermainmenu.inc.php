<nav class="tab_menu header_menu<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>" id="main_menu">
	<ul>
		<?php if (!TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
			<li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>><div class="menuitem_container"><?php echo link_tag(make_url('home'), image_tag('tab_index.png').__('Frontpage')); ?></div></li>
		<?php elseif (TBGContext::isProjectContext()): ?>
			<li<?php if (in_array($tbg_response->getPage(), array('project_dashboard', 'project_planning', 'project_scrum', 'project_scrum_sprint_details', 'project_timeline', 'project_team', 'project_roadmap', 'project_statistics', 'vcs_commitspage'))): ?> class="selected"<?php endif; ?>>
				<div class="menuitem_container">
					<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_dashboard_small.png').__('Summary')); ?>
					<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
				</div>
				<div id="project_information_menu" class="tab_menu_dropdown">
					<?php include_template('project/projectinfolinks', array('submenu' => true)); ?>
				</div>
			</li>
		<?php endif; ?>
		<?php if (!$tbg_user->isThisGuest() && !TBGSettings::isSingleProjectTracker() && !TBGContext::isProjectContext()): ?>
			<li<?php if ($tbg_response->getPage() == 'dashboard'): ?> class="selected"<?php endif; ?>><div class="menuitem_container"><?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png').__('Dashboard')); ?></div></li>
		<?php endif; ?>
		<?php if (TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived() && !TBGContext::getCurrentProject()->isLocked() && ($tbg_user->canReportIssues() || $tbg_user->canReportIssues(TBGContext::getCurrentProject()->getID()))): ?>
			<li<?php if ($tbg_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>>
				<div class="menuitem_container">
					<?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('tab_reportissue.png') . __('Report an issue')); ?>
					<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
				</div>
				<div id="project_issue_menu" class="tab_menu_dropdown">
					<?php foreach (TBGContext::getCurrentProject()->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
						<?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'issuetype' => $issuetype->getKey())), image_tag($issuetype->getIcon() . '_tiny.png' ) . __($issuetype->getName())); ?>
					<?php endforeach;?>
				</div>
			</li>
		<?php endif; ?>
		<?php if (TBGContext::isProjectContext() && $tbg_user->canSearchForIssues()): ?>
			<li<?php if (in_array($tbg_response->getPage(), array('project_issues', 'viewissue'))): ?> class="selected"<?php endif; ?>>
				<div class="menuitem_container">
					<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('tab_search.png').__('Issues')); ?>
					<?php if (TBGContext::isProjectContext()): ?>
						<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
					<?php endif; ?>
				</div>
				<?php if (TBGContext::isProjectContext()): ?>
					<div id="issues_menu" class="tab_menu_dropdown">
						<?php echo link_tag(make_url('project_open_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Open issues for this project')); ?>
						<?php echo link_tag(make_url('project_closed_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Closed issues for this project')); ?>
						<?php echo link_tag(make_url('project_wishlist_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Wishlist for this project')); ?>
						<?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Milestone todo-list for this project')); ?>
						<?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Most voted for issues')); ?>
						<?php echo link_tag(make_url('project_month_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('icon_savedsearch.png') . __('Issues reported this month')); ?>
						<?php echo link_tag(make_url('project_last_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'units' => 30, 'time_unit' => 'days')), image_tag('icon_savedsearch.png') . __('Issues reported last 30 days')); ?>
					</div>
				<?php endif; ?>
			</li>
		<?php endif; ?>
		<?php if (!TBGContext::isProjectContext() && ($tbg_user->hasPageAccess('teamlist') || count($tbg_user->getTeams())) && !is_null(TBGTeamsTable::getTable()->getAll())): ?>
			<li<?php if ($tbg_response->getPage() == 'team'): ?> class="selected"<?php endif; ?>>
				<div class="menuitem_container">
					<?php echo link_tag('javascript:void(0)', image_tag('tab_teams.png') . __('Teams'), array('class' => 'not_clickable')); ?>
					<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
				</div>
				<div id="team_menu" class="tab_menu_dropdown">
					<?php foreach (TBGTeam::getAll() as $team): ?>
						<?php if (!$team->hasAccess()) continue; ?>
						<?php echo link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), image_tag('tab_teams.png' ) . $team->getName()); ?>
					<?php endforeach;?>
				</div>
			</li>
		<?php endif; ?>
		<?php if (!TBGContext::isProjectContext() && $tbg_user->hasPageAccess('clientlist') && count($tbg_user->getClients()) && !is_null(TBGClient::getAll())): ?>
			<li<?php if ($tbg_response->getPage() == 'client'): ?> class="selected"<?php endif; ?>>
				<div class="menuitem_container">
					<?php echo link_tag('javascript:void(0)', image_tag('tab_clients.png') . __('Clients'), array('class' => 'not_clickable')); ?>
					<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
				</div>
				<div id="client_menu" class="tab_menu_dropdown">
					<?php foreach (TBGClient::getAll() as $client): ?>
						<?php if (!$client->hasAccess()) continue; ?>
						<?php echo link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), image_tag('tab_clients.png' ) . $client->getName()); ?>
					<?php endforeach;?>
				</div>
			</li>
		<?php endif; ?>
		<?php TBGEvent::createNew('core', 'menustrip_item_links', null, array('selected_tab' => $tbg_response->getPage()))->trigger(); ?>
	</ul>
	<?php TBGEvent::createNew('core', 'before_header_userinfo')->trigger(); ?>
</nav>
