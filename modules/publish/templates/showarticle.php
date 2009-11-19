<?php BUGScontext::loadLibrary('publish/publish'); ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar" style="width: 250px;">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<?php if ($article instanceof PublishArticle): ?>
				<?php include_component('articledisplay', array('article' => $article)); ?>
			<?php else: ?>
				<div class="header" style="padding: 5px;">
					<?php echo link_tag(make_url('publish_article', array('article_name' => 'IndexMessage')), __('Front page article'), array('class' => (($article_name == 'IndexMessage') ? 'faded_medium' : ''), 'style' => 'float: right; margin-right: 15px;')); ?>
					<?php echo get_spaced_name($article_name); ?>
				</div>
				<div style="font-size: 14px;">
					<?php echo __('This is a placeholder for an article that has not been created yet. You can create it by clicking %create_this_article% below.', array('%create_this_article%' => '<b>'.__('Create this article').'</b>')); ?>
				</div>
			<?php endif; ?>
			<div class="publish_article_actions">
				<div class="sub_header"><?php echo __('Actions available'); ?></div>
				<form action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="get">
					<input type="submit" value="<?php echo ($article instanceof PublishArticle) ? __('Edit this article') : __('Create this article'); ?>">
				</form>
			</div>
		</td>
	</tr>
</table>