<div style="margin: 10px 5px 10px 10px;">
	<div class="left_menu_header"><?php echo __('Latest news'); ?></div>
	<ul class="news_list">
	<?php if (count($news) >= 1): ?>
		<?php foreach($news as $anews): ?>
			<li>
				<div>
					<?php if ($anews->isLink()): ?>
						<?php echo image_tag('news_link.png', array(), false, 'publish'); ?>
						<a href="<?php print $anews->getLinkURL(); ?>" target="_blank"><?php print $anews->getTitle(); ?></a>
					<?php elseif ($anews->hasAnyContent()): ?>
						<?php echo image_tag('news_item_medium.png', array('style' => 'float: left;'), false, 'publish'); ?>
						<a href="<?php print BUGScontext::getTBGPath(); ?>modules/publish/articles.php?article_id=<?php print $anews->getID(); ?>"><?php print $anews->getTitle(); ?></a>
					<?php else: ?>
						<?php echo image_tag('news_item_medium.png', array('style' => 'float: left;'), false, 'publish'); ?>
						<?php print $anews->getTitle(); ?>
					<?php endif; ?>
					<br>
					<span><?php print bugs_formatTime($anews->getPostedDate(), 3); ?></span>
				</div>
			</li>
		<?php endforeach; ?>
			<li class="more_news"><?php echo link_tag(make_url('publish'), __('More news')); ?></li>
	<?php elseif (count($news) == 0): ?>
		<li class="faded_medium"><?php echo __('There are no news published'); ?></li>
	<?php endif; ?>
	</ul>
</div>