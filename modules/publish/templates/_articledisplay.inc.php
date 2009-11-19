<?php BUGScontext::loadLibrary('publish/publish'); ?>
<div class="article" style="width: auto; padding: 5px; position: relative;">
	<?php if ($show_title): ?>
		<div class="header">
			<?php if ($show_actions): ?>
				<?php echo link_tag(make_url('publish_article_history', array('article_name' => $article->getName())), __('History'), array('style' => 'float: right;')); ?>
				<?php echo link_tag(make_url('publish_article', array('article_name' => 'IndexMessage')), __('Front page article'), array('class' => (($article->getName() == 'IndexMessage') ? 'faded_medium' : ''), 'style' => 'float: right; margin-right: 15px;')); ?>
			<?php endif; ?>
			<?php echo $article->getTitle(); ?>
		</div>
	<?php endif; ?>
	<?php if ($show_details): ?>
		<div class="faded_medium" style="padding-bottom: 5px;"><?php echo __('Last updated at %time%, by %user%', array('%time%' => bugs_formatTime($article->getPostedDate(), 3), '%user%' => '<b>'.(($article->getAuthor() instanceof BUGSidentifiable) ? $article->getAuthor()->getName() : __('System')).'</b>')); ; ?></div>
	<?php endif; ?>
	<?php if ($show_intro && $article->hasIntro()): ?>
		<div style="font-size: 13px; padding-bottom: 5px;"><?php echo publish_parse($article->getIntro()); ?></div>
		<br>
	<?php endif; ?>
	<div style="font-size: 13px; padding-bottom: 5px;"><?php echo publish_parse($article->getContent(), true, $article->getID()); ?></div>
	<?php if ($show_link): ?>
		<div style="text-align: right;"><a href="<?php echo BUGScontext::getTBGPath(); ?>modules/publish/articles.php?article_id=<?php echo $article->getID(); ?>"><?php echo BUGScontext::getI18n()->__('Read more'); ?></a></div>
	<?php endif; ?>
</div>