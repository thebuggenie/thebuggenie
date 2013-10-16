<div class="article">
	<div class="header">Special:<?php echo ($projectnamespace != '') ? "<span class='faded_out'>{$projectnamespace}</span>" : ''; ?>Dead End Pages</div>
	<?php if (TBGContext::isProjectContext()): ?>
		<div class="greybox" style="margin: 15px;">
			<?php echo __('Note: This page lists all dead end articles for "%project_name". For a list of global articles with no links, see %dead_end_pages', array('%project_name' => TBGContext::getCurrentProject()->getName(), '%dead_end_pages' => link_tag(make_url('publish_article', array('article_name' => "Special:DeadEndPages")), 'Special:DeadEndPages'))); ?>
		</div>
	<?php endif; ?>
	<p>
		<?php echo __('Below is a listing of all pages that has no links to other pages.'); ?>
	</p>
	<?php include_template('publish/articleslist', array('articles' => $articles, 'include_redirects' => false)); ?>
</div>
