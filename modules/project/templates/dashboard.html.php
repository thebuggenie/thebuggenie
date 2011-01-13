<?php

	$tbg_response->addBreadcrumb(__('Project dashboard'));
	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div id="project_client">
				<?php if ($client instanceof TBGClient): ?>
					<div class="project_client_info rounded_box lightgrey">
						<?php echo include_template('project/clientinfo', array('client' => $client)); ?>
					</div>
				<?php else: ?>
					<div class="faded_out" style="font-weight: normal;"><?php echo __('No client assigned'); ?></div>
				<?php endif; ?>
			</div>
			<div id="project_team">
				<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Team'); ?>:</div>
				<?php if ((count($assignees['users']) > 0) || (count($assignees['teams']) > 0)): ?>
					<?php foreach ($assignees['users'] as $user_id => $info): ?>
						<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
							<?php echo include_component('main/userdropdown', array('user' => $user_id)); ?>
						</div>
					<?php endforeach; ?>
					<?php foreach ($assignees['teams'] as $team_id => $info): ?>
						<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
							<?php echo include_component('main/teamdropdown', array('team' => $team_id)); ?>
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
					<?php include_component('recentactivities', array('id' => '10_recent_issues', 'issues' => $recent_issues, 'link' => link_tag(make_url('project_open_issues', array('project_key' => $selected_project->getKey())), __('More') . ' &raquo;', array('class' => 'more', 'title' => __('Show more issues'))), 'empty' => 'No issues, bugs or defects posted yet', 'default_displayed' => true)); ?>
					<?php include_component('recentactivities', array('id' => '5_recent_requests', 'issues' => $recent_features, 'link' => link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey())), __('More') . ' &raquo;', array('class' => 'more', 'title' => __('Show more issues'))), 'empty' => 'No feature requests posted yet')); ?>
					<?php /* include_component('recentactivities', array('id' => 'recent_ideas', 'issues' => $recent_ideas, 'link' => link_tag(make_url('project_planning', array('project_key' => $selected_project->getKey())), __('Show project planning page') . ' &raquo;', array('class' => 'more')), 'empty' => 'No ideas suggested yet')); */ ?>
					<?php include_component('recentactivities', array('id' => 'recent_ideas', 'issues' => $recent_ideas, 'link' => null, 'empty' => 'No ideas suggested yet')); ?>
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
