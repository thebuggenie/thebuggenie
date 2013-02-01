<?php

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle($article_name);

?>
		<div class="main_area article">
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
				<?php include_component('articledisplay', array('article' => $article, 'show_article' => true, 'redirected_from' => $redirected_from, 'show_actions' => false)); ?>
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
		</div>