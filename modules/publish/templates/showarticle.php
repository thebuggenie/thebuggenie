<?php TBGContext::loadLibrary('publish/publish'); ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar" style="width: 250px;">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<?php if ($error): ?>
				<div class="rounded_box red_borderless" style="margin: 0 0 5px 0;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 3px; font-size: 14px; color: #FFF;">
						<?php echo $error; ?>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
			<?php endif; ?>
			<?php if ($message): ?>
				<div class="rounded_box green_borderless" style="margin: 0 0 5px 5px;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 3px; font-size: 14px;">
						<b><?php echo $message; ?></b>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
			<?php endif; ?>
			<?php if ($article instanceof PublishArticle): ?>
				<?php include_component('articledisplay', array('article' => $article)); ?>
			<?php else: ?>
				<div class="header" style="padding: 5px;">
					<?php echo link_tag(make_url('publish_article', array('article_name' => 'FrontpageArticle')), __('Front page article'), array('class' => (($article_name == 'FrontpageArticle') ? 'faded_medium' : ''), 'style' => 'float: right; margin-right: 15px;')); ?>
					<?php if (TBGContext::isProjectContext()): ?>
						<?php if ((strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 0) || ((substr($article_name, 0, 8) == 'Category') && strpos($article_name, ucfirst(TBGContext::getCurrentProject()->getKey())) == 9)): ?>
							<?php $project_article_name = substr($article_name, ((substr($article_name, 0, 8) == 'Category') * 9) + strlen(TBGContext::getCurrentProject()->getKey())+1); ?>
							<?php if (substr($article_name, 0, 8) == 'Category'): ?><span class="faded_blue">Category:</span><?php endif; ?><span class="faded_dark"><?php echo ucfirst(TBGContext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
						<?php endif; ?>
					<?php elseif (substr($article_name, 0, 9) == 'Category:'): ?>
						<?php $display_article_name = substr($article_name, 9); ?>
						<span class="faded_blue">Category:</span><?php echo get_spaced_name($display_article_name); ?>
					<?php else: ?>
						<?php echo get_spaced_name($article_name); ?>
					<?php endif; ?>
				</div>
				<div style="font-size: 14px; margin: 10px 0 15px 2px;">
					<?php echo __('This is a placeholder for an article that has not been created yet. You can create it by clicking %create_this_article% below.', array('%create_this_article%' => '<b>'.__('Create this article').'</b>')); ?>
				</div>
			<?php endif; ?>
			<div class="publish_article_actions">
				<div class="sub_header"><?php echo __('Actions available'); ?></div>
				<form action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="get" style="float: left; margin-right: 10px;">
					<input type="submit" value="<?php echo ($article instanceof PublishArticle) ? __('Edit this article') : __('Create this article'); ?>">
				</form>
				<?php if ($article instanceof PublishArticle): ?>
					<button onclick="$('delete_article_confirm').toggle();"><?php echo __('Delete this article'); ?></button>
					<div class="rounded_box" style="margin: 10px 0 5px; width: 720px; display: none;" id="delete_article_confirm">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 3px 10px 3px 10px; font-size: 14px;">
							<h4><?php echo __('Really delete this article?'); ?></h4>
							<span class="xboxlarge"><?php echo __('Deleting this article will remove it from the system.'); ?></span><br>
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