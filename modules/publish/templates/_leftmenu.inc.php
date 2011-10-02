<?php include_template('main/menulinks', array('links' => $links, 'target_type' => 'wiki', 'target_id' => $links_target_id, 'title' => __('Wiki menu'))); ?>
<?php if ($article instanceof TBGWikiArticle): ?>
	<div class="container_div" style="margin: 0 0 5px 5px;">
		<div class="header"><?php echo __('Links to this article'); ?></div>
		<div class="content">
			<?php if (count($whatlinkshere) > 0): ?>
				<ul class="article_list">
					<?php foreach ($whatlinkshere as $linking_article): ?>
						<li>
							<?php echo image_tag('news_item_medium.png', array('style' => 'float: left;'), false, 'publish'); ?>
							<?php echo link_tag(make_url('publish_article', array('article_name' => $linking_article->getName())), get_spaced_name($linking_article->getTitle())); ?>
							<br>
							<span><?php echo __('%time%, by %user%', array('%time%' => tbg_formatTime($linking_article->getPostedDate(), 3), '%user%' => '<b>'.(($linking_article->getAuthor() instanceof TBGIdentifiable) ? '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $linking_article->getAuthor()->getID())) . '\');">' . $linking_article->getAuthor()->getName() . '</a>' : __('System')).'</b>')); ; ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="no_items"><?php echo __("No other articles links to this article"); ?></div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; /*?>
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
<?php if (!TBGContext::isProjectContext()): ?>
	<?php include_component('publish/latestArticles'); ?>
<?php endif; ?>