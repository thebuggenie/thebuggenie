<?php if (!$project->hasWikiURL()): ?>
	<?php echo link_tag(make_url('publish_article', array('article_name' => ucfirst($project->getKey()).':MainPage')), __('Wiki'), array('class' => 'button button-silver')); ?>
<?php else: ?>
	<?php echo link_tag($project->getWikiURL(), __('Wiki'), array('target' => 'blank', 'class' => 'button button-silver')); ?>
<?php endif; ?>
