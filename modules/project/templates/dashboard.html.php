<?php

	$tbg_response->addBreadcrumb(__('Project information'));
	$tbg_response->addBreadcrumb(__('Dashboard'));
	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));
	if ($tbg_user->canEditProjectDetails($selected_project))
	{
		$tbg_response->addJavascript('config/projects_ajax.js');
	}

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<td class="project_information_sidebar">
			<?php /*
			<div class="rounded_box lightgrey borderless" style="margin-top: 5px;">
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
									<?php elseif ($activity['change_type'] == 'sprint_start'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_sprint.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('A new sprint has started'); ?></i></td>
										</tr>
									<?php elseif ($activity['change_type'] == 'sprint_end'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_sprint.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('The sprint has ended'); ?></i></td>
										</tr>
									<?php elseif ($activity['change_type'] == 'milestone_release'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_milestone.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('A new milestone has been reached'); ?></i></td>
										</tr>
									<?php else: ?>
										<?php include_component('main/logitem', array('log_action' => $activity, 'include_time' => true, 'extra_padding' => true, 'include_details' => false)); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<div class="timeline_link">
						<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('view_timeline.png', array('style' => 'float: right; margin-left: 5px;')) . __('Show complete timeline')); ?>
					</div>
					<?php else: ?>
						<div class="faded_out dark" style="font-size: 13px; padding-top: 3px;"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
					<?php endif; ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div> */ ?>
			<div id="project_header_container">
				<div>
					<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?><?php echo javascript_link_tag(image_tag('cfg_icon_projectheader.png'), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $selected_project->getID()))."');")); ?><?php endif; ?>
					<div id="project_name">
						<?php echo image_tag($selected_project->getIcon(), array('class' => 'logo'), $selected_project->hasIcon()); ?>
						<?php echo $selected_project->getName(); ?><br>
						<span><?php echo $selected_project->getKey(); ?></span>
					</div>
					<div id="project_description"<?php if (!$selected_project->hasDescription()): ?> class="faded_out dark"<?php endif; ?>>
						<?php if ($selected_project->hasDescription()): ?>
							<?php echo tbg_parse_text($selected_project->getDescription()); ?>
						<?php else: ?>
							<?php echo __('This project has no description'); ?>
						<?php endif; ?>
					</div>
					<div class="sidebar_links">
						<?php include_template('project/projectinfolinks'); ?>
					</div>
				</div>
			</div>
		</td>
		<td class="project_information_main">
			<div id="project_team">
				<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Team'); ?>:</div>
				<?php if (count($assignees['users']) > 0): ?>
					<?php foreach ($assignees['users'] as $user_id => $info): ?>
						<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
							<?php echo include_component('main/userdropdown', array('user' => $user_id)); ?>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="faded_out" style="font-weight: normal;"><?php echo __('No users or teams assigned'); ?></div>
				<?php endif; ?>
			</div>
			<div style="width: 680px; padding-right: 5px;">
				<?php echo image_tag(make_url('project_statistics_last_30', array('project_key' => $selected_project->getKey())), array('style' => 'float: right; margin-bottom: 15px;'), true); ?>
				<div style="clear: both; height: 30px;" class="tab_menu">
					<ul id="project_dashboard_menu">
						<li class="selected" id="tab_10_recent_issues"><a onclick="switchSubmenuTab('tab_10_recent_issues', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Recent issues / bugs'); ?></a></li>
						<li id="tab_5_recent_requests"><a onclick="switchSubmenuTab('tab_5_recent_requests', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Recent feature requests'); ?></a></li>
						<li id="tab_recent_ideas"><a onclick="switchSubmenuTab('tab_recent_ideas', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Recent ideas'); ?></a></li>
						<li id="tab_statistics"><a onclick="switchSubmenuTab('tab_statistics', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Statistics'); ?></a></li>
					</ul>
				</div>
				<div id="project_dashboard_menu_panes">
					<?php include_component('recentactivities', array('id' => '10_recent_issues', 'issues' => $recent_issues, 'link' => link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'predefined_search' => TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES, 'search' => true)), __('More') . ' &raquo;', array('class' => 'more', 'title' => __('Show more issues'))), 'empty' => 'No issues, bugs or defects posted yet', 'default_displayed' => true)); ?>
					<?php include_component('recentactivities', array('id' => '5_recent_requests', 'issues' => $recent_features, 'link' => link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey())), __('More') . ' &raquo;', array('class' => 'more', 'title' => __('Show more issues'))), 'empty' => 'No feature requests posted yet')); ?>
					<?php include_component('recentactivities', array('id' => 'recent_ideas', 'issues' => $recent_ideas, 'link' => link_tag(make_url('project_planning', array('project_key' => $selected_project->getKey())), __('Show project planning page') . ' &raquo;', array('class' => 'more')), 'empty' => 'No ideas suggested yet')); ?>
					<div id="tab_statistics_pane" style="display: none;">
						<?php echo link_tag(make_url('project_statistics', array('project_key' => $selected_project->getKey())), __('More statistics') . ' &raquo;', array('class' => 'more', 'title' => __('Show more issues'))); ?>
						<div class="header_div">
							<?php echo __('Open issues by priority'); ?>
						</div>
						<table cellpadding=0 cellspacing=0 class="priority_percentage" style="margin: 5px 0 10px 0; width: 100%;">
							<?php foreach (TBGPriority::getAll() as $priority_id => $priority): ?>
								<tr class="hover_highlight">
									<td style="font-weight: normal; font-size: 13px; padding-left: 3px;"><?php echo $priority->getName(); ?></td>
									<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;"><?php echo $priority_count[$priority_id]['open']; ?></td>
									<td style="width: 40%; vertical-align: middle;"><?php include_template('main/percentbar', array('percent' => $priority_count[$priority_id]['percentage'], 'height' => 14)); ?></td>
									<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;">&nbsp;<?php echo (int) $priority_count[$priority_id]['percentage']; ?>%</td>
								</tr>
							<?php endforeach; ?>
							<tr class="hover_highlight">
								<td style="font-weight: normal; font-size: 13px; padding-left: 3px;" class="faded_out"><?php echo __('Priority not set'); ?></td>
								<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;" class="faded_out"><?php echo $priority_count[0]['open']; ?></td>
								<td style="width: 40%; vertical-align: middle;" class="faded_out"><?php include_template('main/percentbar', array('percent' => $priority_count[0]['percentage'], 'height' => 14)); ?></td>
								<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;" class="faded_out">&nbsp;<?php echo (int) $priority_count[0]['percentage']; ?>%</td>
							</tr>
						</table>
						<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => 'priority', 'grouporder' => 'desc')), __('Show details'), array('class' => 'more', 'title' => __('Show more issues'))); ?>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>
