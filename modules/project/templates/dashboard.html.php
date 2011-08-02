<?php

	$tbg_response->addBreadcrumb(__('Dashboard'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<?php TBGEvent::createNew('core', 'project_dashboard_top')->trigger(); ?>
			<?php if (false) :?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any view. To add views in this dashboard, press the 'Customize dashboard'-icon to the far right."); ?></p>
			<?php else: ?>
				<ul id="dashboard">
					<li style="clear: left;">
						<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
							<?php echo __('About this project'); ?>
						</div>
						<div class="dashboard_view_content">
							<div id="project_description_span">
								<?php if ($selected_project->hasDescription()): ?>
									<?php echo tbg_parse_text($selected_project->getDescription()); ?>
								<?php endif; ?>
							</div>
	
							<div id="project_no_description"<?php if ($selected_project->hasDescription()): ?> style="display: none;"<?php endif; ?>>
								<?php echo __('This project has no description'); ?>
							</div>
							
							<div id="project_website">
								<span style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Homepage: '); ?></span>
								<?php if ($selected_project->hasHomepage()): ?>
									<a href="<?php echo $selected_project->getHomepage(); ?>" target="_blank"><?php echo $selected_project->getHomepage(); ?></a>
								<?php else: ?>
									<span class="faded_out" style="font-weight: normal;"><?php echo __('No homepage provided'); ?></span>
								<?php endif; ?>
							</div>
							
							<div id="project_documentation">
								<span style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Documentation: '); ?></span>
								<?php if ($selected_project->hasDocumentationURL()): ?>
									<a href="<?php echo $selected_project->getDocumentationURL(); ?>" target="_blank"><?php echo $selected_project->getDocumentationURL(); ?></a>
								<?php else: ?>
									<span class="faded_out" style="font-weight: normal;"><?php echo __('No documentation URL provided'); ?></span>
								<?php endif; ?>
							</div>
							
							<div id="project_owner">
								<?php if ($selected_project->hasOwner()): ?>
									<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Owned by: %name%', array('%name%' => '')); ?></div>
									<?php if ($selected_project->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
										<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
											<?php echo include_component('main/userdropdown', array('user' => $selected_project->getOwner())); ?>
										</div>
									<?php else: ?>
										<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
											<?php echo include_component('main/teamdropdown', array('team' => $selected_project->getOwner())); ?>
										</div>
									<?php endif; ?>
								<?php else: ?>
									<div class="faded_out" style="font-weight: normal;"><?php echo __('No project owner specified'); ?></div>
								<?php endif; ?>
							</div>
							<div id="project_leader">
								<?php if ($selected_project->hasLeader()): ?>
									<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Lead by: %name%', array('%name%' => '')); ?></div>
									<?php if ($selected_project->getLeaderType() == TBGIdentifiableClass::TYPE_USER): ?>
										<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
											<?php echo include_component('main/userdropdown', array('user' => $selected_project->getLeader())); ?>
										</div>
									<?php else: ?>
										<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
											<?php echo include_component('main/teamdropdown', array('team' => $selected_project->getLeader())); ?>
										</div>
									<?php endif; ?>
								<?php else: ?>
									<div class="faded_out" style="font-weight: normal;"><?php echo __('Nor project leader specified'); ?></div>
								<?php endif; ?>
							</div>
							<div id="project_qa">
								<?php if ($selected_project->hasQaResponsible()): ?>
									<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('QA responsible: %name%', array('%name%' => '')); ?></div>
									<?php if ($selected_project->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER): ?>
										<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
											<?php echo include_component('main/userdropdown', array('user' => $selected_project->getQaResponsible())); ?>
										</div>
									<?php else: ?>
										<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
											<?php echo include_component('main/teamdropdown', array('team' => $selected_project->getQaResponsible())); ?>
										</div>
									<?php endif; ?>
								<?php else: ?>
									<div class="faded_out" style="font-weight: normal;"><?php echo __('No QA responsible specified'); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</li>
					<li style="clear: right;">
						<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
							<?php echo __('Project team'); ?>
						</div>
									
						<div class="dashboard_view_content">			
							<?php if ((count($assignees['users']) > 0) || (count($assignees['teams']) > 0)): ?>
								<?php foreach ($assignees['users'] as $user_id => $info): ?>
									<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
										<?php echo include_component('main/userdropdown', array('user' => $user_id)); ?>
										<span class="faded_out"> -
										<?php foreach ($info as $type => $bool): ?>
											<?php if ($bool == true): ?>
												<?php echo ' '.TBGProjectAssigneesTable::getTypeName($type); ?>
											<?php endif; ?>
										<?php endforeach; ?>
										</span>
									</div>
								<?php endforeach; ?>
								<?php foreach ($assignees['teams'] as $user_id => $info): ?>
									<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
										<?php echo include_component('main/teamdropdown', array('team' => $team_id)); ?>
										<span class="faded_out"> -
										<?php foreach ($info as $type => $bool): ?>
											<?php if ($bool == true): ?>
												<?php echo ' '.TBGProjectAssigneesTable::getTypeName($type); ?>
											<?php endif; ?>
										<?php endforeach; ?>
										</span>
									</div>
								<?php endforeach; ?>
							<?php else: ?>
								<p class="content faded_out"><?php echo __('No users or teams assigned'); ?>.</p>
							<?php endif; ?>
						</div>
					</li>
					<li style="clear: left">
						<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
							<?php echo __('Client'); ?>
						</div>

						<div class="dashboard_view_content">	
							<div id="project_client">
								<?php if ($client instanceof TBGClient): ?>
									<div class="project_client_info">
										<?php echo include_template('project/clientinfo', array('client' => $client)); ?>
									</div>
								<?php else: ?>
									<div class="faded_out" style="font-weight: normal;"><?php echo __('No client assigned'); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</li>
					<li style="clear: right;">
						<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
							<?php echo __('Subprojects'); ?>
						</div>
						<?php if ($selected_project->hasChildren()): ?>
							<ul class="project_list simple_list">
							<?php foreach ($subprojects as $project): ?>
								<li><?php include_component('project/overview', array('project' => $project)); ?></li>
							<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<div class="dashboard_view_content">	
								<div class="faded_out" style="font-weight: normal;"><?php echo __('This project has no subprojects'); ?></div>
							</div>
						<?php endif; ?>
					</li>
					<li style="clear: left;">
						<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
							<?php echo __('Graphical overview'); ?>
						</div>
						<div style="text-align: center;"><?php echo image_tag(make_url('project_statistics_last_15', array('project_key' => $selected_project->getKey())), array('style' => 'margin-top: 10px;'), true); ?></div>
					</li>
					<li style="clear: right;">
						<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
							<?php echo __('Recently in this project'); ?>
						</div>
					
						<div style="clear: both; height: 30px; margin-top: 20px;" class="tab_menu">
							<ul id="project_dashboard_menu">
								<li class="selected" id="tab_10_recent_issues"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_10_recent_issues', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Recent issues / bugs'); ?></a></li>
								<li id="tab_5_recent_requests"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_5_recent_requests', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Recent feature requests'); ?></a></li>
								<li id="tab_recent_ideas"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_recent_ideas', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Recent ideas'); ?></a></li>
								<li id="tab_statistics"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_statistics', 'project_dashboard_menu');" href="javascript:void(0);"><?php echo __('Statistics'); ?></a></li>
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
					</li>
				</ul>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'project_dashboard_bottom')->trigger(); ?>
		</td>
	</tr>
</table>
