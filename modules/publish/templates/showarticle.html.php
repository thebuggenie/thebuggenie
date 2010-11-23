<?php

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle($article_name);

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
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
			<?php else: ?>
				<div class="header" style="padding: 5px;">
					<?php echo link_tag(make_url('publish_article', array('article_name' => 'FrontpageArticle')), __('Front page article'), array('class' => (($article_name == 'FrontpageArticle') ? 'faded_out' : ''), 'style' => 'float: right; margin-right: 15px;')); ?>
					<?php if (TBGContext::isProjectContext()): ?>
						<?php if ((strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 0) || ((substr($article_name, 0, 8) == 'Category') && strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 9)): ?>
							<?php $project_article_name = substr($article_name, ((substr($article_name, 0, 8) == 'Category') * 9) + strlen(TBGContext::getCurrentProject()->getKey())+1); ?>
							<?php if (substr($article_name, 0, 8) == 'Category'): ?><span class="faded_out blue">Category:</span><?php endif; ?><span class="faded_out dark"><?php echo ucfirst(TBGContext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
						<?php endif; ?>
					<?php elseif (substr($article_name, 0, 9) == 'Category:'): ?>
						<?php $display_article_name = substr($article_name, 9); ?>
						<span class="faded_out blue">Category:</span><?php echo get_spaced_name($display_article_name); ?>
					<?php else: ?>
						<?php echo get_spaced_name($article_name); ?>
					<?php endif; ?>
				</div>
				<div class="article_placeholder">
					<?php echo __('This is a placeholder for an article that has not been created yet. You can create it by clicking %create_this_article% below.', array('%create_this_article%' => '<b>'.__('Create this article').'</b>')); ?>
				</div>
			<?php endif; ?>
			<div class="publish_article_actions">
				<div class="sub_header"><?php echo __('Actions available'); ?></div>
				<form action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="get" style="float: left; margin-right: 10px;">
					<input type="submit" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Edit this article') : __('Create this article'); ?>">
				</form>
				<?php if ($article instanceof TBGWikiArticle): ?>
					<button onclick="$('delete_article_confirm').toggle();"><?php echo __('Delete this article'); ?></button>
					<div class="rounded_box" style="margin: 10px 0 5px; width: 720px; display: none;" id="delete_article_confirm">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 3px 10px 3px 10px; font-size: 14px;">
							<h4><?php echo __('Really delete this article?'); ?></h4>
							<span class="question_header"><?php echo __('Deleting this article will remove it from the system.'); ?></span><br>
							<div style="text-align: right;">
								<?php echo link_tag(make_url('publish_article_delete', array('article_name' => $article_name)), __('Yes')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('delete_article_confirm').hide();"><?php echo __('No'); ?></a>
							</div>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>