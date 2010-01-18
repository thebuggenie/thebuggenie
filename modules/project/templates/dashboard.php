<?php

	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="project_dashboard">
	<tr>
		<td style="width: 320px; padding: 0 5px 0 5px;">
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png', array('style' => 'float: right; margin: 5px;')), array('title' => __('Subscribe to updates'))); ?>
					<?php if (count($recent_activities) > 0): ?>
						<table cellpadding=0 cellspacing=0 class="recent_activities">
							<?php $prev_date = null; ?>
							<?php foreach ($recent_activities as $timestamp => $activities): ?>
								<?php $date = tbg_formatTime($timestamp, 5); ?>
									<?php if ($date != $prev_date): ?>
									<tr>
										<td class="latest_action_dates" colspan="2"><?php echo tbg_formatTime($timestamp, 5); ?></td>
									</tr>
								<?php endif; ?>
								<?php foreach ($activities as $activity): ?>
									<?php if ($activity['change_type'] == 'build_release'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_build.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('New version released'); ?></i></td>
										</tr>
									<?php else: ?>
										<?php include_template('main/logitem', array('action' => $activity, 'include_time' => true, 'extra_padding' => true)); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<div class="timeline_link">
						<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('view_timeline.png', array('style' => 'float: right; margin-left: 5px;')) . __('Show complete timeline')); ?>
					</div>
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
						<div class="faded_medium" style="font-weight: normal; padding: 8px 0 0 0;"><?php echo __('No users or teams assigned'); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<div style="width: 50%; float: left; margin-right: 5px;">
				<div class="header_div">
					<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey())), __('More'), array('style' => 'float: right; font-weight: normal;', 'title' => __('Show more issues'))); ?>
					<?php echo __('10 most recent issues / bugs'); ?>
				</div>
				<?php if (count($recent_issues) > 0): ?>
					<table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
					<?php foreach ($recent_issues as $issue): ?>
						<tr>
							<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
							<td style="padding-bottom: 15px;">
								<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(), array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))); ?><br>
								<span class="faded_dark" style="font-size: 11px;">
									<?php echo tbg_formatTime($issue->getPosted(), 20); ?>,
									<strong><?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getName() : __('Status not determined'); ?></strong>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>
				<?php else: ?>
					<div class="faded_dark" style="padding: 5px; font-size: 12px;"><?php echo __('No issues, tbg_ or defects posted'); ?></div>
				<?php endif; ?>
			</div>
			<div style="float: right; width: 49%; margin: 0 0 10px 0;">
				<div class="header_div">
					<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => 'priority', 'grouporder' => 'desc')), __('More'), array('style' => 'float: right; font-weight: normal;', 'title' => __('Show more issues'))); ?>
					<?php echo __('Open issues by priority'); ?>
				</div>
				<table cellpadding=0 cellspacing=0 class="priority_percentage" style="margin: 5px 0 10px 0; width: 100%;">
				<?php foreach (TBGPriority::getAll() as $priority_id => $priority): ?>
					<tr class="canhover_light">
						<td style="font-weight: normal; font-size: 13px; padding-left: 3px;"><?php echo $priority->getName(); ?></td>
						<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;"><?php echo $priority_count[$priority_id]['open']; ?></td>
						<td style="width: 40%; vertical-align: middle;"><?php include_template('main/percentbar', array('percent' => $priority_count[$priority_id]['percentage'], 'height' => 14)); ?></td>
						<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;">&nbsp;<?php echo (int) $priority_count[$priority_id]['percentage']; ?>%</td>
					</tr>
				<?php endforeach; ?>
				<tr class="canhover_light">
					<td style="font-weight: normal; font-size: 13px; padding-left: 3px;" class="faded_medium"><?php echo __('Priority not set'); ?></td>
					<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;" class="faded_medium"><?php echo $priority_count[0]['open']; ?></td>
					<td style="width: 40%; vertical-align: middle;" class="faded_medium"><?php include_template('main/percentbar', array('percent' => $priority_count[0]['percentage'], 'height' => 14)); ?></td>
					<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;" class="faded_medium">&nbsp;<?php echo (int) $priority_count[0]['percentage']; ?>%</td>
				</tr>
				</table>
				<div class="header_div">
					<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey())), __('More'), array('style' => 'float: right; font-weight: normal;', 'title' => __('Show more issues'))); ?>
					<?php echo __('5 most recent feature requests'); ?>
				</div>
				<?php if (count($recent_features) > 0): ?>
					<table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
					<?php foreach ($recent_features as $issue): ?>
						<tr>
							<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
							<td style="padding-bottom: 15px;">
								<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(), array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))); ?><br>
								<span class="faded_dark" style="font-size: 11px;">
									<?php echo tbg_formatTime($issue->getPosted(), 20); ?>,
									<strong><?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getName() : __('Status not determined'); ?></strong>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>
				<?php else: ?>
					<div class="faded_dark" style="padding: 5px; font-size: 12px;"><?php echo __('No feature requests posted yet'); ?></div>
				<?php endif; ?>
				<div class="header_div">
					<?php echo link_tag(make_url('project_planning', array('project_key' => $selected_project->getKey())), __('Show project planning page'), array('style' => 'font-weight: normal; float: right;')); ?>
					<?php echo __('Recent ideas'); ?>
				</div>
				<?php if (count($recent_ideas) > 0): ?>
					<table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
					<?php foreach ($recent_ideas as $issue): ?>
						<tr>
							<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_small.png', array('style' => 'margin-top: 3px;')); ?></td>
							<td style="padding-bottom: 15px; font-size: 13px;">
								<?php echo __('%issue% (posted by %user%)', array('%issue%' => '<b>' . link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(), array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))) . '</b>', '%user%' => '<b>' . $issue->getPostedBy()->getName() . '</b>')); ?><br>
								<span class="faded_dark">
									<?php echo __('%number_of% comments, last updated %time%', array('%number_of%' => $issue->getCommentCount(), '%time%' => tbg_formatTime($issue->getLastUpdatedTime(), 20))); ?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>
				<?php else: ?>
					<div class="faded_dark" style="padding: 5px; font-size: 12px;"><?php echo __('No ideas suggested yet'); ?></div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>
