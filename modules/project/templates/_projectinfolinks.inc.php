<?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Dashboard'), (($tbg_response->getPage() == 'project_dashboard') ? array('class' => 'selected') : array())); ?>
<?php //echo link_tag(make_url('project_planning', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Planning')); ?>
<?php //echo link_tag(make_url('project_files', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Files')); ?>
<?php if (TBGContext::getCurrentProject()->usesScrum()): ?>
	<?php echo link_tag(make_url('project_scrum', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Sprint planning'), (($tbg_response->getPage() == 'project_scrum') ? array('class' => 'selected') : array())); ?>
<?php endif; ?>
<?php echo link_tag(make_url('project_roadmap', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Roadmap'), (($tbg_response->getPage() == 'project_roadmap') ? array('class' => 'selected') : array())); ?>
<?php echo link_tag(make_url('project_team', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Team overview'), (($tbg_response->getPage() == 'project_team') ? array('class' => 'selected') : array())); ?>
<?php echo link_tag(make_url('project_statistics', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Statistics'), (($tbg_response->getPage() == 'project_statistics') ? array('class' => 'selected') : array())); ?>
<?php echo link_tag(make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Timeline'), (($tbg_response->getPage() == 'project_timeline') ? array('class' => 'selected') : array())); ?>