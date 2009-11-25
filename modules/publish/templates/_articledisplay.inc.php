<?php BUGScontext::loadLibrary('publish/publish'); ?>
<div class="article" style="width: auto; padding: 5px; position: relative;">
	<?php if ($show_title): ?>
		<div class="header">
			<?php if ($show_actions): ?>
				<?php echo link_tag(make_url('publish_article_history', array('article_name' => $article->getName())), __('History'), array('style' => 'float: right;')); ?>
				<?php echo link_tag(make_url('publish_article', array('article_name' => 'FrontpageArticle')), __('Front page article'), array('class' => (($article->getName() == 'FrontpageArticle') ? 'faded_medium' : ''), 'style' => 'float: right; margin-right: 15px;')); ?>
			<?php endif; ?>
			<?php if (BUGScontext::getCurrentProject() instanceof BUGSproject): ?>
				<?php if ((strpos($article->getName(), ucfirst(BUGScontext::getCurrentProject()->getKey())) == 0) || ($article->isCategory() && strpos($article->getName(), ucfirst(BUGScontext::getCurrentProject()->getKey())) == 9)): ?>
					<?php $project_article_name = substr($article->getName(), ($article->isCategory() * 9) + strlen(BUGScontext::getCurrentProject()->getKey())+1); ?>
					<?php if ($article->isCategory()): ?>Category:<?php endif; ?><span class="faded_dark"><?php echo ucfirst(BUGScontext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo get_spaced_name($article->getName()); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ($show_details): ?>
		<div class="faded_medium" style="padding-bottom: 5px;"><?php echo __('Last updated at %time%, by %user%', array('%time%' => bugs_formatTime($article->getPostedDate(), 3), '%user%' => '<b>'.(($article->getAuthor() instanceof BUGSidentifiable) ? $article->getAuthor()->getName() : __('System')).'</b>')); ; ?></div>
	<?php endif; ?>
	<div style="font-size: 13px; padding-bottom: 5px;"><?php echo tbg_parse_text($article->getContent(), true, $article->getID(), array('embedded' => $embedded)); ?></div>
</div>