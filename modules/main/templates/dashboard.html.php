<?php 

	$tbg_response->setTitle(__('Dashboard'));
	$tbg_response->addBreadcrumb(link_tag(make_url('dashboard'), __('Personal dashboard')));
	$tbg_response->addJavascript('dashboard.js');
	$tbg_response->addFeed(make_url('my_reported_issues', array('format' => 'rss')), __('Issues reported by me'));
	$tbg_response->addFeed(make_url('my_assigned_issues', array('format' => 'rss')), __('Open issues assigned to you'));
	$tbg_response->addFeed(make_url('my_teams_assigned_issues', array('format' => 'rss')), __('Open issues assigned to your teams'));

?>
<?php include_component('main/hideableInfoBox', array('key' => 'dashboard_didyouknow', 'title' => __('This is your personal dashboard'), 'content' => __('This is your personal dashboard page - your starting point when logging in to The Bug Genie. This dashboard page will show projects and people you are associated with, as well as interesting views.') . '<br>' . __('Your dashboard can be configured and personalized. To configure what views to show on this dashboard, click the "Customize dashboard"-icon to the far right, below this box.') . '<br><br><i>' . __('Your dashboard page is accessible from anywhere - click your username in the top right header area at any time to access your dashboard.') . '</i>')); ?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="dashboard_lefthand" class="side_bar">
			<?php TBGEvent::createNew('core', 'dashboard_left_top')->trigger(); ?>
			<div style="margin: 0 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php TBGEvent::createNew('core', 'dashboard_left_bottom')->trigger();?>
		</td>
		<td class="main_area">
			<?php TBGEvent::createNew('core', 'dashboard_main_top')->trigger(); ?>
			<?php if (empty($dashboardViews)) :?>
				<p class="content faded_out"><?php echo __('This dashboard doesn\'t contain any view. To add views in this dashboard, press the "Customize dashboard"-icon to the far right.'); ?>.</p>
			<?php else: ?>
				<ul id="dashboard">
					<?php $clearleft = true; ?>
					<?php foreach($dashboardViews as $view): ?>
					<li style="clear: <?php echo ($clearleft) ? 'left' : 'right'; ?>;">
						<?php include_component('dashboardview', array('type' => $view->get(TBGUserDashboardViewsTable::TYPE), 'id' => $view->get(TBGUserDashboardViewsTable::ID), 'view' => $view->get(TBGUserDashboardViewsTable::VIEW), 'rss' => true)); ?>
					</li>
					<?php $clearleft = !$clearleft; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
		</td>
		<td id="dashboard_righthand" class="side_bar">
			<?php echo javascript_link_tag(image_tag('icon_dashboard_config.png').'<span>'.__('Customize your dashboard').'</span>', array('title' => __('Customize your dashboard'), 'id' => 'customize_dashboard_icon', 'class' => 'image', 'onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'dashboard_config'))."');")); ?>
			<div class="header" style="margin: 0 5px 5px 0;"><?php echo __('Your projects'); ?></div>
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
								<?php echo link_tag(make_url('project_issues', array('project_key' => $project->getKey())), __('Issues')); ?>
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
				<div class="header" style="margin: 5px 5px 5px 0;"><?php echo __('Milestones / sprints'); ?></div>
				<?php $milestone_cc = 0; ?>
				<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
					<?php foreach ($project->getUpcomingMilestonesAndSprints() as $milestone): ?>
						<?php if ($milestone->isScheduled()): ?>
							<div class="rounded_box <?php if ($milestone->isReached()): ?>green borderless<?php elseif ($milestone->isOverdue()): ?>red borderless<?php else: ?>iceblue borderless<?php endif; ?> milestone_box">
								<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
								<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
									<div class="header"><?php echo link_tag(make_url('project_roadmap', array('project_key' => $milestone->getProject()->getKey())).'#roadmap_milestone_'.$milestone->getID(), $milestone->getProject()->getName() . ' - ' . $milestone->getName()); ?></div>
									<div class="date">
										<?php if ($milestone->getStartingDate()): ?>
											<?php echo tbg_formatTime($milestone->getStartingDate(), 20) . ' - ' . tbg_formatTime($milestone->getScheduledDate(), 20); ?>
										<?php else: ?>
											<?php echo __('Scheduled for %scheduled_date%', array('%scheduled_date%' => tbg_formatTime($milestone->getScheduledDate(), 20))); ?>
										<?php endif; ?>
									</div>
									<!-- <span class="faded_out"><?php echo $milestone->getDescription(); ?></span>  -->
									<div class="percentage">
										<div class="numbers">
											<?php if ($milestone->isSprint()): ?>
												<?php if ($milestone->countClosedIssues() == 1): ?>
													<?php echo __('%num_closed% story (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
												<?php else: ?>
													<?php echo __('%num_closed% stories (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
												<?php endif; ?>
											<?php else: ?>
												<?php echo __('%num_closed% issue(s) closed of %num_assigned% assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>')); ?>
											<?php endif; ?>
										</div>
										<?php include_template('main/percentbar', array('percent' => $milestone->getPercentComplete(), 'height' => 14)); ?>
									</div>
									<?php if ($milestone->isReached()): ?>
										<div class="status">
											<?php if ($milestone->getType() == TBGMilestone::TYPE_REGULAR): ?>
												<?php echo __('This milestone has been reached'); ?>
											<?php else: ?>
												<?php echo __('This sprint is completed'); ?>
											<?php endif; ?>
										</div>
									<?php elseif ($milestone->isOverdue()): ?>
										<div class="status">
											<?php if ($milestone->getType() == TBGMilestone::TYPE_REGULAR): ?>
												<?php echo __('This milestone is overdue'); ?>
											<?php else: ?>
												<?php echo __('This sprint is overdue'); ?>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
								<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
							</div>
							<?php $milestone_cc++; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
				<?php if ($milestone_cc == 0): ?>
					<div class="faded_out"><?php echo __('There are no upcoming milestones for any of your associated projects'); ?></div>
				<?php endif; ?>
			<?php else: ?>
				<div class="faded_out" style="padding: 0 0 0 5px;"><?php echo __('You are not associated with any projects'); ?></div>
			<?php endif; ?>
		</td>
	</tr>
</table>
<script type="text/javascript">
	Event.observe(window, 'resize', dashboardResize);
	document.observe('dom:loaded', dashboardResize);
</script>