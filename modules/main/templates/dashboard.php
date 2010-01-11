<?php 

	$bugs_response->setTitle('Dashboard');
	$bugs_response->addJavascript('dashboard.js');
	$bugs_response->addFeed(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true, 'format' => 'rss')), __('Issues reported by me'));
	$bugs_response->addFeed(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to you'));
	$bugs_response->addFeed(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to your teams'));

?>
<table style="margin: 0 0 20px 0; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="dashboard_lefthand">
			<div style="margin-top: 0px;">
			<?php
			
				TBGContext::trigger('core', 'dashboard_left_top');
			
			?>
			</div>
			<div style="margin: 10px 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php 
		
				TBGContext::trigger('core', 'dashboard_left_middle');
				TBGContext::trigger('core', 'dashboard_left_bottom');
			
			?>
		</td>
		<td valign="top" align="left" style="padding-right: 10px;">
			<?php
			
				TBGContext::trigger('core', 'dashboard_right_top');
			
			/*?>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 48px; text-align: center; padding: 0 10px 0 10px;">
						<?php echo image_tag($bugs_user->getAvatarURL(false), array(), true); ?>
					</td>
					<td>
						<div style="font-size: 15px;"><?php echo '<b>' . __('Welcome, %username%', array('%username%' => '</b>' . $bugs_user->getRealname())); ?></div>
						<span><?php echo '<b>' . __('This page was loaded at %time%', array('%time%' => '</b>' . bugs_formatTime($_SERVER['REQUEST_TIME'], 13))); ?></span>
					</td>
				</tr>
			</table>
			<?php*/
			
				TBGContext::trigger('core', 'dashboard_right_middle');
				TBGContext::trigger('core', 'dashboard_right_middle_top');
				
			?>
			<ul id="dashboard">
				<li>
					<div class="rounded_box borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px; font-weight: bold; font-size: 13px;">
							<?php echo link_tag(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
							<?php echo __('Open issues assigned to you'); ?>
						</div>
					</div>
					<?php if (count($bugs_user->getUserAssignedIssues()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
						<?php foreach ($bugs_user->getUserAssignedIssues() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
								<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => bugs_formatTime($theIssue->getLastUpdatedTime(), 12))); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __('No issues are assigned to you'); ?></div>
					<?php endif; ?>
					<?php 
					
					TBGContext::trigger('core', 'dashboard_main_myassignedissues');
					
					?>
				</li>
				<li>
					<div class="rounded_box borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px; font-weight: bold; font-size: 13px;">
							<?php echo link_tag(make_url('search', array('predefined_search' => TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
							<?php echo __('Open issues assigned to your teams'); ?>
						</div>
					</div>
					<?php $team_issues_count = 0; ?>
					<?php foreach ($bugs_user->getTeams() as $tid => $theTeam): ?>
						<?php if (count($bugs_user->getUserTeamAssignedIssues($tid)) > 0): ?>
							<table cellpadding=0 cellspacing=0 style="margin: 5px;">
							<?php foreach ($bugs_user->getUserTeamAssignedIssues($tid) as $theIssue): ?>
								<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED) echo 'issue_closed'; if ($theIssue->isBlocking()) echo ' issue_blocking'; ?>">
									<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
									<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
								</tr>
								<tr>
									<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
										<?php echo (int) $theIssue->isAssigned();?>
										<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => bugs_formatTime($theIssue->getLastUpdatedTime(), 12))); ?><br>
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
					
					TBGContext::trigger('core', 'dashboard_main_teamassignedissues');
					
					?>
				</li>
				<li style="clear: both;"> 
					<div class="rounded_box borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px; font-weight: bold; font-size: 13px;">
							<?php echo __('Issues with pending changes'); ?>
						</div>
					</div>
					<?php if (count($bugs_user->getIssuesPendingChanges()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
						<?php foreach ($bugs_user->getIssuesPendingChanges() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
								<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
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
					
					TBGContext::trigger('core', 'dashboard_main_pendingissues');
					
					?>
				</li>
				<li> 
					<div class="rounded_box borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px; font-weight: bold; font-size: 13px;">
							<?php echo __('Your starred issues'); ?>
						</div>
					</div>
					<?php if (count($bugs_user->getStarredIssues()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
						<?php foreach ($bugs_user->getStarredIssues() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED) echo 'issue_closed'; if ($theIssue->isBlocking()) echo ' issue_blocking'; ?>">
								<td class="imgtd">
									<?php echo image_tag('spinning_16.gif', array('id' => 'issue_favourite_indicator_'.$theIssue->getID(), 'style' => 'display: none;')); ?>
									<?php echo image_tag('star_faded_small.png', array('id' => 'issue_favourite_faded_'.$theIssue->getID(), 'style' => 'cursor: pointer; display: none;', 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
									<?php echo image_tag('star_small.png', array('id' => 'issue_favourite_normal_'.$theIssue->getID(), 'style' => 'cursor: pointer;', 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
								</td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => bugs_formatTime($theIssue->getLastUpdatedTime(), 12))); ?><br>
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
					
					TBGContext::trigger('core', 'dashboard_main_mystarredissues');
					
					?>
				</li>
				<li style="clear: both;"> 
					<div class="rounded_box borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px; font-weight: bold; font-size: 13px;">
							<?php echo __("What you've done recently"); ?>
						</div>
					</div>
					<?php if (count($bugs_user->getLatestActions()) > 0): ?>
						<table cellpadding=0 cellspacing=0 style="margin: 5px;">
							<?php $prev_date = null; ?>
							<?php foreach ($bugs_user->getLatestActions() as $action): ?>
								<?php $date = bugs_formatTime($action['timestamp'], 5); ?>
								<?php if ($date != $prev_date): ?>
									<tr>
										<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
									</tr>
								<?php endif; ?>
								<?php include_template('logitem', array('action' => $action, 'include_project' => true)); ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 5px 5px 15px 5px;"><?php echo __("You haven't done anything recently"); ?></div>
					<?php endif; ?>
				</li>
			</ul>
			<?php 
			
				TBGContext::trigger('core', 'dashboard_right_middle_bottom');
				TBGContext::trigger('core', 'dashboard_right_bottom');
			
			?>
		</td>
		<td id="dashboard_righthand">
			<div class="left_menu_header" style="margin: 7px 5px 5px 0;"><?php echo __('Your projects'); ?></div>
			<?php if (count($bugs_user->getAssociatedProjects()) > 0): ?>
				<ul id="associated_projects">
					<?php foreach ($bugs_user->getAssociatedProjects() as $project): ?>
						<li style="text-align: right;">
							<div class="rounded_box white">
								<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
								<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
									<div class="project_name">
										<?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?>
									</div>
								</div>
							</div>
							<div class="rounded_box lightgrey">
								<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
									<div style="float: left; font-weight: bold;"><?php echo __('Go to'); ?>:</div>
									<?php echo link_tag(make_url('project_planning', array('project_key' => $project->getKey())), __('Planning')); ?>
									|
									<?php echo link_tag(make_url('project_scrum', array('project_key' => $project->getKey())), __('Scrum')); ?>
									|
									<?php echo link_tag(make_url('project_issues', array('project_key' => $project->getKey())), __('Issues')); ?>
								</div>
								<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="left_menu_header" style="margin: 5px 5px 5px 0;"><?php echo __('Milestones / sprints'); ?></div>
				<?php $milestone_cc = 0; ?>
				<?php foreach ($bugs_user->getAssociatedProjects() as $project): ?>
					<?php foreach ($project->getUpcomingMilestonesAndSprints() as $milestone): ?>
						<?php if ($milestone->isVisible() && $milestone->isScheduled()): ?>
							<div class="rounded_box <?php if ($milestone->isReached()): ?>green_borderless<?php elseif ($milestone->isOverdue()): ?>red_borderless<?php else: ?>iceblue_borderless<?php endif; ?> milestone_box">
								<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
								<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
									<div class="header"><?php echo $milestone->getProject()->getName(); ?> - <?php echo $milestone->getName(); ?></div>
									<div class="date">
										<?php if ($milestone->getStartingDate()): ?>
											<?php echo bugs_formatTime($milestone->getStartingDate(), 20) . ' - ' . bugs_formatTime($milestone->getScheduledDate(), 20); ?>
										<?php else: ?>
											<?php echo __('Scheduled for %scheduled_date%', array('%scheduled_date%' => bugs_formatTime($milestone->getScheduledDate(), 20))); ?>
										<?php endif; ?>
									</div>
									<!-- <span class="faded_medium"><?php echo $milestone->getDescription(); ?></span>  -->
									<div class="percentage">
										<div class="numbers">
											<?php if ($milestone->getType() == TBGMilestone::TYPE_REGULAR): ?>
												<?php echo __('%closed% closed of %issues% assigned', array('%closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%issues%' => '<b>'.$milestone->countOpenIssues().'</b>')); ?>
											<?php else: ?>
												<?php echo __('%points_spent% pts spent of %points_estimated% pts estimated', array('%points_spent%' => '<b>'.$milestone->getPointsSpent().'</b>', '%points_estimated%' => '<b>'.$milestone->getPointsEstimated().'</b>')); ?>
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