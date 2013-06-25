<div class="article">
	<div class="header">Special:<?php echo ($projectnamespace != '') ? "<span class='faded_out'>{$projectnamespace}</span>" : ''; ?>All Pages</div>
	<?php if (TBGContext::isProjectContext()): ?>
		<div class="greybox" style="margin: 15px;">
			<?php echo __('Note: This page lists all articles for "%project_name%". For a list of global articles, see %all_pages%', array('%project_name%' => TBGContext::getCurrentProject()->getName(), '%all_pages%' => link_tag(make_url('publish_article', array('article_name' => "Special:AllPages")), 'Special:AllPages'))); ?>
		</div>
	<?php endif; ?>
	<p>
		<?php echo __('Below is a listing of all pages.'); ?>
	</p>
	<?php include_template('publish/articleslist', compact('articles')); ?>
</div>