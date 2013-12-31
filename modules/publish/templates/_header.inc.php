<?php $article_name = $article->getName(); ?>
<div class="header tab_menu">
	<div class="title_left_images">
		<?php if ($tbg_user->isGuest()): ?>
			<?php echo image_tag('star_faded.png', array('id' => 'article_favourite_faded_'.$article->getId())); ?>
			<div class="tooltip from-above leftie">
				<?php echo __('Please log in to subscribe to updates for this article'); ?>
			</div>
		<?php else: ?>
			<div class="tooltip from-above leftie">
				<?php echo __('Click the star to toggle whether you want to be notified whenever this article updates or changes'); ?><br>
			</div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'article_favourite_indicator_'.$article->getId(), 'style' => 'display: none;')); ?>
			<?php echo image_tag('star_faded.png', array('id' => 'article_favourite_faded_'.$article->getId(), 'style' => 'cursor: pointer;'.(($tbg_user->isArticleStarred($article->getID())) ? 'display: none;' : ''), 'onclick' => "TBG.Main.toggleFavouriteArticle('".make_url('toggle_favourite_article', array('article_id' => $article->getID(), 'user_id' => $tbg_user->getID()))."', ".$article->getID().");")); ?>
			<?php echo image_tag('star.png', array('id' => 'article_favourite_normal_'.$article->getId(), 'style' => 'cursor: pointer;'.((!$tbg_user->isArticleStarred($article->getID())) ? 'display: none;' : ''), 'onclick' => "TBG.Main.toggleFavouriteArticle('".make_url('toggle_favourite_article', array('article_id' => $article->getID(), 'user_id' => $tbg_user->getID()))."', ".$article->getID().");")); ?>
		<?php endif; ?>
	</div>
	<?php if ($article->getID() || $mode == 'edit'): ?>
		<?php if ($show_actions): ?>
			<ul class="right">
				<?php if ($article->getID()): ?>
					<li<?php if ($mode == 'view'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), __('Show')); ?></li>
				<?php endif; ?>
				<?php if ((isset($article) && $article->canEdit()) || (!isset($article) && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || (!TBGContext::isProjectContext() && TBGContext::getModule('publish')->canUserEditArticle($article_name))))): ?>
					<li<?php if ($mode == 'edit'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_edit', array('article_name' => $article_name)), ($article->getID()) ? __('Edit') : __('Create new article')); ?></li>
				<?php endif; ?>
				<?php if ($article->getID()): ?>
					<li<?php if ($mode == 'history'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_history', array('article_name' => $article_name)), __('History')); ?></li>
					<?php if ((isset($article) && $article->canEdit()) || (!isset($article) && TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived())): ?>
						<li<?php if ($mode == 'permissions'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_permissions', array('article_name' => $article_name)), __('Permissions')); ?></li>
					<?php endif; ?>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
	<?php endif; ?>
	<?php if (TBGContext::isProjectContext()): ?>
		<?php if ((mb_strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 0) || ($article instanceof TBGWikiArticle && $article->isCategory() && mb_strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 9)): ?>
			<?php $project_article_name = mb_substr($article_name, (($article instanceof TBGWikiArticle && $article->isCategory()) * 9) + mb_strlen(TBGContext::getCurrentProject()->getKey())+1); ?>
			<?php if ($article->getID() && $article->isCategory()): ?><span class="faded_out blue">Category:</span><?php endif; ?><span class="faded_out dark"><?php echo ucfirst(TBGContext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
		<?php endif; ?>
	<?php elseif (mb_substr($article_name, 0, 9) == 'Category:'): ?>
		<span class="faded_out blue">Category:</span><?php echo get_spaced_name(mb_substr($article_name, 9)); ?>
	<?php else: ?>
		<?php echo get_spaced_name($article_name); ?>
	<?php endif; ?>
	<?php

		if ($article->getID() && $mode)
		{
			switch ($mode)
			{
				case 'edit':
					?><span class="faded_out"><?php echo __('%article_name ~ Edit', array('%article_name' => '')); ?></span><?php
					break;
				case 'history':
					?><span class="faded_out"><?php echo __('%article_name ~ History', array('%article_name' => '')); ?></span><?php
					break;
				case 'permissions':
					?><span class="faded_out"><?php echo __('%article_name ~ Permissions', array('%article_name' => '')); ?></span><?php
					break;
				case 'attachments':
					?><span class="faded_out"><?php echo __('%article_name ~ Attachments', array('%article_name' => '')); ?></span><?php
					break;
			}
		}

	?>
</div>
