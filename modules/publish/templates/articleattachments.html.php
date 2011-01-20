<?php

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle(__('%article_name% attachments', array('%article_name%' => $article_name)));

?>
<?php if (TBGSettings::isUploadsEnabled() && $article instanceof TBGWikiArticle): ?>
	<?php include_component('main/uploader', array('article' => $article, 'mode' => 'article')); ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<div class="article" style="width: auto; padding: 5px; position: relative;">
				<div class="header tab_menu">
					<ul class="right">
						<li><?php echo link_tag(make_url('publish_article', array('article_name' => $article_name)), __('Show')); ?></li>
						<?php if (TBGContext::getModule('publish')->canUserEditArticle($article_name)): ?>
							<li><?php echo link_tag(make_url('publish_article_edit', array('article_name' => $article_name)), __('Edit')); ?></li>
						<?php endif; ?>
						<li><?php echo link_tag(make_url('publish_article_history', array('article_name' => $article_name)), __('History')); ?></li>
						<li><?php echo link_tag(make_url('publish_article_permissions', array('article_name' => $article_name)), __('Permissions')); ?></li>
						<li class="selected"><?php echo link_tag(make_url('publish_article_attachments', array('article_name' => $article_name)), __('Attachments')); ?></li>
					</ul>
					<?php if (TBGContext::isProjectContext()): ?>
						<?php if ((strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 0) || ($article->isCategory() && strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 9)): ?>
							<?php $project_article_name = substr($article_name, ($article->isCategory() * 9) + strlen(TBGContext::getCurrentProject()->getKey())+1); ?>
							<?php if ($article->isCategory()): ?><span class="faded_out blue">Category:</span><?php endif; ?><span class="faded_out dark"><?php echo ucfirst(TBGContext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
						<?php endif; ?>
					<?php elseif (substr($article_name, 0, 9) == 'Category:'): ?>
						<span class="faded_out blue">Category:</span><?php echo get_spaced_name(substr($article_name, 9)); ?>
					<?php else: ?>
						<?php echo get_spaced_name($article_name); ?>
					<?php endif; ?>
					<span class="faded_out"><?php echo __('%article_name% ~ Attachments', array('%article_name%' => '')); ?></span>
				</div>
				<?php if ($article instanceof TBGWikiArticle): ?>
					<?php if (TBGSettings::isUploadsEnabled()): ?>
						<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="article_attach_file_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="$('attach_file').show();" value="<?php echo __('Attach a file'); ?>"></td></tr></table>
					<?php else: ?>
						<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="article_attach_file_button"><tr><td class="nice_button disabled" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="failedMessage('<?php echo __('File uploads are not enabled'); ?>');" value="<?php echo __('Attach a file'); ?>"></td></tr></table>
					<?php endif; ?>
					<br style="clear: both;">
					<?php include_template('publish/attachments', array('article' => $article)); ?>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>