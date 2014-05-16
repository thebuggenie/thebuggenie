<?php $article = (isset($article)) ? $article : null; ?>


<?php // CHANGE STYLE OF WIKI TITLE IF USER IS GUEST ?>
<?php if (!$tbg_user->isGuest()): ?> <div class="header tab_menu"   > 
<?php else: ?>
		<div class="guest"   >
<?php endif; ?>


<?php // BEGIN ONLY SHOW EDITING TABS IF USER IS LOGGED IN ?>
<?php if (!$tbg_user->isGuest()): ?>
	<?php if ($article instanceof TBGWikiArticle || $mode == 'edit'): ?>
		<?php if ($show_actions): ?>
			<ul class="right">
				<?php if ($article instanceof TBGWikiArticle): ?>
					<li<?php if ($mode == 'view'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article', array('article_name' => $article_name)), __('Show')); ?></li>
				<?php endif; ?>
				<?php if ((isset($article) && $article->canEdit()) || (!isset($article) && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || (!TBGContext::isProjectContext() && TBGContext::getModule('publish')->canUserEditArticle($article_name))))): ?>
					<li<?php if ($mode == 'edit'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_edit', array('article_name' => $article_name)), ($article instanceof TBGWikiArticle) ? __('Edit') : __('Create new article')); ?></li>
				<?php endif; ?>
				<?php if ($article instanceof TBGWikiArticle): ?>
					<li<?php if ($mode == 'history'): ?> class="selected"<?php endif; ?>>
					<?php echo link_tag(make_url('publish_article_history', array('article_name' => $article_name)), __('History')); ?></li>
					<?php if ((isset($article) && $article->canEdit()) || (!isset($article) && TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived())): ?>
						<li<?php if ($mode == 'permissions'): ?> class="selected"<?php endif; ?>>
						<?php echo link_tag(make_url('publish_article_permissions', array('article_name' => $article_name)), __('Permissions')); ?></li>
					<?php endif; ?>
					<li<?php if ($mode == 'attachments'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_attachments', array('article_name' => $article_name)), __('Attachments')); ?></li>			
			</ul>
				<?php endif; ?>
				<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php // END ONLY SHOW EDITING TABS IF USER IS LOGGED IN ?>


	<?php if (TBGContext::isProjectContext()): ?>
		<?php if ((mb_strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 0) || ($article instanceof TBGWikiArticle && $article->isCategory() && mb_strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 9)): ?>
			<?php $project_article_name = mb_substr($article_name, (($article instanceof TBGWikiArticle && $article->isCategory()) * 9) + mb_strlen(TBGContext::getCurrentProject()->getKey())+1); ?>
			<?php if ($article instanceof TBGWikiArticle && $article->isCategory()): ?><span class="faded_out blue">Category:</span><?php endif; ?><span class="faded_out dark"><?php echo ucfirst(TBGContext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
		<?php endif; ?>
	<?php elseif (mb_substr($article_name, 0, 9) == 'Category:'): ?>
		<span class="faded_out blue">Category:</span><?php echo get_spaced_name(mb_substr($article_name, 9)); ?>
	<?php else: ?>
	
	
<?php // IF USER IS LOGGED IN SHOW OVERVIEW WIKI TITLE AND IF GUEST SHOW TITLE BELOW ?>
		<?php if ($tbg_response->getTitle() != 'Overview'): ?><?php echo get_spaced_name($article_name); ?> <?php else: ?>
		<?php if ($tbg_user->isGuest()): ?>  
			<div class="guest_overview_page"><?php echo image_tag(TBGSettings::getHeaderIconUrl(), array('style' => 'max-height: 24px;'), TBGSettings::isUsingCustomHeaderIcon()); ?><br> 		
 		<?php echo "Welcome to Your Website Name Here!"; ?> <br> <span  style="font-size: .7em" ><?php echo "Feel free to look around.  Website testing and content editing in progress.  <br> Change this title and message in file /YOURBugGenieFolder/modules/publish/templates/_header.inc.php.  It's on line 51."; ?></span></div><br>
		<?php else: ?>
		<?php echo "Your Website Name Overview"; ?><?php endif; ?>
<?php endif; ?>
<?php // END IF USER IS LOGGED IN SHOW OVERVIEW WIKI TITLE AND IF GUEST SHOW TITLE BELOW ?>


	<?php endif; ?>
	<?php if ($article instanceof TBGWikiArticle && $mode)
		{
			switch ($mode)
			{
				case 'edit':
					?><span class="faded_out"><?php echo __('%article_name% ~ Edit', array('%article_name%' => '')); ?></span><?php
					break;
				case 'history':
					?><span class="faded_out"><?php echo __('%article_name% ~ History', array('%article_name%' => '')); ?></span><?php
					break;
				case 'permissions':
					?><span class="faded_out"><?php echo __('%article_name% ~ Permissions', array('%article_name%' => '')); ?></span><?php
					break;
				case 'attachments':
					?><span class="faded_out"><?php echo __('%article_name% ~ Attachments', array('%article_name%' => '')); ?></span><?php
					break;
			}
		}

	?>
</div>