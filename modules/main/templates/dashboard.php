<?php 

	$bugs_response->setTitle('Dashboard');
	$bugs_response->addJavascript('dashboard.js');

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%; height: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td id="dashboard_lefthand">
			<div style="margin-top: 0px;">
			<?php
			
				BUGScontext::trigger('core', 'dashboard_left_top');
			
			?>
			</div>
			<div style="margin: 10px 0 10px 10px;">
				<?php include_component('main/myfriends'); ?>
			</div>
			<?php 
		
				BUGScontext::trigger('core', 'dashboard_left_middle');
				BUGScontext::trigger('core', 'dashboard_left_bottom');
			
			?>
		</td>
		<td valign="top" align="left" style="padding-right: 10px;">
			<?php
			
				BUGScontext::trigger('core', 'dashboard_right_top');
			
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
			
				BUGScontext::trigger('core', 'dashboard_right_middle');
				BUGScontext::trigger('core', 'dashboard_right_middle_top');
				
			?>
			<ul id="dashboard">
				<li> 
					<div class="header"><?php echo __('Open issues assigned to you'); ?></div>
					<?php if (count($bugs_user->getUserAssignedIssues()) > 0): ?>
						<table cellpadding=0 cellspacing=0>
						<?php foreach ($bugs_user->getUserAssignedIssues() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == BUGSissue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
								<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => bugs_formatTime($theIssue->getLastUpdatedTime(), 12))); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 0 0 15px 0;"><?php echo __('No issues are assigned to you'); ?></div>
					<?php endif; ?>
					<?php 
					
					BUGScontext::trigger('core', 'dashboard_main_myassignedissues');
					
					?>
				</li>
				<li>
					<div class="header"><?php echo __('Open issues assigned to your teams'); ?></div>
					<?php $team_issues_count = 0; ?>
					<?php foreach ($bugs_user->getTeams() as $tid => $theTeam): ?>
						<?php if (count($bugs_user->getUserTeamAssignedIssues($tid)) > 0): ?>
							<table cellpadding=0 cellspacing=0>
							<?php foreach ($bugs_user->getUserTeamAssignedIssues($tid) as $theIssue): ?>
								<tr class="<?php if ($theIssue->getState() == BUGSissue::STATE_CLOSED) echo 'issue_closed'; if ($theIssue->isBlocking()) echo ' issue_blocking'; ?>">
									<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
									<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
								</tr>
								<tr>
									<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
										<?php echo (int) $theIssue->isAssigned();?>
										<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => bugs_formatTime($theIssue->getLastUpdatedTime(), 12))); ?><br>
										<?php echo __('Assigned to %assignee%', array('%assignee%' => $theIssue->getAssignee()->getName())); ?>
									</td>
								</tr>
								<?php $team_issues_count++; ?>
							<?php endforeach; ?>
							</table>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ($team_issues_count == 0): ?>
						<div class="faded_medium" style="padding: 0 0 15px 0;"><?php echo __('No issues are assigned to any of your teams'); ?></div>
					<?php endif; ?>
					<?php 
					
					BUGScontext::trigger('core', 'dashboard_main_teamassignedissues');
					
					?>
				</li>
				<li style="clear: both;"> 
					<div class="header"><?php echo __('Issues with pending changes'); ?></div>
					<?php if (count($bugs_user->getIssuesPendingChanges()) > 0): ?>
						<table cellpadding=0 cellspacing=0>
						<?php foreach ($bugs_user->getIssuesPendingChanges() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == BUGSissue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
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
						<div class="faded_medium" style="padding: 0 0 15px 0;"><?php echo __('You have no issues with unsaved changes'); ?></div>
					<?php endif; ?>
					<?php 
					
					BUGScontext::trigger('core', 'dashboard_main_pendingissues');
					
					?>
				</li>
				<li> 
					<div class="header"><?php echo __('Your starred issues'); ?></div>
					<?php if (count($bugs_user->getStarredIssues()) > 0): ?>
						<table cellpadding=0 cellspacing=0>
						<?php foreach ($bugs_user->getStarredIssues() as $theIssue): ?>
							<tr class="<?php if ($theIssue->getState() == BUGSissue::STATE_CLOSED) echo 'issue_closed'; if ($theIssue->isBlocking()) echo ' issue_blocking'; ?>">
								<td class="imgtd">
									<?php echo image_tag('spinning_16.gif', array('id' => 'issue_favourite_indicator_'.$theIssue->getID(), 'style' => 'display: none;')); ?>
									<?php echo image_tag('star_faded_small.png', array('id' => 'issue_favourite_faded_'.$theIssue->getID(), 'style' => 'cursor: pointer; display: none;', 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
									<?php echo image_tag('star_small.png', array('id' => 'issue_favourite_normal_'.$theIssue->getID(), 'style' => 'cursor: pointer;', 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
								</td>
								<td><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle()); ?></td>
							</tr>
							<tr>
								<td colspan="2" class="faded_medium" style="padding-bottom: 15px;">
									<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => bugs_formatTime($theIssue->getLastUpdatedTime(), 12))); ?><br>
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
						<div class="faded_medium" style="padding: 0 0 15px 0;"><?php echo __("You haven't starred any issues yet"); ?></div>
					<?php endif; ?>
					<?php 
					
					BUGScontext::trigger('core', 'dashboard_main_mystarredissues');
					
					?>
				</li>
				<li style="clear: both;"> 
					<div class="header"><?php echo __("What you've done recently"); ?></div>
					<?php if (count($bugs_user->getLatestActions()) > 0): ?>
						<table cellpadding=0 cellspacing=0>
							<?php $prev_date = null; ?>
							<?php foreach ($bugs_user->getLatestActions() as $action): ?>
								<?php $date = bugs_formatTime($action['timestamp'], 5); ?>
								<?php if ($date != $prev_date): ?>
									<tr>
										<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
									</tr>
								<?php endif; ?>
								<?php include_template('logitem', array('action' => $action)); ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_medium" style="padding: 0 0 15px 0;"><?php echo __("You haven't starred any issues yet"); ?></div>
					<?php endif; ?>
				</li>
			</ul>
			<?php 
			
				BUGScontext::trigger('core', 'dashboard_right_middle_bottom');
				BUGScontext::trigger('core', 'dashboard_right_bottom');
			
			?>
		</td>
		<td id="dashboard_righthand">
			<div class="left_menu_header" style="margin: 5px 5px 5px 0;"><?php echo __('Upcoming milestones'); ?></div>
			<?php if (count($bugs_user->getAssociatedProjects()) > 0): ?>
				<?php $milestone_cc = 0; ?>
				<?php foreach ($bugs_user->getAssociatedProjects() as $project): ?>
					<?php foreach ($project->getUpcomingMilestones() as $milestone): ?>
						<?php if ($milestone->isVisible()): ?>
							<div class="rounded_box <?php if ($milestone->isReached()): ?>green_borderless<?php elseif ($milestone->isOverdue()): ?>red_borderless<?php else: ?>iceblue_borderless<?php endif; ?> milestone_box">
								<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
								<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
									<div class="header"><?php echo $milestone->getProject()->getName(); ?> - <?php echo $milestone->getName(); ?></div>
									<div class="date"><?php echo __('Scheduled for %scheduled_date%', array('%scheduled_date%' => bugs_formatTime($milestone->getScheduledDate(), 14))); ?></div>
									<!-- <span class="faded_medium"><?php echo $milestone->getDescription(); ?></span>  -->
									<div class="percentage">
										<div class="numbers"><?php echo __('%closed% closed of %issues% assigned', array('%closed%' => '<b>'.$project->countClosedIssuesByMilestone($milestone->getID()).'</b>', '%issues%' => '<b>'.$project->countIssuesByMilestone($milestone->getID()).'</b>')); ?></div>
										<?php include_template('main/percentbar', array('percent' => $project->getClosedPercentageByMilestone($milestone->getID()), 'height' => 14)); ?>
									</div>
									<?php if ($milestone->isReached()): ?>
										<div class="status"><?php echo __('This milestone has been reached'); ?></div>
									<?php elseif ($milestone->isOverdue()): ?>
										<div class="status"><?php echo __('This milestone is overdue!'); ?></div>
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
				<div class="faded_medium"><?php echo __('You are not associated with any projects'); ?></div>
			<?php endif; ?>
		</td>
	</tr>
</table>