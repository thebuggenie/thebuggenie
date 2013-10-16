<div class="article">
	<div class="header">Special:<?php echo ($projectnamespace != '') ? "<span class='faded_out'>{$projectnamespace}</span>" : ''; ?>All Pages</div>
		<div class="greybox" style="margin: 15px;">
			<?php if (TBGContext::isProjectContext()): ?>
				<?php echo __('Note: This page lists all articles for "%project_name". For a list of global articles, see %all_pages', array('%project_name' => TBGContext::getCurrentProject()->getName(), '%all_pages' => link_tag(make_url('publish_article', array('article_name' => "Special:AllPages")), 'Special:AllPages'))); ?>
			<?php else: ?>
				<?php echo __('Note: This page lists all articles in the global scope. For a list of project articles, see the corresponding "All pages" for that specific project'); ?>
			<?php endif; ?>
		</div>
	<p>
		<?php echo __('Below is a listing of all pages.'); ?>
	</p>
	<?php include_template('publish/articleslist', compact('articles')); ?>
</div>
