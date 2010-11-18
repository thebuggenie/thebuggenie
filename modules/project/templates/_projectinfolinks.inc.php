<?php

	if (!isset($selected_project))
	{
		$selected_project = TBGContext::getCurrentProject();
	}
	$scrum_additional_array = (!TBGContext::getCurrentProject()->usesScrum()) ? array('id' => 'sidebar_link_scrum', 'style' => 'display: none;') : array('id' => 'sidebar_link_scrum');

?>
<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Dashboard'), (($tbg_response->getPage() == 'project_dashboard') ? array('class' => 'selected first') : array('class' => 'first'))); ?>
<?php //echo link_tag(make_url('project_planning', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Planning')); ?>
<?php //echo link_tag(make_url('project_files', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Files')); ?>
<?php echo link_tag(make_url('project_scrum', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Sprint planning'), (($tbg_response->getPage() == 'project_scrum') ? array_merge($scrum_additional_array, array('class' => 'selected')) : $scrum_additional_array)); ?>
<?php echo link_tag(make_url('project_planning', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Planning'), (($tbg_response->getPage() == 'project_planning') ? array('class' => 'selected') : array())); ?>
<?php echo link_tag(make_url('project_roadmap', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Roadmap'), (($tbg_response->getPage() == 'project_roadmap') ? array('class' => 'selected') : array())); ?>
<?php echo link_tag(make_url('project_team', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Team overview'), (($tbg_response->getPage() == 'project_team') ? array('class' => 'selected') : array())); ?>
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
<?php echo link_tag(make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Timeline'), (($tbg_response->getPage() == 'project_timeline') ? array('class' => 'selected last') : array('class' => 'last'))); ?>