<?php

	if (!isset($selected_project))
	{
		$selected_project = TBGContext::getCurrentProject();
	}
	$scrum_additional_array = (!TBGContext::getCurrentProject()->usesScrum()) ? array('id' => 'sidebar_link_scrum', 'style' => 'display: none;') : array('id' => 'sidebar_link_scrum');

?>
<?php if ($tbg_user->hasProjectPageAccess('project_dashboard', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Dashboard'), (($tbg_response->getPage() == 'project_dashboard') ? array('class' => 'selected first') : array('class' => 'first'))); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_dashboard')->trigger(); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_scrum', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_scrum', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Sprint planning'), ((in_array($tbg_response->getPage(), array('project_scrum', 'project_scrum_sprint_details'))) ? array_merge($scrum_additional_array, array('class' => 'selected')) : $scrum_additional_array)); ?>
	<?php if (!isset($submenu) && TBGContext::getCurrentProject()->usesScrum() && in_array($tbg_response->getPage(), array('project_scrum', 'project_scrum_sprint_details'))): ?>
		<ul class="simple_list">
			<?php foreach ($selected_project->getSprints() as $sprint): ?>
				<li><?php echo link_tag(make_url('project_scrum_sprint_details', array('project_key' => $selected_project->getKey(), 'sprint_id' => $sprint->getID())), $sprint->getName()); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_scrum')->trigger(); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_roadmap', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_roadmap', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Roadmap'), (($tbg_response->getPage() == 'project_roadmap') ? array('class' => 'selected') : array())); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_roadmap')->trigger(); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_team', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_team', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Team overview'), (($tbg_response->getPage() == 'project_team') ? array('class' => 'selected') : array())); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_team')->trigger(); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_statistics', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_statistics', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Statistics'), (($tbg_response->getPage() == 'project_statistics') ? array('class' => 'selected') : array())); ?>
	<?php if (!isset($submenu) && $tbg_response->getPage() == 'project_statistics'): ?>
		<ul class="simple_list">
			<li><b><?php echo __('Number of issues per:'); ?></b></li>
			<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_state')); ?>');"><?php echo __('%number_of_issues_per% State (open / closed)', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_category')); ?>');"><?php echo __('%number_of_issues_per% Category', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_priority')); ?>');"><?php echo __('%number_of_issues_per% Priority level', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_resolution')); ?>');"><?php echo __('%number_of_issues_per% Resolution', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_reproducability')); ?>');"><?php echo __('%number_of_issues_per% Reproducability', array('%number_of_issues_per%' => '')); ?></a></li>
			<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_status')); ?>');"><?php echo __('%number_of_issues_per% Status type', array('%number_of_issues_per%' => '')); ?></a></li>
		</ul>
	<?php endif; ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_statistics')->trigger(); ?>
<?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_timeline', $selected_project->getID())): ?>
	<?php echo link_tag(make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Timeline'), (($tbg_response->getPage() == 'project_timeline') ? array('class' => 'selected last') : array('class' => 'last'))); ?>
	<?php TBGEvent::createNew('core', 'project_sidebar_links_timeline')->trigger(); ?>
<?php endif; ?>