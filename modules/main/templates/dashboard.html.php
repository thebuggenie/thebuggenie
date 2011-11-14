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
			<div class="container_div" style="margin: 0 0 5px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php TBGEvent::createNew('core', 'dashboard_left_bottom')->trigger(); ?>
		</td>
		<td class="main_area" style="padding-right: 5px; padding-top: 0;">
			<?php TBGEvent::createNew('core', 'dashboard_main_top')->trigger(); ?>
			<?php if (!count($views)):?>
				<div style="text-align: center; padding: 40px;">
					<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
					<br>
					<form action="<?php echo make_url('dashboard'); ?>" method="post">
						<input type="hidden" name="setup_default_dashboard" value="1">
						<input type="submit" value="<?php echo __('Setup my dashboard'); ?>" class="button button-green" style="font-size: 1.1em; padding: 5px !important;">
					</form>
				</div>
			<?php else: ?>
				<ul id="dashboard" class="column-4s" style="margin: 10px 5px;">
					<?php foreach($views as $_id => $view): ?>
						<li style="clear: none;" id="dashboard_container_<?php echo $_id; ?>">
							<?php include_component('dashboardview', array('view' => $view, 'show' => false)); ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<script type="text/javascript">
					TBG.Main.Dashboard.url = '<?php echo make_url('dashboard_view'); ?>';
				</script>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
		</td>
		<td id="dashboard_righthand" class="side_bar">
			<div class="container_div" style="margin-right: 10px; margin-top: 0;">
				<div class="header" style="padding-left: 5px"><?php echo __('Your projects'); ?></div>
				<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
					<ul id="associated_projects">
						<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
							<?php if ($project->isDeleted()): continue; endif; ?>
							<li style="text-align: right;">
								<div style="padding: 5px;">
									<div class="project_name">
										<?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?>
									</div>
									<div style="float: left; font-weight: bold;"><?php echo __('Go to'); ?>:</div>
									<?php echo link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), __('Issues')); ?>
									|
									<?php if ($project->usesScrum()): ?>
										<?php echo link_tag(make_url('project_planning', array('project_key' => $project->getKey())), __('Planning')); ?>
										|
									<?php endif; ?>
									<?php echo link_tag(make_url('project_roadmap', array('project_key' => $project->getKey())), __('Roadmap')); ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<div class="faded_out" style="font-size: 0.9em; padding: 5px 5px 10px 5px;"><?php echo __('You are not associated with any projects'); ?></div>
				<?php endif; ?>
			</div>
			<div class="container_div" style="margin-right: 10px; margin-top: 0;">
				<div class="header" style="padding-left: 5px"><?php echo __('Upcoming milestones / sprints'); ?></div>
				<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
					<div class="faded_out" style="padding: 5px;"><?php echo __('Showing milestones and sprint for the next 21 days'); ?></div>
					<?php $milestone_cc = 0; ?>
					<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
						<?php foreach ($project->getUpcomingMilestones() as $milestone): ?>
							<?php if ($milestone->isScheduled()): ?>
								<?php include_template('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
								<?php $milestone_cc++; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
					<?php if ($milestone_cc == 0): ?>
						<div class="faded_out" style="padding: 5px;"><?php echo __('There are no upcoming milestones for any of your associated projects'); ?></div>
					<?php endif; ?>
				<?php else: ?>
					<div class="faded_out" style="font-size: 0.9em; padding: 5px 5px 10px 5px;"><?php echo __('You are not associated with any projects'); ?></div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>