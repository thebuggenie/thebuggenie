<?php

	if (!isset($selected_project))
	{
		$selected_project = TBGContext::getCurrentProject();
	}
	$scrum_additional_array = (!TBGContext::getCurrentProject()->usesScrum()) ? array('id' => 'sidebar_link_scrum', 'style' => 'display: none;') : array('id' => 'sidebar_link_scrum');

	if(!isset($submenu)): $submenu = false; endif;
?>
<?php if ($tbg_user->hasProjectPageAccess('project_dashboard', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Dashboard'), (($tbg_response->getPage() == 'project_dashboard') ? array('class' => 'selected') : null)); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_dashboard')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_releases', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_releases', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Releases'), (($tbg_response->getPage() == 'project_releases') ? array('class' => 'selected') : null)); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_releases')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_scrum', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_scrum', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Sprint planning'), ((in_array($tbg_response->getPage(), array('project_scrum', 'project_scrum_sprint_details'))) ? array_merge($scrum_additional_array, array('class' => 'selected')) : $scrum_additional_array)); ?>
	<?php if (!isset($submenu) && (count($selected_project->getSprints()) > 0) && TBGContext::getCurrentProject()->usesScrum() && in_array($tbg_response->getPage(), array('project_scrum', 'project_scrum_sprint_details'))): ?>
		<ul class="simple_list">
			<?php foreach ($selected_project->getSprints() as $sprint): ?>
				<li><?php echo link_tag(make_url('project_scrum_sprint_details', array('project_key' => $selected_project->getKey(), 'sprint_id' => $sprint->getID())), $sprint->getName()); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_scrum')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_roadmap', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_roadmap', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Roadmap'), (($tbg_response->getPage() == 'project_roadmap') ? array('class' => 'selected') : array())); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_roadmap')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_team', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_team', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Team overview'), (($tbg_response->getPage() == 'project_team') ? array('class' => 'selected') : array())); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_team')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_statistics', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_statistics', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Statistics'), (($tbg_response->getPage() == 'project_statistics') ? array('class' => 'selected') : array())); ?>
	<?php if (!($submenu) && $tbg_response->getPage() == 'project_statistics'): ?>
		<ul class="simple_list">
			<li><b><?php echo __('Number of issues per:'); ?></b></li>
			<li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_state')); ?>');"><?php echo __('%number_of_issues_per% State (open / closed)', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_category')); ?>');"><?php echo __('%number_of_issues_per% Category', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_priority')); ?>');"><?php echo __('%number_of_issues_per% Priority level', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_resolution')); ?>');"><?php echo __('%number_of_issues_per% Resolution', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_reproducability')); ?>');"><?php echo __('%number_of_issues_per% Reproducability', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_status')); ?>');"><?php echo __('%number_of_issues_per% Status type', array('%number_of_issues_per%' => '')); ?></a></li>
		</ul>
	<?php endif; ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_statistics')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_timeline', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Timeline'), (($tbg_response->getPage() == 'project_timeline') ? array('class' => 'selected') : null)); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_timeline')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?>
	<?php echo link_tag(make_url('project_settings', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Settings'), (($tbg_response->getPage() == 'project_settings') ? array('class' => 'selected') : array())); ?>
	<?php if (!($submenu) && $tbg_response->getPage() == 'project_settings'): ?>
		<?php if (!isset($selected_tab)) $selected_tab = 'info'; ?>
		<ul class="simple_list" id="project_config_menu">
			<li id="tab_information"<?php if ($selected_tab == 'info'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Project details'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_information', 'project_config_menu');")); ?></li>
			<li id="tab_settings"<?php if ($selected_tab == 'settings'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Additional settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_settings', 'project_config_menu');")); ?></li>
			<li id="tab_hierarchy"<?php if ($selected_tab == 'hierarchy'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Hierarchy'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_hierarchy', 'project_config_menu');")); ?></li>
			<li id="tab_milestones"<?php if ($selected_tab == 'milestones'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Milestones'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_milestones', 'project_config_menu');")); ?></li>
			<li id="tab_developers"<?php if ($selected_tab == 'developers'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Team'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_developers', 'project_config_menu');")); ?></li>
			<li id="tab_other"<?php if ($selected_tab == 'other'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Other'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_other', 'project_config_menu');")); ?></li>
			<li id="tab_permissions"<?php if ($selected_tab == 'permissions'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('permissions'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_permissions', 'project_config_menu');")); ?></li>
			<?php TBGEvent::createNew('core', 'config_project_tabs')->trigger(array('selected_tab' => $selected_tab)); ?>
		</ul>
	<?php endif; ?>
	<?php echo link_tag(make_url('project_release_center', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Release center'), (($tbg_response->getPage() == 'project_release_center') ? array('class' => 'selected') : array())); ?>
<?php endif; ?>
