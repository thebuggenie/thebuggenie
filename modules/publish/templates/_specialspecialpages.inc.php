<div class="article">
	<div class="header"><?php echo __('Wiki special pages'); ?></div>
	<?php if (TBGContext::isProjectContext()): ?>
		<div class="greybox" style="margin: 15px;">
			<?php echo __('Note: This page lists all project-specific special pages for "%project_name". For a list of global special pages, see %special_pages', array('%project_name' => TBGContext::getCurrentProject()->getName(), '%special_pages' => link_tag(make_url('publish_article', array('article_name' => "Special:SpecialPages")), __('Special pages')))); ?>
		</div>
	<?php endif; ?>
	<p>
		<?php echo __('This is a list of all the "special pages" available in The Bug Genie wiki. These are generated automatically and cannot be edited via the builtin wiki-editor.'); ?>
	</p>
	<h3><?php echo __('Wiki maintenance'); ?></h3>
	<ul class="category_list">
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}DeadEndPages")), __('Dead end pages'), array('title' => "Special:{$projectnamespace}DeadEndPages")); ?></li>
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}UncategorizedPages")), __('Uncategorized pages'), array('title' => "Special:{$projectnamespace}UncategorizedPages")); ?></li>
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}OrphanedPages")), __('Orphaned pages'), array('title' => "Special:{$projectnamespace}OrphanedPages")); ?></li>
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}UncategorizedCategories")), __('Uncategorized categories'), array('title' => "Special:{$projectnamespace}UncategorizedCategories")); ?></li>
	</ul>
	<br style="clear: both;">
	<br style="clear: both;">
	<h3><?php echo __('Page lists'); ?></h3>
	<ul class="category_list">
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}AllPages")), __('All pages'), array('title' => "Special:{$projectnamespace}AllPages")); ?></li>
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}AllCategories")), __('All categories'), array('title' => "Special:{$projectnamespace}AllCategories")); ?></li>
		<li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}AllTemplates")), __('All templates'), array('title' => "Special:{$projectnamespace}AllTemplates")); ?></li>
	</ul>
</div>
