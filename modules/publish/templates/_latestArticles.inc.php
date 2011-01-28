<?php TBGContext::loadLibrary('publish/publish'); ?>
<div style="margin: 10px 0 5px 5px;">
	<div class="header"><?php echo __('Recently edited pages'); ?></div>
	<ul class="article_list">
	<?php if (count($latest_articles) >= 1): ?>
		<?php foreach($latest_articles as $article): ?>
			<li>
				<div>
					<?php echo image_tag('news_item_medium.png', array('style' => 'float: left;'), false, 'publish'); ?>
					<?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), get_spaced_name($article->getTitle())); ?>
					<br>
					<span><?php echo __('%time%, by %user%', array('%time%' => tbg_formatTime($article->getPostedDate(), 3), '%user%' => '<b>'.(($article->getAuthor() instanceof TBGIdentifiable) ? '<a href="javascript:void(0);" onclick="showFadedBackdrop(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $article->getAuthor()->getID())) . '\');">' . $article->getAuthor()->getName() . '</a>' : __('System')).'</b>')); ; ?></span>
				</div>
			</li>
		<?php endforeach; ?>
			<li class="more_news"><?php echo link_tag(make_url('publish'), __('Show wiki').'&nbsp;&rArr;'); ?></li>
	<?php elseif (count($latest_articles) == 0): ?>
		<li class="faded_out"><?php echo __('There are no news published'); ?></li>
	<?php endif; ?>
	</ul>
</div>