<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Dashboard')); ?>
<?php //echo link_tag(make_url('project_planning', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Planning')); ?>
<?php //echo link_tag(make_url('project_files', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Files')); ?>
<?php if (TBGContext::getCurrentProject()->usesScrum()): ?>
	<?php echo link_tag(make_url('project_scrum', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Sprint planning')); ?>
<?php endif; ?>
<?php echo link_tag(make_url('project_roadmap', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Roadmap')); ?>
<?php echo link_tag(make_url('project_team', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Team overview')); ?>
<?php echo link_tag(make_url('project_statistics', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Statistics')); ?>
<?php echo link_tag(make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Timeline')); ?>