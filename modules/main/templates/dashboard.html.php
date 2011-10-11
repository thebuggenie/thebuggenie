<?php 

	$tbg_response->setTitle(__('Dashboard'));
	$tbg_response->addBreadcrumb(__('Personal dashboard'), make_url('dashboard'), tbg_get_breadcrumblinks('main_links'));
	$tbg_response->addFeed(make_url('my_reported_issues', array('format' => 'rss')), __('Issues reported by me'));
	$tbg_response->addFeed(make_url('my_assigned_issues', array('format' => 'rss')), __('Open issues assigned to you'));
	$tbg_response->addFeed(make_url('my_teams_assigned_issues', array('format' => 'rss')), __('Open issues assigned to your teams'));

?>
<?php include_component('main/hideableInfoBox', array('key' => 'dashboard_didyouknow', 'title' => __('This is your personal dashboard'), 'content' => __('This is your personal dashboard page - your starting point when logging in to The Bug Genie. This dashboard page will show projects and people you are associated with, as well as interesting views.') . '<br>' . __('Your dashboard can be configured and personalized. To configure what views to show on this dashboard, click the "Customize dashboard"-icon to the far right, below this box.') . '<br><br><i>' . __('Your dashboard page is accessible from anywhere - click your username in the top right header area at any time to access your dashboard.') . '</i>')); ?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="dashboard_lefthand" class="side_bar">
			<?php TBGEvent::createNew('core', 'dashboard_left_top')->trigger(); ?>
			<div class="container_div" style="margin: 5px 0 5px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php TBGEvent::createNew('core', 'dashboard_left_bottom')->trigger();?>
		</td>
		<td class="main_area">
			<?php TBGEvent::createNew('core', 'dashboard_main_top')->trigger(); ?>
			<?php if (empty($dashboardViews)) :?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views. To add views in this dashboard, press the 'Customize dashboard'-icon to the far right."); ?></p>
			<?php else: ?>
				<ul id="dashboard">
					<?php $clearleft = true; ?>
					<?php foreach($dashboardViews as $view): ?>
					<li style="clear: <?php echo ($clearleft) ? 'left' : 'right'; ?>;">
						<?php include_component('dashboardview', array('type' => $view->get(TBGDashboardViewsTable::TYPE), 'id' => $view->get(TBGDashboardViewsTable::ID), 'view' => $view->get(TBGDashboardViewsTable::VIEW), 'rss' => true)); ?>
					</li>
					<?php $clearleft = !$clearleft; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
		</td>
		<td id="dashboard_righthand" class="side_bar">
			<div class="container_div" style="margin-right: 10px;">
				<div class="header" style="margin: 2px 0 5px 0; padding: 3px 3px 3px 5px;"><?php echo __('Your projects'); ?></div>
				<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
					<ul id="associated_projects">
						<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
							<?php if ($project->isDeleted()): continue; endif; ?>
							<li style="text-align: right;">
								<div class="rounded_box white cut_bottom" style="border-bottom: 0;">
									<div class="project_name">
										<?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?>
									</div>
								</div>
								<div class="rounded_box lightgrey cut_top" style="border-top: 0;">
									<div style="float: left; font-weight: bold;"><?php echo __('Go to'); ?>:</div>
									<?php echo link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), __('Issues')); ?>
									|
									<?php if ($project->usesScrum()): ?>
										<?php echo link_tag(make_url('project_scrum', array('project_key' => $project->getKey())), __('Scrum')); ?>
										|
									<?php endif; ?>
									<?php echo link_tag(make_url('project_roadmap', array('project_key' => $project->getKey())), __('Roadmap')); ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="header" style="margin: 5px 5px 5px 0;"><?php echo __('Upcoming milestones / sprints'); ?></div>
					<div class="faded_out" style="font-size: 11px;"><?php echo __('Showing milestones and sprint for the next 21 days'); ?></div>
					<?php $milestone_cc = 0; ?>
					<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
						<?php foreach ($project->getUpcomingMilestonesAndSprints() as $milestone): ?>
							<?php if ($milestone->isScheduled()): ?>
								<?php include_template('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
								<?php $milestone_cc++; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
					<?php if ($milestone_cc == 0): ?>
						<div class="faded_out"><?php echo __('There are no upcoming milestones for any of your associated projects'); ?></div>
					<?php endif; ?>
				<?php else: ?>
					<div class="faded_out" style="font-size: 0.9em; padding: 5px 5px 10px 5px;"><?php echo __('You are not associated with any projects'); ?></div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>