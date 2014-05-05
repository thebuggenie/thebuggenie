<?php

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle($article_name);

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
	
	<?php
// TURNED OFF SIDER BAR
//		
//		
//				<td class="side_bar">
//			<?php include_component('leftmenu', array('article' => $article)); TOOK OUT QUESTION MARK SYMBOL > 
//		</td>
?>
		<td class="main_area article">
			<a name="top"></a>
			<?php if ($error): ?>
				<div class="rounded_box red borderless" style="margin: 0 0 5px 0; padding: 8px; font-size: 14px; color: #FFF;">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<?php if ($message): ?>
				<div class="rounded_box green borderless" style="margin: 0 0 5px 5px; padding: 8px; font-size: 14px;">
					<b><?php echo $message; ?></b>
				</div>
			<?php endif; ?>
			<?php if (isset($revision) && !$error): ?>
				<div class="rounded_box yellow borderless" style="margin: 0 0 5px 5px; padding: 8px; font-size: 14px;">
					<?php echo __('You are now viewing a previous revision of this article - revision %revision_number% %date%, by %author%', array('%revision_number%' => '<b>'.$revision.'</b>', '%date%' => '<span class="faded_out">[ '.tbg_formatTime($article->getPostedDate(), 20).' ]</span>', '%author%' => (($article->getAuthor() instanceof TBGUser) ? $article->getAuthor()->getName() : __('System')))); ?><br>
					<b><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), __('Show current version')); ?></b>
				</div>
			<?php endif; ?>
			<?php if ($article instanceof TBGWikiArticle): ?>
				<?php include_component('articledisplay', array('article' => $article, 'show_article' => true, 'redirected_from' => $redirected_from)); ?>
				<?php $article_name = $article->getName(); ?>
			<?php else: ?>
				<div class="article">
					<?php include_template('publish/header', array('article_name' => $article_name, 'show_actions' => true, 'mode' => 'view')); ?>
					<?php if (TBGContext::isProjectContext() && TBGContext::getCurrentProject()->isArchived()): ?>
						<?php include_template('publish/placeholder', array('article_name' => $article_name, 'nocreate' => true)); ?>
					<?php else: ?>
						<?php include_template('publish/placeholder', array('article_name' => $article_name)); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if (!$article instanceof TBGWikiArticle && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || (!TBGContext::isProjectContext() && TBGContext::getModule('publish')->canUserEditArticle($article_name)))): ?>
				<div class="publish_article_actions">
					<form action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="get" style="float: left; margin-right: 10px;">
						<input class="button button-green" type="submit" value="<?php echo __('Create this article'); ?>">
					</form>
				</div>
			<?php endif; ?>
			<?php if ($article instanceof TBGWikiArticle): ?>
				<div id="article_comments">
					<h4><?php echo __('Article comments (%count%)', array('%count%' => TBGComment::countComments($article->getID(), TBGComment::TYPE_ARTICLE))); ?></h4>
					<?php include_template('main/comments', array('target_id' => $article->getID(), 'target_type' => TBGComment::TYPE_ARTICLE, 'comment_count_div' => 'article_comment_count', 'forward_url' => make_url('publish_article', array('article_name' => $article->getName())))); ?>
				</div>
			<?php endif; ?>
		</td>
	</tr>
</table>