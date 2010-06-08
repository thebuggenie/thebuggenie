<?php include_template('main/menulinks', array('links' => $links, 'target_type' => 'wiki', 'target_id' => $links_target_id, 'title' => __('Wiki menu'))); ?>
<?php if ($article instanceof TBGWikiArticle): ?>
	<div style="margin: 10px 0 5px 5px;">
	<div class="left_menu_header"><?php echo __('Links to this article'); ?></div>
		<?php if (count($whatlinkshere) > 0): ?>
			<ul class="article_list">
				<?php foreach ($whatlinkshere as $linking_article): ?>
					<li>
						<div>
							<?php echo image_tag('news_item.png', array('style' => 'float: left;'), false, 'publish'); ?>
							<?php echo link_tag(make_url('publish_article', array('article_name' => $linking_article->getName())), get_spaced_name($linking_article->getTitle())); ?>
							<br>
							<span><?php print tbg_formatTime($linking_article->getPostedDate(), 3); ?></span>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<span class="left_menu_content faded_medium"><?php echo __("No other articles links to this article"); ?></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
<div style="margin: 10px 0 5px 5px;">
<div class="left_menu_header"><?php echo __('Your drafts'); ?></div>
	<?php if (count($user_drafts) > 0): ?>
		<ul class="article_list">
			<?php foreach ($user_drafts as $article): ?>
			<li><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), get_spaced_name($article->getTitle())); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<span class="faded_medium" style="padding-left: 5px; font-size: 12px;"><?php echo __("You don't have any unpublished pages"); ?></span>
	<?php endif; ?>
</div>