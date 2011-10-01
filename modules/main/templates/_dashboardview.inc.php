<div class="container_div" style="margin: 0 0 5px 5px;">
<?php switch ($type):
		  case TBGDashboard::DASHBOARD_VIEW_PREDEFINED_SEARCH : ?>
	<?php case TBGDashboard::DASHBOARD_VIEW_SAVED_SEARCH : ?>
			<?php include_component('search/results_view',  array_merge($parameters, array('search' => true, 'default_message' => __('No issues in this list')))); ?>
	<?php break; ?>		
	
	<?php case TBGDashboard::DASHBOARD_VIEW_LOGGED_ACTION : ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('What you\'ve done recently'); ?>
		</div>
		<div id="dashboard_<?php echo $id; ?>">
		<?php if (count($tbg_user->getLatestActions()) > 0): ?>
			<table cellpadding=0 cellspacing=0 style="margin: 5px;">
				<?php $prev_date = null; ?>
				<?php $prev_timestamp = null; ?>
				<?php $prev_issue = null; ?>
				<?php foreach ($tbg_user->getLatestActions() as $action): ?>
					<?php $date = tbg_formatTime($action['timestamp'], 5); ?>
					<?php if ($date != $prev_date): ?>
						<tr>
							<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
						</tr>
					<?php endif; ?>
					<?php include_component('main/logitem', array('log_action' => $action, 'include_project' => true, 'include_issue_title' => !($prev_timestamp == $action['timestamp'] && $prev_issue == $action['target']))); ?>
					<?php $prev_date = $date; ?>
					<?php $prev_timestamp = $action['timestamp']; ?>
					<?php $prev_issue = $action['target']; ?>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __("You haven't done anything recently"); ?></div>
		<?php endif; ?>
		</div>
	<?php break; ?>
	
	<?php case TBGDashboard::DASHBOARD_VIEW_LAST_COMMENTS : ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Recent comments'); ?>
		</div>
		<div id="dashboard_<?php echo $id; ?>">
		<?php $comments = TBGComment::getRecentCommentsByAuthor($tbg_user->getID()); ?>
		<?php if (count($comments)): ?>
			<table cellpadding=0 cellspacing=0 style="margin: 5px;">
				<?php $prev_date = null; ?>
				<?php foreach ($comments as $comment): ?>
					<?php $date = tbg_formatTime($comment->getPosted(), 5); ?>
					<?php if ($date != $prev_date): ?>
						<tr>
							<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
						</tr>
					<?php endif; ?>
					<?php include_component('main/commentitem', array('comment' => $comment, 'include_project' => true)); ?>
					<?php $prev_date = $date; ?>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __('No issues recently commented'); ?></div>
		<?php endif; ?>
		</div>
	<?php break; ?>
	
	<?php case TBGDashboard::DASHBOARD_PROJECT_INFO : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('About this project'); ?>
		</div>
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
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
					<div class="faded_out" style="font-weight: normal;"><?php echo __('No project leader specified'); ?></div>
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
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_TEAM : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $assignees = $selected_project->getAssignees(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Project team'); ?>
		</div>
					
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">			
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
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_CLIENT : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $client = $selected_project->getClient(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Client'); ?>
		</div>
					
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">			
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
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_SUBPROJECTS : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $subprojects = $selected_project->getChildren(false); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Subprojects'); ?>
			<a style="float: right;" href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_archived_projects', 'pid' => $selected_project->getID())); ?>');"><?php echo __('View archived subprojects'); ?></a>
		</div>
					
		<div id="dashboard_<?php echo $id; ?>">			
			<?php if (count($subprojects) > 0): ?>
				<ul class="project_list simple_list">
				<?php foreach ($subprojects as $project): ?>
					<li><?php include_component('project/overview', array('project' => $project)); ?></li>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">	
					<div class="faded_out" style="font-weight: normal;"><?php echo __('This project has no subprojects'); ?></div>
				</div>
			<?php endif; ?>
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_LAST15 : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Last 15 days'); ?>
		</div>
					
		<div id="dashboard_<?php echo $id; ?>">			
			<div style="text-align: center;"><?php echo image_tag(make_url('project_statistics_last_15', array('project_key' => $selected_project->getKey())), array('style' => 'margin-top: 10px;'), true); ?></div>
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_RECENT_ISSUES : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php
			$issuetype_icons = array();
			foreach (TBGIssuetype::getIcons() as $key => $descr)
			{
				$issuetype_icons[] = array('key' => $key, 'descr' => $descr);
			}
			
			if ($issuetype_icons[$view]['key'] == 'bug_report')
			{
				$recent_issues = $selected_project->getRecentIssues();
			}
			elseif ($issuetype_icons[$view]['key'] == 'feature_request')
			{
				$recent_issues = $selected_project->getRecentFeatures();
			}
			elseif ($issuetype_icons[$view]['key'] == 'idea')
			{
				$recent_issues = $selected_project->getRecentIdeas();
			}
			elseif ($issuetype_icons[$view]['key'] == 'enhancement')
			{
				$recent_issues = $selected_project->getRecentEnhancements();
			}
			elseif ($issuetype_icons[$view]['key'] == 'developer_report')
			{
				$recent_issues = $selected_project->getRecentDeveloperReports();
			}
			elseif ($issuetype_icons[$view]['key'] == 'documentation_request')
			{
				$recent_issues = $selected_project->getRecentDocumentationRequests();
			}
			elseif ($issuetype_icons[$view]['key'] == 'support_request')
			{
				$recent_issues = $selected_project->getRecentSupportRequests();
			}
			elseif ($issuetype_icons[$view]['key'] == 'task')
			{
				$recent_issues = $selected_project->getRecentTasks();
			}
		?>
		
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo TBGContext::geti18n()->__('Recent issues: %type%', array('%type%' => $issuetype_icons[$view]['descr'])); ?>
		</div>
		
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
			<?php include_component('recentactivities', array('id' => '10_recent_issues', 'issues' => $recent_issues, 'empty' => 'Nothing posted yet', 'default_displayed' => true)); ?>
		</div>
		
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_RECENT_ACTIVITIES : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $recent_activities = $selected_project->getRecentActivities(5); ?>
		<div class="header">
			<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png', array('style' => 'float: right; margin-right: 5px;'))); ?>
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Recent activities'); ?>
		</div>
					
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
			<?php if (count($recent_activities) > 0): ?>
				<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), __('Show more') . ' &raquo;', array('class' => 'more', 'title' => __('Show more'))); ?>
				<?php include_component('project/timeline', array('activities' => $recent_activities)); ?>
			<?php else: ?>
				<div class="faded_out"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
			<?php endif; ?>
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_UPCOMING : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Upcoming milestones and deadlines'); ?>
		</div>
					
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
			<div class="header"><?php echo __('Milestones finishing in the next 14 days'); ?></div>
			<?php $milestone_cc = 0; ?>
			<?php foreach ($selected_project->getUpcomingMilestonesAndSprints(14) as $milestone): ?>
				<?php if ($milestone->isScheduled()): ?>
					<?php include_template('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
					<?php $milestone_cc++; ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ($milestone_cc == 0): ?>
				<div class="faded_out"><?php echo __('This project has no upcoming milestones.'); ?></div>
			<?php endif; ?>
			<div class="header"><?php echo __('Milestones starting in the next 14 days'); ?></div>
			<?php $milestone_cc = 0; ?>
			<?php foreach ($selected_project->getStartingMilestonesAndSprints(14) as $milestone): ?>
				<?php if ($milestone->isStarting()): ?>
					<?php include_template('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
					<?php $milestone_cc++; ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ($milestone_cc == 0): ?>
				<div class="faded_out"><?php echo __('This project has no upcoming milestones.'); ?></div>
			<?php endif; ?>
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_DOWNLOADS : ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Latest downloads'); ?>
		</div>
					
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">			
			<span class="faded_out">This is coming soon...</span>
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_STATISTICS_PRIORITY: ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $priority_count = $selected_project->getPriorityCount(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Open issues by priority'); ?>
		</div>
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
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
			<?php echo link_tag(make_url('project_statistics', array('project_key' => $selected_project->getKey())), __('Show more statistics'), array('class' => 'button button-silver', 'title' => __('More statistics'))); ?>
			<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => 'priority', 'grouporder' => 'desc')), __('Show details'), array('class' => 'button button-silver', 'title' => __('Show more issues'))); ?>
			<br style="clear: both;">
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_STATISTICS_CATEGORY: ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $category_count = $selected_project->getCategoryCount(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Open issues by category'); ?>
		</div>
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
			<table cellpadding=0 cellspacing=0 class="category_percentage" style="margin: 5px 0 10px 0; width: 100%;">
				<?php foreach (TBGCategory::getAll() as $category_id => $category): ?>
					<tr class="hover_highlight">
						<td style="font-weight: normal; font-size: 13px; padding-left: 3px;"><?php echo $category->getName(); ?></td>
						<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;"><?php echo $category_count[$category_id]['open']; ?></td>
						<td style="width: 40%; vertical-align: middle;"><?php include_template('main/percentbar', array('percent' => $category_count[$category_id]['percentage'], 'height' => 14)); ?></td>
						<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;">&nbsp;<?php echo (int) $category_count[$category_id]['percentage']; ?>%</td>
					</tr>
				<?php endforeach; ?>
				<tr class="hover_highlight">
					<td style="font-weight: normal; font-size: 13px; padding-left: 3px;" class="faded_out"><?php echo __('Category not set'); ?></td>
					<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;" class="faded_out"><?php echo $category_count[0]['open']; ?></td>
					<td style="width: 40%; vertical-align: middle;" class="faded_out"><?php include_template('main/percentbar', array('percent' => $category_count[0]['percentage'], 'height' => 14)); ?></td>
					<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;" class="faded_out">&nbsp;<?php echo (int) $category_count[0]['percentage']; ?>%</td>
				</tr>
			</table>
			<?php echo link_tag(make_url('project_statistics', array('project_key' => $selected_project->getKey())), __('Show more statistics'), array('class' => 'button button-silver', 'title' => __('More statistics'))); ?>
			<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => 'category', 'grouporder' => 'desc')), __('Show details'), array('class' => 'button button-silver', 'title' => __('Show more issues'))); ?>
			<br style="clear: both;">
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_STATISTICS_STATUS: ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $status_count = $selected_project->getStatusCount(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Open issues by status'); ?>
		</div>
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
			<table cellpadding=0 cellspacing=0 class="status_percentage" style="margin: 5px 0 10px 0; width: 100%;">
				<?php foreach (TBGStatus::getAll() as $status_id => $status): ?>
					<tr class="hover_highlight">
						<td style="font-weight: normal; font-size: 13px; padding-left: 3px;"><?php echo $status->getName(); ?></td>
						<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;"><?php echo $status_count[$status_id]['open']; ?></td>
						<td style="width: 40%; vertical-align: middle;"><?php include_template('main/percentbar', array('percent' => $status_count[$status_id]['percentage'], 'height' => 14)); ?></td>
						<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;">&nbsp;<?php echo (int) $status_count[$status_id]['percentage']; ?>%</td>
					</tr>
				<?php endforeach; ?>
				<tr class="hover_highlight">
					<td style="font-weight: normal; font-size: 13px; padding-left: 3px;" class="faded_out"><?php echo __('Status not set'); ?></td>
					<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;" class="faded_out"><?php echo $status_count[0]['open']; ?></td>
					<td style="width: 40%; vertical-align: middle;" class="faded_out"><?php include_template('main/percentbar', array('percent' => $status_count[0]['percentage'], 'height' => 14)); ?></td>
					<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;" class="faded_out">&nbsp;<?php echo (int) $status_count[0]['percentage']; ?>%</td>
				</tr>
			</table>
			<?php echo link_tag(make_url('project_statistics', array('project_key' => $selected_project->getKey())), __('Show more statistics'), array('class' => 'button button-silver', 'title' => __('More statistics'))); ?>
			<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => 'status', 'grouporder' => 'desc')), __('Show details'), array('class' => 'button button-silver', 'title' => __('Show more issues'))); ?>
			<br style="clear: both;">
		</div>
		<?php break; ?>
	<?php case TBGDashboard::DASHBOARD_PROJECT_STATISTICS_RESOLUTION: ?>
		<?php $selected_project = TBGContext::getCurrentProject(); ?>
		<?php $resolution_count = $selected_project->getResolutionCount(); ?>
		<div class="header">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Open issues by resolution'); ?>
		</div>
		<div class="dashboard_view_content" id="dashboard_<?php echo $id; ?>">
			<table cellpadding=0 cellspacing=0 class="resolution_percentage" style="margin: 5px 0 10px 0; width: 100%;">
				<?php foreach (TBGResolution::getAll() as $resolution_id => $resolution): ?>
					<tr class="hover_highlight">
						<td style="font-weight: normal; font-size: 13px; padding-left: 3px;"><?php echo $resolution->getName(); ?></td>
						<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;"><?php echo $resolution_count[$resolution_id]['open']; ?></td>
						<td style="width: 40%; vertical-align: middle;"><?php include_template('main/percentbar', array('percent' => $resolution_count[$resolution_id]['percentage'], 'height' => 14)); ?></td>
						<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;">&nbsp;<?php echo (int) $resolution_count[$resolution_id]['percentage']; ?>%</td>
					</tr>
				<?php endforeach; ?>
				<tr class="hover_highlight">
					<td style="font-weight: normal; font-size: 13px; padding-left: 3px;" class="faded_out"><?php echo __('Resolution not set'); ?></td>
					<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;" class="faded_out"><?php echo $resolution_count[0]['open']; ?></td>
					<td style="width: 40%; vertical-align: middle;" class="faded_out"><?php include_template('main/percentbar', array('percent' => $resolution_count[0]['percentage'], 'height' => 14)); ?></td>
					<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;" class="faded_out">&nbsp;<?php echo (int) $resolution_count[0]['percentage']; ?>%</td>
				</tr>
			</table>
			<?php echo link_tag(make_url('project_statistics', array('project_key' => $selected_project->getKey())), __('Show more statistics'), array('class' => 'button button-silver', 'title' => __('More statistics'))); ?>
			<?php echo link_tag(make_url('project_issues', array('project_key' => $selected_project->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => 'resolution', 'grouporder' => 'desc')), __('Show details'), array('class' => 'button button-silver', 'title' => __('Show more issues'))); ?>
			<br style="clear: both;">
		</div>
		<?php break; ?>
	<?php endswitch;?>
	<?php TBGEvent::createNew('core', 'dashboard_main_' . $id)->trigger(); ?>
</div>