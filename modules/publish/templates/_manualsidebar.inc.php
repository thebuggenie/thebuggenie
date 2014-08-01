<div id="manual_sidebar" class="<?php if (TBGContext::isProjectContext()) echo ' single_parent'; ?>">
	<?php if (!TBGContext::isProjectContext()): ?>
		<h3>
			<?php echo __('Main manual'); ?>
		</h3>
	<?php endif; ?>
	<ul>
		<?php $level = 0; ?>
		<?php $first = true; ?>
		<?php include_template('publish/manualsidebarlink', compact('parents', 'article', 'main_article', 'level', 'first')); ?>
	</ul>
</div>
