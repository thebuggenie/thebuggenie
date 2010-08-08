<?php 

	$tbg_response->setTitle('Dashboard');
	$tbg_response->addJavascript('dashboard.js');
	$tbg_response->addFeed(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true, 'format' => 'rss')), __('Issues reported by me'));
	$tbg_response->addFeed(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to you'));
	$tbg_response->addFeed(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to your teams'));

?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="dashboard_lefthand">
			<div style="margin-top: 0px;">
			<?php
			
				TBGEvent::createNew('core', 'dashboard_left_top')->trigger();
			
			?>
			</div>
			<div style="margin: 10px 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php 
		
				TBGEvent::createNew('core', 'dashboard_left_middle')->trigger();
				TBGEvent::createNew('core', 'dashboard_left_bottom')->trigger();
			
			?>
		</td>
		<td valign="top" align="left" style="padding-right: 10px;">
			<?php
			
				TBGEvent::createNew('core', 'dashboard_right_top')->trigger();
			
			/*?>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 48px; text-align: center; padding: 0 10px 0 10px;">
						<?php echo image_tag($tbg_user->getAvatarURL(false), array(), true); ?>
					</td>
					<td>
						<div style="font-size: 15px;"><?php echo '<b>' . __('Welcome, %username%', array('%username%' => '</b>' . $tbg_user->getRealname())); ?></div>
						<span><?php echo '<b>' . __('This page was loaded at %time%', array('%time%' => '</b>' . tbg_formatTime($_SERVER['REQUEST_TIME'], 13))); ?></span>
					</td>
				</tr>
			</table>
			<?php*/
			
				TBGEvent::createNew('core', 'dashboard_right_middle')->trigger();
				TBGEvent::createNew('core', 'dashboard_right_middle_top')->trigger();
				
			?>
			<ul id="dashboard">
				<li>
					<div class="rounded_box mediumgrey borderless cut_bottom" style="margin-top: 5px; font-weight: bold; font-size: 13px;">
						<?php echo link_tag(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
						<?php echo __('Open issues assigned to you'); ?>
					</div>
					<?php if (count($tbg_user->getUserAssignedIssues()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
						<?php foreach ($tbg_user->getUserAssignedIssues() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
								<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedTitle(true)); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => tbg_formatTime($theIssue->getLastUpdatedTime(), 12))); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __('No issues are assigned to you'); ?></div>
					<?php endif; ?>
					<?php 
					
					TBGEvent::createNew('core', 'dashboard_main_myassignedissues')->trigger();
					
					?>
				</li>
				<li>
					<div class="rounded_box mediumgrey borderless cut_bottom" style="margin-top: 5px; font-weight: bold; font-size: 13px;">
						<?php echo link_tag(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
						<?php echo __('Open issues assigned to your teams'); ?>
					</div>
					<?php $team_issues_count = 0; ?>
					<?php foreach ($tbg_user->getTeams() as $tid => $theTeam): ?>
						<?php if (count($tbg_user->getUserTeamAssignedIssues($tid)) > 0): ?>
							<table cellpadding=0 cellspacing=0 style="margin: 5px;">
							<?php foreach ($tbg_user->getUserTeamAssignedIssues($tid) as $theIssue): ?>
								<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED) echo 'issue_closed'; if ($theIssue->isBlocking()) echo ' issue_blocking'; ?>">
									<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
									<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedTitle(true)); ?></td>
								</tr>
								<tr>
									<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
										<?php echo (int) $theIssue->isAssigned();?>
										<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => tbg_formatTime($theIssue->getLastUpdatedTime(), 12))); ?><br>
										<?php echo __('Assigned to %assignee%', array('%assignee%' => $theIssue->getAssignee()->getName())); ?>
									</td>
								</tr>
								<?php $team_issues_count++; ?>
							<?php endforeach; ?>
							</table>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ($team_issues_count == 0): ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __('No issues are assigned to any of your teams'); ?></div>
					<?php endif; ?>
					<?php 
					
					TBGEvent::createNew('core', 'dashboard_main_teamassignedissues')->trigger();
					
					?>
				</li>
				<li style="clear: both;"> 
					<div class="rounded_box mediumgrey borderless cut_bottom" style="margin-top: 5px; font-weight: bold; font-size: 13px;">
						<?php echo __('Issues with pending changes'); ?>
					</div>
					<?php if (count($tbg_user->getIssuesPendingChanges()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
						<?php foreach ($tbg_user->getIssuesPendingChanges() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
								<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedTitle(true)); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('This issue has %number_of% unsaved change(s)', array('%number_of%' => '<strong>' . $theIssue->getNumberOfUnsavedChanges() . '</strong>')); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __('You have no issues with unsaved changes'); ?></div>
					<?php endif; ?>
					<?php 
					
					TBGEvent::createNew('core', 'dashboard_main_pendingissues')->trigger();
					
					?>
				</li>
				<li> 
					<div class="rounded_box mediumgrey borderless cut_bottom" style="margin-top: 5px; font-weight: bold; font-size: 13px;">
						<?php echo __('Your starred issues'); ?>
					</div>
					<?php if (count($tbg_user->getStarredIssues()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
						<?php foreach ($tbg_user->getStarredIssues() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED) echo 'issue_closed'; if ($theIssue->isBlocking()) echo ' issue_blocking'; ?>">
								<td class="imgtd">
									<?php echo image_tag('spinning_16.gif', array('id' => 'issue_favourite_indicator_'.$theIssue->getID(), 'style' => 'display: none;')); ?>
									<?php echo image_tag('star_faded_small.png', array('id' => 'issue_favourite_faded_'.$theIssue->getID(), 'style' => 'cursor: pointer; display: none;', 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
									<?php echo image_tag('star_small.png', array('id' => 'issue_favourite_normal_'.$theIssue->getID(), 'style' => 'cursor: pointer;', 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
								</td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedTitle(true)); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => tbg_formatTime($theIssue->getLastUpdatedTime(), 12))); ?><br>
									<?php if ($theIssue->isAssigned()): ?>
										<?php echo __('Assigned to %assignee%', array('%assignee%' => $theIssue->getAssignee()->getName())); ?>
									<?php else: ?>
										<?php echo __('Not assigned to anyone yet'); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __("You haven't starred any issues yet"); ?></div>
					<?php endif; ?>
					<?php 
					
					TBGEvent::createNew('core', 'dashboard_main_mystarredissues')->trigger();
					
					?>
				</li>
				<li style="clear: both;"> 
					<div class="rounded_box mediumgrey borderless cut_bottom" style="margin-top: 5px; font-weight: bold; font-size: 13px;">
						<?php echo __("What you've done recently"); ?>
					</div>
					<?php if (count($tbg_user->getLatestActions()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
							<?php $prev_date = null; ?>
							<?php foreach ($tbg_user->getLatestActions() as $action): ?>
								<?php $date = tbg_formatTime($action['timestamp'], 5); ?>
								<?php if ($date != $prev_date): ?>
									<tr>
										<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
									</tr>
								<?php endif; ?>
								<?php include_component('main/logitem', array('log_action' => $action, 'include_project' => true)); ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __("You haven't done anything recently"); ?></div>
					<?php endif; ?>
				</li>
			</ul>
			<?php 
			
				TBGEvent::createNew('core', 'dashboard_right_middle_bottom')->trigger();
				TBGEvent::createNew('core', 'dashboard_right_bottom')->trigger();
			
			?>
		</td>
		<td id="dashboard_righthand">
			<div class="left_menu_header" style="margin: 7px 5px 5px 0;"><?php echo __('Your projects'); ?></div>
			<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
				<ul id="associated_projects">
					<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
						<li style="text-align: right;">
							<div class="rounded_box white cut_bottom" style="border-bottom: 0;">
								<div class="project_name">
									<?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?>
								</div>
							</div>
							<div class="rounded_box lightgrey cut_top" style="border-top: 0;">
								<div style="float: left; font-weight: bold;"><?php echo __('Go to'); ?>:</div>
								<?php echo link_tag(make_url('project_planning', array('project_key' => $project->getKey())), __('Planning')); ?>
								<?php if ($project->usesScrum()): ?>
									|
									<?php echo link_tag(make_url('project_scrum', array('project_key' => $project->getKey())), __('Scrum')); ?>
								<?php endif; ?>
								|
								<?php echo link_tag(make_url('project_issues', array('project_key' => $project->getKey())), __('Issues')); ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="left_menu_header" style="margin: 5px 5px 5px 0;"><?php echo __('Milestones / sprints'); ?></div>
				<?php $milestone_cc = 0; ?>
				<?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
					<?php foreach ($project->getUpcomingMilestonesAndSprints() as $milestone): ?>
						<?php if ($milestone->isVisible() && $milestone->isScheduled()): ?>
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
									<!-- <span class="faded_medium"><?php echo $milestone->getDescription(); ?></span>  -->
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
					<div class="faded_medium"><?php echo __('There are no upcoming milestones for any of your associated projects'); ?></div>
				<?php endif; ?>
			<?php else: ?>
				<div class="faded_medium" style="padding: 0 0 0 5px;"><?php echo __('You are not associated with any projects'); ?></div>
			<?php endif; ?>
		</td>
	</tr>
</table>