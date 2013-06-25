<div id="manual_sidebar">
	<h3>
		<?php if (TBGContext::isProjectContext()): ?>
			<?php echo __('%project% manual', array('%project%' => TBGContext::getCurrentProject()->getName())); ?>
		<?php else: ?>
			<?php echo __('Main manual'); ?>
		<?php endif; ?>
	</h3>
	<ul>
		<?php $level = 0; ?>
		<?php foreach ($articles as $main_article): ?>
			<?php include_template('publish/manualsidebarlink', compact('parents', 'article', 'main_article', 'level')); ?>
		<?php endforeach; ?>
	</ul>
</div>