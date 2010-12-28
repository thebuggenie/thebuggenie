<?php 

	$tbg_response->addBreadcrumb(__('Teams'));
	if ($team instanceof TBGTeam)
	{
		$tbg_response->setTitle(__('Team dashboard for %team_name%', array('%team_name%' => $team->getName())));
		$tbg_response->setPage('team');
		$tbg_response->addBreadcrumb(link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), __($team->getName())));
	}
	else
	{
		$tbg_response->setTitle(__('Team dashboard'));
		$tbg_response->addBreadcrumb(__('Team dashboard'));
	}
	
?>

<div class="team_dashboard">
	<div class="dashboard_team_info">
		<span class="dashboard_team_header"><?php echo $team->getName(); ?></span><br />
	</div>
	
	<table class="team_dashboard_table">
		<tr>
			<td class="team_dashboard_projects padded">
				<div class="header">
					<?php echo __('Projects for %team%', array('%team%' => __($team->getName()))); ?>
				</div>
				<?php if (count($projects) > 0): ?>
					<ul class="project_list simple_list">
					<?php foreach ($projects as $aProject): ?>
						<li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
					<?php endforeach; ?>
					</ul>
					<div class="header" style="margin: 5px 5px 5px 0;"><?php echo __('Milestones / sprints'); ?></div>
					<?php $milestone_cc = 0; ?>
					<?php foreach ($projects as $project): ?>
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
						<div class="faded_out"><?php echo __('There are no upcoming milestones for any of this team\'s associated projects'); ?></div>
					<?php endif; ?>
				<?php else: ?>
					<p class="content faded_out"><?php echo __('There are no projects linked to this team'); ?>.</p>
				<?php endif; ?>
			</td>
			<td class="team_dashboard_users padded">
				<div class="header">
					<?php echo __('Members of %team%', array('%team%' => __($team->getName()))); ?>
				</div>
				<?php if (count($users) > 0): ?>
					<ul class="team_users">
					<?php foreach ($users as $user): ?>
						<li><?php echo include_component('main/userdropdown', array('user' => $user)); ?></li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p class="content faded_out"><?php echo __('This team has no members'); ?>.</p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</div>