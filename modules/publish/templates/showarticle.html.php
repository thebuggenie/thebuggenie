<?php

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle($article_name);

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar <?php if ($article->getArticleType() == TBGWikiArticle::TYPE_MANUAL) echo 'manual'; ?>">
			<?php if ($article->getArticleType() == TBGWikiArticle::TYPE_MANUAL): ?>
				<?php include_component('manualsidebar', array('article' => $article)); ?>
			<?php else: ?>
				<?php include_component('leftmenu', array('article' => $article)); ?>
			<?php endif; ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<?php if ($error): ?>
				<div class="redbox">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<?php if ($message): ?>
				<div class="greenbox" style="margin: 0 0 5px 5px; font-size: 14px;">
					<b><?php echo $message; ?></b>
				</div>
			<?php endif; ?>
			<?php if (isset($revision) && !$error): ?>
				<div class="lightyellowbox" style="margin: 0 0 5px 5px; font-size: 14px;">
					<?php echo __('You are now viewing a previous revision of this article - revision %revision_number %date, by %author', array('%revision_number' => '<b>'.$revision.'</b>', '%date' => '<span class="faded_out">[ '.tbg_formatTime($article->getPostedDate(), 20).' ]</span>', '%author' => (($article->getAuthor() instanceof TBGUser) ? $article->getAuthor()->getName() : __('System')))); ?><br>
					<b><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), __('Show current version')); ?></b>
				</div>
			<?php endif; ?>
			<?php if ($article->getID()): ?>
				<?php include_component('articledisplay', array('article' => $article, 'show_article' => true, 'redirected_from' => $redirected_from)); ?>
				<?php $article_name = $article->getName(); ?>
			<?php else: ?>
				<div class="article">
					<?php include_template('publish/header', array('article' => $article, 'show_actions' => true, 'mode' => 'view')); ?>
					<?php if (TBGContext::isProjectContext() && TBGContext::getCurrentProject()->isArchived()): ?>
						<?php include_template('publish/placeholder', array('article_name' => $article_name, 'nocreate' => true)); ?>
					<?php else: ?>
						<?php include_template('publish/placeholder', array('article_name' => $article_name)); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if (!$article->getID() && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || (!TBGContext::isProjectContext() && TBGContext::getModule('publish')->canUserEditArticle($article_name)))): ?>
				<div class="publish_article_actions">
					<form action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="get" style="float: left; margin-right: 10px;">
						<input class="button button-green" type="submit" value="<?php echo __('Create this article'); ?>">
					</form>
				</div>
			<?php endif; ?>
			<?php if ($article->getID()): ?>
				<?php $attachments = $article->getFiles(); ?>
				<div id="article_attachments">
					<?php /*if (TBGSettings::isUploadsEnabled() && $article->canEdit()): ?>
						<?php include_component('main/uploader', array('article' => $article, 'mode' => 'article')); ?>
					<?php endif;*/ ?>
					<h4>
						<?php echo __('Article attachments'); ?>
						<?php if (TBGSettings::isUploadsEnabled() && $article->canEdit()): ?>
							<button class="button button-silver" onclick="TBG.Main.showUploader('<?php echo make_url('get_partial_for_backdrop', array('key' => 'uploader', 'mode' => 'article', 'article_name' => $article_name)); ?>');"><?php echo __('Attach a file'); ?></button>
						<?php else: ?>
							<button class="button button-silver disabled" onclick="TBG.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>');"><?php echo __('Attach a file'); ?></button>
						<?php endif; ?>
					</h4>
					<?php include_template('publish/attachments', array('article' => $article, 'attachments' => $attachments)); ?>
				</div>
				<div id="article_comments">
					<h4>
						<?php echo __('Article comments (%count)', array('%count' => TBGComment::countComments($article->getID(), TBGComment::TYPE_ARTICLE))); ?>
						<?php if ($tbg_user->canPostComments() && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
							<button id="comment_add_button" class="button button-silver" onclick="TBG.Main.Comment.showPost();"><?php echo __('Post comment'); ?></button>
						<?php endif; ?>
					</h4>
					<?php include_template('main/comments', array('target_id' => $article->getID(), 'target_type' => TBGComment::TYPE_ARTICLE, 'show_button' => false, 'comment_count_div' => 'article_comment_count', 'forward_url' => make_url('publish_article', array('article_name' => $article->getName())))); ?>
				</div>
			<?php endif; ?>
		</td>
	</tr>
</table>
