<?php 

	$tbg_response->addBreadcrumb(__('Clients'));
	if ($client instanceof TBGClient)
	{
		$tbg_response->setTitle(__('Client dashboard for %client_name%', array('%client_name%' => $client->getName())));
		$tbg_response->setPage('client');
		$tbg_response->addBreadcrumb(link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), $client->getName()));
	}
	else
	{
		$tbg_response->setTitle(__('Client dashboard'));
		$tbg_response->addBreadcrumb(__('Client dashboard'));
	}
	
?>

<?php if ($client instanceof TBGClient): ?>
	<div class="client_dashboard">
		<div class="dashboard_client_info">
			<span class="dashboard_client_header"><?php echo $client->getName(); ?></span>
			<table>
				<tr>
					<td style="padding-right: 10px">
						<b><?php echo __('Website:'); ?></b> <?php if ($client->getWebsite() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="<?php echo $client->getWebsite(); ?>" target="_blank"><?php echo $client->getWebsite(); ?></a><?php endif; ?>
					</td>
					<td style="padding: 0px 10px">
						<b><?php echo __('Email address:'); ?></b> <?php if ($client->getEmail() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="mailto:<?php echo $client->getEmail(); ?>" target="_blank"><?php echo $client->getEmail(); ?></a><?php endif; ?>
					</td>
					<td style="padding: 0px 10px">
						<b><?php echo __('Telephone:'); ?></b> <?php if ($client->getTelephone() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getTelephone(); endif; ?>
					</td>
					<td style="padding: 0px 10px">
						<b><?php echo __('Fax:'); ?></b> <?php if ($client->getFax() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getFax(); endif; ?>
					</td>
				</tr>
			</table>
		</div>

		<table class="client_dashboard_table">
			<tr>
				<td class="client_dashboard_projects padded">
					<div class="header">
						<?php echo __('Projects for %client%', array('%client%' => $client->getName())); ?>
					</div>
		
					<?php if (count(TBGProject::getAllByClientID($client->getID())) > 0): ?>
						<ul class="project_list simple_list">
						<?php foreach (TBGProject::getAllByClientID($client->getID()) as $aProject): ?>
							<li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
						<?php endforeach; ?>
						</ul>
						<div class="header" style="margin: 5px 5px 5px 0;"><?php echo __('Milestones / sprints'); ?></div>
						<?php $milestone_cc = 0; ?>
						<?php foreach (TBGProject::getAllByClientID($client->getID()) as $project): ?>
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
							<div class="faded_out"><?php echo __('There are no upcoming milestones for any of this client\'s associated projects'); ?></div>
						<?php endif; ?>
					<?php else: ?>
						<p class="content faded_out"><?php echo __('There are no projects assigned to this client'); ?>.</p>
					<?php endif; ?>
				</td>
			<td class="client_dashboard_users padded">
				<div class="header">
					<?php echo __('Members of %client%', array('%client%' => $client->getName())); ?>
				</div>
				<?php if ($client->getNumberOfMembers() > 0): ?>
					<ul class="client_users">
					<?php foreach ($client->getMembers() as $user): ?>
						<li><?php echo include_component('main/userdropdown', array('user' => $user)); ?></li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p class="content faded_out"><?php echo __('This client has no members'); ?>.</p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</div>
<?php else: ?>
<div class="rounded_box red borderless issue_info aligned">
	<?php echo __('This client does not exist'); ?>
</div>
<?php endif; ?>