<?php

	$bugs_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="project_dashboard">
	<tr>
		<td style="width: 365px; padding: 0 5px 0 5px;">
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<?php if (count($recent_activities) > 0): ?>
						<table cellpadding=0 cellspacing=0 class="recent_activities">
							<?php $prev_date = null; ?>
							<?php foreach ($recent_activities as $timestamp => $activities): ?>
								<?php $date = bugs_formatTime($timestamp, 5); ?>
									<?php if ($date != $prev_date): ?>
									<tr>
										<td class="latest_action_dates" colspan="2"><?php echo bugs_formatTime($timestamp, 5); ?></td>
									</tr>
								<?php endif; ?>
								<?php foreach ($activities as $activity): ?>
									<?php if ($activity['change_type'] == 'build_release'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_build.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo bugs_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('New version released'); ?></i></td>
										</tr>
									<?php else: ?>
										<?php include_template('main/logitem', array('action' => $activity, 'include_time' => true, 'extra_padding' => true)); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<div class="timeline_link"><?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('view_timeline.png', array('style' => 'float: right; margin-left: 5px;')) . __('Show complete timeline')); ?></div>
					<?php else: ?>
						<div class="faded_dark" style="font-size: 13px; padding-top: 3px;"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
					<?php endif; ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</td>
		<td style="width: auto; padding: 5px;">
			<div style="clear: both; padding: 0 5px 5px 0;" id="project_header_container">
				<?php echo image_tag($selected_project->getIcon()); ?>
				<div id="project_name"><?php echo $selected_project->getName(); ?> (<?php echo $selected_project->getKey(); ?>)</div>
				<div id="project_description"<?php if (!$selected_project->hasDescription()): ?> class="faded_dark"<?php endif; ?>>
					<?php if ($selected_project->hasDescription()): ?>
						<?php echo tbg_parse_text($selected_project->getDescription()); ?>
					<?php else: ?>
						<?php echo __('This project has no description'); ?>
					<?php endif; ?>
				</div>
				<div id="project_team">
					<div style="font-weight: bold; float: left; padding: 8px 0 0 0; margin: 0 10px 0 0;"><?php echo __('Team'); ?>:</div>
					<?php if (count($assignees['users']) > 0): ?>
						<?php foreach ($assignees['users'] as $user_id => $info): ?>
							<table cellpadding=0 cellspacing=0 style="width: auto; display: inline; clear: none; margin: 0 10px 0 0;">
								<?php echo include_component('main/userdropdown', array('user' => $user_id)); ?>
							</table>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="faded_medium" style="font-weight: normal; padding: 8px 0 0 0;"><?php echo __('No users or teams assigned'); ?></span>
					<?php endif; ?>
				</div>
			</div>
			<div style="clear: both;">
				<div style="width: 305px; float: left; margin-right: 5px;">
					<div class="header_div"><?php echo __('5 most recent issues / bugs'); ?></div>
					<?php if (count($recent_issues) > 0): ?>
						<table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
						<?php foreach ($recent_issues as $issue): ?>
							<tr>
								<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td style="padding-bottom: 15px;">
									<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(), array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))); ?><br>
									<span class="faded_dark" style="font-size: 11px;">
										<?php echo bugs_formatTime($issue->getPosted(), 20); ?>,
										<strong><?php echo ($issue->getStatus() instanceof BUGSdatatype) ? $issue->getStatus()->getName() : __('Status not determined'); ?></strong>
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_dark" style="padding: 5px; font-size: 12px;"><?php echo __('No issues, bugs or defects posted'); ?></div>
					<?php endif; ?>
				</div>
				<div style="width: 305px; float: left; margin-right: 5px;">
					<div class="header_div"><?php echo __('5 most recent feature requests'); ?></div>
					<?php if (count($recent_features) > 0): ?>
						<table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
						<?php foreach ($recent_features as $issue): ?>
							<tr>
								<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
								<td style="padding-bottom: 15px;">
									<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(), array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))); ?><br>
									<span class="faded_dark" style="font-size: 11px;">
										<?php echo bugs_formatTime($issue->getPosted(), 20); ?>,
										<strong><?php echo ($issue->getStatus() instanceof BUGSdatatype) ? $issue->getStatus()->getName() : __('Status not determined'); ?></strong>
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>
					<?php else: ?>
						<div class="faded_dark" style="padding: 5px; font-size: 12px;"><?php echo __('No feature requests posted yet'); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<br style="clear: both;">
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 10px; clear: both; width: 620px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 2px 5px 4px 5px; font-size: 13px;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey())), __('Show product issues'), array('style' => 'font-weight: bold; float: right;')); ?>
					<?php echo __('See more issues for this product'); ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<div style="clear: both; width: 615px; margin: 20px 5px 0 0;">
				<div class="header_div"><?php echo __('Recent ideas'); ?></div>
				<?php if (count($recent_ideas) > 0): ?>
					<table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
					<?php foreach ($recent_ideas as $issue): ?>
						<tr>
							<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_small.png', array('style' => 'margin-top: 3px;')); ?></td>
							<td style="padding-bottom: 15px; font-size: 13px;">
								<?php echo __('%issue% (posted by %user%)', array('%issue%' => '<b>' . link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(), array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))) . '</b>', '%user%' => '<b>' . $issue->getPostedBy()->getName() . '</b>')); ?><br>
								<span class="faded_dark">
									<?php echo __('%number_of% comments, last updated %time%', array('%number_of%' => $issue->getCommentCount(), '%time%' => bugs_formatTime($issue->getLastUpdatedTime(), 20))); ?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>
				<?php else: ?>
					<div class="faded_dark" style="padding: 5px; font-size: 12px;"><?php echo __('No ideas suggested yet'); ?></div>
				<?php endif; ?>
			</div>
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 10px; clear: both; width: 620px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 2px 5px 4px 5px; font-size: 13px;">
					<?php echo link_tag(make_url('project_planning', array('project_key' => $selected_project->getKey())), __('Show product planning page'), array('style' => 'font-weight: bold; float: right;')); ?>
					<?php echo __('Plan your project, discuss and throw ideas around'); ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</td>
	</tr>
</table>
