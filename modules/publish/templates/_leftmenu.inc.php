<?php include_template('main/menulinks', array('links' => $links, 'target_type' => 'wiki', 'target_id' => $links_target_id, 'title' => __('Wiki menu'))); ?>
<?php if ($article instanceof TBGWikiArticle && $article->getID()): ?>
	<?php include_component('publish/whatlinkshere', compact('article')); ?>
	<?php include_component('publish/tools', compact('special', 'article')); ?>
<?php endif; ?>
<?php include_component('publish/latestArticles'); ?>
<?php /*?>
<div style="margin: 10px 0 5px 5px;">
<div class="header"><?php echo __('Your drafts'); ?></div>
	<?php if (count($user_drafts) > 0): ?>
		<ul class="article_list">
			<?php foreach ($user_drafts as $article): ?>
			<li><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), get_spaced_name($article->getTitle())); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<span class="faded_out" style="padding-left: 5px; font-size: 12px;"><?php echo __("You don't have any unpublished pages"); ?></span>
	<?php endif; ?>
</div> */ ?>
